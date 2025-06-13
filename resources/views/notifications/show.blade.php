<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $notification->title }}
            </h2>
            <div class="flex space-x-3">
                <a href="{{ route('notifications.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('Retour') }}
                </a>
                <form action="{{ route('notifications.destroy', $notification) }}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cette notification ?') }}')">
                        {{ __('Supprimer') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Message') }}</h3>
                        <p class="mt-2 text-gray-600">{{ $notification->message }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informations') }}</h3>
                            
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">{{ __('Type') }}</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ ucfirst($notification->type) }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">{{ __('Date de création') }}</h4>
                                <p class="mt-1 text-sm text-gray-900">{{ $notification->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-500">{{ __('Statut') }}</h4>
                                <p class="mt-1">
                                    @if($notification->read_at)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ __('Lu') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ __('Non lu') }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if($notification->read_at)
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Date de lecture') }}</h3>
                                <p class="text-sm text-gray-900">{{ $notification->read_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 