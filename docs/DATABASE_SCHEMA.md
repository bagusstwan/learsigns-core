# Skema Basis Data

Dokumentasi struktur utama basis data relasional Nusa.ai.

## Tabel `users`
*   `id` (PK) - Integer.
*   `name` - String, Nama lengkap.
*   `email` - String, Unik.
*   `google_id` - String, Unik, Token identifikasi OAuth2.
*   `role` - Enum (`student`, `teacher`, `corporate`).
*   `stars` - Integer, Total akumulasi poin (Default: 0).
*   `timestamps` - Waktu pembuatan dan pembaruan.

## Tabel `modules`
*   `id` (PK) - Integer.
*   `title` - String, Judul materi.
*   `description` - Text, Penjelasan materi.
*   `is_active` - Boolean, Status publikasi.

## Tabel `quests`
*   `id` (PK) - Integer.
*   `module_id` (FK) - Relasi ke tabel `modules`.
*   `reward_stars` - Integer, Jumlah poin hadiah.
*   `is_active` - Boolean.