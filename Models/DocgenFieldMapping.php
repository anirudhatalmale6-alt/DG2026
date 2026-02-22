<?php

namespace Modules\DG2026\Models;

use Illuminate\Database\Eloquent\Model;

class DocgenFieldMapping extends Model
{
    protected $table = 'docgen_field_mappings';

    protected $fillable = [
        'template_page_id', 'field_name', 'field_label', 'field_source',
        'pos_x', 'pos_y', 'width', 'height',
        'font_family', 'font_size', 'font_style', 'font_color', 'text_align',
        'field_type', 'date_format', 'default_value', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'pos_x' => 'decimal:2',
        'pos_y' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'font_size' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function templatePage()
    {
        return $this->belongsTo(DocgenTemplatePage::class, 'template_page_id');
    }
}
