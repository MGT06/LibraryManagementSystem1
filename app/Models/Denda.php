<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denda extends Model
{
    protected $table = 'denda';
    
    protected $fillable = [
        'peminjaman_id',
        'jenis_denda',
        'jumlah_denda',
        'status_pembayaran',
        'keterangan'
    ];

    public function peminjaman()
    {
        return $this->belongsTo(PeminjamanModel::class, 'peminjaman_id');
    }
} 