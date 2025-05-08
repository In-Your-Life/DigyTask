<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
    <div class="mb-2 flex flex-wrap gap-2">
        @foreach($tags as $tag)
            <span class="inline-flex items-center bg-yellow-100 rounded px-2 text-xs">
                #{{ $tag->name }}
                <button wire:click="removeTag({{ $tag->id }})" class="ml-1 text-red-500 hover:text-red-700">&times;</button>
            </span>
        @endforeach
    </div>
    <div class="relative">
        <input type="text" wire:model.debounce.300ms="tagInput" placeholder="Aggiungi tag..." class="border rounded px-2 py-1 text-sm w-48">
        @if($tagInput && count($suggestions))
            <ul class="absolute bg-white border rounded shadow mt-1 w-48 z-10">
                @foreach($suggestions as $suggestion)
                    <li class="px-2 py-1 hover:bg-yellow-100 cursor-pointer" wire:click="addTag('{{ $suggestion }}')">#{{ $suggestion }}</li>
                @endforeach
            </ul>
        @endif
    </div>
    <button wire:click="addTag" class="ml-2 bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700">Aggiungi</button>
</div>
