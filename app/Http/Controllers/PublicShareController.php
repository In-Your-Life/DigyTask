<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TaskShare;
use App\Models\SharedPage;
use Illuminate\Http\Response;
use App\Models\ShareAccessLog;

class PublicShareController extends Controller
{
    public function show($token)
    {
        $share = TaskShare::where('token', $token)->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })->firstOrFail();
        $sharedPage = SharedPage::where('task_id', $share->task_id)->latest('generated_at')->firstOrFail();
        // Log accesso
        ShareAccessLog::create([
            'task_share_id' => $share->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'accessed_at' => now(),
        ]);
        return new Response($sharedPage->html_content);
    }
}
