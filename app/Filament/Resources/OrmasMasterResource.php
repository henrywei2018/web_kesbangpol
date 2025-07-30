<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrmasMasterResource\Pages;
use App\Models\OrmasMaster;
use App\Services\OrmasService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class OrmasMasterResource extends Resource
{
    protected static ?string $model = OrmasMaster::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Master ORMAS';

    protected static ?string $navigationGroup = 'POKUS KALTARA';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('nomor_registrasi')
                            ->label('Nomor Registrasi')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('nama_ormas')
                            ->label('Nama ORMAS')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('nama_singkatan_ormas')
                            ->label('Nama Singkatan')
                            ->disabled(),
                        
                        Forms\Components\Select::make('status_administrasi')
                            ->label('Status Administrasi')
                            ->options([
                                'belum_selesai' => 'Belum Selesai',
                                'selesai' => 'Selesai',
                            ])
                            ->required(),
                        
                        Forms\Components\Textarea::make('keterangan_status')
                            ->label('Keterangan Status')
                            ->rows(3),
                        
                        Forms\Components\Select::make('sumber_registrasi')
                            ->label('Sumber Registrasi')
                            ->options([
                                'skt' => 'SKT',
                                'skl' => 'SKL',
                            ])
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Data Organisasi')
                    ->schema([
                        Forms\Components\TextInput::make('tempat_pendirian')
                            ->label('Tempat Pendirian')
                            ->disabled(),
                        
                        Forms\Components\DatePicker::make('tanggal_pendirian')
                            ->label('Tanggal Pendirian')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('bidang_kegiatan')
                            ->label('Bidang Kegiatan')
                            ->disabled(),
                        
                        Forms\Components\Select::make('ciri_khusus')
                            ->label('Ciri Khusus')
                            ->options([
                                'Keagamaan' => 'Keagamaan',
                                'Kewanitaan' => 'Kewanitaan',
                                'Kepemudaan' => 'Kepemudaan',
                                'Kesamaan Profesi' => 'Kesamaan Profesi',
                                'Kesamaan Kegiatan' => 'Kesamaan Kegiatan',
                                'Kesamaan Bidang' => 'Kesamaan Bidang',
                                'Mitra K/L' => 'Mitra K/L',
                            ])
                            ->disabled(),
                        
                        Forms\Components\Textarea::make('tujuan_ormas')
                            ->label('Tujuan ORMAS')
                            ->rows(3)
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Alamat dan Kontak')
                    ->schema([
                        Forms\Components\Textarea::make('alamat_sekretariat')
                            ->label('Alamat Sekretariat')
                            ->rows(2)
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('kab_kota')
                            ->label('Kabupaten/Kota')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('nomor_handphone')
                            ->label('Nomor HP')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Struktur Organisasi')
                    ->schema([
                        Forms\Components\TextInput::make('ketua_nama_lengkap')
                            ->label('Nama Ketua')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('sekretaris_nama_lengkap')
                            ->label('Nama Sekretaris')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('bendahara_nama_lengkap')
                            ->label('Nama Bendahara')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_registrasi')
                    ->label('No. Registrasi')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('nama_ormas')
                    ->label('Nama ORMAS')
                    ->searchable()
                    ->sortable()
                    ->limit(30),
                
                TextColumn::make('nama_singkatan_ormas')
                    ->label('Singkatan')
                    ->searchable(),
                
                BadgeColumn::make('status_administrasi')
                    ->label('Status Administrasi')
                    ->colors([
                        'danger' => 'belum_selesai',
                        'success' => 'selesai',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'belum_selesai',
                        'heroicon-o-check-circle' => 'selesai',
                    ]),
                
                BadgeColumn::make('sumber_registrasi')
                    ->label('Sumber')
                    ->colors([
                        'primary' => 'skt',
                        'secondary' => 'skl',
                    ]),
                
                TextColumn::make('kab_kota')
                    ->label('Kab/Kota')
                    ->searchable(),
                
                TextColumn::make('ciri_khusus')
                    ->label('Ciri Khusus')
                    ->searchable(),
                
                TextColumn::make('tanggal_selesai_administrasi')
                    ->label('Tgl Selesai')
                    ->date('d/m/Y')
                    ->sortable(),
                
                TextColumn::make('first_registered_at')
                    ->label('Terdaftar')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status_administrasi')
                    ->label('Status Administrasi')
                    ->options([
                        'belum_selesai' => 'Belum Selesai',
                        'selesai' => 'Selesai',
                    ]),
                
                SelectFilter::make('sumber_registrasi')
                    ->label('Sumber Registrasi')
                    ->options([
                        'skt' => 'SKT',
                        'skl' => 'SKL',
                    ]),
                
                SelectFilter::make('ciri_khusus')
                    ->label('Ciri Khusus')
                    ->options([
                        'Keagamaan' => 'Keagamaan',
                        'Kewanitaan' => 'Kewanitaan',
                        'Kepemudaan' => 'Kepemudaan',
                        'Kesamaan Profesi' => 'Kesamaan Profesi',
                        'Kesamaan Kegiatan' => 'Kesamaan Kegiatan',
                        'Kesamaan Bidang' => 'Kesamaan Bidang',
                        'Mitra K/L' => 'Mitra K/L',
                    ]),
                
                SelectFilter::make('kab_kota')
                    ->label('Kabupaten/Kota')
                    ->options(function () {
                        return \App\Models\Wilayah::where('level', 'kabupaten')
                            ->pluck('nama', 'nama')
                            ->toArray();
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                
                Action::make('mark_completed')
                    ->label('Tandai Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (OrmasMaster $record) => $record->status_administrasi === 'belum_selesai')
                    ->requiresConfirmation()
                    ->action(function (OrmasMaster $record) {
                        $record->markAsCompleted('Ditandai selesai secara manual pada ' . now()->format('d/m/Y H:i'));
                        
                        Notification::make()
                            ->title('Status berhasil diubah')
                            ->body('ORMAS telah ditandai sebagai selesai administrasi')
                            ->success()
                            ->send();
                    }),
                
                Action::make('mark_incomplete')
                    ->label('Tandai Belum Selesai')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn (OrmasMaster $record) => $record->status_administrasi === 'selesai')
                    ->requiresConfirmation()
                    ->action(function (OrmasMaster $record) {
                        $record->markAsIncomplete('Ditandai belum selesai secara manual pada ' . now()->format('d/m/Y H:i'));
                        
                        Notification::make()
                            ->title('Status berhasil diubah')
                            ->body('ORMAS telah ditandai sebagai belum selesai administrasi')
                            ->success()
                            ->send();
                    }),
                
                Action::make('view_source')
                    ->label('Lihat Sumber')
                    ->icon('heroicon-o-eye')
                    ->url(function (OrmasMaster $record) {
                        if ($record->sumber_registrasi === 'skt') {
                            return route('filament.admin.resources.s-k-t-document-feedbacks.edit', $record->skt_id);
                        } elseif ($record->sumber_registrasi === 'skl') {
                            return route('filament.admin.resources.s-k-l-document-feedbacks.edit', $record->skl_id);
                        }
                        return null;
                    })
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Action::make('bulk_mark_completed')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status_administrasi === 'belum_selesai') {
                                    $record->markAsCompleted('Ditandai selesai secara massal pada ' . now()->format('d/m/Y H:i'));
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Status berhasil diubah')
                                ->body("{$count} ORMAS telah ditandai sebagai selesai administrasi")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('first_registered_at', 'desc');
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
            'index' => Pages\ListOrmasMasters::route('/'),
            'create' => Pages\CreateOrmasMaster::route('/create'),
            'view' => Pages\ViewOrmasMaster::route('/{record}'),
            'edit' => Pages\EditOrmasMaster::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrmasMasterResource\Widgets\OrmasStatsOverview::class,
            OrmasMasterResource\Widgets\OrmasDetailedStats::class,
            OrmasMasterResource\Widgets\OrmasRegionalDistribution::class,
            OrmasMasterResource\Widgets\OrmasCategoryDistribution::class,
        ];
    }
}