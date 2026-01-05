<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SKLResource\Pages;
use App\Filament\Resources\SKLResource\RelationManagers;
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
    public static function getNavigationLabel(): string
    {
        return 'Permohonan SKL';
    }
    protected static ?string $navigationGroup = 'Layanan';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

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

                                        TextInput::make('nama_organisasi')
                                            ->label('Nama Organisasi')
                                            ->required()
                                            ->rules([
                                                'required',
                                                'string',
                                                'max:255',
                                                function ($get) {
                                                    $record = $this->getRecord();
                                                    return new UniqueOrmasNameRule(
                                                        $record ? $record->id : null,
                                                        'skl'
                                                    );
                                                }
                                            ])
                                            ->helperText('Nama organisasi harus unik dan belum terdaftar dalam sistem'),

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
                                            ->required($label->required)
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
                Tables\Actions\ViewAction::make()->hiddenLabel()->tooltip('Detail'),
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Edit'),
                Tables\Actions\DeleteAction::make()->hiddenLabel()->tooltip('Delete'),
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
