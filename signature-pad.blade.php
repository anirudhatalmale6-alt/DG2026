@props([
    'name' => 'signature_data',
    'value' => null,
    'label' => 'Signature Capture',
    'required' => false,
    'enableWacom' => true,
    'enableFallback' => true,
    'wacomPort' => 8000,
    'licenseKey' => null,
])

@php
    $uid = 'sigpad_' . uniqid();
    $resolvedLicense = $licenseKey ?? config('services.wacom.key', '');
@endphp

<div id="{{ $uid }}_root" class="signature-pad-component" data-sigpad-id="{{ $uid }}">
    <!-- Hidden field that the parent form reads on submit -->
    <input type="hidden" name="{{ $name }}" id="{{ $uid }}_data" value="{{ old($name, $value ?? '') }}">

    <!-- Status Messages -->
    <div id="{{ $uid }}_status" class="alert d-none mb-3"></div>

    <!-- Signature Preview Box -->
    <div class="card bg-light mb-3">
        <div class="card-header d-flex justify-content-between align-items-center py-2">
            <h6 class="mb-0 text-white">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</h6>
            <span id="{{ $uid }}_device" class="badge bg-secondary" style="font-size:0.7rem;">Checking...</span>
        </div>
        <div class="card-body text-center py-3">
            <!-- Signature display -->
            <div id="{{ $uid }}_box" style="border: 2px dashed #ccc; min-height: 120px; background: #fff; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; position: relative;">
                <div id="{{ $uid }}_placeholder">
                    <i class="fa fa-pen fa-2x text-muted mb-1"></i>
                    <p class="text-muted mb-0 small">Click "Capture" to sign</p>
                </div>
                <img id="{{ $uid }}_img" src="{{ old($name, $value ?? '') }}" alt="Signature" style="max-width: 100%; max-height: 120px; display: {{ old($name, $value ?? '') ? 'block' : 'none' }};">
            </div>

            @if($enableWacom)
            <div id="{{ $uid }}_wacom_btns" class="btn-group mb-2" style="display:none;">
                <button type="button" id="{{ $uid }}_btnCapture" class="btn btn-primary btn-sm">
                    <i class="fa fa-pen"></i> Capture (Wacom)
                </button>
                <button type="button" id="{{ $uid }}_btnClear" class="btn btn-warning btn-sm" disabled>
                    <i class="fa fa-eraser"></i> Clear
                </button>
            </div>
            @endif

            @if($enableFallback)
            <!-- Fallback canvas -->
            <div id="{{ $uid }}_fallback" class="mt-2" @if($enableWacom) style="display:none;" @endif>
                <p class="text-muted small mb-1">Draw with mouse or touch:</p>
                <canvas id="{{ $uid }}_canvas" width="460" height="130" style="border: 1px solid #999; background: #fff; cursor: crosshair; max-width:100%;"></canvas>
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

