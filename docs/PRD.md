# Dokumen Kebutuhan Produk (Product Requirements Document)

## Visi Produk
Menyediakan infrastruktur backend yang stabil dan aman untuk melayani platform pembelajaran bahasa isyarat (Nusa.ai).

## Lingkup Fitur Backend
1.  **Otentikasi SSO:** Implementasi OAuth2 via Google untuk akses tanpa kata sandi.
2.  **Manajemen Peran Akses:** Pemisahan data antara Siswa, Pendidik, dan Institusi.
3.  **Pencatatan Aktivitas AI:** Menyimpan matriks hasil pengenalan gestur AI dari klien ke basis data.
4.  **Gamifikasi API:** Sistem kalkulasi dan distribusi bintang secara otomatis.
5.  **Dasbor Analitik (Admin):** Visualisasi statistik sistem tingkat tinggi menggunakan FilamentPHP.

## Persyaratan Kinerja (NFR)
*   Waktu respons API harus di bawah 200ms.
*   Penerapan *Rate Limiting* untuk mencegah serangan *Brute Force*.