# Dokumen Kebutuhan Produk (Product Requirements Document)

## Visi Produk
Menyediakan infrastruktur backend yang stabil dan aman untuk melayani platform pembelajaran bahasa isyarat (LearnSigns / Viba.ai). Backend ini dirancang sebagai Stateless RESTful API terpusat yang mendistribusikan data ke klien React Frontend.

## Target Pengguna
| Peran | Deskripsi |
|---|---|
| **Siswa (Student)** | Peserta didik yang mempelajari bahasa isyarat melalui deteksi gestur AI. |
| **Pendidik (Teacher)** | Instruktur yang mengelola kelas, mendelegasikan tugas, dan mengevaluasi siswa. |
| **Institusi (Corporate)** | Entitas organisasi yang menaungi kelompok pendidik dan siswa. |
| **Administrator** | Pengelola sistem melalui panel admin FilamentPHP. |

## Lingkup Fitur Backend

### 1. Otentikasi Multi-Metode
- Registrasi dan login berbasis Email/Kata Sandi dengan proteksi Brute-Force (Rate Limiter: maks. 5 percobaan).
- Single Sign-On (SSO) via Google OAuth2 menggunakan Laravel Socialite.
- Manajemen token menggunakan Laravel Sanctum (Bearer Token).

### 2. Manajemen Peran Akses
- Pemisahan data berdasarkan peran: `student`, `teacher`, `corporate`.
- Endpoint dashboard yang mengembalikan metrik berbeda berdasarkan peran pengguna.

### 3. Sistem Pembelajaran Modular
- Modul pembelajaran bertingkat: **Abjad** (Level 1), **Kosa Kata** (Level 2), **Kalimat** (Level 3).
- Setiap modul memiliki `target_gesture` sebagai kunci deteksi AI.
- Pelacakan progres siswa per modul dengan skor akurasi tertinggi (mekanisme `GREATEST`).
- Ambang batas kelulusan modul: akurasi >= 90%.

### 4. Gamifikasi (Quest & Bintang)
- Sistem misi harian (Quest) dengan hadiah bintang bertingkat:
  - Abjad: 50-75 bintang.
  - Kosa Kata: 100-125 bintang.
  - Kalimat: 200-300 bintang.
- Sistem peringkat (Tier) otomatis: Bronze -> Silver (200) -> Gold (500) -> Gold Pro (800) -> Platinum Elite (1200) -> Diamond Elite (1500).
- Leaderboard 50 siswa teratas berdasarkan akumulasi bintang.

### 5. Fitur Pendidik (Educator)
- Dasbor khusus pendidik dengan statistik kelas.
- Delegasi tugas (Assignment) kepada siswa dalam institusi yang sama.
- Evaluasi tugas dengan pemberian bintang (1-50 per evaluasi).
- Evaluasi praktikum langsung (Live Evaluate) melalui sensor AI di kelas.
- Manajemen murid: Tambah, Edit, Hapus satuan, dan Hapus massal.

### 6. Pencatatan Dataset AI
- Perekaman matriks koordinat MediaPipe (landmarks) ke basis data.
- Klasifikasi gestur: `static` dan `dynamic`.
- Halaman admin untuk perekaman dan ekspor dataset JSON.

### 7. Dasbor Analitik Admin (FilamentPHP)
- Widget Ikhtisar Metrik: Total Pengguna, Siswa Aktif, Modul Pembelajaran, Quest Aktif.
- Grafik Distribusi Peran Pengguna (Doughnut Chart).
- Papan Peringkat 5 Siswa Teratas.
- Riwayat Pendaftaran Pengguna Terbaru.
- CRUD Modul Pembelajaran dan Quest melalui panel admin.
- Studio Perekaman Dataset AI dan Ruang Uji AI.

### 8. Penyajian Model AI
- Rute khusus (`/serve-ai/{folder}/{filename}`) untuk menyajikan file model AI ke klien React dengan header CORS.

## Persyaratan Non-Fungsional (NFR)
- Waktu respons API harus di bawah 200ms.
- Penerapan Rate Limiting pada endpoint login untuk mencegah serangan Brute Force.
- Proteksi Anti-Enumeration pada pesan error otentikasi.
- Konfigurasi CORS yang aman untuk komunikasi dengan frontend React (`localhost:5173`).
- Penggunaan Database Transaction pada operasi kritis (evaluasi tugas, distribusi bintang).