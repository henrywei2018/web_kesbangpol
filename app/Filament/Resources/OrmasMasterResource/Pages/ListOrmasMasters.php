<?php

namespace App\Filament\Resources\OrmasMasterResource\Pages;

use App\Filament\Resources\OrmasMasterResource;
use App\Services\OrmasService;
use App\Exports\OrmasExport;
use App\Exports\OrmasTemplateExport;
use App\Imports\OrmasImport;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ListOrmasMasters extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = OrmasMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('export_all')
                    ->label('Export Semua Data')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->form([
                        
                        \Filament\Forms\Components\Select::make('status_filter')
                            ->label('Filter Status')
                            ->options([
                                'all' => 'Semua Status',
                                'selesai' => 'Hanya Selesai',
                                'belum_selesai' => 'Hanya Belum Selesai',
                            ])
                            ->default('all'),
                            
                        \Filament\Forms\Components\Select::make('source_filter')
                            ->label('Filter Sumber')
                            ->options([
                                'all' => 'Semua Sumber',
                                'skt' => 'Hanya SKT',
                                'skl' => 'Hanya SKL',
                            ])
                            ->default('all'),
                    ])
                    ->action(function (array $data) {
                        $filters = [
                            'status_filter' => $data['status_filter'],
                            'source_filter' => $data['source_filter'],
                        ];
                        
                        $fileName = 'data-ormas-' . date('Y-m-d-H-i-s') . '.xlsx';
                        
                        try {
                            return Excel::download(new OrmasExport($filters), $fileName);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Export gagal')
                                ->body('Terjadi kesalahan saat export: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Actions\Action::make('download_template')
                    ->label('Download Template Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('secondary')
                    ->action(function () {
                        $fileName = 'template-import-ormas-' . date('Y-m-d') . '.xlsx';
                        return Excel::download(new OrmasTemplateExport(), $fileName);
                    }),
                
                Actions\Action::make('import_excel')
                    ->label('Import Excel')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        \Filament\Forms\Components\FileUpload::make('excel_file')
                            ->label('File Excel')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required()
                            ->helperText('Upload file Excel (.xlsx atau .xls) dengan format data ORMAS'),
                            
                        \Filament\Forms\Components\Checkbox::make('update_existing')
                            ->label('Update data yang sudah ada')
                            ->helperText('Centang jika ingin memperbarui data ORMAS yang sudah ada berdasarkan nomor registrasi'),
                            
                        \Filament\Forms\Components\Checkbox::make('validate_only')
                            ->label('Validasi saja (tidak import)')
                            ->helperText('Centang untuk hanya memvalidasi file tanpa mengimport data'),
                    ])
                    ->action(function (array $data) {
                        $updateExisting = $data['update_existing'] ?? false;
                        $validateOnly = $data['validate_only'] ?? false;
                        
                        try {
                            $import = new OrmasImport($updateExisting, $validateOnly);
                            
                            if ($validateOnly) {
                                // Validate only
                                Excel::import($import, $data['excel_file']);
                                
                                Notification::make()
                                    ->title('Validasi selesai')
                                    ->body('File Excel valid dan siap untuk diimport')
                                    ->success()
                                    ->send();
                            } else {
                                // Import data
                                Excel::import($import, $data['excel_file']);
                                
                                $results = $import->getImportResults();
                                
                                $message = "Import selesai: {$results['created']} dibuat, {$results['updated']} diperbarui, {$results['skipped']} dilewati";
                                
                                if (!empty($results['errors'])) {
                                    $message .= ". " . count($results['errors']) . " error terjadi.";
                                    
                                    Notification::make()
                                        ->title('Import selesai dengan error')
                                        ->body($message)
                                        ->warning()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->title('Import berhasil')
                                        ->body($message)
                                        ->success()
                                        ->send();
                                }
                            }
                        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                            $failures = $e->failures();
                            $errors = [];
                            
                            foreach ($failures as $failure) {
                                $errors[] = "Baris {$failure->row()}: " . implode(', ', $failure->errors());
                            }
                            
                            Notification::make()
                                ->title('Import gagal')
                                ->body('Validasi gagal: ' . implode(' | ', array_slice($errors, 0, 3)) . (count($errors) > 3 ? ' ...' : ''))
                                ->danger()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import gagal')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->label('Excel Operations')
            ->icon('heroicon-m-table-cells')
            ->color('success'),
            
            Actions\ActionGroup::make([
                Actions\Action::make('sync_skt_data')
                    ->label('Sinkronisasi Data SKT')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Sinkronisasi Data SKT')
                    ->modalDescription('Ini akan mensinkronkan semua data SKT ke master ORMAS. Proses ini mungkin membutuhkan waktu beberapa menit.')
                    ->action(function () {
                        $ormasService = new OrmasService();
                        $count = $ormasService->syncAllSKTToOrmas();
                        
                        Notification::make()
                            ->title('Sinkronisasi berhasil')
                            ->body("Berhasil mensinkronkan {$count} data SKT ke master ORMAS")
                            ->success()
                            ->send();
                    }),

                Actions\Action::make('sync_skl_data')
                    ->label('Sinkronisasi Data SKL')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Sinkronisasi Data SKL')
                    ->modalDescription('Ini akan mensinkronkan semua data SKL ke master ORMAS.')
                    ->action(function () {
                        $ormasService = new OrmasService();
                        $count = $ormasService->syncAllSKLToOrmas();
                        
                        Notification::make()
                            ->title('Sinkronisasi berhasil')
                            ->body("Berhasil mensinkronkan {$count} data SKL ke master ORMAS")
                            ->success()
                            ->send();
                    }),

                Actions\Action::make('clean_orphaned')
                    ->label('Bersihkan Data Orphaned')
                    ->icon('heroicon-o-trash')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Bersihkan Data Orphaned')
                    ->modalDescription('Ini akan menghapus data ORMAS yang tidak memiliki referensi SKT atau SKL yang valid. Data yang dihapus tidak dapat dikembalikan.')
                    ->action(function () {
                        $output = \Artisan::call('ormas:clean-orphaned');
                        
                        Notification::make()
                            ->title('Pembersihan selesai')
                            ->body('Data orphaned telah dibersihkan')
                            ->success()
                            ->send();
                    }),

                Actions\Action::make('detect_duplicates')
                    ->label('Deteksi Duplikasi')
                    ->icon('heroicon-o-magnifying-glass')
                    ->color('warning')
                    ->form([
                        \Filament\Forms\Components\Select::make('action_type')
                            ->label('Pilih Aksi')
                            ->options([
                                'detect' => 'Hanya Deteksi (Laporan)',
                                'cleanup' => 'Deteksi & Bersihkan Otomatis',
                            ])
                            ->default('detect')
                            ->required()
                            ->helperText('Pilih "Deteksi" untuk melihat laporan duplikasi, atau "Bersihkan" untuk membersihkan otomatis'),
                    ])
                    ->action(function (array $data) {
                        $isCleanup = $data['action_type'] === 'cleanup';
                        
                        try {
                            if ($isCleanup) {
                                $exitCode = \Artisan::call('ormas:clean-duplicates', ['--force' => true]);
                                
                                if ($exitCode === 0) {
                                    Notification::make()
                                        ->title('Pembersihan Berhasil')
                                        ->body('Duplikasi ORMAS telah dibersihkan. Periksa log untuk detail hasil.')
                                        ->success()
                                        ->send();
                                } else {
                                    Notification::make()
                                        ->title('Pembersihan Gagal')
                                        ->body('Terjadi error saat pembersihan. Periksa log untuk detail.')
                                        ->danger()
                                        ->send();
                                }
                            } else {
                                $exitCode = \Artisan::call('ormas:detect-duplicates', ['--dry-run' => true]);
                                
                                Notification::make()
                                    ->title('Deteksi Selesai')
                                    ->body('Deteksi duplikasi selesai. Periksa log sistem untuk detail hasil.')
                                    ->info()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Terjadi kesalahan: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->label('Sync & Maintenance')
            ->icon('heroicon-m-arrow-path')
            ->color('info'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return OrmasMasterResource::getWidgets();
    }

    public function getTitle(): string
    {
        return 'Master ORMAS';
    }
}
