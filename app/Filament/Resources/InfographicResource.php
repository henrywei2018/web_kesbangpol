<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InfographicResource\Pages;
use Illuminate\Support\Str;
use App\Models\Infographic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Components\FileUpload;

class InfographicResource extends Resource
{
    protected static ?string $model = Infographic::class;
    public static function getNavigationLabel(): string
    {
        return 'Infografis';
    }
    protected static ?string $navigationGroup = 'Media';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('judul')
                    ->label('Judul')
                    ->required()
                    ->maxLength(255),
                // Category input
                Forms\Components\Select::make('kategori')
                    ->label('Kategori')
                    ->required()
                    ->options([
                        'ekonomi' => 'Ekonomi',
                        'kesehatan' => 'Kesehatan',
                        'pendidikan' => 'Pendidikan',
                        'pemerintahan' => 'Pemerintahan',
                        'sosial' => 'Sosial',
                        'teknologi' => 'Teknologi',
                        'lainnya' => 'Lainnya',
                    ]),
                // Description input
                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->required(),
                Forms\Components\SpatieMediaLibraryFileUpload::make('infographic_images')
                    ->collection('infographics')
                    ->label('Gambar Infografis')
                    ->image() // Hanya izinkan gambar                    
                    ->directory('infographics') // Direktori penyimpanan
                    ->acceptedFileTypes(['image/jpeg', 'image/png']) // Batasi tipe file                    
                    ->maxSize(2048) // Maksimal ukuran file 2MB
                    ->required(),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')->label(label: 'Judul')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('kategori'),
                Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime(),
                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Thumbnail')
                    ->getStateUsing(fn (Infographic $record) => $record->getFirstMediaUrl('infographics', 'thumb')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            // Clear the media collection before deleting
                            $record->clearMediaCollection('infographic_images');
                            $record->delete();
                        }
                    })
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->icon('heroicon-o-trash'),
            ]);
    }
    public static function getRelations(): array
    {
        return [];
    }

    public static function beforeDelete($record): void
    {
        $record->deleteMedia(); // Clear media collection before deletion
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInfographics::route('/'),
            'create' => Pages\CreateInfographic::route('/create'),
            'edit' => Pages\EditInfographic::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}
