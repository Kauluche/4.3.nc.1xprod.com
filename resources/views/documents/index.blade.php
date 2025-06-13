@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="card">
        <div class="p-6">
            @if($pharmacies->isEmpty())
                <div class="text-center py-8">
                    <p class="text-gray-500">Aucune pharmacie trouvée.</p>
                </div>
            @else
                @foreach($pharmacies as $pharmacy)
                    <div class="mb-8 bg-white rounded-lg shadow-sm border border-gray-200">
                        <!-- En-tête de la pharmacie -->
                        <div class="px-6 py-4 bg-gray-50 rounded-t-lg border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <h2 class="text-xl font-semibold text-gray-800">
                                    <i class="fas fa-store mr-2" style="color: #4F46E5;"></i>
                                    <a href="{{ route('crm.pharmacies.show', $pharmacy) }}" class="hover:text-indigo-600">
                                        {{ $pharmacy->name }}
                                    </a>
                                </h2>
                                <button onclick="uploadDocument({{ $pharmacy->id }})" 
                                        class="btn-primary">
                                    <i class="fas fa-upload mr-2"></i>
                                    Ajouter un document
                                </button>
                            </div>
                        </div>

                        <!-- Contenu -->
                        <div class="px-6 py-4">
                            <!-- Section Commandes -->
                            @if($pharmacy->orders->isNotEmpty())
                                <div class="mb-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Commandes</h3>
                                    <div class="space-y-3">
                                        @foreach($pharmacy->orders as $order)
                                            <div class="flex items-center justify-between p-4 rounded-lg" style="background-color: #F9FAFB; border: 1px solid #E5E7EB;">
                                                <div class="flex items-center">
                                                    <i class="fas fa-shopping-cart mr-3" style="color: #6B7280;"></i>
                                                    <a href="{{ route('crm.orders.show', $order) }}" class="text-gray-700 hover:text-indigo-600">
                                                        Commande #{{ $order->id }} - {{ $order->created_at->format('d/m/Y') }}
                                                    </a>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('crm.documents.generate-invoice', $order) }}" 
                                                       class="btn-primary">
                                                        <i class="fas fa-file-invoice mr-2"></i>
                                                        Facture
                                                    </a>
                                                    <a href="{{ route('crm.documents.generate-delivery-note', $order) }}" 
                                                       class="btn-primary">
                                                        <i class="fas fa-truck mr-2"></i>
                                                        Bon de livraison
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Section Documents -->
                            @if($pharmacy->documents->isNotEmpty())
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Documents</h3>
                                    <div class="grid grid-cols-1 gap-3">
                                        @foreach($pharmacy->documents as $document)
                                            <div class="flex items-center justify-between p-4 rounded-lg" 
                                                 style="background-color: {{ $document->type === 'invoice' ? '#EFF6FF' : ($document->type === 'delivery_note' ? '#F0FDF4' : '#F9FAFB') }}; 
                                                        border: 1px solid {{ $document->type === 'invoice' ? '#BFDBFE' : ($document->type === 'delivery_note' ? '#BBF7D0' : '#E5E7EB') }};">
                                                <div class="flex items-center flex-1">
                                                    @if($document->type === 'invoice')
                                                        <i class="fas fa-file-invoice text-xl mr-3" style="color: #3B82F6;"></i>
                                                        <span style="color: #1D4ED8; font-weight: 500;">Facture:</span>
                                                    @elseif($document->type === 'delivery_note')
                                                        <i class="fas fa-truck text-xl mr-3" style="color: #22C55E;"></i>
                                                        <span style="color: #15803D; font-weight: 500;">Bon de livraison:</span>
                                                    @else
                                                        <i class="fas fa-file-pdf text-xl mr-3" style="color: #EF4444;"></i>
                                                        <span style="color: #374151; font-weight: 500;">Document:</span>
                                                    @endif
                                                    <span class="ml-2 text-gray-600">{{ $document->title }}</span>
                                                </div>
                                                <div class="flex items-center space-x-4">
                                                    <a href="{{ route('crm.documents.download', $document) }}" 
                                                       class="btn-primary">
                                                        <i class="fas fa-download mr-1.5"></i>
                                                        Télécharger
                                                    </a>
                                                    <a href="{{ route('crm.documents.preview', $document) }}" 
                                                       class="btn-secondary">
                                                        <i class="fas fa-eye mr-1.5"></i>
                                                        Voir
                                                    </a>
                                                    <button onclick="deleteDocument({{ $document->id }})"
                                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        <i class="fas fa-trash-alt mr-1.5"></i>
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="mt-6">
                    {{ $pharmacies->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deleteDocument(documentId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
            fetch(`{{ url('crm/documents') }}/${documentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Erreur lors de la suppression du document');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erreur lors de la suppression du document');
            });
        }
    }

    function uploadDocument(pharmacyId) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.pdf';
        input.onchange = (e) => {
            const file = e.target.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('file', file);
                formData.append('pharmacy_id', pharmacyId);
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route("crm.documents.store") }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Erreur lors de l\'upload du fichier');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erreur lors de l\'upload du fichier');
                });
            }
        };
        input.click();
    }
</script>
@endpush
@endsection 