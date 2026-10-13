<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Manajemen Stok</h2></x-slot>
    <div class="py-6 max-w-5xl mx-auto px-4 grid md:grid-cols-2 gap-6">
        <form method="POST" action="{{ route('stock.store') }}" class="bg-white p-5 rounded shadow-sm space-y-3 self-start">
            @csrf
            <h3 class="font-semibold">Stok masuk / penyesuaian</h3>
            <select name="product_id" class="w-full rounded border-gray-300" required>
                @foreach ($products as $p) <option value="{{ $p->id }}">{{ $p->name }} (stok {{ $p->stock }})</option> @endforeach
            </select>
            <select name="type" class="w-full rounded border-gray-300">
                <option value="in">Stok masuk (restock)</option><option value="adjust">Penyesuaian (+/−)</option>
            </select>
            <input name="qty" type="number" placeholder="Jumlah" class="w-full rounded border-gray-300" required>
            <input name="note" placeholder="Catatan (opsional)" class="w-full rounded border-gray-300">
            @foreach ($errors->all() as $e) <p class="text-sm text-red-600">{{ $e }}</p> @endforeach
            <button class="bg-indigo-600 text-white rounded px-4 py-2">Simpan</button>
        </form>

        <div class="bg-white p-5 rounded shadow-sm">
            <h3 class="font-semibold mb-3">20 pergerakan terakhir</h3>
            @foreach ($movements as $m)
                <div class="flex justify-between text-sm py-1 border-b">
                    <span>{{ $m->product->name }} <span class="text-gray-400">· {{ $m->type }} · {{ $m->note }}</span></span>
                    <span class="{{ $m->qty < 0 ? 'text-red-600' : 'text-green-600' }}">{{ $m->qty > 0 ? '+' : '' }}{{ $m->qty }}</span>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
