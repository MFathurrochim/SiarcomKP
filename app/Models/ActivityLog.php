<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id_log';

    public $timestamps = false;
    protected $fillable = [
        'id_user',
        'aktivitas',
        'created_at',
    ];

    protected $casts = [
        'aktivitas' => 'array',
        'created_at' => 'datetime',
    ];

    // Relasi Balik ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}