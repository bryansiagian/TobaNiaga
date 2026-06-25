@extends('layouts.guest')

@section('title', 'Keranjang — TobaNiaga')

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@section('content')

    {{-- ============ ISI KERANJANG ============ --}}
    <section class="bg-paper">
        <div class="max-w-5xl mx-auto px-6 lg:px-10 py-10 lg:py-14"
             x-data="{
                terpilih: [],
                toggleItem(id) {
                    if (this.terpilih.includes(id)) {
                        this.terpilih = this.terpilih.filter(x => x !== id);
                    } else {
                        this.terpilih.push(id);
                    }
                },
                toggleGrup(ids) {
                    const semuaTerpilih = ids.every(id => this.terpilih.includes(id));
                    if (semuaTerpilih) {
                        this.terpilih = this.terpilih.filter(id => !ids.includes(id));
                    } else {
                        ids.forEach(id => { if (!this.terpilih.includes(id)) this.terpilih.push(id); });
                    }
                },
                isGrupTerpilih(ids) {
                    return ids.length > 0 && ids.every(id => this.terpilih.includes(id));
                }
             }">

            <h1 class="font-display text-2xl lg:text-3xl font-medium text-lake-900 mb-8">Keranjang Belanja</h1>

            @if (session('status'))
                <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 px-4 py-3 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-sm text-ulos-maroon">
                    {{ session('error') }}
                </div>
            @endif

            @if ($kelompok->isEmpty())
                <div class="text-center py-20 bg-lake-50 rounded-xl border border-lake-900/10">
                    <div class="w-12 h-12 rounded-xl bg-paper border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <p class="text-sm text-ink/50 mb-4">Keranjang kamu masih kosong.</p>
                    <a href="{{ route('produk.index') }}"
                       class="inline-block px-5 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90 transition-colors">
                        Mulai Belanja
                    </a>
                </div>
            @else
                <div class="space-y-6">
                    @foreach ($kelompok as $umkmId => $items)
                        @php
                            $umkm = $items->first()->produk->umkm;
                            $idsGrup = $items->pluck('id')->toArray();
                        @endphp

                        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
                            {{-- Header toko --}}
                            <div class="px-5 py-4 border-b border-lake-900/8 flex items-center gap-3 bg-lake-50">
                                <input type="checkbox"
                                       :checked="isGrupTerpilih({{ json_encode($idsGrup) }})"
                                       @change="toggleGrup({{ json_encode($idsGrup) }})"
                                       class="w-4 h-4 accent-lake-800 rounded">
                                <svg class="w-4 h-4 text-lake-900/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m16 0h-5m-7 0h7m-7 0v-5a2 2 0 012-2h2a2 2 0 012 2v5"/>
                                </svg>
                                <span class="text-sm font-medium text-lake-900">{{ $umkm->nama_umkm }}</span>
                            </div>

                            {{-- Item produk --}}
                            <div class="divide-y divide-lake-900/6">
                                @foreach ($items as $item)
                                    @php $fotoItem = $item->produk->fotoProduk->first(); @endphp
                                    <div class="px-5 py-4 flex items-center gap-4" x-data="{ jml: {{ $item->jumlah }} }">
                                        <input type="checkbox"
                                               :checked="terpilih.includes({{ $item->id }})"
                                               @change="toggleItem({{ $item->id }})"
                                               class="w-4 h-4 accent-lake-800 rounded flex-shrink-0">

                                        <div class="w-16 h-16 rounded-lg bg-lake-50 border border-lake-900/10 overflow-hidden flex-shrink-0">
                                            @if ($fotoItem)
                                                <img src="{{ Str::startsWith($fotoItem->url_foto, ['http://', 'https://']) ? $fotoItem->url_foto : Storage::url($fotoItem->url_foto) }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-lake-900/15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <a href="{{ route('produk.detail', $item->produk->slug) }}"
                                               class="text-sm font-medium text-ink/80 hover:text-lake-900 line-clamp-1">
                                                {{ $item->produk->nama_produk }}
                                            </a>
                                            <p class="font-display text-sm font-semibold text-lake-900 mt-1">
                                                Rp {{ number_format($item->produk->harga, 0, ',', '.') }}
                                            </p>
                                            @if ($item->produk->stok < $item->jumlah)
                                                <p class="text-[11px] text-ulos-maroon mt-1">Stok tersisa {{ $item->produk->stok }}, kurangi jumlah.</p>
                                            @endif
                                        </div>

                                        {{-- Update jumlah --}}
                                        <form action="{{ route('customer.keranjang.update', $item) }}" method="POST" class="flex items-center border border-lake-900/15 rounded-lg flex-shrink-0">
                                            @csrf
                                            @method('PUT')
                                            <button type="button" @click="jml = Math.max(1, jml - 1)"
                                                    class="px-2.5 py-2 text-ink/50 hover:text-ink transition-colors text-sm">−</button>
                                            <input type="number" name="jumlah" x-model.number="jml" min="1" max="{{ $item->produk->stok }}"
                                                   class="w-10 text-center text-sm border-0 focus:outline-none focus:ring-0 [&::-webkit-inner-spin-button]:appearance-none">
                                            <button type="button" @click="jml = Math.min({{ $item->produk->stok }}, jml + 1)"
                                                    class="px-2.5 py-2 text-ink/50 hover:text-ink transition-colors text-sm">+</button>
                                            <button type="submit"
                                                    class="px-2.5 py-2 text-[11px] font-mono text-lake-800 hover:underline border-l border-lake-900/15">
                                                Simpan
                                            </button>
                                        </form>

                                        {{-- Hapus item --}}
                                        <form action="{{ route('customer.keranjang.destroy', $item) }}" method="POST"
                                              onsubmit="return confirm('Hapus produk ini dari keranjang?')" class="flex-shrink-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-ink/30 hover:text-ulos-maroon transition-colors">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Footer per toko: checkout grup ini --}}
                            <div class="px-5 py-3.5 bg-lake-50 border-t border-lake-900/8 flex items-center justify-end">
                                <form action="{{ route('customer.checkout.create') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="umkm_id" value="{{ $umkmId }}">
                                    <template x-for="id in terpilih.filter(id => {{ json_encode($idsGrup) }}.includes(id))" :key="id">
                                        <input type="hidden" name="keranjang_ids[]" :value="id">
                                    </template>
                                    <button type="submit"
                                            :disabled="terpilih.filter(id => {{ json_encode($idsGrup) }}.includes(id)).length === 0"
                                            class="px-5 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                                        Checkout Toko Ini
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

@endsection
