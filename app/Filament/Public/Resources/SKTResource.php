<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\SKTResource\Pages;
use App\Filament\Public\Resources\SKTResource\RelationManagers;
use App\Models\SKT;
use App\Models\SKTDocumentLabel;
use App\Model\Wilayah;
use App\Models\SKTDocumentFeedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tab;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use App\Rules\UniqueOrmasNameRule;


class SKTResource extends Resource
{
    protected static ?string $slug = 'daftar-ormas';
    protected static ?string $model = SKT::class;

    public static function getNavigationLabel(): string
    {
        return 'Daftar ORMAS';
    }
    protected static ?string $navigationGroup = 'POKUS KALTARA';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {   
        $sktdocumentLabels = SKTDocumentLabel::all();
        $record = $form->getRecord();  // Dapatkan record SKL yang sedang diedit
        $feedbacks = SKTDocumentFeedback::where('skt_id', $record->id ?? null)
            ->get()
            ->keyBy('skt_document_label_id');
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
                                            ->required(),
                                        Select::make('nama_ormas')
                                            ->label('Nama Ormas')
                                            ->required()
                                            ->searchable()
                                            ->allowHtml()
                                            ->getSearchResultsUsing(function (string $search) {
                                                // Cari di tabel ormas_master
                                                $ormasResults = \App\Models\OrmasMaster::where('nama_ormas', 'like', "%{$search}%")
                                                    ->limit(10)
                                                    ->get()
                                                    ->mapWithKeys(function ($ormas) {
                                                        return [$ormas->nama_ormas => sprintf(
                                                            '<div class="flex flex-col"><span class="font-medium text-green-600">%s</span><span class="text-xs text-gray-500">Terdaftar di ORMAS Master</span></div>',
                                                            $ormas->nama_ormas
                                                        )];
                                                    });
                                                
                                                // Tambahkan opsi custom jika pencarian tidak kosong
                                                if (strlen($search) >= 3) {
                                                    $customOption = [
                                                        $search => sprintf(
                                                            '<div class="flex flex-col"><span class="font-medium text-blue-600">%s</span><span class="text-xs text-gray-500">Nama baru (belum terdaftar)</span></div>',
                                                            $search
                                                        )
                                                    ];
                                                    return $customOption + $ormasResults->toArray();
                                                }
                                                
                                                return $ormasResults->toArray();
                                            })
                                            ->getOptionLabelUsing(function ($value) {
                                                // Check if it exists in ormas_master
                                                $ormas = \App\Models\OrmasMaster::where('nama_ormas', $value)->first();
                                                if ($ormas) {
                                                    return sprintf(
                                                        '<div class="flex flex-col"><span class="font-medium text-green-600">%s</span><span class="text-xs text-gray-500">Terdaftar di ORMAS Master</span></div>',
                                                        $value
                                                    );
                                                }
                                                return sprintf(
                                                    '<div class="flex flex-col"><span class="font-medium text-blue-600">%s</span><span class="text-xs text-gray-500">Nama baru (belum terdaftar)</span></div>',
                                                    $value
                                                );
                                            })
                                            ->createOptionUsing(function (string $value) {
                                                // Allow creating new option if it doesn't exist
                                                return $value;
                                            })
                                            ->helperText('Ketik minimal 3 karakter untuk mencari nama organisasi yang sudah terdaftar, atau masukkan nama baru')
                                            ->live()
                                            ->afterStateUpdated(function (?string $state, callable $set) {
                                                if ($state) {
                                                    // Check if organization exists in ormas_master
                                                    $ormas = \App\Models\OrmasMaster::where('nama_ormas', $state)->first();
                                                    if ($ormas) {
                                                        $set('ormas_status', 'existing');
                                                        $set('ormas_info', 'Organisasi ini sudah terdaftar di ORMAS Master');
                                                    } else {
                                                        $set('ormas_status', 'new');
                                                        $set('ormas_info', 'Nama organisasi baru (belum terdaftar)');
                                                    }
                                                } else {
                                                    $set('ormas_status', null);
                                                    $set('ormas_info', null);
                                                }
                                            }),

                                        // Hidden fields untuk tracking status
                                        \Filament\Forms\Components\Hidden::make('ormas_status'),
                                        \Filament\Forms\Components\Hidden::make('ormas_info'),
                                        
                                        // Info placeholder
                                        \Filament\Forms\Components\Placeholder::make('ormas_info_display')
                                            ->label('')
                                            ->content(function (callable $get) {
                                                $info = $get('ormas_info');
                                                $status = $get('ormas_status');
                                                
                                                if (!$info) return '';
                                                
                                                $colorClass = $status === 'existing' ? 'text-green-600 bg-green-50 border-green-200' : 'text-blue-600 bg-blue-50 border-blue-200';
                                                $icon = $status === 'existing' ? 
                                                    '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>' :
                                                    '<svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"></path></svg>';
                                                
                                                return new \Illuminate\Support\HtmlString("<div class='flex items-center p-2 rounded border {$colorClass} text-sm'>{$icon}{$info}</div>");
                                            })
                                            ->visible(fn (callable $get) => !empty($get('ormas_info')))
                                            ->extraAttributes(['class' => 'mt-1']),

                                        TextInput::make('nama_singkatan_ormas')
                                            ->label('Nama Singkatan Ormas')
                                            ->nullable(),
                                        Select::make('tempat_pendirian') 
                                            ->label('Tempat Pendirian')
                                            ->options(\App\Models\Wilayah::where('level', 'kabupaten')->pluck('nama', 'nama'))
                                            ->searchable()
                                            ->nullable(),
                                        DatePicker::make('tanggal_pendirian')
                                            ->label('Tanggal Pendirian')
                                            ->nullable(),
                                        TextInput::make('nomor_surat_permohonan')
                                            ->label('Nomor Surat Permohonan')
                                            ->nullable(),
                                        DatePicker::make('tanggal_surat_permohonan')
                                            ->label('Tanggal Surat Permohonan')
                                            ->nullable()
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
                                            ->nullable()
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
                                Tabs\Tab::make('Upload Dokumen')
                                ->icon('heroicon-s-document-plus')
                                ->schema([
                                    Section::make('Dokumen Syarat')
                                        ->schema(
                                            $sktdocumentLabels->map(function ($label) use ($feedbacks) {
                                                $feedback = $feedbacks->get($label->id);  // Get feedback for the document label
                                                $isVerified = $feedback ? $feedback->verified : false;
                            
                                                $components = [
                                                    // File upload input
                                                    SpatieMediaLibraryFileUpload::make($label->collection_name)
                                                        ->label($label->label)
                                                        ->disk('public')
                                                        ->directory("uploads/skl/{$label->collection_name}")
                                                        ->collection($label->collection_name)
                                                        ->acceptedFileTypes(['application/pdf'])
                                                        ->maxSize(2048)
                                                        ->required($label->required)
                                                        ->hint($label->tooltip)
                                                        ->hidden(fn() => $isVerified),
                                                ];
                            
                                                if ($feedback) {
                                                    // Feedback textarea (read-only)
                                                    $components[] = Textarea::make('feedback_' . $label->collection_name)
                                                        ->label('Feedback for ' . $label->label)
                                                        ->disabled()
                                                        ->afterStateHydrated(function ($state, callable $set) use ($feedback, $label) {
                                                            $set("feedback_{$label->collection_name}", $feedback->feedback);
                                                        })
                                                        ->hidden(fn ($state) => is_null($feedback->feedback) || $feedback->feedback === '');
                            
                                                    // Sanggahan textarea (rebuttal)
                                                    $components[] = Textarea::make('sanggahan_' . $label->collection_name)
                                                        ->label('Sanggahan untuk ' . $label->label)
                                                        ->placeholder('Silahkan melakukan re-upload atau sampaikan sanggahan jika diperlukan...')
                                                        ->afterStateHydrated(function ($state, callable $set) use ($feedback, $label) {
                                                            $set("sanggahan_{$label->collection_name}", $feedback->sanggahan);
                                                        })
                                                        ->hidden(fn ($state) => is_null($feedback->feedback) || $feedback->feedback === '');
                                                }
                            
                                                return $components;  // Return components for this label
                                            })->flatten()->toArray()  // Flatten the array to ensure it's one-dimensional
                                        ),
                                    ]),
                            
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('email')->label('Email Organisasi'),
            TextColumn::make('nama_ormas')->label('Nama Organisasi'),
            TextColumn::make('jenis_permohonan')->label('Jenis Permohonan'),
            TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'primary' => 'pengajuan',
                        'success' => 'terbit',
                        'danger' => 'ditolak',
                        'warning' => 'perbaikan',
                    ]),

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
                ->tooltip('Edit')
                ->visible(fn (SKT $record): bool => in_array($record->status, ['pengajuan', 'perbaikan']) || is_null($record->status)),
            Tables\Actions\DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->label('')
                ->tooltip('Delete')
                ->visible(fn (SKT $record): bool => in_array($record->status, ['pengajuan', 'perbaikan']) || is_null($record->status)),
        ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('Belum Ada Pendaftaran ORMAS')
            ->emptyStateDescription('Laporkan keberadaan atau perubahan status ORMAS di wilayah Provinsi Kalimantan Utara.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Pendaftaran ORMAS')
                    ->icon('heroicon-o-plus'),
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
            'index' => Pages\ListSKTS::route('/'),
            'create' => Pages\CreateSKT::route('/create'),
            'edit' => Pages\EditSKT::route('/{record}/edit'),
        ];
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
