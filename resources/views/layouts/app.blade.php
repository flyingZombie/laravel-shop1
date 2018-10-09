<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('title', 'Laravel Shop') - Laravel e-commence</title>
	<link rel="stylesheet" href="{{ mix('css/app.css') }}">
	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCoORpQwhqYeqsS-dRyFeO_oe1DuGt92w0&libraries=places"></script>
</head>
<body>
	<div id="app" class="{{ route_class()}}-page">
		@include('layouts._header')
		<div class="container">
			@yield('content')
		</div>
		@include('layouts._footer')
	</div>
	<script src="{{ mix('js/app.js') }}"></script>
	@yield('scriptsAfterJs')
</body>
</html>