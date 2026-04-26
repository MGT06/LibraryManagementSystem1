# Sistem Manajemen Perpustakaan

Aplikasi manajemen perpustakaan berbasis web untuk mengelola peminjaman buku, denda, dan transaksi perpustakaan secara efisien.

## 📚 Fitur Utama

### 1. Manajemen Peminjaman
- Pencatatan peminjaman buku dengan data lengkap peminjam
- Sistem perpanjangan peminjaman (maksimal 7 hari)
- Pengembalian buku dengan validasi otomatis
- Tracking status peminjaman real-time

### 2. Sistem Denda
- Perhitungan denda otomatis untuk keterlambatan
- Manajemen pembayaran denda
- Pengelompokan denda per peminjam
- Riwayat dan status pembayaran denda

### 3. Pencarian & Filter
- Pencarian peminjaman berdasarkan nama/judul
- Filter transaksi berdasarkan periode
- Pencarian denda dan status pembayaran

## 💻 Teknologi

- PHP 7.4+
- Laravel 8+
- MySQL 5.7+
- Bootstrap 4
- JavaScript/jQuery
- SweetAlert2

## 📋 Prasyarat

Sebelum instalasi, pastikan sistem Anda memenuhi persyaratan berikut:

- PHP >= 7.4
- Composer
- Node.js & NPM
- MySQL
- Web Server (Apache/Nginx)

## 🚀 Instalasi

1. Clone repository
2. Install dependencies
3. Konfigurasi database
4. Migrasi database
5. Jalankan server

## 📖 Penggunaan

### Login Admin

URL: http://localhost:8000/login

Username: admin
Password: admin123

### Manajemen Peminjaman
1. Klik tombol "Tambah Peminjaman"
2. Pilih peminjam dari daftar
3. Pilih buku yang akan dipinjam
4. Tentukan durasi peminjaman (max 7 hari)
5. Simpan transaksi

### Pengelolaan Denda
1. Akses menu Denda
2. Pilih peminjam yang memiliki denda
3. Input jumlah dan jenis denda
4. Update status pembayaran

## 📜 Aturan Bisnis

### Peminjaman
- Maksimal durasi peminjaman: 7 hari
- Maksimal perpanjangan: 7 hari
- Satu siswa bisa meminjam beberapa buku

### Denda
- Status: Belum bayar/Sudah bayar
- Jenis: Keterlambatan/Kerusakan/Kehilangan
- Pembayaran wajib dilunasi untuk peminjaman baru

## 🔧 Maintenance

### Backup Database
- Jalankan perintah `php artisan backup:run`
- File backup akan disimpan di folder `storage/app/public/backup`

### Update Sistem
- git pull
- composer update
- npm install
- npm run build
- php artisan migrate --force
- php artisan optimize
- php artisan config:cache
- php artisan route:cache

## 🔒 Keamanan

- Validasi input untuk semua form
- Proteksi CSRF untuk semua request
- Autentikasi untuk akses admin
- Logging untuk aktivitas penting

## 🐛 Troubleshooting

### Masalah Umum
1. Database connection error
   - Cek konfigurasi .env
   - Pastikan service MySQL berjalan
   - Verifikasi credentials database

2. Composer error
   - Hapus folder vendor
   - Jalankan `composer install` ulang
   - Clear cache: `php artisan cache:clear`

## 📈 Changelog

### v1.0.0 (29 Okt 2024)
- Sistem peminjaman dasar
- Manajemen denda
- Pencarian dan filter

## 👥 Kontribusi

1. Fork repository
2. Buat branch fitur (`git checkout -b fitur/AmazingFeature`)
3. Commit perubahan (`git commit -m 'Menambahkan fitur'`)
4. Push ke branch (`git push origin fitur/AmazingFeature`)
5. Buat Pull Request

## 📞 Support

- Email: grdgiven4@gmail.com
- WhatsApp: +62 813-1127-7725

## 📄 Lisensi

Dilindungi oleh lisensi MIT. Lihat `LICENSE` untuk informasi lebih lanjut.

## 🙏 Credit

Dikembangkan oleh [Given](https://github.com/MGT06)
Copyright © 2024