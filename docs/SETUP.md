# Panduan Instalasi (Setup & Installation)

## Prasyarat
- PHP >= 8.2
- Composer >= 2.0
- Node.js >= 18 & npm
- MySQL Server 8.0+

## Langkah Instalasi

### 1. Klon Repositori
```bash
git clone <URL_REPOSITORI> learnsigns-core
cd learnsigns-core
```

### 2. Instalasi Dependensi
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
Salin file `.env.example` menjadi `.env` lalu perbarui variabel berikut:
```env
# Basis Data
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=learnsigns_core
DB_USERNAME=root
DB_PASSWORD=

# Google OAuth2 (untuk fitur SSO)
GOOGLE_CLIENT_ID=kredensial_google_anda
GOOGLE_CLIENT_SECRET=rahasia_google_anda
GOOGLE_REDIRECT_URI=http://localhost:8000/api/v1/auth/google/callback

# URL Frontend React (digunakan oleh Google SSO callback untuk redirect)
FRONTEND_URL=http://localhost:5173
```

> **Catatan:** Variabel `FRONTEND_URL` digunakan oleh `AuthController::handleGoogleCallback()` untuk mengarahkan pengguna kembali ke klien React setelah autentikasi Google berhasil. Pastikan nilai ini sesuai dengan URL dev server frontend Vite.

### 4. Inisialisasi Sistem
```bash
php artisan key:generate
php artisan migrate --seed
```

### 5. Menjalankan Server Pengembangan

**Opsi A:** Menjalankan semua service sekaligus (direkomendasikan):
```bash
composer run dev
```
Perintah ini menjalankan Laravel server, queue listener, dan Vite secara bersamaan.

**Opsi B:** Menjalankan secara terpisah:
```bash
php artisan serve          # Backend API (port 8000)
npm run dev                # Vite dev server (aset admin)
php artisan queue:listen   # Queue worker
```

### 6. Akses Aplikasi
- **API Backend:** `http://localhost:8000/api/v1/`
- **Panel Admin:** `http://localhost:8000/admin`
- **Model AI:** `http://localhost:8000/serve-ai/{folder}/model.json`

> **Akses Panel Admin:** FilamentPHP memerlukan akun pengguna untuk login. Saat ini tidak ada seeder khusus untuk admin. Buat akun admin secara manual melalui `php artisan tinker`:
> ```php
> \App\Models\User::create([
>     'name' => 'Administrator',
>     'email' => 'admin@learnsigns.test',
>     'password' => bcrypt('password'),
>     'role' => 'teacher',
> ]);
> ```

## Seeder Data
Perintah `php artisan migrate --seed` akan menjalankan:
- **QuestSeeder:** 23 quest dengan 3 kategori level (Abjad: 50-75 bintang, Kosa Kata: 100-125 bintang, Kalimat: 200-300 bintang).
- **UserFactory:** Membuat user default `test@example.com`.

> **Catatan:** ModuleSeeder tersedia tetapi tidak dijalankan secara default dari `DatabaseSeeder`. Jalankan secara manual jika diperlukan:
> ```bash
> php artisan db:seed --class=ModuleSeeder
> ```
> ModuleSeeder membuat 49 modul pembelajaran (Abjad A-Z, Kosa Kata, dan Kalimat).

## Konfigurasi CORS

File `config/cors.php` dikonfigurasi untuk mengizinkan komunikasi dengan frontend React:
- **Allowed Origins:** `http://localhost:5173`
- **Supports Credentials:** `true`

Rute `/serve-ai` menggunakan header CORS manual (`Access-Control-Allow-Origin: *`) karena berada di luar konfigurasi CORS Laravel standar.

## Pengujian
```bash
php artisan test --compact
```
Konfigurasi pengujian menggunakan SQLite in-memory (lihat `phpunit.xml`).

## Pemformatan Kode
```bash
vendor/bin/pint --dirty
```