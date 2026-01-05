<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KontakKami extends Model
{
    use HasFactory;
    
    protected $table = 'kontak_kami';

    protected $fillable = ['name', 'email', 'phone_number', 'message', 'status'];
}
