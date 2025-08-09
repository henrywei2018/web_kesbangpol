<?php

namespace App\Filament\Resources\LaporGiatResource\Pages;

use App\Filament\Resources\LaporGiatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use App\Models\LaporGiat;

class ListLaporGiats extends ListRecords
{
    protected static string $resource = LaporGiatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export')
                ->label('Export Data')
                ->icon('heroicon-m-arrow-down-tray')
                ->color('success')
                ->action(function () {
                    // Add export logic here if needed
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(LaporGiat::count()),
            
            'pending' => Tab::make('Menunggu Review')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'pending'))
                ->badge(LaporGiat::where('status', 'pending')->count())
                ->badgeColor('warning'),
            
            'approved' => Tab::make('Disetujui')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'approved'))
                ->badge(LaporGiat::where('status', 'approved')->count())
                ->badgeColor('success'),
            
            'rejected' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'rejected'))
                ->badge(LaporGiat::where('status', 'rejected')->count())
                ->badgeColor('danger'),
        ];
    }

    public function getTitle(): string
    {
        return 'Laporan Kegiatan';
    }
}