<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKLDocumentFeedback extends Model
{
    use HasFactory;
    protected $table = 'skl_document_feedback';
    protected $fillable = [
        'skl_id',
        'document_label_id',
        'verified',
        'feedback',
        'sanggahan',
    ];
    protected $casts = [
        'verified' => 'boolean',
        
    ];

       
    public function skl()
    {
        return $this->belongsTo(SKL::class, 'skl_id');
    }
    public function pengesah()
    {
        return $this->belongsTo(KonfigurasiAplikasi::class, 'pengesah_spt_id');
    }

    // Relation with the DocumentLabel model
    public function documentLabel()
    {
        return $this->belongsTo(DocumentLabel::class, 'document_label_id', 'id');
    }

}
