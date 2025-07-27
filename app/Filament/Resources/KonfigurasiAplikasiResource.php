<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KonfigurasiAplikasiResource\Pages;
use App\Filament\Resources\KonfigurasiAplikasiResource\RelationManagers;
use App\Models\KodeInstansi;
use App\Models\Pegawai;
use App\Models\Wilayah;
use App\Models\KonfigurasiAplikasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KonfigurasiAplikasiResource extends Resource
{
    protected static ?string $model = KonfigurasiAplikasi::class;
    public static function getNavigationLabel(): string
    {
        return 'Pengaturan';
    }
    protected static ?string $navigationGroup = 'Data Umum';
    protected static ?int $navigationSort = 9;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('pengesah_spt_id')
                    ->label('Nama Pengesah')
                    ->options(Pegawai::all()->pluck('nama_pegawai', 'id')),
                Forms\Components\Select::make('pa_id')
                    ->label('Nama Penguna Anggaran')
                    ->options(Pegawai::all()->pluck('nama_pegawai', 'id')),
                Forms\Components\Select::make('skpd')
                    ->label('Nama Instansi')
                    ->options(KodeInstansi::all()->pluck('nama_instansi', 'nama_instansi')),
                Forms\Components\Select::make('lokasi_asal')
                    ->label('Lokasi Asal')
                    ->options(Wilayah::where('level', 'kecamatan')->pluck('nama', 'nama')),
            ]); 
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pengesah_spt_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pa_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('skpd')
                    ->searchable(),
                Tables\Columns\TextColumn::make('lokasi_asal')
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
            'index' => Pages\ListKonfigurasiAplikasis::route('/'),
            'create' => Pages\CreateKonfigurasiAplikasi::route('/create'),
            'edit' => Pages\EditKonfigurasiAplikasi::route('/{record}/edit'),
        ];
    }
}
