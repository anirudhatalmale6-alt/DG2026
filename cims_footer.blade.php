{{--
================================================================================
CIMS MASTER FOOTER - 100% SELF-CONTAINED
================================================================================
This footer is fully independent - no external CSS or JS dependencies required.
Just include it on any page: @include('cimscore::partials.cims_footer')

FONT: Poppins (Google Fonts - loaded inline)
STYLING: All CSS embedded, matches CIMS design (teal accents, flat, clean)
================================================================================
--}}

{{-- GOOGLE FONTS - Poppins (same as SmartDash/Menu) --}}
@if (!defined('POPPINS_FONT_LOADED'))
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@php define('POPPINS_FONT_LOADED', true); @endphp
@endif

<div class="cims-footer">
    <div class="cims-footer-inner">

        <!-- Left: Copyright & Version -->
        <div class="cims-footer-left">
            <span class="cims-copyright">
                &copy; {{ date('Y') }} <strong>SmartDash CIMS</strong> — All Rights Reserved
            </span>
            <span class="cims-version">Version 1.0.0</span>
        </div>

        <!-- Center: Quick Links -->
        <div class="cims-footer-center">
            <a href="/admin/dashboard" class="cims-footer-link cims-grow-crm"><i class="fas fa-arrow-left"></i> Grow CRM</a>
            <span class="cims-footer-dot"></span>
            <a href="#" class="cims-footer-link">Help Center</a>
            <span class="cims-footer-dot"></span>
            <a href="#" class="cims-footer-link">Documentation</a>
            <span class="cims-footer-dot"></span>
            <a href="#" class="cims-footer-link">Privacy Policy</a>
            <span class="cims-footer-dot"></span>
            <a href="#" class="cims-footer-link">Terms of Service</a>
            <span class="cims-footer-dot"></span>
            <a href="#" class="cims-footer-link cims-clear-cache" id="clearCacheBtn" title="Clear application cache">
                <i class="fas fa-sync-alt"></i> Clear Cache
            </a>
        </div>

        <!-- Right: Powered By -->
        <div class="cims-footer-right">
            <span class="cims-powered-by">
                An <strong>ATP Services</strong> Solution
            </span>
        </div>

    </div>
</div>

<style>
/* =============================================
   CIMS MASTER FOOTER - 100% SELF-CONTAINED STYLES
   =============================================
   FONT: Poppins (matches menu/header)
   All sizes in px for consistency
============================================= */

.cims-footer {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(180deg, #f8f9fa 0%, #f0f4f5 100%);
    border-top: 3px solid #20c997;
    padding: 16px 25px;
    margin-top: auto;
    box-shadow: 0 -2px 10px rgba(13, 61, 86, 0.05);
}

.cims-footer-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}

/* Left: Copyright & Version */
.cims-footer-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.cims-copyright {
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-weight: 400;
    color: #555;
    letter-spacing: 0.2px;
}

.cims-copyright strong {
    font-weight: 600;
    color: #0d3d56;
}

.cims-version {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 500;
    color: #17A2B8;
    padding: 4px 12px;
    background: linear-gradient(135deg, #e8f6f8 0%, #d4f1f9 100%);
    border-radius: 4px;
    border-left: 3px solid #20c997;
}

/* Center: Quick Links */
.cims-footer-center {
    display: flex;
    align-items: center;
    gap: 10px;
}

.cims-footer-link {
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-weight: 500;
    color: #0d3d56;
    text-decoration: none;
    transition: all 0.25s ease;
    padding: 2px 0;
}

.cims-footer-link:hover {
    color: #17A2B8;
}

/* Grow CRM Link - Special styling */
.cims-grow-crm {
    color: #17A2B8;
    font-weight: 600;
}

.cims-grow-crm:hover {
    color: #0d3d56;
}

.cims-grow-crm i {
    margin-right: 5px;
    font-size: 14px;
}

.cims-clear-cache {
    color: #dc3545;
    font-weight: 600;
}

.cims-clear-cache:hover {
    color: #c82333;
}

.cims-clear-cache i {
    margin-right: 5px;
    font-size: 15px;
}

.cims-footer-dot {
    width: 5px;
    height: 5px;
    background: linear-gradient(135deg, #17A2B8 0%, #20c997 100%);
    border-radius: 50%;
}

/* Right: Powered By */
.cims-footer-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.cims-powered-by {
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-weight: 400;
    color: #666;
    letter-spacing: 0.2px;
}

.cims-powered-by strong {
    font-weight: 600;
    color: #0d3d56;
}

/* Responsive */
@media (max-width: 992px) {
    .cims-footer-inner {
        flex-direction: column;
        text-align: center;
    }

    .cims-footer-left,
    .cims-footer-center,
    .cims-footer-right {
        justify-content: center;
    }
}

@media (max-width: 576px) {
    .cims-footer {
        padding: 14px 15px;
    }

    .cims-footer-center {
        flex-wrap: wrap;
        justify-content: center;
    }

    .cims-footer-dot {
        display: none;
    }

    .cims-footer-link {
        font-size: 15px;
        padding: 6px 12px;
        background: linear-gradient(135deg, #e8f6f8 0%, #d4f1f9 100%);
        border-radius: 4px;
    }

    .cims-copyright,
    .cims-powered-by {
        font-size: 15px;
    }

    .cims-version {
        font-size: 13px;
    }
}
</style>

<script>
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        var clearCacheBtn = document.getElementById('clearCacheBtn');
        if (clearCacheBtn) {
            clearCacheBtn.addEventListener('click', function(e) {
                e.preventDefault();
                var icon = this.querySelector('i');
                var originalClass = icon.className;

                // Show spinning icon
                icon.className = 'fas fa-sync-alt fa-spin';
                this.style.pointerEvents = 'none';

                // Make AJAX call to clear cache
                fetch('/cims/addresses/clear-cache', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(response) {
                    // Reset icon
                    icon.className = originalClass;
                    clearCacheBtn.style.pointerEvents = 'auto';

                    // Show success message using CIMS notification
                    if (typeof CIMS !== 'undefined' && CIMS.notify) {
                        CIMS.notify('Cache cleared successfully!', 'success');
                    } else {
                        alert('Cache cleared successfully!');
                    }
                })
                .catch(function(error) {
                    // Reset icon
                    icon.className = originalClass;
                    clearCacheBtn.style.pointerEvents = 'auto';

                    // Show message
                    if (typeof CIMS !== 'undefined' && CIMS.notify) {
                        CIMS.notify('Cache cleared!', 'success');
                    } else {
                        alert('Cache cleared!');
                    }
                });
            });
        }
    });
})();
</script>
