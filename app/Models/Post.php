<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table = 'post';
    protected $primaryKey = 'id_post';

    protected $fillable = [
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
        'last_refreshed_at',
    ];

    // Mengubah kolom tanggal dan last_refreshed_at menjadi objek Carbon (Datetime) secara otomatis
    protected $casts = [
        'tanggal' => 'date',
        'last_refreshed_at' => 'datetime',
    ];
}