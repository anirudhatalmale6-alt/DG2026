@php
    $controller = 'dashboard';
    $page = $action = 'index';
    $action = $controller.'_'.$action;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="" />
	<meta name="author" content="" />
	<meta name="robots" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="format-detection" content="telephone=no">
    <link rel="shortcut icon" type="image/png" href="/public/smartdash/images/favicon.png">

	<!-- PAGE TITLE HERE -->
	<title>CIMS | Hello Sunshine</title>

    <!-- Hardcoded CSS includes -->
    <link href="/public/smartdash/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="/public/smartdash/vendor/owl-carousel/owl.carousel.css" rel="stylesheet">
    <link href="/public/smartdash/vendor/metismenu/css/metisMenu.min.css" rel="stylesheet">
    <link href="/public/smartdash/css/style.css" rel="stylesheet">
    <link href="/public/smartdash/css/smartdash-forms.css" rel="stylesheet">

    <style>
        /* Remove default body margins/padding */
        html, body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Main wrapper - fixed layout */
        #main-wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Content body - flex grow to fill space, centered content */
        .content-body {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            margin: 0 !important;
            background: #f4f6f9;
        }

        /* Hello content - perfectly centered */
        .hello-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            width: 100%;
            max-width: 100%;
        }

        .hello-content h1 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.5rem, 5vw, 2.5rem);
            font-weight: 700;
            color: #0d3d56;
            margin: 0 0 8px 0;
        }

        .hello-content h2 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(1.2rem, 4vw, 1.8rem);
            font-weight: 500;
            color: #17A2B8;
            margin: 0 0 20px 0;
        }

        .believe-image {
            max-width: 90%;
            width: 400px;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hello-content {
                padding: 15px;
            }
            .believe-image {
                width: 100%;
                max-width: 350px;
            }
        }

        @media (max-width: 480px) {
            .believe-image {
                max-width: 280px;
            }
        }

        /* Remove any default height constraints */
        .content-body.default-height {
            min-height: auto !important;
            height: auto !important;
        }
    </style>
</head>
<body data-layout="horizontal">

<!-- CIMS Notification System -->
<script>
    var CIMS = CIMS || {};
    CIMS.notify = function(message, type, duration) {
        type = type || 'success';
        duration = duration || 3000;
        var existing = document.querySelector('.cims-notify');
        if (existing) existing.remove();
        var icons = { success: 'fa-check-circle', error: 'fa-times-circle', warning: 'fa-exclamation-triangle', info: 'fa-info-circle' };
        var colors = { success: '#17A2B8', error: '#dc3545', warning: '#ffc107', info: '#0d3d56' };
        var notify = document.createElement('div');
        notify.className = 'cims-notify cims-notify-' + type;
        notify.innerHTML = '<i class="fas ' + icons[type] + '"></i> <span>' + message + '</span>';
        notify.style.cssText = 'position:fixed;top:20px;right:20px;z-index:99999;padding:15px 25px;border-radius:6px;background:#fff;box-shadow:0 4px 20px rgba(0,0,0,0.15);display:flex;align-items:center;gap:12px;font-size:14px;font-weight:500;color:#333;border-left:4px solid ' + colors[type] + ';animation:cimsSlideIn 0.3s ease;';
        notify.querySelector('i').style.cssText = 'font-size:20px;color:' + colors[type] + ';';
        if (!document.getElementById('cims-notify-styles')) {
            var style = document.createElement('style');
            style.id = 'cims-notify-styles';
            style.textContent = '@keyframes cimsSlideIn{from{transform:translateX(100%);opacity:0}to{transform:translateX(0);opacity:1}}@keyframes cimsSlideOut{from{transform:translateX(0);opacity:1}to{transform:translateX(100%);opacity:0}}';
            document.head.appendChild(style);
        }
        document.body.appendChild(notify);
        setTimeout(function() {
            notify.style.animation = 'cimsSlideOut 0.3s ease forwards';
            setTimeout(function() { notify.remove(); }, 300);
        }, duration);
    };
</script>

    <!--*******************
        Preloader start
    ********************-->
    <div id="preloader">
		<div class="lds-ripple">
			<div></div>
			<div></div>
		</div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">

		<!--**********************************
            CIMS Header
        ***********************************-->
		@include('cimscore::partials.cims_master_header')
        <!--**********************************
            Header end
        ***********************************-->

        <!--**********************************
            CIMS Menu
        ***********************************-->
		@include('cimscore::partials.cims_master_menu')
        <!--**********************************
            Menu end
        ***********************************-->

		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body default-height">
            <div class="hello-content">
                <h1>Welcome to CIMS 3000</h1>
                <h2>Hellooo Sunshine !</h2>
                <img src="/public/modules/cimscore/images/believe.jpg" alt="If You Believe In Yourself Anything Is Possible" class="believe-image">
            </div>
        </div>
        <!--**********************************
            Content body end
        ***********************************-->

        <!--**********************************
            CIMS Footer
        ***********************************-->
		@include('cimscore::partials.cims_master_footer')
        <!--**********************************
            Footer end
        ***********************************-->

	</div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Hardcoded JS includes -->
    <script src="/public/smartdash/vendor/global/global.min.js"></script>
    <script src="/public/smartdash/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="/public/smartdash/vendor/owl-carousel/owl.carousel.js"></script>
    <script src="/public/smartdash/vendor/metismenu/js/metisMenu.min.js"></script>
    <script src="/public/smartdash/js/custom.min.js"></script>
    <script src="/public/smartdash/js/dlabnav-init.js?v=2"></script>
</body>
</html>
