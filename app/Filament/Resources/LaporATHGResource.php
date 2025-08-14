<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LaporATHGResource\Pages;
use App\Models\LaporATHG;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use App\Services\FonteService;

class LaporATHGResource extends Resource
{
    protected static ?string $model = LaporATHG::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationLabel = 'ATHG Reports';
    protected static ?string $navigationGroup = 'Security';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('lapathg_id')
                                    ->label('Report ID')
                                    ->disabled()
                                    ->visibleOn('edit'),

                                Forms\Components\Select::make('user_id')
                                    ->label('Reporter')
                                    ->relationship('user', 'username')
                                    ->disabled()
                                    ->visibleOn('edit'),

                                Forms\Components\Select::make('bidang')
                                    ->label('Field')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getBidangOptions()))
                                    ->required(),

                                Forms\Components\Select::make('jenis_athg')
                                    ->label('ATHG Type')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getJenisATHGOptions()))
                                    ->required(),

                                Forms\Components\Select::make('tingkat_urgensi')
                                    ->label('Urgency Level')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getTingkatUrgensiOptions()))
                                    ->required(),

                                Forms\Components\Select::make('status_athg')
                                    ->label('Status')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getStatusOptions()))
                                    ->required()
                                    ->default('pending'),
                            ]),

                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\TextInput::make('perihal')
                                    ->label('Subject')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('lokasi')
                                    ->label('Location')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\DatePicker::make('tanggal')
                                    ->label('Incident Date')
                                    ->required(),

                                Forms\Components\Textarea::make('deskripsi_singkat')
                                    ->label('Brief Description')
                                    ->required()
                                    ->rows(3),

                                Forms\Components\Textarea::make('detail_kejadian')
                                    ->label('Incident Details')
                                    ->required()
                                    ->rows(4),

                                Forms\Components\Textarea::make('sumber_informasi')
                                    ->label('Information Source')
                                    ->rows(2),

                                Forms\Components\Textarea::make('dampak_potensial')
                                    ->label('Potential Impact')
                                    ->rows(2),
                            ]),
                    ]),

                Forms\Components\Section::make('Reporter Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama_pelapor')
                                    ->label('Reporter Name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('kontak_pelapor')
                                    ->label('Reporter Contact')
                                    ->required()
                                    ->maxLength(255),
                            ]),
                    ]),

                Forms\Components\Section::make('Admin Actions')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Textarea::make('catatan_admin')
                                    ->label('Admin Notes')
                                    ->rows(3),

                                Forms\Components\DateTimePicker::make('tanggal_verifikasi')
                                    ->label('Verification Date'),

                                Forms\Components\DateTimePicker::make('tanggal_selesai')
                                    ->label('Completion Date'),
                            ]),
                    ])
                    ->visibleOn('edit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lapathg_id')
                    ->label('Report ID')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.username')
                    ->label('Reporter')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('perihal')
                    ->label('Subject')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('bidang')
                    ->label('Field')
                    ->badge()
                    ->color(fn ($record) => $record->getBidangInfo()['color'] ?? 'gray')
                    ->formatStateUsing(fn ($record) => $record->getBidangInfo()['label'] ?? $record->bidang),

                Tables\Columns\TextColumn::make('jenis_athg')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($record) => $record->getJenisInfo()['color'] ?? 'gray')
                    ->formatStateUsing(fn ($record) => $record->getJenisInfo()['label'] ?? $record->jenis_athg),

                Tables\Columns\TextColumn::make('tingkat_urgensi')
                    ->label('Urgency')
                    ->badge()
                    ->color(fn ($record) => $record->getTingkatUrgensiInfo()['color'] ?? 'gray')
                    ->formatStateUsing(fn ($record) => $record->getTingkatUrgensiInfo()['label'] ?? $record->tingkat_urgensi),

                Tables\Columns\TextColumn::make('status_athg')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => $record->getStatusInfo()['color'] ?? 'gray')
                    ->formatStateUsing(fn ($record) => $record->getStatusInfo()['label'] ?? $record->status_athg),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Incident Date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reported')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bidang')
                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getBidangOptions())),

                Tables\Filters\SelectFilter::make('jenis_athg')
                    ->label('ATHG Type')
                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getJenisATHGOptions())),

                Tables\Filters\SelectFilter::make('tingkat_urgensi')
                    ->label('Urgency Level')
                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getTingkatUrgensiOptions())),

                Tables\Filters\SelectFilter::make('status_athg')
                    ->label('Status')
                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getStatusOptions())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send_to_group')
                ->label('Kirim ke Group')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Kirim Notifikasi ke Group Admin')
                ->modalDescription('Apakah Anda yakin ingin mengirim notifikasi ini ke group admin WhatsApp?')
                ->action(function (LaporATHG $record) {
                    $fonteService = app(FonteService::class);
                    
                    // Ambil group ID dari setting
                    $groupId = $fonteService->getSetting('whatsapp.group_athg', '');
                    
                    // Buat pesan khusus untuk group
                    $message = "🔔 *LAPORAN ATHG - POKUS KALTARA*\n\n" .
                                "Ada data laporan ATHG terverifikasi yang perlu perhatian:\n\n" .
                                "📋 *Detail:*\n" .
                                "• ID ATHG: {$record->lapathg_id}\n" .
                                "• Bidang: {$record->bidang}\n" .                                
                                "• Perihal: {$record->perihal}\n\n" .
                                
                                "• Isi Informasi: {$record->detail_kejadian}\n" .
                                "• Catatan: {$record->catatan_admin}\n" .
                                "• Tanggal Veriikasi: " . $record->updated_at->format('d/m/Y H:i') . "\n\n" .
                                "🚨 *PERHATIAN: Laporan ini bersifat rahasia dan harus ditangani sesuai prosedur*\n\n" .
                                "Silakan cek panel admin untuk detail lengkap.";
                    
                    // Kirim ke group
                    $result = $fonteService->sendGroupMessage($groupId, $message);
                    
                    if ($result['success']) {
                        Notification::make()
                            ->title('Berhasil')
                            ->body('Notifikasi berhasil dikirim ke group admin')
                            ->success()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Gagal')
                            ->body('Gagal mengirim notifikasi: ' . ($result['error'] ?? 'Unknown error'))
                            ->danger()
                            ->send();
                    }
                }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No ATHG Reports')
            ->emptyStateDescription('No reports have been submitted yet.');
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
            'index' => Pages\ListLaporATHGS::route('/'),
            'create' => Pages\CreateLaporATHG::route('/create'),
            'view' => Pages\ViewLaporATHG::route('/{record}'),
            'edit' => Pages\EditLaporATHG::route('/{record}/edit'),
        ];
    }
}