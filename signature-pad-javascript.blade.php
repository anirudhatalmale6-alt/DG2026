@props([
    'name' => 'signature_data',
    'value' => null,
    'label' => 'Signature Capture',
    'required' => false,
    'enableWacom' => true,
    'enableFallback' => true,
])

@php
    $uid = 'sigpad_' . uniqid();
    $resolvedKey = config('services.wacom.key', '');
    $resolvedSecret = config('services.wacom.secret', '');
@endphp

<div id="{{ $uid }}_root" class="signature-pad-component" data-sigpad-id="{{ $uid }}">
    {{-- Hidden field for form submission --}}
    <input type="hidden" name="{{ $name }}" id="{{ $uid }}_data" value="{{ old($name, $value ?? '') }}">

    {{-- Status --}}
    <div id="{{ $uid }}_status" class="alert d-none mb-3"></div>

    {{-- Signature Card --}}
    <div class="card bg-light mb-3">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 text-white">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</h6>
            <span id="{{ $uid }}_badge" class="badge bg-secondary" style="font-size:0.7rem;">Initializing...</span>
        </div>
        <div class="card-body text-center py-3">
            {{-- Signature preview --}}
            <div id="{{ $uid }}_box" style="border: 2px dashed #ccc; min-height: 120px; background: #fff; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; position: relative;">
                <div id="{{ $uid }}_placeholder">
                    <i class="fa fa-pen fa-2x text-muted mb-1"></i>
                    <p class="text-muted mb-0 small">Click "Capture" to sign</p>
                </div>
                <img id="{{ $uid }}_img" src="{{ old($name, $value ?? '') }}" alt="Signature"
                     style="max-width: 100%; max-height: 120px; display: {{ old($name, $value ?? '') ? 'block' : 'none' }};">
            </div>

            @if($enableWacom)
            <div id="{{ $uid }}_wacom_btns" class="btn-group mb-2" style="display:none;">
                <button type="button" id="{{ $uid }}_btnSTU" class="btn btn-primary btn-sm">
                    <i class="fa fa-pen"></i> Capture (Wacom STU)
                </button>
                <button type="button" id="{{ $uid }}_btnCanvas" class="btn btn-outline-primary btn-sm">
                    <i class="fa fa-desktop"></i> Capture (Screen)
                </button>
                <button type="button" id="{{ $uid }}_btnClear" class="btn btn-warning btn-sm" disabled>
                    <i class="fa fa-eraser"></i> Clear
                </button>
            </div>
            @endif

            @if($enableFallback)
            {{-- Simple draw fallback --}}
            <div id="{{ $uid }}_fallback" class="mt-2" @if($enableWacom) style="display:none;" @endif>
                <p class="text-muted small mb-1">Draw with mouse or touch:</p>
                <canvas id="{{ $uid }}_fbcanvas" width="460" height="130"
                        style="border: 1px solid #999; background: #fff; cursor: crosshair; max-width:100%; touch-action:none;"></canvas>
                <div class="mt-1">
                    <button type="button" id="{{ $uid }}_fbUse" class="btn btn-info btn-sm">
                        <i class="fa fa-check"></i> Use Signature
                    </button>
                    <button type="button" id="{{ $uid }}_fbClear" class="btn btn-secondary btn-sm">
                        <i class="fa fa-eraser"></i> Clear
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Wacom JS Signature SDK — loaded once via ES module --}}
@once
@push('scripts')
<script type="module">
/**
 * Wacom Signature SDK Manager (singleton)
 *
 * Uses the pure JS/WebAssembly SDK with WebHID for STU devices.
 * No Windows SigCaptX service required.
 */
const { default: SigSDK } = await import("{{ asset('js/update_signiture_pad/@wacom/signature-sdk/signature-sdk.js') }}".replace(/^https?:\/\/[^\/]+/, ''));

