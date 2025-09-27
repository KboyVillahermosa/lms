<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen bg-gray-100">
            <div class="min-h-screen grid grid-cols-1 md:grid-cols-2">
                <!-- Left: auth form (no card) -->
                <div class="flex items-center justify-center p-6">
                    <div class="w-full max-w-md px-6 py-12">
                        {{ $slot }}
                    </div>
                </div>

                <!-- Right: cover image with overlay and marketing text (hidden on small screens) -->
                <div class="hidden md:block relative">
                    <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/login-logo.png') }}');"></div>
                    <div class="absolute inset-0 bg-black bg-opacity-50"></div>
                    <div class="relative h-full flex items-center justify-center p-8">
                        <div class="text-center max-w-xs">
                            <h2 class="text-2xl font-semibold text-white">ATC Tagum College</h2>
                            <p class="mt-3 text-sm text-gray-200">{{ __('Deliver and track online courses, submit and grade assignments, and monitor learner progress — all in one secure platform.') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
