<?php

namespace Modules\DG2026\Models;

use Illuminate\Database\Eloquent\Model;

class DocgenAuditLog extends Model
{
    protected $table = 'docgen_audit_log';

    protected $fillable = [
        'document_id', 'action', 'action_by', 'action_by_id',
        'details', 'ip_address',
    ];

    public function document()
    {
        return $this->belongsTo(DocgenDocument::class, 'document_id');
    }
}
