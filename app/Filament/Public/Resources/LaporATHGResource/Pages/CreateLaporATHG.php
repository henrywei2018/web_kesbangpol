<?php
// Fixed CreateLaporATHG.php - app/Filament/Public/Resources/LaporATHGResource/Pages/CreateLaporATHG.php

namespace App\Filament\Public\Resources\LaporATHGResource\Pages;

use App\Filament\Public\Resources\LaporATHGResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Filament\Actions;

class CreateLaporATHG extends CreateRecord
{
    protected static string $resource = LaporATHGResource::class;

    // Improved UX with step-by-step guidance
    public function getTitle(): string
    {
        return 'Lapor ATHG - Ancaman, Tantangan, Hambatan, Gangguan';
    }

    public function getSubheading(): ?string
    {
        return 'Laporkan situasi yang memerlukan perhatian khusus di bidang Ekonomi, Budaya, Politik, Keamanan, Lingkungan, atau Kesehatan';
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('✅ Laporan ATHG berhasil dibuat!')
            ->body('ID Laporan: ' . $this->getRecord()->lapathg_id . '. Tim akan melakukan verifikasi dalam 1x24 jam.')
            ->actions([
                \Filament\Notifications\Actions\Action::make('view')
                    ->button()
                    ->url($this->getRedirectUrl())
                    ->label('Lihat Laporan'),
            ])
            ->persistent();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status_athg'] = 'pending';
        $data['user_id'] = auth()->id();
        
        // Auto-fill from user profile if not provided
        if (empty($data['nama_pelapor'])) {
            $user = auth()->user();
            $data['nama_pelapor'] = trim($user->firstname . ' ' . $user->lastname) ?: $user->username;
        }
        
        if (empty($data['kontak_pelapor'])) {
            $data['kontak_pelapor'] = auth()->user()->no_telepon ?: auth()->user()->email;
        }
        
        return $data;
    }

    // Fixed method name for newer Filament versions
    protected function getFormActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Kirim Laporan ATHG')
                ->icon('heroicon-o-paper-airplane'),
            Actions\Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}