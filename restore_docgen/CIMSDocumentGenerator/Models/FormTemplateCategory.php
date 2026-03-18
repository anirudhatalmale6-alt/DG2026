<?php

namespace Modules\CIMSDocumentGenerator\Models;

use Illuminate\Database\Eloquent\Model;

class FormTemplateCategory extends Model
{
    protected $table = 'cims_form_template_categories';

    protected $fillable = [
        'category_name', 'category_code', 'description',
        'sort_order', 'is_active', 'is_deleted',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->where('is_deleted', 0);
    }

    public function templates()
    {
        return $this->hasMany(DocgenTemplate::class, 'category_id');
    }
}
