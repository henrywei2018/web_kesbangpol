<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;


class WhatsAppDevice extends Model
{
    use HasFactory, InteractsWithMedia;
    
    protected $table = 'whatsapp_devices';

    protected $fillable = [
        'device_id',
        // Tambahkan field lainnya yang dibutuhkan
    ];
}