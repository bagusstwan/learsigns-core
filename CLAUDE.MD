# Instruksi Sistem untuk Agen AI

Dokumen ini ditujukan bagi AI Code Assistant yang memproses repositori ini.

## Konteks Proyek
Ini adalah repositori backend (`learnsigns-core`) untuk platform pendidikan bahasa isyarat berbasis deteksi gestur AI. Produk dipublikasikan sebagai **Nusa.ai** (nama internal historis: **Viba.ai**). Frontend klien berada di repositori terpisah (`learn-signs`) menggunakan React 18.x + Vite.

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
│   ├── Controller.php              # Base controller
│   └── Api/
│       ├── ModuleController.php       # Endpoint publik modul
│       └── v1/                        # 7 controller API terversioning
├── Models/             # 6 model Eloquent (User, Module, Quest, StudentProgress, UserQuest, DatasetRecord)
└── Providers/          # Service providers
database/
├── migrations/         # 13 file migrasi
├── factories/          # UserFactory
└── seeders/            # DatabaseSeeder, ModuleSeeder, QuestSeeder
docs/                   # Dokumentasi teknis proyek
routes/
├── api.php             # Seluruh rute API (prefix /v1)
├── web.php             # Rute admin & penyajian model AI (/serve-ai)
└── console.php         # Perintah konsol
```

## Aturan Ketat untuk Asisten AI
1. **Bahasa Pemrograman:** Gunakan standar PHP 8.2+ (termasuk match expressions, named arguments, dan strict typing).
2. **Format Respons:** Gunakan bahasa teknis dan profesional berstandar Indonesia. Dilarang menggunakan emotikon.
3. **Arsitektur:** Logika bisnis saat ini berada langsung di Controller. Untuk refaktor ke depan, arahkan ke Service Class (`app/Services/`), tetapi jangan ubah pola yang sudah ada tanpa instruksi.
4. **Keamanan:** Selalu terapkan perlindungan: `$fillable` untuk Mass Assignment, validasi input ketat, dan Database Transaction untuk operasi kritis.
5. **Format API:** Konsisten gunakan standar JSON sesuai `docs/API_CONTRACT.md` dengan kunci `status` (bukan `success`). Perhatikan pengecualian format pada endpoint autentikasi (lihat bagian "Pengecualian Format" di API_CONTRACT.md).
6. **Versi Framework:** Laravel 12 — tidak ada `app/Http/Kernel.php`. Middleware dikonfigurasi di `bootstrap/app.php`.
7. **Penamaan Database:** File `.env.example` menggunakan `DB_DATABASE=learnsigns_core`. Pastikan konsisten.
8. **Admin Panel:** FilamentPHP v3 terisolasi di `/admin`. Jangan campur logika API dengan logika admin.
9. **Tabel Assignments:** Tabel `assignments` **tidak memiliki Model Eloquent**. Gunakan `DB::table('assignments')` untuk seluruh operasi CRUD. Gunakan `Schema::hasTable('assignments')` sebagai guard sebelum akses. Jangan membuat model Eloquent untuk tabel ini tanpa instruksi eksplisit.
10. **Konteks Frontend:** Klien React (`learn-signs`) menggunakan `fetch` API native untuk memanggil endpoint. Token disimpan di `localStorage` dengan kunci `token`. Data pengguna disimpan dengan kunci `user`.

## Referensi Dokumentasi
- [API_CONTRACT.md](docs/API_CONTRACT.md) — Daftar endpoint, format respons, dan pengecualian format.
- [DATABASE_SCHEMA.md](docs/DATABASE_SCHEMA.md) — Skema basis data lengkap dan data kalkulasi runtime.
- [SCHEMA.dbml](docs/SCHEMA.dbml) — Representasi DBML dari skema basis data.
- [SAD.md](docs/SAD.md) — Arsitektur perangkat lunak, tumpukan teknologi, dan strategi keamanan.
- [PRD.md](docs/PRD.md) — Kebutuhan produk, fitur, sistem tier, dan integrasi layanan pihak ketiga.
- [CONVENTIONS.md](docs/CONVENTIONS.md) — Konvensi penulisan kode, pola akses data, dan aturan transaksi.
- [SETUP.md](docs/SETUP.md) — Panduan instalasi, konfigurasi environment, dan akses admin panel.
- [TASK_BREAKDOWN.md](docs/TASK_BREAKDOWN.md) — Status implementasi fitur per fase.