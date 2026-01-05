<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PegawaiResource\Pages;
use App\Models\Pegawai;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;

class PegawaiResource extends Resource
{
    protected static ?string $model = Pegawai::class;

    public static function getNavigationLabel(): string
    {
        return 'Data Pegawai';
    }
    protected static ?string $navigationGroup = 'Data Umum';
    protected static ?int $navigationSort = 11;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nip')
                    ->required()
                    ->maxLength(50),

                Forms\Components\TextInput::make('nama_pegawai')
                    ->required()
                    ->maxLength(100),

                Forms\Components\TextInput::make('jabatan')
                    ->maxLength(100),

                Forms\Components\Select::make('pangkat_gologan')
                    ->label('Pangkat Golongan')
                    ->required()
                    ->options(function ($record) {
                        if ($record) {
                            // In edit form, pull from the database (or cache)
                            return Cache::remember('pangkat_gologan_options', 60*60, function () {
                                return Pegawai::pluck('pangkat_gologan', 'pangkat_gologan')->toArray();
                            });
                        } else {
                            // In create form, use static options
                            return [
                                'Juru Muda / Ia' => 'Juru Muda / Ia',
                                'Juru Muda Tingkat I / Ib' => 'Juru Muda Tingkat I / Ib',
                                'Juru / Ic' => 'Juru / Ic',
                                'Juru Tingkat I / Id' => 'Juru Tingkat I / Id',
                                'Pengatur Muda / IIa' => 'Pengatur Muda / IIa',
                                'Pengatur Muda Tingkat I / IIb' => 'Pengatur Muda Tingkat I / IIb',
                                'Pengatur / IIc' => 'Pengatur / IIc',
                                'Pengatur Tingkat I / IId' => 'Pengatur Tingkat I / IId',
                                'Penata Muda / IIIa' => 'Penata Muda / IIIa',
                                'Penata Muda Tingkat I / IIIb' => 'Penata Muda Tingkat I / IIIb',
                                'Penata / IIIc' => 'Penata / IIIc',
                                'Penata Tingkat I / IIId' => 'Penata Tingkat I / IIId',
                                'Pembina / IVa' => 'Pembina / IVa',
                                'Pembina Tingkat I / IVb' => 'Pembina Tingkat I / IVb',
                                'Pembina Muda / IVc' => 'Pembina Muda / IVc',
                                'Pembina Madya / IVd' => 'Pembina Madya / IVd',
                                'Pembina Utama / IVe' => 'Pembina Utama / IVe',
                            ];
                        }
                    }),

                Forms\Components\TextInput::make('kontak')
                    ->maxLength(50)
                    ->rule('regex:/^\+62\d{9,13}$/') // Ensures the number starts with +62 and contains 9 to 13 digits
                    ->afterStateUpdated(function (callable $set, $state, $get) {
                    $formattedNumber = self::formatPhoneNumber($state);
                    $set('kontak', $formattedNumber);
                    }),
                Forms\Components\SpatieMediaLibraryFileUpload::make('pegawai_photo')
                    ->collection('pegawai_photos')
                    ->label('Foto Pegawai')
                    ->image()                    
                    ->directory('pegawai/photos')
                    ->acceptedFileTypes(['image/jpeg', 'image/png'])                    
                    ->maxSize(2048)
                    ->nullable(),
            ]);
    }
    
    protected static function formatPhoneNumber($phoneNumber)
    {
        // Check if the number already starts with +62, if not, prepend it
        if (!str_starts_with($phoneNumber, '+62')) {
            return '+62' . ltrim($phoneNumber, '0'); // Remove leading 0 if present
        }

        return $phoneNumber; // Return as-is if it already starts with +62
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nip')
                    ->searchable(),

                Tables\Columns\TextColumn::make('nama_pegawai')
                    ->searchable(),

                Tables\Columns\TextColumn::make('jabatan')
                    ->searchable(),

                // Fix typo: pangkat_gologan -> pangkat_golongan
                Tables\Columns\TextColumn::make('pangkat_gologan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('kontak')
                    ->searchable(),
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
            'index' => Pages\ListPegawais::route('/'),
            'create' => Pages\CreatePegawai::route('/create'),
            'edit' => Pages\EditPegawai::route('/{record}/edit'),
        ];
    }
}
