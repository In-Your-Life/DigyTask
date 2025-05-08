<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request) {
        \App\Models\Comment::create($request->only(['task_id', 'user_id', 'content']));
        return redirect()->back();
    }
    public function update(Request $request, $id) { /* ... */ }
    public function destroy($id) { /* ... */ }
} 