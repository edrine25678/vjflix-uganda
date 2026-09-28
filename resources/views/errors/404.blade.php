<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - VJFlix Uganda</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-amber-500">404</h1>
        </div>
        <h2 class="text-3xl font-bold text-white mb-4">Page Not Found</h2>
        <p class="text-gray-400 mb-8 max-w-md mx-auto">
            Sorry, we couldn't find the page you're looking for. The movie or series might have been removed or the URL is incorrect.
        </p>
        <div class="flex gap-4 justify-center">
            <a href="{{ url('/') }}" class="bg-amber-500 hover:bg-amber-400 text-black px-6 py-3 rounded-lg font-semibold transition">
                Go Home
            </a>
            <a href="{{ url('/movies') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                Browse Movies
            </a>
        </div>
    </div>
</body>
</html>
