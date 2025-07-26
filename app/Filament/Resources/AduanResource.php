<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AduanResource\Pages;
use App\Filament\Resources\AduanResource\RelationManagers;
use App\Models\Aduan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;


class AduanResource extends Resource
{
    protected static ?string $model = Aduan::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Layanan';
    
    public static function canViewAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {   
        return $form
            ->schema([
                // Grid untuk membagi layar menjadi 2 kolom
                Forms\Components\Grid::make(5)  // Grid dengan 2 kolom (kiri-kanan)
                    ->schema([
                        // Kolom kiri: Informasi Aduan
                        Forms\Components\Section::make('Informasi Aduan')
                            ->schema([
                                Forms\Components\TextInput::make('ticket')
                                    ->label('Ticket')
                                    ->disabled(),  // Menampilkan ticket aduan

                                Forms\Components\TextInput::make('judul')
                                    ->label('Judul')
                                    ->disabled(),

                                Forms\Components\Select::make('kategori')
                                    ->options([
                                        'Aduan' => 'Aduan',
                                        'Aspirasi' => 'Aspirasi',
                                        'Kritik' => 'Kritik',
                                        'Lainnya' => 'Lainnya',
                                    ])
                                    ->label('Kategori')
                                    ->disabled(),

                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->disabled(),

                                Forms\Components\Select::make('status')
                                    ->options([
                                        'pengajuan' => 'Pengajuan',
                                        'diproses' => 'Diproses',
                                        'selesai' => 'Selesai',
                                    ])
                                    ->label('Status Aduan')
                                    ->disabled(),
                            ])
                            ->columnSpan(2),  // Span satu kolom untuk kolom kiri

                        // Kolom kanan: Daftar Komentar
                        Forms\Components\Section::make('Daftar Pesan')
                            ->schema(function () {
                                // Ambil aduan berdasarkan ticket di URL
                                $aduanId = request()->route('record');
                                $aduan = Aduan::with('komentars.user')->find($aduanId);

                                if (!$aduan) {
                                    return [];
                                }

                                // Mengambil komentar terbaru (pesan terakhir) berdasarkan created_at
                                $komentars = $aduan->komentars->sortBy('created_at');

                                // Mempersiapkan schema untuk setiap komentar
                                return $komentars->map(function ($komentar, $index) use ($komentars) {
                                    // Cek apakah komentar ini adalah yang terbaru (pesan terakhir)
                                    $isLast = $index === $komentars->count() - 1;  // Karena sudah diurutkan descending, index 0 adalah komentar terakhir

                                    return Forms\Components\Section::make($komentar->user->firstname . ' ' . $komentar->user->lastname . ' - ' . $komentar->created_at->format('d M Y H:i'))
                                        ->schema([
                                            Forms\Components\Textarea::make('pesan_' . $komentar->id)  // Menghindari duplikasi field name
                                                ->label('Isi Pesan')
                                                ->disabled()
                                                ->afterStateHydrated(function ($set) use ($komentar) {
                                                    // Mengatur state untuk textarea agar terisi data dari komentar
                                                    $set('pesan_' . $komentar->id, $komentar->pesan);
                                                }),
                                        ])
                                        ->collapsible()  // Jadikan setiap komentar collapseable
                                        ->collapsed(!$isLast);  // Secara default, hanya buka komentar terakhir
                                })->toArray();
                            })
                            ->columnSpan(3),// Span satu kolom untuk kolom kanan
                        ]),
                        Forms\Components\Textarea::make('new_comment')
                        ->disabled(function (callable $get) {
                            // Cek status, jika 'selesai', maka komentar baru dinonaktifkan
                            return $get('status') === 'selesai';
                        })
                        ->columnSpan(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('kategori')->sortable(),
                Tables\Columns\TextColumn::make('status')->sortable(),
                Tables\Columns\TextColumn::make('user.name')->label('Pengguna'),
                Tables\Columns\TextColumn::make('created_at')->date(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pengajuan' => 'Pengajuan',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAduans::route('/'),
            'create' => Pages\CreateAduan::route('/create'),
            'edit' => Pages\EditAduan::route('/{record}/edit'), // Menambahkan halaman untuk komentar
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        // Mendapatkan query asli dari model
        $query = parent::getEloquentQuery();

        // Membatasi query berdasarkan user yang sedang login
        return $query->where('user_id', auth()->user()->id);
    }
}
