@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="content-header">
    <h1>Beranda</h1>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

{{-- Notifikasi Denda --}}
@if($unpaidDenda > 0)
    <div class="denda-notification">
        <div class="notification-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="notification-content">
            <h4>Pemberitahuan Denda</h4>
            <p>Terdapat <strong>{{ $unpaidDenda }}</strong> denda yang belum dibayar</p>
        </div>
        <a href="{{ route('transaksi') }}" class="notification-action">
            Lihat Detail <i class="fas fa-arrow-right"></i>
        </a>
    </div>
@endif

<div class="stats-container">
    <a href="{{ route('buku') }}" class="stat-card-link">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <h3>Total Buku</h3>
                <div class="stat-number">{{ $totalBuku }}</div>
                <small>{{ $newBuku }} buku baru</small>
            </div>
        </div>
    </a>

    <a href="{{ route('transaksi') }}" class="stat-card-link">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-info">
                <h3>Total Peminjaman</h3>
                <div class="stat-number">{{ $totalPeminjaman }}</div>
                <small>{{ $activePeminjaman }} sedang dipinjam</small>
            </div>
        </div>
    </a>

    <a href="{{ route('peminjam') }}" class="stat-card-link">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>Total Peminjam</h3>
                <div class="stat-number">{{ $totalPeminjam }}</div>
                <small>{{ $newPeminjam }} peminjam baru</small>
            </div>
        </div>
    </a>
</div>
@endsection

@section('additional_css')
<style>
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: relative;
    top: 0;
}

/* Efek hover untuk stat-card */
.stat-card:hover {
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    top: -5px;
    transform: translateY(-2px);
    cursor: pointer;
}

.stat-icon {
    background: #f8f9fa;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    transition: all 0.3s ease;
}

/* Efek hover untuk icon */
.stat-card:hover .stat-icon {
    background: #e9ecef;
    transform: scale(1.1);
}

.stat-icon i {
    font-size: 24px;
    color: #3498db;
}

.stat-info h3 {
    margin: 0;
    color: #666;
    font-size: 16px;
}

.stat-number {
    font-size: 24px;
    font-weight: bold;
    color: #2c3e50;
    margin: 5px 0;
}

.stat-info small {
    color: #2ecc71; /* Warna hijau lebih terang */
    font-weight: 500;
}

/* Animasi untuk hover effect */
@keyframes float {
    0% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-5px);
    }
    100% {
        transform: translateY(0px);
    }
}

.stat-card:hover {
    animation: float 2s ease-in-out infinite;
}

.stat-card-link {
    text-decoration: none;
    color: inherit;
}

.stat-card-link:hover {
    text-decoration: none;
    color: inherit;
}

/* Pindahkan efek hover dari .stat-card ke .stat-card-link */
.stat-card-link:hover .stat-card {
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
    top: -5px;
    transform: translateY(-2px);
    animation: float 2s ease-in-out infinite;
}

.stat-card-link:hover .stat-icon {
    background: #e9ecef;
    transform: scale(1.1);
}

/* Style untuk notifikasi denda */
.denda-notification {
    background: #fff3cd;
    border-left: 4px solid #ffc107;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    animation: slideIn 0.5s ease-out;
}

.notification-icon {
    font-size: 24px;
    color: #ffc107;
    margin-right: 20px;
}

.notification-icon i {
    animation: pulse 2s infinite;
}

.notification-content {
    flex-grow: 1;
}

.notification-content h4 {
    margin: 0;
    color: #856404;
    font-size: 18px;
    margin-bottom: 5px;
}

.notification-content p {
    margin: 0;
    color: #856404;
}

.notification-content strong {
    color: #e0a800;
}

.notification-action {
    background: #ffc107;
    color: #856404;
    padding: 8px 16px;
    border-radius: 4px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.notification-action:hover {
    background: #e0a800;
    color: #fff;
    text-decoration: none;
}

/* Animasi untuk notifikasi */
@keyframes slideIn {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.2);
    }
    100% {
        transform: scale(1);
    }
}
</style>
@endsection