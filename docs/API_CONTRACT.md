# Kontrak API (API Contract) - LearnSigns Core Backend

Dokumen ini mendefinisikan standar format respons dan daftar lengkap endpoint RESTful API yang dikonsumsi oleh klien React Frontend (`learn-signs`).

**Base URL:** `/api/v1`

## Standar Format Respons

### Format Umum

Mayoritas endpoint API mengembalikan format JSON yang konsisten berikut:

#### Respons Berhasil (HTTP 200/201)
```json
{
  "status": "success",
  "message": "Deskripsi aksi yang berhasil dilakukan.",
  "data": {}
}
```

#### Respons Gagal (HTTP 400/401/404/422/429/500)
```json
{
  "status": "error",
  "message": "Deskripsi utama kesalahan."
}
```

### Pengecualian Format

Beberapa endpoint menggunakan format respons yang menyimpang dari standar di atas. Penyimpangan ini didokumentasikan secara eksplisit pada tabel berikut:

| Endpoint | Format Aktual | Keterangan |
|---|---|---|
| `POST /register` | `{ status, user, token }` | Objek `user` dan `token` berada di root level, bukan di dalam kunci `data`. |
| `POST /login` | `{ status, user, token }` | Format identik dengan register. |
| `GET /user` | `{ id, name, email, ... }` | Objek User mentah **tanpa wrapper** `status`/`data`. Dikembalikan langsung dari `$request->user()`. |
| `GET /auth/google` | `{ url }` | Mengembalikan URL redirect Google **tanpa** kunci `status`. |
| `GET /auth/google/callback` | HTTP 302 Redirect | **Bukan respons JSON.** Melakukan redirect ke `{FRONTEND_URL}/auth/callback?token={token}`. |
| `GET /quests` | `{ status, user_stars, data }` | Menambahkan kunci `user_stars` (total bintang pengguna) di luar `data`. |
| `POST /quests/complete` | `{ status, message, new_total_stars }` | Menambahkan kunci `new_total_stars` (akumulasi bintang terbaru). |

---

## Endpoint Publik (Tanpa Autentikasi)

### Modul Pembelajaran
| Metode | Endpoint | Deskripsi | Parameter Query |
|---|---|---|---|
| `GET` | `/modules` | Mengambil semua modul aktif. | `?level=abjad\|kata\|kalimat` (opsional) |
| `GET` | `/modules/{id}` | Mengambil detail satu modul. | - |

### Autentikasi
| Metode | Endpoint | Deskripsi | Body / Query |
|---|---|---|---|
| `POST` | `/register` | Registrasi pengguna baru. | Body: `name`, `email`, `password`, `role` (required); `phone`, `institution` (optional) |
| `POST` | `/login` | Login dengan email dan password. | Body: `email`, `password` |
| `GET` | `/auth/google` | Mendapatkan URL redirect OAuth2 Google. | Query: `?role=student\|teacher\|corporate` (opsional, default: `student`) |
| `GET` | `/auth/google/callback` | Callback dari Google. Redirect ke frontend membawa token. | - |

#### Detail Respons Autentikasi

**Register dan Login** (`POST /register`, `POST /login`) mengembalikan format berikut saat berhasil:
```json
{
  "status": "success",
  "user": {
    "id": 1,
    "name": "Nama Pengguna",
    "email": "user@example.com",
    "role": "student",
    "stars": 0
  },
  "token": "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ..."
}
```

Token Sanctum yang diterbitkan diberi nama `viba-auth-token`. Klien harus menyimpan token ke `localStorage` dan mengirimnya sebagai header `Authorization: Bearer {token}` pada setiap permintaan terproteksi.

**Google SSO Redirect** (`GET /auth/google`) mengembalikan URL otorisasi:
```json
{
  "url": "https://accounts.google.com/o/oauth2/v2/auth?..."
}
```

**Google SSO Callback** (`GET /auth/google/callback`) melakukan **HTTP 302 Redirect** ke:
```
{FRONTEND_URL}/auth/callback?token={token}
```
Jika autentikasi Google gagal, redirect menuju:
```
{FRONTEND_URL}/login?error=GoogleAuthFailed
```

---

## Endpoint Terproteksi (Membutuhkan Header: `Authorization: Bearer {token}`)

### Data Pengguna
| Metode | Endpoint | Deskripsi | Format Respons |
|---|---|---|---|
| `GET` | `/user` | Mengambil data pengguna yang sedang login. | Objek User **tanpa wrapper** — langsung `{ id, name, email, role, stars, ... }`. |
| `POST` | `/logout` | Menghancurkan token sesi saat ini. | `{ status, message }` |

### Profil
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `GET` | `/profile` | Mengambil profil lengkap beserta statistik kalkulasi (tier, rank, win rate, quests). | - |
| `PUT` | `/profile` | Memperbarui nama dan institusi. | `name` (required), `institution` (optional) |

#### Detail Respons Profil (`GET /profile`)
```json
{
  "status": "success",
  "data": {
    "name": "Nama Pengguna",
    "email": "user@example.com",
    "role": "Siswa Viba.ai",
    "institution": "Nama Institusi",
    "location": "Medan, Sumatera Utara",
    "joinDate": "Juli 2026",
    "rank": 1,
    "totalStars": 500,
    "tier": "Gold",
    "nextTierStars": 800,
    "quests": 10,
    "winRate": "85%"
  }
}
```

> **Catatan:** Field `role` pada respons profil mengembalikan teks deskriptif (contoh: "Siswa Viba.ai", "Instruktur / Guru", "Super Administrator"), bukan enum mentah (`student`, `teacher`). Field `location` saat ini hardcoded.

