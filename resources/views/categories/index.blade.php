<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Kategori</h2></x-slot>
    <div class="py-6 max-w-2xl mx-auto px-4 space-y-4">
        <form method="POST" action="{{ route('categories.store') }}" class="bg-white p-4 rounded shadow-sm flex gap-2">
            @csrf
            <input name="name" placeholder="Nama kategori baru" class="flex-1 rounded border-gray-300" required>
            <button class="bg-indigo-600 text-white rounded px-4">Tambah</button>
        </form>
        @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

        <div class="bg-white rounded shadow-sm divide-y">
            @foreach ($categories as $c)
                <div class="p-3 flex items-center gap-2">
                    <form method="POST" action="{{ route('categories.update', $c) }}" class="flex flex-1 gap-2">
                        @csrf @method('PUT')
                        <input name="name" value="{{ $c->name }}" class="flex-1 rounded border-gray-300 text-sm">
                        <button class="text-indigo-600 text-sm">Simpan</button>
                    </form>
                    <span class="text-xs text-gray-500">{{ $c->products_count }} produk</span>
                    <form method="POST" action="{{ route('categories.destroy', $c) }}" onsubmit="return confirm('Hapus kategori?')">
                        @csrf @method('DELETE') <button class="text-red-600 text-sm">Hapus</button></form>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
