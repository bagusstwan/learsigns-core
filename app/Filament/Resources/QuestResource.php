<?php

namespace App\Filament\Resources;

use App\Filament\Resources\QuestResource\Pages;
use App\Models\Quest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class QuestResource extends Resource
{
    protected static ?string $model = Quest::class;

    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    protected static ?string $navigationLabel = 'Manajemen Quest';
    
    protected static ?string $modelLabel = 'Tantangan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Tantangan (Quest)')
                    ->description('Atur misi harian yang harus diselesaikan siswa.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Misi')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Pemanasan Isyarat'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Misi')
                            ->required()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                            
                        Forms\Components\TextInput::make('target_gesture')
                            ->label('Target Gestur')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Contoh: A'),
                            
                        Forms\Components\TextInput::make('reward_stars')
                            ->label('Hadiah Bintang')
                            ->required()
                            ->numeric()
                            ->default(50)
                            ->minValue(1),
                            
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Matikan jika misi ini sedang tidak berlaku.'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Misi')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('target_gesture')
                    ->label('Target')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\TextColumn::make('reward_stars')
                    ->label('Bintang')
                    ->numeric()
                    ->sortable()
                    ->icon('heroicon-m-star')
                    ->iconColor('warning'),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // ViewAction sekarang akan otomatis terbuka sebagai Modal 
                    // karena rutenya sudah dihapus di bawah
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->label('Options')
                ->icon('heroicon-m-ellipsis-vertical')
                ->button()
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Detail Misi')
                    ->icon('heroicon-m-information-circle')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('title')
                                    ->label('Judul Misi')
                                    ->weight('bold')
                                    ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                                    
                                Infolists\Components\IconEntry::make('is_active')
                                    ->label('Status Aktif')
                                    ->boolean(),
                                    
                                Infolists\Components\TextEntry::make('target_gesture')
                                    ->label('Target Gestur')
                                    ->badge()
                                    ->color('info'),
                                    
                                Infolists\Components\TextEntry::make('reward_stars')
                                    ->label('Hadiah Bintang')
                                    ->badge()
                                    ->color('warning')
                                    ->icon('heroicon-m-star'),
                            ]),
                            
                        Infolists\Components\TextEntry::make('description')
                            ->label('Deskripsi Misi')
                            ->columnSpanFull()
                            ->prose(),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuests::route('/'),
            'create' => Pages\CreateQuest::route('/create'),
            'edit' => Pages\EditQuest::route('/{record}/edit'),
        ];
    }
}