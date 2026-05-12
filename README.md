# HR Management System - HR-OS

HR-OS adalah sistem manajemen sumber daya manusia (HRM) berbasis web yang dirancang untuk membantu perusahaan mengelola data karyawan, akses pengguna sistem, dan memantau statistik organisasi secara real-time. Dibangun menggunakan PHP Native dengan standar keamanan modern dan integrasi WebSocket untuk notifikasi instan.

## 🚀 Fitur Utama

- **Dashboard Statistik**: Visualisasi data total karyawan, status aktif, jumlah pengguna sistem, dan rekrutmen terbaru.
- **Real-time Notifications**: Notifikasi instan via WebSocket (Socket.io) saat ada aktivitas penambahan data karyawan.
- **Manajemen Karyawan (CRUD)**:
  - Pencarian dan filter dinamis (Departemen & Status).
  - Upload foto profil dengan validasi (Max 300KB, JPEG/JPG).
  - Manajemen status (Active, Inactive, Onboarding).
  - Soft delete untuk keamanan data.
- **Manajemen Pengguna Sistem**:
  - Pengaturan hak akses (Admin, Manager, Editor, Viewer).
  - Sistem autentikasi aman dengan Bcrypt hashing.
- **Keamanan (Security)**:
  - **CSRF Protection**: Perlindungan terhadap serangan Cross-Site Request Forgery di semua form.
  - **SQL Injection Prevention**: Menggunakan PDO Prepared Statements untuk semua query database.
  - **XSS Protection**: Sanitasi input untuk mencegah Cross-Site Scripting.

## 🛠️ Tech Stack

- **Backend**: PHP 8.x (Native)
- **Database**: MySQL 8.0
- **Realtime Server**: Node.js, Express, Socket.io
- **Session/Cache**: Redis
- **Frontend**: Bootstrap 5.3, Bootstrap Icons, Socket.io Client

## 📂 Struktur Direktori

```text
├── auth/           # Modul Login & Authentication
├── users/          # Modul Manajemen User (Akses Sistem)
├── employees/      # Modul Manajemen Pegawai (Direktori SDM)
├── includes/       # Komponen UI Reusable (Header, Footer, Sidebar)
├── config/         # Konfigurasi Database & Redis
├── assets/         # File Statis (CSS Modern, JS, Images)
├── realtime-server/# Server Node.js untuk WebSocket
└── uploads/        # Penyimpanan Foto Profil Pegawai
```

## ⚙️ Instalasi & Setup

### Prasyarat
- Docker dan Docker Compose terinstall.
- Node.js (v16+) dan NPM terinstall.

### Langkah-langkah

1. **Clone Repository & Setup Env**
   ```bash
   git clone <repository-url>
   cp .env.example .env
   ```

2. **Jalankan Infrastruktur (MySQL & Redis)**
   ```bash
   docker-compose up -d
   ```

3. **Jalankan PHP Development Server**
   ```bash
   php -S localhost:8081
   ```

4. **Jalankan Realtime Notification Server**
   ```bash
   cd realtime-server
   npm install
   node server.js
   ```
   *Note: Pastikan port 3001 tersedia untuk Node.js.*

## 🔄 Arsitektur Notifikasi Real-time
Sistem menggunakan pola **HTTP Bridge** untuk memicu notifikasi WebSocket:
1. **PHP Action**: Setelah `INSERT` data ke MySQL, PHP mengirim `POST` request ke Node.js.
2. **Node.js Bridge**: Server Node.js menerima request dan melakukan `io.emit()` ke semua client.
3. **Frontend**: Client-side JS menangkap event dan menampilkan Bootstrap Toast secara instan.

---
**HR-OS** - *Modern HR Solution with Real-time Capabilities*
