<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeedbackAuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = FeedbackAuditLog::with('user:id,name,email')->latest('created_at');

        if ($v = $request->input('action')) {
            $query->where('action', 'like', "%{$v}%");
        }
        if ($v = $request->input('user_id')) {
            $query->where('user_id', $v);
        }
        if ($v = $request->input('from')) {
            $query->where('created_at', '>=', $v.' 00:00:00');
        }
        if ($v = $request->input('to')) {
            $query->where('created_at', '<=', $v.' 23:59:59');
        }

        return view('admin.audit.index', [
            'logs' => $query->paginate(50)->withQueryString(),
        ]);
    }
}
