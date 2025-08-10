<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\LaporGiatResource\Pages;
use App\Models\LaporGiat;
use App\Models\SKT;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporGiatResource extends Resource
{
    protected static ?string $model = LaporGiat::class;
    protected static ?string $navigationGroup = 'POKUS KALTARA';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Lapor Giat';

    protected static ?string $modelLabel = 'Laporan Kegiatan';

    protected static ?string $pluralModelLabel = 'Laporan Kegiatan';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Organisasi')
                    ->description('Data organisasi akan diambil dari SKT yang telah disetujui')
                    ->schema([
                        Forms\Components\Select::make('skt_selection')
                            ->label('Pilih Organisasi (SKT)')
                            ->placeholder('Pilih organisasi dari SKT yang telah disetujui')
                            ->options(function () {
                                return SKT::where('id_pemohon', Auth::id())
                                    ->where('status', 'approved') // Only approved SKT
                                    ->pluck('nama_ormas', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $skt = SKT::find($state);
                                    if ($skt) {
                                        $set('nama_ormas', $skt->nama_ormas);
                                        $set('ketua_nama_lengkap', $skt->ketua_nama_lengkap);
                                        $set('nomor_handphone', $skt->ketua_nomor_handphone ?? $skt->user->no_telepon);
                                    }
                                }
                            })
                            ->dehydrated(false), // Don't save this field to database

                        Forms\Components\TextInput::make('nama_ormas')
                            ->label('Nama Organisasi')
                            ->required()
                            ->disabled()
                            ->dehydrated(), // Save to database

                        Forms\Components\TextInput::make('ketua_nama_lengkap')
                            ->label('Nama Lengkap Ketua')
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('nomor_handphone')
                            ->label('Nomor Handphone')
                            ->tel()
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Kegiatan')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_kegiatan')
                            ->label('Tanggal Kegiatan')
                            ->required()
                            ->maxDate(now())
                            ->displayFormat('d/m/Y')
                            ->native(false),

                        Forms\Components\FileUpload::make('laporan_kegiatan_path')
                            ->label('Upload Laporan Kegiatan (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240) // 10MB
                            ->directory('laporan-kegiatan')
                            ->visibility('private')
                            ->required()
                            ->helperText('Format: PDF, Maksimal 10MB'),

                        Forms\Components\FileUpload::make('images_paths')
                            ->label('Upload Foto Kegiatan')
                            ->image()
                            ->multiple()
                            ->maxFiles(10)
                            ->maxSize(5120) // 5MB per file
                            ->directory('foto-kegiatan')
                            ->visibility('private')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Format: JPG, PNG, JPEG. Maksimal 10 foto, 5MB per foto')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // Hidden field for user_id
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_ormas')
                    ->label('Nama Organisasi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ketua_nama_lengkap')
                    ->label('Nama Ketua')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_kegiatan')
                    ->label('Tanggal Kegiatan')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => LaporGiat::STATUS_OPTIONS[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(LaporGiat::STATUS_OPTIONS),

                Tables\Filters\Filter::make('tanggal_kegiatan')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_kegiatan', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_kegiatan', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (LaporGiat $record) => $record->status === LaporGiat::STATUS_PENDING),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (LaporGiat $record) => $record->status === LaporGiat::STATUS_PENDING),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->can('delete_any_lapor_giat')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                // Only show records for the current user
                return $query->forUser();
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Organisasi')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama_ormas')
                            ->label('Nama Organisasi'),
                        Infolists\Components\TextEntry::make('ketua_nama_lengkap')
                            ->label('Nama Lengkap Ketua'),
                        Infolists\Components\TextEntry::make('nomor_handphone')
                            ->label('Nomor Handphone'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detail Kegiatan')
                    ->schema([
                        Infolists\Components\TextEntry::make('tanggal_kegiatan')
                            ->label('Tanggal Kegiatan')
                            ->date('d F Y'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn ($state) => LaporGiat::STATUS_OPTIONS[$state] ?? $state),

                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->visible(fn ($record) => !empty($record->keterangan))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('File & Foto')
                    ->schema([
                        Infolists\Components\TextEntry::make('laporan_kegiatan_path')
                            ->label('Laporan Kegiatan')
                            ->formatStateUsing(fn ($state) => $state ? 'Laporan_Kegiatan.pdf' : 'Tidak ada file')
                            ->url(fn ($record) => $record->laporan_kegiatan_url)
                            ->openUrlInNewTab()
                            ->icon('heroicon-m-document-arrow-down'),

                        Infolists\Components\ImageEntry::make('images_paths')
                            ->label('Foto Kegiatan')
                            ->visible(fn ($record) => !empty($record->images_paths))
                            ->getStateUsing(fn ($record) => $record->image_urls)
                            ->columnSpanFull(),
                    ]),

                Infolists\Components\Section::make('Informasi Pengajuan')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Pengajuan')
                            ->dateTime('d F Y, H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d F Y, H:i'),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListLaporGiats::route('/'),
            'create' => Pages\CreateLaporGiat::route('/create'),
            'view' => Pages\ViewLaporGiat::route('/{record}'),
            'edit' => Pages\EditLaporGiat::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user'])
            ->forUser(); // Automatically filter for current user
    }
}