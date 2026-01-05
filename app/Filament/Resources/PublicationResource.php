<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PublicationResource\Pages;
use App\Filament\Resources\PublicationResource\RelationManagers;
use App\Models\Publication;
use App\Models\PublicationCategory;
use App\Models\PublicationSubcategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Joaopaulolndev\FilamentPdfViewer\Forms\Components\PdfViewerField;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class PublicationResource extends Resource
{
    protected static ?string $model = Publication::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup = 'Publikasi';
    protected static ?string $navigationLabel = 'Dokumen-Publikasi';
    protected static ?int $navigationSort = 6;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([


                Section::make('Informasi Umum')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Judul Publikasi')
                                    ->required()
                                    ->debounce('1200')
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $set('slug', Str::slug($state));
                                    }),
                                TextInput::make('slug')
                                    ->label('Slug')
                                    ->readOnly()
                                    ->required()
                                    ->unique(ignoreRecord: true),
                            ]),

                        Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->nullable(),
                    ])
                    ->collapsible(),
                Section::make('Kategori & Tanggal Publikasi')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('category_id')
                                    ->label('Kategori Utama')
                                    ->relationship('category', 'name') // Load from the relationship
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, $get) {
                                        // Retrieve the selected category's name dynamically
                                        $categoryName = $get('category_id');
                                        // Pre-fill content if "Daftar Informasi" is selected
                                        if ($categoryName === '3') {
                                            $set('content', "
                                                <strong>Ringkasan Isi:</strong> [Isi Ringkasan]
                                                <br><strong>Pejabat/Unit/Satuan Kerja yang Menguasai Informasi:</strong> [Nama Unit]
                                                <br><strong>Penanggung Jawab Pembuatan/Penerbitan Informasi:</strong> [Penanggung Jawab]
                                                <br><strong>Tempat dan Waktu Pembuatan Informasi:</strong> [Tempat dan Waktu]
                                                <br><strong>Bentuk Informasi yang Tersedia:</strong> [Bentuk Informasi]
                                                <br><strong>Jangka Waktu Penyimpanan / Retensi Arsip:</strong> [Jangka Waktu]
                                            ");
                                        } else {
                                            // Clear the content if another category is selected
                                            $set('content', '');
                                        }
                                    }),
                                Select::make('subcategory_id')
                                    ->label('Sub Kategori')
                                    ->options(function (callable $get) {
                                        $categoryId = $get('category_id'); // Get selected category
                                        if ($categoryId) {
                                            return PublicationSubcategory::where('category_id', $categoryId)
                                                ->pluck('name', 'id');
                                        }
                                        return [];
                                    })
                                    ->reactive()
                                    ->nullable()
                                    ->disabled(fn($get) => !$get('category_id'))
                                    ->dehydrated(fn($get) => $get('category_id') !== null),

                                DatePicker::make('publication_date')
                                    ->label('Tanggal Publikasi')
                                    ->required(),
                            ]),
                    ])
                    ->collapsible(),
                Section::make('Konten Publikasi & PDF Upload')
                    ->schema([
                        // Grid to organize content and PDF
                        Grid::make(1)
                            ->schema([
                                RichEditor::make('content')
                                    ->label('Konten')
                                    ->required(),

                                // PDF Upload using Spatie Media Library
                                SpatieMediaLibraryFileUpload::make('pdf')
                                    ->label('Upload PDF')
                                    ->collection(function (callable $get) {
                                        $subcategoryId = $get('subcategory_id');
                                        if ($subcategoryId) {
                                            $subcategory = PublicationSubcategory::find($subcategoryId);
                                            return $subcategory ? str_replace(' ', '_', strtolower($subcategory->name)) : 'default';
                                        }
                                        $categoryId = $get('category_id');
                                        if ($categoryId) {
                                            $category = PublicationCategory::find($categoryId);
                                            return $category ? str_replace(' ', '_', strtolower($category->name)) : 'default';
                                        }
                                        return 'default';
                                    })
                                    ->acceptedFileTypes(['application/pdf'])
                                    ->disk('public')
                                    ->required(),
                            ]),

                        // PDF Viewer for previewing uploaded PDF
                        PdfViewerField::make('pdf_viewer')
                            ->label('Preview PDF')
                            ->fileUrl(function($record) {
                                return $record ? $record->getPdfUrl() : null;
                            })
                            ->minHeight('50vh')
                            ->visible(function($record) {
                                return $record && $record->hasPdf();
                            }),
                    ])
                    ->collapsible(),
                Section::make('Status Publikasi')
                    ->schema([
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                            ])
                            ->required(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Judul'),
                Tables\Columns\TextColumn::make('subcategory.name')->label('Sub Kategori')->toggleable(),
                Tables\Columns\TextColumn::make('category.name')->label('Kategori Utama')->toggleable(),
                Tables\Columns\TextColumn::make('status')->label('Status'),
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
            'index' => Pages\ListPublications::route('/'),
            'create' => Pages\CreatePublication::route('/create'),
            'edit' => Pages\EditPublication::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }
}
