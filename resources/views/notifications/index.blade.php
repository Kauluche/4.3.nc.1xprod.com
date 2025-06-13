@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-md">
            <ul class="divide-y divide-gray-200">
                @forelse($notifications as $notification)
                    <li class="px-4 py-4 sm:px-6 {{ $notification->isRead() ? 'bg-gray-50' : 'bg-white' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $notification->data['message'] }}
                                </p>
                                @if(isset($notification->data['commercial_name']))
                                    <p class="text-sm text-gray-500">
                                        Demandé par: {{ $notification->data['commercial_name'] }}
                                    </p>
                                @endif
                                <p class="text-sm text-gray-500">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-4">
                                @if($notification->type === 'pharmacy_deletion_request')
                                    <form action="{{ route('notifications.approve', $notification) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white rounded-md transition duration-150 ease-in-out" style="background-color: #059669;">
                                            Approuver
                                        </button>
                                    </form>
                                    <form action="{{ route('notifications.reject', $notification) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition duration-150 ease-in-out">
                                            Rejeter
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.markAsRead', $notification) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                        Marquer comme lu
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-4 sm:px-6 text-center text-gray-500">
                        Aucune notification
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection 