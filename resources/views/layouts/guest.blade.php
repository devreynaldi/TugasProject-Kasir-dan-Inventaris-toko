<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'TokoKu') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Chakra+Petch:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/neon.css') }}">
    <script defer src="{{ asset('js/fx.js') }}"></script>
</head>
<body class="neon">
    <div class="aurora" aria-hidden="true"></div>
    <canvas id="bg-net" aria-hidden="true"></canvas>

    <div class="shell auth">
        <div class="auth-inner" data-stagger>
            <div class="auth-brand">
                <div class="brand">
                    <span class="brand-mark" aria-hidden="true">@include('layouts.logo')</span>
                    <span>TokoKu POS</span>
                </div>
                <p>Kasir dan stok toko dalam satu layar.</p>
            </div>
            <div class="glass auth-card">{{ $slot }}</div>
        </div>
    </div>
</body>
</html>
