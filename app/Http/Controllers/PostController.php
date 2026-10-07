<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Exports\PostExport;
use Maatwebsite\Excel\Facades\Excel;

class PostController extends Controller
{
    /**
     * Tampilan Utama Postingan 
     */
    public function index(Request $request)
    {
        // ========================================================
        // 1. FILTER GLOBAL MASTER: TAHUN & GENERATE DAFTAR TAHUN
        // ========================================================
        $tahunTerpilih = (int)$request->get('tahun', date('Y'));

        // Ambil tahun terkecil & terbesar dari DB
        $minTahunDB = DB::table('post')->whereNotNull('tanggal')->min(DB::raw('YEAR(tanggal)')) ?? date('Y');
        $maxTahunDB = DB::table('post')->whereNotNull('tanggal')->max(DB::raw('YEAR(tanggal)')) ?? date('Y');

        // Pastikan batas atas mencakup minimal (Tahun Sekarang + 1) untuk mengomodir pilihan tahun baru/mendatang
        $tahunSekarangPlusSatu = (int)date('Y') + 1;
        $tahunTerakhir = max((int)$maxTahunDB, $tahunSekarangPlusSatu, $tahunTerpilih);
        $tahunAwal     = min((int)$minTahunDB, $tahunTerpilih);

        // Generate deret tahun dari $tahunTerakhir turun ke $tahunAwal
        $daftarTahun = collect(range($tahunTerakhir, $tahunAwal))->sortDesc()->values();

        // ========================================================
        // 2. FILTER GLOBAL KATEGORI, TIPE, BULAN, & SEARCH
        // ========================================================
        $bulanSosmed            = $request->get('bulan_sosmed', $request->get('bulan', 'all'));
        $kategoriKontenTerpilih = $request->get('kategori_konten', 'all');
        $tipeKontenTerpilih     = $request->get('tipe_konten', 'Keseluruhan');
        $search                 = $request->get('search');

        $sortBy    = $request->get('sort_by', 'tanggal'); 
        $sortOrder = strtolower($request->get('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc'; 

        $rumusLikeComment      = '(likes + comments)';
        $rumusInteraksiLengkap = '(view + likes + comments + share + retweet)';

        // ========================================================
        // 3. DATA UTAMA TABEL DENGAN FILTER GLOBAL LENGKAP
        // ========================================================
        $queryTabel = DB::table('post')
            ->select('*', 
                DB::raw("$rumusLikeComment as total_like_comment"),
                DB::raw("$rumusInteraksiLengkap as total_skor")
            )
            ->whereYear('tanggal', $tahunTerpilih);

        if ($request->filled('search')) {
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

        // Sorting Logika Multikolom
        switch ($sortBy) {
            case 'bulan':
                $queryTabel->orderBy(DB::raw('MONTH(tanggal)'), $sortOrder)
                           ->orderBy(DB::raw('DAY(tanggal)'), $sortOrder);
                break;

            case 'hari':
            case 'tanggal_saja':
                $queryTabel->orderBy(DB::raw('DAY(tanggal)'), $sortOrder);
                break;

            case 'total_like_comment':
                $queryTabel->orderBy('total_like_comment', $sortOrder);
                break;

            case 'total_skor':
                $queryTabel->orderBy('total_skor', $sortOrder);
                break;

            case 'likes':
                $queryTabel->orderBy('likes', $sortOrder);
                break;

            case 'comments':
                $queryTabel->orderBy('comments', $sortOrder);
                break;

            case 'share':
            case 'retweet':
            case 'view':
                $queryTabel->orderBy($sortBy, $sortOrder);
                break;

            default:
                $queryTabel->orderBy('tanggal', $sortOrder);
                break;
        }

        $daftarPost = $queryTabel->get();

        // ========================================================
        // 4. METRIK INFO CARDS (Merespons Semua Filter Global)
        // ========================================================
        $baseCardsQuery = DB::table('post')->whereYear('tanggal', $tahunTerpilih);

        if ($bulanSosmed !== 'all' && !empty($bulanSosmed)) {
            $baseCardsQuery->whereMonth('tanggal', $bulanSosmed);
        }
        if ($kategoriKontenTerpilih !== 'all' && !empty($kategoriKontenTerpilih)) {
            $baseCardsQuery->where('kategori_konten', $kategoriKontenTerpilih);
        }
        if ($tipeKontenTerpilih !== 'Keseluruhan' && $tipeKontenTerpilih !== 'all' && !empty($tipeKontenTerpilih)) {
            $baseCardsQuery->where('tipe_konten', $tipeKontenTerpilih);
        }
        if ($request->filled('search')) {
    $baseCardsQuery->where(function($q) use ($search) {
        $q->where('topik', 'like', '%' . $search . '%')
          ->orWhere('sosial_media', 'like', '%' . $search . '%')
          ->orWhere('tipe_konten', 'like', '%' . $search . '%')
          ->orWhere('kategori_konten', 'like', '%' . $search . '%')
          ->orWhere('tanggal', 'like', '%' . $search . '%');
    });
}

        $cards = new \stdClass();
        $cards->total_postingan = (clone $baseCardsQuery)->count();
        $cards->total_story     = (clone $baseCardsQuery)->where('tipe_konten', 'Story')->count();
        $cards->total_feed_reels = (clone $baseCardsQuery)->where('tipe_konten', 'Feed/Reels')->count();
        $cards->total_collab    = (clone $baseCardsQuery)->where('kategori_konten', 'Collab Content')->count();
        $cards->total_owned     = (clone $baseCardsQuery)->where('kategori_konten', 'Owned Production')->count();
        $cards->total_shared    = (clone $baseCardsQuery)->where('kategori_konten', 'Shared Content')->count();

        // ========================================================
        // 5. ANALISIS INTERAKSI TERTINGGI & TERENDAH
        // ========================================================
        $queryInteraksi = clone $baseCardsQuery;

        $interaksiTertinggi = (clone $queryInteraksi)
            ->select('topik', 'likes', 'comments', 'view', 'share', 'retweet', DB::raw("$rumusInteraksiLengkap as total_skor"))
            ->orderBy('total_skor', 'desc')
            ->limit(5)
            ->get();

        $interaksiTerendah = (clone $queryInteraksi)
            ->select('topik', 'likes', 'comments', 'view', 'share', 'retweet', DB::raw("$rumusInteraksiLengkap as total_skor"))
            ->orderBy('total_skor', 'asc')
            ->limit(5)
            ->get();

        // ========================================================
        // 6. TREN POSTINGAN BULANAN (Merespons Tahun, Kategori, & Tipe)
        // ========================================================
        $trenPostinganRaw = DB::table('post')
            ->whereYear('tanggal', $tahunTerpilih)
            ->select(DB::raw('MONTH(tanggal) as bulan'), DB::raw('COUNT(*) as total'));

        if ($tipeKontenTerpilih !== 'Keseluruhan' && $tipeKontenTerpilih !== 'all' && !empty($tipeKontenTerpilih)) {
            $trenPostinganRaw->where('tipe_konten', $tipeKontenTerpilih);
        }
        if ($kategoriKontenTerpilih !== 'all' && !empty($kategoriKontenTerpilih)) {
            $trenPostinganRaw->where('kategori_konten', $kategoriKontenTerpilih);
        }

        $trenPostinganRaw = $trenPostinganRaw->groupBy(DB::raw('MONTH(tanggal)'))->pluck('total', 'bulan')->toArray();

        $trenPostinganBulanan = [];
        for ($i = 1; $i <= 12; $i++) {
            $trenPostinganBulanan[] = $trenPostinganRaw[$i] ?? 0;
        }

        // ========================================================
        // 7. RESPON AJAX (LIVE SEARCH) — dipanggil dari inputSearch, TIDAK reload halaman
        // Render ulang FILE TABEL YANG SAMA (bukan file partial terpisah).
        // JS di sisi browser yang nanti ambil cuma bagian <tbody id="tableBodyPost">
        // dari HTML ini pakai DOMParser.
        //
        // GANTI 'pages.post.partials.table' DI BAWAH INI sesuai nama/path file
        // tabel kamu yang sebenarnya (yang di-include dari index.blade.php).
        // ========================================================
        if ($request->ajax() || $request->wantsJson()) {
            $html = view('pages.post.partials.table', compact(
                'daftarPost',
                'tahunTerpilih',
                'bulanSosmed',
                'kategoriKontenTerpilih',
                'tipeKontenTerpilih',
                'sortBy',
                'sortOrder'
            ))->render();

            return response()->json(['html' => $html]);
        }

        // ========================================================
        // 8. LEMPAR DATA KE VIEW BLADE (request normal / full page load)
        // ========================================================
        return view('pages.post.index', compact(
            'tahunTerpilih', 
            'daftarTahun', 
            'bulanSosmed',
            'kategoriKontenTerpilih',
            'tipeKontenTerpilih',
            'cards', 
            'trenPostinganBulanan',
            'interaksiTertinggi', 
            'interaksiTerendah', 
            'daftarPost',
            'sortBy',
            'sortOrder'
        ));
    }

    /**
     * AJAX API untuk Request Chart Bulanan secara Real-time
     */
    public function apiGrafikBulanan(Request $request)
    {
        $tahun    = $request->get('tahun', date('Y'));
        $kategori = $request->get('kategori', $request->get('kategori_konten')); 
        $tipe     = $request->get('tipe_konten'); 

        $query = DB::table('post')
            ->whereYear('tanggal', $tahun)
            ->select(DB::raw('MONTH(tanggal) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy(DB::raw('MONTH(tanggal)'));

        if (!empty($kategori) && $kategori !== 'all') {
            $query->where('kategori_konten', $kategori);
        }

        if (!empty($tipe) && $tipe !== 'all' && $tipe !== 'Keseluruhan') {
            $query->where('tipe_konten', $tipe);
        }

        $rawData = $query->pluck('total', 'bulan')->toArray();

        $chartData = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartData[] = $rawData[$m] ?? 0;
        }

        return response()->json($chartData);
    }

    /**
     * Refresh Data Metrik Instagram (Scraper Simulation)
     */
    public function regenerateMetrics(Request $request)
    {
        $tahun = $request->get('tahun', date('Y'));
        $daftarLink = DB::table('post')->whereYear('tanggal', $tahun)->get();

        if ($daftarLink->isEmpty()) {
            return redirect()->back()->with('info', 'Tidak ada postingan yang bisa di-regenerate pada tahun ini.');
        }

        $sukses = 0; $gagal = 0;

        foreach ($daftarLink as $item) {
            if ($item->sosial_media === 'Instagram' && !empty($item->link_post)) {
                try {
                    $response = Http::withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
                    ])->timeout(5)->get($item->link_post);

                    if ($response->successful()) {
                        $html = $response->body();
                        $likes = $item->likes;
                        $comments = $item->comments;
                        $view = $item->view;

                        if (preg_match('/<meta content="([\d,.]+) Likes, ([\d,.]+) Comments/i', $html, $matches)) {
                            $likes = (int)str_replace([',', '.'], '', $matches[1]);
                            $comments = (int)str_replace([',', '.'], '', $matches[2]);
                        }

                        if (preg_match('/([\d,.]+) views/i', $html, $viewMatches)) {
                            $view = (int)str_replace([',', '.'], '', $viewMatches[1]);
                        }

                        DB::table('post')->where('id_post', $item->id_post)->update([
                            'likes' => $likes,
                            'comments' => $comments,
                            'view' => $view,
                            'last_refreshed_at' => now(),
                            'updated_at' => now()
                        ]);
                        $sukses++;
                        
                        usleep(2000000); 
                    } else { $gagal++; }
                } catch (\Exception $e) { $gagal++; }
            } else {
                $gagal++; 
            }
        }

        return redirect()->back()->with('success', "Regenerate data selesai! {$sukses} konten berhasil disinkronisasi.");
    }

    public function scrapeInstagramJson(Request $request)
    {
        $link = $request->get('link_post');

        if (empty($link)) {
            return response()->json(['success' => false, 'message' => 'Link tidak boleh kosong.'], 400);
        }

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            ])->timeout(7)->get($link);

            if ($response->successful()) {
                $html = $response->body();
                $likes = 0;
                $comments = 0;
                $view = 0;

                if (preg_match('/<meta content="([\d,.]+) Likes, ([\d,.]+) Comments/i', $html, $matches)) {
                    $likes = (int)str_replace([',', '.'], '', $matches[1]);
                    $comments = (int)str_replace([',', '.'], '', $matches[2]);
                }

                if (preg_match('/([\d,.]+) views/i', $html, $viewMatches)) {
                    $view = (int)str_replace([',', '.'], '', $viewMatches[1]);
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'likes' => $likes,
                        'comments' => $comments,
                        'view' => $view
                    ]
                ]);
            }

            return response()->json(['success' => false, 'message' => 'Gagal mengambil data dari Instagram. Pastikan post tidak diprivat.'], 422);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()], 500);
        }
    }


    /**
     * PROSES CRUD: STORE DATA POSTINGAN BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'topik' => 'required|string|max:255',
            'kategori_konten' => 'required|in:Collab Content,Owned Production,Shared Content',
            'tipe_konten' => 'required|in:Feed/Reels,Story',
            'sosial_media' => 'required|in:Instagram,Facebook,Twitter/X,TikTok',
            'link_post' => 'nullable|url',
        ]);

        try {
            $user = auth()->user(); 

            $newData = [
                'tanggal' => $request->tanggal,
                'topik' => $request->topik,
                'kategori_konten' => $request->kategori_konten,
                'tipe_konten' => $request->tipe_konten,
                'link_post' => $request->link_post,
                'sosial_media' => $request->sosial_media,
                'view' => $request->view ?? 0,
                'likes' => $request->likes ?? 0,
                'comments' => $request->comments ?? 0,
                'share' => $request->share ?? 0,
                'retweet' => $request->retweet ?? 0,
                'created_at' => now(),
                'updated_at' => now()
            ];

            DB::table('post')->insert($newData);

            // LOG MENGGUNAKAN GAYA CSR (CREATE)
            \App\Models\ActivityLog::create([
                'id_user'    => $user->id_user ?? null, 
                'nama_user'  => $user->username ?? 'System',
                'aktivitas'  => [
                    'modul'       => 'SOSMED', 
                    'nama_user'   => $user->username ?? 'System', 
                    'aksi'        => 'CREATE',
                    'deskripsi'   => "Menambahkan rekap postingan baru berjudul : {$request->topik}",
                    'data_baru'   => (array) $newData 
                ],
                'created_at' => now() 
            ]);

            return redirect()->back()->with('success', 'Data postingan berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * PROSES CRUD: UPDATE DATA POSTINGAN
     */
    public function edit(int $id)
    {
        // Cari data postingan berdasarkan primary key id_post
        $post = \DB::table('post')->where('id_post', $id)->first(); 
        
        if (!$post) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        // Return view partials edit secara terisolasi agar bisa dibaca AJAX
        return view('pages.post.partials.edit', compact('post'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'tanggal'         => 'required|date',
            'topik'           => 'required|string|max:255',
            'kategori_konten' => 'required|in:Collab Content,Owned Production,Shared Content',
            'tipe_konten'     => 'required|in:Feed/Reels,Story',
            'sosial_media'    => 'required|in:Instagram,Facebook,Twitter/X,TikTok',
            'link_post'       => 'nullable|url',
            'view'            => 'nullable|integer|min:0',
            'likes'           => 'nullable|integer|min:0',
            'comments'        => 'nullable|integer|min:0',
            'share'           => 'nullable|integer|min:0',
            'retweet'         => 'nullable|integer|min:0',
        ]);

        try {
            $user = auth()->user(); // Mengambil user yang sedang login

            $dataLamaObj = DB::table('post')->where('id_post', $id)->first();

            if (!$dataLamaObj) {
                return redirect()->back()->with('error', 'Data postingan tidak ditemukan!');
            }

            $dataLama = (array) $dataLamaObj;

            $updateData = [
                'tanggal'         => $request->tanggal,
                'topik'           => $request->topik,
                'kategori_konten' => $request->kategori_konten,
                'tipe_konten'     => $request->tipe_konten,
                'link_post'       => $request->link_post,
                'sosial_media'    => $request->sosial_media,
                'view'            => $request->view ?? 0,
                'likes'           => $request->likes ?? 0,
                'comments'        => $request->comments ?? 0,
                'share'           => $request->share ?? 0,
                'retweet'         => $request->retweet ?? 0,
                'updated_at'      => now()
            ];

            DB::table('post')->where('id_post', $id)->update($updateData);

            // LOG MENGGUNAKAN GAYA CSR (UPDATE)
            \App\Models\ActivityLog::create([
                'id_user'    => $user->id_user ?? null, 
                'nama_user'  => $user->username ?? 'System',
                'aktivitas'  => [
                    'modul'     => 'SOSMED', 
                    'nama_user' => $user->username ?? 'System', 
                    'aksi'      => 'UPDATE',
                    'deskripsi' => "Mengubah data postingan Sosmed dengan judul : {$request->topik}, Link: {$request->link_post}",
                    'data_lama' => $dataLama, 
                    'data_baru' => $updateData
                ],
                'created_at' => now() 
            ]);

            return redirect()->back()->with('success', 'Seluruh data postingan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * PROSES INLINE EDIT: UPDATE SATU KOLOM SAJA 
     */
    public function inlineUpdate(Request $request, int $id)
    {
        // Validasi dasar: id harus positif
        if ($id <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'ID data tidak valid.'
            ], 422);
        }

        $column = $request->input('column');
        $value  = $request->input('value');

        $allowedColumns = [
            'tanggal',
            'topik',
            'kategori_konten',
            'tipe_konten',
            'link_post',
            'sosial_media',
            'view',
            'likes',
            'comments',
            'share',
            'retweet',
        ];

        if (!in_array($column, $allowedColumns, true)) {
            return response()->json([
                'success' => false,
                'message' => 'Kolom tidak diizinkan untuk diedit.'
            ], 422);
        }

        // Sanitasi awal untuk field teks (mencegah stored XSS di lapisan tambahan)
        if (in_array($column, ['topik', 'link_post'], true) && is_string($value)) {
            $value = trim(strip_tags($value));
        }

        // Validasi per-kolom, disesuaikan tipe data & enum di skema tabel `post`
        $rules = [
            'tanggal'         => 'required|date',
            'topik'           => 'required|string|max:255',
            'kategori_konten' => 'required|in:Collab Content,Owned Production,Shared Content',
            'tipe_konten'     => 'required|in:Feed/Reels,Story',
            'link_post'       => 'nullable|url|max:255',
            'sosial_media'    => 'required|in:Instagram,Facebook,Twitter/X,TikTok',
            'view'            => 'nullable|integer|min:0',
            'likes'           => 'nullable|integer|min:0',
            'comments'        => 'nullable|integer|min:0',
            'share'           => 'nullable|integer|min:0',
            'retweet'         => 'nullable|integer|min:0',
        ];

        $validator = Validator::make(
            [$column => $value],
            [$column => $rules[$column]]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first($column)
            ], 422);
        }

        try {
            $user = auth()->user();

            $post = DB::table('post')->where('id_post', $id)->first();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data postingan tidak ditemukan.'
                ], 404);
            }

            $dataLama = (array) $post;
            $nilaiLama = $dataLama[$column] ?? null;

            // Kolom numerik (view, likes, comments, share, retweet) default 0 kalau kosong
            $numericColumns = ['view', 'likes', 'comments', 'share', 'retweet'];
            if (in_array($column, $numericColumns, true)) {
                $value = $value === '' || $value === null ? 0 : (int) $value;
            }

            DB::transaction(function () use ($column, $value, $id, $user, $post, $nilaiLama) {
                DB::table('post')->where('id_post', $id)->update([
                    $column      => $value,
                    'updated_at' => now()
                ]);

                // LOG MENGGUNAKAN GAYA CSR (UPDATE - INLINE)
                \App\Models\ActivityLog::create([
                    'id_user'   => $user->id_user ?? null,
                    'nama_user' => $user->username ?? 'System',
                    'aktivitas' => [
                        'modul'     => 'SOSMED',
                        'nama_user' => $user->username ?? 'System',
                        'aksi'      => 'UPDATE',
                        'deskripsi' => "Update inline kolom '{$column}' pada postingan: {$post->topik}",
                        'data_lama' => [$column => $nilaiLama],
                        'data_baru' => [$column => $value],
                    ],
                    'created_at' => now()
                ]);
            });

            return response()->json([
                'success' => true,
                'value'   => $value
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * PROSES CRUD: HAPUS DATA POSTINGAN
     */
    public function destroy(int $id)
    {
        try {
            $user = auth()->user(); // Mengambil user yang sedang login

            // Ambil data sebelum dihapus agar bisa dijadikan data_lama di log
            $dataYangDihapusObj = DB::table('post')->where('id_post', $id)->first();

            if (!$dataYangDihapusObj) {
                return redirect()->back()->with('error', 'Data postingan tidak ditemukan atau sudah dihapus.');
            }

            $dataYangDihapus = (array) $dataYangDihapusObj;

            DB::table('post')->where('id_post', $id)->delete();

            // LOG MENGGUNAKAN GAYA CSR (DELETE)
            \App\Models\ActivityLog::create([
                'id_user'    => $user->id_user ?? null, 
                'nama_user'  => $user->username ?? 'System',
                'aktivitas'  => [
                    'modul'       => 'SOSMED', 
                    'nama_user'   => $user->username ?? 'System', 
                    'aksi'        => 'DELETE',
                    'deskripsi'   => "Menghapus data postingan yang berjudul : " . ($dataYangDihapus['topik'] ?? '-'),
                    'data_lama'   => $dataYangDihapus, 
                    'data_baru'   => null
                ],
                'created_at' => now() 
            ]);

            return redirect()->back()->with('success', 'Data postingan berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
    
    /**
     * IMPORT DATA DARI PASTE SPREADSHEET
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
            if (!empty($row['tanggal']) && !empty($row['topik'])) {
                \App\Models\Post::create([
                    'tanggal'           => $row['tanggal'],
                    'topik'             => $row['topik'],
                    'kategori_konten'   => $row['kategori_konten'] ?? 'Owned Production',
                    'tipe_konten'       => $row['tipe_konten'] ?? 'Feed/Reels',
                    'link_post'         => $row['link_post'] ?? null,
                    'sosial_media'      => $row['sosial_media'] ?? 'Instagram',
                    'view'              => $row['view'] ?? 0,
                    'likes'             => $row['likes'] ?? 0,
                    'comments'          => $row['comments'] ?? 0,
                    'share'             => $row['share'] ?? 0,
                    'retweet'           => $row['retweet'] ?? 0,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);
                $successCount++;
            }
        }

        return redirect()->back()->with('success', "Berhasil mengimpor {$successCount} data postingan baru.");
    }
    /**
     * EXPORT DATA KE EXCEL
     */
    public function exportExcel(Request $request) 
{
    $export = new PostExport($request);
    return Excel::download($export, $export->getFilename());
}
}