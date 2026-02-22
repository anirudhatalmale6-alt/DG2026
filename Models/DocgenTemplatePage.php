<?php

namespace Modules\DG2026\Models;

use Illuminate\Database\Eloquent\Model;

class DocgenTemplatePage extends Model
{
    protected $table = 'docgen_template_pages';

    protected $fillable = [
        'template_id', 'page_number', 'page_label', 'pdf_path',
        'orientation', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function template()
    {
        return $this->belongsTo(DocgenTemplate::class, 'template_id');
    }

    public function fieldMappings()
    {
        return $this->hasMany(DocgenFieldMapping::class, 'template_page_id')->orderBy('sort_order');
    }

    public function activeFieldMappings()
    {
        return $this->hasMany(DocgenFieldMapping::class, 'template_page_id')->where('is_active', true)->orderBy('sort_order');
    }
}
