@extends('layouts.guest')

@section('title', 'Semua Produk — TobaNiaga')
@section('meta_description', 'Jelajahi seluruh produk UMKM asli sekitar Danau Toba — kain ulos, kopi Lintong, hingga ukiran kayu Batak.')

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@section('content')

    {{-- ============ NAVBAR (ringkas, sama seperti welcome) ============ --}}
    <header class="relative z-20 border-b border-lake-900/10">
        <nav class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between py-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded">
                <span class="w-2.5 h-7 ulos-stripe-v rounded-sm"></span>
                <span class="font-display text-2xl font-semibold tracking-tight text-lake-900">TobaNiaga</span>
            </a>

            <div class="hidden md:flex items-center gap-9 font-medium text-sm text-lake-900/80">
                <a href="{{ url('/') }}#tentang" class="hover:text-ulos-maroon transition-colors focus-ring rounded">Tentang</a>
                <a href="{{ url('/') }}#fitur" class="hover:text-ulos-maroon transition-colors focus-ring rounded">Untuk Siapa</a>
                <a href="{{ route('produk.index') }}" class="text-ulos-maroon focus-ring rounded">Produk</a>
            </div>

            <div class="flex items-center gap-3 relative">
                @guest
                    <a href="{{ route('login') }}"
                       class="hidden sm:inline-block text-sm font-semibold text-lake-900 px-4 py-2 rounded-lg hover:bg-lake-900/5 transition-colors focus-ring">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}"
                       class="text-sm font-semibold text-paper bg-lake-800 px-5 py-2.5 rounded-lg hover:bg-lake-600 transition-colors focus-ring">
                        Daftar
                    </a>
                @endguest

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-2 text-sm font-semibold text-lake-900 bg-lake-900/5 px-4 py-2.5 rounded-lg hover:bg-lake-900/10 transition-colors focus-ring">
                            <span>{{ Auth::user()->nama ?? 'Pengguna' }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open"
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-52 bg-paper rounded-xl shadow-xl border border-lake-900/10 py-1 z-30"
                             style="display: none;">

                            @php
                                $dashboardRoute = match(true) {
                                    Auth::user()->hasRole('admin')   => route('admin.dashboard'),
                                    Auth::user()->hasRole('sales')   => route('sales.dashboard'),
                                    Auth::user()->hasRole('courier') => route('courier.dashboard'),
                                    default                          => route('welcome'),
                                };
                            @endphp

                            @role('customer')
                            <a href="#" class="flex items-center justify-between px-4 py-2.5 text-sm text-ink/80 hover:bg-lake-900/5 transition-colors">
                                <span>Keranjang</span>
                                <svg class="w-4 h-4 text-ink/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </a>
                            <a href="#" class="block px-4 py-2.5 text-sm text-ink/80 hover:bg-lake-900/5 transition-colors">
                                Riwayat Pesanan
                            </a>
                            <hr class="border-lake-900/5 my-1">
                            @endrole

                            <a href="{{ $dashboardRoute }}" class="block px-4 py-2.5 text-sm text-ink/80 hover:bg-lake-900/5 transition-colors">
                                Dashboard
                            </a>

                            <hr class="border-lake-900/5 my-1">

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left block px-4 py-2.5 text-sm text-ulos-maroon hover:bg-ulos-maroon/5 font-medium transition-colors">
                                    Keluar / Logout
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth
            </div>
        </nav>
    </header>

    {{-- ============ HEADER HALAMAN ============ --}}
    <section class="bg-lake-50 border-b border-lake-900/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-12">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-3">Katalog</p>
            <h1 class="font-display text-3xl lg:text-4xl font-medium text-lake-900">Semua Produk UMKM Toba</h1>
            <p class="mt-2 text-ink/60 text-sm">{{ $produk->total() }} produk ditemukan</p>
        </div>
    </section>

    {{-- ============ FILTER + GRID ============ --}}
    <section class="bg-paper">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-10 lg:py-14">
            <div class="grid lg:grid-cols-[260px_1fr] gap-8">

                {{-- ── Sidebar Filter ── --}}
                <aside x-data="{ filterOpen: false }" class="lg:sticky lg:top-6 self-start">
                    <button @click="filterOpen = !filterOpen"
                            class="lg:hidden w-full flex items-center justify-between px-4 py-3 rounded-lg border border-lake-900/15 mb-4 text-sm font-medium text-lake-900">
                        <span>Filter Produk</span>
                        <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': filterOpen}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <form method="GET" action="{{ route('produk.index') }}"
                          x-show="filterOpen || window.innerWidth >= 1024"
                          class="space-y-6 bg-lake-50 border border-lake-900/10 rounded-xl p-5">

                        {{-- Pencarian --}}
                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Cari Produk</label>
                            <input type="text" name="cari" value="{{ request('cari') }}"
                                   placeholder="Nama produk..."
                                   class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                        </div>

                        {{-- Kategori UMKM --}}
                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Kategori UMKM</label>
                            <select name="kategori_umkm"
                                    class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                                <option value="">Semua Kategori UMKM</option>
                                @foreach ($kategoriUmkmList as $k)
                                    <option value="{{ $k->id }}" @selected(request('kategori_umkm') == $k->id)>{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Kategori Produk --}}
                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Kategori Produk</label>
                            <select name="kategori_produk"
                                    class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                                <option value="">Semua Kategori Produk</option>
                                @foreach ($kategoriProdukList as $k)
                                    <option value="{{ $k->id }}" @selected(request('kategori_produk') == $k->id)>{{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Rentang Harga --}}
                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Rentang Harga</label>
                            <div class="flex items-center gap-2">
                                <input type="number" name="harga_min" value="{{ request('harga_min') }}"
                                       placeholder="Min" min="0"
                                       class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                                <span class="text-ink/30 text-sm">—</span>
                                <input type="number" name="harga_maks" value="{{ request('harga_maks') }}"
                                       placeholder="Maks" min="0"
                                       class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                            </div>
                        </div>

                        {{-- Stok --}}
                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Ketersediaan Stok</label>
                            <div class="space-y-2">
                                <label class="flex items-center gap-2 text-sm text-ink/70">
                                    <input type="radio" name="stok" value="" class="accent-lake-800" @checked(request('stok') == null)>
                                    Semua
                                </label>
                                <label class="flex items-center gap-2 text-sm text-ink/70">
                                    <input type="radio" name="stok" value="tersedia" class="accent-lake-800" @checked(request('stok') == 'tersedia')>
                                    Tersedia (stok &gt; 0)
                                </label>
                                <label class="flex items-center gap-2 text-sm text-ink/70">
                                    <input type="radio" name="stok" value="habis" class="accent-lake-800" @checked(request('stok') == 'habis')>
                                    Stok Habis
                                </label>
                            </div>
                        </div>

                        {{-- Urutan --}}
                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Urutkan</label>
                            <select name="urut"
                                    class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                                <option value="terbaru" @selected(request('urut', 'terbaru') == 'terbaru')>Terbaru</option>
                                <option value="harga_terendah" @selected(request('urut') == 'harga_terendah')>Harga Terendah</option>
                                <option value="harga_tertinggi" @selected(request('urut') == 'harga_tertinggi')>Harga Tertinggi</option>
                                <option value="nama" @selected(request('urut') == 'nama')>Nama A-Z</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-2 pt-1">
                            <button type="submit"
                                    class="w-full px-4 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90 transition-colors">
                                Terapkan Filter
                            </button>
                            @if (request()->anyFilled(['cari', 'kategori_umkm', 'kategori_produk', 'harga_min', 'harga_maks', 'stok']) || request('urut'))
                                <a href="{{ route('produk.index') }}"
                                   class="w-full text-center px-4 py-2.5 text-sm text-ink/50 hover:text-ink transition-colors">
                                    Reset Filter
                                </a>
                            @endif
                        </div>
                    </form>
                </aside>

                {{-- ── Grid Produk ── --}}
                <div>
                    @if ($produk->isEmpty())
                        <div class="text-center py-20 bg-lake-50 rounded-xl border border-lake-900/10">
                            <div class="w-12 h-12 rounded-xl bg-paper border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-ink/50">Tidak ada produk yang cocok dengan filter kamu.</p>
                            <a href="{{ route('produk.index') }}" class="inline-block mt-3 text-sm font-semibold text-lake-900 hover:text-ulos-maroon">Reset filter</a>
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach ($produk as $item)
                                <a href="{{ route('produk.detail', $item->slug) }}"
                                   class="group bg-paper rounded-xl border border-lake-900/10 overflow-hidden hover:shadow-md hover:border-lake-900/20 transition-all">
                    <div class="aspect-square bg-lake-50 overflow-hidden relative">
                                        @php $fotoUtama = $item->fotoProduk->first(); @endphp
                                        @if ($fotoUtama)
                                            <img src="{{ Str::startsWith($fotoUtama->url_foto, ['http://', 'https://']) ? $fotoUtama->url_foto : Storage::url($fotoUtama->url_foto) }}"
                                                 alt="{{ $item->nama_produk }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-lake-900/15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                            </div>
                                        @endif

                                        @if ($item->stok <= 0)
                                            <span class="absolute top-2 left-2 bg-ulos-maroon text-paper text-[10px] font-mono uppercase tracking-wide px-2 py-1 rounded">
                                                Stok Habis
                                            </span>
                                        @endif
                                    </div>
                                    <div class="p-3.5">
                                        <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 mb-1 truncate">{{ $item->umkm?->nama_umkm }}</p>
                                        <p class="text-sm font-medium text-ink/80 leading-snug mb-1.5 line-clamp-2">{{ $item->nama_produk }}</p>

                                        @if ($item->jumlah_ulasan > 0)
                                            <div class="flex items-center gap-0.5 mb-1.5">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3.5 h-3.5 {{ $i <= round($item->rating_rata_rata) ? 'text-ulos-gold fill-current' : 'text-lake-900/15 fill-current' }}" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.287 3.957c.299.921-.756 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.783.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.363-1.118L2.02 9.384c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.957z"/>
                                                    </svg>
                                                @endfor
                                                <span class="text-[11px] text-ink/40 ml-1">({{ $item->jumlah_ulasan }})</span>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-0.5 mb-1.5">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3.5 h-3.5 text-lake-900/15 fill-current" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.287 3.957c.299.921-.756 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.783.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.363-1.118L2.02 9.384c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.957z"/>
                                                    </svg>
                                                @endfor
                                                <span class="text-[11px] text-ink/30 ml-1">Belum ada ulasan</span>
                                            </div>
                                        @endif

                                        <p class="font-display text-base font-semibold text-lake-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                                        <p class="text-[11px] text-ink/40 mt-0.5">Stok: {{ $item->stok }} {{ $item->satuan }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-10">
                            {{ $produk->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </section>

@endsection
