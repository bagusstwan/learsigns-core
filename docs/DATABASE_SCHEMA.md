# Skema Basis Data

Dokumentasi lengkap struktur basis data relasional LearnSigns Core.

## Tabel `users`
| Kolom | Tipe | Atribut | Keterangan |
|---|---|---|---|
| `id` | bigint | PK, Auto Increment | Identitas unik pengguna. |
| `name` | varchar(255) | NOT NULL | Nama lengkap. |
| `email` | varchar(255) | UNIQUE, NOT NULL | Alamat surel. |
| `email_verified_at` | timestamp | NULLABLE | Waktu verifikasi surel. |
| `password` | varchar(255) | NOT NULL | Kata sandi terenkripsi (bcrypt). |
| `remember_token` | varchar(100) | NULLABLE | Token sesi ingat saya. |
| `role` | enum | DEFAULT 'student' | Peran akses: `student`, `teacher`, `corporate`. |
| `phone` | varchar(255) | NULLABLE | Nomor telepon. |
| `institution` | varchar(255) | NULLABLE | Nama institusi/organisasi. Digunakan sebagai kunci pengelompokan siswa oleh pendidik. |
| `stars` | integer | DEFAULT 0 | Total akumulasi bintang gamifikasi. |
| `created_at` | timestamp | | Waktu pembuatan akun. |
| `updated_at` | timestamp | | Waktu pembaruan terakhir. |

## Tabel `modules`
| Kolom | Tipe | Atribut | Keterangan |
|---|---|---|---|
| `id` | bigint | PK, Auto Increment | Identitas unik modul. |
| `title` | varchar(255) | NOT NULL | Judul modul (contoh: "Abjad A"). |
| `level_type` | enum | NOT NULL | Tingkat kesulitan: `abjad`, `kata`, `kalimat`. |
| `target_gesture` | varchar(255) | NOT NULL | Kunci deteksi untuk AI (contoh: "A", "IBU"). |
| `description` | text | NULLABLE | Instruksi pembelajaran untuk murid. |
| `reference_image` | varchar(255) | NULLABLE | Path gambar referensi gestur. |
| `is_active` | boolean | DEFAULT true | Status publikasi modul. |
| `created_at` | timestamp | | Waktu pembuatan. |
| `updated_at` | timestamp | | Waktu pembaruan terakhir. |

## Tabel `quests`
| Kolom | Tipe | Atribut | Keterangan |
|---|---|---|---|
| `id` | bigint | PK, Auto Increment | Identitas unik quest. |
| `title` | varchar(255) | NOT NULL | Judul misi. |
| `description` | text | NOT NULL | Deskripsi misi. |
| `target_gesture` | varchar(255) | NOT NULL | Target gestur yang harus dideteksi AI. |
| `reward_stars` | integer | NOT NULL | Jumlah bintang hadiah. Rentang: Abjad (50-75), Kosa Kata (100-125), Kalimat (200-300). |
| `is_active` | boolean | DEFAULT true | Status keaktifan misi. |
| `created_at` | timestamp | | Waktu pembuatan. |
| `updated_at` | timestamp | | Waktu pembaruan terakhir. |

## Tabel `student_progress`
| Kolom | Tipe | Atribut | Keterangan |
|---|---|---|---|
| `id` | bigint | PK, Auto Increment | Identitas unik progres. |
| `user_id` | bigint | FK -> users.id, CASCADE | Referensi ke siswa. |
| `module_id` | bigint | FK -> modules.id, CASCADE | Referensi ke modul. |
| `accuracy` | integer | NOT NULL | Skor akurasi tertinggi (0-100). Diperbarui menggunakan mekanisme `GREATEST(accuracy, new_value)`. |
| `is_completed` | boolean | DEFAULT false | Status kelulusan modul (true jika akurasi >= 90%). |
| `created_at` | timestamp | | Waktu pembuatan. |
| `updated_at` | timestamp | | Waktu pembaruan terakhir. |

> **Constraint:** UNIQUE(`user_id`, `module_id`) — satu siswa hanya memiliki satu baris progres per modul.

## Tabel `user_quests`
| Kolom | Tipe | Atribut | Keterangan |
|---|---|---|---|
| `id` | bigint | PK, Auto Increment | Identitas unik. |
| `user_id` | bigint | FK -> users.id, CASCADE | Referensi ke siswa. |
| `quest_id` | bigint | FK -> quests.id, CASCADE | Referensi ke quest. |
| `completed_at` | timestamp | DEFAULT CURRENT_TIMESTAMP | Waktu penyelesaian misi. |

