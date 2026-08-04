    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    
    @if(isset($platformFavicon) && $platformFavicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $platformFavicon) }}" />
    @else
        <link rel="icon" type="image/x-icon" href="{{ asset('assets/images/favicon.ico') }}" />
    @endif
    
    <title>{{ $platformBrand ?? 'GrainTrack' }} - Business Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--! The above 6 meta tags *must* come first in the head; any other head content must come *after* these tags !-->
    <!--! Dark Mode Prevention Script !-->
    <script>
        (function() {
            var theme = localStorage.getItem('app-skin-dark');
            var customizerSkin = localStorage.getItem('app-skin');
            
            // If neither is set, default to light
            if (!theme && !customizerSkin) {
                theme = 'app-skin-light';
                localStorage.setItem('app-skin-dark', 'app-skin-light');
                localStorage.setItem('app-skin', 'app-skin-light');
            } else if (theme) {
                // Force sync customizer to match header button state
                localStorage.setItem('app-skin', theme);
            } else if (customizerSkin) {
                theme = customizerSkin;
                localStorage.setItem('app-skin-dark', customizerSkin);
            }
            
            if (theme === 'app-skin-dark') {
                document.documentElement.classList.add('app-skin-dark');
                document.documentElement.classList.remove('app-skin-light');
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            } else {
                document.documentElement.classList.remove('app-skin-dark');
                document.documentElement.classList.add('app-skin-light');
                document.documentElement.setAttribute('data-bs-theme', 'light');
            }
        })();
    </script>
    <!--! END: Dark Mode Prevention Script !-->

    @php
        $brandName = \App\Models\System\PlatformSetting::where('key', 'brand_name')->value('value') ?: 'Grain SaaS';
        $favicon = \App\Models\System\PlatformSetting::where('key', 'favicon_path')->value('value');
    @endphp

    <!--! BEGIN: Apps Title-->
    <title>{{ $brandName }} || Dashboard</title>
    <!--! END:  Apps Title-->
    <!--! BEGIN: Favicon-->
    @if($favicon)
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}" />
    @else
        <link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon">
    @endif
    <!--! END: Favicon-->
    <!--! BEGIN: Bootstrap CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('Duralux-admin-1.0.0/assets/css/bootstrap.min.css') }}" />
    <!--! END: Bootstrap CSS-->
    <!--! BEGIN: Vendors CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('Duralux-admin-1.0.0/assets/vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('Duralux-admin-1.0.0/assets/vendors/css/daterangepicker.min.css') }}" />
    <!--! END: Vendors CSS-->
    <!--! BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('Duralux-admin-1.0.0/assets/css/theme.min.css') }}" />
    <!--! END: Custom CSS-->
    @stack('styles')
    <!--! HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries !-->
    <!--! WARNING: Respond.js doesn"t work if you view the page via file: !-->
    <!--[if lt IE 9]>
			<script src="https:oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
			<script src="https:oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->