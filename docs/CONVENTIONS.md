# Konvensi Kode (Coding Conventions)

Proyek ini mematuhi standar kode bersih (clean code) untuk memastikan keterkelolaan dan skalabilitas.

## 1. Standar Penulisan PHP
- Mematuhi standar **PSR-12** (divalidasi oleh Laravel Pint).
- Gunakan fitur PHP 8.2+: `match` expressions, named arguments, constructor promotion.
- Gunakan deklarasi tipe untuk argumen fungsi dan nilai kembalian.

## 2. Arsitektur Controller
- **Controller:** Bertugas memvalidasi permintaan HTTP secara inline (`$request->validate()`), menjalankan kueri, dan mengembalikan JSON.
- **Model:** Berisi definisi `$fillable`/`$guarded`, relasi tabel, mutator, dan type casting.
- **Catatan:** Proyek ini belum memiliki lapisan Service Class (`app/Services/`). Logika bisnis saat ini berada langsung di Controller.

## 3. Penamaan (Naming Conventions)
| Konteks | Konvensi | Contoh |
|---|---|---|
| Tabel Database | Snake case, jamak | `student_progress`, `user_quests` |
| Model | Pascal case, tunggal | `StudentProgress`, `UserQuest` |
| Controller | Pascal case, sufiks Controller | `AuthController`, `EducatorController` |
| Variabel/Fungsi | Camel case | `$throttleKey`, `recordProgress()` |
| Endpoint API | Kebab/snake case | `/educator/dashboard-summary` |

## 4. Format Respons API
Seluruh endpoint menggunakan format JSON konsisten:
```json
{
  "status": "success | error",
  "message": "Deskripsi opsional.",
  "data": {}
}
```
Kunci utama respons adalah `status` (bukan `success`). Lihat `docs/API_CONTRACT.md` untuk detail lengkap.

## 5. Organisasi Route
- Seluruh rute API terkelompok di bawah prefix `/api/v1` di `routes/api.php`.
- Rute publik berada di luar middleware `auth:sanctum`.
- Rute terproteksi berada di dalam middleware `auth:sanctum`.
- Penamaan controller menggunakan FQCN (Fully Qualified Class Name) secara inline.

## 6. Migrasi Database
- Migrasi menggunakan format timestamp Laravel standar.
- Kolom tambahan ditambahkan melalui migrasi terpisah (contoh: `add_role_to_users_table`).
- Constraint `unique` dan foreign key diatur di level migrasi.

## 7. Filament Admin
- Resource panel admin menggunakan Filament v3 dengan form, table, dan infolist schema.
- Widget dashboard menggunakan class-based definition (bukan closure).
- Navigasi panel admin terisolasi pada path `/admin`.