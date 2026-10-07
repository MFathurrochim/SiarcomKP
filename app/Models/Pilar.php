<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pilar extends Model
{
    use HasFactory;

    protected $table = 'pilar';
    protected $primaryKey = 'id_pilar';

    protected $fillable = [
        'nama_pilar',
        'tahun_pilar',
        'anggaran_pilar',
    ];

    /**
     * Relasi: Satu Pilar memiliki banyak Program CSR
     */
    public function csr()
    {
        return $this->hasMany(Csr::class, 'id_pilar', 'id_pilar');
    }
}