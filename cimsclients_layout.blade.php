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
	<meta name="description" content="@yield('page_description', $page_description ?? '')" />
	<meta property="og:title" content="@yield('page_description', $page_description ?? '')" />
	<meta property="og:description" content="@yield('page_description', $page_description ?? '')" />
	<meta name="format-detection" content="telephone=no">
    <link rel="shortcut icon" type="image/png" href="/public/smartdash/images/favicon.png">

	<!-- PAGE TITLE HERE -->
	<title>CIMS | @yield('title', $page_title ?? '')</title>

	@if(!empty(config('dz.public.pagelevel.css.'.$action)))
        @foreach(config('dz.public.pagelevel.css.'.$action) as $style)
            <link href="{{ asset($style) }}" rel="stylesheet" type="text/css"/>
        @endforeach
    @endif

    {{-- Global Theme Styles (used by all pages) --}}
    @if(!empty(config('dz.public.global.css')))
        @foreach(config('dz.public.global.css') as $style)
            <link href="{{ asset($style) }}" rel="stylesheet" type="text/css"/>
        @endforeach
    @endif


    <!-- Hardcoded CSS includes -->
    <link href="/public/smartdash/vendor/bootstrap-select/dist/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="/public/smartdash/vendor/owl-carousel/owl.carousel.css" rel="stylesheet">
    <link href="/public/smartdash/vendor/metismenu/css/metisMenu.min.css" rel="stylesheet">
    <link href="/public/smartdash/css/style.css" rel="stylesheet">
    <link href="/public/smartdash/css/smartdash-forms.css" rel="stylesheet">
    @stack('styles')
