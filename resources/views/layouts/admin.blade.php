<!DOCTYPE html>

<html lang="en">
	<head><base href="">
		<meta charset="utf-8" />
		<title>{{ config('app.name') }} | Dashboard</title>
		<meta name="description" content="Updates and statistics" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link href="{{ asset('public/admin_assets/plugins/custom/fullcalendar/fullcalendar.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/admin_assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/admin_assets/plugins/custom/prismjs/prismjs.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/admin_assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/admin_assets/css/themes/layout/header/base/light.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/admin_assets/css/themes/layout/header/menu/light.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/admin_assets/css/themes/layout/brand/dark.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/admin_assets/css/themes/layout/aside/dark.css') }}" rel="stylesheet" type="text/css" />
		<link href="{{ asset('public/admin_assets/css/custom.css') }}" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
		<link href="{{ asset('public/images/favicon.png') }}" rel="icon">
		<link rel="stylesheet" type="text/css" href="{{ asset('public/admin_assets/css/daterangepicker.css') }}" />
		
	</head>
	<body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		@include('layouts.admin-base-element.mobile-header')
		<div class="d-flex flex-column flex-root">
			<div class="d-flex flex-row flex-column-fluid page">
				@include('layouts.admin-base-element.sidebar')
				<div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
					@include('layouts.admin-base-element.header')
					@yield('content')
					@include('layouts.admin-base-element.footer')
				</div>
			</div>
			@include('layouts.admin-base-element.common-modal')
			@include('layouts.admin-base-element.common-update-pass-modal')
		</div>
		@include('layouts.admin-base-element.header-profile')
		<div id="kt_scrolltop" class="scrolltop">
			<span class="svg-icon">
				<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Up-2.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<polygon points="0 0 24 0 24 24 0 24" />
						<rect fill="#000000" opacity="0.3" x="11" y="10" width="2" height="10" rx="1" />
						<path d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z" fill="#000000" fill-rule="nonzero" />
					</g>
				</svg>
			</span>
		</div>
		
		<script>   
			//basic config parameters
			var BASE_URL = "{{ url('/') }}";
			var ALERT_CLOSE = 5000;
			var USER_ROLE = "{{ Auth::user()->role == '1' ? 'admin' : ''  }}";
		</script>
		<!--begin::Global Config(global config for global JS scripts)-->
		<script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#6993FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1E9FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script>
		<script src="{{ asset('public/admin_assets/plugins/global/plugins.bundle.js') }}"></script>
		<script src="{{ asset('public/admin_assets/plugins/custom/prismjs/prismjs.bundle.js') }}"></script>
		<script src="{{ asset('public/admin_assets/js/scripts.bundle.js') }}"></script>
		<script src="{{ asset('public/admin_assets/plugins/custom/fullcalendar/fullcalendar.bundle.js') }}"></script>
		<script src="{{ asset('public/admin_assets/js/pages/widgets.js') }}"></script>
		<script src="{{ asset('public/admin_assets/plugins/custom/jquery-validation/jquery.validate.min.js') }}"></script>
		<script src="{{ asset('public/admin_assets/plugins/custom/jquery-validation/additional-methods.min.js') }}"></script>
		<script srd="{{ asset('public/admin_assets/js/moment.js') }}"></script>
		<script src="{{ asset('public/admin_assets/js/daterangepicker.js') }}"></script>
		<!-- begin::Page Specific js Contents -->
		@stack('scripts')
		<!-- end::Page Specific js Contents -->
		<!--Custom js by developer-->
		<script src="{{ asset('public/admin_assets/custom_js/main.js') }}"></script>
		@if(session()->has('custom_error'))
			<script>toastAlert("error",`{{session()->get('custom_error')}}`)</script>
		@endif
	</body>
</html>