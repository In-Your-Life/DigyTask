<div>
    <form wire:submit.prevent="upload" class="flex flex-col gap-2 mb-4">
        <input type="file" wire:model="file" class="border rounded p-1" />
        <input type="text" wire:model="file_type" placeholder="Tipo file (es. image/png, pdf, docx)" class="border rounded p-1" />
        <input type="text" wire:model="alt_text" placeholder="Alt text (opzionale, per immagini)" class="border rounded p-1" />
        @error('file') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        @error('file_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        <div class="flex justify-end">
            <button type="submit" class="bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700">Carica</button>
        </div>
        <div wire:loading wire:target="file" class="text-xs text-gray-500">Caricamento in corso...</div>
    </form>
    <ul>
        @foreach($attachments as $attachment)
            <li class="mb-1">
                <a href="{{ asset('storage/'.$attachment->filepath) }}" target="_blank" class="text-blue-600 hover:underline">{{ $attachment->file_type }} - {{ basename($attachment->filepath) }}</a>
                @if($attachment->alt_text)
                    <span class="text-xs text-gray-500">({{ $attachment->alt_text }})</span>
                @endif
            </li>
        @endforeach
    </ul>
</div>
