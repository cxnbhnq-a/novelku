<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class AdminLogsController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->has('log_type') && $request->log_type != '') {
            $query->where('log_type', $request->log_type);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }

        if ($request->has('search') && $request->search != '') {
            $searchText = $request->search;
            $query->where(function ($q) use ($searchText) {
                $q->where('email', 'like', '%' . $searchText . '%')
                  ->orWhere('message', 'like', '%' . $searchText . '%')
                  ->orWhere('ip_address', 'like', '%' . $searchText . '%')
                  ->orWhere('user_agent', 'like', '%' . $searchText . '%');
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('severity') && $request->severity != '') {
            $query->where('severity', $request->severity);
        }

        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $perPage = $request->get('per_page', 25);
        $logs = $query->paginate($perPage);

        $logTypes = ActivityLog::distinct('log_type')->pluck('log_type')->toArray();
        $statuses = ['success', 'failed', 'warning'];
        $severities = ['info', 'warning', 'critical'];

        // REVISI DI SINI: Ngarah ke dashboard.admin-logs
       return view('dashboard.admin-logs', compact(
            'logs', 'logTypes', 'statuses', 'severities'
        ));
    }

    public function detail($id)
    {
        $log = ActivityLog::findOrFail($id);

        return response()->json([
            'id' => $log->id,
            'log_type' => $log->log_type,
            'log_type_label' => ActivityLog::getLogLabel($log->log_type),
            'message' => $log->message,
            'email' => $log->email,
            'user_id' => $log->user_id,
            'ip_address' => $log->ip_address,
            'user_agent' => $log->user_agent,
            'endpoint' => $log->endpoint,
            'payload' => $log->payload,
            'response' => $log->response,
            'stack_trace' => $log->stack_trace,
            'status' => $log->status,
            'severity' => $log->severity,
            'created_at' => $log->created_at->format('d M Y H:i:s'),
        ]);
    }

    public function export(Request $request)
    {
        $query = ActivityLog::query();

        if ($request->has('log_type') && $request->log_type != '') {
            $query->where('log_type', $request->log_type);
        }
        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }
        if ($request->has('search') && $request->search != '') {
            $searchText = $request->search;
            $query->where(function ($q) use ($searchText) {
                $q->where('email', 'like', '%' . $searchText . '%')
                  ->orWhere('message', 'like', '%' . $searchText . '%')
                  ->orWhere('ip_address', 'like', '%' . $searchText . '%');
            });
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $data = $logs->map(function ($log) {
            return [
                'Waktu' => $log->created_at->format('d M Y H:i:s'),
                'Jenis Log' => ActivityLog::getLogLabel($log->log_type),
                'Email' => $log->email ?? '-',
                'User ID' => $log->user_id ?? '-',
                'IP Address' => $log->ip_address ?? '-',
                'Endpoint' => $log->endpoint ?? '-',
                'Status' => ucfirst($log->status),
                'Pesan' => $log->message,
            ];
        });

        $filename = 'activity-logs-' . now()->format('Y-m-d-H-i-s') . '.csv';
        
        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Waktu', 'Jenis Log', 'Email', 'User ID', 'IP Address', 'Endpoint', 'Status', 'Pesan']);
            foreach ($data as $row) {
                fputcsv($handle, array_map([$this, 'sanitizeCsvCell'], array_values($row)));
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ]);
    }

    private function sanitizeCsvCell(mixed $value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r\n]/', $value) ? "'".$value : $value;
    }

    public function deleteOldLogs(Request $request)
    {
        $days = $request->get('days', 30);
        $deletedCount = ActivityLog::deleteOldLogs($days);
        return back()->with('success', "Berhasil menghapus {$deletedCount} log yang lebih tua dari {$days} hari.");
    }

    public function delete($id)
    {
        $log = ActivityLog::findOrFail($id);
        $log->delete();
        return back()->with('success', 'Log berhasil dihapus.');
    }

    public function statistics()
    {
        $today = now()->startOfDay();
        
        $stats = [
            'total_logs' => ActivityLog::count(),
            'today_logs' => ActivityLog::where('created_at', '>=', $today)->count(),
            'failed_today' => ActivityLog::where('status', 'failed')->where('created_at', '>=', $today)->count(),
            'critical_logs' => ActivityLog::where('severity', 'critical')->count(),
        ];

        return response()->json($stats);
    }

    public function clearAll(Request $request)
    {
        ActivityLog::truncate();
        return back()->with('success', 'Semua log berhasil disapu bersih.');
    }
}
