<?php

namespace App\Filament\Pages\Setting;

use App\Services\FonteService;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;

class ManageWhatsApp extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $title = 'WhatsApp Settings';
    protected static string $view = 'filament.pages.setting.manage-whatsapp';

    public ?array $data = [];

    public function mount(): void
    {
        $this->loadSettings();
    }

    protected function loadSettings(): void
    {
        $fonteService = app(FonteService::class);
        $this->data = $fonteService->getSettings();
        
        // Set defaults if empty
        $defaults = [
            'enabled' => 'true',
            'api_url' => 'https://api.fonnte.com/send',
            'token' => '',
            'admin_main' => '',
            'skt_template' => '🔔 *Notifikasi SKT*

Halo! Permohonan SKT Anda telah diterima.

📋 *Detail:*
• ID: {id}
• Nama Ormas: {nama_ormas}
• Jenis: {jenis_permohonan}

✅ Tim kami akan segera memproses permohonan Anda.
Terima kasih! 🙏',
            'skl_template' => '🔔 *Notifikasi SKL*

Halo! Permohonan SKL Anda telah diterima.

📋 *Detail:*
• ID: {id}
• Nama Organisasi: {nama_organisasi}

✅ Tim kami akan segera memproses permohonan Anda.
Terima kasih! 🙏'
        ];

        foreach ($defaults as $key => $default) {
            if (!isset($this->data[$key]) || empty($this->data[$key])) {
                $this->data[$key] = $default;
            }
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('WhatsApp Configuration')
                    ->description('Configure WhatsApp notifications via Fonnte API')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Toggle::make('enabled')
                                ->label('Enable WhatsApp Notifications')
                                ->default(true),

                            Forms\Components\TextInput::make('admin_main')
                                ->label('Admin Phone Number')
                                ->placeholder('628123456789')
                                ->helperText('Phone number for admin notifications'),
                        ]),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('api_url')
                                ->label('API URL')
                                ->default('https://api.fonnte.com/send')
                                ->required()
                                ->url(),

                            Forms\Components\TextInput::make('token')
                                ->label('API Token')
                                ->required()
                                ->password()
                                ->revealable(),
                        ]),
                    ]),

                Forms\Components\Section::make('Message Templates')
                    ->description('Customize WhatsApp message templates (Use {variable} for dynamic content)')
                    ->schema([
                        Forms\Components\Textarea::make('skt_template')
                            ->label('SKT Notification Template')
                            ->rows(6)
                            ->helperText('Available variables: {id}, {nama_ormas}, {jenis_permohonan}'),

                        Forms\Components\Textarea::make('skl_template')
                            ->label('SKL Notification Template')
                            ->rows(6)
                            ->helperText('Available variables: {id}, {nama_organisasi}'),

                        Forms\Components\Textarea::make('info_request_template')
                            ->label('Information Request Template')
                            ->rows(4)
                            ->helperText('Available variables: {id}, {nama_lengkap}'),

                        Forms\Components\Textarea::make('info_objection_template')
                            ->label('Information Objection Template')
                            ->rows(4)
                            ->helperText('Available variables: {id}, {nama_lengkap}'),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->action('saveSettings')
                ->color('success'),

            Action::make('test_connection')
                ->label('Test Connection')
                ->icon('heroicon-o-signal')
                ->color('info')
                ->action('testConnection'),

            Action::make('send_test_message')
                ->label('Send Test Message')
                ->icon('heroicon-o-paper-airplane')
                ->color('warning')
                ->form([
                    Forms\Components\TextInput::make('phone')
                        ->label('Phone Number')
                        ->required()
                        ->placeholder('628123456789'),
                    Forms\Components\Textarea::make('message')
                        ->label('Message')
                        ->required()
                        ->default('Test message from Laravel application'),
                ])
                ->action('sendTestMessage'),
        ];
    }

    public function saveSettings(): void
    {
        $data = $this->form->getState();
        $fonteService = app(FonteService::class);

        try {
            foreach ($data as $key => $value) {
                $fonteService->updateSetting("whatsapp.{$key}", (string) $value);
            }

            Notification::make()
                ->title('Settings Saved')
                ->body('WhatsApp settings have been saved successfully')
                ->success()
                ->send();

            // Reload settings to reflect changes
            $this->loadSettings();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Save Failed')
                ->body('Failed to save settings: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function testConnection(): void
    {
        $fonteService = app(FonteService::class);
        
        $result = $fonteService->testConnection();
        
        if ($result['success']) {
            Notification::make()
                ->title('Connection Successful')
                ->body('Successfully connected to Fonnte API')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Connection Failed')
                ->body('Failed to connect to Fonnte API: ' . ($result['error'] ?? 'Unknown error'))
                ->danger()
                ->send();
        }
    }

    public function sendTestMessage(array $data): void
    {
        $fonteService = app(FonteService::class);
        
        $result = $fonteService->sendMessage($data['phone'], $data['message']);
        
        if ($result['success']) {
            Notification::make()
                ->title('Message Sent')
                ->body('Test message sent successfully')
                ->success()
                ->send();
        } else {
            Notification::make()
                ->title('Message Failed')
                ->body('Failed to send message: ' . ($result['error'] ?? 'Unknown error'))
                ->danger()
                ->send();
        }
    }
}