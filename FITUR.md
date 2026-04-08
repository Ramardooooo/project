# FITUR Sistem Informasi Kependudukan RT/RW

## 📋 Fitur Utama

### 1. **Manajemen Pengguna (Multi-Role)**
- **Admin**: Kelola users, announcements, gallery, RTRW, export audit logs
- **Ketua RT/RW**: Dashboard khusus, manage KK/warga/wilayah/mutasi, laporan, export
- **User/Warga**: Dashboard pribadi, input/edit data diri, daftar anggota KK, notifications

### 2. **Kartu Keluarga (KK) & Warga**
- Tambah/edit/hapus KK & anggota
- Daftar anggota keluarga dengan peran (Kepala Keluarga/Anggota)
- **Profile photos** next to names in KK list
- Status approval (menunggu/diterima/ditolak)
- Export KK to PDF (lengkap dengan tanda tangan)

### 3. **Manajemen Data Penduduk**
- Input lengkap: NIK, TTL, JK, goldar, agama, status kawin, pekerjaan, alamat, RT/RW/KK
- Mutasi warga (datang/pindah/meninggal)
- Status warga (aktif/tidak_aktif/meninggal/pindah)

### 4. **Dashboard & Laporan**
- **Ketua**: Stats KK/warga/mutasi, pending approvals, export Excel/PDF
- **User**: Data diri lengkap, daftar KK dengan foto & peran

### 5. **Gallery & Sosial**
- Upload/view gallery images
- Like/unlike gallery (API toggle_like.php)
- Modern responsive gallery UI

### 6. **Notifications System**
- Real-time notifications (API endpoints)
- Mark read/unread, mark all read

### 7. **Account Management**
- Profile settings (3 tabs: profile, account, preferences)
- Upload/edit profile photo (uploads/profiles/)
- Login/register/logout/auth protected

### 8. **Master Data**
- Manage RT/RW wilayah
- Activities/audit log

### 9. **UI/UX Modern**
- **Modern Glassmorphism Sidebar v2.0** (admin/ketua/user): Collapsible/responsive, profile avatars, active page highlighting, gradient glow hovers, notification badges, smooth scrollbar, backdrop-blur effects
- TailwindCSS, gradient designs
- Responsive tables, modals, pagination
- Dark mode ready, hover effects

### 10. **Tech Stack**
```
Backend: PHP + MySQL + Composer (Dompdf PDF)
Frontend: TailwindCSS + Alpine.js + HTMX
Auth: Session-based roles (admin/ketua/user)
API: REST endpoints (gallery, notifications, likes)
Security: Prepared statements, real_escape_string
```

## 🎯 SARAN FITUR TAMBAHAN RT/RW

### **Priority High (Must Have)**
1. **QR Code Warga** - Generate QR per NIK → scan untuk validasi cepat
2. **WhatsApp Integration** - Broadcast pengumuman via WA Business API
3. **Digital Signature** - E-sign KK/Surat Keterangan (HTML Canvas)
4. **Peta Interaktif** - Google Maps RT/RW boundary + marker warga

### **Priority Medium (Nice to Have)**
5. **iuran RT/RW** - Billing system, tagihan bulanan, reminder auto
6. **Survei Digital** - Google Form embedded, analitik hasil
7. **Event Calendar** - POSYANDU, rapat, kegiatan RT
8. **Chat RT/Warga** - Simple chat room per RT

### **Priority Low (Future)**
9. **AI Chatbot** - Tanya data warga via chat (GPT-3.5)
10. **Face Recognition** - Check-in warga event (via webcam)
11. **AR Home View** - Virtual tour RT via phone camera

### **Monetisasi**
- Premium RT: Rp50rb/bln (advanced features)
- Template Surat: Rp5rb/template
- Custom Domain: Rp100rb/tahun

## 🚀 Cara Pakai (Existing)
1. Login **user** → Input data diri → Daftar KK (dengan foto!)
2. **Ketua** approve → Manage KK/Warga/Mutasi
3. **Admin** → Full control + gallery/notifications

**Recent Updates:**
- PERAN KK: 'Kepala Keluarga' / 'Anggota'
- Profile photos tabel KK (w-10 h-10)
- Smart photo path via helpers.php
- Subtle scroll animations beranda
