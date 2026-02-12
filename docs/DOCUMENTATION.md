# Dokumentasi Proyek Marifine Tailor

Dokumen ini memberikan gambaran umum tentang proyek Marifine Tailor, teknologi yang digunakan, dan cara menjalankan aplikasi secara lokal.

## Gambaran Umum

Proyek ini adalah sebuah starter kit aplikasi web modern yang dibangun di atas tumpukan teknologi yang solid, menyediakan fondasi yang kuat untuk pengembangan aplikasi lebih lanjut. Arsitekturnya menggunakan Laravel sebagai backend monolitik yang terhubung dengan frontend JavaScript modern menggunakan Inertia.js.

Backend mengontrol semua routing dan penyajian halaman, sementara frontend dibangun sebagai *Single-Page Application* (SPA) menggunakan Vue.js.

## Tumpukan Teknologi

### Backend
- **Framework**: Laravel 12
- **Autentikasi**: Laravel Fortify, menyediakan fitur seperti registrasi, login, reset kata sandi, dan autentikasi dua faktor (2FA).
- **Jembatan Backend-Frontend**: Inertia.js (`inertiajs/inertia-laravel`)

### Frontend
- **Framework**: Vue 3 dengan TypeScript
- **Bundler**: Vite
- **Styling**: Tailwind CSS, `clsx`, dan `tailwind-merge` untuk styling komponen yang fleksibel.
- **Ikon**: `lucide-vue-next`

## Struktur Kode

- **Rute Laravel**: Didefinisikan di dalam direktori `routes/`.
  - `web.php`: Rute utama aplikasi.
  - `settings.php`: Rute khusus untuk halaman pengaturan pengguna.
- **Komponen Vue**: Berada di `resources/js/`.
  - `pages/`: Komponen tingkat atas yang berfungsi sebagai halaman.
  - `components/`: Komponen UI yang dapat digunakan kembali (misalnya, tombol, input, modal).
  - `layouts/`: Komponen tata letak untuk halaman yang berbeda (misalnya, tata letak utama aplikasi dan tata letak untuk halaman autentikasi).

## Fungsionalitas Inti

Aplikasi ini berfungsi sebagai boilerplate dengan fitur-fitur utama yang berpusat pada pengguna:
- **Autentikasi Pengguna**: Login, registrasi, reset kata sandi, dan 2FA.
- **Dasbor Pengguna**: Halaman utama setelah pengguna login.
- **Pengaturan Pengguna**:
  - Memperbarui profil (`ProfileController`).
  - Mengubah kata sandi (`PasswordController`).
  - Mengelola autentikasi dua faktor.
  - Mengubah tema/tampilan aplikasi.

## Menjalankan Aplikasi Secara Lokal

Untuk menjalankan aplikasi ini, Anda perlu menyiapkan database dan server aplikasi.

### 1. Menjalankan Database

Proyek ini menggunakan Docker untuk menjalankan database PostgreSQL.
- Salin file `.env.example` menjadi `.env`.
- Sesuaikan kredensial database di file `.env` agar cocok dengan yang ada di `docker-compose.yaml` (DB: `laravel_db`, User: `laravel_user`, Pass: `secret`).
- Jalankan perintah berikut untuk memulai kontainer database:
  ```bash
  docker-compose up -d
  ```

### 2. Instalasi Dependensi

- **Backend (PHP)**:
  ```bash
  composer install
  ```
- **Frontend (Node.js)**:
  ```bash
  npm install
  ```

### 3. Migrasi Database & Key Generation

- Jalankan migrasi untuk membuat skema database:
  ```bash
  php artisan migrate
  ```
- Buat kunci aplikasi:
  ```bash
  php artisan key:generate
  ```

### 4. Menjalankan Server Pengembangan

Proyek ini dikonfigurasi untuk menjalankan server backend (Laravel) dan frontend (Vite) secara bersamaan dengan satu perintah:

```bash
composer run dev
```

Perintah ini akan:
- Menjalankan server pengembangan PHP (`php artisan serve`).
- Menjalankan server pengembangan Vite untuk *hot-reloading* aset frontend.
- Menjalankan *queue listener* dan *log viewer*.

Setelah server berjalan, aplikasi akan dapat diakses di alamat yang ditampilkan di terminal (biasanya `http://127.0.0.1:8000`).

## Fokus Pengembangan Fitur dan Pembaruan Kode

Untuk menambah fitur baru atau memperbarui kode, fokus utama akan terbagi antara backend (Laravel) dan frontend (Vue.js).

### 1. Backend (Logika dan Data - di direktori `app` dan `routes`)

Jika fitur Anda memerlukan **logika bisnis baru, interaksi database, atau endpoint baru**, Anda akan bekerja di sini.

-   **Rute (`routes/web.php` atau `routes/settings.php`):**
    -   Langkah pertama adalah mendefinisikan URL atau *endpoint* baru untuk fitur Anda. Rute ini akan menghubungkan URL ke sebuah metode di *Controller*.

-   **Controller (`app/Http/Controllers/`):**
    -   Di sinilah logika utama berada. *Controller* akan mengambil data dari *Model*, memprosesnya, dan kemudian menampilkan halaman Vue menggunakan `Inertia::render()`. Anda kemungkinan besar akan membuat *Controller* baru untuk fitur baru.

-   **Model (`app/Models/`):**
    -   Jika fitur Anda memerlukan data baru (misalnya, tabel `products`), Anda akan membuat *Model* baru di sini. Model ini yang bertanggung jawab untuk berinteraksi dengan tabel database.

-   **Migrasi Database (`database/migrations/`):**
    -   Untuk setiap *Model* atau perubahan struktur tabel, Anda harus membuat file migrasi baru untuk mendefinisikan skema tabel tersebut.

### 2. Frontend (Tampilan dan Interaksi - di direktori `resources/js`)

Jika Anda ingin **membangun antarmuka pengguna (UI)** untuk fitur Anda, fokus Anda akan ada di sini.

-   **Halaman (`resources/js/pages/`):**
    -   Ini adalah komponen Vue yang berfungsi sebagai halaman penuh. Nama file di sini harus cocok dengan yang Anda panggil di `Inertia::render()` dari *Controller* Anda. Data yang dikirim dari *Controller* akan diterima sebagai `props` di komponen halaman ini.

-   **Komponen (`resources/js/components/`):**
    -   Untuk menjaga kode tetap bersih, pecah UI Anda menjadi komponen-komponen kecil yang dapat digunakan kembali (seperti tombol, form, kartu, dll.). Letakkan semua komponen tersebut di sini.

-   **Tata Letak (`resources/js/layouts/`):**
    -   Sebagian besar halaman Anda mungkin akan menggunakan `AppLayout.vue`. Jika fitur baru Anda memerlukan tata letak yang sama sekali berbeda, Anda bisa membuat file tata letak baru.

---

## Alur Kerja Pengembangan

Untuk mempercepat pengembangan fitur CRUD (Create, Read, Update, Delete), proyek ini menyediakan perintah Artisan kustom.

### Perintah `make:feature`

Perintah ini secara otomatis menghasilkan semua file yang diperlukan untuk memulai sebuah fitur baru—mulai dari Model dan Controller di backend hingga halaman-halaman Vue di frontend.

-   **Untuk dokumentasi detail mengenai perintah ini**, silakan lihat file [**FEATURE_GENERATOR.md**](./FEATURE_GENERATOR.md).
-   **Untuk panduan langkah demi langkah menggunakan perintah ini dalam kasus nyata**, silakan ikuti tutorial di [**Customer.md**](./Customer.md).

