<div class="bg-gray-50 border rounded p-3 max-w-lg mx-auto">
    <h3 class="font-semibold mb-2">Mini-chat del task</h3>
    <div wire:poll.3s="loadMessages" class="h-56 overflow-y-auto bg-white border rounded p-2 mb-2">
        @forelse($messages as $msg)
            <div class="mb-1">
                <span class="font-bold text-xs text-blue-700">{{ $msg->user->name }}</span>
                <span class="text-xs text-gray-400">{{ $msg->created_at->format('H:i d/m') }}</span><br>
                <span class="text-sm">{{ $msg->message }}</span>
            </div>
        @empty
            <div class="text-gray-400 text-sm">Nessun messaggio.</div>
        @endforelse
    </div>
    <form wire:submit.prevent="sendMessage" class="flex gap-2">
        <input type="text" wire:model.defer="message" class="flex-1 border rounded px-2 py-1" placeholder="Scrivi un messaggio...">
        <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Invia</button>
    </form>
    @error('message') <div class="text-red-600 text-xs mt-1">{{ $message }}</div> @enderror
</div>
