@extends('layouts.guest')

@section('title', $produk->nama_produk . ' — TobaNiaga')
@section('meta_description', Str::limit($produk->deskripsi, 150))

@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@section('content')

    {{-- ============ NAVBAR (ringkas, sama seperti products/index) ============ --}}
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

    {{-- ============ BREADCRUMB ============ --}}
    <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-6">
        <nav class="flex items-center gap-2 text-xs text-ink/40 font-mono">
            <a href="{{ url('/') }}" class="hover:text-lake-900">Beranda</a>
            <span>/</span>
            <a href="{{ route('produk.index') }}" class="hover:text-lake-900">Produk</a>
            <span>/</span>
            <span class="text-ink/60 truncate">{{ $produk->nama_produk }}</span>
        </nav>
    </div>

    {{-- ============ DETAIL PRODUK ============ --}}
    <section class="bg-paper">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-8 lg:py-12">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16">

                {{-- ── Galeri Foto ── --}}
                <div x-data="{ activeImg: 0 }">
                    @php $foto = $produk->fotoProduk; @endphp
                    <div class="aspect-square bg-lake-50 rounded-2xl border border-lake-900/10 overflow-hidden mb-3">
                        @if ($foto->isNotEmpty())
                            @foreach ($foto as $i => $f)
                                <img x-show="activeImg === {{ $i }}"
                                     src="{{ Str::startsWith($f->url_foto, ['http://', 'https://']) ? $f->url_foto : Storage::url($f->url_foto) }}"
                                     alt="{{ $produk->nama_produk }}"
                                     class="w-full h-full object-cover">
                            @endforeach
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-14 h-14 text-lake-900/15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </div>

                    @if ($foto->count() > 1)
                        <div class="grid grid-cols-5 gap-2.5">
                            @foreach ($foto as $i => $f)
                                <button @click="activeImg = {{ $i }}"
                                        class="aspect-square rounded-lg overflow-hidden border-2 transition-colors"
                                        :class="activeImg === {{ $i }} ? 'border-lake-800' : 'border-transparent'">
                                    <img src="{{ Str::startsWith($f->url_foto, ['http://', 'https://']) ? $f->url_foto : Storage::url($f->url_foto) }}"
                                         class="w-full h-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── Info Produk ── --}}
                <div>
                    <a href="{{ route('produk.index') }}?kategori_produk={{ $produk->kategori_id }}"
                       class="font-mono text-[11px] uppercase tracking-widest text-ulos-maroon font-medium hover:underline">
                        {{ $produk->kategori?->nama }}
                    </a>

                    <h1 class="font-display text-2xl lg:text-3xl font-medium text-lake-900 mt-2 mb-3">
                        {{ $produk->nama_produk }}
                    </h1>

                    {{-- Rating bintang --}}
                    <div class="flex items-center gap-2 mb-5">
                        <div class="flex items-center gap-0.5">
                            @for ($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= round($produk->rating_rata_rata) ? 'text-ulos-gold fill-current' : 'text-lake-900/15 fill-current' }}" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.287 3.957c.299.921-.756 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.783.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.363-1.118L2.02 9.384c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.957z"/>
                                </svg>
                            @endfor
                        </div>
                        @if ($produk->jumlah_ulasan > 0)
                            <span class="text-sm text-ink/60">{{ $produk->rating_rata_rata }} · {{ $produk->jumlah_ulasan }} ulasan</span>
                        @else
                            <span class="text-sm text-ink/40">Belum ada ulasan</span>
                        @endif
                    </div>

                    <p class="font-display text-3xl font-semibold text-lake-900 mb-6">
                        Rp {{ number_format($produk->harga, 0, ',', '.') }}
                        <span class="text-sm font-normal text-ink/40">/ {{ $produk->satuan }}</span>
                    </p>

                    {{-- Info UMKM --}}
                    <a href="#" class="flex items-center gap-3 p-4 bg-lake-50 rounded-xl border border-lake-900/10 mb-6 hover:border-lake-900/20 transition-colors">
                        @if ($produk->umkm?->foto_profil)
                            <img src="{{ Storage::url($produk->umkm->foto_profil) }}"
                                 class="w-11 h-11 rounded-full object-cover border border-lake-900/10">
                        @else
                            <div class="w-11 h-11 rounded-full bg-lake-100 border border-lake-900/10 flex items-center justify-center">
                                <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="text-sm font-medium text-ink/80">{{ $produk->umkm?->nama_umkm }}</p>
                            <p class="text-xs text-ink/40">{{ $produk->umkm?->kecamatan }}, Kabupaten Toba</p>
                        </div>
                        <svg class="w-4 h-4 text-ink/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>

                    {{-- Stok --}}
                    <div class="flex items-center gap-2 mb-6 text-sm">
                        @if ($produk->stok > 0)
                            <span class="w-2 h-2 rounded-full bg-lake-600"></span>
                            <span class="text-ink/60">Stok tersedia: <strong class="text-ink/80">{{ $produk->stok }} {{ $produk->satuan }}</strong></span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-ulos-maroon"></span>
                            <span class="text-ulos-maroon font-medium">Stok habis</span>
                        @endif
                    </div>

                    {{-- Aksi --}}
                    <div class="flex gap-3">
                        @auth
                            @role('customer')
                                <button {{ $produk->stok <= 0 ? 'disabled' : '' }}
                                        class="flex-1 px-6 py-3.5 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-900/90 transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                                    Tambah ke Keranjang
                                </button>
                            @endrole
                        @else
                            <a href="{{ route('login') }}"
                               class="flex-1 text-center px-6 py-3.5 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-900/90 transition-colors">
                                Masuk untuk Membeli
                            </a>
                        @endauth
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mt-8 pt-6 border-t border-lake-900/10">
                        <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-2">Deskripsi Produk</p>
                        <p class="text-sm text-ink/70 leading-relaxed whitespace-pre-line">{{ $produk->deskripsi }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ ULASAN ============ --}}
    <section class="bg-lake-50 border-t border-lake-900/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-12 lg:py-16">
            <h2 class="font-display text-xl lg:text-2xl font-medium text-lake-900 mb-6">
                Ulasan Pembeli ({{ $produk->jumlah_ulasan }})
            </h2>

            @if ($ulasan->isEmpty())
                <div class="bg-paper border border-lake-900/10 rounded-xl p-10 text-center">
                    <p class="text-sm text-ink/40">Belum ada ulasan untuk produk ini.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($ulasan as $u)
                        <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-lake-100 flex items-center justify-center text-xs font-semibold text-lake-800">
                                        {{ $u->is_anonim ? '?' : Str::upper(Str::substr($u->user?->nama ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-ink/80">{{ $u->is_anonim ? 'Pembeli Anonim' : ($u->user?->nama ?? 'Pengguna') }}</p>
                                        <p class="text-[11px] text-ink/40">{{ $u->created_at->translatedFormat('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= $u->rating ? 'text-ulos-gold fill-current' : 'text-lake-900/15 fill-current' }}" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.957a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.287 3.957c.299.921-.756 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.783.57-1.838-.197-1.539-1.118l1.286-3.957a1 1 0 00-.363-1.118L2.02 9.384c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.957z"/>
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                            @if ($u->komentar)
                                <p class="text-sm text-ink/70 leading-relaxed">{{ $u->komentar }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $ulasan->links() }}
                </div>
            @endif
        </div>
    </section>

    {{-- ============ PRODUK TERKAIT ============ --}}
    @if ($produkTerkait->isNotEmpty())
        <section class="bg-paper">
            <div class="max-w-7xl mx-auto px-6 lg:px-10 py-12 lg:py-16">
                <h2 class="font-display text-xl lg:text-2xl font-medium text-lake-900 mb-6">Produk Terkait</h2>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($produkTerkait as $item)
                        <a href="{{ route('produk.detail', $item->slug) }}"
                           class="group bg-paper rounded-xl border border-lake-900/10 overflow-hidden hover:shadow-md hover:border-lake-900/20 transition-all">
                            <div class="aspect-square bg-lake-50 overflow-hidden">
                                @php $fotoTerkait = $item->fotoProduk->first(); @endphp
                                @if ($fotoTerkait)
                                    <img src="{{ Str::startsWith($fotoTerkait->url_foto, ['http://', 'https://']) ? $fotoTerkait->url_foto : Storage::url($fotoTerkait->url_foto) }}"
                                         alt="{{ $item->nama_produk }}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-8 h-8 text-lake-900/15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3.5">
                                <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 mb-1 truncate">{{ $item->umkm?->nama_umkm }}</p>
                                <p class="text-sm font-medium text-ink/80 leading-snug mb-1.5 line-clamp-2">{{ $item->nama_produk }}</p>
                                <p class="font-display text-base font-semibold text-lake-900">Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

@endsection
