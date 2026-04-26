<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PeminjamanModel extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';

    protected $fillable = [
        'nama_peminjam',
        'nis',
        'judul_buku',
        'tanggal_pinjam',
        'tanggal_kembali',
        'status',
        'lama_pinjam',
        'status_perpanjangan',
        'tanggal_pengembalian'
    ];

    protected $dates = [
        'tanggal_pinjam',
        'tanggal_kembali'
    ];

    public function isDapatDiperpanjang()
    {
        // Log untuk debugging
        \Log::info('Cek Status Perpanjangan:', [
            'id' => $this->id,
            'status' => $this->status,
            'status_perpanjangan' => $this->status_perpanjangan
        ]);

        // Hanya bisa diperpanjang jika:
        // 1. Status masih 'dipinjam'
        // 2. Belum pernah diperpanjang (status_perpanjangan = false)
        return $this->status === 'dipinjam' && !$this->status_perpanjangan;
    }

    public function perpanjangPeminjaman($jumlahHari)
    {
        // Cek apakah bisa diperpanjang
        if (!$this->isDapatDiperpanjang()) {
            throw new \Exception('Buku ini sudah tidak dapat diperpanjang');
        }

        // Pastikan jumlahHari adalah integer
        $jumlahHari = (int) $jumlahHari;

        // Log data perpanjangan
        \Log::info('Perpanjang Peminjaman:', [
            'id' => $this->id,
            'jumlah_hari' => $jumlahHari,
            'tanggal_kembali_lama' => $this->tanggal_kembali
        ]);

        $this->tanggal_kembali = Carbon::parse($this->tanggal_kembali)->addDays($jumlahHari);
        $this->status_perpanjangan = true; // Set status perpanjangan menjadi true

        // Log data setelah perpanjangan
        \Log::info('Hasil Perpanjangan:', [
            'tanggal_kembali_baru' => $this->tanggal_kembali,
            'status_perpanjangan' => $this->status_perpanjangan
        ]);

        return $this->save();
    }

    public function denda()
    {
        return $this->hasOne(Denda::class, 'peminjaman_id');
    }
    protected function getStatusAttribute($value)
    {
        if ($value == 'dipinjam') {
            $tanggalKembali = Carbon::parse($this->tanggal_kembali);
            if (Carbon::now()->gt($tanggalKembali)) {
                return 'terlambat';
            }
        }
        return $value;
    }
}
