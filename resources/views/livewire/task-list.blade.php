<div class="p-4">
    <h2 class="text-2xl font-bold mb-4">Elenco Task</h2>
    <div class="flex flex-wrap gap-2 mb-4">
        <input type="text" wire:model.debounce.500ms="search" placeholder="Cerca titolo..." class="border rounded px-2 py-1" />
        <select wire:model="status" class="border rounded px-2 py-1">
            <option value="">Stato</option>
            <option value="draft">Bozza</option>
            <option value="pending">In Attesa</option>
            <option value="in_progress">In Lavorazione</option>
            <option value="review">In Revisione</option>
            <option value="completed">Completato</option>
        </select>
        <select wire:model="priority" class="border rounded px-2 py-1">
            <option value="">Priorità</option>
            <option value="low">Bassa</option>
            <option value="medium">Media</option>
            <option value="high">Alta</option>
            <option value="urgent">Urgente</option>
        </select>
        <select wire:model="role" class="border rounded px-2 py-1">
            <option value="">Reparto</option>
            @foreach($roles as $role)
                <option value="{{ $role->id }}">{{ $role->name }}</option>
            @endforeach
        </select>
        <select wire:model="assignee" class="border rounded px-2 py-1">
            <option value="">Assegnatario</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>
        {{-- Filtro tag avanzato --}}
        <div class="relative">
            <div class="flex flex-wrap gap-1 mb-1">
                @foreach($tags as $tagId)
                    @php $tagObj = $allTags->find($tagId); @endphp
                    @if($tagObj)
                        <span class="inline-flex items-center bg-yellow-100 rounded px-2 text-xs">
                            #{{ $tagObj->name }}
                            <button wire:click="removeTagFilter({{ $tagObj->id }})" class="ml-1 text-red-500 hover:text-red-700">&times;</button>
                        </span>
                    @endif
                @endforeach
            </div>
            <input type="text" wire:model.debounce.300ms="tagSearch" placeholder="Aggiungi tag filtro..." class="border rounded px-2 py-1 text-sm w-48">
            @if(!empty($tagSearch))
                <ul class="absolute bg-white border rounded shadow mt-1 w-48 z-10" style="max-height: 150px; overflow-y: auto;">
                    @foreach($allTags->filter(fn($tag) => str_contains(strtolower($tag->name), strtolower($tagSearch))) as $tag)
                        @if(!in_array($tag->id, $tags))
                            <li class="px-2 py-1 hover:bg-yellow-100 cursor-pointer" wire:click="addTagFilter({{ $tag->id }})">#{{ $tag->name }}</li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </div>
        {{-- Rimuovo il vecchio select tag --}}
        {{-- <select wire:model="tag" class="border rounded px-2 py-1">
            <option value="">Tag</option>
            @foreach($tags as $tag)
                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
            @endforeach
        </select> --}}
        <button wire:click="showTemplates" class="bg-blue-100 px-3 py-1 rounded text-blue-700">Template</button>
    </div>
    <table class="min-w-full bg-white border">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-2 py-1 border">Titolo</th>
                <th class="px-2 py-1 border">Stato</th>
                <th class="px-2 py-1 border">Priorità</th>
                <th class="px-2 py-1 border">Reparti</th>
                <th class="px-2 py-1 border">Assegnati</th>
                <th class="px-2 py-1 border">Tag</th>
                <th class="px-2 py-1 border">Scadenza</th>
                <th class="px-2 py-1 border">Azioni</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
                <tr>
                    <td class="border px-2 py-1 font-semibold">
                        <a href="{{ route('tasks.show', $task->id) }}" class="text-blue-600 hover:underline">{{ $task->title }}</a>
                    </td>
                    <td class="border px-2 py-1">
                        <span class="px-2 py-1 rounded text-xs {{
                            match($task->status) {
                                'draft' => 'bg-gray-200',
                                'pending' => 'bg-yellow-200',
                                'in_progress' => 'bg-blue-200',
                                'review' => 'bg-purple-200',
                                'completed' => 'bg-green-200',
                                default => 'bg-gray-100',
                            }
                        }}">
                            {{ __(ucwords(str_replace('_', ' ', $task->status))) }}
                        </span>
                    </td>
                    <td class="border px-2 py-1">
                        <span class="px-2 py-1 rounded text-xs {{
                            match($task->priority) {
                                'low' => 'bg-green-100',
                                'medium' => 'bg-blue-100',
                                'high' => 'bg-orange-200',
                                'urgent' => 'bg-red-200',
                                default => 'bg-gray-100',
                            }
                        }}">
                            {{ __(ucwords($task->priority)) }}
                        </span>
                    </td>
                    <td class="border px-2 py-1">
                        @foreach($task->roles as $role)
                            <span class="inline-block bg-gray-200 rounded px-2 text-xs mr-1">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="border px-2 py-1">
                        @foreach($task->assignedUsers as $user)
                            <span class="inline-block bg-blue-100 rounded px-2 text-xs mr-1">{{ $user->name }}</span>
                        @endforeach
                    </td>
                    <td class="border px-2 py-1">
                        @foreach($task->tags as $tag)
                            <span class="inline-block bg-yellow-100 rounded px-2 text-xs mr-1">#{{ $tag->name }}</span>
                        @endforeach
                    </td>
                    <td class="border px-2 py-1">{{ $task->deadline ? $task->deadline->format('d/m/Y') : '-' }}</td>
                    <td class="border px-2 py-1">
                        <a href="{{ route('tasks.show', $task->id) }}" class="text-indigo-600 hover:underline">Dettaglio</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-4">Nessun task trovato.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-4">
        {{ $tasks->links() }}
    </div>
</div>
