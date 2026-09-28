<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - VJFlix Uganda</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-red-500">403</h1>
        </div>
        <h2 class="text-3xl font-bold text-white mb-4">Access Denied</h2>
        <p class="text-gray-400 mb-8 max-w-md mx-auto">
            You don't have permission to access this page. Please log in or contact support if you believe this is an error.
        </p>
        <div class="flex gap-4 justify-center">
            @if(auth()->guest())
                <a href="{{ route('login') }}" class="bg-amber-500 hover:bg-amber-400 text-black px-6 py-3 rounded-lg font-semibold transition">
                    Log In
                </a>
            @endif
            <a href="{{ url('/') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                Go Home
            </a>
        </div>
    </div>
</body>
</html>