### Pengaturan
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `PUT` | `/settings/password` | Mengubah kata sandi. | `current_password`, `new_password` (min: 8, harus berbeda dari current) |

### Progres Belajar
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `POST` | `/progress` | Merekam progres belajar siswa. Menggunakan mekanisme `GREATEST` untuk menyimpan skor tertinggi. | `module_id`, `accuracy` (0-100) |
| `GET` | `/progress/stats` | Mengambil statistik dashboard. Respons berbeda berdasarkan peran pengguna (siswa vs pendidik). | - |

#### Detail Respons Statistik (`GET /progress/stats`)

Endpoint ini mengembalikan data berbeda berdasarkan peran pengguna:

**Untuk Siswa:**
- `total_stars`: Akumulasi bintang dari kolom `users.stars`.
- `average_accuracy`: Rata-rata akurasi dari tabel `student_progress`.
- `completed_modules`: Jumlah modul dengan `is_completed = true`.
- `learning_logs`: 5 riwayat pembelajaran terbaru.
- `weekly_activity`: Grafik aktivitas 7 hari terakhir.
- `quest_logs`: 5 riwayat penyelesaian quest terbaru.

**Untuk Pendidik (`teacher`, `corporate`):**
- `total_stars`: Total bintang yang telah didistribusikan ke siswa.
- `average_accuracy`: Kalkulasi dari rata-rata bintang tugas yang dinilai.
- `completed_modules`: Jumlah tugas berstatus `Selesai Dinilai`.
- `learning_logs`: 5 riwayat evaluasi terbaru dengan nama siswa.
- `weekly_activity`: Grafik aktivitas kelas 7 hari terakhir termasuk daftar `active_students`.
- `quest_logs`: 5 riwayat evaluasi terbaru.
- `today_star_receivers`: Daftar siswa yang menerima bintang hari ini.

### Quest (Misi Harian)
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `GET` | `/quests` | Mengambil daftar misi aktif beserta status penyelesaian per pengguna. | - |
| `POST` | `/quests/complete` | Menyelesaikan misi dan menerima bintang. Misi hanya bisa diselesaikan sekali. | `quest_id` |

### Leaderboard
| Metode | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/leaderboard` | Mengambil 50 siswa teratas berdasarkan bintang. Menyertakan tier, quests count, win rate, inisial nama, dan flag `isMe` untuk pengguna aktif. User ID 1 (admin) dikecualikan. |

### Fitur Pendidik (Educator)
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `GET` | `/educator/dashboard` | Mengambil daftar siswa (berdasarkan institusi yang sama) dan riwayat tugas. | - |
| `GET` | `/educator/dashboard-summary` | Mengambil ringkasan statistik kelas: siswa aktif, modul selesai, evaluasi tertunda, modul aktif, dan antrian siswa pending. | - |
| `POST` | `/educator/assignments` | Mendelegasikan tugas kepada siswa. | `student_id`, `title`, `target`, `notes` (optional) |
| `POST` | `/educator/assignments/{id}/evaluate` | Mengevaluasi tugas dan memberikan bintang. Tugas yang sudah dinilai tidak dapat dievaluasi ulang. | `stars_earned` (1-50), `feedback` (optional) |
| `POST` | `/educator/live-evaluate` | Evaluasi praktikum langsung via sensor AI di kelas. Membuat record assignment otomatis dengan status `Selesai Dinilai`. | `student_id`, `module_id`, `stars_earned` (1-50), `accuracy` |
| `POST` | `/educator/students` | Mendaftarkan siswa baru dalam institusi pendidik. | `name`, `email`, `password` (min: 6) |
| `PUT` | `/educator/students/{id}` | Memperbarui data siswa. | `name`, `email`, `password` (optional, min: 6) |
| `DELETE` | `/educator/students/{id}` | Menghapus akses seorang siswa. Hanya siswa dengan `role = student` yang dapat dihapus. | - |
| `POST` | `/educator/students/bulk-delete` | Menghapus banyak siswa sekaligus. | `ids` (array of user IDs) |

---

## Rute Non-API (routes/web.php)

Rute berikut **bukan** bagian dari API versioned (`/api/v1`), melainkan disajikan langsung melalui `routes/web.php`:

| Metode | Rute | Deskripsi |
|---|---|---|
| `GET` | `/serve-ai/{folder}/{filename}` | Menyajikan file model AI (TensorFlow.js `model.json` dan shard binari) ke klien React. File dibaca dari `public/ai-models/{folder}/{filename}` dengan header CORS: `Access-Control-Allow-Origin: *`. |

Frontend mengakses rute ini pada URL absolut `http://127.0.0.1:8000/serve-ai/{model_name}/model.json` untuk memuat model inferensi bahasa isyarat.

---

## Catatan Keamanan
- **Rate Limiting:** Endpoint `/login` dibatasi 5 percobaan per kombinasi email + IP menggunakan `RateLimiter` facade. Jika terlampaui, respons `HTTP 429` dikembalikan dengan durasi tunggu dalam detik.
- **Anti-Enumeration:** Pesan error login seragam ("Kredensial tidak valid") untuk email tidak ditemukan maupun password salah.
- **Token Revocation:** Logout menghancurkan token secara absolut dari database melalui `currentAccessToken()->delete()`.
- **Token Naming:** Seluruh token Sanctum yang diterbitkan diberi nama `viba-auth-token`.
- **Database Transaction:** Operasi evaluasi tugas (`evaluateTask`, `liveEvaluate`) dibungkus dalam `DB::beginTransaction()` untuk menjamin atomisitas distribusi bintang.