<div>
    {{-- Care about people's approval and you will be their prisoner. --}}
</div>

<form wire:submit.prevent="save" class="flex flex-col gap-2">
    <label class="text-sm font-semibold">Meta Title (max 60 caratteri)</label>
    <input type="text" wire:model.defer="seo_title" maxlength="60" class="border rounded p-1" />
    @error('seo_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

    <label class="text-sm font-semibold">Meta Description (max 160 caratteri)</label>
    <textarea wire:model.defer="seo_description" maxlength="160" rows="2" class="border rounded p-1"></textarea>
    @error('seo_description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

    <label class="text-sm font-semibold">Keywords (separate da virgola)</label>
    <input type="text" wire:model.defer="seo_keywords" class="border rounded p-1" />
    @error('seo_keywords') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700">Salva</button>
    </div>
    @if(session('seo_saved'))
        <div class="text-green-600 text-xs mt-2">{{ session('seo_saved') }}</div>
    @endif
</form>
