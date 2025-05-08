<div class="overflow-x-auto">
    <div class="flex gap-4">
        @foreach($statuses as $statusKey => $statusLabel)
            <div class="min-w-[260px] bg-gray-50 rounded shadow p-2 flex-1">
                <h3 class="font-bold text-center mb-2">{{ $statusLabel }}</h3>
                <div class="flex flex-col gap-2">
                    @foreach($tasksByStatus[$statusKey] as $task)
                        <div class="bg-white border rounded p-2 shadow-sm">
                            <div class="font-semibold">{{ $task->title }}</div>
                            <div class="text-xs text-gray-500 mb-1">Priorità: {{ ucfirst($task->priority) }}</div>
                            <div class="flex flex-wrap gap-1 mb-1">
                                @foreach($task->roles as $role)
                                    <span class="bg-gray-200 rounded px-2 text-xs">{{ $role->name }}</span>
                                @endforeach
                                @foreach($task->assignedUsers as $user)
                                    <span class="bg-blue-100 rounded px-2 text-xs">{{ $user->name }}</span>
                                @endforeach
                            </div>
                            <div class="flex gap-1 mt-2">
                                @foreach($statuses as $moveKey => $moveLabel)
                                    @if($moveKey !== $statusKey)
                                        <button wire:click="moveTask({{ $task->id }}, '{{ $moveKey }}')" class="text-xs bg-blue-200 hover:bg-blue-400 rounded px-2 py-1 mr-1">→ {{ $moveLabel }}</button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
