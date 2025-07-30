<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporATHGResource\Pages;
use App\Filament\Resources\LaporATHGResource\RelationManagers;
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

    protected static ?string $navigationGroup = 'POKUS KALTARA';
    protected static ?string $navigationLabel = 'Lapor ATHG';
    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?int $navigationSort = 2;

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
