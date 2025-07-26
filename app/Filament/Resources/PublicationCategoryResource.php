<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicationCategoryResource\Pages;
use App\Filament\Resources\PublicationCategoryResource\RelationManagers;
use App\Models\PublicationCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PublicationCategoryResource extends Resource
{
    protected static ?string $model = PublicationCategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationGroup = 'Publikasi';
    protected static ?string $navigationLabel = 'Atur-Kategori-Dokumen';
    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->label('Nama Kategori')
                ->required()
                ->debounce(1000) // Reactif terhadap perubahan
                ->afterStateUpdated(fn ($state, callable $set) => 
                    $set('slug', Str::slug($state)) // Generate slug setelah nama diubah
                ),

            Forms\Components\TextInput::make('slug')
                ->label('Slug')
                ->disabled()
                ->required()
                ->unique('publication_categories', 'slug')
                ->dehydrated(true), // Pastikan slug tetap dikirim ke server
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Kategori'),
                Tables\Columns\TextColumn::make('slug')->label('Slug'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->hiddenLabel()->tooltip('Detail'),
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()->hiddenLabel()->tooltip('Delete'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPublicationCategories::route('/'),
            'create' => Pages\CreatePublicationCategory::route('/create'),
            'edit' => Pages\EditPublicationCategory::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}