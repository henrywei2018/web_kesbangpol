<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKTDocumentFeedback extends Model
{
    use HasFactory;
    protected $table = 'skt_document_feedback';
    protected $fillable = [
        'skt_id',
        'skt_document_label_id',
        'verified',
        'feedback',
        'sanggahan',
    ];
    protected $casts = [
        'verified' => 'boolean',
    ];

       
    public function skt()
    {
        return $this->belongsTo(SKT::class, 'skt_id');
    }

    // Relation with the DocumentLabel model
    public function sktdocumentLabel()
    {
        return $this->belongsTo(SKTDocumentLabel::class, 'skt_document_label_id', 'id');
    }
}
