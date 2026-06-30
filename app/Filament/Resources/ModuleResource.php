<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModuleResource\Pages;
use App\Models\Module;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    /* Resource Navigation Configuration */
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Modul Pembelajaran';
    protected static ?string $modelLabel = 'Modul';

    /* Form Schema Configuration */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Modul Pembelajaran')
                    ->description('Pilih level dan atur target gestur yang akan dideteksi oleh AI.')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->label('Judul Modul')
                            ->placeholder('Contoh: Level 1 - Huruf A'),
                            
                        Select::make('level_type')
                            ->options([
                                'abjad' => 'Level 1: Abjad',
                                'kata' => 'Level 2: Kosa Kata',
                                'kalimat' => 'Level 3: Kalimat',
                            ])
                            ->required()
                            ->label('Tingkat Kesulitan'),

                        TextInput::make('target_gesture')
                            ->required()
                            ->label('Target Deteksi AI')
                            ->placeholder('Contoh: A (Harus sesuai dengan label model AI)'),
                    ])->columns(2),

                Section::make('Detail & Referensi')
                    ->schema([
                        Textarea::make('description')
                            ->label('Instruksi Pembelajaran')
                            ->rows(3)
                            ->columnSpanFull(),

                        FileUpload::make('reference_image')
                            ->label('Gambar Referensi Gestur')
                            ->directory('module-references')
                            ->image(),
                            
                        Toggle::make('is_active')
                            ->default(true)
                            ->label('Status Aktif'),
                    ])->columns(1),
            ]);
    }

    /* Table Schema Configuration */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('reference_image')
                    ->label('Visual'),
                
                TextColumn::make('title')
                    ->searchable()
                    ->label('Judul Modul'),
                    
                TextColumn::make('level_type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'abjad' => 'success',
                        'kata' => 'warning',
                        'kalimat' => 'danger',
                        default => 'gray',
                    })
                    ->label('Level'),
                    
                TextColumn::make('target_gesture')
                    ->label('Target AI'),
                
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Aktif'),
            ])
            ->filters([
                SelectFilter::make('level_type')
                    ->label('Filter Level')
                    ->options([
                        'abjad' => 'Abjad',
                        'kata' => 'Kosa Kata',
                        'kalimat' => 'Kalimat',
                    ]),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                ->label('Option')
                ->color('gray')
                ->button(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /* Infolist Schema Configuration */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Utama')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Judul Modul')
                            ->weight('bold'),
                        
                        TextEntry::make('level_type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'abjad' => 'success',
                                'kata' => 'warning',
                                'kalimat' => 'danger',
                                default => 'gray',
                            })
                            ->label('Level Pembelajaran'),
                            
                        TextEntry::make('target_gesture')
                            ->label('Target AI (Gestur)'),
                    ])->columns(3),

                InfolistSection::make('Detail Referensi & Status')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Instruksi Pembelajaran')
                            ->columnSpanFull(),
                            
                        ImageEntry::make('reference_image')
                            ->label('Visual Referensi')
                            ->height(250)
                            ->columnSpanFull(),
                            
                        IconEntry::make('is_active')
                            ->boolean()
                            ->label('Status Aktif'),
                    ])->columns(1),
            ]);
    }

    /* Resource Relationships Configuration */
    public static function getRelations(): array
    {
        return [];
    }
    
    /* Resource Pages Configuration */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListModules::route('/'),
            'create' => Pages\CreateModule::route('/create'),
            'edit' => Pages\EditModule::route('/{record}/edit'),
        ];
    }
}