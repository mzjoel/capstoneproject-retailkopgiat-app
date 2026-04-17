# Retail Koperasi Giat

Retail Koperasi Giat adalah aplikasi manajemen ritel modern yang dirancang khusus untuk kebutuhan operasional koperasi. Aplikasi ini dibangun untuk mempermudah pengelolaan stok, transaksi, dan pelaporan dengan antarmuka yang responsif dan performa yang tinggi.

## 🏗️ Struktur Project

Project ini menggunakan arsitektur **Monolith** dengan pemisahan layer yang jelas antara Backend dan Frontend menggunakan **Inertia.js**.

- **Backend (Laravel)**:
    - 📂 `app/`: Berisi logika inti aplikasi (Models, Controllers, Services).
    - 📂 `routes/`: Definisi routing aplikasi (Web, API).
    - 📂 `database/`: Migrasi database, factories, dan seeders.
    - 📂 `config/`: Konfigurasi sistem.

- **Frontend (Vue.js + Inertia)**:
    - 📂 `resources/js/`: Direktori utama frontend.
    - 📂 `resources/js/Pages/`: Komponen Vue 3 untuk setiap halaman aplikasi.
    - 📂 `resources/css/`: File styling utama menggunakan Tailwind CSS.
    - 📂 `resources/views/app.blade.php`: Entry point (Main Layout) untuk Inertia.

---

## 📦 Dependencies

### Backend (PHP/Laravel)
- **PHP**: ^8.3
- **Laravel**: ^13.0
- **Inertia Laravel**: ^3.0 (Bridge antara Laravel dan Vue)
- **AWS SDK**: ^3.379 (Untuk penyimpanan cloud/S3)
- **Predis**: ^2.4 (Untuk caching & session menggunakan Redis)

### Frontend (JavaScript/Vue)
- **Vue.js**: ^3.5.32
- **Inertia Vue 3**: ^3.0.3
- **Tailwind CSS**: ^4.0.0 (Framework CSS modern)
- **Vite**: ^8.0.0 (Build tool super cepat)

---

## 🛠️ Step Setup Project

Ikuti langkah-langkah di bawah ini untuk menjalankan project di lingkungan lokal:

### 1. Clone Project
```bash
git clone https://github.com/username/capstone-ritelkopgiat-app.git
cd capstone-ritelkopgiat-app
```

### 2. Setup Otomatis (Instalasi & Environment)
Project ini menyediakan script setup otomatis yang menangani:
- Instalasi dependencies PHP (`composer install`)
- Pembuatan file `.env`
- Generate `APP_KEY`
- Database Migration
- Instalasi dependencies JS (`npm install`)
- Frontend Build (`npm run build`)

Jalankan perintah berikut:
```bash
composer setup
```

### 3. Konfigurasi Database (Opsional)
Secara default, project ini menggunakan **SQLite**. Jika ingin mengubahnya, silakan edit file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
...
```

### 4. Menjalankan Aplikasi
Untuk menjalankan server backend dan bundle frontend secara bersamaan dalam mode development:

```bash
# Menjalankan Laravel Development Server (port 9000 by default)
php artisan serve --port=9000

# Menjalankan Vite (untuk Hot Module Replacement/HMR)
npm run dev
```

Akses aplikasi di: [http://localhost:9000](http://localhost:9000)

---

## 🚀 Deployment
Untuk build produksi, jalankan:
```bash
npm run build
```
Pastikan semua konfigurasi environment telah disesuaikan dengan server produksi.
