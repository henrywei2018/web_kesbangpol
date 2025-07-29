<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\LaporATHGResource\Pages;
use App\Filament\Public\Resources\LaporATHGResource\RelationManagers;
use App\Models\LaporATHG;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class LaporATHGResource extends Resource
{
    protected static ?string $model = LaporATHG::class;
    protected static ?string $slug = 'lapor-athg';
    public static function getNavigationLabel(): string
    {
        return 'Lapor ATHG';
    }
    protected static ?string $navigationGroup = 'POKUS KALTARA';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLaporATHGS::route('/'),
            'create' => Pages\CreateLaporATHG::route('/create'),
            'edit' => Pages\EditLaporATHG::route('/{record}/edit'),
        ];
    }
}
