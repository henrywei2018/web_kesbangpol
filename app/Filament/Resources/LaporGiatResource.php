<?php
// File: app/Filament/Resources/LaporGiatResource.php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporGiatResource\Pages;
use App\Models\LaporGiat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;

class LaporGiatResource extends Resource
{
    protected static ?string $model = LaporGiat::class;
    protected static ?string $navigationGroup = 'POKUS KALTARA';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Laporan Kegiatan';

    protected static ?string $modelLabel = 'Laporan Kegiatan';

    protected static ?string $pluralModelLabel = 'Laporan Kegiatan';


    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Organisasi')
                    ->schema([
                        Forms\Components\TextInput::make('nama_ormas')
                            ->label('Nama Organisasi')
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('ketua_nama_lengkap')
                            ->label('Nama Lengkap Ketua')
                            ->required()
                            ->disabled(),

                        Forms\Components\TextInput::make('nomor_handphone')
                            ->label('Nomor Handphone')
                            ->tel()
                            ->required()
                            ->disabled(),

                        Forms\Components\DatePicker::make('tanggal_kegiatan')
                            ->label('Tanggal Kegiatan')
                            ->required()
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Review & Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(LaporGiat::STATUS_OPTIONS)
                            ->required()
                            ->native(false),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan/Catatan Admin')
                            ->placeholder('Berikan keterangan terkait status laporan ini...')
                            ->columnSpanFull()
                            ->rows(3),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('File & Dokumen')
                    ->schema([
                        Forms\Components\FileUpload::make('laporan_kegiatan_path')
                            ->label('Laporan Kegiatan (PDF)')
                            ->disabled()
                            ->downloadable(),

                        Forms\Components\FileUpload::make('images_paths')
                            ->label('Foto Kegiatan')
                            ->multiple()
                            ->disabled()
                            ->downloadable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pemohon')
                    ->searchable(['users.firstname', 'users.lastname'])
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_ormas')
                    ->label('Nama Organisasi')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

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
                    ->formatStateUsing(fn($state) => LaporGiat::STATUS_OPTIONS[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\IconColumn::make('has_files')
                    ->label('File')
                    ->getStateUsing(fn($record) => !empty($record->laporan_kegiatan_path))
                    ->boolean()
                    ->trueIcon('heroicon-o-document-text')
                    ->falseIcon('heroicon-o-x-mark'),

                Tables\Columns\IconColumn::make('has_images')
                    ->label('Foto')
                    ->getStateUsing(fn($record) => !empty($record->images_paths))
                    ->boolean()
                    ->trueIcon('heroicon-o-photo')
                    ->falseIcon('heroicon-o-x-mark'),
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
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_kegiatan', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn(Builder $query, $date): Builder => $query->whereDate('tanggal_kegiatan', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('dari_pengajuan')
                            ->label('Dari Tanggal Pengajuan'),
                        Forms\Components\DatePicker::make('sampai_pengajuan')
                            ->label('Sampai Tanggal Pengajuan'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_pengajuan'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai_pengajuan'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('view_pdf')
                        ->label('Lihat PDF')
                        ->icon('heroicon-m-eye')
                        ->url(fn(LaporGiat $record) => $record->laporan_kegiatan_url)
                        ->openUrlInNewTab()
                        ->visible(fn(LaporGiat $record) => $record->hasLaporanFile()),

                    Tables\Actions\Action::make('download_pdf')
                        ->label('Download PDF')
                        ->icon('heroicon-m-document-arrow-down')
                        ->url(fn(LaporGiat $record) => $record->laporan_kegiatan_download_url)
                        ->visible(fn(LaporGiat $record) => $record->hasLaporanFile()),

                    Tables\Actions\Action::make('download_images')
                        ->label('Download Foto (ZIP)')
                        ->icon('heroicon-m-archive-box-arrow-down')
                        ->url(fn(LaporGiat $record) => $record->all_images_zip_url)
                        ->visible(fn(LaporGiat $record) => $record->hasImages()),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->size('sm')
                    ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->label('Setujui')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['status' => LaporGiat::STATUS_APPROVED]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Setujui Laporan Kegiatan')
                        ->modalDescription('Apakah Anda yakin ingin menyetujui laporan kegiatan yang dipilih?'),

                    Tables\Actions\BulkAction::make('reject')
                        ->label('Tolak')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->form([
                            Forms\Components\Textarea::make('keterangan')
                                ->label('Alasan Penolakan')
                                ->required()
                                ->placeholder('Jelaskan alasan penolakan...'),
                        ])
                        ->action(function ($records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update([
                                    'status' => LaporGiat::STATUS_REJECTED,
                                    'keterangan' => $data['keterangan'],
                                ]);
                            });
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Tolak Laporan Kegiatan')
                        ->modalDescription('Berikan alasan penolakan untuk laporan yang dipilih.'),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pemohon')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Nama Pemohon'),
                        Infolists\Components\TextEntry::make('user.email')
                            ->label('Email'),
                        Infolists\Components\TextEntry::make('user.no_telepon')
                            ->label('No. Telepon'),
                    ])
                    ->columns(3),

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
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn($state) => LaporGiat::STATUS_OPTIONS[$state] ?? $state),

                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan Admin')
                            ->visible(fn($record) => !empty($record->keterangan))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('File & Foto')
                    ->schema([
                        Infolists\Components\Actions::make([
                            Infolists\Components\Actions\Action::make('view_pdf')
                                ->label('Lihat PDF')
                                ->icon('heroicon-m-eye')
                                ->url(fn($record) => $record->laporan_kegiatan_url)
                                ->openUrlInNewTab()
                                ->visible(fn($record) => $record->hasLaporanFile()),

                            Infolists\Components\Actions\Action::make('download_pdf')
                                ->label('Download PDF')
                                ->icon('heroicon-m-document-arrow-down')
                                ->url(fn($record) => $record->laporan_kegiatan_download_url)
                                ->color('success')
                                ->visible(fn($record) => $record->hasLaporanFile()),

                            Infolists\Components\Actions\Action::make('download_all_images')
                                ->label('Download Semua Foto (ZIP)')
                                ->icon('heroicon-m-archive-box-arrow-down')
                                ->url(fn($record) => $record->all_images_zip_url)
                                ->color('info')
                                ->visible(fn($record) => $record->hasImages()),
                        ])
                            ->columnSpanFull(),

                        Infolists\Components\TextEntry::make('image_count')
                            ->label('Jumlah Foto')
                            ->formatStateUsing(fn($record) => $record->image_count . ' foto')
                            ->visible(fn($record) => $record->hasImages()),

                        // Custom image gallery with secure URLs
                        Infolists\Components\ViewEntry::make('images_gallery')
                            ->label('Galeri Foto')
                            ->view('filament.infolists.image-gallery')
                            ->viewData(fn($record) => [
                                'imageUrls' => $record->image_urls,
                                'downloadUrls' => $record->image_download_urls,
                            ])
                            ->visible(fn($record) => $record->hasImages())
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
            'view' => Pages\ViewLaporGiat::route('/{record}'),
            'edit' => Pages\EditLaporGiat::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}