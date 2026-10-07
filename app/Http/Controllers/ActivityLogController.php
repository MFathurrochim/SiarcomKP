<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $filterModul = $request->get('modul');
        $query = ActivityLog::with('user')->orderBy('id_log', 'desc');

        if (!empty($filterModul)) {
            $query->where('aktivitas->modul', $filterModul);
        }

        $logs = $query->paginate(25);
        
        $daftarModul = ['CSR', 'SOSMED', 'BERITA', 'LOGIN']; 

        if ($request->wantsJson() || $request->ajax()) {
            $formattedLogs = collect($logs->items())->map(function($log) {
                $aktivitasData = is_array($log->aktivitas) ? $log->aktivitas : json_decode(json_encode($log->aktivitas), true);
                $aktivitas = $aktivitasData ?? [];

                return [
                    'id_log'    => $log->id_log,
                    'waktu'     => $log->created_at ? $log->created_at->format('d M Y H:i') : '-',
                    'nama_user' => $log->nama_user ?? $log->user->username ?? $aktivitas['nama_user'] ?? 'Unknown',
                    'modul'     => $aktivitas['modul'] ?? '-',
                    'aksi'      => $aktivitas['aksi'] ?? '-',
                    'detail'    => $aktivitas['detail'] ?? $aktivitas['deskripsi'] ?? '-', 
                ];
            });

            return response()->json($formattedLogs);
        }

        // Default return view jika diakses biasa
        return view('pages.pengaturan.log_aktivitas', compact('logs', 'daftarModul', 'filterModul'));
    }

    /**
     * AJAX/API Endpoint
     */
    public function show(int $id)
    {
        $log = ActivityLog::with('user')->find($id);

        if (!$log) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Log aktivitas tidak ditemukan'
            ], 404);
        }

        // Konversi ke array secara paksa agar aman dibaca oleh PHP
        $aktivitasData = is_array($log->aktivitas) ? $log->aktivitas : json_decode(json_encode($log->aktivitas), true);
        $aktivitas = $aktivitasData ?? [];

        return response()->json([
            'status' => 'success',
            'data' => [
                'id_log'    => $log->id_log,
                // PERBAIKAN: Gunakan username agar tidak muncul Sistem/Unknown lagi
                'nama_user' => $log->nama_user ?? $log->user->username ?? $aktivitas['nama_user'] ?? 'Unknown',
                'tanggal'   => $log->created_at ? $log->created_at->format('d M Y H:i:s') : '-',
                'modul'     => $aktivitas['modul'] ?? '-',
                'aksi'      => $aktivitas['aksi'] ?? '-', 
                'detail'    => $aktivitas['detail'] ?? $aktivitas['deskripsi'] ?? '-',          
                'data_lama' => $aktivitas['data_lama'] ?? $aktivitas['data_detail']['sebelum_diedit'] ?? null, 
                'data_baru' => $aktivitas['data_baru'] ?? $aktivitas['data_detail']['setelah_diedit'] ?? $aktivitas['data_detail'] ?? null,
            ]
        ]);
    }
}