<?php

namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Csr;
use App\Models\Pilar;
use App\Models\Berita;
use App\Models\post;
 
class DashboardController extends Controller
{
    private array $listBulan = [
        'Januari','Februari','Maret','April','Mei','Juni',
        'Juli','Agustus','September','Oktober','November','Desember'
    ];
 
    private array $listPilar = ['Sosial', 'Ekonomi', 'Lingkungan'];
 

    private function terapkanFilterGlobalCsr($query, Request $request, array $skip = [])
    {
        if (!in_array('bulan', $skip) && $request->filled('g_bulan') && $request->g_bulan !== 'all') {
            $query->where('csr.bulan_realisasi', $request->g_bulan);
        }
        if (!in_array('g_kabupaten', $skip) && $request->filled('g_kabupaten') && $request->g_kabupaten !== 'all') {
            $query->where('csr.kabupaten', $request->g_kabupaten);
        }
        if (!in_array('g_desa', $skip) && $request->filled('g_desa') && $request->g_desa !== 'all') {
            $query->where('csr.desa', $request->g_desa);
        }
        if (!in_array('g_pilar', $skip) && $request->filled('g_pilar') && $request->g_pilar !== 'all') {
            $query->where('pilar.nama_pilar', $request->g_pilar);
        }
        if (!in_array('g_program', $skip) && $request->filled('g_program') && $request->g_program !== 'all') {
            $query->where('csr.nama_program', $request->g_program);
        }
 
        return $query;
    }
 
    /**
     * View Utama Dashboard Terintegrasi (CSR, SOSMED, BERITA)
     */
    public function index(Request $request)
    {
        // Parameter Utama / Global Filter
        $tahunTerpilih = $request->get('tahun', date('Y'));
 
        // ========================================================
        // MODUL 1: DATA CSR (id_pilar + JOIN pilar, filter global lengkap)
        // ========================================================
 
        // 1. Daftar Tahun
        $daftarTahun = Csr::distinct()->orderBy('tahun_realisasi', 'desc')->pluck('tahun_realisasi');
        if ($daftarTahun->isEmpty()) {
            $daftarTahun = collect([(int) $tahunTerpilih]);
        }
 
        // 2. Nilai filter aktif (buat di-echo balik ke <select> di blade)
        $pilarFilter     = $request->get('g_pilar', 'all');
        $bulanFilter     = $request->get('g_bulan', 'all');
        $kabupatenFilter = $request->get('g_kabupaten', 'all');
        $desaFilter      = $request->get('g_desa', 'all');
        $programFilter   = $request->get('g_program', 'all');
 
        // 3. Opsi dropdown Program / Kabupaten / Desa — dinamis sesuai tahun terpilih
        $daftarProgramUnikCsr = Csr::where('tahun_realisasi', $tahunTerpilih)
            ->whereNotNull('nama_program')->where('nama_program', '!=', '-')
            ->distinct()->orderBy('nama_program')->pluck('nama_program');
 
        $daftarKabupatenCsr = Csr::where('tahun_realisasi', $tahunTerpilih)
            ->whereNotNull('kabupaten')->where('kabupaten', '!=', '-')
            ->distinct()->orderBy('kabupaten')->pluck('kabupaten');
 
        $daftarDesaCsr = Csr::where('tahun_realisasi', $tahunTerpilih)
            ->whereNotNull('desa')->where('desa', '!=', '-')
            ->distinct()->orderBy('desa')->pluck('desa');
 
        // Base Query CSR (semua filter aktif) — dipakai buat total realisasi & pagu program
        $baseCsr = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);
        $this->terapkanFilterGlobalCsr($baseCsr, $request);
 
        // --- A. REALISASI ANGGARAN & DONUT CHART ---
        if ($pilarFilter !== 'all') {
            $totalPaguAnggaran = (float) Pilar::where('tahun_pilar', $tahunTerpilih)
                ->where('nama_pilar', $pilarFilter)
                ->sum('anggaran_pilar');
        } else {
            $totalPaguAnggaran = (float) Pilar::where('tahun_pilar', $tahunTerpilih)->sum('anggaran_pilar');
        }
        // Kalau difilter program spesifik, pagu diambil dari biaya_program program itu (ikut semua filter aktif)
        if ($programFilter !== 'all') {
            $totalPaguAnggaran = (float) (clone $baseCsr)->sum('csr.biaya_program');
        }
 
