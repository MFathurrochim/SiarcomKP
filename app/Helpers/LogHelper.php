<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    /**
     * Menyimpan log aktivitas ke database
     *
     * @param string $modul
     * @param string $aksi
     * @param string $detail
     * @param array|null $dataLama
     * @param array|null $dataBaru
     * @return void
     */
    public static function save($modul, $aksi, $detail, $dataLama = null, $dataBaru = null)
    {
        $user = Auth::user();

        // Menyesuaikan dengan kolom id_user dan username milikmu
        $idUser   = $user ? $user->id_user : null;
        $namaUser = $user ? $user->username : 'System/Guest'; 

        ActivityLog::create([
            'id_user'   => $idUser,
            'nama_user' => $namaUser, 
            'aktivitas' => [
                'modul'     => $modul,
                'aksi'      => $aksi,
                'nama_user' => $namaUser, 
                'detail' => $detail,
                'data_lama' => $dataLama,
                'data_baru' => $dataBaru,
            ],
            'created_at' => now()
        ]);
    }
}