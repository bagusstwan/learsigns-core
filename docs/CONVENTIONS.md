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

## 3. Pola Akses Data (Data Access Patterns)

Proyek ini menerapkan dua pola akses data yang berbeda:

### Eloquent ORM (Pola Utama)
Digunakan oleh 6 model yang terdaftar di `app/Models/`:
- `User`, `Module`, `Quest`, `StudentProgress`, `UserQuest`, `DatasetRecord`.

### Query Builder / DB Facade (Pola Khusus)
Tabel `assignments` **tidak memiliki Model Eloquent**. Seluruh operasi CRUD pada tabel ini dilakukan melalui `DB::table('assignments')` di `EducatorController`. Keputusan arsitektur ini disengaja.

```php
// Pola yang digunakan untuk tabel assignments
DB::table('assignments')->insert([...]);
DB::table('assignments')->where('id', $id)->update([...]);
DB::table('assignments')->where('id', $id)->first();
```

### Schema Guard Pattern
`EducatorController` menggunakan `Schema::hasTable('assignments')` sebagai guard sebelum mengakses tabel `assignments`. Pola ini mencegah error saat tabel belum dimigrasi:

```php
if (\Illuminate\Support\Facades\Schema::hasTable('assignments')) {
    $assignments = DB::table('assignments')->...;
}
```

## 4. Penamaan (Naming Conventions)
| Konteks | Konvensi | Contoh |
|---|---|---|
| Tabel Database | Snake case, jamak | `student_progress`, `user_quests` |
| Model | Pascal case, tunggal | `StudentProgress`, `UserQuest` |
| Controller | Pascal case, sufiks Controller | `AuthController`, `EducatorController` |
| Variabel/Fungsi | Camel case | `$throttleKey`, `recordProgress()` |
| Endpoint API | Kebab/snake case | `/educator/dashboard-summary` |

## 5. Format Respons API
Seluruh endpoint menggunakan format JSON konsisten:
```json
{
  "status": "success | error",
  "message": "Deskripsi opsional.",
  "data": {}
}
```
Kunci utama respons adalah `status` (bukan `success`). Beberapa endpoint autentikasi memiliki format yang menyimpang dari standar ini. Lihat `docs/API_CONTRACT.md` bagian "Pengecualian Format" untuk detail lengkap.

## 6. Organisasi Route
- Seluruh rute API terkelompok di bawah prefix `/api/v1` di `routes/api.php`.
- Rute publik berada di luar middleware `auth:sanctum`.
- Rute terproteksi berada di dalam middleware `auth:sanctum`.
- Penamaan controller menggunakan FQCN (Fully Qualified Class Name) secara inline.
- Rute penyajian model AI dan panel admin berada di `routes/web.php`.

## 7. Migrasi Database
- Migrasi menggunakan format timestamp Laravel standar.
- Kolom tambahan ditambahkan melalui migrasi terpisah (contoh: `add_role_to_users_table`).
- Constraint `unique` dan foreign key diatur di level migrasi.

## 8. Database Transaction
Operasi yang melibatkan modifikasi data pada lebih dari satu tabel secara bersamaan wajib dibungkus dalam `DB::beginTransaction()`:

```php
DB::beginTransaction();
try {
    DB::table('assignments')->where('id', $id)->update([...]);
    User::where('id', $studentId)->increment('stars', $starsEarned);
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    // Handle error
}
```

Saat ini diterapkan pada:
- `EducatorController::evaluateTask()` — evaluasi tugas + distribusi bintang.
- `EducatorController::liveEvaluate()` — evaluasi praktikum langsung + distribusi bintang.

## 9. Filament Admin
- Resource panel admin menggunakan Filament v3 dengan form, table, dan infolist schema.
- Widget dashboard menggunakan class-based definition (bukan closure).
- Navigasi panel admin terisolasi pada path `/admin`.