<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AduanAdminResource\Pages;
use App\Filament\Resources\AduanAdminResource\RelationManagers;
use App\Models\Aduan;
use App\Models\AduanKomentar;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Pages\Actions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;


class AduanAdminResource extends Resource
{
    protected static ?string $model = Aduan::class;
    public static function getNavigationLabel(): string
    {
        return 'Kelola Aduan';
    }
    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'Aplikasi';
    protected static ?int $navigationSort = 1;
    public static function form(Form $form): Form
    {   
        return $form
            ->schema([
                // Grid untuk membagi layar menjadi 2 kolom
                Forms\Components\Grid::make(6)  // Grid dengan 2 kolom (kiri-kanan)
                    ->schema([
                        // Kolom kiri: Informasi Aduan
                        Forms\Components\Section::make('Informasi Aduan')
                            ->schema([
                                Forms\Components\TextInput::make('ticket')
                                    ->label('Ticket')
                                    ->readOnly()->columnSpan(1),
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->readOnly()->columnSpan(2),
                                Forms\Components\TextInput::make('email')
                                    ->label('Email')->columnSpan(2)
                                    ->readOnly(),
                                Forms\Components\TextInput::make('telpon')
                                    ->label('Telepon')->columnSpan(1)
                                    ->readOnly(),
                                Forms\Components\TextInput::make('judul')
                                    ->label('Judul')->columnSpan(3)
                                    ->readOnly(),
                                Forms\Components\Select::make('kategori')
                                    ->options([
                                        'Aduan' => 'Aduan',
                                        'Aspirasi' => 'Aspirasi',
                                        'Kritik' => 'Kritik',
                                        'Lainnya' => 'Lainnya',
                                    ])
                                    ->label('Kategori')->columnSpan(3)
                                    ->disabled(),
                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Isi Aduan')->columnSpan(6)
                                    ->readOnly(),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pengajuan' => 'Pengajuan',
                                        'diproses' => 'Diproses',
                                        'selesai' => 'Selesai',
                                    ])
                                    ->label('Status Aduan')->columnSpan(3)
                                    ->required()
                                    ->reactive(),
                            ]),
                        ]),
            ]);
    }
    public static function canCreate(): bool
    {
        return false;
    }    

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket')->label('Ticket')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('judul')->label('Judul')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('kategori')->label('Kategori')->sortable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Pengaju'),
                Tables\Columns\TextColumn::make('created_at')->label('Tanggal Aduan')->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pengajuan' => 'Pengajuan',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                    ]),
            
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAduanAdmins::route('/'),
            'edit' => Pages\EditAduanAdmin::route('/{record}/edit'),
        ];
    }
}

