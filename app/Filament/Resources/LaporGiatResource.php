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

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Laporan Kegiatan';

    protected static ?string $modelLabel = 'Laporan Kegiatan';

    protected static ?string $pluralModelLabel = 'Laporan Kegiatan';

    protected static ?string $navigationGroup = 'POKUS KALTARA';

    protected static ?int $navigationSort = 5;
    public static function getSlug(): string
    {
        return 'giat-ormas';
    }

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

                Forms\Components\Section::make('File & Foto Kegiatan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Group::make([
                                    Forms\Components\Placeholder::make('pdf_status')
                                        ->label('PDF Laporan')
                                        ->content(fn ($record) => $record ? ($record->hasLaporanFile() ? '✓ File tersedia' : '✗ Tidak ada file') : '✗ Tidak ada file'),

                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('download_pdf')
                                            ->label('Download PDF')
                                            ->icon('heroicon-s-document-arrow-down')
                                            ->url(fn ($record) => $record ? route('lapor-giat.download-laporan', ['laporGiat' => $record->id]) : '#')
                                            ->openUrlInNewTab()
                                            ->color('success')
                                            ->size('sm')
                                            ->visible(fn ($record) => $record && $record->hasLaporanFile()),
                                    ]),
                                ])
                                ->columnSpan(1),

                                Forms\Components\Group::make([
                                    Forms\Components\Placeholder::make('images_status')
                                        ->label('Foto Kegiatan')
                                        ->content(fn ($record) => $record ? ($record->hasImages() ? '✓ ' . $record->image_count . ' foto' : '✗ Tidak ada foto') : '✗ Tidak ada foto'),

                                    Forms\Components\Actions::make([
                                        Forms\Components\Actions\Action::make('download_images_zip')
                                            ->label('Download ZIP')
                                            ->icon('heroicon-s-archive-box-arrow-down')
                                            ->url(fn ($record) => $record ? route('lapor-giat.download-all-images', ['laporGiat' => $record->id]) : '#')
                                            ->openUrlInNewTab()
                                            ->color('warning')
                                            ->size('sm')
                                            ->visible(fn ($record) => $record && $record->hasImages()),
                                    ]),
                                ])
                                ->columnSpan(1),
                            ]),
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
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai_pengajuan'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('')
                    ->tooltip('Detail'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->label('')
                    ->tooltip('Edit'),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('download_pdf')
                        ->label('Download PDF')
                        ->icon('heroicon-m-document-arrow-down')
                        ->color('success')
                        ->url(fn (LaporGiat $record) => route('lapor-giat.download-laporan', ['laporGiat' => $record->id]))
                        ->openUrlInNewTab()
                        ->visible(fn (LaporGiat $record) => $record->hasLaporanFile()),
                    
                    Tables\Actions\Action::make('download_images')
                        ->label(fn (LaporGiat $record) => 'Download Foto (' . $record->image_count . ')')
                        ->icon('heroicon-m-archive-box-arrow-down')
                        ->color('warning')
                        ->url(fn (LaporGiat $record) => route('lapor-giat.download-all-images', ['laporGiat' => $record->id]))
                        ->openUrlInNewTab()
                        ->visible(fn (LaporGiat $record) => $record->hasImages()),
                ])
                    ->label('Download')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->size('sm')
                    ->button(),
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
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn ($state) => LaporGiat::STATUS_OPTIONS[$state] ?? $state),

                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan Admin')
                            ->visible(fn ($record) => !empty($record->keterangan))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('File & Foto Kegiatan')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('laporan_kegiatan_path')
                                        ->label('PDF Laporan')
                                        ->formatStateUsing(fn($record) => $record->hasLaporanFile() ? '✓ File tersedia' : '✗ Tidak ada file')
                                        ->color(fn($record) => $record->hasLaporanFile() ? 'success' : 'gray'),

                                    Infolists\Components\Actions::make([
                                        Infolists\Components\Actions\Action::make('download_pdf')
                                            ->label('Download PDF')
                                            ->icon('heroicon-s-document-arrow-down')
                                            ->url(fn($record) => route('lapor-giat.download-laporan', ['laporGiat' => $record->id]))
                                            ->button()
                                            ->color('success')
                                            ->size('sm')
                                            ->visible(fn($record) => $record->hasLaporanFile()),
                                    ])
                                    ->alignStart(),
                                ]),

                                Infolists\Components\Group::make([
                                    Infolists\Components\TextEntry::make('image_count')
                                        ->label('Foto Kegiatan')
                                        ->formatStateUsing(fn($record) => $record->hasImages() ? '✓ ' . $record->image_count . ' foto' : '✗ Tidak ada foto')
                                        ->color(fn($record) => $record->hasImages() ? 'success' : 'gray'),

                                    Infolists\Components\Actions::make([
                                        Infolists\Components\Actions\Action::make('download_images_zip')
                                            ->label('Download ZIP')
                                            ->icon('heroicon-s-archive-box-arrow-down')
                                            ->url(fn($record) => route('lapor-giat.download-all-images', ['laporGiat' => $record->id]))
                                            ->button()
                                            ->color('warning')
                                            ->size('sm')
                                            ->visible(fn($record) => $record->hasImages()),
                                    ])
                                    ->alignStart(),
                                ]),
                            ]),
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