> **Constraint:** UNIQUE(`user_id`, `quest_id`) — mencegah penyelesaian misi ganda.

## Tabel `dataset_records`
| Kolom | Tipe | Atribut | Keterangan |
|---|---|---|---|
| `id` | bigint | PK, Auto Increment | Identitas unik rekaman. |
| `label` | varchar(255) | NOT NULL, INDEX | Label gestur (contoh: "A", "HALO"). |
| `gesture_type` | enum | DEFAULT 'static' | Tipe gestur: `static` (1 frame), `dynamic` (multi-frame). |
| `landmarks` | json | NOT NULL | Array matriks koordinat MediaPipe (21 titik 3D per tangan). |
| `created_at` | timestamp | | Waktu pembuatan. |
| `updated_at` | timestamp | | Waktu pembaruan terakhir. |

## Tabel `assignments`

> **Catatan Arsitektural:** Tabel ini **tidak memiliki Model Eloquent**. Seluruh operasi CRUD dilakukan melalui `DB::table('assignments')` di `EducatorController`. Pola `Schema::hasTable('assignments')` digunakan sebagai guard sebelum mengakses tabel ini.

| Kolom | Tipe | Atribut | Keterangan |
|---|---|---|---|
| `id` | bigint | PK, Auto Increment | Identitas unik tugas. |
| `student_id` | bigint | FK -> users.id, CASCADE | Referensi ke siswa. |
| `teacher_id` | bigint | FK -> users.id, CASCADE | Referensi ke pendidik. |
| `title` | varchar(255) | NOT NULL | Judul tugas. |
| `target` | varchar(255) | NOT NULL | Target capaian tugas (contoh: gestur yang harus dipraktikkan). |
| `notes` | text | NULLABLE | Catatan instruksi dari pendidik. |
| `status` | varchar(255) | DEFAULT 'Belum Dikerjakan' | Status tugas. |
| `stars_earned` | integer | DEFAULT 0 | Jumlah bintang yang diperoleh dari evaluasi. |
| `feedback` | text | NULLABLE | Catatan evaluasi dari pendidik. |
| `created_at` | timestamp | | Waktu pembuatan. |
| `updated_at` | timestamp | | Waktu pembaruan terakhir. |

> **Status Values:** `Belum Dikerjakan`, `Menunggu Penilaian`, `Selesai Dinilai`.

## Tabel `personal_access_tokens` (Laravel Sanctum)
| Kolom | Tipe | Atribut | Keterangan |
|---|---|---|---|
| `id` | bigint | PK, Auto Increment | Identitas unik token. |
| `tokenable_type` | varchar(255) | NOT NULL | Tipe model (polymorphic). |
| `tokenable_id` | bigint | NOT NULL | ID model (polymorphic). |
| `name` | text | NOT NULL | Nama token (contoh: "viba-auth-token"). |
| `token` | varchar(64) | UNIQUE | Hash token. |
| `abilities` | text | NULLABLE | Kemampuan token. |
| `last_used_at` | timestamp | NULLABLE | Terakhir digunakan. |
| `expires_at` | timestamp | NULLABLE, INDEX | Waktu kedaluwarsa. |
| `created_at` | timestamp | | Waktu pembuatan. |
| `updated_at` | timestamp | | Waktu pembaruan terakhir. |

## Diagram Relasi
```
users 1──N student_progress N──1 modules
users 1──N user_quests     N──1 quests
users 1──N assignments (student_id)
users 1──N assignments (teacher_id)
users 1──N personal_access_tokens (polymorphic)
```

## Data Kalkulasi Runtime (Tidak Tersimpan di Database)

Beberapa data yang dikembalikan oleh API dihitung secara dinamis saat permintaan diterima, bukan disimpan di basis data:

| Field API | Kalkulasi | Sumber Data |
|---|---|---|
| `tier` | Kondisi bertingkat dari `users.stars` (Bronze/Silver/Gold/Gold Pro/Platinum Elite/Diamond Elite) | `users.stars` |
| `rank` | `COUNT(users WHERE stars > current_user.stars) + 1` | `users` |
| `winRate` | `AVG(student_progress.accuracy)` per user | `student_progress` |
| `quests` (count) | `COUNT(user_quests WHERE user_id = ?)` | `user_quests` |
| `initials` | 2 huruf kapital pertama dari nama | `users.name` |
| `joinDate` | Format lokal Indonesia dari `users.created_at` | `users.created_at` |
| `location` | Hardcoded: "Medan, Sumatera Utara" (belum tersimpan di DB) | - |