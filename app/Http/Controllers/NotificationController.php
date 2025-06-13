<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Pharmacy;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->get();
        return view('notifications.index', compact('notifications'));
    }

    public function show(Notification $notification)
    {
        $this->authorize('view', $notification);
        
        if (!$notification->read_at) {
            $notification->markAsRead();
        }
        
        return view('notifications.show', compact('notification'));
    }

    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();
        
        return redirect()->route('notifications.index')
            ->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->notifiable_id !== Auth::id()) {
            abort(403, 'Accès non autorisé.');
        }

        $notification->markAsRead();
        return redirect()->back()->with('success', 'Notification marquée comme lue.');
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);
        
        $notification->delete();
        
        return redirect()->route('notifications.index')
            ->with('success', 'Notification supprimée avec succès.');
    }

    public function approve(Notification $notification)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        if ($notification->type === 'pharmacy_deletion_request') {
            $pharmacy = Pharmacy::find($notification->data['pharmacy_id']);
            if ($pharmacy) {
                // Créer une notification pour le commercial
                $commercialNotification = new Notification([
                    'id' => Str::uuid(),
                    'type' => 'pharmacy_deletion_approved',
                    'data' => [
                        'pharmacy_name' => $pharmacy->name,
                        'message' => 'Votre demande de suppression de la pharmacie ' . $pharmacy->name . ' a été approuvée.'
                    ],
                    'notifiable_id' => $notification->data['commercial_id'],
                    'notifiable_type' => User::class
                ]);
                $commercialNotification->save();

                $pharmacy->delete();
            }
        }

        $notification->delete();
        return redirect()->route('crm.admin.notifications.index')->with('success', 'Demande approuvée avec succès.');
    }

    public function reject(Notification $notification)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Accès non autorisé.');
        }

        if ($notification->type === 'pharmacy_deletion_request') {
            // Créer une notification pour le commercial
            $commercialNotification = new Notification([
                'id' => Str::uuid(),
                'type' => 'pharmacy_deletion_rejected',
                'data' => [
                    'pharmacy_name' => $notification->data['pharmacy_name'],
                    'message' => 'Votre demande de suppression de la pharmacie ' . $notification->data['pharmacy_name'] . ' a été rejetée.'
                ],
                'notifiable_id' => $notification->data['commercial_id'],
                'notifiable_type' => User::class
            ]);
            $commercialNotification->save();
        }

        $notification->delete();
        return redirect()->route('crm.admin.notifications.index')->with('success', 'Demande rejetée.');
    }
} 