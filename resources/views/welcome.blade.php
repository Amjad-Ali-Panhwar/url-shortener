<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel URL Shortener</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="antialiased bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-lg bg-white p-8 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-gray-800 mb-6 text-center">Shorten Your URL</h1>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('shorten.store') }}" method="POST" class="flex flex-col gap-4">
            @csrf
            <input
                type="url"
                name="url"
                id="url"
                placeholder="Enter your long URL here"
                required
                class="p-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-lg"
                value="{{ old('url') }}"
            >
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-md text-lg transition duration-300 ease-in-out"
            >
                Shorten
            </button>
        </form>

        @if (session('short_code'))
            <div class="mt-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md flex flex-col md:flex-row items-center justify-between">
                <p class="text-lg font-semibold mb-2 md:mb-0">Your Shortened URL:</p>
                <a
                    href="{{ url('/') . '/' . session('short_code') }}"
                    target="_blank"
                    class="break-all text-blue-600 hover:underline bg-white p-2 rounded-md border border-blue-300 flex-grow text-center md:text-left"
                >
                    {{ url('/') . '/' . session('short_code') }}
                </a>
                <button
                    onclick="copyToClipboard('{{ url('/') . '/' . session('short_code') }}')"
                    class="ml-0 md:ml-4 mt-2 md:mt-0 bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-md text-sm"
                >
                    Copy
                </button>
            </div>

            <script>
                function copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(function() {
                        alert('Short URL copied to clipboard!');
                    }, function(err) {
                        console.error('Could not copy text: ', err);
                    });
                }
            </script>
        @endif
    </div>
</body>
</html> 