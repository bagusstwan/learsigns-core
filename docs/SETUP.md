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

# URL Frontend React
FRONTEND_URL=http://localhost:5173
```

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
npm run dev                # Vite dev server
php artisan queue:listen   # Queue worker
```

### 6. Akses Aplikasi
- **API Backend:** `http://localhost:8000/api/v1/`
- **Panel Admin:** `http://localhost:8000/admin` (login menggunakan akun yang dibuat langsung di database)

## Seeder Data
Perintah `php artisan migrate --seed` akan menjalankan:
- **QuestSeeder:** 23 quest dengan 3 kategori level (Abjad, Kosa Kata, Kalimat).
- **UserFactory:** Membuat user default `test@example.com`.

> **Catatan:** ModuleSeeder tersedia tetapi tidak dijalankan secara default dari `DatabaseSeeder`. Jalankan secara manual jika diperlukan:
> ```bash
> php artisan db:seed --class=ModuleSeeder
> ```

## Pengujian
```bash
php artisan test --compact
```
Konfigurasi pengujian menggunakan SQLite in-memory (lihat `phpunit.xml`).

## Pemformatan Kode
```bash
vendor/bin/pint --dirty
```