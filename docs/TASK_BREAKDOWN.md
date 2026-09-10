# Rincian Tugas (Task Breakdown) - Backend

Dokumen ini mencatat status implementasi fitur-fitur backend LearnSigns Core.

## Fase 1: Persiapan Fondasi
- [x] Inisialisasi repositori Laravel 12.
- [x] Konfigurasi basis data dan migrasi skema (8 tabel domain).
- [x] Implementasi sistem otentikasi API Token (Laravel Sanctum v4).
- [x] Pengaturan modul SSO Google (Laravel Socialite v5).
- [x] Konfigurasi CORS untuk komunikasi dengan React Frontend.

## Fase 2: Sistem Autentikasi
- [x] Endpoint registrasi pengguna (`POST /register`).
- [x] Endpoint login dengan proteksi Brute-Force / Rate Limiter (`POST /login`).
- [x] Endpoint logout dengan penghancuran token (`POST /logout`).
- [x] Integrasi Google SSO: redirect URL dan callback (`GET /auth/google`, `GET /auth/google/callback`).

## Fase 3: Implementasi Logika Inti
- [x] CRUD modul pembelajaran melalui panel admin FilamentPHP (ModuleResource).
- [x] CRUD quest/tantangan melalui panel admin FilamentPHP (QuestResource).
- [x] API publik untuk daftar modul aktif (`GET /modules`, `GET /modules/{id}`).
- [x] API untuk pelacakan progres deteksi AI (`POST /progress`).
- [x] API statistik dashboard siswa dan pendidik (`GET /progress/stats`).
- [x] API quest: daftar misi dan penyelesaian (`GET /quests`, `POST /quests/complete`).
- [x] Logika perhitungan dan distribusi bintang otomatis pasca-penyelesaian quest.

## Fase 4: Profil & Gamifikasi
- [x] API profil pengguna dengan kalkulasi tier dan ranking global (`GET /profile`).
- [x] API pembaruan profil (`PUT /profile`).
- [x] API leaderboard 50 siswa teratas (`GET /leaderboard`).
- [x] API pengaturan kata sandi (`PUT /settings/password`).

## Fase 5: Fitur Pendidik (Educator)
- [x] API dashboard pendidik: daftar siswa dan riwayat tugas (`GET /educator/dashboard`).
- [x] API ringkasan statistik kelas (`GET /educator/dashboard-summary`).
- [x] API delegasi tugas kepada siswa (`POST /educator/assignments`).
- [x] API evaluasi tugas dengan pemberian bintang (`POST /educator/assignments/{id}/evaluate`).
- [x] API evaluasi praktikum langsung (`POST /educator/live-evaluate`).
- [x] Manajemen murid: tambah (`POST /educator/students`).
- [x] Manajemen murid: edit (`PUT /educator/students/{id}`).
- [x] Manajemen murid: hapus satuan (`DELETE /educator/students/{id}`).
- [x] Manajemen murid: hapus massal (`POST /educator/students/bulk-delete`).

## Fase 6: Dasbor Admin (FilamentPHP)
- [x] Widget Stats Overview (Total Pengguna, Siswa Aktif, Modul, Quest).
- [x] Widget Doughnut Chart distribusi peran pengguna.
- [x] Widget Papan Peringkat 5 Siswa Teratas.
- [x] Widget Riwayat Pendaftaran Pengguna Terbaru (Full-Width).
- [x] Halaman Studio Perekaman Dataset AI (DatasetRecorder).
- [x] Halaman Ruang Uji AI (AiTestingStudio).

## Fase 7: Infrastruktur Pendukung
- [x] Rute penyajian model AI ke klien React (`/serve-ai/{folder}/{filename}`).
- [x] Seeder data: QuestSeeder (23 quest bertingkat) dan ModuleSeeder (49 modul).
- [ ] Pengujian unit dan fitur (test suite) secara menyeluruh.
- [ ] Implementasi lapisan Service Class untuk pemisahan logika bisnis dari Controller.
- [ ] Penerapan Form Request untuk validasi terstruktur.