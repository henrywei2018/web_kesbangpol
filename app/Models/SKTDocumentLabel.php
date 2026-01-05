<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKTDocumentLabel extends Model
{
    use HasFactory;
    protected $table = 'skt_document_labels';

    protected $fillable = [
        'label',
        'collection_name',
        'tooltip',
        'required',
    ];
    public function feedback()
    {
        return $this->hasMany(SKTDocumentFeedback::class);
    }
}
