<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\LaporATHGResource\Pages;
use App\Models\LaporATHG;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\HtmlString;

class LaporATHGResource extends Resource
{
    protected static ?string $model = LaporATHG::class;

    protected static ?string $navigationGroup = 'POKUS KALTARA';
    protected static ?string $navigationLabel = 'Lapor ATHG';
    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->check() && auth()->user()->hasRole('public');
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->check()) {
            $count = static::getModel()::where('user_id', auth()->id())->count();
            return $count > 0 ? (string) $count : null;
        }
        return null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Single-step form with smart sections
                Forms\Components\Section::make('Identifikasi ATHG')
                    ->description('Pilih kategori yang paling sesuai dengan situasi yang dilaporkan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                // Smart bidang selection with visual indicators
                                Forms\Components\Select::make('bidang')
                                    ->label('Bidang Terdampak')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getBidangOptions()))
                                    ->required()
                                    ->searchable()
                                    ->helperText('Pilih bidang yang paling relevan dengan kejadian')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        // Auto-suggest urgency level based on bidang
                                        $urgencyMap = [
                                            'keamanan' => 'tinggi',
                                            'kesehatan' => 'tinggi', 
                                            'ekonomi' => 'sedang',
                                            'politik' => 'sedang',
                                            'budaya' => 'rendah',
                                            'lingkungan' => 'sedang'
                                        ];
                                        $set('tingkat_urgensi', $urgencyMap[$state] ?? 'sedang');
                                    }),

                                Forms\Components\Select::make('jenis_athg')
                                    ->label('Jenis ATHG')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getJenisATHGOptions()))
                                    ->required()
                                    ->helperText('Klasifikasi berdasarkan karakteristik kejadian'),
                            ]),
                    ]),

                Forms\Components\Section::make('Detail Kejadian')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('perihal')
                                    ->label('Perihal (Judul Singkat)')
                                    ->required()
                                    ->maxLength(100)
                                    ->placeholder('Contoh: Gangguan aktivitas ekonomi di...')
                                    ->helperText('Ringkasan singkat dalam 1 kalimat'),

                                Forms\Components\DatePicker::make('tanggal')
                                    ->label('Tanggal Kejadian')
                                    ->required()
                                    ->maxDate(now())
                                    ->default(now()),
                            ]),

                        Forms\Components\TextInput::make('lokasi')
                            ->label('Lokasi Kejadian')
                            ->required()
                            ->placeholder('Contoh: Pasar Tradisional ABC, Kelurahan XYZ, Kota...')
                            ->helperText('Sebutkan lokasi spesifik (nama tempat, alamat, atau koordinat)'),

                        // Smart description with character limits
                        Forms\Components\Textarea::make('deskripsi_singkat')
                            ->label('Deskripsi Singkat')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Jelaskan secara ringkas apa yang terjadi...')
                            ->helperText('Maksimal 500 karakter - fokus pada fakta utama')
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Components\Textarea $component) {
                                $length = strlen($state ?? '');
                                $remaining = 500 - $length;
                                $component->helperText("Tersisa {$remaining} karakter");
                            }),

                        Forms\Components\RichEditor::make('detail_kejadian')
                            ->label('Detail Lengkap Kejadian')
                            ->required()
                            ->placeholder('Berikan detail lengkap: kronologi, pihak yang terlibat, dampak yang terjadi...')
                            ->toolbarButtons([
                                'bold', 'italic', 'bulletList', 'orderedList'
                            ])
                            ->helperText('Jelaskan secara detail dan objektif'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Textarea::make('sumber_informasi')
                                    ->label('Sumber Informasi')
                                    ->required()
                                    ->rows(2)
                                    ->placeholder('Media sosial, saksi mata, dokumen resmi, dll.')
                                    ->helperText('Dari mana informasi ini diperoleh'),

                                Forms\Components\Textarea::make('dampak_potensial')
                                    ->label('Dampak yang Dikhawatirkan')
                                    ->rows(2)
                                    ->placeholder('Dampak yang mungkin terjadi jika tidak ditangani...')
                                    ->helperText('Opsional: Prediksi dampak ke depan'),
                            ]),
                    ]),

                Forms\Components\Section::make('Tingkat Urgensi & Data Pelapor')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('tingkat_urgensi')
                                    ->label('Tingkat Urgensi')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getTingkatUrgensiOptions()))
                                    ->required()
                                    ->helperText('Seberapa mendesak situasi ini'),

                                Forms\Components\TextInput::make('nama_pelapor')
                                    ->label('Nama Pelapor')
                                    ->required()
                                    ->default(fn () => auth()->user()->firstname . ' ' . auth()->user()->lastname)
                                    ->helperText('Nama lengkap pelapor'),

                                Forms\Components\TextInput::make('kontak_pelapor')
                                    ->label('Kontak')
                                    ->required()
                                    ->default(fn () => auth()->user()->no_telepon)
                                    ->placeholder('Nomor HP atau email')
                                    ->helperText('Kontak yang dapat dihubungi'),
                            ]),

                        Forms\Components\Hidden::make('user_id')
                            ->default(auth()->id()),
                    ]),

                // Progress indicator for form completion
                Forms\Components\Section::make('Tips Pelaporan')
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('tips')
                            ->content(new HtmlString('
                                <div class="text-sm space-y-2">
                                    <p><strong>💡 Tips untuk laporan yang efektif:</strong></p>
                                    <ul class="list-disc list-inside space-y-1 text-gray-600">
                                        <li>Gunakan bahasa yang jelas dan objektif</li>
                                        <li>Sertakan detail waktu, tempat, dan pihak yang terlibat</li>
                                        <li>Lampirkan bukti jika tersedia (foto, dokumen, screenshot)</li>
                                        <li>Hindari asumsi atau opini pribadi</li>
                                        <li>Fokus pada fakta yang dapat diverifikasi</li>
                                    </ul>
                                </div>
                            ')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('lapathg_id')
                    ->label('ID Laporan')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->copyable(),

                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        return strlen($state) > 40 ? $state : null;
                    }),

                Tables\Columns\TextColumn::make('bidang')
                    ->label('Bidang')
                    ->badge()
                    ->color(fn ($record) => $record->getBidangInfo()['color'] ?? 'gray')
                    ->formatStateUsing(fn ($record) => $record->getBidangInfo()['label'] ?? $record->bidang),

                Tables\Columns\TextColumn::make('jenis_athg')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn ($record) => $record->getJenisInfo()['color'] ?? 'gray')
                    ->formatStateUsing(fn ($record) => $record->getJenisInfo()['label'] ?? $record->jenis_athg),

                Tables\Columns\TextColumn::make('tingkat_urgensi')
                    ->label('Urgensi')
                    ->badge()
                    ->color(fn ($record) => $record->getTingkatUrgensiInfo()['color'] ?? 'gray')
                    ->formatStateUsing(fn ($record) => $record->getTingkatUrgensiInfo()['label'] ?? $record->tingkat_urgensi),

                Tables\Columns\TextColumn::make('status_athg')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => $record->getStatusInfo()['color'] ?? 'gray')
                    ->formatStateUsing(fn ($record) => $record->getStatusInfo()['label'] ?? $record->status_athg),

                Tables\Columns\TextColumn::make('tanggal')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dilaporkan')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bidang')
                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getBidangOptions())),

                Tables\Filters\SelectFilter::make('jenis_athg')
                    ->label('Jenis ATHG')
                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getJenisATHGOptions())),

                Tables\Filters\SelectFilter::make('tingkat_urgensi')
                    ->label('Tingkat Urgensi')
                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getTingkatUrgensiOptions())),

                Tables\Filters\SelectFilter::make('status_athg')
                    ->label('Status')
                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getStatusOptions())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => $record->status_athg === 'pending'),
            ])
            ->emptyStateHeading('Belum Ada Laporan ATHG')
            ->emptyStateDescription('Laporkan situasi Ancaman, Tantangan, Hambatan, atau Gangguan yang Anda ketahui.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Laporan ATHG')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('created_at', 'desc');
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()->hasRole('super_admin')) {
            return $query;
        }

        if (auth()->user()->hasRole('public')) {
            return $query->where('user_id', auth()->user()->id);
        }

        return $query->where('id', -1);
    }
}