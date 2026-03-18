@props([
    'title' => '',
    'subtitle' => '',
    'icon' => 'fa-solid fa-map-location-dot',
    'breadcrumbs' => [],
])

{{-- CSS now managed centrally in cims_master_system_settings.css --}}

<div {{ $attributes->merge(['class' => 'smartdash-page-header mb-4']) }}>
    <div class="page-title">
        <div class="page-icon">
            <i class="{{ $icon }}"></i>
        </div>
        <div>
            <h1>{{ $title }}</h1>
            @if($subtitle !== '')
                <p>{{ $subtitle }}</p>
            @endif
        </div>
    </div>

    @if(!empty($breadcrumbs))
        <div class="page-breadcrumb">
            @foreach($breadcrumbs as $index => $crumb)
                @if($index > 0)
                    <span class="separator">/</span>
                @endif
                @if(!empty($crumb['url']))
                    <a href="{{ $crumb['url'] }}">{!! $crumb['label'] !!}</a>
                @else
                    <span class="current">{!! $crumb['label'] !!}</span>
                @endif
            @endforeach
        </div>
    @endif

    @if(isset($actions))
        <div class="page-actions">
            {{ $actions }}
        </div>
    @endif
</div>
