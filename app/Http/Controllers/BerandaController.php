<?php

namespace App\Http\Controllers;

use App\Models\BukuModel;
use App\Models\PeminjamModel;
use App\Models\PeminjamanModel;

class BerandaController extends Controller
{
    public function index()
    {
        try {
            // Hitung jumlah denda yang belum dibayar
            $unpaidDenda = \App\Models\Denda::where('status_pembayaran', 'belum_bayar')->count();

            return view('beranda', [
                'totalBuku' => $this->getStatistics()['totalBuku'],
                'newBuku' => $this->getStatistics()['newBuku'],
                'totalPeminjaman' => $this->getStatistics()['totalPeminjaman'],
                'activePeminjaman' => $this->getStatistics()['activePeminjaman'],
                'totalPeminjam' => $this->getStatistics()['totalPeminjam'],
                'newPeminjam' => $this->getStatistics()['newPeminjam'],
                'unpaidDenda' => $unpaidDenda
            ]);
        } catch (\Exception $e) {
            \Log::error('Error di Beranda: ' . $e->getMessage());
            return view('beranda', [
                'totalBuku' => 0,
                'newBuku' => 0,
                'totalPeminjaman' => 0,
                'activePeminjaman' => 0,
                'totalPeminjam' => 0,
                'newPeminjam' => 0,
                'unpaidDenda' => 0,
                'error' => 'Terjadi kesalahan saat memuat data'
            ]);
        }
    }

    private function getStatistics()
    {
        return [
            'totalBuku' => BukuModel::count(),
            'newBuku' => BukuModel::whereMonth('created_at', now()->month)->count(),
            'totalPeminjaman' => PeminjamanModel::count(),
            'activePeminjaman' => PeminjamanModel::whereNull('tanggal_kembali')
                                    ->orWhere('tanggal_kembali', '>=', now())
                                    ->count(),
            'totalPeminjam' => PeminjamModel::count(),
            'newPeminjam' => PeminjamModel::whereMonth('created_at', now()->month)->count(),
        ];
    }
} 