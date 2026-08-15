<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class UserRoleChart extends ChartWidget
{
    /**
     * Judul representatif yang ditampilkan pada bagian atas komponen (header widget).
     */
    protected static ?string $heading = 'Distribusi Peran Pengguna';

    /**
     * Menentukan indeks urutan render komponen pada antarmuka dashboard.
     */
    protected static ?int $sort = 2;
    
    /**
     * Menetapkan batas tinggi absolut komponen visual untuk memastikan 
     * proporsi tata letak yang simetris dan konsisten terhadap komponen di kolom bersebelahan.
     */
    protected static ?string $maxHeight = '360px'; 

    /**
     * Mengagregasi dan memetakan data statistik pengguna berdasarkan peran (role)
     * ke dalam struktur dataset grafikal yang kompatibel dengan standar Chart.js.
     *
     * @return array<string, mixed> Dataset dan konfigurasi label visual.
     */
    protected function getData(): array
    {
        // Mengeksekusi kueri agregasi untuk menghitung entitas berdasarkan spesifikasi peran
        $students   = User::where('role', 'student')->count();
        $teachers   = User::where('role', 'teacher')->count();
        $corporates = User::where('role', 'corporate')->count();

        return [
            'datasets' => [
                [
                    'label'           => 'Total Pengguna',
                    'data'            => [$students, $teachers, $corporates],
                    'backgroundColor' => [
                        '#10B981', // Kode heksadesimal untuk peran Siswa (Emerald)
                        '#3B82F6', // Kode heksadesimal untuk peran Tenaga Pendidik (Blue)
                        '#F59E0B', // Kode heksadesimal untuk peran Institusi (Amber)
                    ],
                    'borderWidth'     => 0, // Mengeliminasi garis batas untuk estetika desain flat
                    'hoverOffset'     => 6, // Memberikan efek transisi ekspansi saat kursor diarahkan (hover)
                ],
            ],
            'labels' => ['Siswa', 'Tenaga Pendidik', 'Institusi'],
        ];
    }

    /**
     * Mendeklarasikan tipe rendering visual yang akan diimplementasikan oleh engine grafik.
     *
     * @return string Tipe bagan (contoh: 'bar', 'line', 'doughnut').
     */
    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * Menginjeksi konfigurasi parameter tingkat lanjut (advanced configuration) 
     * langsung ke dalam objek instansiasi Chart.js di sisi klien (frontend).
     *
     * @return array<string, mixed> Opsi modifikasi render grafik.
     */
    protected function getOptions(): array
    {
        return [
            'scales' => [
                // Menonaktifkan garis sumbu dan grid latar belakang (grid lines) 
                // yang tidak relevan untuk grafik melingkar.
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            // Menentukan persentase radius pemotongan bagian tengah (inner radius).
            'cutout' => '60%', 
            // Menonaktifkan rasio aspek statis agar komponen grafik dapat 
            // beradaptasi secara fleksibel terhadap kontainer tinggi maksimal (maxHeight).
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    // Merelokasi legenda informasi ke bagian bawah elemen grafik.
                    'position' => 'bottom', 
                    'labels' => [
                        // Mengonversi indikator legenda dari bentuk kotak (default) 
                        // menjadi titik lingkar (point style) untuk tampilan modern.
                        'usePointStyle' => true, 
                        'padding'       => 20,
                    ],
                ],
            ],
        ];
    }
}