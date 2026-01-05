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
        // Allow navigation for all users, authenticated or not
        return true;
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
                                    ->live(),

                                Forms\Components\Select::make('jenis_athg')
                                    ->label('Jenis ATHG')
                                    ->options(array_map(fn($option) => $option['label'], LaporATHG::getJenisATHGOptions()))
                                    ->required()
                                    ->searchable()
                                    ->helperText('Pilih jenis yang paling sesuai'),
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

                        
                        Forms\Components\Textarea::make('detail_kejadian')
                            ->label('Detail Kejadian')
                            ->required()
                            ->rows(5)
                            ->placeholder('Jelaskan secara detail kronologi kejadian, siapa saja yang terlibat, dan dampak yang ditimbulkan'),

                        Forms\Components\Textarea::make('sumber_informasi')
                            ->label('Sumber Informasi')
                            ->rows(2)
                            ->placeholder('Dari mana Anda mengetahui informasi ini? (opsional)'),

                        
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
    ->color(function ($record) {
        try {
            $info = $record->getBidangInfo();
            return $info['color'] ?? 'gray';
        } catch (\Exception $e) {
            return 'gray';
        }
    })
    ->formatStateUsing(function ($record) {
        try {
            $info = $record->getBidangInfo();
            return $info['label'] ?? ucfirst((string) $record->bidang) ?? 'N/A';
        } catch (\Exception $e) {
            return 'Error';
        }
    }),

Tables\Columns\TextColumn::make('status_athg')
    ->label('Status')
    ->badge()
    ->color(function ($record) {
        try {
            $info = $record->getStatusInfo();
            return $info['color'] ?? 'gray';
        } catch (\Exception $e) {
            return 'gray';
        }
    })
    ->formatStateUsing(function ($record) {
        try {
            $info = $record->getStatusInfo();
            return $info['label'] ?? ucfirst(str_replace('_', ' ', (string) $record->status_athg)) ?? 'N/A';
        } catch (\Exception $e) {
            return 'Error';
        }
    }),

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
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('')
                    ->tooltip('Detail'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->label('')
                    ->tooltip('Edit')
                    ->visible(fn ($record) => $record->status_athg === 'pending'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('')
                    ->tooltip('Delete')
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
