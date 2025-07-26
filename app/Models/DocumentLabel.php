<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentLabel extends Model
{
    use HasFactory;
    protected $table = 'document_labels';

    protected $fillable = [
        'label',          // Human-readable label
        'collection_name', // Collection name for Spatie Media Library
        'required', 
        'tooltip',       // Whether this document is required or not
    ];

    // Relation with SKLDocumentFeedback model
    public function feedback()
    {
        return $this->hasMany(SKLDocumentFeedback::class);
    }
}
