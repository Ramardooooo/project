🏘️ RT/RW Management System

Website ini adalah sistem manajemen RT/RW berbasis web untuk mengelola data warga, administrasi, dan aktivitas lingkungan secara digital.


📌 Fitur Utama:
- Manajemen Data Warga (CRUD)
- Pendataan KK (Kartu Keluarga)
- Surat Menyurat (opsional)
- 🔐 Sistem Login & Role (Admin / Ketua / User)
- Dashboard Statistik
- Riwayat Data
- 📢 Pengumuman Warga
- Panel Admin


🖥️ Teknologi yang Digunakan:
- Frontend: HTML, Talwind CSS, JavaScript
- Backend: PHP Native
- Database: MySQL
- Server: Apache (XAMPP / Laragon)


📂 Struktur Folder:
project/
 ┣ account/     → halaman akun user
 ┣ api/         → endpoint API untuk like dan notif
 ┣ auth/        → sistem login & register
 ┣ beranda/     → dashboard / homepage
 ┣ config/      → konfigurasi database
 ┣ layouts/     → template UI (header, footer, dll)
 ┣ pages/       → halaman utama sistem
 ┣ static/      → CSS, JS, assets
 ┣ uploads/     → file upload user
 ┣ vendor/      → library tambahan (composer, dll)


⚙️ Cara Install

1. Clone repository
   git clone https://github.com/username/nama-project.git

2. Pindahkan ke folder server
   - XAMPP → htdocs
   - Laragon → www

3. Import database
   - Buat database (misal: rt_rw)
   - Import file .sql

4. Setting koneksi database
   Edit config/database.php

   mysqli_connect("localhost", "root", "", "rt_rw");

5. Jalankan project
   http://localhost/nama-project


👤 Role User
Admin  : Full akses sistem  
Ketua : Kelola data warga  
User   : Lihat data  


🔐 Keamanan
- Password menggunakan password_hash()
- Validasi input dasar
- Session login


🚀 Pengembangan Selanjutnya
- Notifikasi WhatsApp
- Export Excel
- API Integration
- Mobile responsive
- Sistem iuran warga


🤝 Kontribusi
Pull request terbuka untuk pengembangan project ini.

👨‍💻 Developer
Dibuat oleh Ramardo
