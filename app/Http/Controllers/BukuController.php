<?php

namespace App\Http\Controllers;

use App\Models\BukuModel;
use Illuminate\Http\Request;

class BukuController extends Controller
{
    public function index()
    {
        try {
            $buku = BukuModel::orderBy('created_at', 'desc')->get();
            return view('buku', ['buku' => $buku]);
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'tahun_terbit' => 'required|numeric|min:1900|max:2024',
        ]);

        try {
            BukuModel::create($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Buku berhasil ditambahkan!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        try {
            $buku = BukuModel::findOrFail($id);
            return response()->json($buku);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'tahun_terbit' => 'required|numeric|min:1900|max:2024',
        ]);

        try {
            $buku = BukuModel::findOrFail($id);
            $buku->update($request->all());
            return response()->json([
                'status' => 'success',
                'message' => 'Buku berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $buku = BukuModel::findOrFail($id);
            $buku->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Buku berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
