<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\SharedPageEditor;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/share/{token}', [\App\Http\Controllers\PublicShareController::class, 'show'])->name('public.share');

Route::middleware(['auth'])->group(function () {
    Route::get('/tasks/{task}/shared-pages/{sharedPage}/edit', SharedPageEditor::class)
        ->name('shared-pages.edit');

    // Task CRUD, duplicazione, template, stato, assegnazione
    Route::resource('tasks', App\Http\Controllers\TaskController::class);
    Route::post('/tasks/{task}/duplicate', [App\Http\Controllers\TaskController::class, 'duplicate'])->name('tasks.duplicate');
    Route::post('/tasks/{task}/change-status', [App\Http\Controllers\TaskController::class, 'changeStatus'])->name('tasks.changeStatus');
    Route::post('/tasks/{task}/assign', [App\Http\Controllers\TaskController::class, 'assign'])->name('tasks.assign');
    Route::post('/tasks/{task}/template', [App\Http\Controllers\TaskController::class, 'makeTemplate'])->name('tasks.makeTemplate');

    // Task Kanban (Livewire)
    Route::get('/tasks-kanban', App\Livewire\TaskKanban::class)->name('tasks.kanban');

    // Task Chat contestuale (Livewire)
    Route::get('/tasks/{task}/chat', App\Livewire\TaskChat::class)->name('tasks.chat');

    // Commenti (Livewire o Controller)
    Route::resource('comments', App\Http\Controllers\CommentController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // Allegati (upload/download/elimina)
    Route::resource('attachments', App\Http\Controllers\AttachmentController::class)
        ->only(['index', 'store', 'destroy']);
    Route::get('/attachments/{attachment}/download', [App\Http\Controllers\AttachmentController::class, 'download'])->name('attachments.download');

    // Tag Manager (Livewire)
    Route::resource('tags', App\Http\Controllers\TagController::class);

    // Preferenze utente (Livewire)
    Route::get('/user-preferences', App\Livewire\UserPreferencesForm::class)->name('user.preferences');

    // Notifiche (Livewire o Controller)
    // Route::get('/notifications', App\Livewire\NotificationCenter::class)->name('notifications.index');

    // Condivisione interna (tabella, revoca)
    // Route::get('/tasks/{task}/shares', App\Livewire\TaskShares::class)->name('tasks.shares');

    // Integrazioni esterne (Figma, Notion, GitHub, Slack, webhook)
    Route::post('/tasks/{task}/integrations', [App\Http\Controllers\TaskIntegrationController::class, 'store'])->name('tasks.integrations');

    // Logging, Audit
    Route::get('/activity-log', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity.log');
});

require __DIR__ . '/auth.php';
