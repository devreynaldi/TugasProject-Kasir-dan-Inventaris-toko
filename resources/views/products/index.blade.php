<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Produk</h2>
            <a href="{{ route('products.create') }}" class="bg-indigo-600 text-white text-sm rounded px-3 py-1.5">+ Produk</a>
        </div>
    </x-slot>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form class="mb-4"><input name="q" value="{{ request('q') }}" type="search" placeholder="Cari nama / SKU…" class="rounded border-gray-300 w-full sm:w-80"></form>
        <div class="bg-white rounded shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left"><tr>
                    <th class="p-3">SKU</th><th>Nama</th><th>Kategori</th><th class="text-right">Harga</th><th class="text-right">Stok</th><th>Status</th><th></th>
                </tr></thead>
                <tbody>
                @foreach ($products as $p)
                    <tr class="border-t">
                        <td class="p-3">{{ $p->sku }}</td><td>{{ $p->name }}</td><td>{{ $p->category?->name ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                        <td class="text-right {{ $p->stock <= $p->min_stock ? 'text-red-600 font-semibold' : '' }}">{{ $p->stock }}</td>
                        <td>{{ $p->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                        <td class="p-3 text-right whitespace-nowrap">
                            <a href="{{ route('products.edit', $p) }}" class="text-indigo-600">Ubah</a>
                            <form method="POST" action="{{ route('products.destroy', $p) }}" class="inline" onsubmit="return confirm('Hapus produk ini?')">
                                @csrf @method('DELETE') <button class="text-red-600 ml-2">Hapus</button></form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $products->links() }}</div>
    </div>
</x-app-layout>