window.WacomSigSDK = (function() {
    let sigSDK  = null;
    let sigObj  = null;
    let ready   = false;
    let stuOk   = false;
    let version = '';
    const cbs   = [];

    async function init(licKey, licSecret) {
        try {
            console.log('[WacomSig] Loading SDK…');
            sigSDK  = await new SigSDK();
            version = sigSDK.VERSION || '';
            console.log('[WacomSig] SDK v' + version + ' loaded.');

            sigObj = new sigSDK.SigObj();

            if (licKey && licSecret) {
                await sigObj.setLicence(licKey, licSecret);
                console.log('[WacomSig] Licence applied.');
            } else {
                console.warn('[WacomSig] No licence — eval mode.');
            }

            stuOk = !!(sigSDK.STUDevice && sigSDK.STUDevice.isHIDSupported());
            console.log('[WacomSig] WebHID STU support = ' + stuOk);

            ready = true;
            flush(true);
        } catch (e) {
            console.error('[WacomSig] Init failed:', e);
            flush(false, 'SDK init failed: ' + e.message);
        }
    }

    function flush(ok, err) {
        const pending = cbs.splice(0);
        pending.forEach(fn => { try { fn(ok, err); } catch(e) { console.error(e); } });
    }

    /**
     * Render the current sigObj to a PNG data-URL.
     */
    async function renderSignature() {
        // pixels = dpi * mm / 25.4
        let width  = Math.trunc((96 * sigObj.getWidth(false) * 0.01) / 25.4);
        let height = Math.trunc((96 * sigObj.getHeight(false) * 0.01) / 25.4);

        const scaleW = 300 / width;
        const scaleH = 200 / height;
        const scale  = Math.min(scaleW, scaleH);

        let renderW = Math.trunc(width * scale);
        const renderH = Math.trunc(height * scale);

        // render width must be multiple of 4
        if (renderW % 4 !== 0) { renderW += renderW % 4; }

        const inkColor = '#000F55';
        return await sigObj.renderBitmap(
            renderW, renderH, 'image/png', 4,
            inkColor, 'white', 0, 0,
            sigSDK.RenderFlags.RenderEncodeData.value
        );
    }

    /**
     * Capture from a physical Wacom STU device via WebHID.
     */
    async function captureSTU() {
        if (!stuOk) { throw new Error('WebHID not supported in this browser.'); }

        const devices = await sigSDK.STUDevice.requestDevices();
        if (!devices || devices.length === 0) {
            throw new Error('No STU device found. Make sure it is connected via USB.');
        }

        const stuDevice = new sigSDK.STUDevice(devices[0]);
        const config    = new sigSDK.Config();

        return new Promise((resolve, reject) => {
            const dialog = new sigSDK.StuCaptDialog(stuDevice, config);

            dialog.addEventListener(sigSDK.EventType.OK, async () => {
                deleteConfig(config);
                try {
                    const image = await renderSignature();
                    resolve(image);
                } catch (e) {
                    reject(e);
                } finally {
                    stuDevice.delete();
                }
            });

            dialog.addEventListener(sigSDK.EventType.CANCEL, () => {
                deleteConfig(config);
                stuDevice.delete();
                reject(new Error('Capture cancelled.'));
            });

            dialog.open(sigObj, '', '', null, sigSDK.KeyType.SHA512, null);
        });
    }

    /**
     * Capture via on-screen canvas dialog (mouse / touch / pen).
     */
    async function captureCanvas() {
        const config = new sigSDK.Config();
        config.source.mouse = true;
        config.source.touch = true;
        config.source.pen   = true;

        return new Promise((resolve, reject) => {
            const dialog = new sigSDK.SigCaptDialog(config);

            dialog.addEventListener(sigSDK.EventType.OK, async () => {
                deleteConfig(config);
                try {
                    const image = await renderSignature();
                    resolve(image);
                } catch (e) {
                    reject(e);
                }
            });

            dialog.addEventListener(sigSDK.EventType.CANCEL, () => {
                deleteConfig(config);
                reject(new Error('Capture cancelled.'));
            });

            dialog.open(sigObj, '', '', null, sigSDK.KeyType.SHA512, null);
            dialog.startCapture();
        });
    }

    function deleteConfig(config) {
        try {
            for (let i = 0; i < config.buttons.size(); i++) {
                config.buttons.get(i).delete();
            }
            config.delete;
        } catch (e) { /* ignore */ }
    }

    return {
        init:           (k, s) => init(k, s),
        isReady:        ()     => ready,
        isSTUAvailable: ()     => stuOk,
        getVersion:     ()     => version,
        onReady:        (fn)   => { ready ? fn(true) : cbs.push(fn); },
        captureSTU:     ()     => captureSTU(),
        captureCanvas:  ()     => captureCanvas(),
    };
})();

