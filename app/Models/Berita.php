<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Berita extends Model
{
    protected $table = 'berita';
    protected $primaryKey = 'id_berita';

    protected $fillable = [
        'tanggal',
        'link_berita',
        'tone',
        'judul',
        'nama_media',
        'reporter',
        'spokeperson',
        'spokeperson_role',
        'topik',
        'sifat_berita',
        'last_refreshed_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'last_refreshed_at' => 'datetime',
    ];
}