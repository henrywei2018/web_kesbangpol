<?php

namespace App\Filament\Resources\OrmasMasterResource\Pages;

use App\Filament\Resources\OrmasMasterResource;
use App\Services\OrmasService;
use Filament\Actions;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListOrmasMasters extends ListRecords
{
    use ExposesTableToWidgets;

    protected static string $resource = OrmasMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
