<?php

namespace App\Http\Controllers;

use App\Models\Pilar;
use Illuminate\Http\Request;
use App\Helpers\LogHelper; 
use Illuminate\Support\Facades\Auth;

class PilarController extends Controller
{
    // 1. Menampilkan Semua Data Pilar + Anggarannya 
    public function index()
    {

        $pilars = Pilar::with(['anggarans' => function($query) {
            $query->where('tahun', '2026');
        }])->get();

        return view('pages.pilar.index', compact('pilars'));
    }

    // 2. Menyimpan Data Pilar Baru
    public function store(Request $request)
    {
        // FIX: Perbaikan target unique ke tabel pilar kolom nama_pilar
        $request->validate([
            'nama_pilar' => 'required|string|max:255|unique:pilar,nama_pilar',
        ]);

        $pilar = Pilar::create([
            'nama_pilar' => $request->nama_pilar
        ]);

        // Catat ke Activity Log otomatis
        LogHelper::save(
            'Pilar', 
            'Tambah Data', 
            "User " . Auth::user()->username . " menambahkan kategori pilar baru: " . $pilar->nama_pilar . " (ID: #{$pilar->id_pilar})"
        );

        return redirect()->back()->with('success', 'Data pilar berhasil ditambahkan!');
    }

    // 3. Mengubah Data Pilar
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama_pilar' => 'required|string|max:255|unique:pilar,nama_pilar,' . $id . ',id_pilar',
        ]);

        $pilar = Pilar::findOrFail($id);
        $namaLama = $pilar->nama_pilar;
        
        $pilar->update([
            'nama_pilar' => $request->nama_pilar
        ]);

        // Catat ke Activity Log otomatis dengan perubahan namanya
        LogHelper::save(
            'Pilar', 
            'Ubah Data', 
            "User " . Auth::user()->username . " mengubah nama pilar ID #{$id} dari [{$namaLama}] menjadi [{$pilar->nama_pilar}]"
        );

        return redirect()->back()->with('success', 'Data pilar berhasil diperbarui!');
    }

    // 4. Menghapus Data Pilar
    public function destroy(int $id)
    {
        $pilar = Pilar::findOrFail($id);
        $namaDihapus = $pilar->nama_pilar;

        $pilar->delete();

        // Catat ke Activity Log otomatis
        LogHelper::save(
            'Pilar', 
            'Hapus Data', 
            "User " . Auth::user()->username . " menghapus kategori pilar: " . $namaDihapus . " (ID: #{$id})"
        );
        return redirect()->back()->with('success', 'Data pilar berhasil dihapus!');
    }
}