</head>
<body data-layout="horizontal">

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
            Nav header start
        ***********************************-->
		<div class="nav-header">
            <a href="javascript:void(0)" class="brand-logo">
				<svg class="logo-abbr" width="55" height="55" viewBox="0 0 55 55" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path fill-rule="evenodd" clip-rule="evenodd" d="M27.5 0C12.3122 0 0 12.3122 0 27.5C0 42.6878 12.3122 55 27.5 55C42.6878 55 55 42.6878 55 27.5C55 12.3122 42.6878 0 27.5 0ZM28.0092 46H19L19.0001 34.9784L19 27.5803V24.4779C19 14.3752 24.0922 10 35.3733 10V17.5571C29.8894 17.5571 28.0092 19.4663 28.0092 24.4779V27.5803H36V34.9784H28.0092V46Z" fill="url(#paint0_linear)"/>
					<defs>
					</defs>
				</svg>
				<div class="brand-title">
				<svg width="106" height="47" viewBox="0 0 106 47" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M12.926 11.059H8.65397V25.5596H3.71854V11.059H1V8.66123L3.71854 7.32371V5.9862C3.71854 3.90924 4.22556 2.3923 5.23962 1.43538C6.25368 0.478461 7.87725 0 10.1103 0C11.8148 0 13.3305 0.255542 14.6574 0.766625L13.3952 4.42033C12.4027 4.10498 11.4858 3.9473 10.6443 3.9473C9.94312 3.9473 9.43609 4.15935 9.12324 4.58344C8.8104 4.99665 8.65397 5.52949 8.65397 6.18193V7.32371H12.926V11.059ZM15.5474 2.60979C15.5474 0.989544 16.4428 0.179423 18.2336 0.179423C20.0244 0.179423 20.9197 0.989544 20.9197 2.60979C20.9197 3.38185 20.6932 3.98536 20.2401 4.42033C19.7978 4.84442 19.129 5.05646 18.2336 5.05646C16.4428 5.05646 15.5474 4.2409 15.5474 2.60979ZM20.6932 25.5596H15.7578V7.32371H20.6932V25.5596Z" fill="#4E3F6B"/>
				<path d="M30.8068 25.5596H25.8714V0.179423H30.8068V25.5596Z" fill="#4E3F6B"/>
				<path d="M40.9366 25.5596H36.0011V0.179423H40.9366V25.5596Z" fill="#4E3F6B"/>
				<path d="M50.0631 16.409C50.0631 18.2141 50.3544 19.5788 50.9369 20.5031C51.5302 21.4274 52.4904 21.8896 53.8173 21.8896C55.1334 21.8896 56.0773 21.4329 56.6491 20.5194C57.2316 19.5951 57.5229 18.225 57.5229 16.409C57.5229 14.6039 57.2316 13.2501 56.6491 12.3476C56.0665 11.445 55.1118 10.9937 53.7849 10.9937C52.4688 10.9937 51.5195 11.445 50.9369 12.3476C50.3544 13.2392 50.0631 14.5931 50.0631 16.409ZM62.5716 16.409C62.5716 19.3777 61.7949 21.6993 60.2414 23.3739C58.688 25.0485 56.525 25.8858 53.7525 25.8858C52.0157 25.8858 50.4838 25.5052 49.1569 24.744C47.83 23.972 46.8106 22.8683 46.0986 21.4329C45.3866 19.9975 45.0306 18.3229 45.0306 16.409C45.0306 13.4295 45.8019 11.1133 47.3446 9.46048C48.8872 7.80761 51.0556 6.98118 53.8496 6.98118C55.5865 6.98118 57.1183 7.36177 58.4452 8.12296C59.7721 8.88415 60.7916 9.977 61.5036 11.4015C62.2156 12.826 62.5716 14.4952 62.5716 16.409Z" fill="#4E3F6B"/>
				<path d="M81.5204 25.5596L80.1288 19.1819L78.2517 11.1242H78.1384L74.8374 25.5596H69.5297L64.3839 7.32371H69.3032L71.3906 15.3977C71.7251 16.844 72.0649 18.8394 72.4101 21.3839H72.5072C72.5503 20.5575 72.7391 19.2472 73.0736 17.4529L73.3325 16.0665L75.5655 7.32371H81.0026L83.1224 16.0665C83.1656 16.3057 83.2303 16.6591 83.3166 17.1267C83.4137 17.5943 83.5054 18.0945 83.5917 18.6274C83.678 19.1493 83.7535 19.6658 83.8182 20.1769C83.8938 20.6771 83.9369 21.0795 83.9477 21.3839H84.0448C84.1419 20.601 84.3145 19.5299 84.5626 18.1706C84.8107 16.8005 84.9887 15.8762 85.0966 15.3977L87.265 7.32371H92.1033L86.8928 25.5596H81.5204Z" fill="#4E3F6B"/>
				<path d="M94.3364 23.2271C94.3364 22.3137 94.5791 21.6232 95.0646 21.1556C95.55 20.688 96.2566 20.4542 97.1844 20.4542C98.0798 20.4542 98.7702 20.6934 99.2556 21.1719C99.7519 21.6504 100 22.3354 100 23.2271C100 24.0862 99.7519 24.7658 99.2556 25.266C98.7594 25.7553 98.069 26 97.1844 26C96.2782 26 95.577 25.7608 95.0807 25.2823C94.5845 24.793 94.3364 24.1079 94.3364 23.2271Z" fill="#4E3F6B"/>
				<path d="M3.15 43.09C2.83667 43.09 2.53 43.0633 2.23 43.01C1.93 42.9567 1.65 42.88 1.39 42.78C1.13667 42.68 0.906667 42.5533 0.7 42.4C0.62 42.34 0.563333 42.2733 0.53 42.2C0.503333 42.12 0.496667 42.0433 0.51 41.97C0.53 41.89 0.563333 41.8233 0.61 41.77C0.663333 41.7167 0.726667 41.6867 0.8 41.68C0.873333 41.6733 0.953333 41.6967 1.04 41.75C1.34 41.9633 1.66 42.12 2 42.22C2.34 42.3133 2.72333 42.36 3.15 42.36C3.75 42.36 4.19333 42.25 4.48 42.03C4.77333 41.8033 4.92 41.5067 4.92 41.14C4.92 40.84 4.81333 40.6067 4.6 40.44C4.39333 40.2667 4.04333 40.13 3.55 40.03L2.51 39.82C1.88333 39.6867 1.41333 39.47 1.1 39.17C0.793333 38.8633 0.64 38.4567 0.64 37.95C0.64 37.6367 0.703333 37.3533 0.83 37.1C0.956667 36.84 1.13333 36.62 1.36 36.44C1.58667 36.2533 1.85667 36.11 2.17 36.01C2.48333 35.91 2.83 35.86 3.21 35.86C3.63 35.86 4.02 35.92 4.38 36.04C4.74667 36.1533 5.07667 36.3267 5.37 36.56C5.44333 36.62 5.49333 36.69 5.52 36.77C5.54667 36.8433 5.55 36.9167 5.53 36.99C5.51 37.0567 5.47333 37.1133 5.42 37.16C5.37333 37.2067 5.31 37.2333 5.23 37.24C5.15667 37.2467 5.07333 37.22 4.98 37.16C4.71333 36.96 4.43667 36.8167 4.15 36.73C3.86333 36.6367 3.54667 36.59 3.2 36.59C2.84667 36.59 2.54 36.6433 2.28 36.75C2.02667 36.8567 1.82667 37.0133 1.68 37.22C1.54 37.42 1.47 37.6533 1.47 37.92C1.47 38.24 1.56667 38.4933 1.76 38.68C1.96 38.8667 2.28333 39.0033 2.73 39.09L3.77 39.31C4.44333 39.45 4.94 39.6633 5.26 39.95C5.58667 40.23 5.75 40.6133 5.75 41.1C5.75 41.3933 5.69 41.6633 5.57 41.91C5.45 42.1567 5.27667 42.37 5.05 42.55C4.82333 42.7233 4.55 42.8567 4.23 42.95C3.91 43.0433 3.55 43.09 3.15 43.09Z" fill="#717579"/>
				</svg>
				</div>
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->

		<!--**********************************
            Header start
        ***********************************-->
		<div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
							<div class="dashboard_bar">
								{{$page_title ?? ''}}
                            </div>
                        </div>
                        <ul class="navbar-nav header-right">
							<li class="nav-item me-3">
								<a href="/" class="btn btn-primary btn-sm d-flex align-items-center" style="background-color: #886CC0; border-color: #886CC0;">
									<i class="fas fa-arrow-left me-2"></i> Back to GrowCRM
								</a>
							</li>

							<li class="nav-item d-flex align-items-center">
								<div class="input-group search-area">
									<input type="text" class="form-control" placeholder="Search here...">
									<span class="input-group-text"><a href="javascript:void(0)"><i class="flaticon-381-search-2"></i></a></span>
								</div>
							</li>
							<li class="nav-item dropdown notification_dropdown">
                                <a class="nav-link bell dz-theme-mode" href="javascript:void(0);">
									<i id="icon-light" class="fas fa-sun"></i>
                                    <i id="icon-dark" class="fas fa-moon"></i>
                                </a>
							</li>

							<li class="nav-item dropdown header-profile">
								<a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
									<img src="/public/smartdash/images/user.jpg" width="56" alt="">
								</a>
								<div class="dropdown-menu dropdown-menu-end">
									<a href="javascript:void(0)" class="dropdown-item ai-icon">
										<svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
										<span class="ms-2">Profile </span>
									</a>
									<a href="javascript:void(0)" class="dropdown-item ai-icon">
										<svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
										<span class="ms-2">Logout </span>
									</a>
								</div>
							</li>
                        </ul>
                    </div>
				</nav>
			</div>
		</div>
        <!--**********************************
            Header end
        ***********************************-->

        <!--**********************************
            Sidebar start - Using shared horizontal menu
        ***********************************-->
        @include('toolbox.smartdash-horizontal-menu')
        @include('toolbox.smartdash-horizontal-menu-styles')
        @include('toolbox.smartdash-horizontal-menu-scripts')
        <!--**********************************
            Sidebar end
        ***********************************-->

		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body default-height">
           @yield('content')
        </div>
        <!--**********************************
            Content body end
        ***********************************-->

		<!-- Modal -->
		@stack('modal')

        <!--**********************************
            Footer start
        ***********************************-->
        <div class="footer">
            <div class="copyright">
                <p>CIMS 3000 &copy; {{ date('Y') }} | by ATP Solutions</p>
            </div>
        </div>
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
   <!-- Required vendors -->
    @if(!empty(config('dz.public.global.js.top')))
        @foreach(config('dz.public.global.js.top') as $script)
            <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach
    @endif
    @if(!empty(config('dz.public.pagelevel.js.'.$action)))
        @foreach(config('dz.public.pagelevel.js.'.$action) as $script)
            <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach
    @endif
    @if(!empty(config('dz.public.global.js.bottom')))
        @foreach(config('dz.public.global.js.bottom') as $script)
            <script src="{{ asset($script) }}" type="text/javascript"></script>
        @endforeach
    @endif

    <!-- Hardcoded JS includes -->
    <script src="/public/smartdash/vendor/global/global.min.js"></script>
    <script src="/public/smartdash/vendor/bootstrap-select/dist/js/bootstrap-select.min.js"></script>
    <script src="/public/smartdash/vendor/owl-carousel/owl.carousel.js"></script>
    <script src="/public/smartdash/vendor/metismenu/js/metisMenu.min.js"></script>
    <script src="/public/smartdash/js/custom.min.js"></script>
    <script src="/public/smartdash/js/dlabnav-init.js?v=2"></script>

    @stack('scripts')
</body>
</html>
