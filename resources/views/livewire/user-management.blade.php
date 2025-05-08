<div class="p-4">
    <h2 class="text-2xl font-bold mb-4">Gestione Utenti</h2>
    <label class="mb-2 inline-flex items-center">
        <input type="checkbox" wire:model="activeOnly" class="mr-2"> Mostra solo utenti attivi
    </label>
    <table class="min-w-full bg-white border mt-2">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-2 py-1 border">Nome</th>
                <th class="px-2 py-1 border">Email</th>
                <th class="px-2 py-1 border">Ruoli</th>
                <th class="px-2 py-1 border">Stato</th>
                <th class="px-2 py-1 border">Azioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="border px-2 py-1">{{ $user->name }}</td>
                    <td class="border px-2 py-1">{{ $user->email }}</td>
                    <td class="border px-2 py-1">
                        @if($editingUserId === $user->id)
                            <select wire:model="editingRoles" multiple class="border rounded p-1 w-full">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <button wire:click="saveRoles({{ $user->id }})" class="text-xs bg-green-600 text-white rounded px-2 py-1 ml-2">Salva</button>
                        @else
                            @foreach($user->roles as $role)
                                <span class="inline-block bg-gray-200 rounded px-2 text-xs mr-1">{{ $role->name }}</span>
                            @endforeach
                            <button wire:click="editRoles({{ $user->id }})" class="text-xs text-blue-600 hover:underline ml-2">Modifica</button>
                        @endif
                    </td>
                    <td class="border px-2 py-1">
                        <span class="text-xs {{ $user->active ? 'text-green-600' : 'text-red-600' }}">
                            {{ $user->active ? 'Attivo' : 'Disattivato' }}
                        </span>
                    </td>
                    <td class="border px-2 py-1">
                        <button wire:click="toggleActive({{ $user->id }})" class="text-xs bg-gray-300 rounded px-2 py-1">
                            {{ $user->active ? 'Disattiva' : 'Attiva' }}
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
