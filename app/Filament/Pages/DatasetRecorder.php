<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\DatasetRecord;
use App\Models\Module;
use Filament\Notifications\Notification;
use Livewire\Attributes\On;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Response;

class DatasetRecorder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationLabel = 'Studio Dataset AI';
    protected static ?string $title = 'Perekaman Dataset Gestur';
    protected static ?string $navigationGroup = 'Manajemen AI';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.dataset-recorder';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_json')
                ->label('Export Dataset (JSON)')
                ->color('primary')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(function () {
                    // Mengambil seluruh data dari tabel dataset_records
                    $dataset = DatasetRecord::select('label', 'gesture_type', 'landmarks')->get();
                    
                    // Membuat nama file yang dinamis berdasarkan waktu unduh
                    $fileName = 'edusync_dataset_' . now()->format('Y_m_d_His') . '.json';
                    
                    // Melakukan stream download agar server tidak berat saat data membesar
                    return Response::streamDownload(function () use ($dataset) {
                        echo json_encode($dataset, JSON_PRETTY_PRINT);
                    }, $fileName, [
                        'Content-Type' => 'application/json',
                    ]);
                }),
        ];
    }

    /**
     * Mengambil daftar target gestur dari tabel modul untuk dijadikan opsi label
     */
    protected function getViewData(): array
    {
        return [
            'availableLabels' => Module::where('is_active', true)
                ->pluck('target_gesture')
                ->unique()
                ->values()
                ->toArray(),
        ];
    }

    /**
     * Fungsi ini akan dipanggil oleh JavaScript MediaPipe untuk menyimpan kordinat
     */
    #[On('save-dataset-record')]
    public function saveDataset(string $label, string $type, array $landmarks)
    {
        try {
            DatasetRecord::create([
                'label' => $label,
                'gesture_type' => $type,
                'landmarks' => $landmarks,
            ]);

            // Notification::make()
            //    ->title('Frame berhasil direkam!')
            //    ->success()
            //    ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menyimpan frame: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }
}