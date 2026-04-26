@extends('layouts.app')
@include('layouts.sidebar')

@section('title', 'Tambah Denda')

@section('content')
<div class="content-header">
    <h1>Tambah Denda - {{ $peminjaman->first()->nama_peminjam }}</h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('denda.store') }}" method="POST">
            @csrf
                        {{-- Tambahkan debug untuk melihat error --}}
                        @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tampilkan session messages --}}
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            
            <div class="form-group">
                <label for="peminjaman_id">Pilih Buku yang Didenda:</label>
                <select name="peminjaman_id" id="peminjaman_id" class="form-control" required>
                    <option value="">Pilih Buku</option>
                    @foreach($peminjaman as $pinjam)
                        @if($pinjam->status === 'dipinjam')
                            <option value="{{ $pinjam->id }}">
                                {{ $pinjam->judul_buku }} (Dipinjam: {{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') }})
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="jenis_denda">Jenis Denda:</label>
                <select name="jenis_denda" id="jenis_denda" class="form-control" required>
                    <option value="">Pilih Jenis Denda</option>
                    <option value="keterlambatan">Keterlambatan</option>
                    <option value="kerusakan">Kerusakan Buku</option>
                    <option value="kehilangan">Kehilangan Buku</option>
                </select>
            </div>

            <div class="form-group">
                <label for="jumlah_denda">Jumlah Denda (Rp):</label>
                <input type="number" 
                       name="jumlah_denda" 
                       id="jumlah_denda" 
                       class="form-control" 
                       required 
                       min="0">
            </div>

            <div class="form-group">
                <label for="keterangan">Keterangan:</label>
                <textarea name="keterangan" 
                          id="keterangan" 
                          class="form-control" 
                          rows="3"></textarea>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <a href="{{ route('transaksi') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    console.log('Form submitted');
    const formData = new FormData(this);
    
    // Log form data
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    // Submit form
    this.submit();
});
</script>
@endsection