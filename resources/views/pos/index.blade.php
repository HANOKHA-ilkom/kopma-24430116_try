@extends('layouts.app')

@section('title', 'Kasir POS Toko KOPMA')
@section('page_title', 'Point of Sale (POS) Toko KOPMA')
@section('page_subtitle', 'Transaksi penjualan minimarket & merchandise koperasi mahasiswa')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- Kolom Kiri: Produk Katalog (8 Kolom) -->
    <div class="lg:col-span-7 xl:col-span-8 space-y-4">
        <!-- Search & Category Filters -->
        <div class="glass-card rounded-2xl p-4 border border-slate-200/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-72">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-3 text-slate-400"></i>
                <input type="text" id="productSearch" onkeyup="filterProducts()" placeholder="Cari nama produk / kode..."
                    class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <!-- Category Pills -->
            <div class="flex items-center gap-1.5 overflow-x-auto w-full sm:w-auto pb-1 sm:pb-0">
                <button type="button" onclick="filterCategory('all')" class="cat-pill px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-900 text-white transition-all flex-shrink-0">
                    Semua
                </button>
                @foreach($categories as $c)
                <button type="button" onclick="filterCategory('cat-{{ $c->id }}')" class="cat-pill px-3 py-1.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all flex-shrink-0">
                    {{ $c->nama }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-4" id="productGrid">
            @foreach($produks as $p)
            <div class="product-item cat-{{ $p->kategori_produk_id }} glass-card rounded-2xl p-4 border border-slate-200/80 shadow-sm hover:border-emerald-500 hover:shadow-md transition-all cursor-pointer flex flex-col justify-between"
                 onclick="addToCart({{ $p->id }}, '{{ addslashes($p->nama_produk) }}', {{ $p->harga_jual }}, {{ $p->stok }})"
                 data-name="{{ strtolower($p->nama_produk) }}" data-code="{{ strtolower($p->kode_produk) }}">
                <div>
                    <div class="w-full h-24 rounded-xl bg-emerald-50/60 border border-emerald-100/60 flex items-center justify-center text-emerald-700 font-black text-2xl mb-3 shadow-inner">
                        <i data-lucide="package" class="w-8 h-8 opacity-70"></i>
                    </div>
                    <span class="text-[10px] font-mono font-bold text-slate-400 uppercase">{{ $p->kode_produk }}</span>
                    <h5 class="text-xs font-bold text-slate-800 line-clamp-2 mt-0.5 leading-snug">{{ $p->nama_produk }}</h5>
                </div>

                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400">Harga:</span>
                        <p class="text-xs font-black text-emerald-700">Rp {{ number_format($p->harga_jual, 0, ',', '.') }}</p>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $p->stok <= 10 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">
                        Stok: {{ $p->stok }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Kolom Kanan: Keranjang Kasir & Checkout (4 Kolom / Sticky) -->
    <div class="lg:col-span-5 xl:col-span-4 sticky top-24">
        <div class="glass-card rounded-2xl p-5 border border-slate-200/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <i data-lucide="shopping-cart" class="w-5 h-5 text-emerald-600"></i>
                    <h4 class="text-sm font-bold text-slate-900">Keranjang Belanja</h4>
                </div>
                <button type="button" onclick="clearCart()" class="text-[11px] text-rose-600 hover:underline font-semibold">
                    Kosongkan
                </button>
            </div>

            <!-- Cart Items Container -->
            <div class="my-4 max-h-60 overflow-y-auto space-y-2.5 pr-1" id="cartContainer">
                <div class="text-center py-8 text-slate-400 text-xs" id="emptyCartMessage">
                    <i data-lucide="shopping-bag" class="w-8 h-8 mx-auto mb-2 opacity-40"></i>
                    Keranjang masih kosong.<br>Klik produk di sebelah kiri untuk menambahkan.
                </div>
            </div>

            <!-- Checkout Form -->
            <form action="{{ route('pos.checkout') }}" method="POST" id="checkoutForm" class="space-y-3.5 pt-3 border-t border-slate-100">
                @csrf
                <div id="hiddenInputs"></div>

                <!-- Pilih Anggota (Diskon 5%) -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Pembeli / Anggota KOPMA
                    </label>
                    <select name="member_id" id="memberSelect" onchange="calculateCart()"
                        class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-medium focus:bg-white focus:outline-none">
                        <option value="">-- Pembeli Umum (Non-Anggota) --</option>
                        @foreach($members as $m)
                        <option value="{{ $m->id }}">
                            {{ $m->user->name }} - {{ $m->nim }} (Diskon Anggota 5%)
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rincian Total Bayar -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-1.5 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span id="subtotalDisplay" class="font-bold text-slate-800">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-emerald-700 font-semibold" id="discountRow" style="display: none;">
                        <span>Diskon Anggota (5%):</span>
                        <span id="discountDisplay">- Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm font-extrabold text-slate-900 pt-1.5 border-t border-slate-200">
                        <span>Total Akhir:</span>
                        <span id="totalDisplay" class="text-emerald-700 text-base">Rp 0</span>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Metode Pembayaran</label>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="tunai" checked class="peer sr-only" onchange="onPaymentMethodChange()">
                            <div class="p-2 text-center rounded-xl border border-slate-200 font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 hover:bg-slate-50 transition-all">
                                💵 Tunai
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="qris" class="peer sr-only" onchange="onPaymentMethodChange()">
                            <div class="p-2 text-center rounded-xl border border-slate-200 font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 hover:bg-slate-50 transition-all">
                                📱 QRIS
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="saldo_kopma" class="peer sr-only" onchange="onPaymentMethodChange()">
                            <div class="p-2 text-center rounded-xl border border-slate-200 font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 hover:bg-slate-50 transition-all">
                                💳 Saldo KOPMA
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="metode_pembayaran" value="transfer" class="peer sr-only" onchange="onPaymentMethodChange()">
                            <div class="p-2 text-center rounded-xl border border-slate-200 font-bold peer-checked:bg-emerald-600 peer-checked:text-white peer-checked:border-emerald-600 hover:bg-slate-50 transition-all">
                                🏦 Transfer
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Nominal Bayar & Kembalian -->
                <div id="cashInputArea">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Uang Diterima (Rp)</label>
                    <input type="number" name="bayar" id="inputBayar" min="0" step="500" placeholder="0" onkeyup="calculateChange()"
                        class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <div class="flex justify-between items-center text-xs mt-1 text-slate-500">
                        <span>Kembalian:</span>
                        <span id="changeDisplay" class="font-extrabold text-slate-900">Rp 0</span>
                    </div>
                </div>

                <button type="submit" id="btnCheckout" disabled
                    class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center justify-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    <span>Bayar & Terbitkan Struk</span>
                </button>
            </form>
        </div>
    </div>

</div>

@push('scripts')
<script>
    let cart = {};

    function addToCart(id, name, price, stock) {
        if (cart[id]) {
            if (cart[id].qty < stock) {
                cart[id].qty += 1;
            } else {
                alert('Stok maksimal tercapai: ' + stock);
            }
        } else {
            cart[id] = { id: id, name: name, price: price, stock: stock, qty: 1 };
        }
        renderCart();
    }

    function updateQty(id, delta) {
        if (!cart[id]) return;
        cart[id].qty += delta;
        if (cart[id].qty <= 0) {
            delete cart[id];
        } else if (cart[id].qty > cart[id].stock) {
            cart[id].qty = cart[id].stock;
            alert('Stok maksimal tercapai');
        }
        renderCart();
    }

    function removeItem(id) {
        delete cart[id];
        renderCart();
    }

    function clearCart() {
        cart = {};
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cartContainer');
        const emptyMsg = document.getElementById('emptyCartMessage');
        const keys = Object.keys(cart);

        if (keys.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8 text-slate-400 text-xs" id="emptyCartMessage">
                    <svg class="w-8 h-8 mx-auto mb-2 opacity-40" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><path d="M3 6h18"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    Keranjang masih kosong.<br>Klik produk di sebelah kiri untuk menambahkan.
                </div>`;
            document.getElementById('btnCheckout').disabled = true;
        } else {
            let html = '';
            keys.forEach(k => {
                const item = cart[k];
                html += `
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                    <div class="overflow-hidden pr-2">
                        <p class="font-bold text-slate-800 truncate">${item.name}</p>
                        <span class="text-[11px] text-emerald-700 font-semibold">Rp ${item.price.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="flex items-center gap-1.5 flex-shrink-0">
                        <button type="button" onclick="updateQty(${item.id}, -1)" class="w-6 h-6 rounded-lg bg-slate-200 hover:bg-slate-300 font-bold flex items-center justify-center text-slate-700">-</button>
                        <span class="w-6 text-center font-bold text-slate-800">${item.qty}</span>
                        <button type="button" onclick="updateQty(${item.id}, 1)" class="w-6 h-6 rounded-lg bg-slate-200 hover:bg-slate-300 font-bold flex items-center justify-center text-slate-700">+</button>
                        <button type="button" onclick="removeItem(${item.id})" class="ml-1 text-rose-500 hover:text-rose-700">✕</button>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
            document.getElementById('btnCheckout').disabled = false;
        }

        calculateCart();
    }

    function calculateCart() {
        let subtotal = 0;
        const hiddenInputs = document.getElementById('hiddenInputs');
        hiddenInputs.innerHTML = '';

        let idx = 0;
        Object.keys(cart).forEach(k => {
            const item = cart[k];
            subtotal += item.price * item.qty;

            hiddenInputs.innerHTML += `
                <input type="hidden" name="items[${idx}][id]" value="${item.id}">
                <input type="hidden" name="items[${idx}][qty]" value="${item.qty}">
            `;
            idx++;
        });

        const isMember = document.getElementById('memberSelect').value !== '';
        const diskon = isMember ? Math.round(subtotal * 0.05) : 0;
        const total = subtotal - diskon;

        document.getElementById('subtotalDisplay').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');

        const discountRow = document.getElementById('discountRow');
        if (diskon > 0) {
            discountRow.style.display = 'flex';
            document.getElementById('discountDisplay').innerText = '- Rp ' + diskon.toLocaleString('id-ID');
        } else {
            discountRow.style.display = 'none';
        }

        document.getElementById('totalDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');

        // Auto pasang nominal jika non-tunai
        const metode = document.querySelector('input[name="metode_pembayaran"]:checked')?.value || 'tunai';
        if (metode !== 'tunai') {
            document.getElementById('inputBayar').value = total;
        }

        calculateChange();
    }

    function onPaymentMethodChange() {
        const metode = document.querySelector('input[name="metode_pembayaran"]:checked')?.value || 'tunai';
        const totalText = document.getElementById('totalDisplay').innerText.replace(/[^\d]/g, '');
        const total = parseInt(totalText) || 0;

        if (metode !== 'tunai') {
            document.getElementById('inputBayar').value = total;
        }
        calculateChange();
    }

    function calculateChange() {
        const totalText = document.getElementById('totalDisplay').innerText.replace(/[^\d]/g, '');
        const total = parseInt(totalText) || 0;
        const bayar = parseInt(document.getElementById('inputBayar').value) || 0;
        const kembali = Math.max(0, bayar - total);

        document.getElementById('changeDisplay').innerText = 'Rp ' + kembali.toLocaleString('id-ID');
    }

    function filterProducts() {
        const search = document.getElementById('productSearch').value.toLowerCase();
        document.querySelectorAll('.product-item').forEach(el => {
            const name = el.getAttribute('data-name');
            const code = el.getAttribute('data-code');
            if (name.includes(search) || code.includes(search)) {
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
            }
        });
    }

    function filterCategory(catClass) {
        document.querySelectorAll('.cat-pill').forEach(btn => {
            btn.classList.remove('bg-slate-900', 'text-white');
            btn.classList.add('bg-slate-100', 'text-slate-700');
        });
        event.target.classList.remove('bg-slate-100', 'text-slate-700');
        event.target.classList.add('bg-slate-900', 'text-white');

        document.querySelectorAll('.product-item').forEach(el => {
            if (catClass === 'all' || el.classList.contains(catClass)) {
                el.style.display = 'flex';
            } else {
                el.style.display = 'none';
            }
        });
    }
</script>
@endpush
@endsection
