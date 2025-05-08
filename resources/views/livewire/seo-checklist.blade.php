<div class="flex flex-col gap-2">
    <label class="inline-flex items-center">
        <input type="checkbox" wire:model="checklist.title_ok" class="mr-2"> Title tag presente e lunghezza adeguata
    </label>
    <label class="inline-flex items-center">
        <input type="checkbox" wire:model="checklist.desc_ok" class="mr-2"> Meta Description presente e significativa
    </label>
    <label class="inline-flex items-center">
        <input type="checkbox" wire:model="checklist.h1_ok" class="mr-2"> Uso corretto dei titoli H1/H2
    </label>
    <label class="inline-flex items-center">
        <input type="checkbox" wire:model="checklist.alt_ok" class="mr-2"> Immagini con alt text appropriato
    </label>
    <label class="inline-flex items-center">
        <input type="checkbox" wire:model="checklist.url_ok" class="mr-2"> URL SEO-friendly
    </label>
    <label class="inline-flex items-center">
        <input type="checkbox" wire:model="checklist.content_ok" class="mr-2"> Contenuto originale e sufficiente
    </label>
    <div class="mt-2 text-xs text-green-600" wire:loading.remove>Checklist aggiornata automaticamente.</div>
</div>
