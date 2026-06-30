@extends('layouts.guest')

@section('title', 'TobaNiaga — Pasar UMKM Danau Toba')
@section('meta_description', 'TobaNiaga menghubungkan pembeli dengan UMKM asli sekitar Danau Toba — dari kain ulos, kopi Lintong, hingga ukiran kayu Batak.')

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@php
    // Pemetaan ikon per kategori (fallback ke ikon default kalau nama tidak cocok)
    $kategoriIkon = [
        'Makanan Ringan' => 'M3 7h18M3 7l1.5 12a2 2 0 002 2h11a2 2 0 002-2L21 7M3 7l1-3h16l1 3M9 11v5m6-5v5',
        'Minuman' => 'M5 3h14l-1.5 14.5A2 2 0 0115.5 19h-7a2 2 0 01-2-1.5L5 3zM3 3h18',
        'Kain Ulos' => 'M4 6h16M4 10h16M4 14h16M4 18h16',
        'Anyaman & Kerajinan' => 'M12 2l3 6 6 .9-4.5 4.4 1 6.2L12 16.8 6.5 19.5l1-6.2L3 8.9 9 8l3-6z',
        'Rempah & Bumbu' => 'M9 3v2m6-2v2M6 7h12l-1 13a2 2 0 01-2 2H9a2 2 0 01-2-2L6 7z',
        'Ikan & Hasil Laut' => 'M3 12c4-5 9-5 13 0-4 5-9 5-13 0zm13 0c1.5-1.5 3-1.5 5 0-2 1.5-3.5 1.5-5 0z',
        'Kopi & Teh' => 'M4 8h12v6a4 4 0 01-4 4H8a4 4 0 01-4-4V8zm12 2h2a2 2 0 010 4h-2M7 3v2m3-2v2m3-2v2',
        'Souvenir & Oleh-oleh' => 'M20 7h-3.5a2.5 2.5 0 10-2.5-2.5V7H10.5A2.5 2.5 0 108 9.5V20h8v-9h4V7zM4 9.5V20h4',
        'Pertanian & Perkebunan' => 'M12 2C9 6 6 9 6 13a6 6 0 0012 0c0-4-3-7-6-11z',
        'Lainnya' => 'M4 6h16M4 12h16M4 18h16',
    ];
    $ikonDefault = 'M4 6h16M4 12h16M4 18h16';
@endphp

