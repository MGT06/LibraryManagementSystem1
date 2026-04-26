<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamModel extends Model
{
    use HasFactory;
    protected $table = 'peminjam';
    protected $guarded = [];
    
    protected $fillable = [
        'nama',
        'nis',
        'kelas'
    ];
}
