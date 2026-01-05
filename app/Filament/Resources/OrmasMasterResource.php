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
use Illuminate\Database\Eloquent\Builder;
use App\Exports\OrmasExport;
use Maatwebsite\Excel\Facades\Excel;

class OrmasMasterResource extends Resource
{
    protected static ?string $model = OrmasMaster::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Data ORMAS';

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
                        'success' => 'skl',
                    ])
                    ->formatStateUsing(fn (string $state, OrmasMaster $record): string => match ($state) {
                        'skt' => 'SKT' . ($record->skt_id ? " (#{$record->skt_id})" : ''),
                        'skl' => 'SKL' . ($record->skl_id ? " (#{$record->skl_id})" : ''),
                        default => $state,
                    }),
                
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
                
                TextColumn::make('source_status')
                    ->label('Status Sumber')
                    ->getStateUsing(function (OrmasMaster $record) {
                        if ($record->sumber_registrasi === 'skt' && $record->skt) {
                            return $record->skt->status ?? 'N/A';
                        } elseif ($record->sumber_registrasi === 'skl' && $record->skl) {
                            return $record->skl->status ?? 'N/A';
                        }
                        return 'N/A';
                    })
                    ->badge()
                    ->colors([
                        'warning' => 'pengajuan',
                        'danger' => 'perbaikan',
                        'info' => 'diproses',
                        'success' => 'terbit',
                        'danger' => 'ditolak',
                    ]),

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

                SelectFilter::make('source_status')
                    ->label('Status Sumber')
                    ->options([
                        'pengajuan' => 'Pengajuan',
                        'perbaikan' => 'Perbaikan',
                        'diproses' => 'Diproses',
                        'terbit' => 'Terbit',
                        'ditolak' => 'Ditolak',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!$data['value']) {
                            return $query;
                        }
                        
                        return $query->where(function (Builder $query) use ($data) {
                            $query->whereHas('skt', function (Builder $query) use ($data) {
                                $query->where('status', $data['value']);
                            })->orWhereHas('skl', function (Builder $query) use ($data) {
                                $query->where('status', $data['value']);
                            });
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->icon('heroicon-o-eye'),
                
                Tables\Actions\ActionGroup::make([
                    // Status Management Actions
                    Action::make('mark_completed')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (OrmasMaster $record) => $record->status_administrasi === 'belum_selesai')
                        ->form([
                            Forms\Components\Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->placeholder('Opsional: berikan keterangan alasan menandai sebagai selesai')
                                ->rows(3),
                        ])
                        ->action(function (OrmasMaster $record, array $data) {
                            $keterangan = $data['keterangan'] ?? 'Ditandai selesai secara manual pada ' . now()->format('d/m/Y H:i');
                            $record->markAsCompleted($keterangan);
                            
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
                        ->form([
                            Forms\Components\Textarea::make('keterangan')
                                ->label('Alasan')
                                ->placeholder('Berikan alasan mengapa status dikembalikan ke belum selesai')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function (OrmasMaster $record, array $data) {
                            $record->markAsIncomplete($data['keterangan']);
                            
                            Notification::make()
                                ->title('Status berhasil diubah')
                                ->body('ORMAS telah ditandai sebagai belum selesai administrasi')
                                ->success()
                                ->send();
                        }),
                    
                    // Source Management Actions
                    Action::make('view_source')
                        ->label('Lihat Sumber')
                        ->icon('heroicon-o-document-magnifying-glass')
                        ->color('info')
                        ->url(function (OrmasMaster $record) {
                            try {
                                if ($record->sumber_registrasi === 'skt' && $record->skt_id) {
                                    // Try different possible route names for SKT
                                    $possibleRoutes = [
                                        'filament.admin.resources.s-k-ts.edit',
                                        'filament.admin.resources.skt.edit',
                                        'filament.admin.resources.skts.edit',
                                        'filament.admin.resources.reviu-skt.edit',
                                    ];
                                    foreach ($possibleRoutes as $route) {
                                        if (\Illuminate\Support\Facades\Route::has($route)) {
                                            return route($route, $record->skt_id);
                                        }
                                    }
                                } elseif ($record->sumber_registrasi === 'skl' && $record->skl_id) {
                                    // Try different possible route names for SKL
                                    $possibleRoutes = [
                                        'filament.admin.resources.s-k-ls.edit',
                                        'filament.admin.resources.skl.edit', 
                                        'filament.admin.resources.skls.edit',
                                        'filament.admin.resources.s-k-l-resources.edit',
                                        'filament.admin.resources.skl-resources.edit'
                                    ];
                                    foreach ($possibleRoutes as $route) {
                                        if (\Illuminate\Support\Facades\Route::has($route)) {
                                            return route($route, $record->skl_id);
                                        }
                                    }
                                }
                            } catch (\Exception $e) {
                                \Log::error('Route generation error in OrmasMasterResource: ' . $e->getMessage());
                            }
                            return null;
                        })
                        ->openUrlInNewTab()
                        ->visible(fn (OrmasMaster $record) => 
                            ($record->sumber_registrasi === 'skt' && $record->skt_id) || 
                            ($record->sumber_registrasi === 'skl' && $record->skl_id)
                        ),
                    
                    Action::make('sync_from_source')
                        ->label('Sinkronisasi Ulang')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Sinkronisasi Ulang Data')
                        ->modalDescription('Apakah Anda yakin ingin menyinkronisasi ulang data ORMAS dari sumber asli? Data yang telah diubah manual akan ditimpa.')
                        ->action(function (OrmasMaster $record) {
                            if ($record->sumber_registrasi === 'skt' && $record->skt) {
                                OrmasMaster::createOrUpdateFromSKT($record->skt, $record->status_administrasi);
                                $source = 'SKT';
                            } elseif ($record->sumber_registrasi === 'skl' && $record->skl) {
                                OrmasMaster::createOrUpdateFromSKL($record->skl, $record->status_administrasi);
                                $source = 'SKL';
                            } else {
                                Notification::make()
                                    ->title('Sinkronisasi gagal')
                                    ->body('Sumber data tidak ditemukan')
                                    ->danger()
                                    ->send();
                                return;
                            }
                            
                            Notification::make()
                                ->title('Data berhasil disinkronisasi')
                                ->body("Data ORMAS telah disinkronisasi ulang dari {$source}")
                                ->success()
                                ->send();
                        })
                        ->visible(fn (OrmasMaster $record) => 
                            ($record->sumber_registrasi === 'skt' && $record->skt) || 
                            ($record->sumber_registrasi === 'skl' && $record->skl)
                        ),

                    // Administrative Actions
                    Tables\Actions\EditAction::make()
                        ->label('Edit')
                        ->icon('heroicon-o-pencil-square'),
                        
                    Action::make('print_certificate')
                        ->label('Cetak Sertifikat')
                        ->icon('heroicon-o-printer')
                        ->color('success')
                        ->visible(fn (OrmasMaster $record) => $record->status_administrasi === 'selesai')
                        ->url(fn (OrmasMaster $record) => route('ormas.certificate', $record))
                        ->openUrlInNewTab(),
                    
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data ORMAS')
                        ->modalDescription('Apakah Anda yakin ingin menghapus data ORMAS ini? Data yang dihapus tidak dapat dikembalikan.')
                        ->before(function (OrmasMaster $record) {
                            // Log deletion activity
                            activity()
                                ->performedOn($record)
                                ->withProperties([
                                    'nama_ormas' => $record->nama_ormas,
                                    'sumber_registrasi' => $record->sumber_registrasi,
                                    'status_administrasi' => $record->status_administrasi,
                                ])
                                ->log('Data ORMAS dihapus');
                        }),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Status Management Bulk Actions
                    Action::make('bulk_mark_completed')
                        ->label('Tandai Selesai')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->form([
                            Forms\Components\Textarea::make('keterangan')
                                ->label('Keterangan')
                                ->placeholder('Opsional: berikan keterangan untuk semua ORMAS yang dipilih')
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data) {
                            $count = 0;
                            $keterangan = $data['keterangan'] ?? 'Ditandai selesai secara massal pada ' . now()->format('d/m/Y H:i');
                            
                            foreach ($records as $record) {
                                if ($record->status_administrasi === 'belum_selesai') {
                                    $record->markAsCompleted($keterangan);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Status berhasil diubah')
                                ->body("{$count} ORMAS telah ditandai sebagai selesai administrasi")
                                ->success()
                                ->send();
                        }),
                    
                    Action::make('bulk_mark_incomplete')
                        ->label('Tandai Belum Selesai')
                        ->icon('heroicon-o-clock')
                        ->color('warning')
                        ->form([
                            Forms\Components\Textarea::make('keterangan')
                                ->label('Alasan')
                                ->placeholder('Berikan alasan mengapa status dikembalikan ke belum selesai')
                                ->required()
                                ->rows(3),
                        ])
                        ->action(function ($records, array $data) {
                            $count = 0;
                            
                            foreach ($records as $record) {
                                if ($record->status_administrasi === 'selesai') {
                                    $record->markAsIncomplete($data['keterangan']);
                                    $count++;
                                }
                            }
                            
                            Notification::make()
                                ->title('Status berhasil diubah')
                                ->body("{$count} ORMAS telah dikembalikan ke belum selesai")
                                ->success()
                                ->send();
                        }),
                    
                    // Data Management Bulk Actions
                    Action::make('bulk_sync')
                        ->label('Sinkronisasi Ulang')
                        ->icon('heroicon-o-arrow-path')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Sinkronisasi Ulang Data Massal')
                        ->modalDescription('Apakah Anda yakin ingin menyinkronisasi ulang semua data ORMAS yang dipilih dari sumber asli? Data yang telah diubah manual akan ditimpa.')
                        ->action(function ($records) {
                            $count = 0;
                            $errors = 0;
                            
                            foreach ($records as $record) {
                                try {
                                    if ($record->sumber_registrasi === 'skt' && $record->skt) {
                                        OrmasMaster::createOrUpdateFromSKT($record->skt, $record->status_administrasi);
                                        $count++;
                                    } elseif ($record->sumber_registrasi === 'skl' && $record->skl) {
                                        OrmasMaster::createOrUpdateFromSKL($record->skl, $record->status_administrasi);
                                        $count++;
                                    } else {
                                        $errors++;
                                    }
                                } catch (\Exception $e) {
                                    \Log::error('Bulk sync error for ORMAS ' . $record->id . ': ' . $e->getMessage());
                                    $errors++;
                                }
                            }
                            
                            if ($count > 0) {
                                Notification::make()
                                    ->title('Sinkronisasi berhasil')
                                    ->body("{$count} ORMAS berhasil disinkronisasi" . ($errors > 0 ? ", {$errors} gagal" : ""))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Sinkronisasi gagal')
                                    ->body('Tidak ada data yang berhasil disinkronisasi')
                                    ->danger()
                                    ->send();
                            }
                        }),
                    
                    // Export Actions
                    Action::make('export_selected')
                        ->label('Export Terpilih')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Export Data Terpilih')
                        ->modalDescription(function ($records) {
                            $count = $records->count();
                            return "Apakah Anda ingin mengexport {$count} data ORMAS yang dipilih ke format Excel?";
                        })
                        ->action(function ($records, array $data) {
                            $recordIds = $records->pluck('id')->toArray();
                            $fileName = 'data-ormas-terpilih-' . date('Y-m-d-H-i-s') . '.' . $data['format'];
                            
                            try {
                                // Create a custom export for selected records
                                $export = new class($recordIds) extends OrmasExport {
                                    protected $recordIds;
                                    
                                    public function __construct(array $recordIds) {
                                        $this->recordIds = $recordIds;
                                        parent::__construct();
                                    }
                                    
                                    public function query() {
                                        return OrmasMaster::query()
                                            ->with(['skt', 'skl'])
                                            ->whereIn('id', $this->recordIds)
                                            ->orderBy('created_at', 'desc');
                                    }
                                    
                                    public function title(): string {
                                        return 'Data ORMAS Terpilih (' . count($this->recordIds) . ' records)';
                                    }
                                };
                                
                                switch ($data['format']) {
                                    case 'xlsx':
                                        return Excel::download($export, $fileName);
                                    case 'csv':
                                        return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::CSV);
                                    case 'pdf':
                                        return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::DOMPDF);
                                }
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Export gagal')
                                    ->body('Terjadi kesalahan: ' . $e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    
                    // Delete Action
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Terpilih')
                        ->requiresConfirmation()
                        ->modalHeading('Hapus Data ORMAS')
                        ->modalDescription('Apakah Anda yakin ingin menghapus semua data ORMAS yang dipilih? Data yang dihapus tidak dapat dikembalikan.')
                        ->before(function ($records) {
                            // Log bulk deletion activity
                            foreach ($records as $record) {
                                activity()
                                    ->performedOn($record)
                                    ->withProperties([
                                        'nama_ormas' => $record->nama_ormas,
                                        'sumber_registrasi' => $record->sumber_registrasi,
                                        'status_administrasi' => $record->status_administrasi,
                                        'action_type' => 'bulk_delete',
                                    ])
                                    ->log('Data ORMAS dihapus (bulk action)');
                            }
                        }),
                ])
                ->label('Aksi Massal')
                ->color('primary'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['skt', 'skl']);
    }
}