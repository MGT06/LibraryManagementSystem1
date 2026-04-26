<?php

namespace App\Console\Commands;

use App\Models\PeminjamanModel;
use App\Models\Denda;
use Illuminate\Console\Command;

class CheckKeterlambatan extends Command
{
    protected $signature = 'check:keterlambatan';
    protected $description = 'Mengecek keterlambatan pengembalian buku';

    public function handle()
    {
        $this->updateStatus();
        $this->info('Berhasil mengecek keterlambatan');
    }

    private function updateStatus()
    {
        // Ambil semua peminjaman yang masih dipinjam
        $peminjaman = PeminjamanModel::where('status', 'dipinjam')
            ->whereDate('tanggal_kembali', '<', now())
            ->get();

        foreach ($peminjaman as $pinjam) {
            // Update status menjadi terlambat
            $pinjam->update(['status' => 'terlambat']);

            // Cek apakah sudah ada denda keterlambatan
            $existingDenda = Denda::where('peminjaman_id', $pinjam->id)
                ->where('jenis_denda', 'keterlambatan')
                ->first();

            // Jika belum ada denda, buat denda baru
            if (!$existingDenda) {
                Denda::create([
                    'peminjaman_id' => $pinjam->id,
                    'jenis_denda' => 'keterlambatan',
                    'jumlah_denda' => 2000,
                    'status_pembayaran' => 'belum_bayar',
                    'keterangan' => 'Denda keterlambatan otomatis'
                ]);
            }
        }
    }
} 