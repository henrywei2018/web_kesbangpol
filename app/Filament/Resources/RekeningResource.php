<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RekeningResource\Pages;
use App\Models\Rekening;
use App\Models\Pegawai; // Import Pegawai model
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Carbon\Carbon; // Import Carbon for date manipulation

class RekeningResource extends Resource
{
    protected static ?string $model = Rekening::class;
    public static function getNavigationLabel(): string
    {
        return 'Pengaturan Rekening';
    }
    protected static ?string $navigationGroup = 'Data Umum';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Form $form): Form
    {
        $currentYear = Carbon::now()->year; // Get the current year
        $yearsRange = range($currentYear - 2, $currentYear + 2);
        return $form
            ->schema([
                Forms\Components\Select::make('tahun')
                    ->label('Tahun')
                    ->required()
                    ->options(array_combine($yearsRange, $yearsRange)) // Use the years range as options
                    ->default($currentYear), // Set current year as default

                Forms\Components\TextInput::make('nama_rekening')
                    ->label('Nama Rekening')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('nomor_rekening')
                    ->label('Nomor Rekening')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true),

                Forms\Components\Select::make('kode_instansi_id')
                    ->label('Nama Instansi')
                    ->relationship('kodeInstansi', 'nama_instansi') // Display the 'nama_instansi' field in the form
                    ->required(),

                    Forms\Components\Select::make('jenis_rekening')
                    ->label('Jenis Rekening')
                    ->required()
                    ->options([
                        'Sub Kegiatan' => 'Sub Kegiatan',
                        'Belanja' => 'Belanja',
                    ])
                    ->reactive() // Makes the select field reactive so that changes can trigger other field updates
                    ->afterStateUpdated(function (callable $set, $state) {
                        if ($state === 'Belanja') {
                            // Hide or disable pptk field when 'Belanja' is selected
                            $set('pptk', null); // Reset the pptk field if 'Belanja' is selected
                        }
                    }),

                // Adding the searchable pptk field
                Forms\Components\Select::make('pptk')
                    ->label('PPTK')
                    ->relationship('pptkPegawai', 'nama_pegawai') // Link to pegawai model
                    ->searchable() // Make the field searchable
                    ->preload() // Preload for faster loading
                    ->required()
                    ->visible(fn ($get) => $get('jenis_rekening') === 'Sub Kegiatan') // Only visible if 'Sub Kegiatan' is selected
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_rekening')
                    ->label('Nama Rekening')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nomor_rekening')
                    ->label('Nomor Rekening')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('jenis_rekening')
                    ->label('Jenis Rekening')
                    ->sortable(),

                // Display the related 'nama_instansi' field
                Tables\Columns\TextColumn::make('kodeInstansi.nama_instansi')
                    ->label('Instansi')
                    ->sortable(),

                // Display the 'pptk' related pegawai's name
                Tables\Columns\TextColumn::make('pptkPegawai.nama_pegawai')
                    ->label('PPTK')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                // Add necessary filters here
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRekenings::route('/'),
            'create' => Pages\CreateRekening::route('/create'),
            'edit' => Pages\EditRekening::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}
