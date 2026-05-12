# HR Management System - HR-OS

HR-OS adalah sistem manajemen sumber daya manusia (HRM) berbasis web yang dirancang untuk membantu perusahaan mengelola data karyawan, akses pengguna sistem, dan memantau statistik organisasi secara real-time. Dibangun menggunakan PHP Native dengan standar keamanan modern.

## 🚀 Fitur Utama

- **Dashboard Statistik**: Visualisasi data total karyawan, status aktif, jumlah pengguna sistem, dan rekrutmen terbaru.
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
  - **Session Security**: Autentikasi wajib untuk akses setiap modul.

## 🛠️ Tech Stack

- **Backend**: PHP 8.x (Native)
- **Database**: MySQL 8.0
- **Frontend**: Bootstrap 5.3, Bootstrap Icons
- **Infrastructure**: Docker & Docker Compose
- **Session/Cache**: Redis (Ready for scaling)

## 📁 Struktur Direktori

```text
├── auth/           # Modul Login, Logout, dan Authentication Handler
├── users/          # Modul Manajemen User (Sistem Akses)
├── employees/      # Modul Manajemen Pegawai (Direktori SDM)
├── includes/       # Komponen UI Reusable (Header, Sidebar, Navbar, Footer)
├── config/         # Konfigurasi Database dan Environment
├── assets/         # File Statis (CSS, JS, Images)
├── uploads/        # Penyimpanan Foto Profil Pegawai
└── docker/         # Konfigurasi Database Initialization
```

## ⚙️ Instalasi & Setup

### Prasyarat
- Docker dan Docker Compose terinstall di sistem Anda.

### Langkah-langkah
1. **Clone Repository**
   ```bash
   git clone <repository-url>
   cd php-native
   ```

2. **Setup Environment**
   Salin file `.env.example` menjadi `.env` dan sesuaikan jika diperlukan.
   ```bash
   cp env.example .env
   ```

3. **Jalankan Docker**
   Proses ini akan menjalankan container MySQL dan Redis secara otomatis.
   ```bash
   docker-compose up -d
   ```

4. **Jalankan PHP Development Server**
   ```bash
   php -S localhost:8081
   ```
   Akses aplikasi di: `http://localhost:8081`

### Kredensial Default
- **Email**: `admin@company.com`
- **Password**: `password` (Sudah di-hash dalam database)

## 🗄️ Database Schema

### Table: `users`
| Field | Type | Description |
|---|---|---|
| id | INT | Primary Key |
| name | VARCHAR | Nama lengkap pengguna |
| email | VARCHAR | Email untuk login (Unique) |
| password | VARCHAR | Bcrypt Hashed Password |
| role | ENUM | admin, manager, editor, viewer |

### Table: `employees`
| Field | Type | Description |
|---|---|---|
| id | INT | Primary Key |
| employee_id | VARCHAR | ID Karyawan Unik |
| full_name | VARCHAR | Nama lengkap |
| email | VARCHAR | Email perusahaan |
| status | ENUM | active, inactive, onboarding |
| photo_url | VARCHAR | Path foto di folder uploads/ |

---
