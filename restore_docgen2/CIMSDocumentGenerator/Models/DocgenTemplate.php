<?php

namespace Modules\CIMSDocumentGenerator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocgenTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'docgen_templates';

    protected $fillable = [
        'name', 'code', 'description', 'category', 'category_id', 'is_active', 'sort_order',
        'created_by', 'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function pages()
    {
        return $this->hasMany(DocgenTemplatePage::class, 'template_id')->orderBy('sort_order');
    }

    public function activePages()
    {
        return $this->hasMany(DocgenTemplatePage::class, 'template_id')->where('is_active', true)->orderBy('sort_order');
    }

    public function category()
    {
        return $this->belongsTo(FormTemplateCategory::class, 'category_id');
    }

    public function documents()
    {
        return $this->hasMany(DocgenDocument::class, 'template_id');
    }
}
