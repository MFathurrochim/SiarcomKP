<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActivityLog;
use App\Models\Berita; 
use App\Exports\BeritaExport;
use Maatwebsite\Excel\Facades\Excel;

class BeritaController extends Controller
{
    /**
     * Controller Berita
     */
    private function applyGlobalFilters($query, Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $bulan = $request->get('bulan');
        $tone = $request->get('tone');
        $topik = $request->get('topik', $request->get('kategori_tabel'));
        $sifat = $request->get('sifat_berita');

        // Filter Tahun
        if ($tahun && $tahun !== 'all') {
            $query->whereYear('tanggal', $tahun);
        }

        // Filter Bulan
        if ($bulan && $bulan !== 'all') {
            $query->whereMonth('tanggal', $bulan);
        }

        // Filter Tone
        if ($tone && $tone !== 'all') {
            $query->where('tone', $tone);
        }

        // Filter Topik / Kategori
        if ($topik && $topik !== 'all') {
            $query->where('topik', $topik);
        }

        // Filter Sifat Berita
        if ($sifat && $sifat !== 'all') {
            $query->where('sifat_berita', $sifat);
        }

        return $query;
    }

    /**
     * Menampilkan Halaman Utama Dashboard Berita
     */
  public function index(Request $request)
{
    // 1. Tangkap semua parameter filter & sorting dari request
    $tahunTerpilih = $request->get('tahun', date('Y'));
    $bulanBerita   = $request->get('bulan', 'all');
    $toneTerpilih  = $request->get('tone', 'all');
    $topikTerpilih = $request->get('topik', $request->get('kategori_tabel', 'all'));
    $sifatTerpilih = $request->get('sifat_berita', 'all');
    
    $statusTerpilih = $request->get('status_publikasi', 'all');
    $sortBy         = $request->get('sort_by', 'tanggal');
    $sortOrder      = $request->get('sort_order', 'desc');
    $search         = $request->get('search');

    // Alias tambahan untuk kompatibilitas Blade
    $filterKategoriTabel = $topikTerpilih;
    $kategoriTerpilih    = $topikTerpilih;
    $toneBerita          = $toneTerpilih;
    $sifatBerita         = $sifatTerpilih;

    // 2. Mengambil daftar tahun secara dinamis dari kolom tanggal
    $daftarTahunArray = Berita::select(DB::raw('YEAR(tanggal) as tahun'))
        ->whereNotNull('tanggal')
        ->distinct()
        ->pluck('tahun')
        ->map(fn($t) => (int)$t)
        ->toArray();

    if ($tahunTerpilih && $tahunTerpilih !== 'all' && !in_array((int)$tahunTerpilih, $daftarTahunArray)) {
        $daftarTahunArray[] = (int)$tahunTerpilih;
    }

    // Urutkan dari tahun terbaru ke terkecil
    rsort($daftarTahunArray);

    // Jika DB kosong & tidak ada parameter, default tahun saat ini
    if (empty($daftarTahunArray)) {
        $daftarTahunArray = [(int)date('Y')];
    }

    $daftarTahun = collect($daftarTahunArray);

    // 3. MULAI QUERY DASAR TABEL BERITA
    $queryTabel = Berita::query();
    $this->applyGlobalFilters($queryTabel, $request);

    if ($search) {
        $searchLower = mb_strtolower(trim($search));

        $listBulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $matchedBulan = [];
        foreach ($listBulan as $num => $nama) {
            if (str_contains(mb_strtolower($nama), $searchLower)) {
                $matchedBulan[] = $num;
            }
        }

        if (is_numeric($searchLower) && (int) $searchLower >= 1 && (int) $searchLower <= 12) {
            $matchedBulan[] = (int) $searchLower;
            $matchedBulan = array_unique($matchedBulan);
        }

        $queryTabel->where(function ($q) use ($search, $matchedBulan) {
            $q->where('judul', 'like', "%{$search}%")
              ->orWhere('nama_media', 'like', "%{$search}%")
              ->orWhere('reporter', 'like', "%{$search}%")
              ->orWhere('spokeperson', 'like', "%{$search}%")
              ->orWhere('spokeperson_role', 'like', "%{$search}%")
              ->orWhere('topik', 'like', "%{$search}%")
              ->orWhere('tone', 'like', "%{$search}%")
              ->orWhere('sifat_berita', 'like', "%{$search}%");

            if (!empty($matchedBulan)) {
                $placeholders = implode(',', array_fill(0, count($matchedBulan), '?'));
                $q->orWhereRaw("MONTH(tanggal) in ({$placeholders})", $matchedBulan);
            }
        });
    }

    // Hitung Statistik Ringkasan (Total, Sifat Berita, & Topik)
    $totalBerita     = (clone $queryTabel)->count();
    $jumlahInternal  = (clone $queryTabel)->where('sifat_berita', 'Internal')->count();
    $jumlahEksternal = (clone $queryTabel)->where('sifat_berita', 'Eksternal')->count();

    // Perhitungan berdasarkan kolom topik
    $jumlahOperasi   = (clone $queryTabel)->where('topik', 'Operasi')->count();
    $jumlahCsr       = (clone $queryTabel)->where('topik', 'CSR')->count();
    $jumlahInovasi   = (clone $queryTabel)->where('topik', 'Inovasi')->count();
    $jumlahApresiasi = (clone $queryTabel)->where('topik', 'Apresiasi')->count();

    // 4. EKSEKUSI DATA TABEL UTAMA
    $daftarBerita = $queryTabel->orderBy($sortBy, $sortOrder)->get();

    if ($request->ajax() || $request->wantsJson()) {
        $html = view('pages.berita.partials.table', compact(
            'daftarBerita',
            'tahunTerpilih',
            'bulanBerita',
            'toneTerpilih',
            'topikTerpilih',
            'sifatTerpilih',
            'statusTerpilih',
            'sortBy',
            'sortOrder'
        ))->render();

        return response()->json(['html' => $html]);
    }

    // 5. KIRIM DATA KE VIEW
    return view('pages.berita.index', compact(
        'tahunTerpilih', 
        'daftarTahun', 
        'daftarBerita', 
        'bulanBerita',
        'toneTerpilih',
        'topikTerpilih',
        'sifatTerpilih',
        'toneBerita',
        'sifatBerita',
        'filterKategoriTabel',
        'kategoriTerpilih',
        'statusTerpilih',
        'sortBy',
        'sortOrder',
        'search',
        'totalBerita',      
        'jumlahInternal', 
        'jumlahEksternal',
        'jumlahOperasi',
        'jumlahCsr',
        'jumlahInovasi',
        'jumlahApresiasi'
    ));
}
    /**
     * Tambah Tahun Baru Otomatis dengan Data Default/Dummy
     */
    public function tambahTahun(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|digits:4|min:2000|max:2099',
        ]);

        $tahunBaru = $request->tahun;

        // Cek apakah tahun tersebut sudah ada datanya di database
        $exists = Berita::whereYear('tanggal', $tahunBaru)->exists();

        if ($exists) {
            return redirect()->back()->with('error', "Tahun {$tahunBaru} sudah ada di database!");
        }

        try {
            // Buat data default/dummy di awal tahun (1 Januari)
            Berita::create([
                'tanggal'          => "{$tahunBaru}-01-01",
                'judul'            => "Inisialisasi Data Tahun {$tahunBaru}",
                'nama_media'       => "Internal",
                'reporter'         => "System Admin",
                'spokeperson'      => "Humas",
                'spokeperson_role' => "Admin",
                'topik'            => "Operasi",
                'sifat_berita'     => "Internal",
                'tone'             => "Netral",
                'link_berita'      => null
            ]);

            \App\Models\ActivityLog::create([
                'modul'     => 'BERITA',
                'aksi'      => 'Tambah Tahun',
                'deskripsi' => "Membuat inisialisasi data tahun baru: {$tahunBaru}",
                'created_at'=> now()
            ]);

            return redirect()->route('pages.berita.index', ['tahun' => $tahunBaru])
                ->with('success', "Tahun {$tahunBaru} berhasil ditambahkan dan diset sebagai aktif!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan tahun baru: ' . $e->getMessage());
        }
    }

    /**
     * Tambah Data Berita Baru (Auto-adjust Tahun sesuai Filter Aktif jika Perlu)
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'          => 'required|date',
            'link_berita'      => 'nullable|url|max:255',
            'tone'             => 'required|in:Positif,Netral,Negatif',
            'judul'            => 'required|string|max:255',
            'nama_media'       => 'required|string|max:50',
            'reporter'         => 'nullable|string|max:50',
            'spokeperson'      => 'nullable|string|max:50',
            'spokeperson_role' => 'nullable|string|max:50',
            'topik'            => 'required|in:Operasi,CSR,Inovasi,Apresiasi',
            'sifat_berita'     => 'required|in:Internal,Eksternal',
        ]);

        try {
        DB::beginTransaction();

        // Simpan data berita 
        $berita = Berita::create($request->all());

        // 2. Catat Activity Log
        $user = auth()->user();
        ActivityLog::create([
            'id_user'    => $user->id_user ?? null,
            'aktivitas'  => [
                'modul'     => 'BERITA', // Sesuaikan nama modul
                'nama_user' => $user->username ?? 'System',
                'aksi'      => 'CREATE',
                'deskripsi' => "Menambahkan berita baru: " . $berita->judul,
                'data_baru' => $berita->toArray(),
            ],
            'created_at' => now(),
        ]);

        DB::commit();
return redirect()->route('pages.berita.index', [
    'tahun'        => $request->get('tahun_filter', 'all'),
    'bulan'        => $request->get('bulan_filter', 'all'),
    'tone'         => $request->get('tone_filter', 'all'),
    'topik'        => $request->get('topik_filter', 'all'),
    'sifat_berita' => $request->get('sifat_filter', 'all'),
])->with('success', 'Berita berhasil ditambahkan.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Gagal menyimpan berita: ' . $e->getMessage());
    }
}

    /**
     * Data Diagram Tone
     */
    public function apiTone(Request $request)
    {
        $query = Berita::query();
        $this->applyGlobalFilters($query, $request);

        $toneRaw = $query->select('tone', DB::raw('count(*) as total'))
            ->groupBy('tone')
            ->pluck('total', 'tone');

        return response()->json([
            'Positif' => $toneRaw['Positif'] ?? 0,
            'Netral'  => $toneRaw['Netral'] ?? 0,
            'Negatif' => $toneRaw['Negatif'] ?? 0,
        ]);
    }

   /**
     * Data Diagram Jumlah Pemberitaan 
     */
   /** 6. Program Perbandingan Pemberitaan (Kiri vs Kanan) */
