<div class="p-4 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Template di Task</h2>
    <button wire:click="showTasks" class="mb-4 bg-gray-200 px-3 py-1 rounded">Torna ai Task</button>
    <table class="w-full border text-sm">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">Titolo</th>
                <th class="p-2">Ruoli</th>
                <th class="p-2">Tag</th>
                <th class="p-2">Azioni</th>
            </tr>
        </thead>
        <tbody>
            @foreach($templates as $template)
                <tr class="border-b">
                    <td class="p-2">{{ $template->title }}</td>
                    <td class="p-2">
                        @foreach($template->roles as $role)
                            <span class="bg-gray-200 rounded px-2 text-xs mr-1">{{ $role->name }}</span>
                        @endforeach
                    </td>
                    <td class="p-2">
                        @foreach($template->tags as $tag)
                            <span class="bg-yellow-100 rounded px-2 text-xs mr-1">#{{ $tag->name }}</span>
                        @endforeach
                    </td>
                    <td class="p-2">
                        <button wire:click="duplicateFromTemplate({{ $template->id }})" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Usa come modello</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div> 