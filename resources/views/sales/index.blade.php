<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Riwayat Transaksi</h2></x-slot>
    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded shadow-sm overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left"><tr><th class="p-3">Invoice</th><th>Waktu</th><th>Kasir</th><th>Metode</th><th class="text-right p-3">Total</th></tr></thead>
                <tbody>
                @foreach ($sales as $s)
                    <tr class="border-t hover:bg-gray-50">
                        <td class="p-3"><a href="{{ route('sales.show', $s) }}" class="text-indigo-600">{{ $s->invoice_no }}</a></td>
                        <td>{{ $s->created_at->format('d/m/Y H:i') }}</td><td>{{ $s->cashier->name }}</td><td>{{ strtoupper($s->payment_method) }}</td>
                        <td class="text-right p-3">Rp {{ number_format($s->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $sales->links() }}</div>
    </div>
</x-app-layout>
