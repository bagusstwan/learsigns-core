<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AiTestingStudio extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-beaker';
    protected static ?string $navigationGroup = 'Manajemen AI';
    protected static ?string $navigationLabel = 'Ruang Uji AI';
    protected static ?string $title = 'Live Testing: EduSync AI Engine';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.ai-testing-studio';
}