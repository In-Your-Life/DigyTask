<div class="max-w-lg mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-xl font-bold mb-4">Preferenze utente</h2>
    @if(session('success'))
        <div class="mb-4 text-green-600">{{ session('success') }}</div>
    @endif
    <form wire:submit.prevent="save" class="space-y-4">
        <div>
            <label class="block text-sm font-semibold mb-1">Task per pagina</label>
            <input type="number" min="5" max="100" wire:model="per_page" class="border rounded px-2 py-1 w-24">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Tema</label>
            <select wire:model="theme" class="border rounded px-2 py-1">
                <option value="light">Chiaro</option>
                <option value="dark">Scuro</option>
                <option value="system">Sistema</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Notifiche in-app</label>
            <input type="checkbox" wire:model="notifications_enabled"> Abilita notifiche
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Notifiche email</label>
            <input type="checkbox" wire:model="email_notifications"> Abilita notifiche email
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Notifiche granulari</label>
            <div class="space-y-1 ml-2">
                <label><input type="checkbox" wire:model="granular_notifications.commenti"> Commenti</label><br>
                <label><input type="checkbox" wire:model="granular_notifications.assegnazioni"> Assegnazioni</label><br>
                <label><input type="checkbox" wire:model="granular_notifications.stato"> Cambi stato</label><br>
                <label><input type="checkbox" wire:model="granular_notifications.allegati"> Allegati</label>
            </div>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Salva preferenze</button>
    </form>
</div>
