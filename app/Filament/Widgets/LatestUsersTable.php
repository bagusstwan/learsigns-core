<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestUsersTable extends BaseWidget
{
    /**
     * Menentukan urutan render widget pada antarmuka dashboard.
     * Nilai yang lebih besar akan merender widget pada posisi yang lebih bawah.
     */
    protected static ?int $sort = 4;

    /**
     * Mengatur rentang kolom (column span) agar komponen widget 
     * membentang secara penuh (full-width) pada sistem grid Filament.
     */
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                /**
                 * Memuat 5 data entitas pengguna teratas berdasarkan 
                 * riwayat pendaftaran terbaru (descending).
                 */
                User::query()->latest()->limit(5)
            )
            ->heading('Riwayat Pendaftaran Pengguna Terbaru')
            ->columns([
                /**
                 * Konfigurasi Kolom: Nama Lengkap
                 */
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->weight('bold')
                    ->searchable(),

                /**
                 * Konfigurasi Kolom: Alamat Surel
                 */
                Tables\Columns\TextColumn::make('email')
                    ->label('Alamat Surel')
                    ->icon('heroicon-m-envelope')
                    ->searchable(),

                /**
                 * Konfigurasi Kolom: Tipe Akses (Role)
                 * Menerapkan indikator visual (badge) dengan pemetaan warna 
                 * dan pelokalan string (translasi) berbasis match expression.
                 */
                Tables\Columns\TextColumn::make('role')
                    ->label('Tipe Akses')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'student'   => 'success',
                        'teacher'   => 'info',
                        'corporate' => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'student'   => 'Siswa',
                        'teacher'   => 'Tenaga Pendidik',
                        'corporate' => 'Institusi',
                        default     => $state,
                    }),

                /**
                 * Konfigurasi Kolom: Waktu Registrasi
                 */
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Registrasi')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            /**
             * Menonaktifkan sistem paginasi untuk mempertahankan 
             * dimensi tabel yang ringkas dan statis pada halaman dashboard.
             */
            ->paginated(false);
    }
}