// Initialise once with licence from server config
WacomSigSDK.init(
    @json($resolvedKey),
    @json($resolvedSecret)
);
</script>
@endpush
@endonce

{{-- Per-instance wiring --}}
@push('scripts')
<script type="module">
(function() {
    const ID = @json($uid);
    const el = (s) => document.getElementById(ID + '_' + s);

    const hiddenInput = el('data');
    const statusDiv   = el('status');
    const badge       = el('badge');
    const placeholder = el('placeholder');
    const sigImg      = el('img');
    const btnSTU      = el('btnSTU');
    const btnCanvas   = el('btnCanvas');
    const btnClear    = el('btnClear');
    const wacomBtns   = el('wacom_btns');
    const fallbackDiv = el('fallback');
    const fbCanvas    = el('fbcanvas');
    const fbUseBtn    = el('fbUse');
    const fbClearBtn  = el('fbClear');

    /* ---- helpers ---- */
    function showMsg(msg, type) {
        if (!statusDiv) return;
        statusDiv.className = 'alert alert-' + type + ' small';
        statusDiv.textContent = msg;
        statusDiv.classList.remove('d-none');
        if (type === 'success' || type === 'info') {
            setTimeout(() => statusDiv.classList.add('d-none'), 4000);
        }
    }

    function setBadge(text, cls) {
        if (!badge) return;
        badge.textContent = text;
        badge.className = 'badge bg-' + cls;
        badge.style.fontSize = '0.7rem';
    }

    function displaySignature(dataUrl) {
        hiddenInput.value = dataUrl;
        sigImg.src = dataUrl;
        sigImg.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        if (btnClear)    btnClear.disabled = false;
    }

    function clearSignature() {
        hiddenInput.value = '';
        sigImg.src = '';
        sigImg.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
        if (btnClear)    btnClear.disabled = true;
    }

    // Restore existing value on load
    if (hiddenInput && hiddenInput.value && hiddenInput.value.length > 10) {
        sigImg.src = hiddenInput.value;
        sigImg.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        if (btnClear) btnClear.disabled = false;
    }

    /* ========== WACOM SDK ========== */
    @if($enableWacom)
    (function waitForSDK() {
        if (typeof WacomSigSDK !== 'undefined') {
            wireSDK();
        } else {
            setTimeout(waitForSDK, 150);
        }
    })();

    function wireSDK() {
        setBadge('Loading SDK…', 'info');

        WacomSigSDK.onReady(function(success, error) {
            if (!success) {
                setBadge('SDK unavailable', 'danger');
                showMsg(error || 'Wacom SDK failed to load.', 'warning');
                @if($enableFallback)
                if (fallbackDiv) fallbackDiv.style.display = '';
                @endif
                return;
            }

            const ver   = WacomSigSDK.getVersion();
            const hasStu = WacomSigSDK.isSTUAvailable();

            setBadge(hasStu ? 'STU Ready (v' + ver + ')' : 'Screen Only (v' + ver + ')', hasStu ? 'success' : 'info');
            if (wacomBtns) wacomBtns.style.display = '';

            if (!hasStu && btnSTU) {
                btnSTU.disabled = true;
                btnSTU.title = 'WebHID not supported — use Chrome/Edge';
            }
        });

        /* STU capture */
        if (btnSTU) {
            btnSTU.addEventListener('click', async function() {
                if (!WacomSigSDK.isReady()) { showMsg('SDK not ready.', 'warning'); return; }
                btnSTU.disabled = true;
                showMsg('Select your STU device when prompted…', 'info');
                try {
                    const image = await WacomSigSDK.captureSTU();
                    displaySignature(image);
                    showMsg('Signature captured from STU!', 'success');
                } catch (e) {
                    showMsg(e.message || 'STU capture failed.', 'warning');
                } finally {
                    btnSTU.disabled = false;
                }
            });
        }

        /* Screen canvas capture */
        if (btnCanvas) {
            btnCanvas.addEventListener('click', async function() {
                if (!WacomSigSDK.isReady()) { showMsg('SDK not ready.', 'warning'); return; }
                btnCanvas.disabled = true;
                showMsg('Sign in the dialog window…', 'info');
                try {
                    const image = await WacomSigSDK.captureCanvas();
                    displaySignature(image);
                    showMsg('Signature captured!', 'success');
                } catch (e) {
                    showMsg(e.message || 'Canvas capture failed.', 'warning');
                } finally {
                    btnCanvas.disabled = false;
                }
            });
        }

        /* Clear */
        if (btnClear) {
            btnClear.addEventListener('click', clearSignature);
        }
    }
    @else
    setBadge('Canvas mode', 'info');
    @if($enableFallback)
    if (fallbackDiv) fallbackDiv.style.display = '';
    @endif
    @endif

    /* ========== SIMPLE DRAW FALLBACK ========== */
    @if($enableFallback)
    if (fbCanvas) {
        const fctx = fbCanvas.getContext('2d');
        let drawing = false, lx = 0, ly = 0;
        fctx.fillStyle = '#fff';
        fctx.fillRect(0, 0, fbCanvas.width, fbCanvas.height);
        fctx.strokeStyle = '#000';
        fctx.lineWidth = 2;
        fctx.lineCap = 'round';

        function gp(e) {
            const r = fbCanvas.getBoundingClientRect();
            if (e.touches) return { x: e.touches[0].clientX - r.left, y: e.touches[0].clientY - r.top };
            return { x: e.clientX - r.left, y: e.clientY - r.top };
        }

        fbCanvas.addEventListener('mousedown', (e) => { drawing = true; const p = gp(e); lx = p.x; ly = p.y; });
        fbCanvas.addEventListener('mousemove', (e) => {
            if (!drawing) return;
            const p = gp(e);
            fctx.beginPath(); fctx.moveTo(lx, ly); fctx.lineTo(p.x, p.y); fctx.stroke();
            lx = p.x; ly = p.y;
        });
        fbCanvas.addEventListener('mouseup', () => drawing = false);
        fbCanvas.addEventListener('mouseout', () => drawing = false);

        fbCanvas.addEventListener('touchstart', (e) => { e.preventDefault(); drawing = true; const p = gp(e); lx = p.x; ly = p.y; });
        fbCanvas.addEventListener('touchmove', (e) => {
            e.preventDefault();
            if (!drawing) return;
            const p = gp(e);
            fctx.beginPath(); fctx.moveTo(lx, ly); fctx.lineTo(p.x, p.y); fctx.stroke();
            lx = p.x; ly = p.y;
        });
        fbCanvas.addEventListener('touchend', () => drawing = false);

        if (fbClearBtn) {
            fbClearBtn.addEventListener('click', () => {
                fctx.fillStyle = '#fff';
                fctx.fillRect(0, 0, fbCanvas.width, fbCanvas.height);
            });
        }

        if (fbUseBtn) {
            fbUseBtn.addEventListener('click', () => {
                displaySignature(fbCanvas.toDataURL('image/png'));
                showMsg('Signature loaded from canvas.', 'success');
            });
        }
    }
    @endif
})();
</script>
@endpush
