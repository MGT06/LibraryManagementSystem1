<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\PeminjamanModel;
use App\Models\PeminjamModel;
use App\Models\BukuModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Barryvdh\DomPDF\Facade\Pdf;

use Exception;

class TransaksiController extends Controller
{
    public function index()
    {
        try {
            $peminjaman = PeminjamanModel::with('denda')
                ->orderBy('created_at', 'desc')
                ->get();

            $peminjam = PeminjamModel::select('nama', 'nis', 'kelas')->get();

            $buku = BukuModel::select('judul')
                ->orderBy('judul', 'asc')
                ->get();

            return view('transaksi', [
                'peminjaman' => $peminjaman,
                'peminjam' => $peminjam,
                'buku' => $buku
            ]);

        } catch (Exception $e) {
            return view('transaksi', [
                'peminjaman' => collect([]),
                'peminjam' => collect([]),
                'buku' => collect([]),
                'error' => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255',
                'nis' => 'required|string|max:20',
                'judul' => 'required|string|max:255',
                'lama_pinjam' => 'required|integer|min:1|max:7',
            ]);

            $lamaPinjam = (int) $validated['lama_pinjam'];

            DB::beginTransaction();

            $peminjamanAktif = PeminjamanModel::where('nis', $validated['nis'])
                ->where('status', 'dipinjam')
                ->count();

            $peminjamanTidakAktif = PeminjamanModel::where('nis', $validated['nis'])
                ->whereIn('status', ['terlambat', 'dikembalikan'])
                ->count();

            if ($peminjamanAktif >= 2) {
                throw new Exception('Siswa sudah memiliki 2 peminjaman aktif');
            }

            $bukuDipinjam = PeminjamanModel::where('judul_buku', $validated['judul'])
                ->where('status', 'dipinjam')
                ->count();

            if ($bukuDipinjam >= 1) {
                throw new Exception('Buku sedang dipinjam');
            }

            $peminjaman = new PeminjamanModel();
            $peminjaman->nama_peminjam = $validated['nama'];
            $peminjaman->nis = $validated['nis'];
            $peminjaman->judul_buku = $validated['judul'];
            $peminjaman->lama_pinjam = $lamaPinjam;
            $peminjaman->tanggal_pinjam = now();
            $peminjaman->tanggal_kembali = now()->addDays($lamaPinjam);
            $peminjaman->status = 'dipinjam';
            $peminjaman->status_perpanjangan = false;

            $peminjaman->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Peminjaman berhasil ditambahkan'
            ]);

        } catch (ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $peminjaman = PeminjamanModel::findOrFail($id);

            if ($peminjaman->status === 'dipinjam') {
                throw new Exception('Tidak dapat menghapus peminjaman yang masih aktif');
            }

            $peminjaman->delete();

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function perpanjang(Request $request, $id)
    {
        try {
            Log::info('Memulai proses perpanjangan untuk ID: ' . $id);
            
            $validated = $request->validate([
                'jumlah_hari' => 'required|integer|min:1|max:7'
            ]);

            DB::beginTransaction();

            $peminjaman = PeminjamanModel::findOrFail($id);

            if (!$peminjaman->isDapatDiperpanjang()) {
                throw new Exception('Peminjaman ini sudah tidak dapat diperpanjang');
            }

            $jumlahHari = (int) $validated['jumlah_hari'];

            $peminjaman->perpanjangPeminjaman($jumlahHari);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => "Peminjaman berhasil diperpanjang {$jumlahHari} hari"
            ]);

        } catch (Exception $e) {
            Log::error('Error saat memperpanjang: ' . $e->getMessage());
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function testKeterlambatan($id)
    {
        try {
            $peminjaman = PeminjamanModel::findOrFail($id);

            $peminjaman->update([
                'tanggal_kembali' => now()->subDays(1)
            ]);

            \Artisan::call('check:keterlambatan');

            return redirect()->back()
                ->with('success', 'Test keterlambatan berhasil dijalankan. Silakan cek status dan denda.');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menjalankan test: ' . $e->getMessage());
        }
    }

    public function prosesKembali($id)
    {
        try {
            $peminjaman = PeminjamanModel::findOrFail($id);

            if (Carbon::now() <= Carbon::parse($peminjaman->tanggal_kembali)) {
                $peminjaman->update([
                    'status' => 'dikembalikan',
                    'tanggal_pengembalian' => Carbon::now()
                ]);
                return redirect()->back()
                    ->with('success', 'Buku berhasil dikembalikan');
            }

            $peminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_pengembalian' => Carbon::now()
            ]);

            return redirect()->back()
                ->with('success', 'Buku berhasil dikembalikan');

        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menjalankan proses kembali: ' . $e->getMessage());
        }
    }

    public function generateReport(Request $request)
    {
        try {
            $transaksi = PeminjamanModel::where('status', 'dikembalikan')
                ->orderBy('tanggal_pengembalian', 'desc')
                ->get();

            if ($transaksi->isEmpty()) {
                return redirect()->back()
                    ->with('error', 'Tidak ada data transaksi yang dikembalikan');
            }

            $pdf = Pdf::loadView('reports.transaksi', [
                'transaksi' => $transaksi,
                'request' => $request,
                'currentUrl' => $request->url()
            ]);

            $pdf->setPaper('A4', 'portrait');

            return $pdf->download('laporan-transaksi-' . Carbon::now()->format('d-m-Y') . '.pdf');
        } catch (Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat generate report: ' . $e->getMessage());
        }
    }
}