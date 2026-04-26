# Sistem Manajemen Perpustakaan

Aplikasi web berbasis Laravel untuk mengelola peminjaman buku, denda, dan transaksi perpustakaan secara efisien dan otomatis.

## � Tentang Project

Sistem ini dirancang untuk perpustakaan sekolah atau umum yang ingin mendigitalisasi operasional harian mereka. Aplikasi ini menyelesaikan masalah pencatatan manual dengan menyediakan tracking peminjaman real-time, perhitungan denda otomatis untuk keterlambatan, dan pelaporan transaksi yang komprehensif.

**Target Pengguna:** Administrator perpustakaan, pustakawan, dan staff perpustakaan

**Masalah yang Diselesaikan:** Menghilangkan pencatatan manual, mengurangi kesalahan perhitungan denda, dan memberikan akses instan ke riwayat peminjaman dan data anggota.

## ✨ Fitur Utama

- **Manajemen Peminjaman** - Tracking peminjaman buku dengan perhitungan tanggal jatuh tempo otomatis dan fitur perpanjangan
- **Sistem Denda** - Perhitungan denda keterlambatan otomatis dengan tracking pembayaran
- **Manajemen Anggota** - Database lengkap peminjam dengan riwayat transaksi
- **Inventori Buku** - Manajemen katalog dengan status ketersediaan
- **Laporan** - Generate laporan PDF untuk transaksi dan statistik
- **Pencarian & Filter** - Pencarian cepat berdasarkan nama anggota, judul buku, atau rentang tanggal

## �️ Tech Stack

**Backend:** PHP 8.x, Laravel 11.x  
**Database:** MySQL / SQLite  
**Frontend:** Blade Templates, Bootstrap 5, JavaScript  
**Libraries:** DomPDF (laporan), SweetAlert2 (notifikasi)

## � Cara Menjalankan Project

```bash
# Clone repository
git clone https://github.com/MGT06/KPSMT3.git
cd KPSMT3

# Install dependencies
composer install
npm install && npm run build

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate --seed

# Jalankan aplikasi
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

**Login Default:**  
Username: `admin`  
Password: `admin123`

## 📸 Screenshot

### Dashboard
![Dashboard](screenshots/dashboard.png)

### Halaman Peminjaman
![Peminjaman](screenshots/peminjaman.png)

### Manajemen Buku
![Buku](screenshots/buku.png)

### Sistem Denda
![Denda](screenshots/denda.png)

### Laporan Transaksi
![Laporan](screenshots/laporan.png)

> **Note:** Tambahkan screenshot aplikasi Anda ke folder `screenshots/` di root project

## � Kontak

**Developer:** Given  
**Email:** grdgiven4@gmail.com  
**GitHub:** [@MGT06](https://github.com/MGT06)

---

© 2024 - Built with Laravel
