# Dokumen Kebutuhan Produk (Product Requirements Document)

## Visi Produk
Menyediakan infrastruktur backend yang stabil dan aman untuk melayani platform pembelajaran bahasa isyarat **LearnSigns** (dipublikasikan sebagai **Nusa.ai**, dengan nama internal historis **Viba.ai**). Backend ini dirancang sebagai Stateless RESTful API terpusat yang mendistribusikan data ke klien React Frontend (`learn-signs`).

## Target Pengguna
| Peran | Nilai Enum | Deskripsi |
|---|---|---|
| **Siswa (Student)** | `student` | Peserta didik yang mempelajari bahasa isyarat melalui deteksi gestur AI. |
| **Pendidik (Teacher)** | `teacher` | Instruktur yang mengelola kelas, mendelegasikan tugas, dan mengevaluasi siswa. |
| **Institusi (Corporate)** | `corporate` | Entitas organisasi yang menaungi kelompok pendidik dan siswa. Memiliki hak akses setara `teacher`. |
| **Administrator** | - | Pengelola sistem melalui panel admin FilamentPHP. Tidak termasuk dalam enum `role` API. |

## Lingkup Fitur Backend

### 1. Otentikasi Multi-Metode
- Registrasi dan login berbasis Email/Kata Sandi dengan proteksi Brute-Force (Rate Limiter: maks. 5 percobaan per email+IP).
- Single Sign-On (SSO) via Google OAuth2 menggunakan Laravel Socialite dengan mode `stateless()`.
- Auto-register: pengguna baru yang login via Google SSO otomatis didaftarkan dengan password acak.
- Manajemen token menggunakan Laravel Sanctum (Bearer Token) dengan nama token `viba-auth-token`.

### 2. Manajemen Peran Akses
- Pemisahan data berdasarkan peran: `student`, `teacher`, `corporate`.
- Endpoint dashboard yang mengembalikan metrik berbeda berdasarkan peran pengguna (siswa melihat progres pribadi, pendidik melihat metrik kelas).
- Pendidik hanya dapat melihat dan mengelola siswa dalam institusi yang sama (filter berdasarkan kolom `institution`).

### 3. Sistem Pembelajaran Modular
- Modul pembelajaran bertingkat: **Abjad** (Level 1), **Kosa Kata** (Level 2), **Kalimat** (Level 3).
- Setiap modul memiliki `target_gesture` sebagai kunci deteksi AI dan `reference_image` sebagai gambar panduan visual.
- Pelacakan progres siswa per modul dengan skor akurasi tertinggi (mekanisme `GREATEST` di SQL).
- Ambang batas kelulusan modul: akurasi >= 90%.

### 4. Gamifikasi (Quest dan Bintang)
- Sistem misi harian (Quest) dengan hadiah bintang bertingkat:

| Kategori | Rentang Hadiah Bintang |
|---|---|
| Abjad | 50 - 75 bintang |
| Kosa Kata | 100 - 125 bintang |
| Kalimat | 200 - 300 bintang |

- Sistem peringkat (Tier) otomatis berdasarkan akumulasi bintang:

| Tier | Batas Bintang Minimal | Batas Bintang Berikutnya |
|---|---|---|
| Bronze | 0 | 200 |
| Silver | 200 | 500 |
| Gold | 500 | 800 |
| Gold Pro | 800 | 1.200 |
| Platinum Elite | 1.200 | 1.500 |
| Diamond Elite | 1.500 | - (tier tertinggi) |

- Leaderboard 50 siswa teratas berdasarkan akumulasi bintang (user ID 1 / admin dikecualikan).
- Kalkulasi Win Rate berdasarkan rata-rata akurasi dari tabel `student_progress`.

