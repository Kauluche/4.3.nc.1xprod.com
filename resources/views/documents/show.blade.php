<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $document->title }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('documents.edit', $document) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Modifier') }}
                </a>
                <a href="{{ route('documents.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Retour') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informations du document') }}</h3>
                            
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">{{ __('Titre') }}</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $document->title }}</p>
                            </div>

                            @if($document->description)
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500">{{ __('Description') }}</h4>
                                    <p class="mt-1 text-sm text-gray-900">{{ $document->description }}</p>
                                </div>
                            @endif

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">{{ __('Pharmacie') }}</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $document->pharmacy->name }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">{{ __('Date de création') }}</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $document->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">{{ __('Dernière modification') }}</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $document->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Fichier') }}</h3>
                            
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex items-center space-x-3">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ basename($document->file_path) }}</p>
                                        <p class="text-sm text-gray-500">{{ __('Téléchargé le') }} {{ $document->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        {{ __('Télécharger') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <form action="{{ route('documents.destroy', $document) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce document ?') }}')">
                                {{ __('Supprimer') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 