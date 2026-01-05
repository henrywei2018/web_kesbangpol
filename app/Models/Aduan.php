<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Aduan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'telpon',
        'email',
        'judul',
        'kategori',
        'deskripsi',
        'status',
        'ip_address',
        'user_agent',
        'processed_at',
        'ticket'
    ];

    // Define available categories
    public const KATEGORI_LIST = [
        'Aduan' => 'Aduan',
        'Aspirasi' => 'Aspirasi',
        'Kritik' => 'Kritik',
        'Lainnya' => 'Lainnya'
    ];

    // Define available statuses with their display info
    public const STATUS_LIST = [
        'pengajuan' => [
            'label' => 'Pengajuan',
            'icon' => 'fa-file-alt',
            'color' => 'warning',
            'description' => 'Aduan telah diterima dan sedang menunggu verifikasi'
        ],
        'proses' => [
            'label' => 'Sedang Diproses',
            'icon' => 'fa-sync',
            'color' => 'info',
            'description' => 'Aduan sedang dalam proses penanganan'
        ],
        'selesai' => [
            'label' => 'Selesai',
            'icon' => 'fa-check-circle',
            'color' => 'success',
            'description' => 'Aduan telah selesai ditangani'
        ],
        'ditolak' => [
            'label' => 'Ditolak',
            'icon' => 'fa-times-circle',
            'color' => 'danger',
            'description' => 'Aduan tidak dapat diproses'
        ]
    ];

    // Get status information
    public function getStatusInfo(): array
    {
        return self::STATUS_LIST[$this->status] ?? [
            'label' => $this->status,
            'icon' => 'fa-question-circle',
            'color' => 'secondary',
            'description' => 'Status tidak diketahui'
        ];
    }

    // Get timeline data
    public function getTimeline(): array
    {
        $timeline = [
            [
                'date' => $this->created_at,
                'status' => 'pengajuan',
                'info' => self::STATUS_LIST['pengajuan']
            ]
        ];

        if ($this->status !== 'pengajuan') {
            $timeline[] = [
                'date' => $this->updated_at,
                'status' => $this->status,
                'info' => self::STATUS_LIST[$this->status]
            ];
        }

        return $timeline;
    }
}