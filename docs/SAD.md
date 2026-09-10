# Dokumen Arsitektur Perangkat Lunak (Software Architecture Document)

## Ringkasan Arsitektur
`learnsigns-core` diimplementasikan sebagai **Stateless RESTful API** terpusat. Peladen tidak merender tampilan HTML secara langsung (kecuali rute `/admin` untuk panel FilamentPHP dan `/serve-ai` untuk aset model AI), melainkan mendistribusikan data mentah via HTTP/HTTPS ke klien React Frontend (`learn-signs`).

## Tumpukan Teknologi
| Komponen | Teknologi | Versi |
|---|---|---|
| Framework | Laravel | 12.x |
| Bahasa | PHP | >= 8.2 |
| Basis Data | MySQL | 8.0+ |
| Autentikasi Token | Laravel Sanctum | 4.x |
| OAuth2 SSO | Laravel Socialite | 5.x |
| Panel Admin | FilamentPHP | 3.x |
| Frontend Bundler | Vite + Tailwind CSS | 4.x |
| Testing | Pest | 3.x |
| Code Formatter | Laravel Pint | 1.x |

## Komponen Infrastruktur
1. **Aplikasi Inti:** Laravel 12.x (PHP 8.2+) dengan struktur Laravel 12 (tanpa `Kernel.php`, middleware dikonfigurasi di `bootstrap/app.php`).
2. **Basis Data:** MySQL 8.0+ dengan 7 tabel domain utama dan 1 tabel sistem autentikasi (total 8 tabel).
3. **Autentikasi Terdistribusi:** Laravel Sanctum (Bearer Token, tanpa state di server). Token diberi nama `viba-auth-token`.
4. **SSO Google:** Laravel Socialite dengan mode `stateless()` dan auto-register (pengguna baru otomatis didaftarkan saat login pertama via Google).
5. **Panel Admin:** Terisolasi pada rute `/admin` menggunakan FilamentPHP v3.
6. **CORS:** Dikonfigurasi melalui `config/cors.php` untuk mengizinkan `localhost:5173` dengan `supports_credentials: true`.
7. **Penyajian Model AI:** Rute khusus di `routes/web.php` dengan header CORS manual (`Access-Control-Allow-Origin: *`). File model disajikan dari `public/ai-models/`.

## Tabel Basis Data

| Tabel | Kategori | Model Eloquent | Catatan |
|---|---|---|---|
| `users` | Domain | `User` | Pengguna dengan 3 peran: `student`, `teacher`, `corporate`. |
| `modules` | Domain | `Module` | Modul pembelajaran bertingkat (abjad, kata, kalimat). |
| `quests` | Domain | `Quest` | Misi harian dengan hadiah bintang. |
| `student_progress` | Domain | `StudentProgress` | Progres per siswa per modul. UNIQUE(user_id, module_id). |
| `user_quests` | Domain | `UserQuest` | Riwayat penyelesaian quest. UNIQUE(user_id, quest_id). |
| `dataset_records` | Domain | `DatasetRecord` | Rekaman koordinat MediaPipe untuk pelatihan model AI. |
| `assignments` | Domain | **Tidak ada** | Tugas dari pendidik ke siswa. Diakses via `DB::table()` (Query Builder). |
| `personal_access_tokens` | Sistem | - (Sanctum) | Token autentikasi. Dikelola otomatis oleh Laravel Sanctum. |

> **Catatan Arsitektural:** Tabel `assignments` tidak memiliki Model Eloquent. Seluruh operasi CRUD dilakukan melalui `DB::table('assignments')` di `EducatorController`. Pola `Schema::hasTable('assignments')` digunakan sebagai guard sebelum mengakses tabel ini.

## Struktur Direktori Utama
```
app/
├── Filament/
│   ├── Pages/          # AiTestingStudio, DatasetRecorder
│   ├── Resources/      # ModuleResource, QuestResource (CRUD admin)
│   └── Widgets/        # StatsOverview, UserRoleChart, TopStudentsTable, LatestUsersTable
├── Http/Controllers/
│   ├── Controller.php              # Base controller
│   └── Api/
│       ├── ModuleController.php          # Endpoint publik modul
│       └── v1/
│           ├── AuthController.php        # Register, Login, Logout, Google SSO
│           ├── StudentProgressController.php  # Rekam progres & statistik dashboard
│           ├── QuestController.php       # Daftar quest & penyelesaian misi
│           ├── ProfileController.php     # Profil pengguna & tier ranking
│           ├── LeaderboardController.php # Papan peringkat 50 teratas
│           ├── SettingsController.php    # Ubah kata sandi
│           └── EducatorController.php    # Dashboard pendidik & manajemen siswa
├── Models/             # User, Module, Quest, StudentProgress, DatasetRecord, UserQuest (6 model)
└── Providers/
    ├── AppServiceProvider.php
    └── Filament/AdminPanelProvider.php

database/
├── migrations/         # 13 file migrasi
├── factories/          # UserFactory
└── seeders/            # DatabaseSeeder, ModuleSeeder, QuestSeeder

routes/
├── api.php             # Seluruh rute API (prefix /api/v1)
├── web.php             # Rute admin FilamentPHP & penyajian model AI (/serve-ai)
└── console.php         # Perintah konsol

docs/                   # Dokumentasi teknis proyek
```

## Alur Data (Request Lifecycle)
1. Klien React mengirim request HTTP beserta Bearer Token melalui header `Authorization`.
2. Laravel Sanctum memvalidasi integritas token via middleware `auth:sanctum`.
3. Controller melakukan validasi input secara inline (`$request->validate()`).
4. Model berinteraksi dengan basis data via Eloquent ORM, atau `DB::table()` untuk tabel `assignments`.
5. Respons dikembalikan ke klien dalam format JSON standar (`status`, `data`/`message`).

## Data Kalkulasi Runtime

Beberapa data yang dikembalikan oleh API tidak tersimpan di basis data, melainkan dihitung secara dinamis saat runtime:

| Data | Sumber Kalkulasi | Controller |
|---|---|---|
| Tier (Bronze, Silver, ...) | Kalkulasi bertingkat dari `users.stars` | `ProfileController`, `LeaderboardController` |
| Rank (Peringkat Global) | `COUNT` siswa dengan bintang lebih tinggi + 1 | `ProfileController` |
| Win Rate | `AVG(accuracy)` dari `student_progress` | `ProfileController`, `LeaderboardController` |
| Inisial Nama | 2 huruf pertama dari kata-kata dalam nama | `EducatorController`, `LeaderboardController` |
| Statistik Mingguan | Agregasi 7 hari terakhir dari `student_progress`/`assignments` | `StudentProgressController` |

## Strategi Keamanan
- **Rate Limiting:** Implementasi manual pada endpoint login menggunakan `RateLimiter` facade (5 percobaan per email+IP). Kunci throttle: `transliterate(lower(email))|ip`.
- **Anti-Enumeration:** Pesan error login yang seragam ("Kredensial tidak valid") untuk email tidak ditemukan maupun password salah.
- **Token Revocation:** Penghancuran token absolut dari database saat logout via `currentAccessToken()->delete()`.
- **Mass Assignment Protection:** Menggunakan `$fillable` pada semua model (kecuali Module yang menggunakan `$guarded = []`).
- **Database Transaction:** Digunakan pada operasi evaluasi tugas (`evaluateTask`, `liveEvaluate`) untuk menjamin konsistensi data saat memperbarui assignment dan mendistribusikan bintang secara bersamaan.
- **CORS:** Konfigurasi ketat dengan `supports_credentials: true` untuk komunikasi SPA.