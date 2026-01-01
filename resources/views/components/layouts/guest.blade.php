<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Login Sistem Monitoring' }}</title>

    <link rel="icon" type="image/png" href="{{ asset('assets/logo-sucofindo.png') }}?v=2025">
    <link rel="shortcut icon" href="{{ asset('assets/logo-sucofindo.png') }}?v=2025">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-['Space_Grotesk'] antialiased bg-gray-100 text-gray-900">
    
    {{ $slot }}

</body>
</html>