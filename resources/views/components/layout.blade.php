<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title ?? 'VJFlix Uganda — Your Movies. Your VJs. Your Language.' }}</title>
    <meta name="description" content="Watch movies and series translated by Uganda's top Video Jockeys (VJs) like VJ Junior, VJ Jingo, VJ Emmy, VJ Ice P, and more.">

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
    </style>
</head>
<body class="min-h-full flex flex-col antialiased selection:bg-amber-500 selection:text-black">

    <div class="flex-grow">
        {{ $slot }}
    </div>

    <x-flash />

    @livewireScripts
    @stack('scripts')
</body>
</html>
