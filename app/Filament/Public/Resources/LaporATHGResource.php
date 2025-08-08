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
                Forms\Components\Section::make('Identifikasi ATHG')
                    ->description('Pilih kategori yang paling sesuai dengan situasi yang dilaporkan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('bidang')
                                    ->label('Bidang Terdampak')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getBidangOptions()))
                                    ->required()
                                    ->searchable()
                                    ->helperText('Pilih bidang yang paling relevan dengan kejadian')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                                        $urgencyMap = [
                                            'keamanan' => 'tinggi',
                                            'kesehatan' => 'tinggi', 
                                            'ekonomi' => 'sedang',
                                            'politik' => 'sedang',
                                            'budaya' => 'rendah',
                                            'lingkungan' => 'sedang'
                                        ];
                                        $set('tingkat_urgensi', $urgencyMap[$state] ?? 'normal');
                                    }),

                                Forms\Components\Select::make('jenis_athg')
                                    ->label('Jenis ATHG')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getJenisATHGOptions()))
                                    ->required()
                                    ->searchable()
                                    ->helperText('Pilih jenis yang paling sesuai'),

                                Forms\Components\Select::make('tingkat_urgensi')
                                    ->label('Tingkat Urgensi')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getTingkatUrgensiOptions()))
                                    ->required()
                                    ->default('normal')
                                    ->helperText('Akan diisi otomatis berdasarkan bidang'),
                            ]),
                    ]),

                Forms\Components\Section::make('Detail Kejadian')
                    ->description('Berikan informasi detail tentang kejadian yang dilaporkan')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('perihal')
                                    ->label('Perihal/Judul Laporan')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Dugaan Korupsi Dana Desa'),

                                Forms\Components\TextInput::make('lokasi')
                                    ->label('Lokasi Kejadian')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Desa Sukamaju, Kecamatan Malinau'),

                                Forms\Components\DatePicker::make('tanggal')
                                    ->label('Tanggal Kejadian')
                                    ->required()
                                    ->maxDate(now())
                                    ->default(now()),
                            ]),

                        Forms\Components\Textarea::make('deskripsi_singkat')
                            ->label('Deskripsi Singkat')
                            ->required()
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Jelaskan secara singkat apa yang terjadi'),

                        Forms\Components\Textarea::make('detail_kejadian')
                            ->label('Detail Kejadian')
                            ->required()
                            ->rows(5)
                            ->placeholder('Jelaskan secara detail kronologi kejadian, siapa saja yang terlibat, dan dampak yang ditimbulkan'),

                        Forms\Components\Textarea::make('sumber_informasi')
                            ->label('Sumber Informasi')
                            ->rows(2)
                            ->placeholder('Dari mana Anda mengetahui informasi ini? (opsional)'),

                        Forms\Components\Textarea::make('dampak_potensial')
                            ->label('Dampak Potensial')
                            ->rows(2)
                            ->placeholder('Apa dampak yang mungkin terjadi jika tidak ditangani? (opsional)'),
                    ]),

                Forms\Components\Section::make('Informasi Pelapor')
                    ->description('Informasi kontak untuk follow-up (akan dijaga kerahasiaannya)')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama_pelapor')
                                    ->label('Nama Pelapor')
                                    ->required()
                                    ->maxLength(255)
                                    ->default(fn() => auth()->check() ? trim(auth()->user()->firstname . ' ' . auth()->user()->lastname) : ''),

                                Forms\Components\TextInput::make('kontak_pelapor')
                                    ->label('Kontak (HP/Email)')
                                    ->required()
                                    ->maxLength(255)
                                    ->default(fn() => auth()->check() ? (auth()->user()->no_telepon ?: auth()->user()->email) : '')
                                    ->placeholder('08123456789 atau email@domain.com'),
                            ]),
                    ]),

                // Hidden fields
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => auth()->id()),

                Forms\Components\Hidden::make('status_athg')
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                LaporATHG::query()->where('user_id', auth()->id())
            )
            ->columns([
                Tables\Columns\TextColumn::make('lapathg_id')
                    ->label('ID Laporan')
                    ->searchable()
                    ->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('perihal')
                    ->label('Perihal')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 40) {
                            return null;
                        }
                        return $state;
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
            ]);
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
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
