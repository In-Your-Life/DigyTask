<div class="p-4 max-w-4xl mx-auto">
    <h2 class="text-2xl font-bold mb-2">{{ $task->title }}</h2>
    <div class="flex flex-wrap gap-2 mb-2">
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
        @foreach($task->roles as $role)
            <span class="inline-block bg-gray-200 rounded px-2 text-xs mr-1">{{ $role->name }}</span>
        @endforeach
        @foreach($task->assignedUsers as $user)
            <span class="inline-block bg-blue-100 rounded px-2 text-xs mr-1">{{ $user->name }}</span>
        @endforeach
        @foreach($task->tags as $tag)
            <span class="inline-block bg-yellow-100 rounded px-2 text-xs mr-1">#{{ $tag->name }}</span>
        @endforeach
        @livewire('tag-manager', ['taskId' => $task->id], key('tag-manager-'.$task->id))
        <span class="inline-block bg-gray-100 rounded px-2 text-xs mr-1">Scadenza: {{ $task->deadline ? $task->deadline->format('d/m/Y') : '-' }}</span>
    </div>
    <div class="mb-4 text-gray-700">
        {!! nl2br(e($task->description)) !!}
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
        <div>
            <h3 class="font-semibold mb-2">Meta SEO</h3>
            @livewire('seo-meta-form', ['taskId' => $task->id], key('seo-meta-'.$task->id))
            <h3 class="font-semibold mt-4 mb-2">Checklist SEO</h3>
            @livewire('seo-checklist', ['taskId' => $task->id], key('seo-checklist-'.$task->id))
        </div>
        <div>
            <h3 class="font-semibold mb-2">Allegati</h3>
            @livewire('attachment-uploader', ['taskId' => $task->id], key('attach-'.$task->id))
            <ul class="mt-2">
                @foreach($task->attachments as $attachment)
                    <li class="mb-1">
                        <a href="{{ asset('storage/'.$attachment->filepath) }}" target="_blank" class="text-blue-600 hover:underline">{{ $attachment->file_type }} - {{ $attachment->filepath }}</a>
                        @if($attachment->alt_text)
                            <span class="text-xs text-gray-500">({{ $attachment->alt_text }})</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    <div class="mb-4">
        <h3 class="font-semibold mb-2">Commenti</h3>
        @livewire('comments', ['taskId' => $task->id], key('comments-'.$task->id))
    </div>
    <div class="mb-4">
        <h3 class="font-semibold mb-2">Attività</h3>
        <ul class="text-sm text-gray-600">
            @foreach($task->activities as $activity)
                <li class="mb-1">{{ $activity->created_at->format('d/m/Y H:i') }} - {{ $activity->description }} @if($activity->user) <span class="text-xs">({{ $activity->user->name }})</span>@endif</li>
            @endforeach
        </ul>
    </div>
    <div class="mb-4">
        <h3 class="font-semibold mb-2">Condivisioni</h3>
        <ul class="text-sm">
            @foreach($task->shares as $share)
                <li class="mb-1 flex items-center gap-2">
                    <span class="text-xs">Link:</span> <a href="{{ url('/share/'.$share->token) }}" class="text-blue-600 hover:underline">{{ url('/share/'.$share->token) }}</a>
                    @if($share->expires_at)
                        <span class="text-xs">(Scade: {{ $share->expires_at->format('d/m/Y H:i') }})</span>
                    @endif
                    @if(!$share->is_active)
                        <span class="text-xs text-red-500">(Disattivato)</span>
                    @else
                        <button wire:click="deactivateShare({{ $share->id }})" class="text-xs text-red-600 hover:underline">Disattiva</button>
                        <button wire:click="regenerateSharedHtml({{ $share->id }})" class="text-xs text-gray-600 hover:underline">Rigenera HTML</button>
                        @auth
                            @php
                                $latestSharedPage = $task->sharedPages->where('task_id', $task->id)->sortByDesc('version')->first();
                            @endphp
                            @if($latestSharedPage)
                                <a href="{{ route('shared-pages.edit', ['task' => $task->id, 'sharedPage' => $latestSharedPage->id]) }}" class="text-xs text-blue-700 hover:underline">Modifica HTML condiviso</a>
                            @endif
                        @endauth
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    <div class="mb-4">
        <form wire:submit.prevent="generatePublicShare" class="inline">
            <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">Condividi esternamente</button>
        </form>
        @if(session('public_share_link'))
            <div class="mt-2 text-sm">
                <span class="font-semibold">Link pubblico:</span>
                <input type="text" readonly value="{{ session('public_share_link') }}" class="border rounded px-2 py-1 w-64" onclick="this.select()">
            </div>
        @endif
    </div>
    <div class="mb-4">
        <h3 class="font-semibold mb-2">Stato Task</h3>
        <div class="flex items-center gap-2">
            @php
                $statuses = [
                    'draft' => 'Bozza',
                    'pending' => 'In Attesa',
                    'in_progress' => 'In Lavorazione',
                    'review' => 'In Revisione',
                    'completed' => 'Completato',
                ];
                $current = $task->status;
                $user = auth()->user();
            @endphp
            @foreach($statuses as $status => $label)
                @php
                    $can = app(\Illuminate\Contracts\Auth\Access\Gate::class)->forUser($user)->allows('canTransitionTo', [$task, $status]);
                @endphp
                <button wire:click="changeStatus('{{ $status }}')"
                        class="px-3 py-1 rounded border text-xs {{ $current === $status ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }} {{ $can ? 'hover:bg-blue-200' : 'opacity-50 cursor-not-allowed' }}"
                        @if(!$can) disabled @endif>
                    {{ $label }}
                </button>
                @if(!$loop->last)
                    <span class="text-gray-400">→</span>
                @endif
            @endforeach
        </div>
        @if(session('error'))
            <div class="text-red-600 text-xs mt-2">{{ session('error') }}</div>
        @endif
    </div>
    <div class="mb-4 flex items-center gap-4">
        <form wire:submit.prevent="toggleTemplate">
            <button type="submit" class="px-3 py-1 rounded border text-xs {{ $task->is_template ? 'bg-yellow-200 text-yellow-900' : 'bg-gray-100 text-gray-700' }} hover:bg-yellow-300">
                {{ $task->is_template ? 'Rimuovi da Template' : 'Salva come Template' }}
            </button>
        </form>
        @if($task->is_template)
            <span class="text-xs text-yellow-700">Questo task è un template</span>
        @endif
    </div>
    <div class="mb-4">
        <h3 class="font-semibold mb-2">Condivisione Interna</h3>
        <form wire:submit.prevent="shareInternal" class="flex items-center gap-2 mb-2">
            <select wire:model="internalShareUserId" class="border rounded px-2 py-1">
                <option value="">Seleziona utente...</option>
                @foreach(\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
            <span class="text-xs text-gray-400">o</span>
            <select wire:model="internalShareRoleId" class="border rounded px-2 py-1">
                <option value="">Seleziona ruolo...</option>
                @foreach(\App\Models\Role::all() as $role)
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Condividi</button>
        </form>
        <ul class="text-sm">
            @foreach($task->internalShares as $share)
                @if($share->is_active)
                    <li class="mb-1 flex items-center gap-2">
                        @if($share->user)
                            <span class="text-xs">Utente:</span> <span class="font-semibold">{{ $share->user->name }}</span>
                        @elseif($share->role)
                            <span class="text-xs">Ruolo:</span> <span class="font-semibold">{{ $share->role->name }}</span>
                        @endif
                        @if($share->expires_at)
                            <span class="text-xs">(Scade: {{ $share->expires_at->format('d/m/Y H:i') }})</span>
                        @endif
                        <button wire:click="revokeInternalShare({{ $share->id }})" class="text-xs text-red-600 hover:underline">Revoca</button>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
    <div class="mb-4">
        @livewire('task-chat', ['taskId' => $task->id], key('task-chat-'.$task->id))
    </div>
    <div class="mb-4">
        <h3 class="font-semibold mb-2">Integrazioni esterne</h3>
        @if(session('success_external'))
            <div class="mb-2 text-green-600">{{ session('success_external') }}</div>
        @endif
        <form wire:submit.prevent="saveExternalLinks" class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold mb-1">Figma URL</label>
                <input type="url" wire:model.defer="figma_url" class="border rounded px-2 py-1 w-full" placeholder="https://www.figma.com/file/...">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Notion URL</label>
                <input type="url" wire:model.defer="notion_url" class="border rounded px-2 py-1 w-full" placeholder="https://www.notion.so/...">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">GitHub URL</label>
                <input type="url" wire:model.defer="github_url" class="border rounded px-2 py-1 w-full" placeholder="https://github.com/...">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Slack Channel</label>
                <input type="text" wire:model.defer="slack_channel" class="border rounded px-2 py-1 w-full" placeholder="#canale-slack">
            </div>
            <div>
                <label class="block text-xs font-semibold mb-1">Webhook URL</label>
                <input type="url" wire:model.defer="webhook_url" class="border rounded px-2 py-1 w-full" placeholder="https://...">
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Salva integrazioni</button>
            </div>
        </form>
        <div class="flex flex-wrap gap-4 items-center mt-4">
            @if($task->figma_url)
                <a href="{{ $task->figma_url }}" target="_blank" class="flex items-center gap-1 text-purple-600 hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
                    Figma
                </a>
            @endif
            @if($task->notion_url)
                <a href="{{ $task->notion_url }}" target="_blank" class="flex items-center gap-1 text-black hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect x="4" y="4" width="16" height="16" rx="2" stroke-width="2"/></svg>
                    Notion
                </a>
            @endif
            @if($task->github_url)
                <a href="{{ $task->github_url }}" target="_blank" class="flex items-center gap-1 text-gray-800 hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12c0 4.42 2.87 8.17 6.84 9.5.5.09.66-.22.66-.48 0-.24-.01-.87-.01-1.7-2.78.6-3.37-1.34-3.37-1.34-.45-1.15-1.1-1.46-1.1-1.46-.9-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.89 1.52 2.34 1.08 2.91.83.09-.65.35-1.08.63-1.33-2.22-.25-4.56-1.11-4.56-4.95 0-1.09.39-1.98 1.03-2.68-.1-.25-.45-1.27.1-2.65 0 0 .84-.27 2.75 1.02A9.56 9.56 0 0112 6.8c.85.004 1.71.12 2.51.35 1.91-1.29 2.75-1.02 2.75-1.02.55 1.38.2 2.4.1 2.65.64.7 1.03 1.59 1.03 2.68 0 3.85-2.34 4.7-4.57 4.95.36.31.68.92.68 1.85 0 1.33-.01 2.4-.01 2.73 0 .27.16.58.67.48A10.01 10.01 0 0022 12c0-5.52-4.48-10-10-10z" stroke-width="2"/></svg>
                    GitHub
                </a>
            @endif
            @if($task->slack_channel)
                <span class="flex items-center gap-1 text-pink-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
                    Slack: {{ $task->slack_channel }}
                </span>
            @endif
            @if($task->webhook_url)
                <span class="flex items-center gap-1 text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M17 17v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1" stroke-width="2"/></svg>
                    Webhook attivo
                </span>
            @endif
        </div>
    </div>
    <div class="mb-4">
        <h3 class="font-semibold mb-2">Azioni Speciali</h3>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('tasks.duplicate', $task->id) }}" class="px-3 py-1 rounded border text-xs bg-gray-100 text-gray-700 hover:bg-gray-200">Duplica Task</a>
            <a href="{{ route('tasks.changeStatus', $task->id) }}" class="px-3 py-1 rounded border text-xs bg-gray-100 text-gray-700 hover:bg-gray-200">Cambia Stato</a>
            <a href="{{ route('tasks.assign', $task->id) }}" class="px-3 py-1 rounded border text-xs bg-gray-100 text-gray-700 hover:bg-gray-200">Assegna Task</a>
            <a href="{{ route('tasks.makeTemplate', $task->id) }}" class="px-3 py-1 rounded border text-xs bg-gray-100 text-gray-700 hover:bg-gray-200">Salva come Template</a>
        </div>
    </div>
</div>
