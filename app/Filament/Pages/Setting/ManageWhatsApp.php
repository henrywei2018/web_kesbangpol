<?php

namespace App\Filament\Pages\Setting;

use App\Settings\WhatsAppSettings;
use App\Services\FonteService;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\Support\Htmlable;

use function Filament\Support\is_app_url;

class ManageWhatsApp extends SettingsPage
{
    use HasPageShield;

    protected static string $settings = WhatsAppSettings::class;

    protected static ?int $navigationSort = 98;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $this->callHook('beforeFill');

        $data = $this->mutateFormDataBeforeFill(app(static::getSettings())->toArray());

        $this->form->fill($data);

        $this->callHook('afterFill');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('WhatsApp API Configuration')
                            ->description('Configure your Fonnte WhatsApp API settings')
                            ->icon('heroicon-o-cog-6-tooth')
                            ->schema([
                                Forms\Components\Toggle::make('enabled')
                                    ->label('Enable WhatsApp Notifications')
                                    ->helperText('Turn on/off all WhatsApp notifications')
                                    ->inline(false),

                                Forms\Components\TextInput::make('api_url')
                                    ->label('API URL')
                                    ->placeholder('https://api.fonnte.com/send')
                                    ->url()
                                    ->required(),

                                Forms\Components\TextInput::make('token')
                                    ->label('API Token')
                                    ->placeholder('Enter your Fonnte API token')
                                    ->password()
                                    ->revealable()
                                    ->required(),
                            ]),
                    ])
                    ->columnSpan([
                        "md" => 2
                    ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Admin Notifications')
                            ->description('Configure admin phone numbers to receive notifications')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Forms\Components\TextInput::make('admin_main')
                                    ->label('Main Admin')
                                    ->helperText('Receives ALL notifications (required)')
                                    ->placeholder('e.g., 085387555568')
                                    ->required(),

                                Forms\Components\TextInput::make('admin_backup')
                                    ->label('Backup Admin')
                                    ->helperText('Fallback admin (optional)')
                                    ->placeholder('e.g., 081234567890'),

                                Forms\Components\Fieldset::make('Service-Specific Admins')
                                    ->label('Service-Specific Admins (Optional)')
                                    ->schema([
                                        Forms\Components\TextInput::make('admin_skt')
                                            ->label('SKT Admin')
                                            ->placeholder('For ORMAS registrations'),

                                        Forms\Components\TextInput::make('admin_skl')
                                            ->label('SKL Admin')
                                            ->placeholder('For ORMAS clearance'),

                                        Forms\Components\TextInput::make('admin_ppid')
                                            ->label('PPID Admin')
                                            ->placeholder('For information requests'),

                                        Forms\Components\TextInput::make('admin_athg')
                                            ->label('ATHG Admin')
                                            ->placeholder('For security reports'),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Section::make('Test WhatsApp')
                            ->description('Send a test message to verify your settings')
                            ->schema([
                                Forms\Components\TextInput::make('test_phone')
                                    ->label('Test Phone Number')
                                    ->placeholder('085387555568')
                                    ->helperText('Enter phone number to send test message'),

                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('test_user')
                                        ->label('Test User Message')
                                        ->action('sendTestUserMessage')
                                        ->color('success')
                                        ->icon('heroicon-o-paper-airplane')
                                        ->requiresConfirmation()
                                        ->modalDescription('This will send a test user notification message'),
                                        
                                    Forms\Components\Actions\Action::make('test_admin')
                                        ->label('Test Admin Message')
                                        ->action('sendTestAdminMessage')
                                        ->color('warning')
                                        ->icon('heroicon-o-megaphone')
                                        ->requiresConfirmation()
                                        ->modalDescription('This will send a test admin notification message'),
                                ])->fullWidth(),
                            ])
                    ])
                    ->columnSpan([
                        "md" => 1
                    ]),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function save(WhatsAppSettings $settings = null): void
    {
        try {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeSave($data);

            $this->callHook('beforeSave');

            $settings->fill($data);
            $settings->save();

            $this->callHook('afterSave');

            $this->sendSuccessNotification('WhatsApp Settings updated successfully!');

            $this->redirect(static::getUrl(), navigate: FilamentView::hasSpaMode() && is_app_url(static::getUrl()));
        } catch (\Throwable $th) {
            $this->sendErrorNotification('Failed to update settings: ' . $th->getMessage());
            throw $th;
        }
    }

    public function sendTestUserMessage()
    {
        $data = $this->form->getState();
        
        if (empty($data['test_phone'])) {
            $this->sendErrorNotification('Please enter a test phone number');
            return;
        }

        try {
            // Temporarily update settings for testing
            $settings = app(WhatsAppSettings::class);
            $settings->fill($data);
            
            $fonteService = new FonteService();
            $result = $fonteService->sendMessage(
                $data['test_phone'], 
                "🧪 *Test Message - User Notification*\n\nHi! This is a test message from your WhatsApp notification system.\n\n✅ Your user notifications are working correctly!\n\nTime: " . now()->format('d/m/Y H:i:s')
            );

            if ($result['success']) {
                $this->sendSuccessNotification('✅ Test user message sent successfully to ' . $data['test_phone']);
            } else {
                $this->sendErrorNotification('❌ Failed to send test message: ' . ($result['error'] ?? $result['reason'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->sendErrorNotification('❌ Test failed: ' . $e->getMessage());
        }
    }

    public function sendTestAdminMessage()
    {
        $data = $this->form->getState();
        
        if (empty($data['test_phone'])) {
            $this->sendErrorNotification('Please enter a test phone number');
            return;
        }

        try {
            // Temporarily update settings for testing
            $settings = app(WhatsAppSettings::class);
            $settings->fill($data);
            
            $fonteService = new FonteService();
            $result = $fonteService->sendMessage(
                $data['test_phone'], 
                "🔔 *[ADMIN] Test Notification*\n\nThis is a test admin notification message.\n\n📋 *Test Details:*\n• Type: Admin Alert\n• Time: " . now()->format('d/m/Y H:i:s') . "\n• Status: Testing\n\n⏰ *Action Required:* No action needed - this is just a test\n\n✅ Your admin notifications are working correctly!"
            );

            if ($result['success']) {
                $this->sendSuccessNotification('✅ Test admin message sent successfully to ' . $data['test_phone']);
            } else {
                $this->sendErrorNotification('❌ Failed to send test message: ' . ($result['error'] ?? $result['reason'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            $this->sendErrorNotification('❌ Test failed: ' . $e->getMessage());
        }
    }

    public function sendSuccessNotification($title)
    {
        Notification::make()
            ->title($title)
            ->success()
            ->send();
    }

    public function sendErrorNotification($title)
    {
        Notification::make()
            ->title($title)
            ->danger()
            ->send();
    }

    public static function getNavigationGroup(): ?string
    {
        return __("menu.nav_group.settings");
    }

    public static function getNavigationLabel(): string
    {
        return 'WhatsApp Settings';
    }

    public function getTitle(): string|Htmlable
    {
        return 'WhatsApp Notifications';
    }

    public function getHeading(): string|Htmlable
    {
        return 'WhatsApp Settings';
    }

    public function getSubheading(): string|Htmlable|null
    {
        return 'Configure WhatsApp notifications for users and administrators';
    }
}