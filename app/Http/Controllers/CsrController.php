<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Csr;
use App\Models\Pilar;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Exports\CsrExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class CsrController extends Controller
{
    private array $listBulan = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];

    private array $listPilar = ['Sosial', 'Ekonomi', 'Lingkungan'];

    private array $listTpb = [
        '1. Tanpa Kemiskinan', '2. Tanpa Kelaparan', '3. Kehidupan Sehat dan Sejahtera',
        '4. Pendidikan Berkualitas', '5. Kesetaraan Gender', '6. Air Bersih dan Sanitasi Layak',
        '7. Energi Bersih dan Terjangkau', '8. Pekerjaan Layak dan Pertumbuhan Ekonomi',
        '9. Industri, Inovasi dan Infrastruktur', '10. Berkurangnya Kesenjangan',
        '11. Kota dan Permukiman yang Berkelanjutan', '12. Konsumsi dan Produksi yang Bertanggung Jawab',
        '13. Penanganan Perubahan Iklim', '14. Ekosistem Kelautan', '15. Ekosistem Daratan',
        '16. Perdamaian, Keadilan dan Kelembagaan yang Tangguh', '17. Kemitraan untuk Mencapai Tujuan'
    ];

    private array $listAstaCita = [
        'Asta Cita 1', 'Asta Cita 2', 'Asta Cita 3', 'Asta Cita 4',
        'Asta Cita 5', 'Asta Cita 6', 'Asta Cita 7', 'Asta Cita 8'
    ];

    private array $listCabang = ['PNK'];

    
    private function terapkanFilterGlobal(mixed $query, Request $request, array $skip = [])
    {
        if (!in_array('bulan', $skip) && $request->filled('bulan')) {
            $query->where('csr.bulan_realisasi', $request->bulan);
        }
        if (!in_array('g_kabupaten', $skip) && $request->filled('g_kabupaten')) {
            $val = $request->g_kabupaten;
            $query->where(function($q) use ($val) {
                $q->where('csr.kabupaten', $val)
                  ->orWhere('csr.kabupaten', 'LIKE', $val . ',%')
                  ->orWhere('csr.kabupaten', 'LIKE', '%, ' . $val . ',%')
                  ->orWhere('csr.kabupaten', 'LIKE', '%, ' . $val);
            });
        }

        if (!in_array('g_desa', $skip) && $request->filled('g_desa')) {
            $val = $request->g_desa;
            $query->where(function($q) use ($val) {
                $q->where('csr.desa', $val)
                  ->orWhere('csr.desa', 'LIKE', $val . ',%')
                  ->orWhere('csr.desa', 'LIKE', '%, ' . $val . ',%')
                  ->orWhere('csr.desa', 'LIKE', '%, ' . $val);
            });
        }
        if (!in_array('g_program', $skip) && $request->filled('g_program') && $request->g_program !== 'all') {
            $query->where('csr.nama_program', $request->g_program);
        }

        if (!in_array('g_pilar', $skip) && $request->filled('g_pilar')) {
            if ($query instanceof \Illuminate\Database\Eloquent\Builder) {
                $query->whereHas('pilar', function($q) use ($request) {
                    $q->where('nama_pilar', $request->g_pilar);
                });
            } else {
                // Untuk Tabel CSR (DB::table karena pakai leftJoin)
                $query->where('pilar.nama_pilar', $request->g_pilar);
            }
        }

        // FILTER GLOBAL STATUS REALISASI (done/undone)
        if (!in_array('g_status', $skip) && $request->filled('g_status')) {
            $statusFilter = strtolower(trim($request->g_status));
            if (in_array($statusFilter, ['done', 'undone'])) {
                $query->where('csr.status', $statusFilter);
            }
        }

        return $query;
    }

    /**
     * Helper validasi multi-link (dipanggil di store() & update())
     */
    private function multiLinkRule()
    {
        return function ($attribute, $value, $fail) {
            if (empty($value)) return;
            $links = array_filter(array_map('trim', explode("\n", $value)));
            foreach ($links as $link) {
                if (!filter_var($link, FILTER_VALIDATE_URL)) {
                    $fail("Salah satu link pada {$attribute} tidak valid: {$link}");
                }
            }
        };
    }

    /**
     * Halaman Utama Dashboard & Manajemen CSR
     */
    public function index(Request $request)
    {
        $tahunTerpilih = $request->get('tahun', date('Y'));

        // 1. Daftar tahun murni dari data csr + pastikan $tahunTerpilih ikut masuk
        $daftarTahun = Csr::distinct()->orderBy('tahun_realisasi', 'desc')->pluck('tahun_realisasi');
        if (!$daftarTahun->contains((int) $tahunTerpilih)) {
            $daftarTahun->push((int) $tahunTerpilih);
        }
        $daftarTahun = $daftarTahun->sort()->reverse()->values();

        // 2. Daftar nama_program unik (dinamis) berdasarkan tahun terpilih
        $daftarProgramUnik = Csr::where('tahun_realisasi', $tahunTerpilih)
            ->whereNotNull('nama_program')
            ->where('nama_program', '!=', '-')
            ->distinct()->orderBy('nama_program')->pluck('nama_program');

        // 3. Daftar Pilar (Ambil dari tabel pilar berdasarkan tahun terpilih atau list default)
        $masterPilarAll = Pilar::where('tahun_pilar', $tahunTerpilih)
            ->whereNotNull('nama_pilar')
            ->orderBy('nama_pilar')
            ->pluck('nama_pilar');

        if ($masterPilarAll->isEmpty()) {
            $masterPilarAll = collect($this->listPilar);
        }

        // 4. Counter total kegiatan (ikut filter global dengan join pilar) — ikut filter g_status
        $totalKegiatanQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);
        $this->terapkanFilterGlobal($totalKegiatanQuery, $request);
        $totalKegiatan = $totalKegiatanQuery->count();

        // ========================================================
        // ANGGARAN PER PILAR
        // ========================================================
        $manajemenAnggaranPilar = collect();
        foreach ($this->listPilar as $p) {
            // Ambil data plafon anggaran dari tabel pilar
            $dataPilar = Pilar::where('tahun_pilar', $tahunTerpilih)
                ->where('nama_pilar', $p)
                ->first();

            $jumlahAnggaran = $dataPilar ? (float) $dataPilar->anggaran_pilar : 0;

            // Hitung total realisasi biaya dari program CSR yang terikat ke pilar ini
            $qRealisasi = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
                ->where('csr.tahun_realisasi', $tahunTerpilih)
                ->where('pilar.nama_pilar', $p);

            $this->terapkanFilterGlobal($qRealisasi, $request, ['g_pilar']);
            $totalRealisasi = (float) $qRealisasi->sum('csr.biaya_realisasi');

            $manajemenAnggaranPilar->push((object) [
                'nama_pilar'       => $p,
                'jumlah_anggaran'  => $jumlahAnggaran,
                'total_realisasi'  => $totalRealisasi
            ]);
        }

        // ========================================================
        // SUM BIAYA & REALISASI PER PROGRAM (Contoh: Injourney Berbagi, dll)
        // ========================================================
        $qRingkasanProgram = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih)
            ->whereNotNull('csr.nama_program')
            ->where('csr.nama_program', '!=', '-');

        // Ikutkan filter global (kecuali g_program supaya bisa lihat perbandingan antar program atau spesifik)
        $this->terapkanFilterGlobal($qRingkasanProgram, $request, ['g_program']);

        $ringkasanPerProgram = $qRingkasanProgram
            ->select(
                'csr.nama_program',
                DB::raw('SUM(csr.biaya_program) as total_biaya_program'),
                DB::raw('SUM(csr.biaya_realisasi) as total_biaya_realisasi'),
                DB::raw('COUNT(csr.id_csr) as jumlah_kegiatan')
            )
            ->groupBy('csr.nama_program')
            ->get();

        // ========================================================
        // Dropdown filter global kabupaten & desa berdasarkan tahun terpilih
        // ========================================================
        $daftarKabupatenFilter = Csr::where('tahun_realisasi', $tahunTerpilih)
            ->whereNotNull('kabupaten')->where('kabupaten', '!=', '-')
            ->pluck('kabupaten')
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map(function ($item) {
                return trim($item);
            })
            ->filter(function ($item) {
                return !empty($item);
            })
            ->unique()
            ->sort()
            ->values();

        $daftarDesaFilter = Csr::where('tahun_realisasi', $tahunTerpilih)
            ->whereNotNull('desa')->where('desa', '!=', '-')
            ->pluck('desa')
            ->flatMap(function ($item) {
                return explode(',', $item);
            })
            ->map(function ($item) {
                return trim($item);
            })
            ->filter(function ($item) {
                return !empty($item);
            })
            ->unique()
            ->sort()
            ->values();

        // ========================================================
        // Base query listing CSR — menggunakan JOIN ke tabel pilar
        // ========================================================
        $queryCsr = DB::table('csr')
            ->leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->select('csr.*', 'pilar.nama_pilar', 'pilar.tahun_pilar', 'pilar.anggaran_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);

        if ($request->filled('g_program')) {
            $queryCsr->where('csr.nama_program', $request->g_program);
        }
        // Filter status per-kolom 
        if ($request->filled('status')) {
            $queryCsr->where('csr.status', strtolower($request->status));
        }
        if ($request->filled('tpb')) {
            $queryCsr->where('csr.tpb', $request->tpb);
        }
        if ($request->filled('asta_cita')) {
            $queryCsr->where('csr.asta_cita', $request->asta_cita);
        }
        if ($request->filled('search')) {
            $keyword = $request->search;
            $queryCsr->where(function ($q) use ($keyword) {
                $q->where('csr.nama_program', 'like', "%{$keyword}%")
                    ->orWhere('csr.bentuk_bantuan', 'like', "%{$keyword}%")
                    ->orWhere('csr.kabupaten', 'like', "%{$keyword}%")
                    ->orWhere('csr.desa', 'like', "%{$keyword}%")
                    ->orWhere('csr.bulan_realisasi', 'like', "%{$keyword}%")
                    ->orWhere('csr.tahun_realisasi', 'like', "%{$keyword}%")
                    ->orWhere('csr.tpb', 'like', "%{$keyword}%")
                    ->orWhere('csr.asta_cita', 'like', "%{$keyword}%")
                    ->orWhere('csr.cabang', 'like', "%{$keyword}%")
                    ->orWhere('pilar.nama_pilar', 'like', "%{$keyword}%");
            });
        }

        // Filter global
        $this->terapkanFilterGlobal($queryCsr, $request);

        // Sorting
        $sortBy    = $request->get('sort_by');
        $sortOrder = strtolower($request->get('sort_order')) === 'desc' ? 'DESC' : 'ASC';

        $urutanBulan    = "'" . implode("','", $this->listBulan) . "'";
        $urutanTpb      = "'" . implode("','", $this->listTpb) . "'";
        $urutanAstaCita = "'" . implode("','", $this->listAstaCita) . "'";

        if ($sortBy === 'bulan') {
            $queryCsr->orderByRaw("FIELD(csr.bulan_realisasi, {$urutanBulan}) {$sortOrder}");
        } elseif ($sortBy === 'tpb') {
            $queryCsr->orderByRaw("FIELD(csr.tpb, {$urutanTpb}) {$sortOrder}");
        } elseif ($sortBy === 'asta_cita') {
            $queryCsr->orderByRaw("FIELD(csr.asta_cita, {$urutanAstaCita}) {$sortOrder}");
        } else {
            $queryCsr->orderBy('csr.id_csr', 'desc');
        }

        $daftarCsr = $queryCsr->get();

       // ========================================================
        // DONUT REALISASI ANGGARAN (initial load) 
        // ========================================================
        $paguQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);
        $this->terapkanFilterGlobal($paguQuery, $request);
        $totalPaguAnggaran = $paguQuery->sum('csr.biaya_program');

        $totalRealisasiBiayaQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);
        $this->terapkanFilterGlobal($totalRealisasiBiayaQuery, $request);
        $totalRealisasiBiaya = $totalRealisasiBiayaQuery->sum('csr.biaya_realisasi');

        $sisaAnggaranHitung = $totalPaguAnggaran - $totalRealisasiBiaya;
        $efektifitasHitung  = $totalPaguAnggaran > 0
            ? round(($totalRealisasiBiaya / $totalPaguAnggaran) * 100)
            : 0;

        $realisasiAnggaran = [
            'total_anggaran'      => $totalPaguAnggaran,
            'total_realisasi'     => $totalRealisasiBiaya,
            'sisa_anggaran'       => max(0, $sisaAnggaranHitung),
            'tingkat_efektifitas' => $efektifitasHitung,
        ];

        $realisasiPerPilarQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih)
            ->where('csr.status', 'done');

        $this->terapkanFilterGlobal($realisasiPerPilarQuery, $request, ['g_pilar', 'g_status']);

        $realisasiPerPilarRaw = $realisasiPerPilarQuery
            ->select('pilar.nama_pilar', DB::raw('COUNT(csr.id_csr) as jumlah_kegiatan_done'))
            ->groupBy('pilar.nama_pilar')
            ->pluck('jumlah_kegiatan_done', 'nama_pilar')
            ->toArray();

        $realisasiPerPilar = collect($this->listPilar)->map(function ($namaPilar) use ($realisasiPerPilarRaw) {
            return (object) [
                'nama_pilar' => $namaPilar,
                'jumlah_kegiatan_done' => $realisasiPerPilarRaw[$namaPilar] ?? 0,
            ];
        });

        // ========================================================
        // JUMLAH KEGIATAN per bulan 
        // ========================================================
        $kegiatanBulananQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih)
            ->where('csr.status', 'done'); // 

        $this->terapkanFilterGlobal($kegiatanBulananQuery, $request, ['bulan', 'g_status']);

        $kegiatanBulananRaw = $kegiatanBulananQuery
            ->select('csr.bulan_realisasi', DB::raw('count(csr.id_csr) as total'))
            ->groupBy('csr.bulan_realisasi')
            ->pluck('total', 'bulan_realisasi')
            ->toArray();

        $kegiatanBulanan = [];
        foreach ($this->listBulan as $bln) {
            $kegiatanBulanan[$bln] = $kegiatanBulananRaw[$bln] ?? 0;
        }

        // ========================================================
        // RINGKASAN TRIWULAN (Initial Load)
        // ========================================================
        $triwulanQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);
        $this->terapkanFilterGlobal($triwulanQuery, $request, ['bulan']);

        $triwulanRaw = $triwulanQuery->select('pilar.nama_pilar', 'csr.bulan_realisasi', 'csr.biaya_realisasi')->get();

        $anggaranTriwulan = [];
        foreach ($this->listPilar as $namaPilar) {
            $tw = ['TW1' => 0, 'TW2' => 0, 'TW3' => 0, 'TW4' => 0];
            foreach ($triwulanRaw->where('nama_pilar', $namaPilar) as $row) {
                if (in_array($row->bulan_realisasi, ['Januari', 'Februari', 'Maret'])) $tw['TW1'] += $row->biaya_realisasi;
                elseif (in_array($row->bulan_realisasi, ['April', 'Mei', 'Juni'])) $tw['TW2'] += $row->biaya_realisasi;
                elseif (in_array($row->bulan_realisasi, ['Juli', 'Agustus', 'September'])) $tw['TW3'] += $row->biaya_realisasi;
                elseif (in_array($row->bulan_realisasi, ['Oktober', 'November', 'Desember'])) $tw['TW4'] += $row->biaya_realisasi;
            }
            $anggaranTriwulan[$namaPilar] = $tw;
        }

        // ========================================================
        // PROGRAM PER WILAYAH — 
        // ========================================================
        $dataWilayahQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);
        $this->terapkanFilterGlobal($dataWilayahQuery, $request);

        $dataWilayahChart = $dataWilayahQuery
            ->select('csr.kabupaten as nama_lokasi', DB::raw('COUNT(csr.id_csr) as total_program'))
            ->groupBy('csr.kabupaten')
            ->get();

        // Chart Asta Cita — kena SEMUA filter global (termasuk g_status)
        $dataAstaCitaQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);
        $this->terapkanFilterGlobal($dataAstaCitaQuery, $request);

        $dataAstaCitaChart = $dataAstaCitaQuery
            ->select('csr.asta_cita', DB::raw('COUNT(csr.id_csr) as total_jumlah'))
            ->groupBy('csr.asta_cita')
            ->get();

       // ========================================================
        // CARD RINGKASAN TAMBAHAN 
        // ========================================================
        
        // Base query yang sudah menerapkan semua filter global 
        $cardBaseQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);
        $this->terapkanFilterGlobal($cardBaseQuery, $request);

        // 1. Total Kegiatan 
        $totalKegiatanQuery = clone $cardBaseQuery;
        $totalKegiatan = $totalKegiatanQuery->count();

        // 2. Realisasi Selesai
        $totalRealisasiDoneQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih)
            ->where('csr.status', 'done');
        $this->terapkanFilterGlobal($totalRealisasiDoneQuery, $request, ['g_status']);
        $totalRealisasiDone = $totalRealisasiDoneQuery->count();

        // 3. Total Biaya Realisasi
        $totalSumBiayaRealisasiQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih)
            ->where('csr.status', 'done');
        $this->terapkanFilterGlobal($totalSumBiayaRealisasiQuery, $request, ['g_status']);
        $totalSumBiayaRealisasi = $totalSumBiayaRealisasiQuery->sum('csr.biaya_realisasi');

        // 4. Total Biaya Program 
        $totalSumBiayaProgramQuery = clone $cardBaseQuery;
        $totalSumBiayaProgram = $totalSumBiayaProgramQuery->sum('csr.biaya_program');

        // 5. Total Anggaran
        $totalAnggaranTahunIni = $totalPaguAnggaran ?? 0;

        // 6. Kabupaten 
        $totalKabupatenQuery = clone $cardBaseQuery;
        $totalKabupaten = $totalKabupatenQuery
            ->whereNotNull('csr.kabupaten')
            ->where('csr.kabupaten', '!=', '-')
            ->whereRaw("TRIM(csr.kabupaten) != ''")
            ->where('csr.kabupaten', 'REGEXP', '[a-zA-Z0-9]')
            ->distinct()
            ->count('csr.kabupaten');

        // 7. Desa 
        $totalDesaQuery = clone $cardBaseQuery;
        $totalDesa = $totalDesaQuery
            ->whereNotNull('csr.desa')
            ->where('csr.desa', '!=', '-')
            ->whereRaw("TRIM(csr.desa) != ''")
            ->where('csr.desa', 'REGEXP', '[a-zA-Z0-9]')
            ->distinct()
            ->count('csr.desa');

        return view('pages.csr.index', compact(
            'tahunTerpilih', 'daftarTahun', 'daftarProgramUnik', 'masterPilarAll',
            'totalKegiatan', 'manajemenAnggaranPilar', 'daftarCsr',
            'realisasiAnggaran', 'kegiatanBulanan',
            'realisasiPerPilar', 'anggaranTriwulan', 'dataWilayahChart', 'dataAstaCitaChart',
            'totalRealisasiDone', 'totalAnggaranTahunIni', 'totalKabupaten', 'totalDesa',
            'daftarKabupatenFilter', 'daftarDesaFilter',
            'totalSumBiayaProgram', 'totalSumBiayaRealisasi', 'ringkasanPerProgram'
        ))->with('listPilar', $this->listPilar);
    }

    /**
     * FUNGSI TAMBAHAN: Menyimpan / Update Plafon Anggaran Pilar dari Halaman CSR
     */
   public function simpanAnggaranPilar(Request $request)
    {
        $request->validate([
            'tahun'    => 'required|digits:4',
            'anggaran' => 'required|array',
        ]);

        foreach ($request->anggaran as $namaPilar => $nominal) {
            // Bersihkan format pemisah ribuan jika ada
            $bersihNomor = str_replace(['.', ','], '', $nominal);

            Pilar::updateOrCreate(
                [
                    'tahun_pilar' => $request->tahun,
                    'nama_pilar'  => $namaPilar
                ],
                [
                    'anggaran_pilar' => (int) $bersihNomor
                ]
            );
        }

        // Ubah dari redirect()->back() menjadi JSON agar respons AJAX bersih
        return response()->json([
            'success' => true,
            'message' => 'Plafon anggaran pilar berhasil diperbarui!'
        ]);
    }

    /**
     * FUNGSI TAMBAH TAHUN: Menambah tahun baru sekaligus inisialisasi 3 pilar default (Sosial, Ekonomi, Lingkungan)
     */
    public function tambahTahun(Request $request)
    {
        $request->validate([
            'tahun_baru' => 'required|digits:4',
        ], [
            'tahun_baru.digits' => 'Jumlah tahun yang diinput harus berformat 4 angka!',
        ]);

        $tahun = $request->tahun_baru;

        // Inisialisasi otomatis 3 pilar wajib untuk tahun baru tersebut dengan anggaran default 0
        foreach ($this->listPilar as $pilar) {
            Pilar::firstOrCreate(
                [
                    'tahun_pilar' => $tahun,
                    'nama_pilar'  => $pilar,
                ],
                [
                    'anggaran_pilar' => 0,
                ]
            );
        }

        return redirect()->route('pages.csr.index', ['tahun' => $tahun])
            ->with('success', "Tahun {$tahun} beserta data pilar default berhasil ditambahkan. Silakan mulai input data kegiatan CSR.");
    }

   /**
     * CRUD: Form Tambah Kegiatan CSR (modal mengambang)
     */
    public function create(Request $request)
    {
        $tahunTerpilih = $request->get('tahun', date('Y'));
        $listPilar = $this->listPilar;
        $daftarProgramUnik = Csr::where('tahun_realisasi', $tahunTerpilih)
            ->whereNotNull('nama_program')->distinct()->pluck('nama_program');

        return view('pages.csr.tambah', compact('listPilar', 'tahunTerpilih', 'daftarProgramUnik'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pilar'             => 'required|in:' . implode(',', $this->listPilar),
            'bulan_realisasi'   => 'nullable|in:' . implode(',', $this->listBulan),
            'tahun_realisasi'   => 'required|digits:4',
            'tpb'               => 'nullable|string|max:255',
            'asta_cita'         => 'nullable|string|max:100',
            'cabang'            => 'nullable|string|max:50',
            'nama_program'      => 'nullable|string|max:255',
            'bentuk_bantuan'    => 'nullable|string',
            'kabupaten'         => 'nullable|string|max:100',
            'desa'              => 'nullable|string|max:100',
            'biaya_program'     => 'nullable|integer|min:0',
            'biaya_realisasi'   => 'nullable|integer|min:0',
            'realisasi_program' => 'nullable|string',
            'status'            => 'nullable|in:done,undone',
            'link_ig'           => ['nullable', 'string', $this->multiLinkRule()],
            'link_berita'       => ['nullable', 'string', $this->multiLinkRule()],
            'link_gdrive'       => 'nullable|url',
        ]);


        try {
            $user = Auth::user();
            $tahunRealisasi = $request->tahun_realisasi ?? date('Y');
            $namaPilar = $request->pilar ?? $this->listPilar[0];

            // Cari atau buat otomatis id_pilar berdasarkan nama pilar dan tahun realisasi yang dipilih
            $pilarRecord = Pilar::firstOrCreate(
                [
                    'tahun_pilar' => $tahunRealisasi,
                    'nama_pilar'  => $namaPilar,
                ],
                [
                    'anggaran_pilar' => 0,
                ]
            );

            $kabupatenClean = !empty($request->kabupaten) ? Str::title($request->kabupaten) : '-';
            $desaClean      = !empty($request->desa) ? Str::title($request->desa) : '-';

            $idInserted = DB::table('csr')->insertGetId([
                'nama_program'      => $request->nama_program ?? '-',
                'id_pilar'          => $pilarRecord->id_pilar,
                'tpb'               => $request->tpb ?? $this->listTpb[0],
                'asta_cita'         => $request->asta_cita ?? $this->listAstaCita[0],
                'cabang'            => $request->cabang ?? $this->listCabang[0],
                'biaya_program'     => $request->biaya_program ?? 0,
                'biaya_realisasi'   => $request->biaya_realisasi ?? 0,
                'realisasi_program' => $request->realisasi_program,
                'bentuk_bantuan'    => $request->bentuk_bantuan ?? '-',
                'bulan_realisasi'   => $request->bulan_realisasi ?? 'Januari',
                'tahun_realisasi'   => $tahunRealisasi,
                'kabupaten'         => $kabupatenClean,
                'desa'              => $desaClean,
                'status'            => $request->status ?? 'done',
                'link_ig'           => $request->link_ig,
                'link_berita'       => $request->link_berita,
                'link_gdrive'       => $request->link_gdrive,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);

            $newData = DB::table('csr')->where('id_csr', $idInserted)->first();

            ActivityLog::create([
                'id_user'   => $user->id_user ?? null,
                'nama_user' => $user->username ?? 'System',
                'aktivitas' => [
                    'modul'     => 'CSR',
                    'nama_user' => $user->username ?? 'System',
                    'aksi'      => 'CREATE',
                    'deskripsi' => 'Menambahkan data CSR baru',
                    'data_baru' => (array) $newData
                ],
                'created_at' => now()
            ]);

            return redirect()->back()->with('success', 'Data CSR berhasil disimpan.');
        } catch (\Exception $e) {
            // Hapus redirect, ganti dengan dd() supaya eror aslinya meledak di layar
            dd([
                'Pesan Eror' => $e->getMessage(),
                'File' => $e->getFile(),
                'Baris' => $e->getLine()
            ]);
        }
    }

    public function edit(int $id_csr)
    {
        $csr = Csr::with('pilarRelasi')->findOrFail($id_csr);
        $listPilar = $this->listPilar;
        $tahunTerpilih = $csr->tahun_realisasi;

        $daftarProgramUnik = Csr::where('tahun_realisasi', $tahunTerpilih)
            ->whereNotNull('nama_program')->distinct()->pluck('nama_program');

        return view('pages.csr.edit', compact('csr', 'listPilar', 'tahunTerpilih', 'daftarProgramUnik'));
    }

    public function update(Request $request, int $id_csr)
    {
        $validatedData = $request->validate([
            'pilar'             => 'required|in:' . implode(',', $this->listPilar),
            'bulan_realisasi'   => 'required|string',
            'tahun_realisasi'   => 'required|digits:4',
            'tpb'               => 'required|string|max:255',
            'asta_cita'         => 'required|string|max:100',
            'cabang'            => 'required|string|max:50',
            'nama_program'      => 'required|string|max:255',
            'bentuk_bantuan'    => 'required|string',
            'kabupaten'         => 'nullable|string|max:100',
            'desa'              => 'nullable|string|max:100',
            'biaya_program'     => 'required|integer|min:0',
            'biaya_realisasi'   => 'required|integer|min:0',
            'realisasi_program' => 'nullable|string',
            'status'            => 'required|in:done,undone',
            'link_ig'           => ['nullable', 'string', $this->multiLinkRule()],
            'link_berita'       => ['nullable', 'string', $this->multiLinkRule()],
            'link_gdrive'       => 'nullable|url',
        ]);

        try {
            $user = Auth::user();
            $csr = Csr::findOrFail($id_csr);
            $dataLama = $csr->toArray();

            // Cari atau buat id_pilar yang sesuai dengan tahun realisasi baru & nama pilar yang dipilih
            $pilarRecord = Pilar::firstOrCreate(
                [
                    'tahun_pilar' => $validatedData['tahun_realisasi'],
                    'nama_pilar'  => $validatedData['pilar'],
                ],
                [
                    'anggaran_pilar' => 0,
                ]
            );

            $csr->nama_program      = $validatedData['nama_program'];
            $csr->id_pilar          = $pilarRecord->id_pilar;
            $csr->bulan_realisasi   = $validatedData['bulan_realisasi'];
            $csr->tahun_realisasi   = $validatedData['tahun_realisasi'];
            $csr->tpb               = $validatedData['tpb'];
            $csr->asta_cita         = $validatedData['asta_cita'];
            $csr->cabang            = $validatedData['cabang'];
            $csr->bentuk_bantuan    = $validatedData['bentuk_bantuan'];
            $csr->kabupaten         = !empty($validatedData['kabupaten']) ? Str::title($validatedData['kabupaten']) : '-';
            $csr->desa              = !empty($validatedData['desa']) ? Str::title($validatedData['desa']) : '-';
            $csr->biaya_program     = $validatedData['biaya_program'];
            $csr->biaya_realisasi   = $validatedData['biaya_realisasi'];
            $csr->realisasi_program = $validatedData['realisasi_program'];
            $csr->status            = $validatedData['status'];
            $csr->link_ig           = $validatedData['link_ig'];
            $csr->link_berita       = $validatedData['link_berita'];
            $csr->link_gdrive       = $validatedData['link_gdrive'];
            $csr->save();

            ActivityLog::create([
                'id_user'   => $user->id_user ?? null,
                'nama_user' => $user->username ?? 'System',
                'aktivitas' => [
                    'modul'     => 'CSR',
                    'nama_user' => $user->username ?? 'System',
                    'aksi'      => 'UPDATE',
                    'deskripsi' => "Mengubah data CSR: {$csr->nama_program} pada bulan {$csr->bulan_realisasi}",
                    'data_lama' => $dataLama,
                    'data_baru' => $csr->toArray()
                ],
                'created_at' => now()
            ]);

            return redirect()->route('pages.csr.index', ['tahun' => $csr->tahun_realisasi])
                ->with('success', 'Data CSR berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function destroy(int $id_csr)
    {
        try {
            $user = Auth::user();
            $csr = Csr::findOrFail($id_csr);
            $dataYangDihapus = $csr->toArray();

            $csr->delete();

            ActivityLog::create([
                'id_user'   => $user->id_user ?? null,
                'nama_user' => $user->username ?? 'System',
                'aktivitas' => [
                    'modul'     => 'CSR',
                    'nama_user' => $user->username ?? 'System',
                    'aksi'      => 'DELETE',
                    'deskripsi' => "Menghapus program CSR: {$dataYangDihapus['nama_program']} bulan " . ($dataYangDihapus['bulan_realisasi'] ?? '-'),
                    'data_lama' => $dataYangDihapus,
                    'data_baru' => null
                ],
                'created_at' => now()
            ]);

            return redirect()->back()->with('success', "Kegiatan CSR berhasil dihapus!");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal menghapus data: " . $e->getMessage());
        }
    }

    public function inlineUpdate(Request $request, int $id_csr)
    {
        $request->validate([
            'column' => 'required|string|in:nama_program,pilar,bulan_realisasi,tpb,asta_cita,cabang,bentuk_bantuan,kabupaten,desa,biaya_program,biaya_realisasi,realisasi_program,status,link_ig,link_berita,link_gdrive',
            'value'  => 'nullable'
        ]);

        try {
            $csr = Csr::findOrFail($id_csr);
            $column = $request->column;
            $newValue = trim((string) $request->value);

            if ($column === 'pilar') {
                if (!in_array($newValue, $this->listPilar)) {
                    return response()->json(['success' => false, 'message' => 'Pilihan pilar tidak valid.'], 422);
                }
                // Cari atau buat id_pilar berdasarkan tahun data CSR tersebut
                $pilarRecord = Pilar::firstOrCreate(
                    [
                        'tahun_pilar' => $csr->tahun_realisasi,
                        'nama_pilar'  => $newValue,
                    ],
                    [
                        'anggaran_pilar' => 0,
                    ]
                );
                $csr->id_pilar = $pilarRecord->id_pilar;
                $csr->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Data CSR berhasil diperbarui!',
                    'value'   => $newValue
                ]);
            }

            if ($column === 'bulan_realisasi' && !in_array($newValue, $this->listBulan)) {
                return response()->json(['success' => false, 'message' => 'Pilihan bulan tidak valid.'], 422);
            }
            if ($column === 'tpb' && !in_array($newValue, $this->listTpb)) {
                return response()->json(['success' => false, 'message' => 'Pilihan TPB tidak valid.'], 422);
            }
            if ($column === 'asta_cita' && !in_array($newValue, $this->listAstaCita)) {
                if (is_numeric($newValue) && (int) $newValue >= 1 && (int) $newValue <= 8) {
                    $newValue = "Asta Cita " . $newValue;
                } else {
                    return response()->json(['success' => false, 'message' => 'Pilihan Asta Cita tidak valid.'], 422);
                }
            }
            if ($column === 'cabang' && !in_array($newValue, $this->listCabang)) {
                return response()->json(['success' => false, 'message' => 'Pilihan Cabang tidak valid.'], 422);
            }
            if ($column === 'nama_program' && empty($newValue)) {
                return response()->json(['success' => false, 'message' => 'Nama Program tidak boleh kosong.'], 422);
            }
            if ($column === 'status') {
                $newValue = strtolower($newValue);
                if (!in_array($newValue, ['done', 'undone'])) {
                    return response()->json(['success' => false, 'message' => 'Status harus Done atau Undone.'], 422);
                }
            }
            if (in_array($column, ['biaya_program', 'biaya_realisasi']) && (float) $newValue < 0) {
                return response()->json(['success' => false, 'message' => 'Nominal tidak boleh kurang dari 0.'], 422);
            }
            if (in_array($column, ['link_ig', 'link_berita', 'link_gdrive'])) {
                $links = array_filter(array_map('trim', explode("\n", $newValue)));
                foreach ($links as $link) {
                    if (!filter_var($link, FILTER_VALIDATE_URL)) {
                        return response()->json(['success' => false, 'message' => "Link tidak valid: {$link}"], 422);
                    }
                }
            }
            if (in_array($column, ['kabupaten', 'desa'])) {
                $newValue = Str::title($newValue);
            }

            $csr->$column = $newValue;
            $csr->save();

            return response()->json([
                'success' => true,
                'message' => 'Data CSR berhasil diperbarui!',
                'value'   => $newValue
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }
   public function apiGetProgramByTahun(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));

        $programs = Csr::where('tahun_realisasi', $tahun)
            ->whereNotNull('nama_program')->where('nama_program', '!=', '')
            ->distinct()->pluck('nama_program');

        return response()->json($programs);
    }

    // ========================================================
    // API CHART/WIDGET
    // ========================================================

 public function apiStatistikCard(Request $request)
{
    $tahun = $request->filled('tahun') ? $request->get('tahun') : date('Y');
    
    $query = Csr::where('tahun_realisasi', $tahun);
    $this->terapkanFilterGlobal($query, $request);

    // 1. Total Kegiatan
    $totalKegiatan = (clone $query)->count();

    // 2. Realisasi Selesai
    $queryDone = Csr::where('tahun_realisasi', $tahun);
    $this->terapkanFilterGlobal($queryDone, $request, ['g_status']);
    $realisasiDone = $queryDone->where('status', 'done')->count();

    // 3. Total Biaya Realisasi
    $queryBiayaDone = Csr::where('tahun_realisasi', $tahun);
    $this->terapkanFilterGlobal($queryBiayaDone, $request, ['g_status']);
    $totalRealisasi = $queryBiayaDone->where('status', 'done')->sum('biaya_realisasi');

    // 4. Kabupaten 
    $totalKabupaten = (clone $query)
        ->whereNotNull('kabupaten')
        ->where('kabupaten', '!=', '-')
        ->whereRaw("TRIM(kabupaten) != ''")
        ->where('kabupaten', 'REGEXP', '[a-zA-Z0-9]')
        ->distinct()
        ->count('kabupaten');

    // 5. Desa 
    $totalDesa = (clone $query)
        ->whereNotNull('desa')
        ->where('desa', '!=', '-')
        ->whereRaw("TRIM(desa) != ''")
        ->where('desa', 'REGEXP', '[a-zA-Z0-9]')
        ->distinct()
        ->count('desa');

    return response()->json([
        'total_kegiatan'   => $totalKegiatan,
        'realisasi_done'   => $realisasiDone,
        'total_realisasi'  => $totalRealisasi, 
        'total_kabupaten'  => $totalKabupaten,
        'total_desa'       => $totalDesa,
    ]);
}
  public function apiKelompokPilarDanBulanan(Request $request)
{
    $tahun = $request->get('tahun', date('Y'));

    // --- 1. DATA ANGGARAN PER PILAR ---
    $pilarRecords = Pilar::where('tahun_pilar', $tahun)->get()->keyBy('nama_pilar');
    $anggaranPilar = collect($this->listPilar)->map(function($p) use ($pilarRecords) {
        return [
            'nama_pilar' => $p,
            'jumlah_anggaran' => (float) ($pilarRecords[$p]->anggaran_pilar ?? 0),
        ];
    });


    // --- 2. DATA REALISASI PER PILAR ---
    $queryRealisasi = Csr::where('tahun_realisasi', $tahun)->where('status', 'done');
    
    // Terapkan filter global untuk realisasi
    $this->terapkanFilterGlobal($queryRealisasi, $request, ['g_pilar', 'g_status']);

    $rawRealisasi = $queryRealisasi->join('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
        ->select('pilar.nama_pilar', DB::raw('COUNT(csr.id_csr) as total_realisasi'))
        ->groupBy('pilar.nama_pilar')
        ->pluck('total_realisasi', 'nama_pilar')
        ->toArray();

    $realisasiPilar = collect($this->listPilar)->map(fn($p) => [
        'nama_pilar' => $p,
        'total_realisasi' => (int) ($rawRealisasi[$p] ?? 0),
    ]);


    // --- 3. DATA JUMLAH KEGIATAN BULANAN (Hanya status 'done') ---
    $queryKegiatan = Csr::where('tahun_realisasi', $tahun)
        ->where('status', 'done'); 
    
    // Terapkan filter global dan abaikan 'bulan' serta 'g_status'
    $this->terapkanFilterGlobal($queryKegiatan, $request, ['bulan', 'g_status']);

    $rawKegiatan = $queryKegiatan->select('bulan_realisasi', DB::raw('count(*) as total'))
        ->groupBy('bulan_realisasi')->pluck('total', 'bulan_realisasi')->toArray();

    $kegiatanBulanan = [];
    foreach ($this->listBulan as $bln) {
        $kegiatanBulanan[$bln] = $rawKegiatan[$bln] ?? 0;
    }


    // --- RETURN KETIGA DATA DALAM 1 JSON ---
    return response()->json([
        'anggaran_pilar' => $anggaranPilar,
        'realisasi_pilar' => $realisasiPilar,
        'kegiatan_bulanan' => $kegiatanBulanan,
    ]);
}

   public function apiRingkasanDashboardGabungan(Request $request)
{
    $tahun = $request->get('tahun', date('Y'));

    // ==========================================
    // 1. DATA DONUT REALISASI ANGGARAN
    // ==========================================
    $isFilteredSpecific = $request->filled('bulan') || $request->filled('g_desa') || $request->filled('g_kabupaten');

    $queryPagu = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
        ->where('csr.tahun_realisasi', $tahun);
    
    $queryRealisasiDonut = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
        ->where('csr.tahun_realisasi', $tahun);

    $this->terapkanFilterGlobal($queryPagu, $request);
    $this->terapkanFilterGlobal($queryRealisasiDonut, $request);

    if ($request->filled('g_pilar')) {
        $pagu = Pilar::where('tahun_pilar', $tahun)
            ->where('nama_pilar', $request->g_pilar)
            ->sum('anggaran_pilar');
    } else {
        $pagu = Pilar::where('tahun_pilar', $tahun)->sum('anggaran_pilar');
    }

    if ($request->filled('g_program') && $request->g_program !== 'all') {
        $pagu = (clone $queryPagu)->sum('csr.biaya_program');
    }

    $terpakai = $queryRealisasiDonut->sum('csr.biaya_realisasi');
    $sisa = $isFilteredSpecific ? 0 : ($pagu - $terpakai);
    $efektifitas = $pagu > 0 ? round(($terpakai / $pagu) * 100) : 0;

    $donutRealisasi = [
        'pagu'                 => (float) $pagu,
        'terpakai'             => (float) $terpakai,
        'sisa'                 => (float) ($sisa < 0 ? 0 : $sisa),
        'efektifitas'          => $efektifitas,
        'is_filtered_specific' => $isFilteredSpecific,
    ];


    // ==========================================
    // 2. DATA RINGKASAN TRIWULAN
    // ==========================================
    $queryTriwulan = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
        ->where('csr.tahun_realisasi', $tahun);
        
    $this->terapkanFilterGlobal($queryTriwulan, $request, ['bulan']);
    
    $rawTriwulan = $queryTriwulan->select('pilar.nama_pilar', 'csr.bulan_realisasi', 'csr.biaya_realisasi')->get();

    $dataTriwulan = [];
    foreach ($this->listPilar as $namaPilar) {
        $tw1 = $tw2 = $tw3 = $tw4 = 0;
        
        foreach ($rawTriwulan->where('nama_pilar', $namaPilar) as $row) {
            if (in_array($row->bulan_realisasi, ['Januari', 'Februari', 'Maret'])) $tw1 += $row->biaya_realisasi;
            elseif (in_array($row->bulan_realisasi, ['April', 'Mei', 'Juni'])) $tw2 += $row->biaya_realisasi;
            elseif (in_array($row->bulan_realisasi, ['Juli', 'Agustus', 'September'])) $tw3 += $row->biaya_realisasi;
            elseif (in_array($row->bulan_realisasi, ['Oktober', 'November', 'Desember'])) $tw4 += $row->biaya_realisasi;
        }

        $dataTriwulan[$namaPilar] = [
            'TW1' => (float) $tw1,
            'TW2' => (float) $tw2,
            'TW3' => (float) $tw3,
            'TW4' => (float) $tw4,
        ];
    }

    $ringkasanTriwulan = [
        'pilars' => $this->listPilar,
        'data'   => $dataTriwulan
    ];


   // ==========================================
    // 3. DATA PROGRAM PER WILAYAH 
    // ==========================================
    $mode = $request->get('mode', 'kabupaten');
    $kolomWilayah = ($mode === 'desa') ? 'desa' : 'kabupaten';

    $queryWilayah = Csr::where('tahun_realisasi', $tahun);

    // Ambil nilai filter status dari request
    $filterStatus = $request->get('g_status');

    if ($filterStatus === 'done') {
        $queryWilayah->where('csr.status', 'done');
    } elseif ($filterStatus === 'undone') {
        $queryWilayah->where('csr.status', 'undone');
    } else {       
        $queryWilayah->where('csr.status', 'done');
    }

    $this->terapkanFilterGlobal($queryWilayah, $request, ['g_status']);
    $rawKumpulanData = $queryWilayah->select($kolomWilayah, 'id_csr')
        ->whereNotNull($kolomWilayah)
        ->get();
    $mappingHasil = [];

    foreach ($rawKumpulanData as $row) {
        $wilayahMentah = trim($row->$kolomWilayah);
        if ($wilayahMentah === '' || $wilayahMentah === null) {
            $wilayahMentah = '-';
        }

        $pecahWilayah = explode(',', $wilayahMentah);

        $wilayahUnikPerBaris = [];
        foreach ($pecahWilayah as $wilayah) {
            $wilayahBersih = trim($wilayah); 
            if ($wilayahBersih !== '') {
                $wilayahUnikPerBaris[strtolower($wilayahBersih)] = $wilayahBersih; 
            }
        }

        foreach ($wilayahUnikPerBaris as $wilayahBersih) {
            if (!isset($mappingHasil[$wilayahBersih])) {
                $mappingHasil[$wilayahBersih] = 0;
            }
        
            $mappingHasil[$wilayahBersih]++;
        }
    }

    $formattedData = [];
    foreach ($mappingHasil as $namaWilayah => $total) {
        $formattedData[] = [
            'nama_wilayah' => $namaWilayah,
            'total'        => $total
        ];
    }

    usort($formattedData, function ($a, $b) {
        $isATidakValid = ($a['nama_wilayah'] === '-' || !preg_match('/[a-zA-Z0-9]/', $a['nama_wilayah']));
        $isBTidakValid = ($b['nama_wilayah'] === '-' || !preg_match('/[a-zA-Z0-9]/', $b['nama_wilayah']));

        if ($isATidakValid && !$isBTidakValid) {
            return 1;
        }
        if (!$isATidakValid && $isBTidakValid) {
            return -1;
        }

        return $b['total'] <=> $a['total'];
    });

    $programWilayah = $formattedData;

    return response()->json([
        'donut_realisasi'     => $donutRealisasi,
        'ringkasan_triwulan'  => $ringkasanTriwulan,
        'program_wilayah'     => $programWilayah,
    ]);
}

    /** 7. Chart Asta Cita — ikut filter g_status */
    public function apiAstaCita(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $query = Csr::where('tahun_realisasi', $tahun);
        $this->terapkanFilterGlobal($query, $request);

        $raw = $query->select('asta_cita', DB::raw('count(*) as total_jumlah'))
            ->groupBy('asta_cita')
            ->pluck('total_jumlah', 'asta_cita')
            ->toArray();

        $data = collect($this->listAstaCita)->map(fn($item) => [
            'asta_cita'    => $item,
            'total_jumlah' => (int) ($raw[$item] ?? 0),
        ]);

        return response()->json($data);
    }
    public function importPaste(Request $request)
{
    $request->validate([
        'excel_text' => 'required_without:rows|nullable|string',
        'tahun'      => 'required|numeric'
    ]);

    try {
        $tahunTerpilih = (int) $request->input('tahun');
        $isPreview = $request->input('is_preview') == '1';
        $dataToInsert = [];

        // Blok penyimpanan permanen (hanya dieksekusi di sini)
        if (!$isPreview && $request->has('rows')) {
            $submittedRows = $request->input('rows');

            $pilarRecords = Pilar::where('tahun_pilar', $tahunTerpilih)->get()->keyBy(function($item) {
                return strtolower(trim($item->nama_pilar));
            });

            // Validasi: Jika master pilar untuk tahun tersebut kosong di database
            if ($pilarRecords->isEmpty()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal menyimpan: Master data pilar untuk tahun ' . $tahunTerpilih . ' belum tersedia di database. Mohon input terlebih dahulu.'
                ], 422);
            }

            foreach ($submittedRows as $row) {
                $namaPilar = trim($row['nama_pilar_preview'] ?? '');
                $lowerPilarName = strtolower($namaPilar);

                if (!isset($pilarRecords[$lowerPilarName])) {
                    continue; 
                }

                $idPilar = $pilarRecords[$lowerPilarName]->id_pilar;

                // Normalisasi Asta Cita agar sesuai enum DB
                preg_match('/\d+/', $row['asta_cita'] ?? '1', $matchAsta);
                $cleanAsta = 'Asta Cita ' . ($matchAsta[0] ?? '1');

                $dataToInsert[] = [
                    'id_pilar'          => $idPilar,
                    'nama_program'      => $row['nama_program'] ?? 'Program CSR',
                    'tpb'               => $row['tpb'] ?? '1. Tanpa Kemiskinan',
                    'asta_cita'         => $cleanAsta,
                    'cabang'            => 'PNK',
                    'biaya_program'     => (int) ($row['biaya_program'] ?? 0),
                    'biaya_realisasi'   => (int) ($row['biaya_realisasi'] ?? 0),
                    'realisasi_program' => $row['realisasi_program'] ?? '-',
                    'bentuk_bantuan'    => $row['bentuk_bantuan'] ?? '-',
                    'bulan_realisasi'   => $row['bulan_realisasi'] ?? 'Januari',
                    'tahun_realisasi'   => $tahunTerpilih,
                    'kabupaten'         => $row['kabupaten'] ?? '-',
                    'desa'              => $row['desa'] ?? '-',
                    'status'            => $row['status'] ?? 'done',
                    'link_ig'           => null,
                    'link_berita'       => null,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ];
            }

            if (empty($dataToInsert)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal menyimpan: Pastikan pilar yang dipilih sesuai dengan master data pada tahun tersebut.'
                ], 422);
            }

            Csr::insert($dataToInsert);

            return response()->json([
                'success' => true,
                'message' => count($dataToInsert) . ' Data CSR berhasil disimpan permanen!',
                'total'   => count($dataToInsert)
            ]);
        }

        $rawText = $request->input('excel_text');
        if (empty($rawText)) {
            return response()->json(['success' => false, 'message' => 'Data teks excel kosong.'], 422);
        }

        $parseResponse = $this->parseExcelCSR($request);
        $responseData = json_decode($parseResponse->getContent(), true);

        if (!$responseData['success'] || empty($responseData['data'])) {
            return response()->json([
                'success' => false, 
                'message' => $responseData['message'] ?? 'Tidak ada baris data valid yang berhasil diproses.'
            ], 422);
        }

        return response()->json([
            'success' => true,
            'data'    => $responseData['data'],
            'message' => 'Berhasil memuat preview untuk ' . count($responseData['data']) . ' data.'
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    }
}

public function parseExcelCSR(Request $request)
{
    $tahunTerpilih = (int) $request->input('tahun');
    $rawText = $request->input('excel_text');

    try {
        if (empty($rawText)) {
            return response()->json(['success' => false, 'message' => 'Data teks excel kosong.'], 422);
        }

        $validTpb = [
            '1. Tanpa Kemiskinan', '2. Tanpa Kelaparan', '3. Kehidupan Sehat dan Sejahtera',
            '4. Pendidikan Berkualitas', '5. Kesetaraan Gender', '6. Air Bersih dan Sanitasi Layak',
            '7. Energi Bersih dan Terjangkau', '8. Pekerjaan Layak dan Pertumbuhan Ekonomi',
            '9. Industri, Inovasi dan Infrastruktur', '10. Berkurangnya Kesenjangan',
            '11. Kota dan Permukiman yang Berkelanjutan', '12. Konsumsi dan Produksi yang Bertanggung Jawab',
            '13. Penanganan Perubahan Iklim', '14. Ekosistem Kelautan', '15. Ekosistem Daratan',
            '16. Perdamaian, Keadilan dan Kelembagaan yang Tangguh', '17. Kemitraan untuk Mencapai Tujuan'
        ];

        $pilarCollection = Pilar::where('tahun_pilar', $tahunTerpilih)->get();
        $listPilarNames = $pilarCollection->pluck('nama_pilar')->toArray();
        
        if (empty($listPilarNames)) {
            return response()->json([
                'success' => false, 
                'message' => 'Master data pilar untuk tahun ' . $tahunTerpilih . ' tidak ditemukan. Harap input master pilar tahun ' . $tahunTerpilih . ' terlebih dahulu.'
            ], 422);
        }

        $listBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $wilayahKeywords = [
            'Sambas' => ['sambas', 'sebubus', 'paloh', 'tekarong', 'temajuk'],
            'Landak' => ['landak', 'ngabang', 'kutaringin', 'darit', 'menyuke'],
            'Kubu Raya' => ['arang limbung', 'limbung', 'sungai raya', 'kuala dua', 'rasau jaya', 'kubu raya', 'kkr', 'pal ix', 'rasau', 'sungai ambawang', 'Desa Kapur', 'Desa Pungur', 'Pungur Kecil', 'Pungur Besar'],
            'Pontianak' => ['pontianak kota', 'darat sekip', 'sungai bangkong', 'sungai jawi', 'kotabaru', 'pontianak', 'ptk', 'kota pontianak', 'siantan']
        ];

        $cleanNumber = function ($str) {
            return (int) preg_replace('/[^0-9]/', '', $str ?? '0');
        };

       $lines = explode("\n", trim($rawText));
        $parsedData = [];

        foreach ($lines as $line) {
            if (trim($line) === '') continue;

            // Pecah kolom berdasarkan tab (standar copy-paste excel)
            $cols = explode("\t", trim($line, "\r"));
            $cols = array_map('trim', $cols);

            if (count($cols) < 2) {
                $cols = preg_split('/\t+/', trim($line));
            }

            if (count($cols) < 2) continue;

            // Lewati baris header jika ikut ter-copy
            $joinedLine = strtolower(implode(' ', $cols));
            if (str_contains($joinedLine, 'nama program') || (str_contains($joinedLine, 'bentuk bantuan') && str_contains($joinedLine, 'pilar'))) {
                continue;
            }

            // Fungsi pembersih angka rupiah string ke integer
            $parseRupiah = function ($str) {
                return (int) preg_replace('/[^0-9]/', '', $str ?? '0');
            };

            // PEMETAAN KOLOM BERDASARKAN URUTAN EXCEL ASLI KAMU:
            // Index 0: Program
            $namaProgram = $cols[0] ?? 'Program CSR';
            
            // Index 1: Bentuk Bantuan
            $bentukBantuan = $cols[1] ?? '-';
            
            // Index 2: Pilar
            $rawPilar = $cols[2] ?? ($listPilarNames[0] ?? '');
            
            // Index 3: TPB
            $finalTpb = $cols[3] ?? '1. Tanpa Kemiskinan';
            
            // Index 4: Asta Cita
            $astaCita = $cols[4] ?? 'Asta Cita 1';
            
            // Index 5: Cabang
            $cabang = $cols[5] ?? 'PNK';
            
            // Index 6: Biaya Program
            $biayaProgram = $parseRupiah($cols[6] ?? '0');
            
            // Index 7: Biaya Realisasi
          
            $biayaRealisasiVal = $cols[7] ?? ($cols[8] ?? '0');
            $biayaRealisasi = $parseRupiah($biayaRealisasiVal);
            if ($biayaRealisasi === 0 && $biayaProgram > 0) {
                $biayaRealisasi = $biayaProgram;
            }

            // Index 8/9: Realisasi Program / Keterangan
            $realisasiProgram = $cols[8] ?? ($cols[9] ?? $bentukBantuan);
            if (is_numeric(str_replace(['.', ','], '', $realisasiProgram))) {
                $realisasiProgram = $bentukBantuan;
            }

            // Index 9/10: Bulan Realisasi
            $bulan = 'Januari';
            $rawBulan = strtolower($cols[9] ?? ($cols[10] ?? ''));
            foreach ($listBulan as $b) {
                if (str_contains($rawBulan, strtolower($b))) {
                    $bulan = $b;
                    break;
                }
            }

            // Index Terakhir: Status (Done / Undone)
            $statusFinal = 'done';
            $rawStatus = strtolower(end($cols));
            if (str_contains($rawStatus, 'undone') || str_contains($rawStatus, 'belum')) {
                $statusFinal = 'undone';
            }

            // Deteksi Pilar yang cocok dengan Master Database (Pencocokan fleksibel)
            $targetPilarName = $listPilarNames[0];
            foreach ($listPilarNames as $pilarEnum) {
                if (str_contains(strtolower($rawPilar), strtolower($pilarEnum)) || str_contains(strtolower($pilarEnum), strtolower($rawPilar))) {
                    $targetPilarName = $pilarEnum;
                    break;
                }
            }

            // Deteksi Wilayah dari keseluruhan teks baris
            $kabupaten = '-';
            $desa = '-';
            foreach ($wilayahKeywords as $kabName => $keywords) {
                foreach ($keywords as $kw) {
                    if (str_contains($joinedLine, $kw)) {
                        $kabupaten = $kabName;
                        $desa = Str::title($kw);
                        break 2;
                    }
                }
            }

            $parsedData[] = [
                'nama_pilar_preview'=> $targetPilarName, 
                'nama_program'      => Str::title($namaProgram),
                'tpb'               => $finalTpb,
                'asta_cita'         => $astaCita,
                'cabang'            => $cabang !== '' ? $cabang : 'PNK',
                'biaya_program'     => $biayaProgram,
                'biaya_realisasi'   => $biayaRealisasi,
                'realisasi_program' => $realisasiProgram,
                'bentuk_bantuan'    => $bentukBantuan,
                'bulan_realisasi'   => $bulan,
                'kabupaten'         => $kabupaten,
                'desa'              => $desa,
                'status'            => $statusFinal,
                'tahun_realisasi'   => $tahunTerpilih
            ];
        }

        return response()->json([
            'success'    => true,
            'data'       => $parsedData,
            'message'    => 'Berhasil memuat preview.',
            'pilar_list' => $pilarCollection
        ]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
    }
}
   public function exportExcel(Request $request)
{
    $bulan = $request->input('bulan'); 
    $tahun = $request->input('tahun', date('Y'));

    if (!$bulan) {
        $indexBulanAktif = (int) date('m') - 1; // 0 untuk Januari, 7 untuk Agustus, dst.
        $bulan = $this->listBulan[$indexBulanAktif] ?? 'Agustus';
    }

    return Excel::download(new CsrExport($bulan, $tahun), "Laporan_CSR_{$bulan}_{$tahun}.xlsx");
}
}