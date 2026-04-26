<?php

namespace App\Http\Controllers;

use App\Models\PeminjamModel;
use App\Models\PeminjamanModel;
use App\Models\Denda;
use Illuminate\Http\Request;

class PeminjamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $peminjam = PeminjamModel::orderBy('created_at', 'desc')->get();
            return view('peminjam', ['peminjam' => $peminjam]);
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'nis' => 'required|numeric',
            'kelas' => 'required'
        ]);

        try {
            PeminjamModel::create($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Data peminjam berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        try {
            $peminjam = PeminjamModel::findOrFail($id);
            return response()->json($peminjam);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required',
            'nis' => 'required|numeric',
            'kelas' => 'required'
        ]);

        try {
            $peminjam = PeminjamModel::findOrFail($id);
            $peminjam->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Data peminjam berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $peminjam = PeminjamModel::findOrFail($id);
            $peminjam->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data peminjam berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data peminjam'
            ], 500);
        }
    }

    public function prosesKembali($id)
    {
        $peminjaman = PeminjamanModel::findOrFail($id);
        
        // Jika buku dikembalikan sebelum tanggal kembali
        if (now() <= $peminjaman->tanggal_kembali) {
            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_pengembalian' => now()
            ]);
            return redirect()->back()->with('success', 'Buku berhasil dikembalikan');
        }
        
        // Jika terlambat
        $peminjaman->update([
            'status' => 'dikembalikan',
            'tanggal_pengembalian' => now()
        ]);

        // Cek apakah sudah ada denda keterlambatan
        $existingDenda = Denda::where('peminjaman_id', $peminjaman->id)
            ->where('jenis_denda', 'keterlambatan')
            ->first();

        // Jika belum ada denda, buat denda baru
        if (!$existingDenda) {
            Denda::create([
                'peminjaman_id' => $peminjaman->id,
                'jenis_denda' => 'keterlambatan',
                'jumlah_denda' => 2000,
                'status_pembayaran' => 'belum_bayar',
                'keterangan' => 'Denda keterlambatan'
            ]);
        }
        return redirect()->back()->with('success', 'Buku dikembalikan dengan denda keterlambatan');
    }
}