        $totalRealisasiBiaya = (float) (clone $baseCsr)->sum('csr.biaya_realisasi');
 
        $sisaAnggaranHitung = $totalPaguAnggaran - $totalRealisasiBiaya;
        $efektifitasHitung  = $totalPaguAnggaran > 0 ? round(($totalRealisasiBiaya / $totalPaguAnggaran) * 100) : 0;
 
        $realisasiAnggaran = [
            'total_anggaran'      => $totalPaguAnggaran,
            'total_realisasi'     => $totalRealisasiBiaya,
            'sisa_anggaran'       => max(0, $sisaAnggaranHitung),
            'tingkat_efektifitas' => $efektifitasHitung
        ];
 
        // --- B. ANGGARAN PILAR (plafon master, TIDAK ikut filter apapun — memang breakdown per pilar) ---
        $anggaranPilar = collect($this->listPilar)->map(function ($namaPilar) use ($tahunTerpilih) {
            $dataPilar = Pilar::where('tahun_pilar', $tahunTerpilih)
                ->where('nama_pilar', $namaPilar)
                ->first();
 
            return (object) [
                'nama_pilar'      => $namaPilar,
                'jumlah_anggaran' => $dataPilar ? (float) $dataPilar->anggaran_pilar : 0,
            ];
        });
 
$kegiatanBulananQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
    ->where('csr.tahun_realisasi', $tahunTerpilih);
$this->terapkanFilterGlobalCsr($kegiatanBulananQuery, $request, ['bulan']);

$kegiatanBulananRaw = $kegiatanBulananQuery
    ->select('csr.bulan_realisasi', DB::raw('count(csr.id_csr) as total'))
    ->groupBy('csr.bulan_realisasi')
    ->pluck('total', 'bulan_realisasi')
    ->toArray();
$bulanSingkat = [
    'Januari' => 'Jan', 'Februari' => 'Feb', 'Maret' => 'Mar', 'April' => 'Apr',
    'Mei' => 'Mei', 'Juni' => 'Jun', 'Juli' => 'Jul', 'Agustus' => 'Agu',
    'September' => 'Sep', 'Oktober' => 'Okt', 'November' => 'Nov', 'Desember' => 'Des'
];

$kegiatanBulanan = [];
foreach ($bulanSingkat as $bulanPanjang => $bulanPendek) {
    $kegiatanBulanan[$bulanPendek] = $kegiatanBulananRaw[$bulanPanjang] 
                                  ?? $kegiatanBulananRaw[$bulanPendek] 
                                  ?? 0;
}
 
        // --- D. REALISASI PER PILAR / Status Done (skip filter pilar — ini breakdown per pilar) ---
        $realisasiPerPilarQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih)
            ->where('csr.status', 'done');
        $this->terapkanFilterGlobalCsr($realisasiPerPilarQuery, $request, ['g_pilar']);
 
        $realisasiPerPilarRaw = $realisasiPerPilarQuery
            ->select('pilar.nama_pilar', DB::raw('COUNT(csr.id_csr) as jumlah_kegiatan_done'))
            ->groupBy('pilar.nama_pilar')
            ->pluck('jumlah_kegiatan_done', 'nama_pilar')
            ->toArray();
 
        $realisasiPerPilar = collect($this->listPilar)->map(function ($namaPilar) use ($realisasiPerPilarRaw) {
            return (object) [
                'nama_pilar'           => $namaPilar,
                'jumlah_kegiatan_done' => $realisasiPerPilarRaw[$namaPilar] ?? 0,
            ];
        });
 
        // --- E. ANGGARAN TRIWULAN PER PILAR (skip filter bulan — ini breakdown per triwulan/bulan) ---
        $triwulanQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);
        $this->terapkanFilterGlobalCsr($triwulanQuery, $request, ['bulan']);
 
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
 
