<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SKTDocumentFeedbackResource\Pages;
use App\Filament\Resources\SKTDocumentFeedbackResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\SKT;
use App\Models\SKTDocumentLabel;
use App\Models\SKTDocumentFeedback;
use App\Models\KonfigurasiAplikasi;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Tab;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;



use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class SKTDocumentFeedbackResource extends Resource
{
    protected static ?string $model = SKTDocumentFeedback::class;

    public static function getNavigationLabel(): string
    {
        return 'Reviu SKT';
    }
    protected static ?string $navigationGroup = 'POKUS KALTARA';
    protected static ?int $navigationSort = 4; 
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    public static function getSlug(): string
    {
        return 'reviu-skt';
    }

    public static function form(Form $form): Form
    {   
        $skt = $form->getRecord();
        if (!$skt) {
            return $form->schema([]);
        }

        return $form
            ->schema([
                Tabs::make('Ormas Information')
                    ->tabs([
                        Tabs\Tab::make('Formulir Permohonan')
                            ->schema([
                                Section::make('Formulir Permohonan')
                                    ->schema([
                                        Select::make('jenis_permohonan')
                                            ->label('Jenis Permohonan')
                                            ->options([
                                                'Pendaftaran' => 'Pendaftaran',
                                                'Perubahan' => 'Perubahan',
                                            ])
                                            ->disabled()
                                            ->required(),
                                        TextInput::make('nama_ormas')
                                            ->label('Nama Ormas')
                                            ->disabled()
                                            ->required(),
                                        TextInput::make('nama_singkatan_ormas')
                                            ->label('Nama Singkatan Ormas')
                                            ->disabled()
                                            ->nullable(),
                                        Select::make('tempat_pendirian')
                                            ->label('Tempat Pendirian')
                                            ->options(\App\Models\Wilayah::where('level', 'kabupaten')->pluck('nama', 'nama'))
                                            ->disabled()
                                            ->nullable(),
                                        DatePicker::make('tanggal_pendirian')
                                            ->label('Tanggal Pendirian')
                                            ->disabled()
                                            ->nullable(),
                                        TextInput::make('nomor_surat_permohonan')
                                            ->label('Nomor Surat Permohonan')
                                            ->disabled()
                                            ->nullable(),
                                        DatePicker::make('tanggal_surat_permohonan')
                                            ->label('Tanggal Surat Permohonan')
                                            ->disabled()
                                            ->nullable(),                                        
                                    ]),
                            ]),

                        Tabs\Tab::make('Data Umum Organisasi')
                            ->schema([
                                Section::make('Data Umum Organisasi')
                                    ->schema([
                                        TextInput::make('bidang_kegiatan')
                                            ->label('Bidang Kegiatan')
                                            ->nullable(),
                                        Select::make('ciri_khusus')
                                            ->label('Ciri Khusus')
                                            ->options([
                                                'Keagamaan' => 'Keagamaan',
                                                'Kewanitaan' => 'Kewanitaan',
                                                'Kepemudaan' => 'Kepemudaan',
                                                'Kesamaan Profesi' => 'Kesamaan Profesi',
                                                'Kesamaan Kegiatan' => 'Kesamaan Kegiatan',
                                                'Kesamaan Bidang' => 'Kesamaan Bidang',
                                                'Mitra K/L' => 'Mitra K/L',
                                            ])
                                            ->nullable(),
                                        Textarea::make('tujuan_ormas')
                                            ->label('Tujuan Ormas')
                                            ->nullable(),
                                        Textarea::make('alamat_sekretariat')
                                            ->label('Alamat Sekretariat')
                                            ->nullable(),
                                        Select::make('provinsi')
                                            ->label('Provinsi')
                                            ->options(\App\Models\Wilayah::where('level', 'provinsi')->pluck('nama', 'nama'))
                                            ->searchable(),
                                        Select::make('kab_kota')
                                            ->label('Kabupaten/Kota')
                                            ->options(\App\Models\Wilayah::where('level', 'kabupaten')->pluck('nama', 'nama'))
                                            ->searchable(),
                                        TextInput::make('kode_pos')
                                            ->label('Kode Pos')
                                            ->nullable(),
                                        TextInput::make('nomor_handphone')
                                            ->label('Nomor Handphone')
                                            ->nullable(),
                                        TextInput::make('nomor_fax')
                                            ->label('Nomor Fax')
                                            ->nullable(),
                                        TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->nullable(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Data Legal Organisasi')
                            ->schema([
                                Section::make('Data Legal Organisasi')
                                    ->schema([
                                        TextInput::make('nomor_akta_notaris')
                                            ->label('Nomor Akta Notaris')
                                            ->nullable(),
                                        DatePicker::make('tanggal_akta_notaris')
                                            ->label('Tanggal Akta Notaris')
                                            ->nullable(),
                                        Select::make('jenis_akta')
                                            ->label('Jenis Akta')
                                            ->options([
                                                'Akta Pendirian' => 'Akta Pendirian',
                                                'Akta Perubahan' => 'Akta Perubahan',
                                            ])
                                            ->nullable(),
                                        TextInput::make('nomor_npwp')
                                            ->label('Nomor NPWP')
                                            ->nullable(),
                                        TextInput::make('nama_bank')
                                            ->label('Nama Bank')
                                            ->nullable(),
                                        TextInput::make('nomor_rekening_bank')
                                            ->label('Nomor Rekening Bank')
                                            ->nullable(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Data Struktur Organisasi')
                            ->schema([
                                Section::make('Data Struktur Organisasi')
                                    ->schema([
                                        TextInput::make('ketua_nama_lengkap')
                                            ->label('Nama Lengkap Ketua')
                                            ->required(),
                                        TextInput::make('ketua_nik')
                                            ->label('NIK Ketua')
                                            ->required(),
                                        DatePicker::make('ketua_masa_bakti_akhir')
                                            ->label('Masa Bakti Akhir Ketua')
                                            ->nullable(),
                                        TextInput::make('sekretaris_nama_lengkap')
                                            ->label('Nama Lengkap Sekretaris')
                                            ->required(),
                                        TextInput::make('sekretaris_nik')
                                            ->label('NIK Sekretaris')
                                            ->required(),
                                        DatePicker::make('sekretaris_masa_bakti_akhir')
                                            ->label('Masa Bakti Akhir Sekretaris')
                                            ->nullable(),
                                        TextInput::make('bendahara_nama_lengkap')
                                            ->label('Nama Lengkap Bendahara')
                                            ->required(),
                                        TextInput::make('bendahara_nik')
                                            ->label('NIK Bendahara')
                                            ->required(),
                                        DatePicker::make('bendahara_masa_bakti_akhir')
                                            ->label('Masa Bakti Akhir Bendahara')
                                            ->nullable(),
                                    ]),
                            ]),

                            Tabs\Tab::make('Data Tambahan')
                                ->schema([
                                    Section::make('Data Tambahan')
                                        ->schema([
                                            Repeater::make('nama_pendiri')
                                                ->label('Nama Pendiri')
                                                ->simple(
                                                    TextInput::make('nama')
                                                        ->label('Nama Pendiri')
                                                        ->required(),
                                                )
                                                ->addActionLabel('Tambah Nama Pendiri')
                                                ->nullable(),

                                            Repeater::make('nik_pendiri')
                                                ->label('NIK Pendiri')
                                                ->simple(
                                                    TextInput::make('nik')
                                                        ->label('NIK Pendiri')
                                                        ->required(),
                                                )
                                                ->addActionLabel('Tambah NIK Pendiri')
                                                ->nullable(),

                                            Repeater::make('nama_pembina')
                                                ->label('Nama Pembina')
                                                ->simple(
                                                    TextInput::make('nama')
                                                        ->label('Nama Pembina')
                                                        ->required(),
                                                )
                                                ->addActionLabel('Tambah Nama Pembina')
                                                ->nullable(),

                                            Repeater::make('nik_pembina')
                                                ->label('NIK Pembina')
                                                ->simple(
                                                    TextInput::make('nik')
                                                        ->label('NIK Pembina')
                                                        ->required(),
                                                )
                                                ->addActionLabel('Tambah NIK Pembina')
                                                ->nullable(),

                                            Repeater::make('nama_penasihat')
                                                ->label('Nama Penasihat')
                                                ->simple(
                                                    TextInput::make('nama')
                                                        ->label('Nama Penasihat')
                                                        ->required(),
                                                )
                                                ->addActionLabel('Tambah Nama Penasihat')
                                                ->nullable(),

                                            Repeater::make('nik_penasihat')
                                                ->label('NIK Penasihat')
                                                ->simple(
                                                    TextInput::make('nik')
                                                        ->label('NIK Penasihat')
                                                        ->required(),
                                                )
                                                ->addActionLabel('Tambah NIK Penasihat')
                                                ->nullable(),
                                        ]),
                                ]),
                            
                                ...SKTDocumentLabel::all()->map(function ($label, $index) use ($skt) {
                                    // Fetch feedback for the current SKT and document label
                                    $feedback = SKTDocumentFeedback::where('skt_id', $skt->id)
                                        ->where('skt_document_label_id', $label->id)
                                        ->first();
                                
                                    // Log the feedback object for debugging purposes
                                    Log::info("Feedback data for document label {$label->id}:", ['feedback' => $feedback ? $feedback->toArray() : 'No Feedback Found']);
                                
                                    return Tabs\Tab::make('Syarat ' . ($index + 1))
                                        ->schema([
                                            // PDF Viewer for the document with a fallback if no media is found
                                            PdfViewerField::make($label->collection_name)
                                                ->label($label->label)
                                                ->fileUrl(fn($record) => $record->getFirstMediaUrl($label->collection_name) ?? '') // Default to empty string if null
                                                ->minHeight('50vh'),
                                
                                            // Toggle for verified state
                                            Toggle::make("verified_{$label->id}")
                                                ->label('Verified')
                                                ->default(false) // Set default to false initially
                                                ->reactive()
                                                ->afterStateHydrated(function ($state, callable $set) use ($feedback, $label) {
                                                    // Manually set the state when the form is hydrated
                                                    $set("verified_{$label->id}", $feedback ? $feedback->verified : false);
                                                    Log::info("Hydrated and set state for Verified toggle for {$label->id}:", ['state' => $feedback ? $feedback->verified : 'No Feedback']);
                                                }),
                                
                                            // Feedback textarea, hidden if verified is true
                                            Textarea::make("feedback_{$label->id}")
                                                ->label('Feedback')
                                                ->default('') // Set default to empty string initially
                                                ->placeholder('Provide feedback')
                                                ->hidden(fn($get) => $get("verified_{$label->id}"))
                                                ->reactive()
                                                ->afterStateHydrated(function ($state, callable $set) use ($feedback, $label) {
                                                    // Manually set the state when the form is hydrated
                                                    $set("feedback_{$label->id}", $feedback ? $feedback->feedback : '');
                                                    Log::info("Hydrated and set state for Feedback textarea for {$label->id}:", ['state' => $feedback ? $feedback->feedback : 'No Feedback']);
                                                }),
                                        ]);
                                })->toArray(),
                                
                                    
                            ])
                    ->columnSpanFull(),
            ]);
    }
    public function print($id, Request $request)
    {
        // Ambil data SKL berdasarkan ID
        $skl = SKT::findOrFail($id);
        $konfig = KonfigurasiAplikasi::first();
        $pengesah = DB::table('pegawai')
            ->join('konfigurasi_aplikasi', 'konfigurasi_aplikasi.pengesah_spt_id', '=', 'pegawai.id')
            ->where('konfigurasi_aplikasi.pengesah_spt_id', $konfig->pengesah_spt_id)
            ->select('pegawai.*')
            ->first();

        // Ambil tanggal masa berlaku dari request
        $validityDate = $request->input('validityDate');

        // Logika untuk mencetak atau generate PDF
        $pdf = PDF::loadView('components.pdf.skl', [
            'skl' => $skl,
            'konfig' => $konfig, // Data SKL yang akan digunakan dalam PDF
            'pengesah' => $pengesah, // Data pengesah yang akan dig
            'validityDate' => $validityDate, // Tanggal masa berlaku dari form
        ]);

        // Bisa simpan atau langsung download
        return $pdf->stream('SKL_'.$skl->nama_organisasi.'.pdf');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_ormas')->label('Nama Organisasi')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('jenis_permohonan')->label('Jenis Permohonan')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('sktdocumentFeedbacks')
                    ->label('Perlu Perbaikan')
                    ->formatStateUsing(function ($record) {                    
                        if ($record->status === 'perbaikan') {
                            // Count the number of feedback entries where 'verified' is 0
                            return $record->sktdocumentFeedbacks->where('verified', 0)->count();
                        } else {
                            // If status is not 'perbaikan', return 0
                            return 0;
                        }
                    })
                    ->html(false),
                
                    
                
                
                ])
                
            ->actions([
                Tables\Actions\EditAction::make()->label('Edit')->hiddenLabel()->tooltip('Reviu Pengajuan SKT'),
                Tables\Actions\Action::make('Cetak SKT')->hiddenLabel()->tooltip('Cetak SKT')->icon('heroicon-m-printer')
                    ->action(fn ($record, $data) => redirect()->route('skt.print', [
                        'id' => $record->id,
                        'validityDate' => $data['validityDate'],
                        ]))
                        ->form([
                            Forms\Components\DatePicker::make('validityDate')
                                ->label('Tanggal Masa Berlaku')
                                ->required(),
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pengajuan' => 'Pengajuan',
                        'perbaikan' => 'Perbaikan',
                        'terbit' => 'Terbit',
                    ]),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
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
            'index' => Pages\ListSKTDocumentFeedbacks::route('/'),
            'create' => Pages\CreateSKTDocumentFeedback::route('/create'),
            'edit' => Pages\EditSKTDocumentFeedback::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return SKT::query()->with('sktdocumentFeedbacks'); // Eager-load the related SKL data
    }
}
