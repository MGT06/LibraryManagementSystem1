@include('layouts.sidebar')
@extends('layouts.app')

@section('title', 'Data Transaksi')

@section('content')
<div class="content-header">
    <h1>Data Transaksi</h1>
</div>

{{-- Search Peminjaman --}}
<div class="peminjaman-search-container">
    <div class="peminjaman-search-group">
        <div class="peminjaman-search-wrapper">
            <input type="text" id="searchInput" placeholder="" class="peminjaman-search-input">
        </div>
        <select id="filterHari" class="form-control" onchange="filterByDays(this.value)">
            <option value="">Semua Transaksi</option>
            <option value="7">7 Hari Terakhir</option>
            <option value="14">14 Hari Terakhir</option>
            <option value="30">30 Hari Terakhir</option>
        </select>
        <button class="btn btn-danger" onclick="resetSearch()">Reset</button>
        <button class="btn btn-primary" onclick="showPeminjamanModal()">Tambah Peminjaman</button>
        <a href="{{ route('transaksi.report') }}" class="btn btn-success" style="text-decoration: none;">Export PDF</a>
    </div>
</div>

@if(session('error') || isset($error))
    <div class="alert alert-danger">
        {{ session('error') ?? $error }}
    </div>
@endif

<div class="card">
    @if(isset($peminjaman) && $peminjaman->count() > 0)
        @php
            $groupedPeminjaman = $peminjaman->groupBy('nama_peminjam')
                ->sortByDesc(function($group) {
                    return $group->count();
                });
        @endphp

        @foreach($groupedPeminjaman as $namaPeminjam => $peminjamanGroup)
            <div class="peminjaman-group">
                <div class="peminjaman-header" onclick="togglePeminjaman('peminjaman-{{ str_replace(' ', '-', $namaPeminjam) }}')">
                    <div class="peminjaman-title">
                        <i class="fas fa-user-circle"></i>
                        <span>{{ $namaPeminjam }}</span>
                        <span class="badge badge-info">{{ $peminjamanGroup->count() }} Peminjaman</span>
                    </div>
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </div>
                
                <div id="peminjaman-{{ str_replace(' ', '-', $namaPeminjam) }}" class="peminjaman-content">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Tanggal Kembali</th>
                                <th>Tanggal Pengembalian</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjamanGroup as $index => $pinjam)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $pinjam->judul_buku }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->format('d/m/Y') }}</td>
                                    <td>
                                        @if($pinjam->tanggal_pengembalian)
                                            {{ \Carbon\Carbon::parse($pinjam->tanggal_pengembalian)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ 
                                            $pinjam->status === 'dipinjam' ? 'badge-primary' :
                                            ($pinjam->status === 'terlambat' ? 'badge-warning' : 'badge-success') 
                                        }}">
                                            {{ ucfirst($pinjam->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            @if($pinjam->status === 'dipinjam')
                                                <form action="{{ route('transaksi.kembali', $pinjam->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" 
                                                        onclick="return confirm('Apakah Anda yakin ingin mengembalikan buku ini?')"
                                                        title="Kembalikan buku">
                                                        <i class="fas fa-check"></i> Kembalikan
                                                    </button>
                                                </form>

                                                <button onclick="showPerpanjangDialog({{ $pinjam->id }})"
                                                    class="btn btn-info btn-sm btn-primary" style="margin-top: 5px;">
                                                    <i class="fas fa-clock"></i> Perpanjang
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center p-4">
            <p>Tidak ada data peminjaman</p>
        </div>
    @endif
</div>

{{-- Search Denda --}}
<div class="denda-search-container mb-3">
    <div class="denda-search-group">
        <div class="denda-search-wrapper">
            <input type="text" id="searchDenda" placeholder="Cari informasi denda..." class="denda-search-input">
        </div>
        <button class="btn btn-danger" style="width: 100px;" onclick="resetSearchDenda()">Reset</button>
    </div>
</div>

@if(isset($peminjaman) && $peminjaman->count() > 0)
    @php
        $groupedPeminjaman = $peminjaman->groupBy('nama_peminjam');
    @endphp

    <div class="denda-accordion">
        @foreach($groupedPeminjaman as $namaPeminjam => $peminjamanGroup)
            @php
                $totalDenda = $peminjamanGroup->sum(function ($pinjam) {
                    return $pinjam->denda ? $pinjam->denda->jumlah_denda : 0;
                });

                $hasDenda = $peminjamanGroup->filter(function ($pinjam) {
                    return $pinjam->denda !== null;
                })->count() > 0;

                // Cek status pembayaran denda
                $hasUnpaidDenda = $peminjamanGroup->filter(function ($pinjam) {
                    return $pinjam->denda && $pinjam->denda->status_pembayaran === 'belum_bayar';
                })->count() > 0;
            @endphp
            <div class="denda-item {{ $hasDenda ? ($hasUnpaidDenda ? 'active' : 'inactive') : '' }}">
                <div class="denda-header" onclick="toggleDenda('denda-{{ str_replace(' ', '-', $namaPeminjam) }}')">
                    <div class="denda-title">
                        <i class="fas fa-user-circle"></i>
                        <span>Informasi Denda - {{ $namaPeminjam }}</span>
                        @if($hasDenda)
                            @if($hasUnpaidDenda)
                                <span class="badge badge-primary">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Selesai</span>
                            @endif
                        @endif
                    </div>
                    <div class="denda-actions">
                        @if($peminjamanGroup->where('status', 'dipinjam')->count() > 0)
                            <a href="{{ route('denda.create', $peminjamanGroup->where('status', 'dipinjam')->first()->id) }}"
                                class="btn-denda">
                                <i class="fas fa-plus"></i> Tambah Denda
                            </a>
                        @endif
                        <i class="fas fa-chevron-down toggle-icon"></i>
                    </div>
                </div>
                <div id="denda-{{ str_replace(' ', '-', $namaPeminjam) }}" class="denda-content">
                    @if($hasDenda)
                        <div class="denda-table-wrapper">
                            <table class="denda-table">
                                <thead>
                                    <tr>
                                        <th>Judul Buku</th>
                                        <th>Jenis Denda</th>
                                        <th>Jumlah Denda</th>
                                        <th>Status</th>
                                        <th>Keterangan</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($peminjamanGroup as $pinjam)
                                        @if($pinjam->denda)
                                            <tr>
                                                <td>{{ $pinjam->judul_buku }}</td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        {{ ucfirst($pinjam->denda->jenis_denda) }}
                                                    </span>
                                                </td>
                                                <td class="nominal-denda">
                                                    Rp {{ number_format($pinjam->denda->jumlah_denda, 0, ',', '.') }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $pinjam->denda->status_pembayaran == 'sudah_bayar' ? 'badge-success' : 'badge-warning' }}">
                                                        {{ $pinjam->denda->status_pembayaran == 'sudah_bayar' ? 'Lunas' : 'Belum Dibayar' }}
                                                    </span>
                                                </td>
                                                <td>{{ $pinjam->denda->keterangan ?? '-' }}</td>
                                                <td>
                                                    <button
                                                        onclick="updateStatusPembayaran({{ $pinjam->denda->id }}, '{{ $pinjam->denda->status_pembayaran }}')"
                                                        class="btn btn-sm {{ $pinjam->denda->status_pembayaran == 'sudah_bayar' ? 'btn-warning' : 'btn-success' }}">
                                                        @if($pinjam->denda->status_pembayaran == 'sudah_bayar')
                                                            <i class="fas fa-times"></i> Batalkan
                                                        @else
                                                            <i class="fas fa-check"></i> Bayar
                                                        @endif
                                                    </button>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    <tr class="total-row">
                                        <td colspan="2" class="text-right"><strong>Total Denda:</strong></td>
                                        <td colspan="1" class="nominal-denda">
                                            <strong>Rp {{ number_format($totalDenda, 0, ',', '.') }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="no-denda">
                            <i class="fas fa-info-circle"></i>
                            <p>Tidak ada denda untuk peminjam ini</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif

<!-- Modal Form Peminjaman -->
<div id="peminjamanModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closePeminjamanModal()">&times;</span>
        <div class="modal-header">
            <h2 class="modal-title">Form Peminjaman Buku</h2>
        </div>
        <form id="peminjamanForm" onsubmit="submitPeminjaman(event)">
            @csrf
            <div class="form-group">
                <label for="nama">Nama Peminjam:</label>
                <select id="nama" name="nama" class="form-control" required onchange="updateNIS()">
                    <option value="">Pilih Peminjam</option>
                    @foreach($peminjam as $p)
                        <option value="{{ $p->nama }}" data-nis="{{ $p->nis }}">
                            {{ $p->nama }} - {{ $p->kelas }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="nis">NIS:</label>
                <input type="text" id="nis" name="nis" class="form-control" readonly>
            </div>
            <div class="form-group">
                <label for="judul">Judul Buku:</label>
                <select id="judul" name="judul" class="form-control" required>
                    <option value="">Pilih Buku</option>
                    @foreach($buku as $b)
                        <option value="{{ $b->judul }}">{{ $b->judul }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="lama_pinjam">Lama Peminjaman (hari):</label>
                <input type="number" id="lama_pinjam" name="lama_pinjam" class="form-control" min="1" max="7" value="7"
                    required onchange="this.value = parseInt(this.value) || 7">
            </div>
            <div class="modal-buttons">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="button" class="btn btn-danger" onclick="closePeminjamanModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Perpanjangan -->
<div id="perpanjangModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closePerpanjangModal()">&times;</span>
        <h2>Perpanjang Peminjaman</h2>

        <form id="perpanjangForm" method="POST">
            @csrf
            <div class="form-group">
                <label for="jumlah_hari">Jumlah Hari Perpanjangan:</label>
                <input type="number" id="jumlah_hari" name="jumlah_hari" class="form-control" min="1" max="7" value="7">
                <small class="text-muted">Maksimal 7 hari</small>
            </div>

            <div class="modal-buttons">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="button" class="btn btn-secondary" onclick="closePerpanjangModal()">
                    <i class="fas fa-times"></i> Batal
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('additional_css')
<style>
    /* Style yang sudah ada tetap dipertahankan */
    .helper-text {
        font-size: 12px;
        color: #666;
        margin-top: 5px;
    }

    .modal-buttons {
        margin-top: 20px;
        text-align: right;
    }

    .modal-buttons button {
        margin-left: 10px;
    }


    .search-input {
        width: 250px;
        position: relative;
        overflow: hidden;
    }

    .search-input::placeholder {
        animation: slide 8s infinite;
        white-space: nowrap;
        position: absolute;
    }

    /* Perbaikan style search container */
    .search-container {
        margin-bottom: 20px;
    }

    .search-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .search-input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .search-input:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
    }

    @keyframes slideText {
        0% {
            left: 100%;
        }

        100% {
            left: -100%;
        }
    }

    .search-wrapper {
        position: relative;
        width: 250px;
        overflow: hidden;
    }

    .search-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .search-input:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
    }

    .search-wrapper::before {
        content: 'Cari nama peminjam atau judul buku...';
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        pointer-events: none;
        white-space: nowrap;
        animation: slideText 10s linear infinite;
        z-index: 1;
    }

    .search-input:focus::placeholder,
    .search-input:not(:placeholder-shown)::placeholder {
        opacity: 0;
    }

    .search-input:focus+.search-wrapper::before,
    .search-input:not(:placeholder-shown)+.search-wrapper::before {
        display: none;
    }

    .search-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    /* Style baru untuk card denda */
    .denda-card {
        margin-bottom: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }

    .denda-header {
        background-color: #f8fafc;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        transition: background-color 0.2s;
    }

    .denda-header:hover {
        background-color: #edf2f7;
    }

    .denda-header h4 {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .toggle-icon {
        transition: transform 0.2s;
    }

    .toggle-icon.rotated {
        transform: rotate(-180deg);
    }

    .denda-body {
        padding: 1rem;
        background-color: white;
    }

    .denda-table {
        width: 100%;
        border-collapse: collapse;
    }

    .denda-table th,
    .denda-table td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .denda-table th {
        background-color: #f8fafc;
        font-weight: 600;
    }

    .total-row {
        background-color: #f8fafc;
    }

    .nominal-denda {
        font-family: monospace;
        text-align: right;
    }

    .btn-denda {
        padding: 0.5rem 1rem;
        background-color: #4299e1;
        color: white;
        border-radius: 0.375rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-denda:hover {
        background-color: #3182ce;
        color: white;
        text-decoration: none;
    }

    .no-denda {
        color: #718096;
        text-align: center;
        padding: 24px;
        margin: 0;
        font-style: italic;
        font-size: 0.95rem;
    }

    /* Animasi hover yang lebih halus */
    .denda-card {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .denda-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    /* Warna untuk status tertentu */
    .status-belum-bayar {
        color: #e53e3e;
    }

    .status-sudah-bayar {
        color: #38a169;
    }

    /* Responsive design improvements */
    @media (max-width: 768px) {
        .denda-header {
            flex-direction: column;
            gap: 12px;
            text-align: center;
        }

        .btn-denda {
            width: 100%;
            justify-content: center;
        }

        .denda-table th {
            width: 40%;
        }

        .denda-table {
            table-layout: auto;
        }
    }

    /* Hover effect untuk table rows */
    .denda-table tr:hover {
        background-color: #f7fafc;
    }

    /* Style untuk nominal denda */
    .nominal-denda {
        font-family: 'Roboto Mono', monospace;
        font-weight: 500;
        color: #2d3748;
    }

    /* Tambahan style untuk tabel denda yang baru */
    .denda-table thead th {
        background-color: #f8fafc;
        font-weight: 600;
        text-align: left;
        padding: 12px 16px;
        border-bottom: 2px solid #e2e8f0;
    }

    .denda-table tbody td {
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
    }

    .total-row {
        background-color: #f8fafc;
    }

    .total-row td {
        border-top: 2px solid #e2e8f0;
        font-weight: 500;
    }

    .text-right {
        text-align: right;
    }

    .nominal-denda {
        font-family: 'Roboto Mono', monospace;
        color: #2d3748;
    }

    .status-column {
        text-align: center;
    }

    .btn-status {
        display: none;
        /* Sembunyikan tombol secara default */
        margin-top: 8px;
        padding: 6px 12px;
        border: none;
        border-radius: 4px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-status i {
        margin-right: 4px;
    }

    .status-column:hover .btn-status {
        display: inline-block;
        /* Tampilkan tombol saat hover */
    }

    .btn-success {
        background-color: #48bb78;
        color: white;
    }

    .btn-success:hover {
        background-color: #38a169;
    }

    .btn-warning {
        background-color: #f6ad55;
        color: #744210;
    }

    .btn-warning:hover {
        background-color: #ed8936;
    }

    .denda-accordion {
        max-width: 100%;
        margin: 20px 0;
    }

    .denda-item {
        background: #fff;
        border-radius: 8px;
        margin-bottom: 12px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .denda-header {
        padding: 16px 20px;
        background: #f8fafc;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }

    .denda-header:hover {
        background: #edf2f7;
    }

    .denda-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        color: #2d3748;
    }

    .denda-title i {
        color: #4299e1;
        font-size: 1.2em;
    }

    .denda-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .toggle-icon {
        transition: transform 0.3s ease;
        color: #718096;
    }

    .toggle-icon.active {
        transform: rotate(-180deg);
    }

    .denda-content {
        display: none;
        padding: 20px;
        background: #fff;
        border-top: 1px solid #e2e8f0;
    }

    .denda-table-wrapper {
        overflow-x: auto;
    }

    .denda-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .denda-table th {
        background: #f7fafc;
        padding: 12px 16px;
        font-weight: 600;
        text-align: left;
        color: #4a5568;
        border-bottom: 2px solid #e2e8f0;
    }

    .denda-table td {
        padding: 12px 16px;
        border-bottom: 1px solid #edf2f7;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .badge-info {
        background: #ebf8ff;
        color: #2b6cb0;
    }

    .badge-success {
        background: #f0fff4;
        color: #2f855a;
    }

    .badge-warning {
        background: #fffaf0;
        color: #c05621;
    }

    .no-denda {
        text-align: center;
        padding: 40px 20px;
        color: #718096;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .no-denda i {
        font-size: 2em;
        color: #a0aec0;
    }

    .btn-denda {
        padding: 0.5rem 1rem;
        background-color: #4299e1;
        color: white;
        border-radius: 0.375rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .denda-item.inactive {
        opacity: 0.8;
    }

    .denda-item.inactive .denda-header {
        background: #f1f5f9;
    }

    .badge-primary {
        background: #3b82f6;
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        margin-left: 8px;
    }

    .badge-secondary {
        background: #64748b;
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        margin-left: 8px;
    }

    /* Tambahkan separator antara aktif dan tidak aktif */
    .denda-item.inactive:first-of-type {
        margin-top: 24px;
        border-top: 2px dashed #e2e8f0;
        padding-top: 24px;
    }

    /* Optional: Tambahkan label untuk section */
    .denda-section-label {
        color: #64748b;
        font-size: 0.875rem;
        margin: 16px 0 8px;
        padding-left: 4px;
    }

    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 4px;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-success {
        background-color: #48bb78;
        color: white;
    }

    .btn-success:hover {
        background-color: #38a169;
    }

    .btn-warning {
        background-color: #ed8936;
        color: white;
    }

    .btn-warning:hover {
        background-color: #dd6b20;
    }

    .denda-table td {
        vertical-align: middle;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .btn-sm {
        width: 100%;
        text-align: center;
        justify-content: center;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 10px;
    }

    .btn-sm:disabled {
        opacity: 0.65;
        cursor: not-allowed;
        pointer-events: none;
    }

    /* Tambahkan style untuk tooltip */
    [title] {
        position: relative;
    }

    [title]:hover:after {
        content: attr(title);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 1000;
        margin-bottom: 5px;
    }

    [title]:hover:before {
        content: '';
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid transparent;
        border-top-color: rgba(0, 0, 0, 0.8);
        margin-bottom: -5px;
    }

    .btn-secondary {
        background-color: #718096;
        color: white;
        border: none;
    }

    .btn-secondary:hover {
        background-color: #4a5568;
        color: white;
    }

    /* Style untuk search peminjaman */
    .peminjaman-search-container {
        margin-bottom: 20px;
    }

    .peminjaman-search-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .peminjaman-search-wrapper {
        position: relative;
        width: 250px;
        overflow: hidden;
    }

    .peminjaman-search-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .peminjaman-search-input:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
    }

    .peminjaman-search-wrapper::before {
        content: 'Cari nama peminjam atau judul buku...';
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        pointer-events: none;
        white-space: nowrap;
        animation: slideText 10s linear infinite;
        z-index: 1;
    }

    .peminjaman-search-input:focus::placeholder,
    .peminjaman-search-input:not(:placeholder-shown)::placeholder {
        opacity: 0;
    }

    .peminjaman-search-input:focus + .peminjaman-search-wrapper::before,
    .peminjaman-search-input:not(:placeholder-shown) + .peminjaman-search-wrapper::before {
        display: none;
    }
    
    .denda-search-container {
        margin: 20px 0;
    }

    .denda-search-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .denda-search-wrapper {
        position: relative;
        width: 900px;
        overflow: hidden;
    }

    .denda-search-input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .denda-search-input:focus {
        outline: none;
        border-color: #4CAF50;
        box-shadow: 0 0 5px rgba(76, 175, 80, 0.2);
    }

    @keyframes slideText {
        0% {
            left: 100%;
        }
        100% {
            left: -100%;
        }
    }

    /* Style untuk grup peminjaman */
    .peminjaman-group {
        margin-bottom: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }

    .peminjaman-header {
        padding: 1rem;
        background: #f8fafc;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background-color 0.2s;
    }

    .peminjaman-header:hover {
        background: #edf2f7;
    }

    .peminjaman-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        color: #2d3748;
    }

    .peminjaman-title i {
        color: #4299e1;
        font-size: 1.2em;
    }

    .peminjaman-content {
        display: none;
        padding: 1rem;
        background: white;
    }

    .peminjaman-content table {
        width: 100%;
        border-collapse: collapse;
    }

    .peminjaman-content th,
    .peminjaman-content td {
        padding: 0.75rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .peminjaman-content th {
        background: #f7fafc;
        font-weight: 600;
        text-align: left;
    }

    .peminjaman-content thead {
        display: table-header-group;
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .peminjaman-content th {
        padding: 12px;
        text-align: left;
        font-weight: bold;
        border: 1px solid #dee2e6;
        color: #000000;
    }

    /* Alternatif jika masih belum berubah */
    .peminjaman-content table thead tr th {
        color: #000000 !important;
    }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const routes = {
        store: '{{ route('transaksi.store') }}',
        perpanjang: '{{ route('transaksi.perpanjang', ':id') }}'
    };

    let selectedPeminjamanId = null;

    function showPerpanjangDialog(id) {
        Swal.fire({
            title: 'Perpanjang Peminjaman',
            text: 'Masukkan jumlah hari perpanjangan (maksimal 7 hari):',
            input: 'number',
            inputAttributes: {
                min: 1,
                max: 7,
                step: 1
            },
            inputValue: 7,
            showCancelButton: true,
            confirmButtonText: 'Perpanjang',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: (jumlahHari) => {
                // Konversi ke integer
                jumlahHari = parseInt(jumlahHari);

                if (isNaN(jumlahHari) || jumlahHari < 1 || jumlahHari > 7) {
                    Swal.showValidationMessage('Jumlah hari harus antara 1-7 hari');
                    return false;
                }

                const url = routes.perpanjang.replace(':id', id);

                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ jumlah_hari: jumlahHari })
                })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => Promise.reject(err));
                        }
                        return response.json();
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: result.value.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
                });
            }
        }).catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: error.message || 'Terjadi kesalahan saat memperpanjang peminjaman'
            });
        });
    }

    function closeModal() {
        document.getElementById('perpanjangModal').style.display = 'none';
    }

    function validateHari(input) {
        if (input.value > 7) {
            input.value = 7;
        } else if (input.value < 1) {
            input.value = 1;
        }
    }

    function hapusPeminjaman(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data peminjaman akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/transaksi/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message || 'Terjadi kesalahan saat menghapus data'
                        });
                    });
            }
        });
    }

    function searchTransaksi() {
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const filterHari = document.getElementById('filterHari').value;
        const peminjamanGroups = document.querySelectorAll('.peminjaman-group');

        const today = new Date();
        const pastDate = filterHari ? new Date(today.setDate(today.getDate() - parseInt(filterHari))) : null;

        peminjamanGroups.forEach(group => {
            const nama = group.querySelector('.peminjaman-title span').textContent.toLowerCase();
            const rows = group.querySelectorAll('tbody tr');
            let showGroup = false;

            rows.forEach(row => {
                const buku = row.cells[1].textContent.toLowerCase();
                const tanggalParts = row.cells[2].textContent.split('/');
                const tanggalPinjam = new Date(
                    parseInt(tanggalParts[2]), // year
                    parseInt(tanggalParts[1]) - 1, // month (0-based)
                    parseInt(tanggalParts[0]) // day
                );

                // Mencari di nama ATAU judul buku
                const matchText = nama.includes(searchText) || buku.includes(searchText);

                // Filter berdasarkan hari
                let matchDate = true;
                if (pastDate) {
                    matchDate = tanggalPinjam >= pastDate;
                }

                if (matchText && matchDate) {
                    showGroup = true;
                }
            });

            if (showGroup) {
                group.style.display = '';
            } else {
                group.style.display = 'none';
            }
        });

        updateDisplayedNumbers();
    }

    function resetSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('filterHari').value = '';
        document.getElementById('searchDenda').value = '';

        // Reset transaksi
        const rows = document.querySelectorAll('tbody tr');
        rows.forEach(row => {
            row.style.display = '';
        });

        // Reset denda
        const dendaItems = document.querySelectorAll('.denda-item');
        dendaItems.forEach(item => {
            item.style.display = '';
            item.querySelector('.denda-content').style.display = 'none';
            item.querySelector('.toggle-icon').classList.remove('rotated');
        });

        updateDisplayedNumbers();
    }

    function filterByDays(days) {
        searchTransaksi(); // Gunakan fungsi pencarian yang sudah ada
    }

    function updateDisplayedNumbers() {
        let visibleIndex = 1;
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            if (row.style.display !== 'none') {
                row.cells[0].textContent = visibleIndex++;
            }
        });
    }

    // Event listeners untuk pencarian real-time
    document.getElementById('searchInput').addEventListener('keyup', searchTransaksi);
    document.getElementById('filterHari').addEventListener('change', searchTransaksi);

    // Fungsi untuk form peminjaman
    function showPeminjamanModal() {
        document.getElementById('peminjamanModal').style.display = 'block';
    }

    function closePeminjamanModal() {
        document.getElementById('peminjamanModal').style.display = 'none';
        document.getElementById('peminjamanForm').reset();
    }

    function updateNIS() {
        const selectElement = document.getElementById('nama');
        const nisInput = document.getElementById('nis');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        nisInput.value = selectedOption.getAttribute('data-nis') || '';
    }

    function submitPeminjaman(event) {
        event.preventDefault();

        const formData = new FormData(event.target);

        // Konversi lama_pinjam ke integer
        const lamaPinjam = parseInt(formData.get('lama_pinjam')) || 7;
        formData.set('lama_pinjam', lamaPinjam);

        // Debug log
        console.log('Data yang akan dikirim:', Object.fromEntries(formData));

        fetch(routes.store, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        closePeminjamanModal();
                        location.reload();
                    });
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: error.message || 'Terjadi kesalahan saat menyimpan data'
                });
            });
    }

    function updateStatusPembayaran(dendaId, currentStatus) {
        const newStatus = currentStatus === 'sudah_bayar' ? 'belum_bayar' : 'sudah_bayar';
        const confirmMessage = newStatus === 'sudah_bayar' ?
            'Apakah Anda yakin ingin menandai denda ini sebagai sudah dibayar?' :
            'Apakah Anda yakin ingin menandai denda ini sebagai belum dibayar?';

        Swal.fire({
            title: 'Konfirmasi',
            text: confirmMessage,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Ubah Status',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim request ke server
                fetch(`/denda/${dendaId}/update-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status_pembayaran: newStatus
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Status pembayaran berhasil diperbarui',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            throw new Error(data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: error.message || 'Terjadi kesalahan saat mengubah status'
                        });
                    });
            }
        });
    }

    function toggleDenda(dendaId) {
        const dendaBody = document.getElementById(dendaId);
        const header = dendaBody.previousElementSibling;
        const icon = header.querySelector('.toggle-icon');

        if (dendaBody.style.display === 'none') {
            dendaBody.style.display = 'block';
            icon.classList.add('rotated');
        } else {
            dendaBody.style.display = 'none';
            icon.classList.remove('rotated');
        }
    }

    // Tambahkan fungsi untuk membuka denda spesifik dari URL jika ada
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const openDenda = urlParams.get('open_denda');

        if (openDenda) {
            const dendaElement = document.getElementById(`denda-${openDenda}`);
            if (dendaElement) {
                dendaElement.style.display = 'block';
                dendaElement.previousElementSibling.querySelector('.toggle-icon').classList.add('rotated');
            }
        }
    });

    function searchDenda() {
        const searchText = document.getElementById('searchDenda').value.toLowerCase();
        const dendaItems = document.querySelectorAll('.denda-item');

        dendaItems.forEach(item => {
            const namaPeminjam = item.querySelector('.denda-title span').textContent.toLowerCase();
            const dendaContent = item.querySelector('.denda-content');
            const rows = item.querySelectorAll('.denda-table tbody tr:not(.total-row)');

            let found = false;

            // Cari di nama peminjam
            if (namaPeminjam.includes(searchText)) {
                found = true;
            }

            // Cari di detail denda
            rows.forEach(row => {
                const judulBuku = row.cells[0].textContent.toLowerCase();
                const jenisDenda = row.cells[1].textContent.toLowerCase();
                const keterangan = row.cells[4].textContent.toLowerCase();

                if (judulBuku.includes(searchText) ||
                    jenisDenda.includes(searchText) ||
                    keterangan.includes(searchText)) {
                    found = true;
                }
            });

            if (found) {
                item.style.display = '';
                // Buka accordion jika ditemukan
                dendaContent.style.display = 'block';
                item.querySelector('.toggle-icon').classList.add('rotated');
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Tambahkan event listener untuk pencarian denda
    document.getElementById('searchDenda').addEventListener('keyup', searchDenda);

    function togglePeminjaman(peminjamanId) {
        const content = document.getElementById(peminjamanId);
        const header = content.previousElementSibling;
        const icon = header.querySelector('.toggle-icon');

        if (content.style.display === 'none' || !content.style.display) {
            content.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }
</script>
@endsection