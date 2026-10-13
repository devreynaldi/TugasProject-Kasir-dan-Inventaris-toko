<x-app-layout>
    <x-slot name="header"><h2>Kasir</h2></x-slot>

    <script>
        function pos(products) {
            return {
                products, q: '', cart: [], paid: '', method: 'cash', error: '', busy: false,
                rp: n => 'Rp ' + new Intl.NumberFormat('id-ID').format(n),
                get filtered() {
                    const q = this.q.toLowerCase();
                    return this.products.filter(p => !q || p.name.toLowerCase().includes(q) || p.sku.toLowerCase().includes(q));
                },
                get total() { return this.cart.reduce((s, i) => s + i.price * i.qty, 0); },
                get change() { return Math.max(0, (Number(this.paid) || 0) - this.total); },
                get canPay() { return this.cart.length > 0 && (this.method !== 'cash' || (Number(this.paid) || 0) >= this.total); },
                add(p) {
                    const row = this.cart.find(i => i.id === p.id);
                    if (row) { if (row.qty < p.stock) row.qty++; }
                    else this.cart.push({ id: p.id, name: p.name, price: p.price, stock: p.stock, qty: 1 });
                    p.flash = true; setTimeout(() => p.flash = false, 360);
                },
                setQty(i, v) { i.qty = Math.min(i.stock, Math.max(1, parseInt(v) || 1)); },
                remove(i) { this.cart = this.cart.filter(r => r !== i); },
                async pay() {
                    this.error = ''; this.busy = true;
                    try {
                        const res = await fetch('{{ route('pos.checkout') }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json',
                                       'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
                            body: JSON.stringify({
                                items: this.cart.map(i => ({ product_id: i.id, qty: i.qty })),
                                paid: this.method === 'cash' ? Number(this.paid) || 0 : this.total,
                                payment_method: this.method,
                            }),
                        });
                        const data = await res.json();
                        if (!res.ok) { this.error = Object.values(data.errors || {}).flat().join(' ') || data.message; return; }
                        window.location = data.redirect;
                    } catch (e) { this.error = 'Gagal terhubung ke server.'; }
                    finally { this.busy = false; }
                },
            };
        }
    </script>

    <div class="wrap py-6 pos-grid" x-data="pos(@js($products))">
        <section class="glass" style="padding:1.1rem">
            <input x-model="q" type="search" placeholder="Cari nama atau SKU" class="w-full mb-4" autofocus>
            <div class="sku-grid">
                <template x-for="p in filtered" :key="p.id">
                    <button type="button" @click="add(p)" class="sku-tile tilt" :class="{ 'flash': p.flash, 'is-low': p.stock <= 5 }">
                        <span class="sku-code" x-text="p.sku"></span>
                        <span class="sku-name" x-text="p.name"></span>
                        <span class="sku-meta"><b x-text="rp(p.price)"></b><em x-text="'stok ' + p.stock"></em></span>
                    </button>
                </template>
                <p x-show="!filtered.length" class="empty">Produk tidak ditemukan. Coba kata kunci lain.</p>
            </div>
        </section>

        <aside class="glass cart">
            <h3 class="font-semibold text-lg">Keranjang</h3>

            <div>
                <template x-for="i in cart" :key="i.id">
                    <div class="cart-row">
                        <div><div x-text="i.name"></div><div class="text-xs text-gray-500" x-text="rp(i.price)"></div></div>
                        <div class="stepper">
                            <button type="button" @click="setQty(i, i.qty - 1)" aria-label="Kurangi">−</button>
                            <span x-text="i.qty"></span>
                            <button type="button" @click="setQty(i, i.qty + 1)" aria-label="Tambah">+</button>
                        </div>
                        <button type="button" class="x-btn" @click="remove(i)" aria-label="Hapus item">✕</button>
                    </div>
                </template>
                <p x-show="!cart.length" class="empty">Keranjang kosong. Pilih produk di sebelah kiri.</p>
            </div>

            <div class="total-display"><span class="text-gray-600">Total</span><b x-text="rp(total)"></b></div>

            <select x-model="method" class="w-full text-sm">
                <option value="cash">Tunai</option><option value="transfer">Transfer</option><option value="qris">QRIS</option>
            </select>

            <div x-show="method === 'cash'" class="space-y-2">
                <input x-model="paid" type="number" min="0" placeholder="Uang diterima" class="w-full text-sm">
                <div class="quick">
                    <button type="button" @click="paid = total">Uang pas</button>
                    <button type="button" @click="paid = 50000">50.000</button>
                    <button type="button" @click="paid = 100000">100.000</button>
                </div>
                <div class="flex justify-between text-sm"><span class="text-gray-600">Kembalian</span><b x-text="rp(change)" style="font-family:var(--ff-head)"></b></div>
            </div>

            <p x-show="error" x-text="error" class="text-sm text-red-600"></p>
            <button type="button" @click="pay()" :disabled="!cart.length || busy" :class="{ 'ready': canPay }"
                    class="btn-pay bg-indigo-600 disabled:opacity-40">Bayar</button>
        </aside>
    </div>
</x-app-layout>