// --- F. PROGRAM PER WILAYAH ---
        $modeWilayah  = $request->input('tampilkan_berdasarkan', 'kabupaten');
        $kolomWilayah = $modeWilayah === 'desa' ? 'desa' : 'kabupaten';

        // FIX #1: tambahin leftJoin ke pilar, karena terapkanFilterGlobalCsr()
        // butuh "pilar.nama_pilar" kalau filter g_pilar lagi aktif — sebelumnya
        // gak ada join ini, jadi bakal error SQL kalau pilar difilter.
        $dataWilayahQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);

        // Skip filter kabupaten/desa (karena ini breakdown per wilayah itu sendiri)
        $this->terapkanFilterGlobalCsr($dataWilayahQuery, $request, ['g_kabupaten', 'g_desa']);

        // Ambil data mentah dari kolom wilayah tersebut
        $rawKumpulanData = $dataWilayahQuery
            ->select("csr.{$kolomWilayah} as wilayah_raw")
            ->whereNotNull("csr.{$kolomWilayah}")
            ->where("csr.{$kolomWilayah}", '!=', '')
            ->get();

        // FIX #2: normalisasi nama wilayah biar "Sambas", "sambas", "SAMBAS " (beda
        // kapital/spasi doang) dianggap SATU wilayah yang sama, bukan pecah jadi
        // beberapa entry terpisah. Kunci pencocokan pakai versi lowercase+trim,
        // tapi label yang ditampilkan tetap rapi pakai Title Case.
        $mappingHasil = [];   // kunciCocok(lowercase) => jumlah
        $labelAsli    = [];   // kunciCocok(lowercase) => label tampilan (Title Case)

        foreach ($rawKumpulanData as $row) {
            $pecahWilayah = explode(',', $row->wilayah_raw);

            foreach ($pecahWilayah as $wilayah) {
                // Rapikan spasi berlebih & spasi di pinggir
                $wilayahBersih = trim(preg_replace('/\s+/', ' ', $wilayah));

                // Lewati kalau kosong atau cuma tanda strip (artinya "belum diisi")
                if ($wilayahBersih === '' || $wilayahBersih === '-') {
                    continue;
                }

                $kunciCocok = mb_strtolower($wilayahBersih);

                if (!isset($mappingHasil[$kunciCocok])) {
                    $mappingHasil[$kunciCocok] = 0;
                    $labelAsli[$kunciCocok] = \Illuminate\Support\Str::title($wilayahBersih);
                }
                $mappingHasil[$kunciCocok]++;
            }
        }

        // Ubah ke format Collection/Object untuk dibaca oleh Blade / Chart
        $dataWilayahChart = collect($mappingHasil)
            ->map(function ($total, $kunciCocok) use ($labelAsli) {
                return (object) [
                    'nama_lokasi'   => $labelAsli[$kunciCocok],
                    'total_program' => $total,
                ];
            })
            ->sortByDesc('total_program')
            ->values();
        // --- G. ASTA CITA (ikut semua filter) ---
        $dataAstaCitaQuery = Csr::leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahunTerpilih);
        $this->terapkanFilterGlobalCsr($dataAstaCitaQuery, $request);
 
        $dataAstaCitaChart = $dataAstaCitaQuery
            ->select('csr.asta_cita', DB::raw('COUNT(csr.id_csr) as total_jumlah'))
            ->groupBy('csr.asta_cita')
            ->get();
 
 
        // ========================================================
        // MODUL 2: DATA SOSMED (Tabel: post) 
        // ========================================================
 
        $tahunSosmedTerpilih = $request->get('tahun_sosmed', $tahunTerpilih);
        $bulanSosmed = $request->get('bulan_sosmed', 'all');
        $kategoriKontenTerpilih = $request->get('kategori_konten', 'all');
 
        $queryTabel = DB::table('post')->whereYear('tanggal', $tahunSosmedTerpilih);
 
        if ($request->filled('tipe_konten') && $request->tipe_konten !== 'Keseluruhan') {
            $queryTabel->where('tipe_konten', $request->tipe_konten);
        }
 
        if ($kategoriKontenTerpilih !== 'all' && $kategoriKontenTerpilih !== '') {
            $queryTabel->where('kategori_konten', $kategoriKontenTerpilih);
        }
 
        if ($bulanSosmed !== 'all' && $bulanSosmed !== '') {
            $queryTabel->whereMonth('tanggal', $bulanSosmed);
        }
 
        $daftarPost = $queryTabel->orderBy('tanggal', 'desc')->get();
 
        $baseCardsQuery = DB::table('post')->whereYear('tanggal', $tahunSosmedTerpilih);
 
        if ($bulanSosmed !== 'all' && $bulanSosmed !== '') {
            $baseCardsQuery->whereMonth('tanggal', $bulanSosmed);
        }
        if ($kategoriKontenTerpilih !== 'all' && $kategoriKontenTerpilih !== '') {
            $baseCardsQuery->where('kategori_konten', $kategoriKontenTerpilih);
        }
 
        $cards = new \stdClass();
        $cards->total_postingan = (clone $baseCardsQuery)->count();
        $cards->total_collab    = (clone $baseCardsQuery)->where('kategori_konten', 'Collab Content')->count();
        $cards->total_owned     = (clone $baseCardsQuery)->where('kategori_konten', 'Owned Production')->count();
        $cards->total_shared    = (clone $baseCardsQuery)->where('kategori_konten', 'Shared Content')->count();
 
        $trenPostinganRaw = DB::table('post')
            ->whereYear('tanggal', $tahunSosmedTerpilih)
            ->select(DB::raw('MONTH(tanggal) as bulan'), DB::raw('COUNT(*) as total'));
 
        if ($request->filled('tipe_konten') && $request->tipe_konten !== 'Keseluruhan') {
            $trenPostinganRaw->where('tipe_konten', $request->tipe_konten);
        }
        if ($kategoriKontenTerpilih !== 'all' && $kategoriKontenTerpilih !== '') {
            $trenPostinganRaw->where('kategori_konten', $kategoriKontenTerpilih);
        }
        $trenPostinganRaw = $trenPostinganRaw->groupBy(DB::raw('MONTH(tanggal)'))->pluck('total', 'bulan')->toArray();
 
        $trenPostinganBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $trenPostinganBulanan[] = $trenPostinganRaw[$i] ?? 0;
        }
 
      $queryInteraksi = DB::table('post')->whereYear('tanggal', $tahunSosmedTerpilih);
 
        if ($bulanSosmed !== 'all' && $bulanSosmed !== '') {
            $queryInteraksi->whereMonth('tanggal', $bulanSosmed);
        }
        if ($kategoriKontenTerpilih !== 'all' && $kategoriKontenTerpilih !== '') {
            $queryInteraksi->where('kategori_konten', $kategoriKontenTerpilih);
        }
        if ($request->filled('tipe_konten') && $request->tipe_konten !== 'Keseluruhan') {
            $queryInteraksi->where('tipe_konten', $request->tipe_konten);
        }
 
        // Menambahkan kolom view, share, dan retweet ke dalam select dan rumus total_skor
        $interaksiTertinggi = (clone $queryInteraksi)
            ->select(
                'topik', 
                'view', 
                'likes', 
                'comments', 
                'share', 
                'retweet', 
                DB::raw('(view + likes + comments + share + retweet) as total_skor')
            )
            ->orderBy(DB::raw('(view + likes + comments + share + retweet)'), 'desc')
            ->limit(5)->get();
 
        $interaksiTerendah = (clone $queryInteraksi)
            ->select(
                'topik', 
                'view', 
                'likes', 
                'comments', 
                'share', 
                'retweet', 
                DB::raw('(view + likes + comments + share + retweet) as total_skor')
            )
            ->orderBy(DB::raw('(view + likes + comments + share + retweet)'), 'asc')
            ->limit(5)->get();
 
 
        // ========================================================
        // MODUL 3: DATA BERITA / MEDIA MONITORING — tidak diubah
        // ========================================================
 
        $tahunBeritaTerpilih = $request->get('tahun_berita', $tahunTerpilih);
 
        $baseBerita = Berita::query();
        if ($tahunBeritaTerpilih !== 'all') {
            $baseBerita->whereYear('tanggal', $tahunBeritaTerpilih);
        }
 
        $bulanBerita = $request->get('bulan_berita', 'all');
        if ($bulanBerita && $bulanBerita !== 'all') {
            $baseBerita->whereMonth('tanggal', $bulanBerita);
        }
 
        $toneRaw = (clone $baseBerita)->select('tone', DB::raw('count(*) as total'))
            ->groupBy('tone')->pluck('total', 'tone');
 
        $dataToneBerita = [
            'Positif' => $toneRaw['Positif'] ?? 0,
            'Netral'  => $toneRaw['Netral'] ?? 0,
            'Negatif' => $toneRaw['Negatif'] ?? 0,
        ];
 
        $sifatRaw = (clone $baseBerita)->select('sifat_berita', DB::raw('count(*) as total'))
            ->groupBy('sifat_berita')->pluck('total', 'sifat_berita')->toArray();
 
        $dataSifatPemberitaan = [
            'Internal'  => $sifatRaw['Internal'] ?? 0,
            'Eksternal' => $sifatRaw['Eksternal'] ?? 0,
        ];
 
        $dataSpokesperson = (clone $baseBerita)->select('spokeperson', 'spokeperson_role', DB::raw('count(*) as total'))
            ->groupBy('spokeperson', 'spokeperson_role')
            ->orderBy('total', 'desc')->limit(5)->get();
 
        // --- TOP 5 MEDIA ---
