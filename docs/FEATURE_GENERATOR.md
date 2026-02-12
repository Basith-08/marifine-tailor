# Dokumentasi Perintah Artisan Kustom: `make:feature`

Perintah `make:feature` adalah sebuah skrip kustom yang dibuat untuk mengotomatiskan dan mempercepat proses pembuatan fitur baru dengan struktur CRUD (Create, Read, Update, Delete), Service Layer, dan Form Request di dalam proyek ini.

## Tujuan

Tujuan utama dari perintah ini adalah untuk mengurangi pekerjaan manual berulang dengan secara otomatis men-generate semua file boilerplate yang diperlukan untuk sebuah fitur, sehingga developer bisa langsung fokus pada logika bisnis dan pengembangan antarmuka pengguna (UI).

## Cara Penggunaan

Gunakan perintah ini di terminal dari direktori root proyek Anda:

```bash
php artisan make:feature {nama_fitur}
```

### Argumen
-   `{nama_fitur}`: **Wajib.** Nama dari fitur yang ingin Anda buat. Sangat disarankan untuk menggunakan format *Singular* dan *PascalCase* (contoh: `Product`, `Order`, `SalesInvoice`).

### Contoh
```bash
php artisan make:feature Product
```

## Apa yang Dilakukan Perintah Ini?

Saat Anda menjalankan `php artisan make:feature Product`, perintah ini akan melakukan serangkaian tindakan berikut:

1.  **Membuat Model dan File Migrasi:**
    -   Membuat file Model di `app/Models/Product.php`.
    -   Membuat file Migrasi di `database/migrations/xxxx_xx_xx_xxxxxx_create_products_table.php`.

2.  **Membuat Service Class:**
    -   Membuat file Service di `app/Services/ProductService.php`.
    -   Service ini berfungsi sebagai lapisan untuk logika bisnis utama fitur, dengan metode boilerplate seperti `store()`, `update()`, dan `destroy()`.

3.  **Membuat Form Request Class:**
    -   Membuat direktori baru di `app/Http/Requests/Product/`.
    -   Di dalam direktori tersebut, dibuat dua file Form Request dengan *boilerplate* dasar:
        -   `StoreRequest.php`: Untuk validasi data saat membuat entitas baru.
        -   `UpdateRequest.php`: Untuk validasi data saat memperbarui entitas yang sudah ada.

4.  **Membuat Resource Controller:**
    -   Membuat file Controller di `app/Http/Controllers/ProductController.php`.
    -   Controller ini sudah dilengkapi dengan metode-metode standar untuk operasi CRUD: `index`, `create`, `store`, `show`, `edit`, `update`, dan `destroy`. Controller ini akan menjadi lapisan tipis yang berinteraksi dengan `ProductService` dan menggunakan Form Request untuk validasi.

5.  **Membuat Halaman Vue:**
    -   Membuat direktori baru di `resources/js/pages/Product/`.
    -   Di dalam direktori tersebut, dibuat tiga file Vue dengan *boilerplate* dasar:
        -   `Index.vue`: Untuk menampilkan daftar data (misalnya, daftar produk).
        -   `Create.vue`: Untuk menampilkan formulir pembuatan data baru.
        -   `Edit.vue`: Untuk menampilkan formulir pengeditan data yang sudah ada.

6.  **Menambahkan Resource Route:**
    -   Secara otomatis menambahkan baris rute resource baru ke bagian akhir file `routes/web.php`.
        ```php
        Route::resource('/products', App\Http\Controllers\ProductController::class);
        ```

## Langkah Selanjutnya Setelah Menjalankan Perintah

Setelah perintah berhasil dieksekusi, alur kerja Anda adalah sebagai berikut:

1.  **Definisikan Skema Database:**
    -   Buka file migrasi yang baru dibuat di `database/migrations/`.
    -   Definisikan kolom-kolom yang diperlukan untuk tabel Anda di dalam metode `up()`.
    -   Jalankan `php artisan migrate` untuk membuat tabel di database.

2.  **Definisikan Aturan Validasi:**
    -   Buka `app/Http/Requests/{NamaFitur}/StoreRequest.php` dan `UpdateRequest.php`.
    -   Tambahkan aturan validasi yang sesuai di metode `rules()` untuk memastikan integritas data.

3.  **Implementasikan Logika Bisnis:**
    -   Buka `app/Services/{NamaFitur}Service.php`.
    -   Implementasikan logika `store`, `update`, dan `destroy` sesuai kebutuhan fitur Anda.

4.  **Kustomisasi Controller:**
    -   Buka `app/Http/Controllers/{NamaFitur}Controller.php`.
    -   Injeksi `ProductService` ke controller.
    -   Gunakan Form Request (`StoreRequest`, `UpdateRequest`) untuk validasi input dan panggil metode dari `ProductService` untuk menjalankan logika bisnis.

5.  **Kembangkan Antarmuka Pengguna (UI):**
    -   Buka file-file Vue di `resources/js/pages/{NamaFitur}/`.
    -   Bangun tampilan dan fungsionalitas halaman menggunakan komponen-komponen yang sudah ada di `resources/js/components/`.
