<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PermohonanInformasiPublikResource\Pages;
use App\Models\PermohonanInformasiPublik;
use App\Models\StatusLayanan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Support\HtmlString;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Tabs;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;


class PermohonanInformasiPublikResource extends Resource
{
    protected static ?string $model = PermohonanInformasiPublik::class;
    public static function getNavigationLabel(): string
    {
        return 'Permohonan Informasi';
    }
    protected static ?string $navigationGroup = 'PPID';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Pengajuan Informasi')
                    ->tabs([
                        Tabs\Tab::make('Informasi Umum')
                            ->schema([
                                Forms\Components\Section::make('Informasi Umum')
                                    ->schema([
                                        Forms\Components\Grid::make(12)
                                            ->schema([
                                                Forms\Components\TextInput::make('nik_no_identitas')
                                                    ->label('NIK/No. Identitas Pribadi')
                                                    ->columnSpan(4)
                                                    ->required()
                                                    ->rules(function ($record) {
                                                        return [
                                                            'required',
                                                            Rule::unique('permohonan_informasi_publik', 'nik_no_identitas')
                                                                ->ignore($record?->id),
                                                        ];
                                                    }),

                                                Forms\Components\TextInput::make('nomor_register')
                                                    ->label('Nomor Register')
                                                    ->columnSpan(8)
                                                    ->required()
                                                    ->disabled()
                                                    ->default(fn() => static::generateNomorRegistrasi())
                                                    ->rules(function ($record) {
                                                        return [
                                                            'required',
                                                            Rule::unique('permohonan_informasi_publik', 'nomor_register')
                                                                ->ignore($record?->id),
                                                        ];
                                                    }),

                                                Forms\Components\TextInput::make('nama_lengkap')
                                                    ->label('Nama Lengkap')
                                                    ->disabled()
                                                    ->columnSpan(4)
                                                    ->default(fn() => Auth::user() ? Auth::user()->firstname . ' ' . Auth::user()->lastname : '')
                                                    ->dehydrated(false)
                                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                                        if ($record && $record->user) {
                                                            $component->state("{$record->user->firstname} {$record->user->lastname}");
                                                        }
                                                    }),

                                                Forms\Components\TextInput::make('email')
                                                    ->label('Email')
                                                    ->columnSpan(4)
                                                    ->disabled()
                                                    ->default(fn() => Auth::user() ? Auth::user()->email : '')
                                                    ->dehydrated(false)
                                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                                        if ($record && $record->user) {
                                                            $component->state($record->user->email);
                                                        }
                                                    }),
                                                

                                                PhoneInput::make('no_telp')
                                                    ->label('Nomor Telepon')
                                                    ->initialCountry('id')
                                                    ->columnSpan(4)
                                                    ->required(),

                                                Forms\Components\Textarea::make('alamat')
                                                    ->label('Alamat')
                                                    ->columnSpan(12)
                                                    ->required(),

                                                Forms\Components\Select::make('pekerjaan')
                                                    ->label('Pekerjaan')
                                                    ->columnSpan(4)
                                                    ->options([
                                                        'Belum/ Tidak Bekerja' => 'Belum/ Tidak Bekerja',
                                                        'Pelajar/ Mahasiswa' => 'Pelajar/ Mahasiswa',
                                                        'Pegawai Negeri Sipil' => 'Pegawai Negeri Sipil',
                                                        'Karyawan Swasta' => 'Karyawan Swasta',
                                                        'Wiraswasta' => 'Wiraswasta',
                                                        'Pensiunan' => 'Pensiunan',
                                                        'Lainnya' => 'Lainnya', 
                                                    ])
                                                    ->required()
                                                    ->placeholder('Pilih Pekerjaan'),

                                                Forms\Components\FileUpload::make('ktp_path')
                                                    ->label('Upload KTP')
                                                    ->directory('user/privacy/ktp')
                                                    ->columnSpan(8)
                                                    ->acceptedFileTypes(['image/jpeg', 'image/jpg'])
                                                    ->disk('public')
                                                    ->image()
                                                    ->maxSize(2048)
                                                    ->required(),
                                            ]),
                                    ]),
                                Forms\Components\Section::make('Tujuan dan Rincian Informasi')
                                    ->schema([
                                        Forms\Components\Grid::make(12)
                                            ->schema([
                                                Forms\Components\Textarea::make('tujuan_penggunaan_informasi')
                                                    ->columnSpan(12)
                                                    ->label('Tujuan Penggunaan Informasi')
                                                    ->nullable(),

                                                Forms\Components\Textarea::make('rincian_informasi')
                                                    ->columnSpan(12)
                                                    ->label('Rincian Informasi')
                                                    ->nullable(),

                                                Forms\Components\Select::make('cara_memperoleh_informasi')
                                                    ->columnSpan(4)
                                                    ->label('Cara Memperoleh Informasi')
                                                    ->required()
                                                    ->options([
                                                        'Melihat' => 'Melihat',
                                                        'Membaca' => 'Membaca',
                                                        'Mendengarkan' => 'Mendengarkan',
                                                        'Mencatat' => 'Mencatat',
                                                    ]),

                                                Forms\Components\Select::make('mendapatkan_salinan_informasi')
                                                    ->columnSpan(4)
                                                    ->label('Mendapatkan Salinan Informasi')
                                                    ->required()
                                                    ->options([
                                                        'Softcopy' => 'Softcopy',
                                                        'Hardcopy' => 'Hardcopy',
                                                    ]),

                                                Forms\Components\Select::make('cara_mendapatkan_salinan')
                                                    ->columnSpan(4)
                                                    ->label('Cara Mendapatkan Salinan')
                                                    ->required()
                                                    ->options([
                                                        'Mengambil Langsung' => 'Mengambil Langsung',
                                                        'Faksimili' => 'Faksimili',
                                                        'Email' => 'Email',
                                                    ]),
                                            ]),
                                    ]),
                            ]),
                        Tabs\Tab::make('Tindak Lanjut')
                            ->schema([
                                Forms\Components\Section::make('Tindak Lanjut')
                                    ->schema([
                                        Forms\Components\Grid::make(12)
                                            ->schema([
                                                Forms\Components\TextInput::make('latest_status')
                                                    ->label('Status')
                                                    ->columnSpan(4)
                                                    ->disabled()
                                                    ->dehydrated(false)
                                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                                        if ($record && $record->statuses->last()) {
                                                            $component->state($record->statuses->last()->status);
                                                        }
                                                    }),

                                                Forms\Components\TextInput::make('latest_deskripsi_status')
                                                    ->label('Deskripsi Status')
                                                    ->columnSpan(8)
                                                    ->disabled()
                                                    ->dehydrated(false)
                                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state, $record) {
                                                        if ($record && $record->statuses->last()) {
                                                            $component->state($record->statuses->last()->deskripsi_status);
                                                        }
                                                    }),
                                                PdfViewerField::make('pdf_viewer')
                                                            ->label('Surat Penyampaian Informasi')
                                                            ->columnSpan(12)
                                                            ->fileUrl(fn($record) => $record ? $record->getFirstMediaUrl('bukti-penyampain-ppid-docs') : null) // Use the same dynamic collection name here
                                                            ->minHeight('50vh')
                                                            ->visible(fn($record) => $record && $record->getFirstMediaUrl('bukti-penyampain-ppid-docs')),
                                                // ->fileUrl(fn($record) => $record ? $record->getFirstMediaUrl($record->getDynamicCollectionName()) : null) // Use the same dynamic collection name here
                                                // ->minHeight('50vh')
                                                // ->visible(fn($record) => $record && $record->getFirstMediaUrl($record->getDynamicCollectionName())), // Create a Blade view to display the PDF
                                            ]),                                        
                                    ]),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nomor_register')->label('Nomor Register')->searchable(),
                TextColumn::make('rincian_informasi')->label('Rincian Informasi'),
                Tables\Columns\TextColumn::make('latest_status')
                    ->label('Status')
                    ->formatStateUsing(function ($record) {
                        $status = $record->latest_status ?? 'Pending';
                        $description = $record->latest_deskripsi_status;

                        return new HtmlString("
                        <div class='space-y-1'>
                            <div class='font-medium'>{$status}</div>
                            " . ($description ? "<div class='text-sm text-gray-500'>{$description}</div>" : "") . "
                        </div>
                    ");
                    })
                    ->html(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->hiddenLabel()->tooltip('Detail'),
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()->hiddenLabel()->tooltip('Delete'),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('cetak_permohonan')
                        ->label('Cetak Bukti Register')
                        ->icon('heroicon-m-printer')
                        ->url(fn(PermohonanInformasiPublik $record): string => route('permohonan-informasi.cetak', ['id' => $record->id]))
                        ->openUrlInNewTab(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPermohonanInformasiPubliks::route('/'),
            'create' => Pages\CreatePermohonanInformasiPublik::route('/create'),
            'edit' => Pages\EditPermohonanInformasiPublik::route('/{record}/edit'),
        ];
    }

    public static function generateNomorRegistrasi(): string
    {
    // Get the current year
    $currentYear = Carbon::now()->year;

    // Count records created in the current year
    $recordCountForYear = PermohonanInformasiPublik::whereYear('created_at', $currentYear)->count();

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

    public static function cetak_bukti($id)
    {
        // Fetch the permohonan record with relationships
        $permohonan = PermohonanInformasiPublik::with(['user', 'statuses'])->findOrFail($id);

        // Prepare data to pass to the view
        $data = [
            'permohonan' => $permohonan,
            'user' => $permohonan->user,
            'statuses' => $permohonan->statuses,
        ];

        // Generate the PDF using the 'permohonan_bukti' view
        $pdf = PDF::loadView('components.pdf.bukti_register_ppid', $data);

        $filename = $permohonan->nomor_register;
        $cleanFilename = str_replace(['/', '\\'], '', $filename);
        return $pdf->stream('bukti_register_ppid' . $cleanFilename . '.pdf');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['statuses', 'user']);  // Eager load the statuses and user relationships
    }
}
