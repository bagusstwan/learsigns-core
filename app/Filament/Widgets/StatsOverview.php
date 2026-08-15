<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Module;
use App\Models\Quest;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    /**
     * Menentukan prioritas urutan render komponen pada antarmuka dashboard.
     * Menggunakan nilai indeks 1 untuk memastikan widget kartu statistik ini 
     * selalu dirender pada hierarki paling atas (top-level view).
     */
    protected static ?int $sort = 1;

    /**
     * Mengagregasi dan mendefinisikan metrik statistik utama (Key Performance Indicators) 
     * untuk direpresentasikan ke dalam antarmuka kartu ikhtisar (overview cards).
     *
     * @return array<Stat> Kumpulan objek metrik statistik.
     */
    protected function getStats(): array
    {
        return [
            /**
             * Kartu Metrik 1: Total Pengguna Keseluruhan
             * Melakukan komputasi terhadap seluruh entitas akun yang terdaftar 
             * di dalam basis data tanpa membedakan parameter peran (role).
             */
            Stat::make('Total Pengguna', User::count())
                ->description('Seluruh entitas terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
                
            /**
             * Kartu Metrik 2: Akumulasi Siswa Aktif
             * Mengeksekusi kueri spesifik untuk menghitung entitas dengan 
             * hak akses (role) 'student'. Diinjeksikan dengan array data 
             * untuk merender visualisasi tren performa (sparkline chart).
             */
            Stat::make('Siswa Aktif', User::where('role', 'student')->count())
                ->description('Peserta didik platform')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]), 
                
            /**
             * Kartu Metrik 3: Ketersediaan Modul Pembelajaran
             * Mengkalkulasi total modul materi edukasi (bahasa isyarat) 
             * yang telah dipublikasikan ke dalam sistem.
             */
            Stat::make('Modul Pembelajaran', Module::count())
                ->description('Total modul bahasa isyarat')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info'),
                
            /**
             * Kartu Metrik 4: Status Quest Interaktif
             * Memfilter dan menghitung jumlah tugas/modul interaktif yang 
             * saat ini berstatus aktif (boolean is_active = true) dan 
             * valid untuk dikerjakan oleh entitas siswa.
             */
            Stat::make('Quest Interaktif', Quest::where('is_active', true)->count())
                ->description('Tugas yang sedang berjalan')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning'),
        ];
    }
}