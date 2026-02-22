<?php

namespace Modules\DG2026\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocgenDocument extends Model
{
    use SoftDeletes;

    protected $table = 'docgen_documents';

    protected $fillable = [
        'template_id', 'client_id', 'client_code', 'client_name',
        'document_name', 'document_number', 'file_path', 'file_name', 'file_size',
        'requested_by', 'prepared_by', 'approved_by', 'signed_by',
        'document_date', 'notes', 'status',
        'emailed', 'emailed_to', 'emailed_at', 'generated_by',
    ];

    protected $casts = [
        'document_date' => 'date',
        'emailed' => 'boolean',
        'emailed_at' => 'datetime',
    ];

    public function template()
    {
        return $this->belongsTo(DocgenTemplate::class, 'template_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(DocgenAuditLog::class, 'document_id')->orderBy('created_at', 'desc');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }
}
