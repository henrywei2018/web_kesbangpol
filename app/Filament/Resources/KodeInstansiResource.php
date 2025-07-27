<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KodeInstansiResource\Pages;
use App\Models\KodeInstansi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KodeInstansiResource extends Resource
{
    // Define the associated model
    protected static ?string $model = KodeInstansi::class;

    public static function getNavigationLabel(): string
    {
        return 'Data Instansi';
    }
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'Data Umum';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_instansi')
                    ->label('Nama Instansi')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Created At')
                    ->disabled()  // Disable it as it will be automatically managed by Laravel
                    ->hiddenOn(['create', 'edit']),  // Hide it on the create form
                
                Forms\Components\DateTimePicker::make('updated_at')
                    ->label('Updated At')
                    ->disabled()  // Disable it as it will be automatically managed by Laravel
                    ->hiddenOn(['create', 'edit']),  // Hide it on the create form
            ]);
    }

    /**
     * Define the table schema for listing resources.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_instansi')
                    ->label('Nama Instansi')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // Add any necessary filters here
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /**
     * Define any relationships for the resource.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            // Define any relationship managers here if needed
        ];
    }

    /**
     * Define the pages for the resource.
     *
     * @return array
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKodeInstansis::route('/'),
            'create' => Pages\CreateKodeInstansi::route('/create'),
            'edit' => Pages\EditKodeInstansi::route('/{record}/edit'),
        ];
    }
}
