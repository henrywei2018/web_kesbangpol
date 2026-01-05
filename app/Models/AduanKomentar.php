<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AduanKomentar extends Model
{
    use HasFactory;

    // Kolom yang bisa diisi melalui mass assignment
    protected $fillable = ['ticket', 'user_id', 'pesan'];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Dapatkan id dari user yang sedang login dan set nilai id_pemohon
        $data['user_id'] = Auth::id(); // atau auth()->user()->id;

        return $data;
    }

    // Relasi ke aduan berdasarkan ticket
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // User pengirim komentar
    }

    public function aduan()
    {
        return $this->belongsTo(Aduan::class, 'ticket', 'ticket');
    }
}
