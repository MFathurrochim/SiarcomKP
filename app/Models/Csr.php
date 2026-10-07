<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Csr extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'csr';

    /**
     * @var string
     */
    protected $primaryKey = 'id_csr';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'id_pilar',
        'nama_program',
        'tpb',
        'asta_cita',
        'cabang',
        'biaya_program',
        'biaya_realisasi',
        'realisasi_program',
        'bentuk_bantuan',
        'bulan_realisasi',
        'tahun_realisasi',
        'kabupaten',
        'desa',
        'status',
        'link_ig',
        'link_berita',
        'link_gdrive',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'biaya_program'   => 'integer',
        'biaya_realisasi' => 'integer',
        'tahun_realisasi' => 'integer',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /**
     * Relasi: Program CSR milik satu Pilar tertentu
     */
    public function pilar()
    {
        return $this->belongsTo(Pilar::class, 'id_pilar', 'id_pilar');
    }

    /**
     * Local Scope untuk memfilter berdasarkan tahun realisasi
     */
    public function scopeTahun(Builder $query, int|string $tahun): Builder
    {
        return $query->where('tahun_realisasi', $tahun);
    }

    /**
     * Local Scope untuk memfilter status program yang sudah selesai (done)
     */
    public function scopeDone(Builder $query): Builder
    {
        return $query->where('status', 'done');
    }
    public function pilarRelasi()
    {
        return $this->belongsTo(Pilar::class, 'id_pilar', 'id_pilar');
    }
}

