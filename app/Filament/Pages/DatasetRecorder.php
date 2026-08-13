<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Module;
use Filament\Actions\Action;
use Livewire\Component;

class DatasetRecorder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-video-camera';
    protected static ?string $navigationLabel = 'Studio Dataset AI';
    protected static ?string $title = 'Perekaman Dataset Gestur';
    protected static ?string $navigationGroup = 'Manajemen AI';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.dataset-recorder';

    /** Mengatur aksi header halaman dan mengarahkannya melalui Livewire dispatch */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_json')
                ->label('Ekspor Dataset JSON')
                ->color('warning')
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn (Component $livewire) => $livewire->dispatch('trigger-export-json')),
            
            Action::make('clear_memory')
                ->label('Bersihkan Memori')
                ->color('gray')
                ->icon('heroicon-o-trash')
                ->action(fn (Component $livewire) => $livewire->dispatch('trigger-clear-memory')),
        ];
    }

    /** Mengambil target gestur dan tipe tingkat pembelajaran masing masing dari basis data */
    protected function getViewData(): array
    {
        $modulesData = Module::where('is_active', true)
            ->select('level_type', 'target_gesture')
            ->distinct()
            ->get()
            ->toArray();

        return [
            'modulesDataJson' => json_encode($modulesData),
        ];
    }
}