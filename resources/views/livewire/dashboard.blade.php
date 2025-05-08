<div class="p-4">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Dashboard</h2>
        @livewire('notification-bell')
    </div>
    @if(in_array('Project Manager', $roles) || in_array('Capo Sviluppo', $roles))
        <div class="mb-6">
            @livewire('project-stats')
        </div>
        <div class="mb-6">
            @livewire('task-kanban')
        </div>
    @else
        <div class="mb-6">
            @livewire('task-list')
        </div>
    @endif
</div>
