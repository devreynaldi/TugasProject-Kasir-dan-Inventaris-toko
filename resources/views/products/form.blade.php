<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">{{ $product->exists ? 'Ubah' : 'Tambah' }} Produk</h2></x-slot>
    <div class="py-6 max-w-2xl mx-auto px-4">
        <form method="POST" action="{{ $product->exists ? route('products.update', $product) : route('products.store') }}" class="bg-white p-6 rounded shadow-sm space-y-4">
            @csrf @if ($product->exists) @method('PUT') @endif

            @php $fields = [['sku','SKU','text'],['name','Nama','text'],['price','Harga jual (Rp)','number'],['cost','Harga modal (Rp)','number'],['min_stock','Batas stok minimum','number']];
                 if (! $product->exists) $fields[] = ['stock','Stok awal','number']; @endphp

            @foreach ($fields as [$name, $label, $type])
                <div>
                    <label class="block text-sm font-medium mb-1">{{ $label }}</label>
                    <input name="{{ $name }}" type="{{ $type }}" value="{{ old($name, $product->$name) }}" class="w-full rounded border-gray-300">
                    @error($name) <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
            @endforeach

            <div>
                <label class="block text-sm font-medium mb-1">Kategori</label>
                <select name="category_id" class="w-full rounded border-gray-300">
                    <option value="">— Tanpa kategori —</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))> Aktif dijual</label>
            @if ($product->exists) <p class="text-xs text-gray-500">Stok saat ini {{ $product->stock }} — ubah lewat menu Stok.</p> @endif

            <div class="flex gap-3">
                <button class="bg-indigo-600 text-white rounded px-4 py-2">Simpan</button>
                <a href="{{ route('products.index') }}" class="px-4 py-2 text-gray-600">Batal</a>
            </div>
        </form>
    </div>
</x-app-layout>