@section('content')

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-10 pb-16 lg:pt-14 lg:pb-20">
            <div class="grid lg:grid-cols-[1.15fr_0.85fr] gap-14 lg:gap-8 items-center">

                <div class="relative">
                    <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-5">
                        Pasar Digital Sekitar Danau Toba
                    </p>
                    <h1 class="font-display text-[2.5rem] sm:text-5xl lg:text-[3.75rem] leading-[1.05] font-medium text-lake-900 tracking-tight">
                        Dari lapo dan
                        <span class="italic text-ulos-maroon">tenun rumah</span>,
                        ke meja pembeli di mana saja.
                    </h1>
                    <p class="mt-6 text-lg text-ink/70 max-w-lg leading-relaxed">
                        Kain ulos, kopi Lintong, ukiran kayu, hingga makanan khas Batak — langsung dari tangan
                        UMKM di sekitar Danau Toba.
                    </p>

                    {{-- Search bar singkat --}}
                    <form method="GET" action="{{ route('produk.index') }}" class="mt-8 max-w-md">
                        <div class="flex items-center gap-2 bg-paper border border-lake-900/15 rounded-lg p-1.5 focus-within:ring-2 focus-within:ring-lake-900/20">
                            <svg class="w-4 h-4 text-ink/30 ml-2.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/>
                            </svg>
                            <input type="text" name="cari" placeholder="Cari kopi, ulos, kerajinan..."
                                   class="flex-1 bg-transparent text-sm text-ink placeholder:text-ink/35 focus:outline-none py-1.5">
                            <button type="submit"
                                    class="bg-lake-900 text-paper text-sm font-medium px-4 py-2 rounded-md hover:bg-lake-900/90 transition-colors focus-ring">
                                Cari
                            </button>
                        </div>
                    </form>

                    <div class="mt-7 flex flex-wrap items-center gap-4">
                        <a href="{{ route('produk.index') }}"
                           class="inline-flex items-center gap-2 bg-ulos-maroon text-paper font-semibold px-6 py-3 rounded-lg hover:bg-[#7a1f2c] transition-colors focus-ring">
                            Jelajahi Produk
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                        </a>
                        @guest
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center gap-2 text-lake-900 font-semibold px-6 py-3 rounded-lg border border-lake-900/15 hover:border-lake-900/30 hover:bg-lake-900/5 transition-colors focus-ring">
                                Mulai Berjualan
                            </a>
                        @endguest
                    </div>
                </div>

                {{-- Visual kanan --}}
                <div class="hidden lg:block relative">
                    <div class="aspect-square max-w-sm ml-auto rounded-2xl bg-lake-50 border border-lake-900/10 overflow-hidden relative">
                        <img src="{{ asset('images/hero-toba.png') }}"
                             alt="Produk UMKM dan kain ulos khas Danau Toba"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-lake-900/40 via-transparent to-transparent"></div>
                        <div class="absolute bottom-0 left-0 right-0 h-2 ulos-stripe"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ KATEGORI PRODUK ============ --}}
    @if (isset($kategoriProduk) && $kategoriProduk->isNotEmpty())
    <section class="bg-lake-50 border-y border-lake-900/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-12 lg:py-16">
            <div class="flex items-end justify-between mb-8">
                <h2 class="font-display text-2xl lg:text-3xl font-medium text-lake-900">Belanja per Kategori</h2>
                <a href="{{ route('produk.index') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-lake-900 hover:text-ulos-maroon transition-colors">
                    Lihat semua
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                @foreach ($kategoriProduk as $kategori)
                    <a href="{{ route('produk.index', ['kategori_produk' => $kategori->id]) }}"
                       class="group bg-paper rounded-xl border border-lake-900/10 p-5 flex flex-col items-center text-center gap-3 hover:border-ulos-maroon/30 hover:shadow-sm transition-all">
                        <span class="w-12 h-12 rounded-lg bg-lake-50 group-hover:bg-ulos-maroon/10 flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5 text-lake-900 group-hover:text-ulos-maroon transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $kategoriIkon[$kategori->nama] ?? $ikonDefault }}"/>
                            </svg>
                        </span>
                        <span class="text-sm font-medium text-ink/80 leading-snug">{{ $kategori->nama }}</span>
                        @if (isset($kategori->produk_count))
                            <span class="font-mono text-[10px] text-ink/35">{{ $kategori->produk_count }} produk</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============ PRODUK TERBARU ============ --}}
    <section id="produk" class="bg-paper">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 lg:py-20">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-3">Produk Terbaru</p>
                    <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900">Langsung dari tangan pembuatnya.</h2>
                </div>
                <a href="{{ route('produk.index') }}"
                   class="hidden sm:inline-flex items-center gap-1.5 text-sm font-semibold text-lake-900 hover:text-ulos-maroon transition-colors">
                    Lihat semua
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            @if ($produkTerbaru->isEmpty())
                <div class="text-center py-16">
                    <p class="text-ink/40 text-sm">Belum ada produk tersedia.</p>
                </div>
            @else
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach ($produkTerbaru as $produk)
                        <a href="{{ route('produk.detail', $produk->slug) }}"
                           class="group bg-paper rounded-xl border border-lake-900/10 overflow-hidden hover:shadow-md hover:border-lake-900/20 transition-all">
                            {{-- Foto --}}
                            <div class="aspect-square bg-lake-50 overflow-hidden">
                                @if ($produk->fotoProduk->first())
                                    @php $foto = $produk->fotoProduk->first(); @endphp
                                    <img src="{{ Str::startsWith($foto->url_foto, ['http://', 'https://']) ? $foto->url_foto : Storage::url($foto->url_foto) }}"
                                        alt="{{ $produk->nama_produk }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-lake-900/15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            {{-- Info --}}
                            <div class="p-3.5">
                                <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 mb-1 truncate">{{ $produk->umkm?->nama_umkm }}</p>
                                <p class="text-sm font-medium text-ink/80 leading-snug mb-1.5 line-clamp-2">{{ $produk->nama_produk }}</p>
                                <p class="font-display text-base font-semibold text-lake-900">Rp {{ number_format($produk->harga, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-8 text-center sm:hidden">
                    <a href="{{ route('produk.index') }}"
                       class="inline-flex items-center gap-1.5 text-sm font-semibold text-lake-900 hover:text-ulos-maroon transition-colors">
                        Lihat semua produk
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- ============ BANNER AJAKAN JUALAN UMKM ============ --}}
    @guest
    <section class="bg-lake-900 text-paper relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1.5 ulos-stripe"></div>
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-12 lg:py-14 flex flex-col sm:flex-row items-center justify-between gap-6 text-center sm:text-left">
            <div>
                <h2 class="font-display text-2xl lg:text-3xl font-medium leading-tight">
                    Punya usaha di sekitar Toba?
                </h2>
                <p class="mt-2 text-paper/70 text-sm max-w-md">
                    Daftarkan UMKM kamu dan mulai jangkau pembeli lebih luas — gratis, hanya butuh beberapa menit.
                </p>
            </div>
            <a href="{{ route('register') }}"
               class="flex-shrink-0 inline-flex items-center gap-2 bg-ulos-gold text-lake-900 font-semibold px-6 py-3 rounded-lg hover:bg-[#d3a059] transition-colors focus-ring">
                Mulai Berjualan
            </a>
        </div>
        <div class="h-1.5 ulos-stripe"></div>
    </section>
    @endguest

    {{-- ============ FOOTER ============ --}}
    <footer class="bg-paper border-t border-lake-900/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 ulos-stripe-v rounded-sm"></span>
                <span class="font-display text-lg font-semibold text-lake-900">TobaNiaga</span>
            </div>
            <nav class="flex items-center gap-6 text-sm text-ink/60">
                @if (Route::has('tentang'))
                    <a href="{{ route('tentang') }}" class="hover:text-lake-900 transition-colors">Tentang</a>
                @endif
                @if (Route::has('cara-kerja'))
                    <a href="{{ route('cara-kerja') }}" class="hover:text-lake-900 transition-colors">Cara Kerja</a>
                @endif
                <a href="{{ route('produk.index') }}" class="hover:text-lake-900 transition-colors">Produk</a>
            </nav>
            <p class="font-mono text-xs text-ink/50">&copy; {{ date('Y') }} TobaNiaga. Dibuat untuk UMKM se-Toba.</p>
        </div>
    </footer>

@endsection