public function apiPemberitaan(Request $request)
{
    // Filter bulan mandiri (tidak terikat filter bulan global)
    $bulanKiri  = $request->get('bulan_kiri', 4);
    $bulanKanan = $request->get('bulan_kanan', 5);
    $tahun      = $request->get('tahun', date('Y'));

    // Daftar nama bulan untuk label frontend
    $listBulan = [
        1 => 'JANUARI', 2 => 'FEBRUARI', 3 => 'MARET', 4 => 'APRIL',
        5 => 'MEI', 6 => 'JUNI', 7 => 'JULI', 8 => 'AGUSTUS',
        9 => 'SEPTEMBER', 10 => 'OKTOBER', 11 => 'NOVEMBER', 12 => 'DESEMBER'
    ];

    // Helper untuk mengambil data per bulan berdasarkan filter global (kecuali bulan)
    $getDataBulan = function($bulanTarget) use ($request) {
        $query = Berita::query();
        
        $tahunParam = $request->get('tahun', date('Y'));
        $tone = $request->get('tone');
        $topik = $request->get('topik', $request->get('kategori_tabel'));
        $sifat = $request->get('sifat_berita');

        if ($tahunParam && $tahunParam !== 'all') {
            $query->whereYear('tanggal', $tahunParam);
        }
        if ($tone && $tone !== 'all') {
            $query->where('tone', $tone);
        }
        if ($topik && $topik !== 'all') {
            $query->where('topik', $topik);
        }
        if ($sifat && $sifat !== 'all') {
            $query->where('sifat_berita', $sifat);
        }

        // Filter spesifik bulan mandiri
        if ($bulanTarget && $bulanTarget !== 'all') {
            $query->whereMonth('tanggal', $bulanTarget);
        }

        $data = $query->select('sifat_berita', DB::raw('count(*) as total'))
            ->groupBy('sifat_berita')
            ->pluck('total', 'sifat_berita')
            ->toArray();

        $internal  = $data['Internal'] ?? 0;
        $eksternal = $data['Eksternal'] ?? 0;

        return [
            'internal'  => $internal,
            'eksternal' => $eksternal,
            'total'     => $internal + $eksternal
        ];
    };

    // Ambil data untuk sisi Kiri dan Kanan
    $dataKiri  = $getDataBulan($bulanKiri);   
    $dataKanan = $getDataBulan($bulanKanan); 

    $totalKiri  = $dataKiri['total'];   
    $totalKanan = $dataKanan['total'];   

    $persen = 0;
    $status = 'equal'; 

    if ($totalKiri > 0) {
        $selisih = $totalKiri - $totalKanan; 
        $persen  = round(abs(($selisih / $totalKiri) * 100)); 
        
        if ($selisih > 0) {
          
            $status = 'down';
        } elseif ($selisih < 0) {
            
            $status = 'up';
        } else {
            $status = 'equal';
        }
    } else {
      
        if ($totalKanan > 0) {
            $persen = 100;
            $status = 'up';
        } else {
            // 0 ke 0
            $persen = 0;
            $status = 'equal';
        }
    }

    return response()->json([
        'bulan_kiri' => [
            'nama'      => $listBulan[(int)$bulanKiri] ?? 'BULAN',
            'internal'  => $dataKiri['internal'],
            'eksternal' => $dataKiri['eksternal'],
            'total'     => $totalKiri,
        ],
        'bulan_kanan' => [
            'nama'      => $listBulan[(int)$bulanKanan] ?? 'BULAN',
            'internal'  => $dataKanan['internal'],
            'eksternal' => $dataKanan['eksternal'],
            'total'     => $totalKanan,
        ],
        'perbandingan' => [
            'status' => $status, 
            'persen' => $persen,
        ]
    ]);

    }
    /**
     * Data Diagram Spokesperson
     */
   public function apiSpokesperson(Request $request)
{
    $limit = $request->get('show_all') ? 1000 : 5;

    $query = Berita::query();
    $this->applyGlobalFilters($query, $request);

    $data = $query->select('spokeperson', 'spokeperson_role', DB::raw('count(*) as total'))
        ->whereNotNull('spokeperson')
        ->where('spokeperson', '!=', '-')
        ->where('spokeperson', '!=', '')
        ->whereRaw("TRIM(spokeperson) != ''")
        ->whereRaw("spokeperson REGEXP '[a-zA-Z0-9]'") 
        ->groupBy('spokeperson', 'spokeperson_role')
        ->orderBy('total', 'desc')
        ->limit($limit)
        ->get();

    return response()->json($data);
}
    /**
     * Data Top 5 Media & Jurnalis
     */
    public function apiTop5(Request $request)
{
    $queryBase = Berita::query();
    $this->applyGlobalFilters($queryBase, $request);

    $topMedia = (clone $queryBase)->select('nama_media', DB::raw('count(*) as total'))
        ->whereNotNull('nama_media')
        ->where('nama_media', '!=', '-')
        ->where('nama_media', '!=', '')
        ->whereRaw("TRIM(nama_media) != ''")
        ->whereRaw("nama_media REGEXP '[a-zA-Z0-9]'") 
        ->groupBy('nama_media')
        ->orderBy('total', 'desc')
        ->limit(5)
        ->get();

    $topJurnalis = (clone $queryBase)->select('reporter', DB::raw('count(*) as total'))
        ->whereNotNull('reporter')
        ->where('reporter', '!=', '-')
        ->where('reporter', '!=', '')
        ->whereRaw("TRIM(reporter) != ''")
        ->whereRaw("reporter REGEXP '[a-zA-Z0-9]'") 
        ->groupBy('reporter')
        ->orderBy('total', 'desc')
        ->limit(5)
        ->get();

    return response()->json([
        'media'    => $topMedia,
        'jurnalis' => $topJurnalis
    ]);
}

    /**
     * Render Modal Edit
     */
    public function editModal(int $id)
    {
        $berita = Berita::findOrFail($id);
        return view('pages.berita.partials.edit', compact('berita'));
    }

    /**
     * Update Data Berita dari Modal
     */
   public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'tanggal'          => 'required|date',
            'link_berita'      => 'nullable|url|max:255',
            'tone'             => 'required|in:Positif,Netral,Negatif',
            'judul'            => 'required|string|max:255',
            'nama_media'       => 'required|string|max:255',
            'reporter'         => 'required|string|max:255',
            'spokeperson'      => 'required|string|max:255',
            'spokeperson_role' => 'required|string|max:255',
            'topik'            => 'required|in:Operasi,CSR,Inovasi,Apresiasi',
            'sifat_berita'     => 'required|in:Internal,Eksternal',
        ]);

        try {
            DB::beginTransaction();

            $berita = Berita::findOrFail($id);
            
            // Ambil data lama sebelum di-update
            $dataLama = $berita->toArray();

            // Lakukan update data
            $berita->update($validated);

            // Catat activity log sesuai struktur array model
            $user = auth()->user();
            \App\Models\ActivityLog::create([
                'id_user'    => $user->id_user ?? null,
                'aktivitas'  => [
                    'modul'     => 'BERITA',
                    'nama_user' => $user->username ?? 'System',
                    'aksi'      => 'UPDATE',
                    'deskripsi' => "Mengubah data berita yang berjudul : \"{$berita->judul}\"",
                    'data_lama' => $dataLama,
                    'data_baru' => $berita->fresh()->toArray(),
                ],
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Data berita berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memperbarui berita: ' . $e->getMessage()
            ], 422);
        }
    }

    /**
     * Update Data Berita via Inline Editing
     */
    public function inlineUpdate(Request $request, int $id)
    {
        $request->validate([
            'field' => 'required|string',
            'value' => 'nullable|string',
        ]);

        $berita = Berita::findOrFail($id);
        
        $allowedFields = ['tanggal', 'judul', 'nama_media', 'reporter', 'spokeperson', 'spokeperson_role', 'tone', 'topik', 'sifat_berita', 'link_berita'];
        
        if (!in_array($request->field, $allowedFields)) {
            return response()->json(['message' => 'Kolom tidak diizinkan!'], 422);
        }

        $berita->{$request->field} = $request->value;
        $berita->save();

        return response()->json(['status' => 'success', 'message' => 'Data berhasil diperbarui!']);
    }

    /**
     * Hapus Data Berita
     */
    /**
     * Hapus Data Berita
     */
    public function destroy(int $id)
    {
        try {
            DB::beginTransaction();

            $berita = Berita::findOrFail($id);
            $dataLama = $berita->toArray();
            $judulBackup = $berita->judul;

            $berita->delete();

            $user = auth()->user();
            \App\Models\ActivityLog::create([
                'id_user'    => $user->id_user ?? null,
                'aktivitas'  => [
                    'modul'     => 'BERITA', // Diseragamkan jadi 'BERITA'
                    'nama_user' => $user->username ?? 'System',
                    'aksi'      => 'DELETE',
                    'deskripsi' => "Menghapus berita yang berjudul : \"{$judulBackup}\"",
                    'data_lama' => $dataLama,
                ],
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Data berita berhasil dihapus !');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus berita: ' . $e->getMessage());
        }
    }
    /**
     * IMPORT DATA DARI PASTE SPREADSHEET BERITA
     */
    public function storePaste(Request $request)
    {
        $request->validate([
            'parsed_rows' => 'required|json',
        ]);

        $rows = json_decode($request->input('parsed_rows'), true);

        if (empty($rows) || !is_array($rows)) {
            return redirect()->back()->with('error', 'Tidak ada data valid yang dapat disimpan.');
        }

        $successCount = 0;

        foreach ($rows as $row) {
            if (!empty($row['tanggal']) && !empty($row['judul'])) {
                
                $sifatBerita = 'Eksternal';
                $rawSifat = strtoupper(trim($row['sifat_berita'] ?? 'EXT'));
                if (str_contains($rawSifat, 'INT')) {
                    $sifatBerita = 'Internal';
                }

                \App\Models\Berita::create([
                    'tanggal'           => $row['tanggal'],
                    'link_berita'       => $row['link_berita'] ?? null,
                    'tone'              => $row['tone'] ?? 'Positif',
                    'judul'             => $row['judul'],
                    'nama_media'        => $row['nama_media'] ?? 'Unknown',
                    'reporter'          => $row['reporter'] ?? '-',
                    'spokeperson'       => $row['spokeperson'] ?? '-',
                    'spokeperson_role'  => $row['spokeperson_role'] ?? '-',
                    'topik'             => $row['topik'] ?? 'Operasi',
                    'sifat_berita'      => $sifatBerita,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
                
                $successCount++;
            }
        }

        return redirect()->back()->with('success', "Berhasil mengimpor {$successCount} data berita baru.");
    }


    /**
     * Export Excel
     */
    public function exportExcel(Request $request) 
{
    $export = new BeritaExport($request);
    return Excel::download($export, $export->getFilename());
}
}