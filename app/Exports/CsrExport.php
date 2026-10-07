<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CsrExport implements FromView, ShouldAutoSize, WithTitle
{
    protected string $bulanPilihan;
    protected string|int $tahunPilihan;

    // Bisa dioverride kalau nama GM / Dept Head beda tiap bulan
    protected string $namaGM;
    protected string $namaDeptHead;
    protected string $jabatanDeptHead;
    protected string $namaCabang;

    private array $listBulan = [
        'Januari' => 1, 'Februari' => 2, 'Maret' => 3, 'April' => 4,
        'Mei' => 5, 'Juni' => 6, 'Juli' => 7, 'Agustus' => 8,
        'September' => 9, 'Oktober' => 10, 'November' => 11, 'Desember' => 12
    ];

    // Urutan pilar HARUS sinkron dengan kode akun di bawah (mengikuti template Excel kantor)
    private array $urutanPilar = ['Sosial', 'Ekonomi', 'Lingkungan'];

    private array $kodeAkunPilar = [
        'Sosial'     => '51.211',
        'Ekonomi'    => '51.212',
        'Lingkungan' => '51.213',
    ];

    public function __construct(
        string $bulanPilihan,
        string|int $tahunPilihan,
        string $namaGM = 'MAYA DAMAYANTI',
        string $namaDeptHead = 'M. JOKO WAHYUDI',
        string $jabatanDeptHead = 'BRANCH COMM. & CSR DEPT. HEAD',
        string $namaCabang = 'CABANG BANDARA : SUPADIO PONTIANAK'
    ) {
        $this->bulanPilihan    = $bulanPilihan;
        $this->tahunPilihan    = $tahunPilihan;
        $this->namaGM          = $namaGM;
        $this->namaDeptHead    = $namaDeptHead;
        $this->jabatanDeptHead = $jabatanDeptHead;
        $this->namaCabang      = $namaCabang;
    }

    /**
     * Judul tab sheet Excel, mengikuti bulan & tahun yang dipilih.
     */
    public function title(): string
    {
        return strtoupper($this->bulanPilihan) . ' ' . $this->tahunPilihan;
    }

    public function view(): View
    {
        $tahun        = $this->tahunPilihan;
        $bulanDipilih = $this->bulanPilihan;

        // =====================================================================
        // BAGIAN 1 — TABEL KIRI: Realisasi bulan berjalan (s/d bulan lalu, bulan
        // ini, s/d bulan ini) per pilar. Query lama tetap dipertahankan.
        // =====================================================================
        $keysBulan        = array_keys($this->listBulan);
        $indexBulanDipilih = array_search($bulanDipilih, $keysBulan);
        $bulanLalu         = array_slice($keysBulan, 0, $indexBulanDipilih);

        $dataBulanLalu = DB::table('csr')
            ->leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahun)
            ->whereIn('csr.bulan_realisasi', $bulanLalu)
            ->select('pilar.nama_pilar', DB::raw('SUM(csr.biaya_realisasi) as total'))
            ->groupBy('pilar.nama_pilar')
            ->pluck('total', 'nama_pilar');

        $dataBulanIni = DB::table('csr')
            ->leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahun)
            ->where('csr.bulan_realisasi', $bulanDipilih)
            ->select('pilar.nama_pilar', DB::raw('SUM(csr.biaya_realisasi) as total'))
            ->groupBy('pilar.nama_pilar')
            ->pluck('total', 'nama_pilar');

        $ringkasanPilar = [];
        $totalPenyaluranLalu = 0;
        $totalPenyaluranIni  = 0;
        $totalPenyaluranSmp  = 0;

        foreach ($this->urutanPilar as $pilar) {
            $smpBulanLalu       = (float) ($dataBulanLalu[$pilar] ?? 0);
            $realisasiBulanIni  = (float) ($dataBulanIni[$pilar] ?? 0);
            $smpBulanIni        = $smpBulanLalu + $realisasiBulanIni;

            $ringkasanPilar[$pilar] = [
                'kode'           => $this->kodeAkunPilar[$pilar],
                'smp_bulan_lalu' => $smpBulanLalu,
                'bulan_ini'      => $realisasiBulanIni,
                'smp_bulan_ini'  => $smpBulanIni,
            ];

            $totalPenyaluranLalu += $smpBulanLalu;
            $totalPenyaluranIni  += $realisasiBulanIni;
            $totalPenyaluranSmp  += $smpBulanIni;
        }

        // =====================================================================
        // BAGIAN 2 — TABEL KANAN: Rekap 1 tahun penuh (Januari s/d Desember)
        // per pilar, buat tabel "DANA TERSEDIA / PENYALURAN DANA" sebelah kanan.
        // =====================================================================
        $dataPerBulanPerPilar = DB::table('csr')
            ->leftJoin('pilar', 'csr.id_pilar', '=', 'pilar.id_pilar')
            ->where('csr.tahun_realisasi', $tahun)
            ->select(
                'pilar.nama_pilar',
                'csr.bulan_realisasi',
                DB::raw('SUM(csr.biaya_realisasi) as total')
            )
            ->groupBy('pilar.nama_pilar', 'csr.bulan_realisasi')
            ->get();

        // Susun jadi: $tahunanPilar['Sosial']['Januari'] = 12345
        $tahunanPilar = [];
        foreach ($this->urutanPilar as $pilar) {
            foreach ($keysBulan as $bln) {
                $tahunanPilar[$pilar][$bln] = 0;
            }
        }
        foreach ($dataPerBulanPerPilar as $row) {
            if (isset($tahunanPilar[$row->nama_pilar][$row->bulan_realisasi])) {
                $tahunanPilar[$row->nama_pilar][$row->bulan_realisasi] = (float) $row->total;
            }
        }

        // Total per bulan (baris "Jumlah Penyaluran Dana (I)" tabel kanan)
        $totalPerBulan = [];
        foreach ($keysBulan as $bln) {
            $totalPerBulan[$bln] = 0;
            foreach ($this->urutanPilar as $pilar) {
                $totalPerBulan[$bln] += $tahunanPilar[$pilar][$bln];
            }
        }
        $totalSetahun = array_sum($totalPerBulan);

        // Total per baris (kolom AA "JUMLAH S/D BULAN INI") untuk tiap pilar
        $totalPerPilarSetahun = [];
        foreach ($this->urutanPilar as $pilar) {
            $totalPerPilarSetahun[$pilar] = array_sum($tahunanPilar[$pilar]);
        }

        // 3. Kirim semua variabel ke Blade export
        return view('pages.csr.export', [
            'bulanPilihan'          => strtoupper($bulanDipilih),
            'tahunPilihan'          => $tahun,
            'listBulan'             => $keysBulan,
            'ringkasanPilar'        => $ringkasanPilar,
            'totalPenyaluranLalu'   => $totalPenyaluranLalu,
            'totalPenyaluranIni'    => $totalPenyaluranIni,
            'totalPenyaluranSmp'    => $totalPenyaluranSmp,
            'tahunanPilar'          => $tahunanPilar,
            'totalPerBulan'         => $totalPerBulan,
            'totalSetahun'          => $totalSetahun,
            'totalPerPilarSetahun'  => $totalPerPilarSetahun,
            'kodeAkunPilar'         => $this->kodeAkunPilar,
            'namaGM'                => $this->namaGM,
            'namaDeptHead'          => $this->namaDeptHead,
            'jabatanDeptHead'       => $this->jabatanDeptHead,
            'namaCabang'            => $this->namaCabang,
            'tanggalTtd'            => Carbon::now()->translatedFormat('d F Y'),
        ]);
    }
}