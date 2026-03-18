<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>CIMS | @yield('title', 'Welcome')</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- MetisMenu (for dropdown menus) -->
    <link href="/public/smartdash/vendor/metismenu/css/metisMenu.min.css" rel="stylesheet">

    <!-- CIMS Core Styles -->
    <link href="/public/modules/cimscore/css/cims.css" rel="stylesheet">

    <style>
        /* ============================================
           FIXED HEADER/FOOTER WITH SCROLLABLE BODY
           ============================================ */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Prevent body scroll */
        }

        /* Main wrapper - full viewport height flex container */
        .cims-page-wrapper {
            height: 100vh;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Fixed Header Section (header + menu) */
        .cims-fixed-header {
            flex-shrink: 0; /* Don't shrink */
            position: relative;
            z-index: 1000;
        }

        /* Scrollable Main Content Area */
        .cims-main-content {
            flex: 1; /* Take remaining space */
            overflow-y: auto; /* Enable vertical scroll */
            overflow-x: hidden;
            padding: 20px;
            background: #f4f6f9;
        }

        /* Custom scrollbar for main content */
        .cims-main-content::-webkit-scrollbar {
            width: 8px;
        }
        .cims-main-content::-webkit-scrollbar-track {
            background: #e0e0e0;
            border-radius: 4px;
        }
        .cims-main-content::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #17A2B8 0%, #0d3d56 100%);
            border-radius: 4px;
        }
        .cims-main-content::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #20c997 0%, #17A2B8 100%);
        }

        /* Fixed Footer Section */
        .cims-fixed-footer {
            flex-shrink: 0; /* Don't shrink */
            position: relative;
            z-index: 1000;
        }

        /* CRITICAL: Desktop - show horizontal menu, hide sidebar */
        .cims-horizontal-menu {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        .cims-sidebar-menu {
            display: none !important;
        }

        /* Force dlabnav menu to show */
        .cims-horizontal-menu .dlabnav,
        .cims-horizontal-menu .cims-menu-wrapper {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: relative !important;
            width: 100% !important;
            left: 0 !important;
            transform: none !important;
        }

        /* Mobile: flip visibility */
        @media (max-width: 991px) {
            .cims-horizontal-menu {
                display: none !important;
            }
            .cims-sidebar-menu {
                display: block !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body data-layout="horizontal">

    <div class="cims-page-wrapper">

        {{-- FIXED HEADER SECTION (Header + Menu) --}}
        <div class="cims-fixed-header">
            {{--
                HEADER PARTIAL (Optional)
                Override with @section('header') to customize or hide
                Use @section('header', '') to remove header completely
            --}}
            @hasSection('header')
                @yield('header')
            @else
                @if(!isset($hideHeader) || !$hideHeader)
                    @include('cimscore::partials.cims_header')
                @endif
            @endif

            {{--
                MENU PARTIALS (Optional)
                Both horizontal and sidebar menus included by default
                Override with @section('menu') to customize
            --}}
            @hasSection('menu')
                @yield('menu')
            @else
                @if(!isset($hideMenu) || !$hideMenu)
                    <!-- Horizontal Menu (desktop) - 3 level -->
                    <div class="cims-horizontal-menu">
                        @include('cimscore::partials.cims_menu_horizontal')
                    </div>

                    <!-- Sidebar Menu (mobile/zoomed) - 2 level -->
                    <div class="cims-sidebar-menu">
                        @include('cimscore::partials.cims_menu_sidebar')
                    </div>
                @endif
            @endif
        </div>

        {{-- SCROLLABLE MAIN CONTENT --}}
        <div class="cims-main-content">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        {{-- FIXED FOOTER SECTION --}}
        <div class="cims-fixed-footer">
            {{--
                FOOTER PARTIAL (Optional)
                Override with @section('footer') to customize or hide
                Use @section('footer', '') to remove footer completely
            --}}
            @hasSection('footer')
                @yield('footer')
            @else
                @if(!isset($hideFooter) || !$hideFooter)
                    @include('cimscore::partials.cims_footer')
                @endif
            @endif
        </div>

    </div>

    <!-- Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/smartdash/vendor/metismenu/js/metisMenu.min.js"></script>

    <script>
        // Initialize MetisMenu for dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof metisMenu !== 'undefined') {
                new metisMenu('#menu');
            }
        });

        /**
         * CIMS Notification System
         * Usage: CIMS.notify('Message here', 'success');
         * Types: success, error, warning, info
         */
        var CIMS = CIMS || {};
        CIMS.notify = function(message, type, duration) {
            type = type || 'success';
            duration = duration || 3000;

            // Remove any existing notification
            var existing = document.querySelector('.cims-notify');
            if (existing) existing.remove();

            // Icon and colors based on type
            var icons = {
                success: 'fa-check-circle',
                error: 'fa-times-circle',
                warning: 'fa-exclamation-triangle',
                info: 'fa-info-circle'
            };
            var colors = {
                success: '#17A2B8',
                error: '#dc3545',
                warning: '#ffc107',
                info: '#0d3d56'
            };

            // Create notification element
            var notify = document.createElement('div');
            notify.className = 'cims-notify cims-notify-' + type;
            notify.innerHTML = '<i class="fas ' + icons[type] + '"></i> <span>' + message + '</span>';
            notify.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;padding:15px 25px;border-radius:6px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,0.15);display:flex;align-items:center;gap:12px;font-size:14px;font-weight:500;color:#333;border-left:4px solid ' + colors[type] + ';animation:cimsSlideIn 0.3s ease;';
            notify.querySelector('i').style.cssText = 'font-size:20px;color:' + colors[type] + ';';

            // Add animation keyframes if not exists
            if (!document.getElementById('cims-notify-styles')) {
                var style = document.createElement('style');
                style.id = 'cims-notify-styles';
                style.textContent = '@keyframes cimsSlideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}@keyframes cimsSlideOut{from{transform:translateX(0);opacity:1}to{transform:translateX(100%);opacity:0}}';
                document.head.appendChild(style);
            }

            document.body.appendChild(notify);

            // Auto remove after duration
            setTimeout(function() {
                notify.style.animation = 'cimsSlideOut 0.3s ease forwards';
                setTimeout(function() { notify.remove(); }, 300);
            }, duration);
        };
    </script>

    @stack('scripts')
</body>
</html>
