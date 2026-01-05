<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SKTDocumentLabelResource\Pages;
use App\Filament\Resources\SKTDocumentLabelResource\RelationManagers;
use App\Models\SKTDocumentLabel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class SKTDocumentLabelResource extends Resource
{
    protected static ?string $model = SKTDocumentLabel::class;

    public static function getNavigationLabel(): string
    {
        return 'Atur Syarat SKT';
    }
    protected static ?string $navigationGroup = 'Aplikasi';
    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('label')
                    ->label('Document Label')
                    ->required(),
                TextInput::make('collection_name')
                    ->label('Spatie Media Collection Name')
                    ->required(),
                Textarea::make('tooltip')
                    ->label('Deskripsi')
                    ->required(),
                Toggle::make('required')
                    ->label('Required')
                    ->default(true),
            ])
            ;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')->label('Document Label'),
                TextColumn::make('collection_name')->label('Nama Koleksi'),
                TextColumn::make('group')->label('Nama Group'),
                IconColumn::make('required')->label('Required')
                            ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->hiddenLabel()->tooltip('Detail'),
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()->hiddenLabel()->tooltip('Delete'),
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
            'index' => Pages\ListSKTDocumentLabels::route('/'),
            'create' => Pages\CreateSKTDocumentLabel::route('/create'),
            'edit' => Pages\EditSKTDocumentLabel::route('/{record}/edit'),
        ];
    }
}
