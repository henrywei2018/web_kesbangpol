<?php

// app/Filament/Resources/GaleriResource.php
namespace App\Filament\Resources;

use App\Filament\Resources\GaleriResource\Pages;
use App\Models\Galeri;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;


class GaleriResource extends Resource
{
    protected static ?string $model = Galeri::class;
    public static function getNavigationLabel(): string
    {
        return 'Galeri';
    }
    protected static ?string $navigationGroup = 'Media';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('judul')
                ->required()
                ->maxLength(255)
                ->reactive()
                ->debounce('1600ms')  // This will make the field update reactively
                ->afterStateUpdated(function ($state, $set) {
                    // Dynamically update the slug based on the 'judul' field
                    $set('slug', Str::slug($state));
                }),
            TextInput::make('slug')
                ->disabled()  // Display the slug but don't allow manual editing
                ->visibleOn(['edit', 'create']),  // Show on create and edit
        
            Select::make('kategori')
                ->options([
                    'kegiatan' => 'Kegiatan',
                    'awards' => 'Awards',
                    'lainnya' => 'Lainnya',
                ])
                ->required(),
            Textarea::make('deskripsi')
                ->required(),
            SpatieTagsInput::make('tags'),
        
            // Corrected FileUpload component for multiple media
            FileUpload::make('images')
                ->label('Galeri Images')
                ->multiple()
                ->image()
                ->maxFiles(10)
                ->directory(function ($get) {
                    $slug = $get('slug');
                    return 'galeri/' . ($slug ? $slug : 'default-directory');
                })
                ->disk('public')
                ->storeFileNamesIn('images')
                ->preserveFilenames()
                ->afterStateUpdated(function ($state) {
                    foreach ($state as $path) {
                        // Resize the image using Intervention Image
                        $imagePath = storage_path('app/public/' . $path);
                        
                        if (file_exists($imagePath)) {
                            $img = Image::make($imagePath)->resize(1170, 540, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
    
                            // Save the resized image back to the file system
                            $img->save($imagePath);
                        }
                    }
                }), // Optionally preserve original filenames
        ]);                
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('judul')->sortable()->searchable(),
                TextColumn::make('kategori')->sortable()->searchable(),
                TextColumn::make('created_at')->label('Dibuat pada'),
                TextColumn::make('updated_at')->label('Diperbarui pada'),
            
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
            
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGaleris::route('/'),
            'create' => Pages\CreateGaleri::route('/create'),
            'edit' => Pages\EditGaleri::route('/{record}/edit'),
        ];
    }
}