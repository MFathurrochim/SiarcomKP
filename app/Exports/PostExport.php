<?php

namespace App\Exports;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PostExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected Request $request;
    private $rowNumber = 0;

    public function __construct(Request $request) {
        $this->request = $request;
    }

    public function collection()
    {
        $tahunTerpilih = (int)$this->request->get('tahun', date('Y'));
        
        $bulanSosmed            = $this->request->get('bulan_sosmed', $this->request->get('bulan', 'all'));
        $kategoriKontenTerpilih = $this->request->get('kategori_konten', 'all');
        $tipeKontenTerpilih     = $this->request->get('tipe_konten', 'Keseluruhan');
        $search                 = $this->request->get('search');

        $rumusLikeComment       = '(likes + comments)';
        $rumusInteraksiLengkap  = '(view + likes + comments + share + retweet)';

        // Query mengikuti filter yang sama persis seperti di PostController@index
        $queryTabel = DB::table('post')
            ->select('*', 
                DB::raw("$rumusLikeComment as total_like_comment"),
                DB::raw("$rumusInteraksiLengkap as total_skor")
            )
            ->whereYear('tanggal', $tahunTerpilih);

        if (!empty($search)) {
            $queryTabel->where(function($q) use ($search) {
                $q->where('topik', 'like', '%' . $search . '%')
                  ->orWhere('sosial_media', 'like', '%' . $search . '%')
                  ->orWhere('tipe_konten', 'like', '%' . $search . '%');
            });
        }

        if ($tipeKontenTerpilih !== 'Keseluruhan' && $tipeKontenTerpilih !== 'all' && !empty($tipeKontenTerpilih)) {
            $queryTabel->where('tipe_konten', $tipeKontenTerpilih);
        }

        if ($kategoriKontenTerpilih !== 'all' && !empty($kategoriKontenTerpilih)) {
            $queryTabel->where('kategori_konten', $kategoriKontenTerpilih);
        }

        if ($bulanSosmed !== 'all' && !empty($bulanSosmed)) {
            $queryTabel->whereMonth('tanggal', $bulanSosmed);
        }

        return $queryTabel->orderBy('tanggal', 'desc')->get();
    }

    public function map($post): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($post->tanggal)->format('d/m/Y'),
            \Carbon\Carbon::parse($post->tanggal)->translatedFormat('F'),
            $post->topik,
            $post->kategori_konten,
            $post->tipe_konten,
            $post->link_post,
            $post->sosial_media,
            $post->view,
            $post->likes,
            $post->comments,
            $post->share,
            $post->retweet,
            0 
        ];
    }

    public function headings(): array
    {
        return [
            'NO',
            'TANGGAL',
            'BULAN',
            'TOPIK/JUDUL',
            'KATEGORI',
            'TIPE KONTEN',
            'LINK/EVIDENCE',
            'SOSIAL MEDIA',
            'Viewers',
            'Likes',
            'Comments',
            'Share',
            'Retweet',
            'Saved'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $cellRange = "A1:{$highestColumn}{$highestRow}";

        return [
            // Styling Header Baris 1 (Background Putih Polos & Teks Tebal Hitam)
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['argb' => '000000']
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFFFFF'] // Warna putih polos
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            
            // Memberikan Border/Garis Hitam (Horizontal & Vertikal) ke seluruh data
            $cellRange => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '000000'], // Warna garis hitam penuh
                    ],
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Generator nama file Excel dinamis berdasarkan filter yang aktif
     */
    public function getFilename(): string
    {
        $tahun = $this->request->get('tahun', date('Y'));
        $namaFile = "Laporan_Performa_Sosmed_{$tahun}";

        $bulanSosmed = $this->request->get('bulan_sosmed', $this->request->get('bulan', 'all'));
        if ($bulanSosmed !== 'all' && !empty($bulanSosmed)) {
            if (is_numeric($bulanSosmed)) {
                $namaBulan = \Carbon\Carbon::create()->month((int)$bulanSosmed)->translatedFormat('F');
                $namaFile .= "_" . $namaBulan;
            } else {
                $namaFile .= "_" . $bulanSosmed;
            }
        }

        $tipeKonten = $this->request->get('tipe_konten', 'Keseluruhan');
        if ($tipeKonten !== 'Keseluruhan' && $tipeKonten !== 'all' && !empty($tipeKonten)) {
            $namaFile .= "_" . str_replace(' ', '_', $tipeKonten);
        }

        $kategoriKonten = $this->request->get('kategori_konten', 'all');
        if ($kategoriKonten !== 'all' && !empty($kategoriKonten)) {
            $namaFile .= "_" . str_replace(' ', '_', $kategoriKonten);
        }

        return $namaFile . ".xlsx";
    }
}