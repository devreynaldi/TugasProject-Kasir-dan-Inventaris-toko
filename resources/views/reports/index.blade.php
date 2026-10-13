<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Laporan Penjualan</h2></x-slot>
    <div class="py-6 max-w-5xl mx-auto px-4 space-y-6">
        <form class="bg-white p-4 rounded shadow-sm flex flex-wrap gap-3 items-end">
            <div><label class="block text-xs mb-1">Dari</label><input type="date" name="from" value="{{ $from }}" class="rounded border-gray-300"></div>
            <div><label class="block text-xs mb-1">Sampai</label><input type="date" name="to" value="{{ $to }}" class="rounded border-gray-300"></div>
            <button class="bg-indigo-600 text-white rounded px-4 py-2">Tampilkan</button>
            @error('to') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
        </form>

        <div class="grid sm:grid-cols-2 gap-4">
            <div class="bg-white p-5 rounded shadow-sm"><div class="text-sm text-gray-500">Total pendapatan</div><div class="text-2xl font-bold">Rp {{ number_format($revenue, 0, ',', '.') }}</div></div>
            <div class="bg-white p-5 rounded shadow-sm"><div class="text-sm text-gray-500">Jumlah transaksi</div><div class="text-2xl font-bold">{{ $count }}</div></div>
        </div>

        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white p-5 rounded shadow-sm"><h3 class="font-semibold mb-3">Per hari</h3>
                @forelse ($daily as $d)
                    <div class="flex justify-between text-sm py-1 border-b"><span>{{ $d->day }} ({{ $d->trx }} trx)</span><span>Rp {{ number_format($d->total, 0, ',', '.') }}</span></div>
                @empty <p class="text-sm text-gray-500">Tidak ada data.</p> @endforelse
            </div>
            <div class="bg-white p-5 rounded shadow-sm"><h3 class="font-semibold mb-3">5 produk terlaris</h3>
                @forelse ($top as $t)
                    <div class="flex justify-between text-sm py-1 border-b"><span>{{ $t->product_name }}</span><span>{{ $t->qty }} pcs</span></div>
                @empty <p class="text-sm text-gray-500">Tidak ada data.</p> @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
