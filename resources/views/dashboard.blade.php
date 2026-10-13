<x-app-layout>
    <x-slot name="header"><h2>Dashboard</h2></x-slot>

    <div class="wrap py-6 space-y-5" data-stagger
         x-data="{ t: '' }" x-init="const f = () => t = new Date().toLocaleTimeString('id-ID'); f(); setInterval(f, 1000)">

        @php $max = max(1, $days->max('total')); @endphp

        <div class="grid-hero">
            <section class="glass hero">
                <div class="scan" aria-hidden="true"></div>
                <div>
                    <p class="hero-label">Penjualan hari ini</p>
                    <div class="hero-num"><span data-count="{{ $todayTotal }}" data-prefix="Rp ">Rp {{ number_format($todayTotal, 0, ',', '.') }}</span></div>
                    <p class="hero-sub"><b>{{ $todayCount }}</b> transaksi, jam <span x-text="t"></span></p>
                </div>
                <div class="bars" aria-label="Penjualan 7 hari terakhir">
                    @foreach ($days as $d)
                        <div class="bar {{ $loop->last ? 'today' : '' }}" title="{{ $d['label'] }}: Rp {{ number_format($d['total'], 0, ',', '.') }}">
                            <div class="col"><i style="--h: {{ $d['total'] > 0 ? max(6, round($d['total'] / $max * 100)) : 3 }}%; --k: {{ $loop->index }}"></i></div>
                            <span>{{ $d['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="tiles">
                <div class="glass tile">
                    <div class="tile-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"/><path d="M9 8h6M9 12h6"/></svg></div>
                    <div><div class="num" data-count="{{ $todayCount }}">{{ $todayCount }}</div><div class="lbl">Transaksi hari ini</div></div>
                </div>
                <div class="glass tile">
                    <div class="tile-icon warn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 2 20h20z"/><path d="M12 10v5M12 18v.01"/></svg></div>
                    <div><div class="num" data-count="{{ $lowStock->count() }}">{{ $lowStock->count() }}</div><div class="lbl">Produk stok menipis</div></div>
                </div>
            </div>
        </div>

        <div class="grid-2">
            <section class="glass panel">
                <h3>Stok menipis</h3>
                @forelse ($lowStock as $p)
                    <div class="row">
                        <span>{{ $p->name }}</span>
                        <span class="chip {{ $p->stock <= 0 ? 'crit' : 'low' }}">sisa {{ $p->stock }} dari min {{ $p->min_stock }}</span>
                    </div>
                @empty
                    <p class="empty">Semua stok aman.</p>
                @endforelse
            </section>

            <section class="glass panel">
                <h3>Transaksi terakhir</h3>
                @forelse ($recentSales as $s)
                    <a href="{{ route('sales.show', $s) }}" class="row">
                        <span>{{ $s->invoice_no }}</span>
                        <span>Rp {{ number_format($s->total, 0, ',', '.') }}</span>
                    </a>
                @empty
                    <p class="empty">Belum ada transaksi. Buka menu Kasir untuk memulai.</p>
                @endforelse
            </section>
        </div>
    </div>
</x-app-layout>
