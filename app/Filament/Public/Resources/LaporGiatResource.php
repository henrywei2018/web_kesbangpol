<?php

namespace App\Filament\Public\Resources;

use App\Filament\Public\Resources\LaporGiatResource\Pages;
use App\Models\LaporGiat;
use App\Models\SKL;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LaporGiatResource extends Resource
{
    protected static ?string $model = LaporGiat::class;
    protected static ?string $navigationGroup = 'POKUS KALTARA';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Lapor Giat';

    protected static ?string $modelLabel = 'Laporan Kegiatan';

    protected static ?string $pluralModelLabel = 'Laporan Kegiatan';

    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Organisasi')
                    ->description('Data organisasi akan diambil dari SKL yang telah disetujui')
                    ->schema([
                        Forms\Components\Select::make('skt_selection')
                            ->label('Pilih Organisasi (SKL)')
                            ->placeholder('Pilih organisasi dari SKL')
                            ->options(function () {
                                return SKL::where('id_pemohon', Auth::id())
                                    ->pluck('nama_organisasi', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $skl = SKL::find($state);
                                    if ($skl) {
                                        $set('nama_ormas', $skl->nama_organisasi);
                                        $set('ketua_nama_lengkap', $skl->nama_ketua);
                                        $set('nomor_handphone', $skl->nomor_hp ?? $skl->user->no_telepon);
                                    }
                                }
                            })
                            ->dehydrated(false), // Don't save this field to database

                        Forms\Components\TextInput::make('nama_ormas')
                            ->label('Nama Organisasi')
                            ->required()
                            ->disabled()
                            ->dehydrated(), // Save to database

                        Forms\Components\TextInput::make('ketua_nama_lengkap')
                            ->label('Nama Lengkap Ketua')
                            ->required()
                            ->disabled()
                            ->dehydrated(),

                        Forms\Components\TextInput::make('nomor_handphone')
                            ->label('Nomor Handphone')
                            ->tel()
                            ->required()
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Kegiatan')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_kegiatan')
                            ->label('Tanggal Kegiatan')
                            ->required()
                            ->maxDate(now())
                            ->displayFormat('d/m/Y')
                            ->native(false),

                        Forms\Components\FileUpload::make('laporan_kegiatan_path')
                            ->label('Upload Laporan Kegiatan (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240) // 10MB
                            ->directory('laporan-kegiatan')
                            ->visibility('private')
                            ->required()
                            ->helperText('Format: PDF, Maksimal 10MB'),

                        Forms\Components\FileUpload::make('images_paths')
                            ->label('Upload Foto Kegiatan')
                            ->image()
                            ->multiple()
                            ->maxFiles(10)
                            ->maxSize(5120) // 5MB per file
                            ->directory('foto-kegiatan')
                            ->visibility('private')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->helperText('Format: JPG, PNG, JPEG. Maksimal 10 foto, 5MB per foto')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // Hidden field for user_id
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_ormas')
                    ->label('Nama Organisasi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ketua_nama_lengkap')
                    ->label('Nama Ketua')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tanggal_kegiatan')
                    ->label('Tanggal Kegiatan')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => LaporGiat::STATUS_OPTIONS[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Pengajuan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(LaporGiat::STATUS_OPTIONS),

                Tables\Filters\Filter::make('tanggal_kegiatan')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_kegiatan', '>=', $date),
                            )
                            ->when(
                                $data['sampai_tanggal'],
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_kegiatan', '<=', $date),
                            );
                    }),
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
                    ->visible(fn (LaporGiat $record) => $record->status === LaporGiat::STATUS_PENDING),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label('')
                    ->tooltip('Delete')
                    ->visible(fn (LaporGiat $record) => $record->status === LaporGiat::STATUS_PENDING),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->can('delete_any_lapor_giat')),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                // Only show records for the current user
                return $query->forUser();
            });
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Organisasi')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama_ormas')
                            ->label('Nama Organisasi'),
                        Infolists\Components\TextEntry::make('ketua_nama_lengkap')
                            ->label('Nama Lengkap Ketua'),
                        Infolists\Components\TextEntry::make('nomor_handphone')
                            ->label('Nomor Handphone'),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('Detail Kegiatan')
                    ->schema([
                        Infolists\Components\TextEntry::make('tanggal_kegiatan')
                            ->label('Tanggal Kegiatan')
                            ->date('d F Y'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'pending' => 'warning',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                default => 'secondary',
                            })
                            ->formatStateUsing(fn ($state) => LaporGiat::STATUS_OPTIONS[$state] ?? $state),

                        Infolists\Components\TextEntry::make('keterangan')
                            ->label('Keterangan')
                            ->visible(fn ($record) => !empty($record->keterangan))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make('File & Foto Kegiatan')
                    ->icon('heroicon-o-folder-open')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                // LEFT COLUMN: PDF Section
                                Infolists\Components\TextEntry::make('pdf_section')
                                    ->label('Laporan PDF')
                                    ->html()
                                    ->getStateUsing(function ($record) {
                                        if (!$record->laporan_kegiatan_path) {
                                            return '<div class="space-y-3">
                                                <div class="flex items-center p-3 bg-red-50 border border-red-200 rounded-md">
                                                    <svg class="w-5 h-5 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-red-700 text-sm">Tidak ada file laporan</span>
                                                </div>
                                            </div>';
                                        } else {
                                            return '<div class="space-y-3">
                                                <div class="flex items-center p-3 bg-green-50 border border-green-200 rounded-md">
                                                    <svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <div class="flex-grow">
                                                        <span class="text-green-700 text-sm font-medium">File PDF tersedia</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 gap-2">
                                                    <a href="' . route('lapor-giat.view-laporan', ['laporGiat' => $record->id]) . '" 
                                                       target="_blank"
                                                       class="inline-flex items-center justify-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                        </svg>
                                                        Preview
                                                    </a>
                                                    <a href="' . route('lapor-giat.download-laporan', ['laporGiat' => $record->id]) . '" 
                                                       target="_blank"
                                                       class="inline-flex items-center justify-center px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        Download
                                                    </a>
                                                </div>
                                            </div>';
                                        }
                                    }),
                                
                                // RIGHT COLUMN: Images Section
                                Infolists\Components\TextEntry::make('image_section')
                                    ->label('Foto Kegiatan')
                                    ->html()
                                    ->getStateUsing(function ($record) {
                                        if (!$record->images_paths || empty($record->images_paths)) {
                                            return '<div class="space-y-3">
                                                <div class="flex items-center p-3 bg-gray-50 border border-gray-200 rounded-md">
                                                    <svg class="w-5 h-5 text-gray-400 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-gray-600 text-sm">Tidak ada foto</span>
                                                </div>
                                            </div>';
                                        }
                                        
                                        $imageCount = count($record->images_paths);
                                        
                                        return '<div class="space-y-3">
                                            <div class="flex items-center p-3 bg-blue-50 border border-blue-200 rounded-md">
                                                <svg class="w-5 h-5 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <div class="flex-grow">
                                                    <span class="text-blue-700 text-sm font-medium">' . $imageCount . ' foto tersedia</span>
                                                </div>
                                            </div>
                                            
                                            <div class="grid grid-cols-1">
                                                <a href="' . route('lapor-giat.download-all-images', ['laporGiat' => $record->id]) . '" 
                                                   target="_blank"
                                                   class="inline-flex items-center justify-center px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition-colors duration-200">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Download ZIP
                                                </a>
                                            </div>
                                        </div>';
                                    })
                                    ->visible(fn ($record) => !empty($record->images_paths) || empty($record->images_paths)),
                            ]),
                    ]),

                Infolists\Components\Section::make('Informasi Pengajuan')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')
                            ->label('Tanggal Pengajuan')
                            ->dateTime('d F Y, H:i'),
                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime('d F Y, H:i'),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListLaporGiats::route('/'),
            'create' => Pages\CreateLaporGiat::route('/create'),
            'view' => Pages\ViewLaporGiat::route('/{record}'),
            'edit' => Pages\EditLaporGiat::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user'])
            ->forUser(); // Automatically filter for current user
    }
}