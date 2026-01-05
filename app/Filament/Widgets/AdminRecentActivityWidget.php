<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\PermohonanInformasiPublik;
use App\Models\KeberatanInformasiPublik;
use Illuminate\Database\Eloquent\Builder;

class AdminRecentActivityWidget extends BaseWidget
{
    protected static ?string $heading = 'Aktivitas Terkini';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'permohonan' => 'primary',
                        'keberatan' => 'warning',
                        'skt' => 'success',
                        'athg' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('user_name')
                    ->label('Pengguna')
                    ->searchable(),

                Tables\Columns\TextColumn::make('action')
                    ->label('Aksi'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'diproses' => 'info',
                        'selesai' => 'success',
                        'ditolak' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }

    protected function getTableQuery(): Builder
    {
        // Create a union query to get activities from multiple tables
        $permohonanQuery = PermohonanInformasiPublik::select([
            'id',
            'id_pemohon as user_id',
            \DB::raw("'permohonan' as type"),
            \DB::raw("'Permohonan Baru' as action"),
            \DB::raw("'pending' as status"), // You might want to get the actual latest status
            'created_at',
        ]);

        $keberatanQuery = KeberatanInformasiPublik::select([
            'id',
            'id_pemohon as user_id',
            \DB::raw("'keberatan' as type"),
            \DB::raw("'Keberatan Baru' as action"),
            \DB::raw("'pending' as status"),
            'created_at',
        ]);

        // For this example, we'll just use permohonan data
        // In a real implementation, you'd want to use a proper union or a dedicated activity log
        return PermohonanInformasiPublik::query()
            ->with('user')
            ->select([
                'id',
                'id_pemohon',
                'created_at',
                \DB::raw("'permohonan' as type"),
                \DB::raw("'Permohonan Informasi Baru' as action"),
                \DB::raw("'pending' as status"),
            ])
            ->addSelect([
                'user_name' => \DB::table('users')
                    ->select('name')
                    ->whereColumn('id', 'permohonan_informasi_publik.id_pemohon')
                    ->limit(1)
            ]);
    }
}