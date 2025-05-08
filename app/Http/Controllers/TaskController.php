<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index() { /* ... */ }
    public function create() { /* ... */ }
    public function store(Request $request) {
        $task = \App\Models\Task::create($request->only(['title', 'description']));
        return redirect()->route('tasks.show', $task);
    }
    public function show($id) { /* ... */ }
    public function edit($id) { /* ... */ }
    public function update(Request $request, $id) { /* ... */ }
    public function destroy($id) { /* ... */ }

    // Custom actions
    public function duplicate($id) { /* ... */ }
    public function changeStatus(Request $request, $id) { /* ... */ }
    public function assign(Request $request, $id) { /* ... */ }
    public function makeTemplate($id) { /* ... */ }
} 