<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentLabelResource\Pages;
use App\Filament\Resources\DocumentLabelResource\RelationManagers;
use App\Models\DocumentLabel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;

class DocumentLabelResource extends Resource
{
    protected static ?string $model = DocumentLabel::class;
    public static function getNavigationLabel(): string
    {
        return 'Atur Syarat SKL';
    }
    protected static ?string $navigationGroup = 'Aplikasi';
    protected static ?int $navigationSort = 3;

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
                TextColumn::make('collection_name')->label('Spatie Collection'),
                IconColumn::make('required')->label('Required')
                            ->boolean(),
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
            'index' => Pages\ListDocumentLabels::route('/'),
            'create' => Pages\CreateDocumentLabel::route('/create'),
            'edit' => Pages\EditDocumentLabel::route('/{record}/edit'),
        ];
    }
}
