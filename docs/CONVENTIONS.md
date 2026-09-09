# Konvensi Kode (Coding Conventions)

Proyek ini mematuhi standar kode bersih (clean code) untuk memastikan skalabilitas.

## 1. Standar Penulisan PHP
*   Mematuhi standar **PSR-12**.
*   Gunakan deklarasi tipe data yang ketat (`strict_types=1`) pada setiap awal file.
*   Gunakan deklarasi tipe untuk argumen fungsi dan nilai kembalian.

## 2. Arsitektur (Fat Model, Skinny Controller)
*   **Controller:** Hanya bertugas memvalidasi permintaan HTTP, memanggil Service, dan mengembalikan JSON.
*   **Service Class:** Letakkan logika bisnis (contoh: kalkulasi poin) di dalam `app/Services/`.
*   **Model:** Hanya berisi relasi tabel, mutator, dan ruang lingkup kueri (Query Scopes).

## 3. Penamaan (Naming Conventions)
*   **Tabel Database:** *Snake case*, jamak (`user_modules`).
*   **Model:** *Pascal case*, tunggal (`UserModule`).
*   **Variabel/Fungsi:** *Camel case* (`calculateStars()`).