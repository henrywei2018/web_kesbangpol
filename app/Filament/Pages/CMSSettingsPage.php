<?php

namespace App\Filament\Pages;

use App\Settings\CMSSettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Illuminate\Support\Str;

class CMSSettingsPage extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Kelola Konten';

    protected static ?string $title = 'Kelola Konten Website';

    protected static ?string $navigationGroup = 'Settings';

    protected static string $settings = CMSSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('cms_pages')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Profil Organisasi')
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('profil_title')
                                            ->label('Judul Halaman')
                                            ->required(),

                                        Forms\Components\TextInput::make('profil_subtitle')
                                            ->label('Subtitle')
                                            ->required()
                                            ->placeholder('e.g., Badan Kesatuan Bangsa dan Politik'),

                                        Forms\Components\Textarea::make('profil_description')
                                            ->label('Deskripsi Organisasi')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\Repeater::make('profil_features')
                                            ->label('Daftar Tugas/Fitur Utama')
                                            ->schema([
                                                Forms\Components\Textarea::make('feature')
                                                    ->label('Tugas/Fitur')
                                                    ->required()
                                                    ->rows(2)
                                                    ->placeholder('e.g., Merumuskan kebijakan teknis...'),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => Str::limit($state['feature'] ?? '', 60))
                                            ->addActionLabel('Tambah Tugas/Fitur')
                                            ->columnSpanFull()
                                            ->minItems(1)
                                            ->collapsible(),

                                        Forms\Components\FileUpload::make('profil_image')
                                            ->label('Gambar Profil')
                                            ->image()
                                            ->directory('cms/profil')
                                            ->visibility('public')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Visi & Misi')
                            ->icon('heroicon-o-eye')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('visi_misi_title')
                                            ->label('Judul Halaman')
                                            ->required(),

                                        Forms\Components\Textarea::make('visi_content')
                                            ->label('Visi')
                                            ->required()
                                            ->rows(3)
                                            ->placeholder('e.g., TERWUJUDNYA PROVINSI KALIMANTAN UTARA YANG BERUBAH, MAJU DAN SEJAHTERA')
                                            ->columnSpanFull(),

                                        Forms\Components\Repeater::make('misi_items')
                                            ->label('Daftar Misi')
                                            ->schema([
                                                Forms\Components\Textarea::make('misi')
                                                    ->label('Misi')
                                                    ->required()
                                                    ->rows(3)
                                                    ->placeholder('e.g., Mewujudkan Kalimantan Utara...'),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => 'Misi: ' . Str::limit($state['misi'] ?? '', 50))
                                            ->addActionLabel('Tambah Misi')
                                            ->columnSpanFull()
                                            ->minItems(1)
                                            ->collapsible()
                                            ->reorderable(),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Tugas & Fungsi')
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('tugas_fungsi_title')
                                            ->label('Judul Halaman')
                                            ->required(),

                                        Forms\Components\Textarea::make('tugas_description')
                                            ->label('Deskripsi Tugas Pokok')
                                            ->required()
                                            ->rows(4)
                                            ->columnSpanFull(),

                                        Forms\Components\Repeater::make('fungsi_items')
                                            ->label('Daftar Fungsi')
                                            ->schema([
                                                Forms\Components\Textarea::make('fungsi')
                                                    ->label('Fungsi')
                                                    ->required()
                                                    ->rows(3)
                                                    ->placeholder('e.g., Perumusan kebijakan teknis...'),
                                            ])
                                            ->itemLabel(fn (array $state): ?string => 'Fungsi: ' . Str::limit($state['fungsi'] ?? '', 50))
                                            ->addActionLabel('Tambah Fungsi')
                                            ->columnSpanFull()
                                            ->minItems(1)
                                            ->collapsible()
                                            ->reorderable(),

                                        Forms\Components\FileUpload::make('tugas_fungsi_image')
                                            ->label('Gambar Tugas & Fungsi')
                                            ->image()
                                            ->directory('cms/tugas-fungsi')
                                            ->visibility('public')
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Forms\Components\Tabs\Tab::make('Struktur Organisasi')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Forms\Components\Section::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('struktur_organisasi_title')
                                            ->label('Judul Halaman')
                                            ->required(),

                                        Forms\Components\Textarea::make('struktur_description')
                                            ->label('Deskripsi')
                                            ->required()
                                            ->rows(3)
                                            ->columnSpanFull(),

                                        Forms\Components\FileUpload::make('struktur_chart_image')
                                            ->label('Bagan Struktur Organisasi')
                                            ->image()
                                            ->directory('cms/struktur')
                                            ->visibility('public')
                                            ->helperText('Upload gambar bagan struktur organisasi')
                                            ->acceptedFileTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'])
                                            ->maxSize(5120) // 5MB
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }
}