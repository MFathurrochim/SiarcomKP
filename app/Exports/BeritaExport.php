<?php

namespace App\Exports;

use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BeritaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected Request $request;
    private $rowNumber = 0;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function collection()
    {
        $tahun = $this->request->get('tahun', date('Y'));
        $bulan = $this->request->get('bulan');
        $tone = $this->request->get('tone');
        $topik = $this->request->get('topik', $this->request->get('kategori_tabel'));
        $sifat = $this->request->get('sifat_berita');
        $search = $this->request->get('search');
        
        $sortBy = $this->request->get('sort_by', 'tanggal');
        $sortOrder = $this->request->get('sort_order', 'desc');

        // Query Dasar Berita
        $query = Berita::query();

        // Terapkan Filter Tahun
        if ($tahun && $tahun !== 'all') {
            $query->whereYear('tanggal', $tahun);
        }

        // Terapkan Filter Bulan
        if ($bulan && $bulan !== 'all') {
            $query->whereMonth('tanggal', $bulan);
        }

        // Terapkan Filter Tone
        if ($tone && $tone !== 'all') {
            $query->where('tone', $tone);
        }

        // Terapkan Filter Topik / Kategori
        if ($topik && $topik !== 'all') {
            $query->where('topik', $topik);
        }

        // Terapkan Filter Sifat Berita
        if ($sifat && $sifat !== 'all') {
            $query->where('sifat_berita', $sifat);
        }

        // Terapkan Logika Search (Sama persis dengan di Controller)
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

            $query->where(function ($q) use ($search, $matchedBulan) {
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

        return $query->orderBy($sortBy, $sortOrder)->get();
    }

    public function map($berita): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($berita->tanggal)->format('d/m/Y'),
            $berita->link_berita,
            $berita->tone,
            $berita->judul,
            $berita->nama_media,
            $berita->reporter,
            $berita->spokeperson,
            $berita->spokeperson_role,
            $berita->topik,
            $berita->sifat_berita,
        ];
    }

    public function headings(): array
    {
        return [
            'no',
            'Tanggal',
            'Link',
            'Tone',
            'Title',
            'Media Name',
            'Reporter',
            'Spokeperson',
            'Spokeperson Role',
            'TOPIK',
            'INT/EXT'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $cellRange = "A1:{$highestColumn}{$highestRow}";

        return [
            // Styling Header Baris 1 (Background putih, teks bold, center)
            1 => [
                'font' => [
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            
            // Memberikan Border/Garis Hitam Horizontal & Vertikal ke seluruh tabel (Header & Isi)
            $cellRange => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'], // Warna hitam pekat
                    ],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Generator nama file Excel dinamis berdasarkan filter berita yang aktif
     */
    public function getFilename(): string
    {
        $tahun = $this->request->get('tahun', date('Y'));
        $namaFile = "Laporan_Media_Monitoring_{$tahun}";

        $bulan = $this->request->get('bulan');
        if ($bulan && $bulan !== 'all') {
            if (is_numeric($bulan)) {
                $namaBulan = \Carbon\Carbon::create()->month((int)$bulan)->translatedFormat('F');
                $namaFile .= "_" . $namaBulan;
            } else {
                $namaFile .= "_" . $bulan;
            }
        }

        $tone = $this->request->get('tone');
        if ($tone && $tone !== 'all') {
            $namaFile .= "_Tone_" . str_replace(' ', '_', $tone);
        }

        $topik = $this->request->get('topik', $this->request->get('kategori_tabel'));
        if ($topik && $topik !== 'all') {
            $namaFile .= "_Topik_" . str_replace(' ', '_', $topik);
        }

        $sifat = $this->request->get('sifat_berita');
        if ($sifat && $sifat !== 'all') {
            $namaFile .= "_" . str_replace(' ', '_', $sifat);
        }

        return $namaFile . ".xlsx";
    }
}