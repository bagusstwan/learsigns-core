# Kontrak API (API Contract) - LearnSigns Core Backend

Dokumen ini mendefinisikan standar format respons dan daftar lengkap endpoint RESTful API.

**Base URL:** `/api/v1`

## Standar Format Respons

Seluruh endpoint API mengembalikan format JSON yang konsisten.

### Respons Berhasil (HTTP 200/201)
```json
{
  "status": "success",
  "message": "Deskripsi aksi yang berhasil dilakukan.",
  "data": {}
}
```

### Respons Gagal (HTTP 400/401/404/422/429/500)
```json
{
  "status": "error",
  "message": "Deskripsi utama kesalahan."
}
```

---

## Endpoint Publik (Tanpa Autentikasi)

### Modul Pembelajaran
| Metode | Endpoint | Deskripsi | Parameter Query |
|---|---|---|---|
| `GET` | `/modules` | Mengambil semua modul aktif. | `?level=abjad\|kata\|kalimat` (opsional) |
| `GET` | `/modules/{id}` | Mengambil detail satu modul. | - |

### Autentikasi
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `POST` | `/register` | Registrasi pengguna baru. | `name`, `email`, `password`, `role` (required); `phone`, `institution` (optional) |
| `POST` | `/login` | Login dengan email dan password. | `email`, `password` |
| `GET` | `/auth/google` | Mendapatkan URL redirect OAuth2 Google. | `?role=student\|teacher\|corporate` (opsional) |
| `GET` | `/auth/google/callback` | Callback dari Google, menerbitkan token. | - |

---

## Endpoint Terproteksi (Membutuhkan Header: `Authorization: Bearer {token}`)

### Data Pengguna
| Metode | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/user` | Mengambil data pengguna yang sedang login (raw). |
| `POST` | `/logout` | Menghancurkan token sesi saat ini. |

### Profil
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `GET` | `/profile` | Mengambil profil lengkap beserta statistik (tier, rank, win rate). | - |
| `PUT` | `/profile` | Memperbarui nama dan institusi. | `name` (required), `institution` (optional) |

### Pengaturan
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `PUT` | `/settings/password` | Mengubah kata sandi. | `current_password`, `new_password` |

### Progres Belajar
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `POST` | `/progress` | Merekam progres belajar siswa. | `module_id`, `accuracy` (0-100) |
| `GET` | `/progress/stats` | Mengambil statistik dashboard (berbeda untuk siswa dan pendidik). | - |

### Quest (Misi Harian)
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `GET` | `/quests` | Mengambil daftar misi aktif beserta status penyelesaian. | - |
| `POST` | `/quests/complete` | Menyelesaikan misi dan menerima bintang. | `quest_id` |

### Leaderboard
| Metode | Endpoint | Deskripsi |
|---|---|---|
| `GET` | `/leaderboard` | Mengambil 50 siswa teratas berdasarkan bintang (beserta tier, quest, win rate). |

### Fitur Pendidik (Educator)
| Metode | Endpoint | Deskripsi | Body |
|---|---|---|---|
| `GET` | `/educator/dashboard` | Mengambil daftar siswa dan riwayat tugas dalam institusi. | - |
| `GET` | `/educator/dashboard-summary` | Mengambil ringkasan statistik kelas. | - |
| `POST` | `/educator/assignments` | Mendelegasikan tugas kepada siswa. | `student_id`, `title`, `target`, `notes` (optional) |
| `POST` | `/educator/assignments/{id}/evaluate` | Mengevaluasi tugas dan memberikan bintang. | `stars_earned` (1-50), `feedback` (optional) |
| `POST` | `/educator/live-evaluate` | Evaluasi praktikum langsung via AI. | `student_id`, `module_id`, `stars_earned` (1-50), `accuracy` |
| `POST` | `/educator/students` | Mendaftarkan siswa baru dalam institusi. | `name`, `email`, `password` |
| `PUT` | `/educator/students/{id}` | Memperbarui data siswa. | `name`, `email`, `password` (optional) |
| `DELETE` | `/educator/students/{id}` | Menghapus akses seorang siswa. | - |
| `POST` | `/educator/students/bulk-delete` | Menghapus banyak siswa sekaligus. | `ids` (array) |

---

## Catatan Keamanan
- **Rate Limiting:** Endpoint `/login` dibatasi 5 percobaan per email+IP. Jika terlampaui, respons `HTTP 429` dikembalikan.
- **Anti-Enumeration:** Pesan error login seragam untuk email tidak ditemukan maupun password salah.
- **Token Revocation:** Logout menghancurkan token secara absolut dari database.