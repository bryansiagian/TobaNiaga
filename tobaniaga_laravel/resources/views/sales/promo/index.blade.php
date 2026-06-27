@extends('layouts.backoffice')
@section('title', 'Promo — TobaNiaga')
@section('page_title', 'Promo')

@section('content')
<div x-data="promoPage()" x-init="init()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-display text-xl font-medium text-lake-900">Promo Toko</h2>
            <p class="text-sm text-ink/50 mt-0.5">Kelola kode promo untuk tokomu</p>
        </div>
        <button @click="openCreate()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Promo
        </button>
    </div>

    {{-- Flash --}}
    @if(session('status'))
    <div class="mb-5 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('status') }}
    </div>
    @endif

    {{-- Daftar promo --}}
    @if($promos->isEmpty())
    <div class="bg-paper border border-lake-900/10 rounded-xl p-16 text-center">
        <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/>
            </svg>
        </div>
        <p class="text-sm text-ink/40 mb-4">Belum ada promo.</p>
        <button @click="openCreate()"
                class="inline-block px-5 py-2 rounded-lg bg-lake-900 text-paper text-sm font-medium hover:bg-lake-800">
            Buat Promo Pertama
        </button>
    </div>
    @else
    <div class="space-y-3">
        @foreach($promos as $p)
        @php
            $expired = now()->toDateString() > $p->berlaku_sampai->toDateString();
            $habis   = $p->kuota !== null && $p->terpakai >= $p->kuota;
            $aktif   = $p->is_aktif && !$expired && !$habis;
        @endphp
        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <div class="px-5 py-4 flex items-start justify-between gap-4">
                <div class="flex items-start gap-3 min-w-0">
                    {{-- Kode badge --}}
                    <span class="font-mono text-xs font-bold text-lake-900 bg-lake-50 border border-lake-200 px-2.5 py-1.5 rounded-lg flex-shrink-0 mt-0.5">
                        {{ $p->kode }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-ink">{{ $p->nama_promo }}</p>
                        <p class="text-xs text-ink/50 mt-0.5">
                            @if($p->tipe === 'persen')
                                Diskon {{ $p->nilai }}%
                                @if($p->maks_diskon) · maks Rp{{ number_format($p->maks_diskon, 0, ',', '.') }}@endif
                            @else
                                Diskon Rp{{ number_format($p->nilai, 0, ',', '.') }}
                            @endif
                            @if($p->min_belanja > 0) · min Rp{{ number_format($p->min_belanja, 0, ',', '.') }}@endif
                        </p>
                        <p class="text-xs text-ink/40 mt-1">
                            {{ $p->berlaku_mulai->format('d M Y') }} – {{ $p->berlaku_sampai->format('d M Y') }}
                            · {{ $p->terpakai }}/{{ $p->kuota ?? '∞' }} dipakai
                        </p>
                        @if($p->produk->isNotEmpty())
                        <p class="text-xs text-ink/40 mt-0.5">
                            Target: {{ $p->produk->pluck('nama_produk')->join(', ') }}
                        </p>
                        @else
                        <p class="text-xs text-ink/40 mt-0.5">Target: Semua produk</p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    {{-- Status badge --}}
                    @if(!$p->is_aktif)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 border border-gray-200">Nonaktif</span>
                    @elseif($expired)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-red-50 text-red-600 border border-red-200">Kadaluarsa</span>
                    @elseif($habis)
                        <span class="text-xs px-2 py-0.5 rounded-full bg-orange-50 text-orange-600 border border-orange-200">Kuota Habis</span>
                    @else
                        <span class="text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200">Aktif</span>
                    @endif
                    {{-- Actions --}}
                    <div class="flex items-center gap-3">
                        @if($p->umkm_id !== null)
                            {{-- Promo milik sales sendiri: bisa edit/toggle/hapus --}}
                            <button @click="openEdit({{ $p->toJson() }}, {{ $p->produk->pluck('id')->toJson() }})"
                                    class="text-xs text-lake-800 font-medium hover:underline">Edit</button>
                            <form action="{{ route('sales.promo.toggle', $p) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs text-ink/50 font-medium hover:underline">
                                    {{ $p->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form action="{{ route('sales.promo.destroy', $p) }}" method="POST"
                                onsubmit="return confirm('Hapus promo {{ $p->kode }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-ulos-maroon font-medium hover:underline">Hapus</button>
                            </form>
                        @else
                            {{-- Promo dari admin: read-only --}}
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-ulos-gold/10 text-ulos-gold border border-ulos-gold/20">
                                PLATFORM
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-5">{{ $promos->links() }}</div>
    @endif

    {{-- ── Modal Buat / Edit ──────────────────────────────── --}}
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
         @keydown.escape.window="modalOpen = false">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-ink/40" @click="modalOpen = false"></div>

        {{-- Panel --}}
        <div class="relative w-full sm:max-w-lg bg-paper rounded-t-2xl sm:rounded-2xl shadow-2xl max-h-[92vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">

            {{-- Handle (mobile) --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden">
                <div class="w-10 h-1 rounded-full bg-lake-900/20"></div>
            </div>

            {{-- Title --}}
            <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-lake-900/8">
                <h3 class="font-display text-lg font-medium text-lake-900" x-text="isEdit ? 'Edit Promo' : 'Buat Promo'"></h3>
                <button @click="modalOpen = false" class="p-1.5 rounded-lg text-ink/40 hover:bg-lake-50 hover:text-ink transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Form --}}
            <form :action="isEdit ? editUrl : '{{ route('sales.promo.store') }}'"
                method="POST" class="px-5 py-4 space-y-4">
                @csrf

                @if($errors->any())
                <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <template x-if="isEdit">
                    <input name="_method" type="hidden" value="PUT">
                </template>

                {{-- Hidden inputs sebagai carrier nilai --}}
                <input type="hidden" name="kode"           :value="form.kode">
                <input type="hidden" name="nama_promo"     :value="form.nama_promo">
                <input type="hidden" name="deskripsi"      :value="form.deskripsi">
                <input type="hidden" name="tipe"           :value="form.tipe">
                <input type="hidden" name="nilai"          :value="form.nilai">
                <input type="hidden" name="min_belanja"    :value="form.min_belanja">
                <input type="hidden" name="maks_diskon"    :value="form.maks_diskon">
                <input type="hidden" name="kuota"          :value="form.kuota">
                <input type="hidden" name="berlaku_mulai"  :value="form.berlaku_mulai">
                <input type="hidden" name="berlaku_sampai" :value="form.berlaku_sampai">
                <template x-if="form.is_aktif">
                    <input type="hidden" name="is_aktif" value="1">
                </template>

                {{-- Visual inputs (hanya untuk display & interaksi user) --}}
                {{-- Kode & Nama --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Kode Promo</label>
                        <input type="text"
                            x-model="form.kode"
                            @input="form.kode = form.kode.toUpperCase().replace(/[^A-Z0-9_-]/g, '')"
                            placeholder="TOBA10"
                            class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Nama Promo</label>
                        <input type="text"
                            x-model="form.nama_promo"
                            placeholder="Diskon Akhir Pekan"
                            class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>
                </div>

                {{-- Tipe & Nilai --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Tipe Diskon</label>
                        <select x-model="form.tipe"
                                class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 bg-paper">
                            <option value="persen">Persen (%)</option>
                            <option value="nominal">Nominal (Rp)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1"
                            x-text="form.tipe === 'persen' ? 'Nilai (%)' : 'Nilai (Rp)'"></label>
                        <input type="number"
                            x-model="form.nilai"
                            min="1" step="any"
                            class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>
                </div>

                {{-- Min belanja & Maks diskon --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Min. Belanja (Rp)</label>
                        <input type="number"
                            x-model="form.min_belanja"
                            min="0" placeholder="0"
                            class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>
                    <div x-show="form.tipe === 'persen'">
                        <label class="block text-xs font-medium text-lake-900 mb-1">Maks. Potongan (Rp)</label>
                        <input type="number"
                            x-model="form.maks_diskon"
                            min="0" placeholder="Tidak terbatas"
                            class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>
                </div>

                {{-- Tanggal --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Berlaku Mulai</label>
                        <input type="date"
                            x-model="form.berlaku_mulai"
                            class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Berlaku Sampai</label>
                        <input type="date"
                            x-model="form.berlaku_sampai"
                            class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>
                </div>

                {{-- Kuota --}}
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Kuota Pemakaian <span class="text-ink/40 font-normal">(kosong = tidak terbatas)</span></label>
                    <input type="number"
                        x-model="form.kuota"
                        min="1" placeholder="Tidak terbatas"
                        class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                </div>

                {{-- Target produk --}}
                @if($produk->isNotEmpty())
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Target Produk <span class="text-ink/40 font-normal">(kosong = semua produk)</span></label>
                    <div class="border border-lake-900/15 rounded-lg divide-y divide-lake-900/8 max-h-36 overflow-y-auto">
                        @foreach($produk as $p)
                        <label class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer hover:bg-lake-50">
                            <input type="checkbox" name="produk_ids[]" value="{{ $p->id }}"
                                :checked="form.produk_ids.includes({{ $p->id }})"
                                @change="toggleProduk({{ $p->id }})"
                                class="w-4 h-4 accent-lake-800 rounded flex-shrink-0">
                            <span class="text-sm text-ink/80 flex-1 min-w-0 truncate">{{ $p->nama_produk }}</span>
                            <span class="text-xs text-ink/40 flex-shrink-0">Rp{{ number_format($p->harga, 0, ',', '.') }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Status (edit only) --}}
                <div x-show="isEdit">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox"
                            :checked="form.is_aktif"
                            @change="form.is_aktif = !form.is_aktif"
                            class="w-4 h-4 accent-lake-800 rounded">
                        <div>
                            <p class="text-sm font-medium text-lake-900">Promo Aktif</p>
                            <p class="text-xs text-ink/40">Nonaktifkan untuk menyembunyikan tanpa menghapus</p>
                        </div>
                    </label>
                </div>

                {{-- Actions --}}
                <div class="flex gap-3 pt-2 pb-2">
                    <button type="submit"
                            class="flex-1 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-800 transition-colors"
                            x-text="isEdit ? 'Simpan Perubahan' : 'Buat Promo'">
                    </button>
                    <button type="button" @click="modalOpen = false"
                            class="px-5 py-2.5 border border-lake-200 text-lake-900 text-sm font-medium rounded-lg hover:bg-lake-50 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function promoPage() {
    return {
        modalOpen: false,
        isEdit: false,
        editUrl: '',
        form: {
            kode: '', nama_promo: '', deskripsi: '',
            tipe: 'persen', nilai: '', min_belanja: '',
            maks_diskon: '', kuota: '',
            berlaku_mulai: '', berlaku_sampai: '',
            produk_ids: [], is_aktif: true,
        },

        init() {
            // Buka modal otomatis kalau ada error validasi
            @if($errors->any())
                const oldMethod = document.querySelector('input[name="_method"]');
                this.isEdit = oldMethod ? true : false;
                this.form = {
                    kode:           '{{ old('kode') }}',
                    nama_promo:     '{{ old('nama_promo') }}',
                    tipe:           '{{ old('tipe', 'persen') }}',
                    nilai:          '{{ old('nilai') }}',
                    min_belanja:    '{{ old('min_belanja') }}',
                    maks_diskon:    '{{ old('maks_diskon') }}',
                    kuota:          '{{ old('kuota') }}',
                    berlaku_mulai:  '{{ old('berlaku_mulai') }}',
                    berlaku_sampai: '{{ old('berlaku_sampai') }}',
                    produk_ids:     {!! json_encode(old('produk_ids', [])) !!},
                    is_aktif:       true,
                };
                this.modalOpen = true;
            @endif
        },

        openCreate() {
            this.isEdit = false;
            this.editUrl = '';
            this.form = {
                kode: '', nama_promo: '', deskripsi: '',
                tipe: 'persen', nilai: '', min_belanja: '',
                maks_diskon: '', kuota: '',
                berlaku_mulai: '', berlaku_sampai: '',
                produk_ids: [], is_aktif: true,
            };
            this.modalOpen = true;
        },

        openEdit(promo, produkIds) {
            this.isEdit = true;
            this.editUrl = `/sales/promo/${promo.id}`;
            this.form = {
                kode:           promo.kode,
                nama_promo:     promo.nama_promo,
                deskripsi:      promo.deskripsi ?? '',
                tipe:           promo.tipe,
                nilai:          promo.nilai,
                min_belanja:    promo.min_belanja ?? '',
                maks_diskon:    promo.maks_diskon ?? '',
                kuota:          promo.kuota ?? '',
                berlaku_mulai:  promo.berlaku_mulai,
                berlaku_sampai: promo.berlaku_sampai,
                produk_ids:     produkIds,
                is_aktif:       promo.is_aktif,
            };
            this.modalOpen = true;
        },

        toggleProduk(id) {
            const idx = this.form.produk_ids.indexOf(id);
            if (idx === -1) {
                this.form.produk_ids.push(id);
            } else {
                this.form.produk_ids.splice(idx, 1);
            }
        },
    }
}
</script>
@endpush
@endsection
