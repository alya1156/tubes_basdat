# Pantai Beach Resort - Hotel Booking Management System

Sistem manajemen pemesanan hotel berbasis web yang dirancang khusus untuk **operasional administrasi pembayaran, verifikasi pembayaran, dan manajemen tamu**. Sistem ini merupakan aplikasi simpel dan fokus pada kebutuhan administrator hotel untuk mengelola pembayaran, tamu, dan reservasi.

## 🏨 Fitur Utama

### Admin Dashboard
- **Statistik Ringkas**: Menampilkan ringkasan operasional (total kamar, kamar terisi, pembayaran pending)
- **Akses Cepat**: Link langsung ke 3 fitur utama operasional

### 1. Verifikasi Pembayaran
- **Lihat daftar pembayaran** dengan search dan sorting
- **Verifikasi pembayaran** secara langsung (instant approval)
- **Tolak pembayaran** dengan alasan yang visible untuk pemesan
- **Status badges** dengan warna yang jelas (Pending, Verifikasi, Lunas, Ditolak)
- **Pencarian**: Cari berdasarkan kode booking atau nama tamu
- **Sorting**: Urutkan berdasarkan tanggal, status, atau jumlah pembayaran

### 2. Cek Kamar & Reservasi
- **Overview kamar dan reservasi** dengan status real-time
- **Pencarian**: Cari berdasarkan kode booking atau nama tamu
- **Sorting**: Urutkan berdasarkan check-in date, status, atau nama tamu
- **Detail reservasi**: Lihat informasi lengkap per reservasi

### 3. Manajemen Tamu
- **Daftar tamu** dengan pencarian nama/email/telepon
- **Tambah tamu baru** dengan validasi format email & telepon
- **Edit data tamu**: Update nama, identitas, email, telepon
- **Hapus tamu**: Hapus data tamu dari sistem
- **Sorting**: Urutkan berdasarkan nama atau tanggal terdaftar

## 📋 Persyaratan Sistem

- **PHP**: 7.4 atau lebih baru
- **MySQL/MariaDB**: 5.7 atau lebih baru
- **Web Server**: Apache/Nginx
- **Browser**: Modern browser dengan JavaScript enabled

## 🚀 Instalasi

### 1. Setup Database
```bash
# Buka phpMyAdmin atau command line MySQL
# Copy-paste isi schema.sql untuk membuat database dan tabel
mysql -u root -p < schema.sql
```

### 2. Konfigurasi
Edit file `includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Password database
define('DB_NAME', 'hotel_db');
```

### 3. Akses Admin
Tempatkan folder di folder web root (contoh: `htdocs/tubes_basdat/`)

**Login Admin**:
- URL: `http://localhost/tubes_basdat/admin/login.php`
- Username: `admin`
- Password: `1234`

## 📁 Struktur Folder

```
tubes_basdat/
├── admin/
│   ├── dashboard.php          # Dashboard admin
│   ├── login.php              # Halaman login
│   ├── process_login.php      # Proses verifikasi login
│   └── logout.php             # Logout
├── modules/
│   ├── tamu/
│   │   ├── list.php           # Daftar tamu (search & sort)
│   │   ├── tambah.php         # Tambah/edit tamu
│   │   ├── edit.php           # Edit tamu
│   │   └── delete.php         # Hapus tamu
│   ├── pembayaran/
│   │   ├── list.php           # Daftar pembayaran (search & sort)
│   │   └── verifikasi.php     # Verifikasi pembayaran
│   └── reservasi/
│       ├── list.php           # Daftar reservasi (search & sort)
│       └── detail.php         # Detail reservasi
├── includes/
│   ├── config.php             # Konfigurasi database & hotel
│   ├── functions.php          # Helper functions
│   └── auth.php               # Fungsi autentikasi
├── guest/
│   ├── booking.php            # Form booking tamu
│   ├── proses_booking.php     # Proses booking
│   ├── check_status.php       # Cek status booking
│   └── struk.php              # Bukti reservasi
├── index.php                  # Halaman home tamu
├── rooms.php                  # Daftar kamar untuk tamu
├── gallery.php                # Galeri hotel untuk tamu
├── detail_kamar.php           # Detail kamar untuk tamu
├── style.css                  # Stylesheet
├── schema.sql                 # Database schema & dummy data
└── README.md                  # Dokumentasi ini
```

## 🔑 Fitur Operasional

### Data Dummy Tersedia
Sistem sudah dilengkapi dengan data dummy untuk testing:
- **3 Tipe Kamar**: Standard Beach Room, Deluxe Ocean View, Premium Suite Sunset
- **10 Kamar**: Terdistribusi di 3 tipe dengan status variasi
- **15 Tamu**: Data tamu lengkap dengan kontak
- **8 Reservasi**: Reservasi dengan status mix (pending, konfirmasi, checked-in, checked-out)
- **8 Pembayaran**: Pembayaran dengan status variasi (pending, verifikasi, lunas, ditolak)

### Search & Sorting
Semua list page mendukung:
- **Search/Filter**: Pencarian real-time berdasarkan kolom tertentu
- **Sorting**: Klik header kolom untuk mengubah urutan (ASC/DESC)
- **Reset**: Tombol untuk reset filter dan sorting

### Status Badges
Setiap status ditampilkan dengan warna untuk visual clarity:
- 🟢 **Hijau** (Lunas/Checked-in/Konfirmasi)
- 🔵 **Biru** (Info/Verifikasi)
- 🟡 **Kuning** (Pending/Warning)
- 🔴 **Merah** (Ditolak/Danger)

### Validasi Form
- **Email**: Format email valid
- **No. Telepon**: Format Indonesia (08xxx-xxx-xxx atau +62xxx)
- **Field Wajib**: Nama, email, no. telepon (no. identitas opsional)

## 🔐 Keamanan

- **Password Hashing**: Password admin di-hash dengan bcrypt
- **SQL Injection Prevention**: Menggunakan prepared statements
- **XSS Prevention**: Input di-sanitize dengan htmlspecialchars()
- **Session Management**: Authentikasi via session

## 💡 Catatan Pengembangan

### Yang Dihapus (Simplified)
- ❌ Module Kamar Management (tambah/edit/hapus kamar)
- ❌ Module Tipe Kamar Management
- ❌ Module Fasilitas Management
- ❌ Module Gallery Upload
- ❌ Field Alamat di form tamu

### Yang Dipertahankan (Core Features)
- ✅ Guest booking & status checking
- ✅ Admin payment verification
- ✅ Guest management (CRUD)
- ✅ Reservation overview
- ✅ Search & sorting di semua list pages
- ✅ Status badges untuk visual clarity

## 📝 Default Login

**Admin Login**:
| Field | Value |
|-------|-------|
| Username | `admin` |
| Password | `1234` |

> ⚠️ **Penting**: Ganti password default sebelum production!

## 🐛 Troubleshooting

### Database Connection Error
- Pastikan MySQL running
- Cek konfigurasi di `includes/config.php`
- Cek username/password database

### Login Gagal
- Pastikan database sudah di-import (schema.sql)
- Default credentials: admin / 1234
- Clear browser cache jika tetap gagal

### Search/Sort Tidak Bekerja
- Pastikan form method = GET
- Check URL query parameters
- Cek browser console untuk JavaScript errors

## 📞 Support

Untuk pertanyaan atau bug report, silakan hubungi tim development.

---

**Last Updated**: January 2026  
**System Version**: 1.0 (Simplified Admin)
