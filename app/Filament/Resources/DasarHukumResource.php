<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DasarHukumResource\Pages;
use App\Filament\Resources\DasarHukumResource\RelationManagers;
use App\Models\DasarHukum;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DasarHukumResource extends Resource
{
    protected static ?string $model = DasarHukum::class;

    public static function getNavigationLabel(): string
    {
        return 'Pengaturan Dasar SPPD';
    }
    protected static ?string $navigationGroup = 'Data Umum';
    protected static ?int $navigationSort = 13;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema(components: [
                Forms\Components\TextInput::make(name: 'tahun')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make(name: 'deskripsi')
                    ->maxLength(100),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(components: [
                Tables\Columns\TextColumn::make(name: 'tahun')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make(name: 'deskripsi')
                    ->searchable(),
            ])
            ->filters(filters: [
                //
            ])
            ->actions(actions: [
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions(actions: [
                Tables\Actions\BulkActionGroup::make(actions: [
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListDasarHukums::route(path: '/'),
            'create' => Pages\CreateDasarHukum::route(path: '/create'),
            'edit' => Pages\EditDasarHukum::route(path: '/{record}/edit'),
        ];
    }
}
