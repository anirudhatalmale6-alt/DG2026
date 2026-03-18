@props([
    'name',
    'id' => null,
    'options' => [],
    'selected' => null,
    'label' => null,
    'placeholder' => 'Select an option',
    'required' => false,
    'liveSearch' => true,
    'size' => 6,
    'class' => '',
])

@php
    $componentId = $id ?? 'tooltip_select_' . uniqid();
@endphp

<div class="mb-3">

    <label for="{{ $componentId }}" class="form-label">
        @if (isset($label))
            {{$label }}
        @else
            {{ Str::headline($id) }}
        @endif
        @if($required)<span class="text-danger">*</span>@endif
    </label>

    <select 
        name="{{ $id }}" 
        id="{{ $componentId }}" 
        class="sd_drop_class {{ $class }}" 
        {{ $required ? 'required' : '' }}
        {{ $attributes }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach($options as $option)
            <option 
                value="{{ $option['value'] ?? $option['id'] }}" 
                data-description="{{ $option['description'] ?? '' }}"
                {{ ($selected == ($option['value'] ?? $option['id'])) ? 'selected' : '' }}
            >
                {{ $option['label'] ?? $option['name'] ?? $option['text'] }}
            </option>
        @endforeach
    </select>
</div>

@once
@push('styles')
<style>
    .sd_tooltip_teal::before{
        display:block;
    }
    .sd_tooltip_teal.show {
        display: block !important;
        animation: sd_tooltip_fadeIn 0.3s ease-in-out;
    }

    .bootstrap-select .dropdown-menu .sd_tooltip_teal {
        white-space: normal;
        width: 200px;
        z-index: 10000;
    }

    .bootstrap-select .dropdown-menu li {
        position: relative;
        z-index: 1;
    }

    .tooltip_reset * {
        color: #fff !important;
    }
    
    .tooltip_reset ul {
        margin-bottom: 10px;
    }
</style>
@endpush
@endonce

@push('scripts')
<script>
$(document).ready(function() {
    var $select = $('#{{ $componentId }}');
    
    // Initialize Bootstrap-Select
    $select.selectpicker({
        liveSearch: {{ $liveSearch ? 'true' : 'false' }},
        size: {{ $size }}
    });
    
    // Attach tooltip handlers
    function attachTooltipHandlers_{{ str_replace('-', '_', $componentId) }}() {
        var $dropdownMenu = $select.parent().find('.dropdown-menu, .inner[role="listbox"]');
        var $optionLinks = $dropdownMenu.find('a[role="option"]');
        
        $optionLinks.off('mouseenter.tooltip_{{ $componentId }} mouseleave.tooltip_{{ $componentId }}');
        
        $optionLinks.on('mouseenter.tooltip_{{ $componentId }}', function() {
            var originalIndex = getOriginalIndex_{{ str_replace('-', '_', $componentId) }}(this);
            if (originalIndex === null) return;
            
            var $option = $select.find('option').eq(originalIndex);
            var description = $option.data('description');
            
            if (description && description.trim()) {
                var rect = this.getBoundingClientRect();
                
                $('body').find('.sd_tooltip_teal[data-tooltip-id="{{ $componentId }}-' + originalIndex + '"]').remove();
                
                var $tooltip = $('<div class="tooltip_reset sd_tooltip_teal show" data-tooltip-id="{{ $componentId }}-' + originalIndex + '"></div>')
                    .html(description)
                    .css({
                        'position': 'fixed',
                        'left': rect.left + 'px',
                        'bottom': (window.innerHeight - rect.top + 5) + 'px',
                        'margin': '0',
                        'white-space': 'normal',
                        'width': '500px',
                        'max-height': '100%',
                        'overflow-y': 'auto',
                        'z-index': '9999',
                        'box-shadow': '0 2px 10px rgba(0,0,0,0.3)',
                        'pointer-events': 'none'
                    });
                
                $('body').append($tooltip);
            }
        })
        .on('mouseleave.tooltip_{{ $componentId }}', function() {
            var originalIndex = getOriginalIndex_{{ str_replace('-', '_', $componentId) }}(this);
            if (originalIndex === null) return;
            $('body').find('.sd_tooltip_teal[data-tooltip-id="{{ $componentId }}-' + originalIndex + '"]').remove();
        });
    }

    function getOriginalIndex_{{ str_replace('-', '_', $componentId) }}(linkEl) {
        var $link = $(linkEl);
        var $li = $link.closest('li');

        var originalIndex = $li.data('originalIndex');
        if (typeof originalIndex !== 'undefined') {
            return parseInt(originalIndex, 10);
        }

        originalIndex = $link.data('originalIndex');
        if (typeof originalIndex !== 'undefined') {
            return parseInt(originalIndex, 10);
        }

        originalIndex = $link.data('index');
        if (typeof originalIndex !== 'undefined') {
            return parseInt(originalIndex, 10);
        }

        var id = $link.attr('id');
        if (id && id.lastIndexOf('-') !== -1) {
            var idIndex = parseInt(id.substring(id.lastIndexOf('-') + 1), 10);
            if (!Number.isNaN(idIndex)) {
                return idIndex;
            }
        }

        var pos = $link.attr('aria-posinset');
        if (pos) {
            var posIndex = parseInt(pos, 10) - 1;
            if (!Number.isNaN(posIndex)) {
                return posIndex;
            }
        }

        return null;
    }
    
    // Listen for when dropdown is shown
    $select.on('shown.bs.select', function() {
        attachTooltipHandlers_{{ str_replace('-', '_', $componentId) }}();
    });
    
    // Also try the click event on the button
    $select.parent().find('.btn').on('click', function() {
        setTimeout(function() {
            attachTooltipHandlers_{{ str_replace('-', '_', $componentId) }}();
        }, 100);
    });
});
</script>
@endpush
