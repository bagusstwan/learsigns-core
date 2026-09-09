# Instruksi Sistem untuk Agen AI

Dokumen ini ditujukan bagi AI Code Assistant yang memproses repositori ini.

## Konteks Proyek
Ini adalah repositori backend (`learsigns-core`) untuk platform pendidikan bahasa isyarat tingkat *Enterprise*.
Tumpukan teknologi: **Laravel 11, PHP 8.2, MySQL, FilamentPHP**.

## Aturan Ketat untuk Asisten AI
1. **Bahasa Pemrograman:** Gunakan standar PHP 8.2+ (termasuk *Match expressions* dan *Strict Typing*).
2. **Format Respons:** Gunakan bahasa teknis dan profesional berstandar Indonesia (seperti PHPDoc). Dilarang menggunakan emotikon.
3. **Arsitektur:** Arahkan instruksi logika komputasi kompleks ke kelas *Service* (`app/Services/`), bukan *Controller*.
4. **Keamanan:** Selalu terapkan perlindungan kerentanan (mass assignment, validasi input ketat).
5. **Format API:** Konsisten gunakan standar pembungkus JSON sesuai `docs/API_CONTRACT.md`.