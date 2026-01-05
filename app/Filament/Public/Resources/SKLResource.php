<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\SKLResource\Pages;
use App\Filament\Public\Resources\SKLResource\RelationManagers;
use App\Models\SKL;
use App\Models\DocumentLabel;
use App\Models\SKLDocumentFeedback;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\components\modals\help;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Rules\UniqueOrmasNameRule;


class SKLResource extends Resource
{
    protected static ?string $model = SKL::class;
    protected static ?string $slug = 'lapor-ormas';
    public static function getNavigationLabel(): string
    {
        return 'Lapor ORMAS';
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
        // Mengambil label dokumen dinamis
        $documentLabels = DocumentLabel::all();

        // Mengambil feedback yang relevan berdasarkan skl_id
        $record = $form->getRecord();  // Dapatkan record SKL yang sedang diedit
        $feedbacks = SKLDocumentFeedback::where('skl_id', $record->id ?? null)
            ->get()
            ->keyBy('document_label_id');  // Mengelompokkan berdasarkan document_label_id

        return $form
            ->schema([
                Tabs::make('Tabs')
                    ->tabs([
                        Tabs\Tab::make('Informasi Dasar')
                            ->icon('heroicon-c-pencil-square')
                            ->schema([
                                Section::make('Informasi Dasar')
                                    ->schema([
                                        TextInput::make('email_organisasi')
                                            ->label('Email Organisasi')
                                            ->suffixAction(
                                                \Filament\Forms\Components\Actions\Action::make('help')
                                                    ->icon('heroicon-o-question-mark-circle')
                                                    ->tooltip('This is a detailed explanation about the title field.')
                                                    ->action(function () {
                                                        // Logika custom saat tombol "help" diklik
                                                    })
                                            )
                                            ->required(),

                                        Select::make('jenis_permohonan')
                                            ->label('Jenis Permohonan')
                                            ->options([
                                                'Pelaporan Keberadaan Ormas' => 'Pelaporan Keberadaan Ormas',
                                                'Perubahan/Perpanjangan SKL Ormas' => 'Perubahan/Perpanjangan SKL Ormas',
                                            ])
                                            ->required(),

                                        Select::make('nama_organisasi')
                                            ->label('Nama Organisasi')
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

                                        TextInput::make('nama_ketua')
                                            ->label('Nama Ketua')
                                            ->required(),

                                        TextInput::make('nomor_hp')
                                            ->label('Nomor HP Pengurus')
                                            ->required(),
                                    ]),
                            ]),

                        Tabs\Tab::make('Upload Dokumen')
                            ->icon('heroicon-s-document-plus')
                            ->schema(
                                $documentLabels->map(function ($label) use ($feedbacks) {
                                    $feedback = $feedbacks->get($label->id);  // Ambil feedback untuk label dokumen ini
                                    $isVerified = $feedback ? $feedback->verified : false;
                                    // Buat array untuk elemen form
                                    $components = [
                                        // Form untuk upload dokumen
                                        SpatieMediaLibraryFileUpload::make($label->collection_name)
                                            ->label($label->label)
                                            ->disk('public')
                                            ->directory("uploads/skl/{$label->collection_name}")
                                            ->collection($label->collection_name)
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->maxSize(2048)
                                            // ->required($label->required)
                                            ->hint($label->tooltip)
                                            ->hidden(fn() => $isVerified),
                                    ];

                                    // Jika ada feedback, tambahkan Textarea feedback
                                    if ($feedback) {
                                        $components[] = Textarea::make('feedback_' . $label->collection_name)
                                            ->label('Feedback for ' . $label->label)
                                            ->disabled()  // Feedback tidak bisa diubah oleh user
                                            ->afterStateHydrated(function ($state, callable $set) use ($feedback, $label) {
                                                // Set state setelah form di-hydrate
                                                $set("feedback_{$label->collection_name}", $feedback->feedback);
                                            })
                                            ->hidden(fn ($state) => is_null($feedback->feedback) || $feedback->feedback === '');  // Sembunyikan jika feedback kosong

                                        // Tambahkan Textarea untuk sanggahan (user bisa menambahkan sanggahan)
                                        $components[] = Textarea::make('sanggahan_' . $label->collection_name)
                                            ->label('Sanggahan untuk ' . $label->label)
                                            ->placeholder('Silahkan melakukan re-upload atau sampaikan sanggahan jika diperlukan...')
                                            ->afterStateHydrated(function ($state, callable $set) use ($feedback, $label) {
                                                // Set state setelah form di-hydrate
                                                $set("sanggahan_{$label->collection_name}", $feedback->sanggahan);
                                            })                                            
                                            ->hidden(fn ($state) => is_null($feedback->feedback) || $feedback->feedback === '');  // Sembunyikan jika sanggahan kosong
                                    }

                                    return $components;  // Mengembalikan array komponen yang valid
                                })->flatten()->toArray()  // Pastikan semua elemen form di-flatten menjadi array satu dimensi
                            ),
                    ])
                    ->columnSpanFull(),
            ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email_organisasi')->label('Email Organisasi'),
                TextColumn::make('nama_organisasi')->label('Nama Organisasi'),
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
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->label('')
                    ->tooltip('Detail'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->label('')
                    ->tooltip('Edit')
                    ->visible(fn (SKL $record): bool => in_array($record->status, ['pengajuan', 'perbaikan']) || is_null($record->status)),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('')
                    ->tooltip('Delete')
                    ->visible(fn (SKL $record): bool => in_array($record->status, ['pengajuan', 'perbaikan']) || is_null($record->status)),
            ])
            ->emptyStateHeading('Belum Ada Pelaporan ORMAS')
            ->emptyStateDescription('Laporkan keberadaan atau perubahan status ORMAS di wilayah Provinsi Kalimantan Utara.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Buat Pelaporan ORMAS')
                    ->icon('heroicon-o-plus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSKLs::route('/'),
            'create' => Pages\CreateSKL::route('/create'),
            'edit' => Pages\EditSKL::route('/{record}/edit'),
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
