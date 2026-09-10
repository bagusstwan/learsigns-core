# Rincian Tugas (Task Breakdown) - Backend

Dokumen ini mencatat status implementasi fitur-fitur backend LearnSigns Core.

## Fase 1: Persiapan Fondasi
- [x] Inisialisasi repositori Laravel 12.
- [x] Konfigurasi basis data dan migrasi skema (8 tabel: 7 domain + 1 sistem Sanctum).
- [x] Implementasi sistem otentikasi API Token (Laravel Sanctum v4).
- [x] Pengaturan modul SSO Google (Laravel Socialite v5) dengan mode `stateless()` dan auto-register.
- [x] Konfigurasi CORS untuk komunikasi dengan React Frontend (`localhost:5173`).

## Fase 2: Sistem Autentikasi
- [x] Endpoint registrasi pengguna (`POST /register`) dengan dukungan peran `student`, `teacher`, `corporate`.
- [x] Endpoint login dengan proteksi Brute-Force / Rate Limiter (`POST /login`) — 5 percobaan per email+IP.
- [x] Endpoint logout dengan penghancuran token absolut (`POST /logout`).
- [x] Integrasi Google SSO: redirect URL dan callback (`GET /auth/google`, `GET /auth/google/callback`).
- [x] Redirect callback ke frontend dengan token (`{FRONTEND_URL}/auth/callback?token={token}`).

## Fase 3: Implementasi Logika Inti
- [x] CRUD modul pembelajaran melalui panel admin FilamentPHP (ModuleResource).
- [x] CRUD quest/tantangan melalui panel admin FilamentPHP (QuestResource).
- [x] API publik untuk daftar modul aktif (`GET /modules`, `GET /modules/{id}`).
- [x] API untuk pelacakan progres deteksi AI (`POST /progress`) dengan mekanisme `GREATEST` untuk skor tertinggi.
- [x] API statistik dashboard siswa dan pendidik (`GET /progress/stats`) — respons berbeda berdasarkan peran.
- [x] API quest: daftar misi dan penyelesaian (`GET /quests`, `POST /quests/complete`).
- [x] Logika perhitungan dan distribusi bintang otomatis pasca-penyelesaian quest.

## Fase 4: Profil & Gamifikasi
- [x] API profil pengguna dengan kalkulasi tier dan ranking global (`GET /profile`).
- [x] API pembaruan profil (`PUT /profile`).
- [x] API leaderboard 50 siswa teratas (`GET /leaderboard`) — termasuk tier, quest count, win rate, inisial.
- [x] API pengaturan kata sandi (`PUT /settings/password`).
- [x] Kalkulasi runtime: tier (6 tingkat), rank global, win rate (rata-rata akurasi), inisial nama.

## Fase 5: Fitur Pendidik (Educator)
- [x] API dashboard pendidik: daftar siswa (filter institusi) dan riwayat tugas (`GET /educator/dashboard`).
- [x] API ringkasan statistik kelas (`GET /educator/dashboard-summary`) — siswa aktif, modul selesai, evaluasi tertunda, antrian pending.
- [x] API delegasi tugas kepada siswa (`POST /educator/assignments`).
- [x] API evaluasi tugas dengan pemberian bintang + Database Transaction (`POST /educator/assignments/{id}/evaluate`).
- [x] API evaluasi praktikum langsung + Database Transaction (`POST /educator/live-evaluate`).
- [x] Manajemen murid: tambah (`POST /educator/students`).
- [x] Manajemen murid: edit (`PUT /educator/students/{id}`).
- [x] Manajemen murid: hapus satuan (`DELETE /educator/students/{id}`).
- [x] Manajemen murid: hapus massal (`POST /educator/students/bulk-delete`).

> **Catatan Arsitektural:** Seluruh fitur pendidik mengakses tabel `assignments` melalui `DB::table()` (Query Builder) tanpa Model Eloquent. Pola `Schema::hasTable()` digunakan sebagai guard.

## Fase 6: Dasbor Admin (FilamentPHP)
- [x] Widget Stats Overview (Total Pengguna, Siswa Aktif, Modul, Quest).
- [x] Widget Doughnut Chart distribusi peran pengguna.
- [x] Widget Papan Peringkat 5 Siswa Teratas.
- [x] Widget Riwayat Pendaftaran Pengguna Terbaru (Full-Width).
- [x] Halaman Studio Perekaman Dataset AI (DatasetRecorder).
- [x] Halaman Ruang Uji AI (AiTestingStudio).

## Fase 7: Infrastruktur Pendukung
- [x] Rute penyajian model AI ke klien React (`/serve-ai/{folder}/{filename}`) dengan header CORS manual.
- [x] Seeder data: QuestSeeder (23 quest bertingkat) dan ModuleSeeder (49 modul, tidak dijalankan default).
- [ ] Pengujian unit dan fitur (test suite) secara menyeluruh.
- [ ] Implementasi lapisan Service Class (`app/Services/`) untuk pemisahan logika bisnis dari Controller.
- [ ] Penerapan Form Request untuk validasi terstruktur (menggantikan `$request->validate()` inline).
- [ ] Pembuatan Model Eloquent `Assignment` untuk menggantikan pola `DB::table('assignments')` (opsional).