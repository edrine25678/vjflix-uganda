<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - VJFlix Uganda</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-black text-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <h1 class="text-9xl font-bold text-purple-500">500</h1>
        </div>
        <h2 class="text-3xl font-bold text-white mb-4">Server Error</h2>
        <p class="text-gray-400 mb-8 max-w-md mx-auto">
            Something went wrong on our end. Our team has been notified and we're working to fix it. Please try again later.
        </p>
        <div class="flex gap-4 justify-center">
            <a href="{{ url('/') }}" class="bg-amber-500 hover:bg-amber-400 text-black px-6 py-3 rounded-lg font-semibold transition">
                Go Home
            </a>
            <a href="{{ url('/movies') }}" class="bg-gray-800 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition">
                Browse Movies
            </a>
        </div>

        @if(config('app.debug') && isset($exception))
        <div class="mt-10 text-left max-w-4xl mx-auto bg-gray-900 rounded-xl p-6 text-sm overflow-auto">
            <p class="text-red-400 font-bold text-base mb-2">{{ get_class($exception) }}</p>
            <p class="text-yellow-300 mb-4">{{ $exception->getMessage() }}</p>
            <p class="text-gray-400 text-xs mb-4">{{ $exception->getFile() }}:{{ $exception->getLine() }}</p>
            <pre class="text-gray-400 text-xs whitespace-pre-wrap">{{ $exception->getTraceAsString() }}</pre>
        </div>
        @endif
    </div>
</body>
</html>
