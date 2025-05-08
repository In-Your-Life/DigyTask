<div class="relative inline-block">
    <button wire:click="toggleDropdown" class="relative focus:outline-none">
        <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($unreadCount > 0)
            <span class="absolute top-0 right-0 inline-block w-4 h-4 bg-red-600 text-white text-xs rounded-full text-center">{{ $unreadCount }}</span>
        @endif
    </button>
    @if($showDropdown)
        <div class="absolute right-0 mt-2 w-80 bg-white border rounded shadow-lg z-50">
            <div class="p-2 font-bold border-b">Notifiche</div>
            <ul>
                @forelse($notifications as $notification)
                    <li class="p-2 border-b hover:bg-gray-100 flex justify-between items-center">
                        <div class="flex items-center space-x-2">
                            @if(isset($notification->data['icon']))
                                <i class="fas {{ $notification->data['icon'] }} text-gray-500"></i>
                            @endif
                            <div>
                                <div class="font-semibold">{{ $notification->data['title'] ?? $notification->type }}</div>
                                <div class="text-xs text-gray-600">{{ $notification->data['body'] ?? '' }}</div>
                                @if(isset($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="text-xs text-blue-600 hover:underline">Vai al dettaglio</a>
                                @endif
                                <div class="text-2xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                        <button wire:click="markAsRead('{{ $notification->id }}')" class="text-xs text-blue-600 hover:underline">Segna come letta</button>
                    </li>
                @empty
                    <li class="p-2 text-gray-500">Nessuna notifica non letta.</li>
                @endforelse
            </ul>
        </div>
    @endif
</div>
