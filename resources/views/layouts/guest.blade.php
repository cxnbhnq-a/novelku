<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'NovelKu') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --bg-color: #f4f6f8;
                --card-bg: #ffffff;
                --text-main: #111111;
                --text-muted: #555555;
                --border-color: #e5e7eb;
                --input-bg: #ffffff;
                --btn-bg: #111111;
                --btn-text: #ffffff;
                --shadow: 0 10px 25px rgba(0,0,0,0.05);
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    --bg-color: #000000;
                    --card-bg: #0b0b0b;
                    --text-main: #f5f5f5;
                    --text-muted: #aaaaaa;
                    --border-color: #222222;
                    --input-bg: #111111;
                    --btn-bg: #f5f5f5;
                    --btn-text: #000000;
                    --shadow: 0 10px 25px rgba(0,0,0,0.5);
                }
            }

            body {
                background: var(--bg-color);
                color: var(--text-main);
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased dark:text-gray-100">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0" style="background: var(--bg-color);">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current" style="color: var(--text-main);" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 shadow-md overflow-hidden sm:rounded-lg" style="background: var(--card-bg); border: 1px solid var(--border-color);">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
