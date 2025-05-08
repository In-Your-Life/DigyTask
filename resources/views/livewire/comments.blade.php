<div>
    <ul class="mb-4">
        @foreach($comments as $comment)
            <li class="mb-2 border-b pb-2">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-semibold text-sm">{{ $comment->user->name }}</span>
                    <span class="text-xs text-gray-500">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="text-gray-800">{!! nl2br(e($comment->content)) !!}</div>
            </li>
        @endforeach
    </ul>
    <form wire:submit.prevent="addComment" class="flex flex-col gap-2">
        <textarea wire:model.defer="content" rows="2" class="border rounded w-full p-2" placeholder="Aggiungi un commento... (usa @nome per menzionare)"></textarea>
        @error('content') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">Invia</button>
        </div>
    </form>
</div>
