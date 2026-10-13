<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Struk {{ $sale->invoice_no }}</h2></x-slot>
    <div class="py-6 max-w-md mx-auto px-4">
        <div class="bg-white p-6 rounded shadow-sm text-sm font-mono space-y-1" id="receipt">
            <div class="text-center font-bold">TokoKu</div>
            <div class="text-center text-xs">{{ $sale->created_at->format('d/m/Y H:i') }} · Kasir: {{ $sale->cashier->name }}</div>
            <hr class="my-2">
            @foreach ($sale->items as $i)
                <div>{{ $i->product_name }}</div>
                <div class="flex justify-between"><span>{{ $i->qty }} × {{ number_format($i->price, 0, ',', '.') }}</span><span>{{ number_format($i->subtotal, 0, ',', '.') }}</span></div>
            @endforeach
            <hr class="my-2">
            <div class="flex justify-between font-bold"><span>Total</span><span>Rp {{ number_format($sale->total, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>Bayar ({{ strtoupper($sale->payment_method) }})</span><span>{{ number_format($sale->paid, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>Kembali</span><span>{{ number_format($sale->change_amount, 0, ',', '.') }}</span></div>
        </div>
        <div class="mt-4 flex gap-3">
            <button onclick="window.print()" class="bg-indigo-600 text-white rounded px-4 py-2 text-sm">Cetak</button>
            <a href="{{ route('pos.index') }}" class="px-4 py-2 text-sm text-gray-600">Transaksi baru</a>
        </div>
    </div>
</x-app-layout>
