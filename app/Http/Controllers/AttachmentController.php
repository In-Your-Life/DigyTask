<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function store(Request $request) {
        \App\Models\Attachment::create($request->only(['task_id', 'user_id', 'filepath', 'file_type']));
        return redirect()->back();
    }
    public function destroy($id) { /* ... */ }
    public function download($id) { /* ... */ }
} 