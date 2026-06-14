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
<body class="font-sans antialiased">
    <div class="min-h-screen" style="background: var(--bg-color);">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="shadow" style="background: var(--card-bg); border-bottom: 1px solid var(--border-color);">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{-- Support both $slot (for components) and @yield('content') (for blade inheritance) --}}
            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>
    </div>
</body>
</html>