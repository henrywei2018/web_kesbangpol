<?php

// In app/Models/TemporaryUrl.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TemporaryUrl extends Model
{
    use HasFactory;

    protected $fillable = ['spt_id', 'url', 'expires_at'];

    // Relationship with Spt
    public function spt()
    {
        return $this->belongsTo(Spt::class);
    }

    // Check if the URL is still valid
    public function isValid()
    {
        return Carbon::now()->lessThanOrEqualTo($this->expires_at);
    }
}