### 5. Fitur Pendidik (Educator)
- Dasbor khusus pendidik dengan statistik kelas: jumlah siswa aktif, modul terselesaikan, dan antrean evaluasi tertunda.
- Delegasi tugas (Assignment) kepada siswa dalam institusi yang sama.
- Evaluasi tugas manual dengan pemberian bintang (1-50 per evaluasi) dan catatan umpan balik.
- **Evaluasi Praktikum Langsung (Live Evaluate):** Pendidik dapat mengevaluasi praktik gestur siswa secara real-time melalui sensor AI di kelas. Sistem otomatis membuat record assignment berstatus `Selesai Dinilai` dan mendistribusikan bintang secara atomis menggunakan Database Transaction.
- Manajemen murid: Tambah akun baru (terikat institusi pendidik), Edit profil, Hapus satuan, dan Hapus massal (bulk delete).

### 6. Pencatatan Dataset AI
- Perekaman matriks koordinat MediaPipe (landmarks) ke basis data.
- Klasifikasi gestur: `static` (satu frame, contoh: abjad) dan `dynamic` (multi-frame, contoh: kosa kata/kalimat).
- Halaman admin untuk perekaman dan ekspor dataset JSON.

### 7. Dasbor Analitik Admin (FilamentPHP)
- Widget Ikhtisar Metrik: Total Pengguna, Siswa Aktif, Modul Pembelajaran, Quest Aktif.
- Grafik Distribusi Peran Pengguna (Doughnut Chart).
- Papan Peringkat 5 Siswa Teratas.
- Riwayat Pendaftaran Pengguna Terbaru.
- CRUD Modul Pembelajaran dan Quest melalui panel admin.
- Studio Perekaman Dataset AI (`DatasetRecorder`) dan Ruang Uji AI (`AiTestingStudio`).

### 8. Penyajian Model AI
- Rute khusus (`/serve-ai/{folder}/{filename}`) untuk menyajikan file model TensorFlow.js ke klien React dengan header CORS permisif.
- File model disimpan di `public/ai-models/` dan mencakup arsitektur (`model.json`) serta bobot (shard binari).
- Frontend memuat model untuk dua mode inferensi:
  - **Mode Statis (Abjad):** Tensor 2D dari 1 frame (126 koordinat landmark).
  - **Mode Dinamis (Kosa Kata/Kalimat):** Tensor 3D dengan buffer 60 frame temporal.

### 9. Integrasi Layanan Pihak Ketiga (Sisi Frontend)

Backend mendukung integrasi frontend dengan layanan AI pihak ketiga berikut:

| Layanan | Kegunaan | Konfigurasi |
|---|---|---|
| Google Generative AI (Gemini 2.5 Flash) | Umpan balik korektif dan motivasional berbasis AI untuk setiap sesi latihan gestur. | Kunci API publik via `VITE_GEMINI_API_KEY` di klien. |
| ElevenLabs Text-to-Speech | Sintesis suara instruksi dan hasil deteksi menggunakan model `eleven_multilingual_v2`. | Kunci API via `VITE_ELEVENLABS_API_KEY` di klien. Fallback ke `window.speechSynthesis` (Web Speech API, bahasa `id-ID`). |
| MediaPipe Hands | Deteksi 21 titik koordinat 3D per tangan secara real-time di peramban. | Berjalan sepenuhnya di sisi klien, tanpa konfigurasi backend. |
| TensorFlow.js | Inferensi model klasifikasi gestur bahasa isyarat di peramban. | Model disajikan oleh backend via rute `/serve-ai`. |

## Persyaratan Non-Fungsional (NFR)
- Waktu respons API harus di bawah 200ms.
- Penerapan Rate Limiting pada endpoint login untuk mencegah serangan Brute Force.
- Proteksi Anti-Enumeration pada pesan error otentikasi.
- Konfigurasi CORS yang aman untuk komunikasi dengan frontend React (`localhost:5173`) melalui `config/cors.php` dengan `supports_credentials: true`.
- Penggunaan Database Transaction pada operasi kritis (evaluasi tugas, distribusi bintang).
- Pesan error login seragam untuk seluruh skenario kegagalan autentikasi.