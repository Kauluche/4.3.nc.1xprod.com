<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Order;
use App\Models\Pharmacy;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        \Log::info('User role: ' . $user->role);
        \Log::info('User isAdmin: ' . ($user->isAdmin() ? 'true' : 'false'));
        \Log::info('User isCommercial: ' . ($user->isCommercial() ? 'true' : 'false'));
        
        if ($user->isAdmin()) {
            $pharmacies = Pharmacy::with(['orders' => function($query) {
                $query->latest();
            }])->paginate(10);
            \Log::info('Admin mode - Pharmacies count: ' . $pharmacies->count());
        } else {
            $pharmacies = Pharmacy::where('commercial_id', $user->id)
                ->with(['orders' => function($query) {
                    $query->latest();
                }])->paginate(10);
            \Log::info('Commercial mode - User ID: ' . $user->id . ' - Pharmacies count: ' . $pharmacies->count());
        }
        
        \Log::info('Pharmacies data: ' . json_encode($pharmacies->toArray()));
        
        return view('documents.index', [
            'pharmacies' => $pharmacies
        ]);
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        
        $document = new Document([
            'title' => $file->getClientOriginalName(),
            'type' => 'other',
            'pharmacy_id' => $request->pharmacy_id,
            'commercial_id' => auth()->id(),
            'file_path' => $file->storeAs('documents', $fileName, 'public')
        ]);

        $document->save();

        return response()->json(['success' => true]);
    }

    public function show(Document $document)
    {
        $this->authorize('view', $document);
        return view('documents.show', compact('document'));
    }

    public function edit(Document $document)
    {
        $this->authorize('update', $document);
        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $this->authorize('update', $document);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'pharmacy_id' => 'required|exists:pharmacies,id',
        ]);

        $document->fill($validated);

        if ($request->hasFile('file')) {
            $document->file_path = $request->file('file')->store('documents', 'public');
        }

        $document->save();

        return redirect()->route('crm.documents.index')
            ->with('success', 'Document mis à jour avec succès.');
    }

    public function destroy(Document $document)
    {
        try {
            // Supprimer le fichier physique
            if (Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Supprimer l'enregistrement de la base de données
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document supprimé avec succès'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du document'
            ], 500);
        }
    }

    public function generateInvoice(Order $order)
    {
        $pharmacy = Pharmacy::find($order->pharmacy_id);
        
        $pdf = Pdf::loadView('documents.invoice', [
            'order' => $order,
            'pharmacy' => $pharmacy,
            'date' => now()->format('d/m/Y'),
        ]);

        // Sauvegarder le document dans la base de données
        $document = new Document([
            'title' => 'Facture #' . $order->id,
            'type' => 'invoice',
            'pharmacy_id' => $pharmacy->id,
            'order_id' => $order->id,
            'commercial_id' => auth()->id(),
        ]);

        $fileName = 'facture-' . $order->id . '-' . now()->format('Y-m-d') . '.pdf';
        $document->file_path = 'documents/' . $fileName;
        
        // Sauvegarder le PDF
        Storage::disk('public')->put('documents/' . $fileName, $pdf->output());
        
        $document->save();

        return $pdf->download($fileName);
    }

    public function generateQuote(Request $request, Pharmacy $pharmacy)
    {
        $data = $request->validate([
            'products' => 'required|array',
            'products.*.name' => 'required|string',
            'products.*.quantity' => 'required|integer|min:1',
            'products.*.price' => 'required|numeric|min:0',
            'validity_days' => 'required|integer|min:1',
        ]);

        $total = collect($data['products'])->sum(function ($product) {
            return $product['quantity'] * $product['price'];
        });

        $pdf = Pdf::loadView('documents.quote', [
            'pharmacy' => $pharmacy,
            'products' => $data['products'],
            'total' => $total,
            'validity_date' => now()->addDays($data['validity_days'])->format('d/m/Y'),
            'date' => now()->format('d/m/Y'),
        ]);

        // Sauvegarder le document dans la base de données
        $document = new Document([
            'title' => 'Devis ' . $pharmacy->name . ' - ' . now()->format('Y-m-d'),
            'type' => 'quote',
            'pharmacy_id' => $pharmacy->id,
            'commercial_id' => auth()->id(),
        ]);

        $fileName = 'devis-' . $pharmacy->id . '-' . now()->format('Y-m-d') . '.pdf';
        $document->file_path = 'documents/' . $fileName;
        
        // Sauvegarder le PDF
        Storage::disk('public')->put('documents/' . $fileName, $pdf->output());
        
        $document->save();

        return $pdf->download($fileName);
    }

    public function generateDeliveryNote(Order $order)
    {
        $pharmacy = Pharmacy::find($order->pharmacy_id);
        
        $pdf = Pdf::loadView('documents.delivery-note', [
            'order' => $order,
            'pharmacy' => $pharmacy,
            'date' => now()->format('d/m/Y'),
        ]);

        // Sauvegarder le document dans la base de données
        $document = new Document([
            'title' => 'Bon de livraison #' . $order->id,
            'type' => 'delivery_note',
            'pharmacy_id' => $pharmacy->id,
            'order_id' => $order->id,
            'commercial_id' => auth()->id(),
        ]);

        $fileName = 'bon-livraison-' . $order->id . '-' . now()->format('Y-m-d') . '.pdf';
        $document->file_path = 'documents/' . $fileName;
        
        // Sauvegarder le PDF
        Storage::disk('public')->put('documents/' . $fileName, $pdf->output());
        
        $document->save();

        return $pdf->download($fileName);
    }

    public function download(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'Le fichier n\'existe pas.');
        }

        return Storage::disk('public')->download($document->file_path, $document->title);
    }

    public function preview(Document $document)
    {
        if (!Storage::disk('public')->exists($document->file_path)) {
            return back()->with('error', 'Le fichier n\'existe pas.');
        }

        $file = Storage::disk('public')->get($document->file_path);
        $mimeType = Storage::disk('public')->mimeType($document->file_path);

        return response($file)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'inline; filename="' . $document->title . '"');
    }
} 