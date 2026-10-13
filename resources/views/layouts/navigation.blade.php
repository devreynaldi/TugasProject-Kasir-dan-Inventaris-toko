@php
    $links = [['dashboard', 'Dashboard'], ['pos.index', 'Kasir'], ['sales.index', 'Transaksi']];
    if (auth()->user()->isAdmin()) {
        $links = array_merge($links, [['products.index', 'Produk'], ['categories.index', 'Kategori'], ['stock.index', 'Stok'], ['reports.index', 'Laporan']]);
    }
@endphp

<header class="topbar" x-data="{ open: false }">
    <div class="wrap nav">
        <a href="{{ route('dashboard') }}" class="brand">
            <span class="brand-mark" aria-hidden="true">@include('layouts.logo')</span>
            <span>TokoKu</span>
        </a>

        <button type="button" class="nav-toggle" @click="open = !open" :aria-expanded="open" aria-label="Buka menu">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>

        <nav class="nav-links" :class="{ 'is-open': open }">
            @foreach ($links as [$route, $label])
                <a href="{{ route($route) }}" class="nav-link {{ request()->routeIs(explode('.', $route)[0].'*') ? 'is-active' : '' }}">{{ $label }}</a>
            @endforeach
        </nav>

        <div class="nav-user" :class="{ 'is-open': open }">
            <a href="{{ route('profile.edit') }}" class="text-gray-600">{{ auth()->user()->name }}</a>
            <span class="role">{{ auth()->user()->role }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="text-red-600 hover:underline">Keluar</button>
            </form>
        </div>
    </div>
</header>

@if (session('status'))
    <div class="wrap mt-4">
        <div class="rounded bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-2">{{ session('status') }}</div>
    </div>
@endif