{{-- Shared CSS (loaded once) --}}
@once
@push('styles')
<style>
.sigpad-modal-bg {
    position: fixed; top: 0; left: 0; width: 100%; height: 100%;
    background: rgba(0,0,0,0.5); z-index: 9998;
}
.sigpad-modal-win {
    position: fixed; z-index: 9999; background: #fff;
    border: 2px solid #333; box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}
</style>
@endpush
@endonce

{{-- Wacom SigCaptX SDK (loaded once) --}}
@once
@push('scripts')
<script src="{{ asset('js/wgssSigCaptX.js') }}"></script>
<script>
/**
 * SigCaptX Session Manager — singleton shared across all signature-pad instances.
 *
 * Based on the official Wacom SigCaptX sample pattern:
 *   WacomGSS_SignatureSDK() -> SigCtl() -> PutLicence() -> GetSignature() -> DynamicCapture() -> ready
 *   Then per-capture: dynCapt.Capture() -> RenderBitmap()
 *
 * Objects (sigCtl, sigObj, dynCapt) are created ONCE per session and reused.
 */
window.SigCaptXSession = (function() {
    // Session objects — created once, reused for every capture
    var wgssSignatureSDK = null;
    var sigCtl = null;
    var sigObj = null;
    var dynCapt = null;

    // State
    var sessionReady = false;
    var readyCallbacks = [];
    var initStarted = false;

    // Config
    var TIMEOUT = 1500;
    var MAX_RETRY = 5;
    var retryCount = 0;
    var retryTimer = null;
    var licence = '';
    var servicePort = 8000;

    /**
     * Start the session: connect to service, apply licence, create objects.
     */
    function init(port, licenceKey) {
        if (initStarted) return;
        initStarted = true;
        servicePort = port || 8000;
        licence = licenceKey || '';

        console.log('SigCaptX: Connecting to service on port ' + servicePort + '...');
        wgssSignatureSDK = new WacomGSS_SignatureSDK(onDetectRunning, servicePort);
    }

    function onDetectRunning() {
        if (wgssSignatureSDK.running) {
            console.log('SigCaptX: Service detected (v' + (wgssSignatureSDK.service_fileVersion || '?') + ')');
            retryCount = 0;

            // Step 1: Create SigCtl
            sigCtl = new wgssSignatureSDK.SigCtl(onSigCtlConstructed);
        } else {
            retryCount++;
            if (retryCount < MAX_RETRY) {
                console.log('SigCaptX: Not detected yet, retry ' + retryCount + '/' + MAX_RETRY);
                retryTimer = setTimeout(function() {
                    wgssSignatureSDK = new WacomGSS_SignatureSDK(onDetectRunning, servicePort);
                }, TIMEOUT);
            } else {
                console.warn('SigCaptX: Service not available after ' + MAX_RETRY + ' attempts.');
                fireCallbacks(false, 'SigCaptX service not available');
            }
        }
    }

    function onSigCtlConstructed(sigCtlV, status) {
        if (status !== 0) {
            console.error('SigCaptX: SigCtl constructor failed (status ' + status + ')');
            fireCallbacks(false, 'SigCtl init failed');
            return;
        }
        console.log('SigCaptX: SigCtl created. Applying licence (' + licence.length + ' chars)...');

        // Step 2: Apply licence (JWT key)
        sigCtl.PutLicence(licence, onPutLicence);
    }

    function onPutLicence(sigCtlV, status) {
        if (status !== 0) {
            console.warn('SigCaptX: PutLicence status=' + status + ' (expected 0). Licence may be invalid.');
        } else {
            console.log('SigCaptX: Licence applied successfully.');
        }

        // Step 3: Get the signature object
        sigCtl.GetSignature(onGetSignature);
    }

    function onGetSignature(sigCtlV, sigObjV, status) {
        if (status !== 0) {
            console.error('SigCaptX: GetSignature failed (status ' + status + ')');
            fireCallbacks(false, 'GetSignature failed');
            return;
        }
        sigObj = sigObjV;
        console.log('SigCaptX: Signature object obtained.');

        // Step 4: Create DynamicCapture object
        dynCapt = new wgssSignatureSDK.DynamicCapture(onDynCaptConstructed);
    }

    function onDynCaptConstructed(dynCaptV, status) {
        if (status !== 0) {
            console.error('SigCaptX: DynamicCapture constructor failed (status ' + status + ')');
            fireCallbacks(false, 'DynamicCapture init failed');
            return;
        }

        console.log('SigCaptX: Session fully ready. Capture available.');
        sessionReady = true;
        fireCallbacks(true);
    }

    function fireCallbacks(success, error) {
        var cbs = readyCallbacks.slice();
        readyCallbacks = [];
        for (var i = 0; i < cbs.length; i++) {
            try { cbs[i](success, error); } catch(e) { console.error('SigCaptX callback error:', e); }
        }
    }

    /**
     * Render bitmap parameters — matching official Wacom SigCaptX sample values
     */
    var RENDER = {
        FORMAT: 'image/png',
        WIDTH:  300,
        HEIGHT: 200,
        INK_WIDTH: 0.7,
        INK_COLOR: 0x00003300,
        BG_COLOR:  0x00FFFFFF,
        PAD_X: 4,
        PAD_Y: 4
    };

    /**
     * Try RenderBitmap on a sigObj. Tries Base64, then Picture, then 32BPP variants.
     */
    function renderSigObj(targetSigObj, label, callback) {
        console.log('SigCaptX: renderSigObj [' + label + '] handle=' + targetSigObj.handle);

        // Attempt 1: Base64 + 24BPP
        var flags1 = wgssSignatureSDK.RBFlags.RenderOutputBase64
                   | wgssSignatureSDK.RBFlags.RenderColor24BPP;

        targetSigObj.RenderBitmap(RENDER.FORMAT, RENDER.WIDTH, RENDER.HEIGHT,
            RENDER.INK_WIDTH, RENDER.INK_COLOR, RENDER.BG_COLOR, flags1,
            RENDER.PAD_X, RENDER.PAD_Y,
            function(sigObjRef, bmpData, st) {
                console.log('SigCaptX: [' + label + '] Base64+24BPP status=' + st);
                if (st === 0 && bmpData) {
                    callback('data:image/png;base64,' + bmpData, null);
                    return;
                }

                // Attempt 2: Base64 + 32BPP
                var flags2 = wgssSignatureSDK.RBFlags.RenderOutputBase64
                           | wgssSignatureSDK.RBFlags.RenderColor32BPP;

                targetSigObj.RenderBitmap(RENDER.FORMAT, RENDER.WIDTH, RENDER.HEIGHT,
                    RENDER.INK_WIDTH, RENDER.INK_COLOR, RENDER.BG_COLOR, flags2,
                    RENDER.PAD_X, RENDER.PAD_Y,
                    function(ref2, bmpData2, st2) {
                        console.log('SigCaptX: [' + label + '] Base64+32BPP status=' + st2);
                        if (st2 === 0 && bmpData2) {
                            callback('data:image/png;base64,' + bmpData2, null);
                            return;
                        }

                        // Attempt 3: Picture + 24BPP
                        var flags3 = wgssSignatureSDK.RBFlags.RenderOutputPicture
                                   | wgssSignatureSDK.RBFlags.RenderColor24BPP;

                        targetSigObj.RenderBitmap(RENDER.FORMAT, RENDER.WIDTH, RENDER.HEIGHT,
                            RENDER.INK_WIDTH, RENDER.INK_COLOR, RENDER.BG_COLOR, flags3,
                            RENDER.PAD_X, RENDER.PAD_Y,
                            function(ref3, bmpObj, st3) {
                                console.log('SigCaptX: [' + label + '] Picture+24BPP status=' + st3);
                                if (st3 === 0 && bmpObj && bmpObj.image) {
                                    var img = bmpObj.image;
                                    if (img.src && img.src.indexOf('data:') === 0) {
                                        callback(img.src, null);
                                    } else if (img.src) {
                                        var t = new Image();
                                        t.crossOrigin = 'anonymous';
                                        t.onload = function() {
                                            var c = document.createElement('canvas');
                                            c.width = t.naturalWidth; c.height = t.naturalHeight;
                                            c.getContext('2d').drawImage(t, 0, 0);
                                            callback(c.toDataURL('image/png'), null);
                                        };
                                        t.onerror = function() { callback(null, 'Bitmap image load failed'); };
                                        t.src = img.src;
                                    } else {
                                        callback(null, 'Render [' + label + '] returned empty bitmap');
                                    }
                                    return;
                                }
                                callback(null, 'All render attempts failed for [' + label + ']');
                            }
                        );
                    }
                );
            }
        );
    }

    /**
     * Try rendering across multiple SigObj references until one works.
     * Order: session sigObj → capture sigObjV → fresh GetSignature sigObj
     */
    function tryRenderAll(capturedSigObjV, finalCallback) {
        // Diagnostic: log all handles
        console.log('SigCaptX: === RENDER DIAGNOSTICS ===');
        console.log('SigCaptX: Session sigObj handle:', sigObj ? sigObj.handle : 'NULL');
        console.log('SigCaptX: Capture sigObjV handle:', capturedSigObjV ? capturedSigObjV.handle : 'NULL');

        // Step 1: Check if session sigObj has captured data
        if (sigObj && sigObj.handle) {
            sigObj.GetIsCaptured(function(ref, isCaptured, capSt) {
                console.log('SigCaptX: Session sigObj.GetIsCaptured=' + isCaptured + ' status=' + capSt);

                if (isCaptured) {
                    renderSigObj(sigObj, 'session', function(dataUrl, err) {
                        if (dataUrl) { finalCallback(dataUrl, null); return; }
                        console.warn('SigCaptX: Session sigObj render failed: ' + err);
                        tryCaptureSigObj(capturedSigObjV, finalCallback);
                    });
                } else {
                    tryCaptureSigObj(capturedSigObjV, finalCallback);
                }
            });
        } else {
            tryCaptureSigObj(capturedSigObjV, finalCallback);
        }
    }

    function tryCaptureSigObj(capturedSigObjV, finalCallback) {
        // Step 2: Try the capture callback's sigObjV
        if (capturedSigObjV && capturedSigObjV.handle) {
            capturedSigObjV.GetIsCaptured(function(ref, isCaptured, capSt) {
                console.log('SigCaptX: Capture sigObjV.GetIsCaptured=' + isCaptured + ' status=' + capSt);

                if (isCaptured) {
                    renderSigObj(capturedSigObjV, 'capture', function(dataUrl, err) {
                        if (dataUrl) { finalCallback(dataUrl, null); return; }
                        console.warn('SigCaptX: Capture sigObjV render failed: ' + err);
                        tryFreshGetSignature(capturedSigObjV, finalCallback);
                    });
                } else {
                    tryFreshGetSignature(capturedSigObjV, finalCallback);
                }
            });
        } else {
            console.warn('SigCaptX: Capture sigObjV has no handle');
            tryFreshGetSignature(capturedSigObjV, finalCallback);
        }
    }

    function tryFreshGetSignature(capturedSigObjV, finalCallback) {
        // Step 3: Re-fetch from SigCtl
        console.log('SigCaptX: Trying fresh GetSignature...');
        sigCtl.GetSignature(function(sigCtlRef, freshSig, getSigSt) {
            console.log('SigCaptX: Fresh GetSignature status=' + getSigSt + ' handle=' + (freshSig ? freshSig.handle : 'NULL'));

            if (getSigSt !== 0 || !freshSig || !freshSig.handle) {
                // Last resort: try GetSigText from capture sigObjV
                trySigTextFallback(capturedSigObjV, finalCallback);
                return;
            }

            freshSig.GetIsCaptured(function(ref, isCaptured, capSt) {
                console.log('SigCaptX: Fresh sigObj.GetIsCaptured=' + isCaptured + ' status=' + capSt);

                renderSigObj(freshSig, 'fresh', function(dataUrl, err) {
                    if (dataUrl) { finalCallback(dataUrl, null); return; }
                    console.warn('SigCaptX: Fresh sigObj render failed: ' + err);
                    trySigTextFallback(capturedSigObjV, finalCallback);
                });
            });
        });
    }

    function trySigTextFallback(capturedSigObjV, finalCallback) {
        // Step 4: Last resort — get SigText from any available sigObj,
        // load it into the session sigObj, then try rendering again.
        console.log('SigCaptX: Trying SigText fallback...');

        var source = (capturedSigObjV && capturedSigObjV.handle) ? capturedSigObjV : sigObj;
        if (!source || !source.handle) {
            finalCallback(null, 'No valid signature object available for rendering.');
            return;
        }

        source.GetSigText(function(ref, sigText, textSt) {
            console.log('SigCaptX: GetSigText status=' + textSt + ' length=' + (sigText ? sigText.length : 0));

            if (textSt !== 0 || !sigText || sigText.length === 0) {
                finalCallback(null, 'Signature capture succeeded but rendering failed. RenderBitmap status=1. SigText unavailable.');
                return;
            }

            // We have raw sig text. Load it into the session sigObj and try rendering.
            sigObj.PutSigText(sigText, function(ref2, putSt) {
                console.log('SigCaptX: PutSigText status=' + putSt);

                if (putSt !== 0) {
                    finalCallback(null, 'PutSigText failed (status ' + putSt + '). Cannot render signature.');
                    return;
                }

                renderSigObj(sigObj, 'sigtext-reload', function(dataUrl, err) {
                    if (dataUrl) { finalCallback(dataUrl, null); return; }
                    finalCallback(null, 'All rendering methods exhausted. Last error: ' + err);
                });
            });
        });
    }

    /**
     * Perform a signature capture using the pre-created session objects.
     */
    function capture(who, why, callback) {
        if (!sessionReady || !dynCapt || !sigCtl) {
            callback(null, 'SigCaptX session not ready');
            return;
        }

        console.log('SigCaptX: Starting capture...');
        dynCapt.Capture(sigCtl, who, why, null, null, function onCapture(dynCaptV, sigObjV, status) {
            console.log('SigCaptX: Capture result status=' + status);

            if (status === 0) {
                // Try rendering with all available sigObj references
                tryRenderAll(sigObjV, callback);
            } else {
                var msgs = {
                    1: 'Capture cancelled.',
                    100: 'Pad error — is the STU device connected?',
                    103: 'Capture not licensed. Check your WACOM_KEY in .env.',
                    200: 'Capture timed out.'
                };
                callback(null, msgs[status] || 'Capture ended (status ' + status + ')');
            }
        });
    }

    return {
        init: function(p, l) { init(p, l); },
        isReady: function() { return sessionReady; },
        onReady: function(cb) {
            if (sessionReady) { cb(true); }
            else { readyCallbacks.push(cb); }
        },
        capture: function(who, why, cb) { capture(who, why, cb); },
        getSDK: function() { return wgssSignatureSDK; }
    };
})();
</script>
@endpush
@endonce

{{-- Per-instance initializer --}}
@push('scripts')
<script>
(function() {
    var ID = '{{ $uid }}';
    var WACOM_PORT = {{ $wacomPort }};
    var WACOM_LICENCE = '{!! $resolvedLicense !!}';
    var el = function(suffix) { return document.getElementById(ID + '_' + suffix); };

    // Elements
    var hiddenInput   = el('data');
    var statusMsg     = el('status');
    var deviceBadge   = el('device');
    var placeholder   = el('placeholder');
    var sigImg        = el('img');

    // Wacom elements (may be null if enableWacom=false)
    var btnCapture    = el('btnCapture');
    var btnClear      = el('btnClear');
    var wacomBtns     = el('wacom_btns');

    // Fallback elements (may be null if enableFallback=false)
    var fbCanvas      = el('canvas');
    var fbUseBtn      = el('fbUse');
    var fbClearBtn    = el('fbClear');
    var fallbackDiv   = el('fallback');

    // Helpers
    function showMsg(msg, type) {
        statusMsg.className = 'alert alert-' + type;
        statusMsg.textContent = msg;
        statusMsg.classList.remove('d-none');
        if (type === 'success') { setTimeout(function() { statusMsg.classList.add('d-none'); }, 4000); }
    }

    function setBadge(text, cls) {
        deviceBadge.textContent = text;
        deviceBadge.className = 'badge bg-' + cls;
        deviceBadge.style.fontSize = '0.7rem';
    }

    function displaySignature(dataUrl) {
        hiddenInput.value = dataUrl;
        sigImg.src = dataUrl;
        sigImg.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        if (btnClear) btnClear.disabled = false;
    }

    function clearSignature() {
        hiddenInput.value = '';
        sigImg.src = '';
        sigImg.style.display = 'none';
        if (placeholder) placeholder.style.display = 'block';
        if (btnClear) btnClear.disabled = true;
    }

    // If existing value is present, show it
    if (hiddenInput.value && hiddenInput.value.length > 10) {
        sigImg.src = hiddenInput.value;
        sigImg.style.display = 'block';
        if (placeholder) placeholder.style.display = 'none';
        if (btnClear) btnClear.disabled = false;
    }

    // ===================== WACOM SESSION =====================
    @if($enableWacom)
    function showFallbackMode(reason) {
        setBadge(reason || 'No Wacom — using canvas', 'warning');
        if (wacomBtns) wacomBtns.style.display = 'none';
        @if($enableFallback)
        if (fallbackDiv) fallbackDiv.style.display = '';
        @endif
    }

    setBadge('Connecting...', 'info');

    // Initialize the shared session (only runs once across all instances)
    SigCaptXSession.init(WACOM_PORT, WACOM_LICENCE);

    SigCaptXSession.onReady(function(success, error) {
        if (success) {
            var sdk = SigCaptXSession.getSDK();
            var ver = sdk && sdk.service_fileVersion ? ' v' + sdk.service_fileVersion : '';
            setBadge('STU Ready' + ver, 'success');
            if (wacomBtns) wacomBtns.style.display = '';
            @if($enableFallback)
            if (fallbackDiv) fallbackDiv.style.display = 'none';
            @endif
        } else {
            showFallbackMode(error || 'SigCaptX not detected');
        }
    });

    // Capture button
    if (btnCapture) {
        btnCapture.addEventListener('click', function() {
            if (!SigCaptXSession.isReady()) {
                showMsg('SigCaptX not ready. Use canvas fallback.', 'warning');
                return;
            }
            btnCapture.disabled = true;
            showMsg('Sign on the Wacom pad now. Press OK when done.', 'info');

            SigCaptXSession.capture('Client', 'Signature', function(dataUrl, error) {
                btnCapture.disabled = false;
                if (dataUrl) {
                    displaySignature(dataUrl);
                    showMsg('Signature captured successfully!', 'success');
                } else {
                    showMsg(error || 'Capture failed.', 'warning');
                }
            });
        });
    }

    if (btnClear) {
        btnClear.addEventListener('click', function() { clearSignature(); });
    }
    @else
    setBadge('Canvas mode', 'info');
    @if($enableFallback)
    if (fallbackDiv) fallbackDiv.style.display = '';
    @endif
    @endif

    // ===================== FALLBACK CANVAS =====================
    @if($enableFallback)
    (function() {
        var fctx = fbCanvas.getContext('2d');
        var drawing = false, lx = 0, ly = 0;
        fctx.fillStyle = '#fff'; fctx.fillRect(0, 0, fbCanvas.width, fbCanvas.height);
        fctx.strokeStyle = '#000'; fctx.lineWidth = 2; fctx.lineCap = 'round';

        function getPos(e) {
            var rect = fbCanvas.getBoundingClientRect();
            if (e.touches) return { x: e.touches[0].clientX - rect.left, y: e.touches[0].clientY - rect.top };
            return { x: e.clientX - rect.left, y: e.clientY - rect.top };
        }

        fbCanvas.addEventListener('mousedown', function(e) { drawing = true; var p = getPos(e); lx = p.x; ly = p.y; });
        fbCanvas.addEventListener('mousemove', function(e) {
            if (!drawing) return; var p = getPos(e);
            fctx.beginPath(); fctx.moveTo(lx, ly); fctx.lineTo(p.x, p.y); fctx.stroke();
            lx = p.x; ly = p.y;
        });
        fbCanvas.addEventListener('mouseup', function() { drawing = false; });
        fbCanvas.addEventListener('mouseout', function() { drawing = false; });
        fbCanvas.addEventListener('touchstart', function(e) { e.preventDefault(); drawing = true; var p = getPos(e); lx = p.x; ly = p.y; });
        fbCanvas.addEventListener('touchmove', function(e) {
            e.preventDefault(); if (!drawing) return; var p = getPos(e);
            fctx.beginPath(); fctx.moveTo(lx, ly); fctx.lineTo(p.x, p.y); fctx.stroke();
            lx = p.x; ly = p.y;
        });
        fbCanvas.addEventListener('touchend', function() { drawing = false; });

        fbClearBtn.addEventListener('click', function() { fctx.fillStyle = '#fff'; fctx.fillRect(0, 0, fbCanvas.width, fbCanvas.height); });
        fbUseBtn.addEventListener('click', function() { displaySignature(fbCanvas.toDataURL('image/png')); showMsg('Signature loaded from canvas.', 'success'); });
    })();
    @endif
})();
</script>
@endpush
