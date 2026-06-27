@extends('layouts.guest')

@section('title', 'Checkout — TobaNiaga')
@section('hide_navbar', true)

@section('content')

<header class="relative z-20 border-b border-lake-900/10">
    <nav class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between py-6">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded">
            <span class="w-2.5 h-7 ulos-stripe-v rounded-sm"></span>
            <span class="font-display text-2xl font-semibold tracking-tight text-lake-900">TobaNiaga</span>
        </a>
        <a href="{{ route('customer.keranjang.index') }}" class="text-sm text-ink/60 hover:text-lake-900 transition-colors">
            ← Kembali ke Keranjang
        </a>
    </nav>
</header>

<section class="bg-paper min-h-screen">
    <div class="max-w-4xl mx-auto px-6 lg:px-10 py-10 lg:py-14"
         x-data="checkoutPage()"
         x-init="init()">

        <h1 class="font-display text-2xl lg:text-3xl font-medium text-lake-900 mb-8">Checkout</h1>

        @if (session('error'))
            <div class="mb-5 px-4 py-3 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-sm text-ulos-maroon">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('customer.checkout.store') }}" method="POST">
            @csrf
            <input type="hidden" name="umkm_id" value="{{ $umkm->id }}">
            <input type="hidden" name="promo_kode" :value="promoKode">
            @foreach ($keranjang_ids as $kid)
                <input type="hidden" name="keranjang_ids[]" value="{{ $kid }}">
            @endforeach

            <div class="grid lg:grid-cols-3 gap-6">

                {{-- ── Kiri: Form ── --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Metode Pengiriman --}}
                    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
                        <h2 class="text-sm font-semibold text-lake-900 mb-4">Metode Pengiriman</h2>
                        <div class="space-y-2">
                            @foreach ($metodePengiriman as $mp)
                                <label class="flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                                       :class="metode == '{{ $mp->id }}' ? 'border-lake-800 bg-lake-50' : 'border-lake-900/10 hover:bg-lake-50'">
                                    <input type="radio" name="metode_pengiriman_id" value="{{ $mp->id }}"
                                           x-model="metode"
                                           @change="hitungTotal()"
                                           class="accent-lake-800">
                                    <div>
                                        <p class="text-sm font-medium text-lake-900">{{ $mp->label }}</p>
                                        @if ($mp->kode === 'kurir')
                                            <p class="text-xs text-ink/50 mt-0.5">Rp 10.000 (flat sementara)</p>
                                        @else
                                            <p class="text-xs text-ink/50 mt-0.5">Gratis</p>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Alamat Pengiriman --}}
                    @php $metodeKurir = $metodePengiriman->firstWhere('kode', 'kurir'); @endphp
                    @if ($metodeKurir)
                        <div class="bg-paper border border-lake-900/10 rounded-xl p-5"
                             x-show="metode == '{{ $metodeKurir->id }}'">
                            <h2 class="text-sm font-semibold text-lake-900 mb-4">Alamat Pengiriman</h2>

                            @if ($alamatList->isEmpty())
                                <p class="text-sm text-ink/50 mb-3">Kamu belum punya alamat tersimpan.</p>
                            @else
                                <div class="space-y-2 mb-4">
                                    @foreach ($alamatList as $al)
                                        <label class="flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors"
                                               :class="alamat == '{{ $al->id }}' ? 'border-lake-800 bg-lake-50' : 'border-lake-900/10 hover:bg-lake-50'">
                                            <input type="radio" name="alamat_id" value="{{ $al->id }}"
                                                   x-model="alamat"
                                                   class="accent-lake-800 mt-0.5">
                                            <div>
                                                <p class="text-sm font-medium text-lake-900">
                                                    {{ $al->label }}
                                                    @if ($al->is_utama)
                                                        <span class="ml-1 text-[10px] bg-lake-100 text-lake-800 px-1.5 py-0.5 rounded">Utama</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-ink/60 mt-0.5">{{ $al->nama_penerima }} · {{ $al->no_hp_penerima }}</p>
                                                <p class="text-xs text-ink/50 mt-0.5">{{ $al->alamat_lengkap }}, {{ $al->kelurahan }}, {{ $al->kecamatan }}, {{ $al->kota }}</p>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif

                            <button type="button" @click="modalAlamat = true"
                                    class="text-sm text-lake-800 hover:underline font-medium">
                                + Tambah alamat baru
                            </button>
                        </div>
                    @endif

                    {{-- Catatan --}}
                    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
                        <h2 class="text-sm font-semibold text-lake-900 mb-3">
                            Catatan untuk Penjual
                            <span class="font-normal text-ink/40">(opsional)</span>
                        </h2>
                        <textarea name="catatan_customer" rows="3" maxlength="500"
                                  placeholder="Contoh: tolong dikemas rapi, jangan dilipat..."
                                  class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-lake-800 resize-none bg-paper text-ink placeholder-ink/30">{{ old('catatan_customer') }}</textarea>
                    </div>
                </div>

                {{-- ── Kanan: Ringkasan ── --}}
                <div class="space-y-4">
                    <div class="bg-paper border border-lake-900/10 rounded-xl p-5 sticky top-6">
                        <h2 class="text-sm font-semibold text-lake-900 mb-4">Ringkasan Pesanan</h2>

                        <p class="text-xs text-ink/50 mb-3 font-medium uppercase tracking-wide">{{ $umkm->nama_umkm }}</p>

                        {{-- Item produk --}}
                        <div class="space-y-3 mb-4">
                            @foreach ($items as $item)
                                @php $foto = $item->produk->fotoProduk->first(); @endphp
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-lake-50 border border-lake-900/10 overflow-hidden flex-shrink-0">
                                        @if ($foto)
                                            <img src="{{ Str::startsWith($foto->url_foto, ['http://', 'https://']) ? $foto->url_foto : Storage::url($foto->url_foto) }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-lake-900/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs text-ink/70 line-clamp-1">{{ $item->produk->nama_produk }}</p>
                                        <p class="text-xs text-ink/50">{{ $item->jumlah }} × Rp {{ number_format($item->produk->harga, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="text-xs font-medium text-lake-900 flex-shrink-0">
                                        Rp {{ number_format($item->produk->harga * $item->jumlah, 0, ',', '.') }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Promo input --}}
                        <div class="border-t border-lake-900/8 pt-4 mb-3">
                            <label class="block text-xs font-medium text-lake-900 mb-1.5">
                                Kode Promo <span class="text-ink/40 font-normal">(opsional)</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="text"
                                       x-model="inputKode"
                                       @input="inputKode = inputKode.toUpperCase()"
                                       :disabled="promoApplied"
                                       placeholder="Masukkan kode promo"
                                       class="flex-1 border border-lake-900/15 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-lake-900/20 disabled:opacity-50 disabled:bg-lake-50 min-w-0">
                                <button type="button"
                                        @click="promoApplied ? hapusPromo() : terapkanPromo()"
                                        :disabled="loadingPromo || (!promoApplied && !inputKode)"
                                        :class="promoApplied
                                            ? 'border border-red-200 text-red-600 hover:bg-red-50'
                                            : 'bg-lake-900 text-paper hover:bg-lake-800 disabled:opacity-40'"
                                        class="px-3 py-2 rounded-lg text-xs font-medium transition-colors flex-shrink-0"
                                        x-text="promoApplied ? 'Hapus' : (loadingPromo ? '...' : 'Pakai')">
                                </button>
                            </div>
                            {{-- Feedback --}}
                            <p x-show="promoMessage"
                               x-text="promoMessage"
                               :class="promoApplied ? 'text-green-600' : 'text-red-600'"
                               class="text-xs mt-1.5"></p>
                        </div>

                        {{-- Kalkulasi --}}
                        <div class="space-y-1.5 text-sm">
                            <div class="flex justify-between text-ink/60">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-ink/60">
                                <span>Ongkos Kirim</span>
                                <span x-text="ongkosKirim > 0 ? 'Rp 10.000' : 'Gratis'"></span>
                            </div>
                            <div x-show="promoApplied" class="flex justify-between text-green-600 font-medium">
                                <span x-text="'Promo ' + promoKode"></span>
                                <span x-text="'- ' + formatRupiah(diskon)"></span>
                            </div>
                            <div class="flex justify-between font-bold text-lake-900 pt-2 border-t border-lake-900/8">
                                <span>Total</span>
                                <span x-text="formatRupiah(total)"></span>
                            </div>
                        </div>

                        <button type="submit"
                                class="mt-5 w-full px-5 py-3 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-900/90 transition-colors">
                            Lanjut ke Pembayaran
                        </button>
                    </div>
                </div>

            </div>
        </form>

        {{-- Modal Tambah Alamat --}}
        <div x-show="modalAlamat" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
             @click.self="modalAlamat = false"
             x-data="{
                 kecamatanList: [], desaList: [],
                 kecamatanDipilih: '', loadingKec: false, loadingDesa: false,
                 async loadKecamatan() {
                     this.loadingKec = true;
                     try {
                         const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/districts/1206.json');
                         this.kecamatanList = await res.json();
                     } catch(e) {}
                     this.loadingKec = false;
                 },
                 async loadDesa() {
                     this.desaList = [];
                     const kec = this.kecamatanList.find(k => k.name === this.kecamatanDipilih);
                     if (!kec) return;
                     this.loadingDesa = true;
                     try {
                         const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/villages/' + kec.id + '.json');
                         this.desaList = await res.json();
                     } catch(e) {}
                     this.loadingDesa = false;
                 },
                 init() { this.loadKecamatan(); }
             }">
            <div class="bg-paper rounded-xl max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
                <h3 class="font-display text-lg font-medium text-lake-900 mb-4">Tambah Alamat Baru</h3>
                <form action="{{ route('customer.alamat.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="redirect_to" value="{{ route('customer.checkout.resume') }}">
                    <div>
                        <label class="text-xs text-ink/60 mb-1 block">Label Alamat</label>
                        <input type="text" name="label" placeholder="Rumah, Kantor, dll" required
                               class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-lake-800">
                    </div>
                    <div>
                        <label class="text-xs text-ink/60 mb-1 block">Nama Penerima</label>
                        <input type="text" name="nama_penerima" required
                               class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-lake-800">
                    </div>
                    <div>
                        <label class="text-xs text-ink/60 mb-1 block">No. HP Penerima</label>
                        <input type="text" name="no_hp_penerima" required
                               class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-lake-800">
                    </div>
                    <div>
                        <label class="text-xs text-ink/60 mb-1 block">Provinsi</label>
                        <input type="text" name="provinsi" value="Sumatera Utara" readonly
                               class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2 bg-lake-50 text-ink/50 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="text-xs text-ink/60 mb-1 block">Kota/Kabupaten</label>
                        <input type="text" name="kota" value="Kabupaten Toba" readonly
                               class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2 bg-lake-50 text-ink/50 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="text-xs text-ink/60 mb-1 block">Kecamatan</label>
                        <select name="kecamatan" x-model="kecamatanDipilih" @change="loadDesa()" required
                                :disabled="loadingKec"
                                class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-lake-800 bg-paper">
                            <option value="" x-text="loadingKec ? 'Memuat...' : 'Pilih kecamatan...'"></option>
                            <template x-for="kec in kecamatanList" :key="kec.id">
                                <option :value="kec.name" x-text="kec.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-ink/60 mb-1 block">Desa / Kelurahan</label>
                        <select name="kelurahan" required
                                :disabled="desaList.length === 0 || loadingDesa"
                                class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-lake-800 bg-paper disabled:opacity-50">
                            <option value="" x-text="loadingDesa ? 'Memuat...' : (kecamatanDipilih ? 'Pilih desa...' : 'Pilih kecamatan dulu')"></option>
                            <template x-for="desa in desaList" :key="desa.id">
                                <option :value="desa.name" x-text="desa.name"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="text-xs text-ink/60 mb-1 block">Kode Pos <span class="text-ink/30">(opsional)</span></label>
                        <input type="text" name="kode_pos" placeholder="22384"
                               class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-lake-800">
                    </div>
                    <div>
                        <label class="text-xs text-ink/60 mb-1 block">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" rows="2" required
                                  placeholder="Nama jalan, no. rumah, RT/RW..."
                                  class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-lake-800 resize-none"></textarea>
                    </div>
                    <div class="flex gap-2 pt-2">
                        <button type="button" @click="modalAlamat = false"
                                class="flex-1 px-4 py-2.5 border border-lake-900/15 text-ink/60 text-sm rounded-lg hover:bg-lake-50">
                            Batal
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90">
                            Simpan Alamat
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
function checkoutPage() {
    return {
        metode: '{{ $metodePengiriman->first()?->id }}',
        alamat: '',
        modalAlamat: false,

        // Kalkulasi
        subtotal:    {{ $subtotal }},
        ongkosKirim: 0,
        diskon:      0,
        total:       {{ $subtotal }},
        kurirId:     '{{ $metodeKurir?->id }}',

        // Promo
        inputKode:    '',
        promoKode:    '',
        promoApplied: false,
        loadingPromo: false,
        promoMessage: '',
        umkmId:       {{ $umkm->id }},
        csrfToken:    '{{ csrf_token() }}',

        init() {
            this.hitungTotal();
        },

        hitungTotal() {
            this.ongkosKirim = this.metode == this.kurirId ? 10000 : 0;
            this.total = Math.max(0, this.subtotal + this.ongkosKirim - this.diskon);
        },

        formatRupiah(nilai) {
            return 'Rp ' + nilai.toLocaleString('id-ID');
        },

        async terapkanPromo() {
            if (!this.inputKode || this.loadingPromo) return;
            this.loadingPromo = true;
            this.promoMessage = '';

            try {
                const res = await fetch('{{ route('customer.checkout.apply-promo') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        kode:     this.inputKode,
                        umkm_id:  this.umkmId,
                        subtotal: this.subtotal,
                    }),
                });

                const data = await res.json();

                if (data.valid) {
                    this.promoApplied = true;
                    this.promoKode    = this.inputKode;
                    this.diskon       = data.diskon;
                    this.promoMessage = data.message;
                    this.hitungTotal();
                } else {
                    this.promoMessage = data.message;
                }
            } catch (e) {
                this.promoMessage = 'Gagal memeriksa promo.';
            } finally {
                this.loadingPromo = false;
            }
        },

        hapusPromo() {
            this.promoApplied = false;
            this.promoKode    = '';
            this.inputKode    = '';
            this.diskon       = 0;
            this.promoMessage = '';
            this.hitungTotal();
        },
    }
}
</script>
@endpush

@endsection
