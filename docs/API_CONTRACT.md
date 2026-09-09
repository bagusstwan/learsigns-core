# Kontrak API (API Contract) - Nusa.ai Backend

Dokumen ini mendefinisikan standar format respons untuk seluruh RESTful API pada layanan Nusa.ai.

## Standar Format Respons

Seluruh titik akhir (endpoint) API harus mengembalikan format JSON yang konsisten.

### Respons Berhasil (HTTP 200/201)
```json
{
  "success": true,
  "message": "Deskripsi aksi yang berhasil dilakukan.",
  "data": {
    "id": 1,
    "name": "Bagus Setiawan"
  },
  "meta": {}
}
```

### Respons Gagal (HTTP 400/401/403/404/422/500)
```json
{
  "success": false,
  "message": "Deskripsi utama kesalahan.",
  "errors": {
    "email": ["Format email tidak valid."]
  }
}
```

## Daftar Endpoint Utama (V1)
*   `POST /api/v1/auth/google/callback` - Menangani autentikasi SSO Google.
*   `GET /api/v1/users/profile` - Mengambil data profil (Membutuhkan Header: `Authorization: Bearer {token}`).
*   `GET /api/v1/leaderboard` - Mengambil 5 siswa teratas.
*   `GET /api/v1/quests` - Mengambil daftar modul aktif.