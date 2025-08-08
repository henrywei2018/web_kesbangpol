<?php

// UPDATE: app/Filament/Pages/Setting/ManageWhatsApp.php

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

                        Forms\Components\Section::make('Basic WhatsApp Test')
                            ->description('Send a simple test message to verify API connection')
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
                            ]),

                        // NEW SECTION: Service Integration Tests
                        Forms\Components\Section::make('🚀 Service Integration Tests')
                            ->description('Test WhatsApp notifications for all public panel services')
                            ->schema([
                                Forms\Components\Placeholder::make('service_test_info')
                                    ->content('Test the actual notification messages that users receive when submitting forms on the public panel. Each test simulates a real service submission.')
                                    ->columnSpanFull(),

                                Forms\Components\TextInput::make('service_test_phone')
                                    ->label('Test Phone Number')
                                    ->placeholder('085387555568')
                                    ->helperText('Phone number to receive test notifications')
                                    ->columnSpanFull(),

                                Forms\Components\Actions::make([
                                    // SKT Test
                                    Forms\Components\Actions\Action::make('test_skt')
                                        ->label('Test SKT Service')
                                        ->action('testSKTService')
                                        ->color('primary')
                                        ->icon('heroicon-o-building-office-2')
                                        ->requiresConfirmation()
                                        ->modalHeading('Test SKT ORMAS Registration')
                                        ->modalDescription('This will send both USER and ADMIN notifications for SKT (ORMAS Registration) service as if someone just submitted a new registration.')
                                        ->modalSubmitActionLabel('Send Test'),

                                    // SKL Test
                                    Forms\Components\Actions\Action::make('test_skl')
                                        ->label('Test SKL Service')
                                        ->action('testSKLService')
                                        ->color('info')
                                        ->icon('heroicon-o-document-check')
                                        ->requiresConfirmation()
                                        ->modalHeading('Test SKL ORMAS Clearance')
                                        ->modalDescription('This will send both USER and ADMIN notifications for SKL (ORMAS Clearance) service.')
                                        ->modalSubmitActionLabel('Send Test'),

                                    // PPID Information Request Test
                                    Forms\Components\Actions\Action::make('test_ppid_request')
                                        ->label('Test Information Request')
                                        ->action('testPPIDRequestService')
                                        ->color('success')
                                        ->icon('heroicon-o-document-text')
                                        ->requiresConfirmation()
                                        ->modalHeading('Test PPID Information Request')
                                        ->modalDescription('This will send both USER and ADMIN notifications for Information Request service.')
                                        ->modalSubmitActionLabel('Send Test'),
                                ])->fullWidth(),

                                Forms\Components\Actions::make([
                                    // PPID Objection Test
                                    Forms\Components\Actions\Action::make('test_ppid_objection')
                                        ->label('Test Information Objection')
                                        ->action('testPPIDObjectionService')
                                        ->color('warning')
                                        ->icon('heroicon-o-exclamation-triangle')
                                        ->requiresConfirmation()
                                        ->modalHeading('Test PPID Information Objection')
                                        ->modalDescription('This will send both USER and ADMIN notifications for Information Objection service.')
                                        ->modalSubmitActionLabel('Send Test'),

                                    // ATHG Test
                                    Forms\Components\Actions\Action::make('test_athg')
                                        ->label('Test ATHG Report')
                                        ->action('testATHGService')
                                        ->color('danger')
                                        ->icon('heroicon-o-shield-exclamation')
                                        ->requiresConfirmation()
                                        ->modalHeading('Test ATHG Security Report')
                                        ->modalDescription('This will send both USER and ADMIN notifications for ATHG (Security Report) service.')
                                        ->modalSubmitActionLabel('Send Test'),

                                    // Test ALL Services
                                    Forms\Components\Actions\Action::make('test_all_services')
                                        ->label('🎯 Test ALL Services')
                                        ->action('testAllServices')
                                        ->color('gray')
                                        ->icon('heroicon-o-rocket-launch')
                                        ->requiresConfirmation()
                                        ->modalHeading('Test All Public Panel Services')
                                        ->modalDescription('This will send test notifications for ALL services (SKT, SKL, PPID Request, PPID Objection, ATHG) to verify complete integration. Total: 10 messages will be sent.')
                                        ->modalSubmitActionLabel('Send All Tests'),
                                ])->fullWidth(),
                            ])
                            ->collapsible(),
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

    // ===================== BASIC TEST METHODS =====================

    public function sendTestUserMessage()
    {
        $data = $this->form->getState();
        
        if (empty($data['test_phone'])) {
            $this->sendErrorNotification('Please enter a test phone number');
            return;
        }

        try {
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

    // ===================== SERVICE TEST METHODS =====================

    public function testSKTService()
    {
        $data = $this->form->getState();
        $phone = $data['service_test_phone'] ?? $data['test_phone'] ?? null;
        
        if (empty($phone)) {
            $this->sendErrorNotification('Please enter a phone number for service testing');
            return;
        }

        try {
            $settings = app(WhatsAppSettings::class);
            $settings->fill($data);
            
            $fonteService = new FonteService();
            
            // Test data for SKT
            $testData = [
                'id' => 'TEST-001',
                'nama_ormas' => 'ORMAS Test Kaltara',
                'jenis_permohonan' => 'pendaftaran',
            ];
            
            $result = $fonteService->sendSKTNotification($phone, $testData);

            if ($result['overall_success']) {
                $userSuccess = $result['user']['success'] ? '✅ User' : '❌ User';
                $adminSuccess = $result['admin']['success'] ? '✅ Admin' : '❌ Admin';
                
                $this->sendSuccessNotification("SKT Service Test Results:\n{$userSuccess} notification\n{$adminSuccess} notification");
            } else {
                $this->sendErrorNotification('❌ SKT Service test failed. Check your settings and try again.');
            }
        } catch (\Exception $e) {
            $this->sendErrorNotification('❌ SKT Service test error: ' . $e->getMessage());
        }
    }

    public function testSKLService()
    {
        $data = $this->form->getState();
        $phone = $data['service_test_phone'] ?? $data['test_phone'] ?? null;
        
        if (empty($phone)) {
            $this->sendErrorNotification('Please enter a phone number for service testing');
            return;
        }

        try {
            $settings = app(WhatsAppSettings::class);
            $settings->fill($data);
            
            $fonteService = new FonteService();
            
            $testData = [
                'id' => 'TEST-SKL-001',
                'nama_organisasi' => 'Organisasi Test Kaltara',
                'email_organisasi' => 'test@kaltara.id',
            ];
            
            $result = $fonteService->sendSKLNotification($phone, $testData);

            if ($result['overall_success']) {
                $userSuccess = $result['user']['success'] ? '✅ User' : '❌ User';
                $adminSuccess = $result['admin']['success'] ? '✅ Admin' : '❌ Admin';
                
                $this->sendSuccessNotification("SKL Service Test Results:\n{$userSuccess} notification\n{$adminSuccess} notification");
            } else {
                $this->sendErrorNotification('❌ SKL Service test failed. Check your settings and try again.');
            }
        } catch (\Exception $e) {
            $this->sendErrorNotification('❌ SKL Service test error: ' . $e->getMessage());
        }
    }

    public function testPPIDRequestService()
    {
        $data = $this->form->getState();
        $phone = $data['service_test_phone'] ?? $data['test_phone'] ?? null;
        
        if (empty($phone)) {
            $this->sendErrorNotification('Please enter a phone number for service testing');
            return;
        }

        try {
            $settings = app(WhatsAppSettings::class);
            $settings->fill($data);
            
            $fonteService = new FonteService();
            
            $testData = [
                'id' => 'TEST-PPID-001',
                'nama_lengkap' => 'Budi Santoso Test',
            ];
            
            $result = $fonteService->sendInformationRequestNotification($phone, $testData);

            if ($result['overall_success']) {
                $userSuccess = $result['user']['success'] ? '✅ User' : '❌ User';
                $adminSuccess = $result['admin']['success'] ? '✅ Admin' : '❌ Admin';
                
                $this->sendSuccessNotification("PPID Information Request Test Results:\n{$userSuccess} notification\n{$adminSuccess} notification");
            } else {
                $this->sendErrorNotification('❌ PPID Information Request test failed. Check your settings and try again.');
            }
        } catch (\Exception $e) {
            $this->sendErrorNotification('❌ PPID Information Request test error: ' . $e->getMessage());
        }
    }

    public function testPPIDObjectionService()
    {
        $data = $this->form->getState();
        $phone = $data['service_test_phone'] ?? $data['test_phone'] ?? null;
        
        if (empty($phone)) {
            $this->sendErrorNotification('Please enter a phone number for service testing');
            return;
        }

        try {
            $settings = app(WhatsAppSettings::class);
            $settings->fill($data);
            
            $fonteService = new FonteService();
            
            $testData = [
                'id' => 'TEST-OBJECTION-001',
                'nama_lengkap' => 'Siti Aminah Test',
            ];
            
            $result = $fonteService->sendInformationObjectionNotification($phone, $testData);

            if ($result['overall_success']) {
                $userSuccess = $result['user']['success'] ? '✅ User' : '❌ User';
                $adminSuccess = $result['admin']['success'] ? '✅ Admin' : '❌ Admin';
                
                $this->sendSuccessNotification("PPID Information Objection Test Results:\n{$userSuccess} notification\n{$adminSuccess} notification");
            } else {
                $this->sendErrorNotification('❌ PPID Information Objection test failed. Check your settings and try again.');
            }
        } catch (\Exception $e) {
            $this->sendErrorNotification('❌ PPID Information Objection test error: ' . $e->getMessage());
        }
    }

    public function testATHGService()
    {
        $data = $this->form->getState();
        $phone = $data['service_test_phone'] ?? $data['test_phone'] ?? null;
        
        if (empty($phone)) {
            $this->sendErrorNotification('Please enter a phone number for service testing');
            return;
        }

        try {
            $settings = app(WhatsAppSettings::class);
            $settings->fill($data);
            
            $fonteService = new FonteService();
            
            $testData = [
                'id' => 'TEST-ATHG-001',
                'nama_lengkap' => 'Ahmad Rianto Test',
                'bidang' => 'keamanan',
                'tingkat_urgensi' => 'tinggi',
            ];
            
            $result = $fonteService->sendATHGReportNotification($phone, $testData);

            if ($result['overall_success']) {
                $userSuccess = $result['user']['success'] ? '✅ User' : '❌ User';
                $adminSuccess = $result['admin']['success'] ? '✅ Admin' : '❌ Admin';
                
                $this->sendSuccessNotification("ATHG Service Test Results:\n{$userSuccess} notification\n{$adminSuccess} notification");
            } else {
                $this->sendErrorNotification('❌ ATHG Service test failed. Check your settings and try again.');
            }
        } catch (\Exception $e) {
            $this->sendErrorNotification('❌ ATHG Service test error: ' . $e->getMessage());
        }
    }

    public function testAllServices()
    {
        $data = $this->form->getState();
        $phone = $data['service_test_phone'] ?? $data['test_phone'] ?? null;
        
        if (empty($phone)) {
            $this->sendErrorNotification('Please enter a phone number for service testing');
            return;
        }

        try {
            $settings = app(WhatsAppSettings::class);
            $settings->fill($data);
            
            $fonteService = new FonteService();
            $results = [];

            // Test all services
            $services = [
                'SKT' => [$fonteService, 'sendSKTNotification', [
                    'id' => 'TEST-001',
                    'nama_ormas' => 'ORMAS Test Kaltara',
                    'jenis_permohonan' => 'pendaftaran',
                ]],
                'SKL' => [$fonteService, 'sendSKLNotification', [
                    'id' => 'TEST-SKL-001',
                    'nama_organisasi' => 'Organisasi Test Kaltara',
                ]],
                'PPID Request' => [$fonteService, 'sendInformationRequestNotification', [
                    'id' => 'TEST-PPID-001',
                    'nama_lengkap' => 'Budi Santoso Test',
                ]],
                'PPID Objection' => [$fonteService, 'sendInformationObjectionNotification', [
                    'id' => 'TEST-OBJECTION-001',
                    'nama_lengkap' => 'Siti Aminah Test',
                ]],
                'ATHG' => [$fonteService, 'sendATHGReportNotification', [
                    'id' => 'TEST-ATHG-001',
                    'nama_lengkap' => 'Ahmad Rianto Test',
                    'bidang' => 'keamanan',
                    'tingkat_urgensi' => 'tinggi',
                ]],
            ];

            foreach ($services as $serviceName => [$service, $method, $testData]) {
                try {
                    $result = $service->$method($phone, $testData);
                    $results[$serviceName] = $result['overall_success'];
                    
                    // Small delay between messages
                    usleep(500000); // 0.5 seconds
                } catch (\Exception $e) {
                    $results[$serviceName] = false;
                }
            }

            // Create summary
            $successCount = array_sum($results);
            $totalCount = count($results);
            
            $summary = "🎯 All Services Test Results ({$successCount}/{$totalCount} passed):\n\n";
            foreach ($results as $service => $success) {
                $icon = $success ? '✅' : '❌';
                $summary .= "• {$icon} {$service}\n";
            }

            if ($successCount === $totalCount) {
                $this->sendSuccessNotification($summary . "\n🚀 All services are working perfectly!");
            } else {
                $this->sendErrorNotification($summary . "\n⚠️ Some services need attention. Check your settings and admin phone numbers.");
            }

        } catch (\Exception $e) {
            $this->sendErrorNotification('❌ All Services test error: ' . $e->getMessage());
        }
    }

    // ===================== HELPER METHODS =====================

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