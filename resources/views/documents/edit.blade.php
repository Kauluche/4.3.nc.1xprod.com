<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le document') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('documents.update', $document) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <x-input-label for="title" :value="__('Titre')" />
                            <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title', $document->title)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('title')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description', $document->description) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="pharmacy_id" :value="__('Pharmacie')" />
                            <select id="pharmacy_id" name="pharmacy_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">{{ __('Sélectionner une pharmacie') }}</option>
                                @foreach(auth()->user()->pharmacies as $pharmacy)
                                    <option value="{{ $pharmacy->id }}" {{ old('pharmacy_id', $document->pharmacy_id) == $pharmacy->id ? 'selected' : '' }}>
                                        {{ $pharmacy->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('pharmacy_id')" />
                        </div>

                        <div class="mb-4">
                            <x-input-label for="file" :value="__('Fichier')" />
                            <input type="file" id="file" name="file" class="mt-1 block w-full" accept=".pdf,.doc,.docx">
                            <x-input-error class="mt-2" :messages="$errors->get('file')" />
                            <p class="mt-1 text-sm text-gray-500">{{ __('Formats acceptés : PDF, DOC, DOCX. Taille maximale : 10MB') }}</p>
                            @if($document->file_path)
                                <p class="mt-1 text-sm text-gray-500">{{ __('Fichier actuel :') }} {{ basename($document->file_path) }}</p>
                            @endif
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-secondary-button type="button" onclick="window.history.back()">
                                {{ __('Annuler') }}
                            </x-secondary-button>

                            <x-primary-button class="ml-3">
                                {{ __('Mettre à jour') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 