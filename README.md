# Nusa.ai Core API (learsigns-core)

## Deskripsi Repositori
`learsigns-core` adalah repositori infrastruktur backend utama untuk platform Nusa.ai (AI-Powered Sign Language Platform). Dibangun menggunakan kerangka kerja Laravel, repositori ini bertindak sebagai pusat layanan data (RESTful API), penyedia otentikasi terpusat, dan panel administratif berskala enterprise. Sistem ini dirancang dengan prinsip pemisahan antarmuka (decoupled architecture) untuk mendukung skalabilitas dan keamanan data yang optimal.

## Tumpukan Teknologi (Tech Stack)
*   **Framework:** Laravel (PHP)
*   **Autentikasi:** Laravel Sanctum (API Tokens), Laravel Socialite (OAuth2)
*   **Panel Administratif:** FilamentPHP v3
*   **Basis Data:** Relational Database Management System (RDBMS)

## Fitur dan Arsitektur Modul

### Layanan Integrasi Data (RESTful API)
Menyediakan titik akhir (endpoint) komunikasi yang aman dan tervalidasi untuk melayani interaksi data dari sisi klien (React Frontend), mencakup sinkronisasi profil, pelacakan progres, dan manajemen modul.

### Sistem Autentikasi dan Keamanan
*   **Manajemen Sesi:** Menerapkan otentikasi berbasis token menggunakan Laravel Sanctum.
*   **Single Sign-On (SSO):** Terintegrasi dengan Google OAuth2 melalui Laravel Socialite untuk registrasi dan otentikasi satu pintu.
*   **Proteksi Keamanan:** Dilengkapi dengan mekanisme Rate Limiting (Throttle) untuk mencegah serangan *brute-force* pada modul login, serta enkripsi kata sandi menggunakan algoritma *hashing* standar industri.

### Panel Administratif Enterprise (Filament)
Dasbor analitik internal yang digunakan oleh administrator sistem untuk memantau aktivitas platform secara *real-time*:
*   **Widget Ikhtisar Metrik:** Indikator performa utama (KPI) yang mencakup Total Pengguna, Siswa Aktif, Modul Pembelajaran, dan Quest Interaktif.
*   **Grafik Distribusi Peran:** Visualisasi komposisi demografi pengguna (Siswa, Pendidik, Institusi) menggunakan grafik cincin (Doughnut Chart) presisi tinggi.
*   **Papan Peringkat Siswa:** Tabel pemantauan performa lima siswa teratas berdasarkan perolehan bintang.
*   **Riwayat Pendaftaran:** Tabel pemantauan aktivitas registrasi pengguna terbaru secara komprehensif, dilengkapi status indikator visual.