<?php

namespace Modules\DG2026\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocgenTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'docgen_templates';

    protected $fillable = [
        'name', 'code', 'description', 'category', 'is_active', 'sort_order',
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

    public function documents()
    {
        return $this->hasMany(DocgenDocument::class, 'template_id');
    }
}
