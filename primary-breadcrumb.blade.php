@props([
    'title' => '',
    'subtitle' => '',
    'icon' => 'fa-solid fa-map-location-dot',
    'breadcrumbs' => [],
])

<style>
/* Page Header / Breadcrumb */
.smartdash-page-header {
    background: linear-gradient(135deg, #17A2B8 0%, #138496 100%);
    border-radius: 12px;
    padding: 20px 28px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    box-shadow: 0 4px 15px rgba(23, 162, 184, 0.25);
}
.smartdash-page-header .page-title {
    display: flex;
    align-items: center;
    gap: 15px;
}
.smartdash-page-header .page-icon {
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}
.smartdash-page-header .page-title h1 {
    font-size: 26px;
    font-weight: 800;
    margin: 0;
    letter-spacing: 0.5px;
    color: #fff;
}
.smartdash-page-header .page-title p {
    font-size: 13px;
    margin: 4px 0 0 0;
    opacity: 0.9;
}
.smartdash-page-header .page-breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    transform: translateX(-5rem);
}
.smartdash-page-header .page-breadcrumb a {
    color: rgba(255,255,255,0.85);
    text-decoration: none;
    transition: color 0.2s;
}
.smartdash-page-header .page-breadcrumb a:hover {
    color: #fff;
}
.smartdash-page-header .page-breadcrumb .separator {
    opacity: 0.5;
}
.smartdash-page-header .page-breadcrumb .current {
    font-weight: 700;
    color: #fff;
}
.smartdash-page-header .page-actions {
    display: flex;
    gap: 10px;
}
.smartdash-page-header .btn-page-action {
    background: rgba(255,255,255,0.2);
    border: none;
    color: #fff;
    padding: 10px 14px;
    border-radius: 10px;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s;
}
.smartdash-page-header .btn-page-action:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
}
.smartdash-page-header .btn-page-primary {
    background: #fff;
    color: #17A2B8;
    border: none;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.smartdash-page-header .btn-page-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
    color: #0d3d56;
}

@media (max-width: 992px) {
    .smartdash-navbar {
        margin: -15px -15px 20px -15px;
    }
    .smartdash-navbar .navbar-top {
        padding: 10px 15px;
    }
    .smartdash-navbar .navbar-menu {
        padding: 0 10px;
    }
    .smartdash-navbar .nav-link {
        padding: 12px 14px;
        font-size: 13px;
    }
    .smartdash-page-header {
        flex-direction: column;
        text-align: center;
    }
    .smartdash-page-header .page-title {
        flex-direction: column;
    }
    .smartdash-page-header .page-title h1 {
        font-size: 22px;
    }
}
</style>

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
