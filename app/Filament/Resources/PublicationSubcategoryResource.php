<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicationSubcategoryResource\Pages;
use App\Filament\Resources\PublicationSubcategoryResource\RelationManagers;
use App\Models\PublicationSubcategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PublicationSubcategoryResource extends Resource
{
    protected static ?string $model = PublicationSubcategory::class;
    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationGroup = 'Publikasi';
    protected static ?string $navigationLabel = 'Atur-SubKategori-Dokumen';
    protected static ?int $navigationSort = 5;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Sub Kategori')
                    ->required()
                    ->debounce(1000) // Reactif terhadap perubahan
                    ->afterStateUpdated(fn ($state, callable $set) => 
                        $set('slug', Str::slug($state)) // Generate slug setelah nama diubah
                    ),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->disabled()
                    ->required()
                    ->dehydrated(true)
                    ->unique('publication_subcategories', 'slug'),
                Forms\Components\Select::make('category_id')
                    ->label('Kategori Utama')
                    ->relationship('category', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama Sub Kategori'),
                Tables\Columns\TextColumn::make('slug')->label('Slug'),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori Utama'),
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
            'index' => Pages\ListPublicationSubcategories::route('/'),
            'create' => Pages\CreatePublicationSubcategory::route('/create'),
            'edit' => Pages\EditPublicationSubcategory::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}