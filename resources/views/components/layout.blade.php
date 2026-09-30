<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'VJFlix Uganda — Your Movies. Your VJs. Your Language.' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#0b0f19">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Bebas+Neue&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/flickity@2/dist/flickity.min.css">
    <script src="https://unpkg.com/flickity@2/dist/flickity.pkgd.min.js"></script>
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0f19; }
        .font-display { font-family: 'Bebas Neue', sans-serif; }
        input:not([class*="text-white"]):not([class*="bg-slate"]):not([class*="bg-black"]):not([type="hidden"]),
        textarea:not([class*="text-white"]):not([class*="bg-slate"]):not([class*="bg-black"]) {
            color: #000000 !important;
        }
        input.bg-white, input.bg-gray-100, input.bg-slate-100 {
            color: #000000 !important;
        }
    </style>
</head>
<body class="min-h-full flex flex-col antialiased selection:bg-amber-500 selection:text-black pb-16 md:pb-0">

    <div class="flex-grow">
        {{ $slot }}
    </div>

    <x-bottom-nav />
    <x-flash />

    @livewireScripts
    @stack('scripts')
</body>
</html>
