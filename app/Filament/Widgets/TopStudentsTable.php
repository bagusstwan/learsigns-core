<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopStudentsTable extends BaseWidget
{
    /**
     * Menentukan urutan render widget pada antarmuka dashboard.
     * Menggunakan nilai 3 untuk menyelaraskan posisi bersebelahan dengan grafik Doughnut.
     */
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                /**
                 * Memuat 5 entitas siswa teratas berdasarkan akumulasi metrik bintang (stars)
                 * dengan pengurutan menurun (descending).
                 */
                User::query()
                    ->where('role', 'student')
                    ->orderByDesc('stars')
                    ->limit(5)
            )
            ->heading('Peringkat Bintang Siswa (Top 5)')
            ->columns([
                /**
                 * Konfigurasi Kolom: Indeks Penomoran
                 * Menghasilkan nomor urut baris secara otomatis tanpa bergantung pada ID database.
                 */
                Tables\Columns\TextColumn::make('index')
                    ->label('#')
                    ->rowIndex(),

                /**
                 * Konfigurasi Kolom: Nama Siswa
                 * Menampilkan nama entitas dengan bobot tipografi tebal (bold) untuk penekanan visual.
                 */
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Siswa')
                    ->weight('bold')
                    ->searchable(),

                /**
                 * Konfigurasi Kolom: Akumulasi Bintang
                 * Merepresentasikan nilai metrik dalam bentuk lencana visual (badge) 
                 * berwarna peringatan (warning/amber) yang disertai ikon representatif.
                 */
                Tables\Columns\TextColumn::make('stars')
                    ->label('Total Bintang')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-m-star')
                    ->sortable(),
            ])
            /**
             * Menonaktifkan sistem paginasi antarmuka untuk memastikan dimensi tabel 
             * tetap ringkas, statis, dan proporsional di dalam tata letak dashboard.
             */
            ->paginated(false);
    }
}