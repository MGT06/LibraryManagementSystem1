# Sistem Manajemen Perpustakaan

Aplikasi web berbasis Laravel untuk mengelola peminjaman buku, denda, dan transaksi perpustakaan secara efisien dan otomatis.

## Tentang Project

Sistem ini dirancang untuk perpustakaan sekolah atau umum yang ingin mendigitalisasi operasional harian mereka. Aplikasi ini menyelesaikan masalah pencatatan manual dengan menyediakan tracking peminjaman real-time, dan pelaporan transaksi yang komprehensif.

**Target Pengguna:** Administrator perpustakaan, pustakawan, dan staff perpustakaan

**Masalah yang Diselesaikan:** Menghilangkan pencatatan manual, dan memberikan akses instan ke riwayat peminjaman dan data anggota.

## Fitur Utama

- **Manajemen Peminjaman** - Tracking peminjaman buku dengan perhitungan tanggal jatuh tempo otomatis dan fitur perpanjangan
- **Sistem Denda** - Perhitungan denda dengan tracking pembayaran
- **Manajemen Anggota** - Database lengkap peminjam dengan riwayat transaksi
- **Inventori Buku** - Manajemen katalog dengan status ketersediaan
- **Laporan** - Generate laporan PDF untuk transaksi dan statistik
- **Pencarian & Filter** - Pencarian cepat berdasarkan nama anggota, judul buku, atau rentang tanggal

## Tech Stack

**Backend:** PHP 8.3.12 , Laravel 11.51 
**Database:** MySQL 
**Frontend:** Blade Templates, Bootstrap 5, JavaScript  
**Libraries:** DomPDF (laporan), SweetAlert2 (notifikasi)

## Cara Menjalankan Project

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

## Screenshot

### Login
<img width="1000" height="600" alt="image" src="https://github.com/user-attachments/assets/12a299de-09b1-462c-a286-c38511dfb646" />

### Dashboard
<img width="1000" height="600" alt="image" src="https://github.com/user-attachments/assets/bc5fbed1-e61d-48bc-af1c-36766104bca6" />

### Transaksi
<img width="1000" height="600" alt="image" src="https://github.com/user-attachments/assets/54a49a23-0618-424a-8d82-095e034d5602" />

### Manajemen Peminjam
<img width="1000" height="600" alt="image" src="https://github.com/user-attachments/assets/77a7788e-f2e6-4117-bec2-cea9212b0d14" />

### Manajemen Buku
<img width="1000" height="600" alt="image" src="https://github.com/user-attachments/assets/30117b7b-8b9b-4ed5-b557-917488e1897e" />


## Kontak

**Developer:** Given  
**Email:** grdgiven4@gmail.com  
**GitHub:** [@MGT06](https://github.com/MGT06)

---

© 2024 - Built with Laravel
