<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\KeberatanInformasiPublikResource\Pages;
use App\Models\KeberatanInformasiPublik;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rule;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Carbon\Carbon;
use Filament\Forms\Components\Tabs;
use Barryvdh\DomPDF\Facade\Pdf;



class KeberatanInformasiPublikResource extends Resource
{
    protected static ?string $model = KeberatanInformasiPublik::class;
    protected static ?string $slug = 'keberatan-informasi';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Keberatan Informasi Publik';
    protected static ?string $navigationGroup = 'PPID';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Tabs::make('Pengajuan Keberatan')
                    ->tabs([
                        Tabs\Tab::make('Informasi Utama')
                            ->schema([
                                Forms\Components\Section::make('Informasi Permohonan')
                                    ->description('Informasi terkait permohonan informasi sebelumnya')
                                    ->schema([
                                        Forms\Components\Select::make('permohonan_id')
                                            ->label('Nomor Register Permohonan Informasi')
                                            ->relationship(
                                                'permohonan',
                                                'nomor_register',
                                                fn(Builder $query) => $query->where('id_pemohon', auth()->id())
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->columnSpan('full')
                                            ->reactive() // Add this to enable reactivity
                                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                                // Get the selected permohonan
                                                $permohonan = \App\Models\PermohonanInformasiPublik::find($state);
                                                if ($permohonan) {
                                                    // Set the values for other fields
                                                    $set('no_telp', $permohonan->no_telp);
                                                    $set('pekerjaan', $permohonan->pekerjaan);
                                                    $set('alamat', $permohonan->alamat);
                                                    $set('rincian_informasi', $permohonan->rincian_informasi);
                                                    $set('nik_no_identitas', $permohonan->nik_no_identitas);
                                                }
                                            }),

                                        Forms\Components\TextInput::make('nomor_registrasi')
                                            ->label('Nomor Registrasi Keberatan')
                                            ->required()
                                            ->default(fn() => static::generateNomorRegistrasi())
                                            ->readonly()
                                            ->rules(function ($record) {
                                                return [
                                                    'required',
                                                    Rule::unique('permohonan_informasi_publik', 'nomor_register')
                                                        ->ignore($record?->id),
                                                ];
                                            })
                                            ->columnSpan('full'),
                                    ])
                                    ->columns(12),

                                Forms\Components\Section::make('Data Pemohon')
                                    ->description('Informasi detail pemohon')
                                    ->schema([
                                        Forms\Components\TextInput::make('nama_lengkap')
                                            ->label('Nama Lengkap')
                                            ->readonly()
                                            ->columnSpan(4)
                                            ->default(fn() => Auth::user() ? Auth::user()->firstname . ' ' . Auth::user()->lastname : '')
                                            ->dehydrated(false)
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                                if ($record && $record->user) {
                                                    $component->state("{$record->user->firstname} {$record->user->lastname}");
                                                }
                                            }),
                                        Forms\Components\TextInput::make('nik_no_identitas')
                                            ->label('NIK/No. Identitas Pribadi')
                                            ->columnSpan(4)
                                            ->required()
        
                                            ->rules(function ($record) {
                                                return [
                                                    'required',
                                                    Rule::unique('keberatan_informasi', 'nik_no_identitas')
                                                        ->ignore($record?->id),
                                                ];
                                            }),

                                        PhoneInput::make('no_telp')
                                            ->label('Nomor Telepon')
                                            ->initialCountry('id')
                                            ->columnSpan(4)
                                            ->required(),

                                        Forms\Components\TextInput::make('pekerjaan')
                                            ->label('Pekerjaan')
                                            ->columnSpan(4)->readonly()
                                            ->required(),

                                        Forms\Components\Textarea::make('alamat')
                                            ->label('Alamat')
                                            ->readonly()
                                            ->columnSpan('full')
                                            ->required(),
                                    ])
                                    ->columns(12),

                                Forms\Components\Section::make('Detail Keberatan')
                                    ->description('Informasi detail keberatan yang diajukan')
                                    ->schema([
                                        Forms\Components\Textarea::make('rincian_informasi')
                                            ->label('Rincian Informasi Permohonan')
                                            ->readonly()
                                            ->rows(3)
                                            ->columnSpan('full'),

                                        Forms\Components\Textarea::make('tujuan_keberatan')
                                            ->label('Tujuan Pengajuan Keberatan')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpan('full'),

                                        Forms\Components\Select::make('alasan_keberatan')
                                            ->label('Alasan Pengajuan Keberatan')
                                            ->options([
                                                'Permohonan Informasi ditolak' => 'Permohonan Informasi ditolak',
                                                'Informasi berkala tidak disediakan' => 'Informasi berkala tidak disediakan',
                                                'Permintaan Informasi tidak ditanggapi' => 'Permintaan Informasi tidak ditanggapi',
                                                'Permintaan Informasi tidak ditanggapi sebagaimana yang diminta' => 'Permintaan Informasi tidak ditanggapi sebagaimana yang diminta',
                                                'Permintaan Informasi tidak dipenuhi' => 'Permintaan Informasi tidak dipenuhi',
                                                'Biaya yang dikenakan tidak wajar' => 'Biaya yang dikenakan tidak wajar',
                                                'Informasi disampaikan melebihi jangka waktu yang ditentukan' => 'Informasi disampaikan melebihi jangka waktu yang ditentukan',
                                            ])
                                            ->required()
                                            ->columnSpan('full'),
                                        
                                    ])
                                    ->columns(12),

                                Forms\Components\Hidden::make('user_id')
                                    ->default(auth()->id()),
                            ]),

                        Tabs\Tab::make('Lampiran')
                            ->schema([
                                Forms\Components\Section::make('Upload Dokumen')
                                    ->description('Upload dokumen pendukung pengajuan keberatan (Format PDF, maksimal 5MB)')
                                    ->schema([
                                        SpatieMediaLibraryFileUpload::make('dokumen_pendukung')
                                            ->label('Dokumen Pendukung Lainnya')
                                            ->acceptedFileTypes(['application/pdf'])  // Only allow PDFs
                                            ->collection('keberatan-docs')  // This should match the media collection in your model
                                            ->disk('public'),
                                        SpatieMediaLibraryFileUpload::make('bukti_permohonan')
                                            ->label('Bukti Cetak Permohonan')
                                            ->acceptedFileTypes(['application/pdf'])  // Only allow PDFs
                                            ->collection('permohonan-docs')  // This should match the media collection in your model
                                            ->disk('public'),
                                    ])
                            ]),
                            Tabs\Tab::make('Tindak Lanjut')
                            ->schema([

                                Forms\Components\Section::make('Update Status')
                                    ->schema([
                                        Forms\Components\TextInput::make('batas_waktu')
                                            ->disabled()
                                            ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                                // Ensure the record has a created_at date
                                                if ($record && $record->created_at) {
                                                    // Add 7 days to the created_at date
                                                    $expiryDate = $record->created_at->addDays(7);

                                                    // Calculate the difference between the current time and expiry time
                                                    $now = Carbon::now();
                                                    $remainingTime = $now->diffInDays($expiryDate, false); // Allow negative values
                                                    $remainingTimes = floor($remainingTime);
                                                    if ($remainingTimes > 0) {
                                                        $component->state("{$remainingTimes} hari sebelum melewati batas waktu penyelesaian");
                                                    } elseif ($remainingTimes == 0) {
                                                        $component->state("Batas waktu penyelesaian adalah hari ini");
                                                    } else {
                                                        $component->state("Expired " . abs($remainingTimes) . " days ago");
                                                    }
                                                } else {
                                                    $component->state('No expiration info available');
                                                }
                                            }),

                                        Forms\Components\Select::make('status')
                                            ->required()
                                            ->disabled()
                                            ->options([
                                                'Pending' => 'Pending',
                                                'Diproses' => 'Diproses',
                                                'Selesai' => 'Selesai',
                                                'Ditolak' => 'Ditolak',
                                            ]),
                                        Forms\Components\Textarea::make('deskripsi_status')
                                            ->required()
                                            ->disabled()
                                            ->rows(3),
                                    ])->columns(1),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nomor_registrasi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('permohonan.nomor_register')
                    ->label('Nomor Permohonan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('tujuan_keberatan')
                    ->limit(50),

                Tables\Columns\TextColumn::make('latest_status')
                    ->label('Status')
                    ->formatStateUsing(function ($record) {
                        $status = $record->latest_status ?? 'Pending';
                        $description = $record->latest_deskripsi_status;

                        $statusColors = [
                            'Pending' => 'text-yellow-600 bg-yellow-100',
                            'Diproses' => 'text-blue-600 bg-blue-100',
                            'Selesai' => 'text-green-600 bg-green-100',
                            'Ditolak' => 'text-red-600 bg-red-100',
                        ];

                        $colorClass = $statusColors[$status] ?? 'text-gray-600 bg-gray-100';

                        return new HtmlString("
                            <div class='space-y-1'>
                                <div class='inline-flex items-center px-2 py-1 rounded-full text-sm font-medium {$colorClass}'>
                                    {$status}
                                </div>
                                " . ($description ? "<div class='text-sm text-gray-500'>{$description}</div>" : "") . "
                            </div>
                        ");
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('')
                    ->tooltip('Detail'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->label('')
                    ->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('')
                    ->tooltip('Delete'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListKeberatanInformasiPubliks::route('/'),
            'create' => Pages\CreateKeberatanInformasiPublik::route('/create'),
            'edit' => Pages\EditKeberatanInformasiPublik::route('/{record}/edit'),
        ];
    }
    public static function generateNomorRegistrasi(): string
    {
    // Get the current year
    $currentYear = Carbon::now()->year;

    // Count records created in the current year
    $recordCountForYear = KeberatanInformasiPublik::whereYear('created_at', $currentYear)->count();

    // Calculate the next number for this year
    $nextNumber = str_pad($recordCountForYear + 1, 3, '0', STR_PAD_LEFT);

    // Convert current month to Roman numerals
    $romanMonths = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
        5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII',
        9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    $currentMonth = Carbon::now()->month;
    $romanMonth = $romanMonths[$currentMonth];

    // Generate the formatted nomor registrasi
    return "{$nextNumber}/REG-PPID/{$romanMonth}/{$currentYear}";
    }

    public static function getEloquentQuery(): Builder
    {
        // Ambil query awal dari parent resource
        $query = parent::getEloquentQuery();

        // Jika user memiliki role 'super_admin', biarkan akses ke semua data
        if (auth()->user()->hasRole('super_admin')) {
            return $query;
        }

        // Jika user memiliki role 'public', batasi query hanya ke aduan milik mereka
        if (auth()->user()->hasRole('public')) {
            return $query->where('id_pemohon', auth()->user()->id);
        }

        // Jika role tidak terdefinisi, bisa mengatur default behavior
        return $query->where('id', -1);  // Tidak menampilkan data jika role tidak cocok
    }
}
