<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SptResource\Pages;
use App\Models\Spt;
use App\Models\Pegawai;
use App\Models\Rekening;
use App\Models\SptPegawai;
use App\Models\Signature;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Twilio\Rest\Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Http;
use Filament\Forms\Components\Actions\Action;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Log;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Panel;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\KonfigurasiAplikasi;

class SptResource extends Resource
{
    protected static ?string $model = Spt::class;
    public static function getNavigationLabel(): string
    {
        return 'Kelola SPT/SPPD';
    }
    protected static ?string $navigationGroup = 'Aplikasi';
    protected static ?int $navigationSort = 10;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Wizard::make([
                // Step 1: Informasi Surat Tugas
                Forms\Components\Wizard\Step::make('Informasi Surat Tugas')
                    ->schema([

                        // Section: Detail Penugasan
                        Forms\Components\Section::make('Detail Penugasan')
                            ->description('Isi informasi terkait penugasan')
                            ->schema([

                                Forms\Components\Grid::make(12)
                                    ->schema([
                                        Forms\Components\Select::make('kategori_perjalanan')
                                            ->label('Kategori Penugasan')
                                            ->options([
                                                'DD' => 'Dalam Daerah',
                                                'LD' => 'Luar Daerah',
                                                'LN' => 'Luar Negeri',
                                            ])
                                            ->required()
                                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                                SptResource::generateResume($set, $get); 
                                            })
                                            ->columnSpan(3), // Column span of 4

                                        Forms\Components\DatePicker::make('tanggal_spt')
                                            ->label('Tanggal Surat Tugas')
                                            ->required()
                                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                                SptResource::generateNomorSpt($set, $get('kategori_perjalanan'), $state); 
                                                SptResource::generateResume($set, $get); 
                                            })
                                            ->columnSpan(3), // Column span of 4

                                        Forms\Components\Textarea::make('perihal_spt')
                                            ->label('Perihal Surat Tugas')
                                            ->nullable()
                                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                                SptResource::generateResume($set, $get);
                                            })
                                            ->columnSpan(6), // Column span of 4
                                    ]),
                            ]),

                        // Section: Lokasi dan Tanggal
                        Forms\Components\Section::make('Lokasi dan Tanggal')
                            ->description('Isi informasi terkait lokasi dan tanggal perjalanan')
                            ->schema([
                                Forms\Components\Grid::make(12)
                                    ->schema([
                                        Forms\Components\Select::make('tempat_berangkat')
                                            ->label('Tempat Berangkat')
                                            ->options(\App\Models\Wilayah::where('level', 'kecamatan')->pluck('nama', 'nama'))
                                            ->searchable()
                                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                                SptResource::generateResume($set, $get); 
                                            })
                                            ->columnSpan(4), // Half width
                                            
                                            Forms\Components\DatePicker::make('tanggal_berangkat')
                                            ->label('Tanggal Berangkat')
                                            ->nullable()
                                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                                SptResource::generateResume($set, $get); 
                                            })
                                            ->columnSpan(3), // Half width
                            
                                        // Multiple select for 'tempat_tujuan' with dynamic filtering
                                        Forms\Components\Select::make('filter_wilayah')
                                            ->label('Filter Wilayah')
                                            ->options([
                                                'provinsi' => 'Provinsi',
                                                'kabupaten' => 'Kabupaten',
                                                'kecamatan' => 'Kecamatan',
                                            ])
                                            ->searchable()
                                            ->nullable()
                                            ->afterStateUpdated(function (callable $set, $state) {
                                                // Optionally, reset 'tempat_tujuan' when filter changes
                                                $set('tempat_tujuan', null);
                                            })
                                            ->columnSpan(3),
                                        Forms\Components\Select::make('tempat_tujuan')
                                            ->label('Tempat Tujuan')
                                            ->multiple()  // Enable multiple selections
                                            ->reactive()  // Make it reactive to filter updates
                                            ->options(function ($get) {
                                                // Get the selected filter type (provinsi, kabupaten, kecamatan)
                                                $filterWilayah = $get('filter_wilayah');

                                                // Dynamically fetch the options based on the selected filter type
                                                if ($filterWilayah == 'provinsi') {
                                                    // Fetch all provinsi
                                                    return \App\Models\Wilayah::where('level', 'provinsi')->pluck('nama', 'nama');
                                                } elseif ($filterWilayah == 'kabupaten') {
                                                    // Fetch all kabupaten
                                                    return \App\Models\Wilayah::where('level', 'kabupaten')->pluck('nama', 'nama');
                                                } elseif ($filterWilayah == 'kecamatan') {
                                                    // Fetch all kecamatan
                                                    return \App\Models\Wilayah::where('level', 'kecamatan')->pluck('nama', 'nama');
                                                }

                                                // Default: Return an empty array if no filter is selected
                                                return [];
                                            })
                                            ->searchable()  // Allow search
                                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                                SptResource::generateResume($set, $get); 
                                            })
                                            ->columnSpan(4),
                                                                    
                                                                    // Half width

                                                                    Forms\Components\DatePicker::make('tanggal_kembali')
                                                                        ->label('Tanggal Kembali')
                                                                        ->nullable()
                                                                        ->afterStateUpdated(function (callable $set, $state, $get) {
                                                                            SptResource::generateResume($set, $get); 
                                                                        })
                                                                        ->columnSpan(3), // Half width
                                                                ]),
                                                        ]),

                        // Section: Informasi Tambahan
                        Forms\Components\Section::make('Informasi Tambahan')
                            ->description('Isi informasi tambahan terkait perjalanan')
                            ->schema([
                                Forms\Components\Grid::make(12)
                                    ->schema([

                                        Forms\Components\Select::make('kode_rekening')
                                            ->label('Kode Rekening')
                                            ->options(Rekening::all()->pluck('combinedLabel', 'id'))
                                            ->searchable()
                                            ->nullable()
                                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                                SptResource::generateResume($set, $get); 
                                            })
                                            ->columnSpan(12), // Full width

                                        Forms\Components\Select::make('pengesah_id')
                                            ->label('Pengesah (Pegawai)')
                                            ->options(Pegawai::all()->pluck('nama_pegawai', 'id'))
                                            ->searchable()
                                            ->nullable()
                                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                                SptResource::generateResume($set, $get); 
                                            })
                                            ->columnSpan(6), // Half width

                                        Forms\Components\Select::make('status_pengesah')
                                            ->label('Status Pengesah')
                                            ->options([
                                                'Kepala Dinas' => 'Kepala Dinas',
                                                'Plt. Kepala Dinas' => 'Plt. Kepala Dinas',
                                                'Plh. Kepala Dinas' => 'Plh. Kepala Dinas',
                                            ])
                                            ->nullable()
                                            ->afterStateUpdated(function (callable $set, $state, $get) {
                                                SptResource::generateResume($set, $get); 
                                            })
                                            ->columnSpan(6), // Half width
                                    ]),
                            ]),
                    ])
                    ->columns(12), // Define columns for Step 1

                // Step 2: Pegawai dan Nomor SPT
                Forms\Components\Wizard\Step::make('Pegawai dan Nomor SPT')
                    ->schema([

                        Forms\Components\Section::make('Nomor dan Pegawai SPT')
                            ->schema([

                                Forms\Components\Grid::make(12)
                                    ->schema([

                                        Forms\Components\TextInput::make('nomor_spt')
                                ->label('Nomor SPT')
                                ->disabled()
                                ->columnSpan(4)
                                ->default(fn ($get) => $get('nomor_spt')),  // Pre-populate existing value
                                
                                Forms\Components\Select::make('pegawai')
                                ->label('Pilih Pegawai')
                                ->options(Pegawai::all()->pluck('nama_pegawai', 'id'))
                                ->multiple()
                                ->searchable()
                                ->afterStateUpdated(function (callable $set, $state, $get) {
                                    if (is_array($state)) {
                                        // Ensure we only deal with non-empty arrays
                                        $flattenedPegawai = Arr::flatten($state);  // Flatten any nested arrays
                            
                                        // Log the state to verify its structure
                                        Log::info('Flattened Pegawai State:', ['flattened' => $flattenedPegawai]);
                            
                                        // Check that the state contains valid integer values (assuming pegawai IDs are integers)
                                        $validPegawaiIds = array_filter($flattenedPegawai, fn($id) => is_numeric($id));
                                        Log::info('Valid Pegawai IDs:', ['pegawaiIds' => $validPegawaiIds]);
                            
                                        // If valid, set the state and generate the resume
                                        $set('pegawai', $validPegawaiIds);
                                        SptResource::generateResume($set, $get);
                                    } else {
                                        Log::error('Invalid state type', ['state' => $state]);
                                    }
                                })
                                ->default(fn ($record) => $record ? $record->pegawai()->pluck('id')->toArray() : [])
                                ->columnSpan(8), // Half width
                            ]),
                            ]),
                    ])
                    ->columns(12), // Define columns for Step 2

                // Step 3: Resume Perjalanan
                Forms\Components\Wizard\Step::make('Resume Perjalanan')
                    ->schema([
                        Forms\Components\Section::make('Resume')
                            ->description('Resume dari SPT yang telah diisi')
                            ->schema([
                                Forms\Components\Textarea::make('resume')
                                    ->label('Resume SPT')
                                    ->disabled()
                                    ->default(fn ($get) => $get('resume'))
                                    ->columnSpan(12), // Full width
                                Forms\Components\Checkbox::make('generate_sppd')
                                    ->label('Terbitkan SPPD?')
                                    ->default(true)  // Default to true
                                    ->columnSpan(6),
                            ]),
                            Forms\Components\Section::make('sppd')
                            ->description('Daftar SPPD')
                            ->schema([
                                Forms\Components\Textarea::make('resume')
                                    ->label('Resume SPT')
                                    ->disabled()
                                    ->default(fn ($get) => $get('resume'))
                                    ->columnSpan(12), // Full width
                                Forms\Components\Checkbox::make('generate_sppd')
                                    ->label('Terbitkan SPPD?')
                                    ->default(true)  // Default to true
                                    ->columnSpan(6),
                            ]),
                    ])
                    ->columns(12), // Define columns for Step 3
            ])
            ->columnSpan('full'), // Full width for the wizard itself
        ]);
    }
    public static function generateResume(callable $set, callable $get)
{
    $pegawaiIds = $get('pegawai');

    // Lakukan operasi yang diinginkan, misalnya ambil nama pegawai dari ID
    if (is_array($pegawaiIds)) {
        $pegawaiNames = Pegawai::whereIn('id', $pegawaiIds)->pluck('nama_pegawai')->implode(', ');
    } elseif (is_numeric($pegawaiIds)) {
        $pegawai = Pegawai::find($pegawaiIds);
        $pegawaiNames = $pegawai ? $pegawai->nama_pegawai : 'Belum dipilih';
    } else {
        $pegawaiNames = 'Belum dipilih';
    }
    $tempatTujuan = $get('tempat_tujuan') ?? [];
    if (is_array($tempatTujuan)) {
        $tempatTujuan = implode(', ', Arr::flatten(array_filter($tempatTujuan, fn($val) => is_string($val))));  // Flatten and filter strings
    } else {
        $tempatTujuan = 'Belum diisi';
    }

    // Fetch Pengesah (approving authority) and handle null values
    $pengesah = Pegawai::find($get('pengesah_id'));  // Fetch the Pegawai model based on the pengesah_id
    $pengesahName = $pengesah ? $pengesah->nama_pegawai : 'Belum dipilih';  // Check if pengesah exists

    // Fetch Rekening and handle null values
    $rekening = Rekening::find($get('kode_rekening'));  // Fetch the Rekening model based on kode_rekening
    $rekeningName = $rekening ? $rekening->nama_rekening : 'Belum dipilih';  // Check if rekening exists

    // Create the resume string with all the gathered information
    $resume = sprintf(
        "Nomor SPT: %s\nKategori Perjalanan: %s\nTanggal SPT: %s\nPerihal: %s\nTempat Berangkat: %s\nTempat Tujuan: %s\nTanggal Berangkat: %s\nTanggal Kembali: %s\nPegawai: %s\nPengesah: %s\nStatus Pengesah: %s\nKode Rekening: %s",
        $get('nomor_spt') ?? 'Belum di-generate',  // Default if nomor_spt is not set
        $get('kategori_perjalanan') ?? 'Belum dipilih',  // Default if kategori_perjalanan is not set
        $get('tanggal_spt') ?? 'Belum dipilih',  // Default if tanggal_spt is not set
        $get('perihal_spt') ?? 'Tidak ada perihal',  // Default if perihal_spt is not set
        $get('tempat_berangkat') ?? 'Belum diisi',  // Default if tempat_berangkat is not set
        $tempatTujuan,  // Processed tempat_tujuan
        $get('tanggal_berangkat') ?? 'Belum dipilih',  // Default if tanggal_berangkat is not set
        $get('tanggal_kembali') ?? 'Belum dipilih',  // Default if tanggal_kembali is not set
        $pegawaiNames,  // Processed Pegawai names
        $pengesahName,  // Processed Pengesah name
        $get('status_pengesah') ?? 'Belum dipilih',  // Default if status_pengesah is not set
        $rekeningName  // Processed Rekening name
    );

    // Set the generated resume in the form
    $set('resume', $resume);
    Log::info('Pegawai IDs for resume:'.$resume);
    }
    public static function generateNomorSpt(callable $set, $kategori_perjalanan, $tanggal_spt)
    {
        if ($kategori_perjalanan && $tanggal_spt) {
            $date = Carbon::parse($tanggal_spt);
            $bulan_romawi = self::monthToRoman($date->month);
            $currentYear = date('Y');

            $lastSpt = Spt::orderBy('id', 'desc')->first();
            $lastYear = $lastSpt ? $lastSpt->created_at->year : null;

            $countRows = $lastYear !== $currentYear ? 1 : Spt::whereYear('created_at', $currentYear)->count() + 1;

            $nomor_spt = sprintf(
                '094/%d/SPT/BKBP/%s/%s/%d',
                $countRows,
                Str::upper($kategori_perjalanan),
                $bulan_romawi,
                $currentYear
            );

            $set('nomor_spt', $nomor_spt);
        }
    }

    public static function monthToRoman($month)
    {
        $romanMonths = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
        ];

        return $romanMonths[$month] ?? '';
    }
    
    protected function beforeSave(array $data): array
    {
        $this->generateNomorSpt(function ($set) use ($data) {
            $set('nomor_spt', $data['nomor_spt']);
        }, $data['kategori_perjalanan'], $data['tanggal_spt']);
    
    return $data; 
    }
    
    protected static $whatsappErrorMessages = [
    300 => 'Failed to send: No result.',
    400 => 'Device ID not found.',
    401 => 'API Key not found.',
    402 => 'WhatsApp number not registered.',
    403 => 'WhatsApp Multi Device issue.',
    404 => 'Please scan the QR code before using the API.',
    406 => 'Failed to connect to WhatsApp.',
    500 => 'Failed to send message.',
    ];
 
    public static function sendSignatureLink($record,$signedAs = 'PA')
    {
    
    if ($signedAs == 'PA') {
        $signer = Pegawai::findOrFail($record->pengesah_id);
    } else {
        // For PPTK, fetch via 'rekening->pegawai'
        $signer = $record->rekening->pegawai;
    }

    // Check if the signer exists and has a valid WhatsApp contact number
    if (!$signer || !$signer->kontak) {
        return Notification::make()
            ->title('WhatsApp Number Missing')
            ->danger()
            ->body("The {$signedAs} does not have a valid WhatsApp number.")
            ->send();
    }

    $no_hp = $signer->kontak;

    // Generate a unique signed route based on the signer role
    $routeName = ($signedAs == 'PA') ? 'spt.signature.pa' : 'spt.signature.pptk';
    $url = URL::temporarySignedRoute($routeName, now()->addHour(2), ['id' => $record->id, 'role' => $signedAs]);

    $messageBody = "Yth, Bapak/Ibu. {$signer->nama_pegawai},\n\nini merupakan pesan yang dikirimkan melalui sistem elektronik.\n"
        ."Anda tercatat sebagai ({$signedAs}) Mohon kesediaan waktunya melakukan validasi dan persetujuan pelaksanaan SPT pada tautan terlampir:\n\n{$url}\n\nTautan ini hanya berlaku selama 120 Menit.";
    $appkey = '8d659ece-0941-4faa-a9fd-75ec6fd9f937';
    $authkey = 'EL6epXoLmjQ8BP6JknqqhUnHAV14uQn3DJGyGFQjnXrFA2F46B';
    $api_url = 'https://app.wapanels.com/api/create-message';

    try {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'appkey' => $appkey,
            'authkey' => $authkey,
            'to' => $no_hp,
            'message' => $messageBody,
            'sandbox' => 'false' 
            ),
        ));
    $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpcode === 200) {
            return Notification::make()
                ->title('Tautan Terkirim')
                ->success()
                ->body("Tautan tanda tangan telah dikirim kepada {$signer->nama_pegawai}.")
                ->send();
        }

        // Handle errors using a predefined error message array
        $errorMessage = self::$whatsappErrorMessages[$httpcode] ?? 'Unknown error occurred.';
        throw new \Exception($errorMessage);

    } catch (\Exception $e) {
        return Notification::make()
            ->title('Failed to Send WhatsApp Message')
            ->danger()
            ->body('Error: ' . $e->getMessage())
            ->send();
    }
}


    public static function showPdf($sptId)
{
    // Fetch the SPT with relationships
    $spt = Spt::with(['rekening', 'pengesah'])->findOrFail($sptId);
    
    $sptPegawai = SptPegawai::with(['pegawai', 'spt'])
            ->where('spt_id', $sptId)
            ->get();
    
    // Find the signature where signed_as is 'PA'
    $signature = Signature::where('spt_id', $sptId)
        ->where('signed_as', 'PA')
        ->first();

    // Check if the signature exists and retrieve its signed_path
    $paSignPath = $signature ? storage_path('app/' . $signature->signed_path) : null;
    // dd($paSignPath);
    // Continue to generate the PDF, passing the signature path to the view
    $pdf = PDF::loadView('components.pdf.spt', [
        'spt' => $spt,
        'pegawais' => $sptPegawai,
        'pa_sign_path' => $paSignPath // Pass the signature path or null
    ]);

    // Set paper size and orientation
    $pdf->setPaper('F4', 'portrait');

    // Clean filename and stream the PDF
    $filename = $spt->nomor_spt;
    $cleanFilename = str_replace(['/', '\\'], '', $filename);
    return $pdf->stream('spt_'.$cleanFilename.'.pdf');
}


    public function showSppdPdf($sptId)
    {
    // Retrieve all SPT Pegawai records associated with this SPT ID, along with related data
    $sptPegawaiRecords = SptPegawai::with(['pegawai', 'spt'])
                            ->where('spt_id', $sptId)
                            ->get();
    $konfig = KonfigurasiAplikasi::firstOrFail();
    

    // Check if there are any records
    if ($sptPegawaiRecords->isEmpty()) {
        abort(404, 'No SPPD records found for this SPT.');
    }
    
    $signaturePPTK = Signature::where('spt_id', $sptId)
        ->where('signed_as', 'PPTK')
        ->first();
    $signaturePA = Signature::where('spt_id', $sptId)
        ->where('signed_as', 'PA')
        ->first();

    // Check if the signature exists and retrieve its signed_path
    $pptkSignPath = $signaturePPTK ? storage_path('app/' . $signaturePPTK->signed_path) : null;
    $paSignPath = $signaturePA ? storage_path('app/' . $signaturePA->signed_path) : null;
    $data = [
        'sptPegawaiRecords' => $sptPegawaiRecords, // Pass all related records
        'spt' => $sptPegawaiRecords->first()->spt,
        'pptk_sign_path' => $pptkSignPath,
        'pa_sign_path' => $paSignPath,
        'konfig' => $konfig,
    ];
    
   
    

    // Generate the PDF using the 'components.pdf.sppd' view and the prepared data
    $pdf = PDF::loadView('components.pdf.sppd-landscape', $data);
    return $pdf->stream('SPPD-'.$sptId.'.pdf');
    }
    public function generateFilteredPdf(Request $request)
    {
        // Use $request->input() to get query parameters
        $year = $request->input('year');
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
    
        // Validate the date range
        if ($startMonth > $endMonth) {
            return abort(400, 'Bulan Mulai tidak boleh lebih besar dari Bulan Akhir.');
        }
    
        // Fetch records
        $spt = Spt::with(['rekening', 'pengesah'])->whereYear('tanggal_spt', $year)->first();

        if (!$spt) {
            return abort(404, 'No matching SPT found for the provided filters.');
        }
    
        // Filter SPT Pegawai records
        $sptPegawai = SptPegawai::with(['pegawai', 'spt'])
            ->whereHas('spt', function ($query) use ($year, $startMonth, $endMonth) {
                $query->whereYear('tanggal_spt', $year)
                      ->whereMonth('tanggal_spt', '>=', $startMonth)
                      ->whereMonth('tanggal_spt', '<=', $endMonth);
            })
            ->get();

        
        $pdf = Pdf::loadView('components.pdf.filtered-data', [
        'spt' => $spt,
        'sptPegawai' => $sptPegawai,
        ])->setPaper('letter', 'landscape');
    
        // Stream the PDF to the browser
        return $pdf->stream("Rekapitulasi_Data_SPT_{$startMonth}_{$endMonth}.pdf");
    }
    
    public static function query(): Builder
    {
    return Spt::query()
        ->with('pegawai')  // Load the pegawai relationship, which includes the pivot data (nomor_sppd)
        ->select('spt.*');
    }


    public static function table(Table $table): Table
    {
        return $table
        ->columns([                           
                Tables\Columns\TextColumn::make('nomor_spt')
                ->label('Nomor SPT / SPPD')
                ->getStateUsing(function ($record) {
                    // Fetch the Nomor SPT
                    $nomorSpt = $record->nomor_spt ?? 'N/A';

                    // Directly query the spt_pegawai table to get all nomor_sppd for this SPT
                    $nomorSppdRecords = DB::table('spt_pegawai')
                        ->where('spt_id', $record->id)  // Match the current SPT ID
                        ->pluck('nomor_sppd');  // Get the nomor_sppd field

                    if ($nomorSppdRecords->isNotEmpty()) {
                        // Add the arrow '->' for all but the first record
                        $nomorSppd = $nomorSppdRecords->map(function ($nomorSppd, $index) {
                            return $index > -1 ? '->' . $nomorSppd : $nomorSppd;  // Add '->' to all except the first one
                        })->implode('<br>');
                    } else {
                        $nomorSppd = 'N/A';
                    }

                    // Combine Nomor SPT and Nomor SPPD in a single column
                    return $nomorSpt . '<br>' . $nomorSppd;
                })
                ->html()  // Enable HTML rendering to support <br>
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('tempat_tujuan')->label('Tujuan')->getStateUsing(function ($record) {
                    // Fetch the tempat_tujuan from the database
                    $getTempatTujuan = $record->tempat_tujuan ?? '[]';  // Default to an empty JSON array if null
            
                    // Check if it's a JSON string and decode it
                    if (is_string($getTempatTujuan)) {
                        $tempatTujuanArray = json_decode($getTempatTujuan, true);  // Decode the JSON string into an array
                    } else {
                        $tempatTujuanArray = $getTempatTujuan;  // Assume it's already an array
                    }
            
                    // Check if decoding was successful and implode the array into a string
                    if (is_array($tempatTujuanArray)) {
                        $tempatTujuanString = implode('<br>', $tempatTujuanArray);  // Convert the array into a comma-separated string
                    } else {
                        $tempatTujuanString = 'Invalid data';  // Handle the case where it's not an array
                    }
                    return $tempatTujuanString;
                })
                ->sortable()
                ->html()
                ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_spt')->label('Tanggal SPT')->date(),
                Tables\Columns\TextColumn::make('status_spt')->label('Status SPT'),
                Tables\Columns\TextColumn::make('rekening.pegawai.nama_pegawai')->label('PPTK'),
            ])     
                    
            ->actions([
                Tables\Actions\EditAction::make(),                
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('sendSignatureLink')
                            ->label('Kirim ke PA/KPA')->hiddenLabel()->tooltip('Pengesahan PA/KPA')
                            ->icon('heroicon-o-paper-airplane')
                            ->action(fn ($record) => static::sendSignatureLink($record,'PA'))
                            ->requiresConfirmation()
                            ->modalHeading('Konfirmasi Pengiriman')
                            ->modalDescription(fn ($record) => new HtmlString('<p>Anda akan mengirimkan permintaan ke PA/KPA<strong>' . $record->pengesah->nama_pegawai . '</strong>?</p>'))
                            ->modalSubmitActionLabel('Kirim Tautan'),
                        Tables\Actions\Action::make('sendPptkLink')
                            ->label('Kirim ke PPTK')->hiddenLabel()->tooltip('Pengesahan PPTK') 
                            ->icon('heroicon-o-paper-airplane')
                            ->action(fn ($record) => static::sendSignatureLink($record,'PPTK'))
                            ->requiresConfirmation()
                            ->modalHeading('Konfirmasi Pengiriman')
                            ->modalDescription(fn ($record) => new HtmlString('<p>Anda akan mengirimkan permintaan ke PPTK <strong>' . $record->rekening->pegawai->nama_pegawai . '</strong>?</p>'))
                            ->modalSubmitActionLabel('Kirim Tautan'),
                        Tables\Actions\Action::make('cetak_spt')
                            ->label('Cetak SPT')
                            ->icon('heroicon-m-printer')
                            ->url(fn (Spt $record): string => route('spt.show-pdf', ['sptId' => $record->id]))
                            ->openUrlInNewTab(),
                        Tables\Actions\Action::make('cetak_sppd')
                            ->label('Cetak SPPD')
                            ->icon('heroicon-m-printer')
                            ->url(fn (Spt $record): string => route('sppd.show-pdf', ['sptId' => $record->id]))
                            ->openUrlInNewTab(),
                        ]),    
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'ditolak' => 'Ditolak',
                        'setuju' => 'Setuju',
                    ]),
            ])
            ->headerActions([
    Tables\Actions\Action::make('print_by_month')
        ->label('Cetak Rekap')
        ->icon('heroicon-o-printer')
        ->form([
            Forms\Components\Select::make('year')
                ->label('Tahun')
                ->options(
                    collect(range(now()->year - 3, now()->year + 3))
                        ->mapWithKeys(fn($year) => [$year => (string) $year])
                )
                ->searchable()
                ->preload()
                ->live()
                ->required()
                ->default(now()->year),

            Forms\Components\Select::make('start_month')
                ->label('Bulan Mulai')
                ->options([
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ])
                ->required(),

            Forms\Components\Select::make('end_month')
                ->label('Bulan Akhir')
                ->options([
                    1 => 'Januari',
                    2 => 'Februari',
                    3 => 'Maret',
                    4 => 'April',
                    5 => 'Mei',
                    6 => 'Juni',
                    7 => 'Juli',
                    8 => 'Agustus',
                    9 => 'September',
                    10 => 'Oktober',
                    11 => 'November',
                    12 => 'Desember',
                ])
                ->required(),
        ])
        ->action(function (array $data) {
            // Redirect to the PDF route with query parameters
            return redirect()->route('spt.filtered-pdf', [
                'year' => $data['year'],
                'start_month' => $data['start_month'],
                'end_month' => $data['end_month'],
            ]);
        })
        ->requiresConfirmation(),
])
;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSpts::route('/'),
            'create' => Pages\CreateSpt::route('/create'),
            'edit' => Pages\EditSpt::route('/{record}/edit'),
        ];
    }    
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['rekening.pegawai']);
    }
}