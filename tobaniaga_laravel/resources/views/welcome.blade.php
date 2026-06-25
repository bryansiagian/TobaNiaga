@extends('layouts.guest')

@section('title', 'TobaNiaga — Pasar UMKM Danau Toba')
@section('meta_description', 'TobaNiaga menghubungkan pembeli dengan UMKM asli sekitar Danau Toba — dari kain ulos, kopi Lintong, hingga ukiran kayu Batak.')

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@section('content')

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-10 pb-24 lg:pt-16 lg:pb-32">
            <div class="grid lg:grid-cols-[1.15fr_0.85fr] gap-14 lg:gap-8 items-center">

                <div class="relative">
                    <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-5">
                        Pasar Digital Sekitar Danau Toba
                    </p>
                    <h1 class="font-display text-[2.75rem] sm:text-6xl lg:text-[4.25rem] leading-[1.03] font-medium text-lake-900 tracking-tight">
                        Dari lapo dan
                        <span class="italic text-ulos-maroon">tenun rumah</span>,
                        ke meja pembeli di mana saja.
                    </h1>
                    <p class="mt-7 text-lg text-ink/70 max-w-lg leading-relaxed">
                        TobaNiaga mempertemukan pelaku UMKM di sekitar Danau Toba dengan pembeli yang mencari barang asli —
                        kain ulos, kopi Lintong, ukiran kayu, hingga makanan khas Batak — langsung dari tangan pembuatnya.
                    </p>

                    <div class="mt-10 flex flex-wrap items-center gap-4">
                        @guest
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center gap-2 bg-ulos-maroon text-paper font-semibold px-7 py-3.5 rounded-lg hover:bg-[#7a1f2c] transition-colors focus-ring">
                                Mulai Berjualan
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </a>
                            <a href="{{ route('produk.index') }}"
                               class="inline-flex items-center gap-2 text-lake-900 font-semibold px-7 py-3.5 rounded-lg border border-lake-900/15 hover:border-lake-900/30 hover:bg-lake-900/5 transition-colors focus-ring">
                                Jelajahi Produk
                            </a>
                        @endguest

                        @auth
                            <a href="{{ route('produk.index') }}"
                               class="inline-flex items-center gap-2 bg-lake-800 text-paper font-semibold px-7 py-3.5 rounded-lg hover:bg-lake-600 transition-colors focus-ring">
                                Jelajahi Produk
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Visual kanan --}}
                <div class="hidden lg:block relative">
                    <div class="aspect-square max-w-sm ml-auto rounded-2xl bg-lake-50 border border-lake-900/10 overflow-hidden flex items-center justify-center">
                        <div class="absolute inset-0 opacity-20 ulos-stripe-v"></div>
                        <p class="font-display text-lake-900/20 text-xl italic relative z-10">Danau Toba</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ PRODUK TERBARU ============ --}}
    <section id="produk" class="bg-lake-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
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
                        <a href="{{ route('produk.detail', $produk->slug) }}" {{-- ganti dengan route detail produk nanti --}}
                           class="group bg-paper rounded-xl border border-lake-900/10 overflow-hidden hover:shadow-md hover:border-lake-900/20 transition-all">
                            {{-- Foto --}}
                            <div class="aspect-square bg-lake-50 overflow-hidden">
                                @if ($produk->fotoProduk->first())
                                    <img src="{{ Storage::url($produk->fotoProduk->first()->url_foto) }}"
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

    {{-- ============ TENTANG ============ --}}
    <section id="tentang" class="bg-paper">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28 grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-5">Tentang TobaNiaga</p>
                <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 leading-snug mb-6">
                    Platform e-commerce khusus UMKM di sekitar Danau Toba.
                </h2>
                <p class="text-ink/65 leading-relaxed mb-4">
                    TobaNiaga lahir dari kesadaran bahwa banyak produk UMKM berkualitas tinggi di sekitar Danau Toba
                    belum memiliki akses pasar yang memadai.
                </p>
                <p class="text-ink/65 leading-relaxed">
                    Dengan TobaNiaga, setiap pelaku UMKM — dari penenun ulos, petani kopi, hingga pengrajin ukiran —
                    bisa dikenal lebih luas, bahkan oleh masyarakat Toba sendiri yang merantau jauh dari kampung.
                </p>
            </div>
            <div class="rounded-2xl bg-lake-50 border border-lake-900/10 p-10 space-y-6">
                <div class="flex gap-4 items-start">
                    <span class="w-8 h-8 rounded-lg bg-lake-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </span>
                    <div>
                        <p class="font-medium text-lake-900 text-sm mb-1">Fokus Lokal</p>
                        <p class="text-ink/60 text-sm leading-relaxed">Dirancang khusus untuk UMKM di kawasan Danau Toba, Sumatera Utara.</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start">
                    <span class="w-8 h-8 rounded-lg bg-ulos-maroon flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                    <div>
                        <p class="font-medium text-lake-900 text-sm mb-1">Terverifikasi</p>
                        <p class="text-ink/60 text-sm leading-relaxed">Setiap UMKM diverifikasi admin sebelum bisa berjualan di platform.</p>
                    </div>
                </div>
                <div class="flex gap-4 items-start">
                    <span class="w-8 h-8 rounded-lg bg-ulos-gold flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-lake-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </span>
                    <div>
                        <p class="font-medium text-lake-900 text-sm mb-1">Mudah Digunakan</p>
                        <p class="text-ink/60 text-sm leading-relaxed">Antarmuka sederhana yang bisa digunakan siapa saja, tanpa keahlian teknis.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ FITUR / UNTUK SIAPA ============ --}}
    <section id="fitur" class="bg-paper border-t border-lake-900/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-4">Untuk Siapa TobaNiaga</p>
            <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 max-w-xl mb-16">Dua peran, satu tujuan yang sama.</h2>

            <div class="grid md:grid-cols-2 gap-px bg-lake-900/10 rounded-2xl overflow-hidden border border-lake-900/10">
                <div class="bg-paper p-10 lg:p-12">
                    <div class="w-11 h-11 rounded-lg bg-lake-800 flex items-center justify-center mb-7">
                        <svg class="w-5 h-5 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="font-display text-xl font-medium text-lake-900 mb-3">Untuk Pembeli</h3>
                    <p class="text-ink/65 leading-relaxed mb-6">Temukan produk asli dari pelaku UMKM di sekitar Toba — lengkap dengan informasi UMKM pembuatnya, tanpa perantara berlapis.</p>
                    <ul class="space-y-3 text-sm text-ink/70">
                        <li class="flex gap-3"><span class="text-ulos-gold font-mono mt-0.5">—</span> Jelajahi produk per kategori dan UMKM</li>
                        <li class="flex gap-3"><span class="text-ulos-gold font-mono mt-0.5">—</span> Pesan dan lacak status pengiriman</li>
                        <li class="flex gap-3"><span class="text-ulos-gold font-mono mt-0.5">—</span> Beri ulasan untuk bantu UMKM bertumbuh</li>
                    </ul>
                </div>
                <div class="bg-paper p-10 lg:p-12">
                    <div class="w-11 h-11 rounded-lg bg-ulos-maroon flex items-center justify-center mb-7">
                        <svg class="w-5 h-5 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m16 0h-5m-7 0h7m-7 0v-5a2 2 0 012-2h2a2 2 0 012 2v5" /></svg>
                    </div>
                    <h3 class="font-display text-xl font-medium text-lake-900 mb-3">Untuk Pemilik UMKM</h3>
                    <p class="text-ink/65 leading-relaxed mb-6">Daftarkan usahamu, kelola produk dan pesanan dari satu tempat, dan jangkau pembeli yang sebelumnya tidak terjangkau toko fisik.</p>
                    <ul class="space-y-3 text-sm text-ink/70">
                        <li class="flex gap-3"><span class="text-ulos-gold font-mono mt-0.5">—</span> Kelola katalog produk dan stok sendiri</li>
                        <li class="flex gap-3"><span class="text-ulos-gold font-mono mt-0.5">—</span> Pantau pesanan masuk dan pengiriman</li>
                        <li class="flex gap-3"><span class="text-ulos-gold font-mono mt-0.5">—</span> Bangun reputasi lewat ulasan pembeli</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ CARA KERJA ============ --}}
    <section id="cara-kerja" class="bg-lake-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-4">Cara Kerja</p>
            <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 max-w-xl mb-16">Tiga langkah untuk mulai berjualan.</h2>
            <div class="grid md:grid-cols-3 gap-10">
                <div>
                    <p class="font-mono text-sm text-ulos-gold font-medium mb-3">01</p>
                    <h3 class="font-display text-lg font-medium text-lake-900 mb-2">Daftar sebagai UMKM</h3>
                    <p class="text-ink/65 text-sm leading-relaxed">Isi data usahamu lewat formulir pendaftaran singkat.</p>
                </div>
                <div>
                    <p class="font-mono text-sm text-ulos-gold font-medium mb-3">02</p>
                    <h3 class="font-display text-lg font-medium text-lake-900 mb-2">Tunggu verifikasi</h3>
                    <p class="text-ink/65 text-sm leading-relaxed">Tim TobaNiaga memeriksa data usahamu sebelum akun diaktifkan.</p>
                </div>
                <div>
                    <p class="font-mono text-sm text-ulos-gold font-medium mb-3">03</p>
                    <h3 class="font-display text-lg font-medium text-lake-900 mb-2">Mulai berjualan</h3>
                    <p class="text-ink/65 text-sm leading-relaxed">Tambahkan produk dan mulai terima pesanan dari pembeli.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ CTA ============ --}}
    <section class="bg-lake-900 text-paper relative overflow-hidden">
        <div class="absolute top-0 left-0 right-0 h-1.5 ulos-stripe"></div>
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-24 text-center">
            <h2 class="font-display text-3xl lg:text-[2.75rem] font-medium leading-tight max-w-2xl mx-auto">
                Saatnya produk Toba dikenal lebih luas.
            </h2>
            <p class="mt-5 text-paper/70 max-w-md mx-auto">
                Bergabung sebagai pembeli atau pemilik UMKM — gratis, dan hanya butuh beberapa menit.
            </p>
            <div class="mt-9 flex flex-wrap justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 bg-ulos-gold text-lake-900 font-semibold px-7 py-3.5 rounded-lg hover:bg-[#d3a059] transition-colors focus-ring">
                        Daftar Sekarang
                    </a>
                    <a href="{{ route('login') }}"
                       class="inline-flex items-center gap-2 text-paper font-semibold px-7 py-3.5 rounded-lg border border-paper/25 hover:bg-paper/10 transition-colors focus-ring">
                        Sudah punya akun? Masuk
                    </a>
                @endguest
                @auth
                    <a href="{{ route('produk.index') }}"
                       class="inline-flex items-center gap-2 bg-ulos-gold text-lake-900 font-semibold px-7 py-3.5 rounded-lg hover:bg-[#d3a059] transition-colors focus-ring">
                        Jelajahi Semua Produk
                    </a>
                @endauth
            </div>
        </div>
        <div class="h-1.5 ulos-stripe"></div>
    </section>

    {{-- ============ FOOTER ============ --}}
    <footer class="bg-paper border-t border-lake-900/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2.5">
                <span class="w-2 h-5 ulos-stripe-v rounded-sm"></span>
                <span class="font-display text-lg font-semibold text-lake-900">TobaNiaga</span>
            </div>
            <p class="font-mono text-xs text-ink/50">&copy; {{ date('Y') }} TobaNiaga. Dibuat untuk UMKM se-Toba.</p>
        </div>
    </footer>

@endsection
