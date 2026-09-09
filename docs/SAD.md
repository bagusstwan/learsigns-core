# Dokumen Arsitektur Perangkat Lunak (Software Architecture Document)

## Ringkasan Arsitektur
`learsigns-core` diimplementasikan sebagai **Stateless RESTful API** terpusat. Peladen tidak merender tampilan HTML secara langsung (kecuali rute `/admin`), melainkan mendistribusikan data mentah via HTTP/HTTPS ke klien.

## Komponen Infrastruktur
1.  **Aplikasi Inti:** Laravel 11.x (PHP 8.2+).
2.  **Basis Data:** MySQL 8.0+ / PostgreSQL.
3.  **Autentikasi Terdistribusi:** Laravel Sanctum (State dipertahankan di klien melalui Bearer Token).
4.  **Panel Admin:** Terisolasi pada rute `/admin` menggunakan FilamentPHP.

## Alur Data
1. Klien mengirim *request* HTTP beserta Bearer Token.
2. Middleware memvalidasi integritas token.
3. Controller melakukan validasi Form Request.
4. Model berinteraksi dengan basis data via Eloquent ORM.
5. Respons dikembalikan ke klien dalam format JSON standar.