$topMedia = (clone $baseBerita)
    ->select('nama_media', DB::raw('count(*) as total'))
    ->whereNotNull('nama_media')
    ->where('nama_media', '!=', '')
    ->where('nama_media', 'not like', '-')
    ->where(DB::raw('nama_media'), 'REGEXP', '[a-zA-Z0-9]') // Memastikan ada minimal 1 huruf atau angka
    ->groupBy('nama_media')
    ->orderBy('total', 'desc')
    ->limit(5)
    ->get();

// --- TOP 5 JURNALIS ---
$topJurnalis = (clone $baseBerita)
    ->select('reporter', DB::raw('count(*) as total'))
    ->whereNotNull('reporter')
    ->where('reporter', '!=', '')
    ->where('reporter', 'not like', '-')
    ->where(DB::raw('reporter'), 'REGEXP', '[a-zA-Z0-9]') // Memastikan ada minimal 1 huruf atau angka
    ->groupBy('reporter')
    ->orderBy('total', 'desc')
    ->limit(5)
    ->get();
 
        $daftarBerita = (clone $baseBerita)->orderBy('tanggal', 'desc')->get();
 
 
        // ========================================================
        // LEMPAR DATA KE VIEW
        // ========================================================
        return view('dashboard', compact(
            'tahunTerpilih',
            'tahunSosmedTerpilih',
            'tahunBeritaTerpilih',
            'daftarTahun',
            'pilarFilter',
            'bulanFilter',
            'kabupatenFilter',
            'desaFilter',
            'programFilter',
            'daftarProgramUnikCsr',
            'daftarKabupatenCsr',
            'daftarDesaCsr',
            'realisasiAnggaran',
            'anggaranPilar',
            'kegiatanBulanan',
            'realisasiPerPilar',
            'anggaranTriwulan',
            'modeWilayah',
            'dataWilayahChart',
            'dataAstaCitaChart',
            'cards',
            'trenPostinganBulanan',
            'interaksiTertinggi',
            'interaksiTerendah',
            'daftarPost',
            'dataToneBerita',
            'dataSifatPemberitaan',
            'dataSpokesperson',
            'topMedia',
            'topJurnalis',
            'daftarBerita',
            'bulanSosmed',
            'bulanBerita',
            'kategoriKontenTerpilih'
        ));
    }
}
