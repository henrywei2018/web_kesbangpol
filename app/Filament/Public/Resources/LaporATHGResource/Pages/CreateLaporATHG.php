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

    // REDIRECT LANGSUNG KE INDEX TANPA MODAL
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // NOTIFIKASI SEDERHANA TANPA ACTIONS
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Laporan ATHG berhasil dikirim!')
            ->body('ID Laporan: ' . $this->getRecord()->lapathg_id . '. Tim akan memproses laporan Anda dalam 1x24 jam.')
            ->duration(5000); // 5 detik
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

    // CUSTOM FORM ACTIONS - HANYA SUBMIT DAN CANCEL
    protected function getFormActions(): array
    {
        return [
            $this->getCreateAction()
                ->label('Kirim Laporan ATHG')
                ->icon('heroicon-o-paper-airplane')
                ->color('primary'),
                
            $this->getCancelFormAction()
                ->label('Batal')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
        ];
    }

    // OVERRIDE CREATE ACTION UNTUK DISABLE MODAL
    protected function getCreateAction(): Actions\Action
    {
        return Actions\Action::make('create')
            ->label('Kirim Laporan ATHG')
            ->icon('heroicon-o-paper-airplane')
            ->color('primary')
            ->action(function () {
                // Validate form
                $this->form->validate();
                
                // Get form data
                $data = $this->form->getState();
                
                // Process data before create
                $data = $this->mutateFormDataBeforeCreate($data);
                
                // Create record
                $record = $this->getModel()::create($data);
                
                // Set record
                $this->record = $record;
                
                // Show notification
                $this->getCreatedNotification()?->send();
                
                // Redirect
                $this->redirect($this->getRedirectUrl());
            })
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Pengiriman Laporan')
            ->modalDescription('Apakah Anda yakin ingin mengirim laporan ATHG ini? Pastikan semua informasi sudah benar.')
            ->modalSubmitActionLabel('Ya, Kirim Laporan')
            ->modalCancelActionLabel('Batal');
    }
}