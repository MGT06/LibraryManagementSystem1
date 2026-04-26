<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PeminjamanModel;
use Carbon\Carbon;

class UpdatePinjamanStatus extends Command
{
    protected $signature = 'pinjaman:update-status';
    protected $description = 'Update status pinjaman yang terlambat';

    public function handle()
    {
        PeminjamanModel::where('status', 'dipinjam')
            ->where('tanggal_kembali', '<', Carbon::now())
            ->update(['status' => 'terlambat']);
        
        $this->info('Status pinjaman berhasil diperbarui');
    }
}