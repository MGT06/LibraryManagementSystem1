<?php

namespace App\Http\Controllers;

use App\Models\Denda;
use App\Models\PeminjamanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DendaController extends Controller
{
    public function create($peminjamanId)
    {
        $initialPeminjaman = PeminjamanModel::findOrFail($peminjamanId);
        $peminjaman = PeminjamanModel::where('nama_peminjam', $initialPeminjaman->nama_peminjam)
                                    ->where('status', 'dipinjam')
                                    ->get();

        return view('denda', compact('peminjaman'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'peminjaman_id' => 'required|exists:peminjaman,id',
                'jenis_denda' => 'required|in:keterlambatan,kerusakan,kehilangan',
                'jumlah_denda' => 'required|numeric|min:0',
                'keterangan' => 'nullable|string'
            ]);

            Log::info('Validated data:', $validated);

            $denda = Denda::create([
                'peminjaman_id' => $validated['peminjaman_id'],
                'jenis_denda' => $validated['jenis_denda'],
                'jumlah_denda' => $validated['jumlah_denda'],
                'status_pembayaran' => 'belum_bayar',
                'keterangan' => $validated['keterangan'] ?? null
            ]);

            DB::commit();

            return redirect()->route('transaksi')
                           ->with('success', 'Denda berhasil ditambahkan');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding denda: ' . $e->getMessage());
            
            return redirect()->back()
                           ->with('error', 'Gagal menambahkan denda: ' . $e->getMessage())
                           ->withInput();
        }
    }

    public function updateStatus(Request $request, Denda $denda)
    {
        try {
            $request->validate([
                'status_pembayaran' => 'required|in:sudah_bayar,belum_bayar'
            ]);

            $denda->update([
                'status_pembayaran' => $request->status_pembayaran
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Status pembayaran berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengubah status pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}