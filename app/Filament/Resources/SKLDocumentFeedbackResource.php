<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SKLDocumentFeedbackResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Checkbox;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use App\Models\SKL;
use App\Models\DocumentLabel;
use App\Models\KonfigurasiAplikasi;
use App\Models\SKLDocumentFeedback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;
use Illuminate\Support\Facades\DB;


class SKLDocumentFeedbackResource extends Resource
{
    protected static ?string $model = SKLDocumentFeedback::class;
    public static function getNavigationLabel(): string
    {
        return 'Reviu SKL';
    }
    protected static ?string $navigationGroup = 'POKUS KALTARA';
    protected static ?string $navigationGroupIcon = 'heroicon-c-tv';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard';
    public static function getSlug(): string
    {
        return 'reviu-skl';
    }

    // Define the form for admin review
    public static function form(Form $form): Form
    {
    $skl = $form->getRecord();
    if (!$skl) {
        return $form->schema([]);
    }

    return $form
        ->schema([
            Tabs::make('SKL Review')
                ->tabs([
                    // Tab 1: Informasi Dasar
                    Tabs\Tab::make('Informasi Dasar')
                        ->schema([
                            Section::make('Informasi Dasar')
                                ->schema([
                                    TextInput::make('jenis_permohonan')
                                        ->label('Jenis Permohonan')
                                        ->disabled(),

                                    TextInput::make('nama_organisasi')
                                        ->label('Nama Organisasi')
                                        ->disabled(),

                                    TextInput::make('email_organisasi')
                                        ->label('Email Organisasi')
                                        ->disabled(),

                                    TextInput::make('nama_ketua')
                                        ->label('Nama Ketua')
                                        ->disabled(),
                                ]),
                        ]),

                    // Generate tabs for each document label
                    ...DocumentLabel::all()->map(function ($label, $index) use ($skl) {
                        // Fetch feedback for the current SKL and document label
                        $feedback = SKLDocumentFeedback::where('skl_id', $skl->id)
                            ->where('document_label_id', $label->id)
                            ->first();

                        return Tabs\Tab::make('Syarat ' . ($index + 1))
                            ->schema([
                                // PDF Viewer for the document
                                PdfViewerField::make($label->collection_name)
                                    ->label($label->label)
                                    ->fileUrl(fn($record) => $record->getFirstMediaUrl($label->collection_name))
                                    ->minHeight('50vh'),

                                // Toggle for verified state
                                Toggle::make("verified_{$label->id}")
                                    ->label('Verified')
                                    ->default(false) // Set default to false initially
                                    ->reactive()
                                    ->afterStateHydrated(function ($state, callable $set) use ($feedback, $label) {
                                        // Set the state when the form is hydrated
                                        $set("verified_{$label->id}", $feedback ? $feedback->verified : false);
                                    }),

                                // Feedback textarea, hidden if verified is true
                                Textarea::make("feedback_{$label->id}")
                                    ->label('Feedback')
                                    ->default('') // Set default to empty string initially
                                    ->placeholder('Provide feedback')
                                    ->hidden(fn($get) => $get("verified_{$label->id}"))
                                    ->reactive()
                                    ->afterStateHydrated(function ($state, callable $set) use ($feedback, $label) {
                                        // Set the state when the form is hydrated
                                        $set("feedback_{$label->id}", $feedback ? $feedback->feedback : '');
                                    }),

                                // Sanggahan textarea, hidden if no feedback available
                                Textarea::make("sanggahan_{$label->id}")
                                    ->label('Sanggahan untuk ' . $label->label)
                                    ->placeholder('Masukkan sanggahan jika diperlukan...')
                                    ->default('')
                                    ->disabled() // Set default to empty string initially
                                    ->afterStateHydrated(function ($state, callable $set) use ($feedback, $label) {
                                        // Set state after form is hydrated
                                        $set("sanggahan_{$label->id}", $feedback ? $feedback->sanggahan : '');
                                    })
                                    ->hidden(fn($state) => is_null($feedback) || is_null($feedback->feedback)), // Hide if feedback is empty
                            ]);
                    })->toArray(),
                ])
                ->columnSpanFull(),
        ]);
    }
    public function print($id, Request $request)
    {
        // Ambil data SKL berdasarkan ID
        $skl = SKL::findOrFail($id);
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
                Tables\Columns\TextColumn::make('nama_organisasi')->label('Nama Organisasi'),
                Tables\Columns\TextColumn::make('jenis_permohonan')->label('Jenis Permohonan'),
                Tables\Columns\TextColumn::make('status')->label('Status'),
                Tables\Columns\TextColumn::make('documentFeedbacks')
                    ->label('Perlu Perbaikan')
                    ->formatStateUsing(function ($record) {                    
                        if ($record->status === 'perbaikan') {
                            // Count the number of feedback entries where 'verified' is 0
                            return $record->documentFeedbacks->where('verified', 0)->count();
                        } else {
                            // If status is not 'perbaikan', return 0
                            return 0;
                        }
                    })
                    ->html(false),
            ])
            ->actions([                
                Tables\Actions\EditAction::make()->hiddenLabel()->tooltip('Reviu Pengajuan SKL'),                
                Tables\Actions\Action::make('Cetak SKL')->hiddenLabel()->tooltip('Cetak SKL')->icon('heroicon-m-printer')
                ->action(fn ($record, $data) => redirect()->route('skl.print', [
                    'id' => $record->id,
                    'validityDate' => $data['validityDate'],
                    ]))
                    ->form([
                        Forms\Components\DatePicker::make('validityDate')
                            ->label('Tanggal Masa Berlaku')
                            ->required(),
                ]),
            ]);
    } 
    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return SKL::query()->with('documentFeedbacks'); // Eager-load the related SKL data
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSKLDocumentFeedback::route('/'),
            'edit' => Pages\EditSKLDocumentFeedback::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

}
