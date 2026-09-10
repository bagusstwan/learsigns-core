# Dokumen Arsitektur Perangkat Lunak (Software Architecture Document)

## Ringkasan Arsitektur
`learnsigns-core` diimplementasikan sebagai **Stateless RESTful API** terpusat. Peladen tidak merender tampilan HTML secara langsung (kecuali rute `/admin` untuk panel FilamentPHP dan `/serve-ai` untuk aset model AI), melainkan mendistribusikan data mentah via HTTP/HTTPS ke klien React Frontend.

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
1. **Aplikasi Inti:** Laravel 12.x (PHP 8.2+) dengan struktur Laravel 12 (tanpa `Kernel.php`).
2. **Basis Data:** MySQL 8.0+ dengan 8 tabel domain utama.
3. **Autentikasi Terdistribusi:** Laravel Sanctum (Bearer Token, tanpa state di server).
4. **SSO Google:** Laravel Socialite dengan mode `stateless()` dan auto-register.
5. **Panel Admin:** Terisolasi pada rute `/admin` menggunakan FilamentPHP v3.
6. **CORS:** Dikonfigurasi melalui `config/cors.php` untuk mengizinkan `localhost:5173`.
7. **Penyajian Model AI:** Rute khusus di `routes/web.php` dengan header CORS manual.

## Struktur Direktori Utama
```
app/
├── Filament/
│   ├── Pages/          # AiTestingStudio, DatasetRecorder
│   ├── Resources/      # ModuleResource, QuestResource (CRUD)
│   └── Widgets/        # StatsOverview, UserRoleChart, TopStudentsTable, LatestUsersTable
├── Http/Controllers/
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
├── Models/             # User, Module, Quest, StudentProgress, DatasetRecord, UserQuest
└── Providers/          # AppServiceProvider, Filament/AdminPanelProvider
```

## Alur Data (Request Lifecycle)
1. Klien React mengirim request HTTP beserta Bearer Token melalui header `Authorization`.
2. Laravel Sanctum memvalidasi integritas token via middleware `auth:sanctum`.
3. Controller melakukan validasi input secara inline (`$request->validate()`).
4. Model berinteraksi dengan basis data via Eloquent ORM atau Query Builder (`DB::table`).
5. Respons dikembalikan ke klien dalam format JSON standar (`status`, `data`/`message`).

## Strategi Keamanan
- **Rate Limiting:** Implementasi manual pada endpoint login menggunakan `RateLimiter` facade (5 percobaan per email+IP).
- **Anti-Enumeration:** Pesan error login yang seragam untuk email tidak ditemukan maupun password salah.
- **Token Revocation:** Penghancuran token absolut dari database saat logout.
- **Mass Assignment Protection:** Menggunakan `$fillable` pada semua model (kecuali Module yang menggunakan `$guarded = []`).
- **Database Transaction:** Digunakan pada operasi evaluasi tugas (`evaluateTask`, `liveEvaluate`) untuk menjamin konsistensi data.
- **CORS:** Konfigurasi ketat dengan `supports_credentials: true` untuk komunikasi SPA.