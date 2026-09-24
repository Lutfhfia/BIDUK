<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * Memastikan hanya Super Admin dan Kepala Sekolah
     * yang dapat mengakses Log Aktivitas.
     */
    private function authorizeAccess(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && (
                $user->isSuperAdmin() ||
                $user->isKepalaSekolah()
            ),
            403
        );
    }

    /**
     * Menampilkan daftar log aktivitas.
     */
    public function index(Request $request)
    {
        $this->authorizeAccess();

        $query = ActivityLog::with('user.role')
        ->latest();

        // Filter pengguna
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter modul
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter aktivitas
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter tanggal mulai
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        // Filter tanggal akhir
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query
            ->paginate(15)
            ->withQueryString();

        $users = \App\Models\User::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $modules = ActivityLog::query()
            ->whereNotNull('module')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $actions = ActivityLog::query()
            ->whereNotNull('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('activity-logs.index', compact(
            'logs',
            'users',
            'modules',
            'actions'
        ));
    }

    /**
     * Menampilkan detail satu log aktivitas.
     */
    public function show(ActivityLog $activityLog)
    {
        $this->authorizeAccess();

        $activityLog->load('user', 'subject');

        return view('activity-logs.show', compact('activityLog'));
    }
}