<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Modifica HTML condiviso (versione {{ $sharedPage->version }})</h2>
    @if($sharedPage->editedBy)
        <div class="mb-2 text-xs text-gray-500">Ultima modifica: {{ $sharedPage->editedBy->name }} ({{ $sharedPage->updated_at ? $sharedPage->updated_at->format('d/m/Y H:i') : '-' }})</div>
    @endif
    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif
    <form wire:submit.prevent="save">
        <div class="mb-4">
            <input id="trix-content" type="hidden" wire:model.defer="htmlContent">
            <trix-editor input="trix-content"></trix-editor>
            @error('htmlContent') <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Salva Modifiche</button>
        <a href="{{ url()->previous() }}" class="ml-4 text-gray-600 hover:underline">Annulla</a>
    </form>
</div>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/trix/2.0.0/trix.umd.min.js"></script>
<script>
    document.addEventListener('trix-change', function(e) {
        window.livewire.find('@this.id').set('htmlContent', e.target.innerHTML);
    });
</script>
