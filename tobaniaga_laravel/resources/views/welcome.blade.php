@extends('layouts.guest')

@section('title', 'TobaNiaga — Pasar UMKM Danau Toba')
@section('meta_description', 'TobaNiaga menghubungkan pembeli dengan UMKM asli sekitar Danau Toba — dari kain ulos, kopi Lintong, hingga ukiran kayu Batak.')

{{-- Menambahkan Alpine.js agar fungsi dropdown berjalan --}}
@push('scripts')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@endpush

@section('content')

    {{-- ============ NAVBAR ============ --}}
    <header class="relative z-20">
        <nav class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between py-6">
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded">
                <span class="w-2.5 h-7 ulos-stripe-v rounded-sm"></span>
                <span class="font-display text-2xl font-semibold tracking-tight text-lake-900">TobaNiaga</span>
            </a>

            <div class="hidden md:flex items-center gap-9 font-medium text-sm text-lake-900/80">
                <a href="#tentang" class="hover:text-ulos-maroon transition-colors focus-ring rounded">Tentang</a>
                <a href="#fitur" class="hover:text-ulos-maroon transition-colors focus-ring rounded">Untuk Siapa</a>
                <a href="#cara-kerja" class="hover:text-ulos-maroon transition-colors focus-ring rounded">Cara Kerja</a>
            </div>

            {{-- Menangani aksi Auth --}}
            <div class="flex items-center gap-3 relative">
                @guest
                    {{-- TAMPIL JIKA BELUM LOGIN --}}
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
                    {{-- TAMPIL JIKA SUDAH LOGIN --}}
                    <div class="relative" x-data="{ open: false }">
                        <!-- Tombol Nama User -->
                        <button @click="open = !open" @click.outside="open = false"
                                class="flex items-center gap-2 text-sm font-semibold text-lake-900 bg-lake-900/5 px-4 py-2.5 rounded-lg hover:bg-lake-900/10 transition-colors focus-ring">
                            <span>{{ Auth::user()->nama ?? 'Pengguna' }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Menu Dropdown -->
                        <div x-show="open"
                             x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-paper rounded-xl shadow-xl border border-lake-900/10 py-1 z-30 min-w-[200px]"
                             style="display: none;">

                            @php
                                $dashboardRoute = match(true) {
                                    Auth::user()->hasRole('admin')   => route('admin.dashboard'),
                                    Auth::user()->hasRole('sales')   => route('sales.dashboard'),
                                    Auth::user()->hasRole('courier') => route('courier.dashboard'),
                                    default                          => route('welcome'), // customer
                                };
                            @endphp

                            <a href="{{ $dashboardRoute }}" class="block px-4 py-2.5 text-sm text-ink/80 hover:bg-lake-900/5 transition-colors">
                                Dashboard
                            </a>

                            <hr class="border-lake-900/5 my-1">

                            <!-- Form Logout -->
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

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-10 pb-24 lg:pt-16 lg:pb-32">
            <div class="grid lg:grid-cols-[1.15fr_0.85fr] gap-14 lg:gap-8 items-center">

                {{-- Kolom kiri: headline --}}
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
                            <a href="{{ route('register') }}"
                               class="inline-flex items-center gap-2 text-lake-900 font-semibold px-7 py-3.5 rounded-lg border border-lake-900/15 hover:border-lake-900/30 hover:bg-lake-900/5 transition-colors focus-ring">
                                Jelajahi sebagai Pembeli
                            </a>
                        @endguest

                        @auth
                            {{-- Jika sudah login, arahkan langsung masuk ke ekosistem internal --}}
                            <a href="/"
                               class="inline-flex items-center gap-2 bg-lake-800 text-paper font-semibold px-7 py-3.5 rounded-lg hover:bg-lake-600 transition-colors focus-ring">
                                Masuk ke Dashboard Anda
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                            </a>
                        @endauth
                    </div>

                    <div class="mt-14 flex items-center gap-8 font-mono text-xs text-ink/50">
                        <span>UMKM lokal terverifikasi</span>
                        <span class="w-1 h-1 rounded-full bg-ink/30"></span>
                        <span>Tanpa biaya pendaftaran</span>
                    </div>
                </div>

                {{-- Kolom kanan: signature motif ulos --}}
                <div class="relative h-[420px] lg:h-[520px] hidden sm:block" aria-hidden="true">
                    <div class="absolute inset-0 rounded-[2rem] bg-lake-800 overflow-hidden shadow-2xl shadow-lake-900/20">
                        <div class="absolute inset-0 opacity-90 ulos-stripe-v"></div>
                        <div class="absolute inset-0 bg-gradient-to-b from-lake-900/10 via-transparent to-lake-900/60"></div>
                        <div class="absolute bottom-0 left-0 right-0 p-8">
                            <p class="font-display italic text-paper text-2xl leading-snug">
                                "Setiap helai punya cerita,<br>setiap produk punya pembuatnya."
                            </p>
                        </div>
                    </div>
                    <div class="absolute -top-5 -left-5 w-24 h-24 rounded-2xl bg-paper-100 border border-ulos-gold/30 hidden lg:block"></div>
                </div>
            </div>
        </div>

        <div class="h-3 ulos-stripe"></div>
    </section>

    {{-- ============ TENTANG ============ --}}
    <section id="tentang" class="bg-lake-900 text-paper">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
            <div class="grid lg:grid-cols-2 gap-12 items-start">
                <h2 class="font-display text-3xl lg:text-4xl font-medium leading-tight">
                    Dibangun untuk komunitas yang membuatnya istimewa.
                </h2>
                <p class="text-paper/70 text-lg leading-relaxed">
                    Banyak produk khas Toba masih sulit ditemukan di luar pasar tradisional atau toko fisik.
                    TobaNiaga hadir sebagai etalase digital yang sederhana — supaya pelaku UMKM Toba bisa
                    dikenal lebih luas, bahkan oleh masyarakat Toba sendiri yang merantau jauh dari kampung.
                </p>
            </div>
        </div>
    </section>

    {{-- ============ FITUR / UNTUK SIAPA ============ --}}
    <section id="fitur" class="bg-paper">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-4">
                Untuk Siapa TobaNiaga
            </p>
            <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 max-w-xl mb-16">
                Dua peran, satu tujuan yang sama.
            </h2>

            <div class="grid md:grid-cols-2 gap-px bg-lake-900/10 rounded-2xl overflow-hidden border border-lake-900/10">
                {{-- Card: Pembeli --}}
                <div class="bg-paper p-10 lg:p-12">
                    <div class="w-11 h-11 rounded-lg bg-lake-800 flex items-center justify-center mb-7">
                        <svg class="w-5 h-5 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="font-display text-xl font-medium text-lake-900 mb-3">Untuk Pembeli</h3>
                    <p class="text-ink/65 leading-relaxed mb-6">
                        Temukan produk asli dari pelaku UMKM di sekitar Toba — lengkap dengan informasi UMKM
                        pembuatnya, tanpa perantara berlapis.
                    </p>
                    <ul class="space-y-3 text-sm text-ink/70">
                        <li class="flex gap-3"><span class="text-ulos-gold font-mono mt-0.5">—</span> Jelajahi produk per kategori dan UMKM</li>
                        <li class="flex gap-3"><span class="text-ulos-gold font-mono mt-0.5">—</span> Pesan dan lacak status pengiriman</li>
                        <li class="flex gap-3"><span class="text-ulos-gold font-mono mt-0.5">—</span> Beri ulasan untuk bantu UMKM bertumbuh</li>
                    </ul>
                </div>

                {{-- Card: UMKM --}}
                <div class="bg-paper p-10 lg:p-12">
                    <div class="w-11 h-11 rounded-lg bg-ulos-maroon flex items-center justify-center mb-7">
                        <svg class="w-5 h-5 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m16 0h-5m-7 0h7m-7 0v-5a2 2 0 012-2h2a2 2 0 012 2v5" /></svg>
                    </div>
                    <h3 class="font-display text-xl font-medium text-lake-900 mb-3">Untuk Pemilik UMKM</h3>
                    <p class="text-ink/65 leading-relaxed mb-6">
                        Daftarkan usahamu, kelola produk dan pesanan dari satu tempat, dan jangkau pembeli
                        yang sebelumnya tidak terjangkau toko fisik.
                    </p>
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
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-4">
                Cara Kerja
            </p>
            <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 max-w-xl mb-16">
                Tiga langkah untuk mulai berjualan.
            </h2>

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
                    {{-- Ganti ajakan mendaftar dengan tombol masuk kembali ke dalam sistem --}}
                    <a href="/"
                       class="inline-flex items-center gap-2 bg-ulos-gold text-lake-900 font-semibold px-7 py-3.5 rounded-lg hover:bg-[#d3a059] transition-colors focus-ring">
                        Buka Dashboard TobaNiaga Anda
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
