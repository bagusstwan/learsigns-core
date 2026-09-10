# Instruksi Sistem untuk Agen AI

Dokumen ini ditujukan bagi AI Code Assistant yang memproses repositori ini.

## Konteks Proyek
Ini adalah repositori backend (`learnsigns-core`) untuk platform pendidikan bahasa isyarat berbasis deteksi gestur AI.

**Tumpukan Teknologi:**
- **Framework:** Laravel 12.x (PHP 8.2+)
- **Basis Data:** MySQL 8.0+
- **Autentikasi:** Laravel Sanctum v4 (Bearer Token) + Laravel Socialite v5 (Google SSO)
- **Panel Admin:** FilamentPHP v3
- **Frontend Bundler:** Vite + Tailwind CSS v4
- **Testing:** Pest v3

## Struktur Proyek
```
app/
├── Filament/           # Panel admin: Resources, Pages, Widgets
├── Http/Controllers/
│   └── Api/
│       ├── ModuleController.php       # Endpoint publik modul
│       └── v1/                        # 7 controller API terversioning
├── Models/             # 6 model Eloquent
└── Providers/          # Service providers
database/
├── migrations/         # 13 file migrasi
├── factories/          # UserFactory
└── seeders/            # DatabaseSeeder, ModuleSeeder, QuestSeeder
docs/                   # Dokumentasi teknis proyek
routes/
├── api.php             # Seluruh rute API (prefix /v1)
└── web.php             # Rute admin & penyajian model AI
```

## Aturan Ketat untuk Asisten AI
1. **Bahasa Pemrograman:** Gunakan standar PHP 8.2+ (termasuk match expressions, named arguments, dan strict typing).
2. **Format Respons:** Gunakan bahasa teknis dan profesional berstandar Indonesia. Dilarang menggunakan emotikon.
3. **Arsitektur:** Logika bisnis saat ini berada langsung di Controller. Untuk refaktor ke depan, arahkan ke Service Class (`app/Services/`), tetapi jangan ubah pola yang sudah ada tanpa instruksi.
4. **Keamanan:** Selalu terapkan perlindungan: `$fillable` untuk Mass Assignment, validasi input ketat, dan Database Transaction untuk operasi kritis.
5. **Format API:** Konsisten gunakan standar JSON sesuai `docs/API_CONTRACT.md` dengan kunci `status` (bukan `success`).
6. **Versi Framework:** Laravel 12 — tidak ada `app/Http/Kernel.php`. Middleware dikonfigurasi di `bootstrap/app.php`.
7. **Penamaan Database:** File `.env.example` menggunakan `DB_DATABASE=learnsigns_core`. Pastikan konsisten.
8. **Admin Panel:** FilamentPHP v3 terisolasi di `/admin`. Jangan campur logika API dengan logika admin.

## Referensi Dokumentasi
- [API_CONTRACT.md](docs/API_CONTRACT.md) — Daftar endpoint dan format respons.
- [DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md) — Skema basis data lengkap.
- [SAD.md](docs/SAD.md) — Arsitektur perangkat lunak.
- [CONVENTIONS.md](docs/CONVENTIONS.md) — Konvensi penulisan kode.
- [TASK_BREAKDOWN.md](docs/TASK_BREAKDOWN.md) — Status implementasi fitur.