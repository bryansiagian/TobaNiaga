<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TobaNiaga — Pasar UMKM Danau Toba')</title>
    <meta name="description" content="@yield('meta_description', 'TobaNiaga menghubungkan pembeli dengan UMKM asli sekitar Danau Toba — dari kain ulos, kopi Lintong, hingga ukiran kayu Batak.')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..700;1,9..144,400..600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        lake: {
                            50: '#EAF1F0',
                            100: '#CBDEDC',
                            400: '#1E5F60',
                            600: '#164A4B',
                            800: '#0F3D3E',
                            900: '#0A2C2D',
                        },
                        paper: {
                            DEFAULT: '#FAF7F0',
                            100: '#F2EDE1',
                        },
                        ulos: {
                            maroon: '#8B2635',
                            gold: '#C08A3E',
                        },
                        ink: '#2A2622',
                    },
                    fontFamily: {
                        display: ['Fraunces', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        body { background-color: #FAF7F0; }
        .ulos-stripe {
            background: repeating-linear-gradient(
                90deg,
                #C08A3E 0px, #C08A3E 3px,
                transparent 3px, transparent 14px,
                #8B2635 14px, #8B2635 17px,
                transparent 17px, transparent 28px
            );
        }
        .ulos-stripe-v {
            background: repeating-linear-gradient(
                180deg,
                #C08A3E 0px, #C08A3E 3px,
                transparent 3px, transparent 14px,
                #8B2635 14px, #8B2635 17px,
                transparent 17px, transparent 28px
            );
        }
        @media (prefers-reduced-motion: reduce) {
            * { animation-duration: 0.001ms !important; animation-iteration-count: 1 !important; transition-duration: 0.001ms !important; }
        }
        .focus-ring:focus-visible {
            outline: 2px solid #C08A3E;
            outline-offset: 2px;
        }
        html {
            scroll-behavior: smooth;
        }
    </style>
    @stack('styles')
</head>
<body class="font-sans text-ink antialiased">

    {{-- ══ NAVBAR GLOBAL ══════════════════════════════════════════ --}}
    @php
        $currentRoute = Route::currentRouteName() ?? '';
        $navLinks = [
            ['label' => 'Tentang',     'href' => url('/') . '#tentang',    'external' => true],
            ['label' => 'Untuk Siapa', 'href' => url('/') . '#fitur',      'external' => true],
            ['label' => 'Cara Kerja',  'href' => url('/') . '#cara-kerja', 'external' => true],
            ['label' => 'Produk',      'href' => route('produk.index'),     'route' => 'produk.index'],
        ];
    @endphp

    @unless(View::hasSection('hide_navbar'))
    {{-- ══ NAVBAR GLOBAL ══ --}}
    <header class="relative z-20 border-b border-lake-900/10 bg-paper"
        x-data="{ mobileOpen: false }">
        <nav class="max-w-7xl mx-auto px-6 lg:px-10 flex items-center justify-between py-5">

            {{-- Logo --}}
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded flex-shrink-0">
                <span class="w-2.5 h-7 ulos-stripe-v rounded-sm"></span>
                <span class="font-display text-2xl font-semibold tracking-tight text-lake-900">TobaNiaga</span>
            </a>

            {{-- Desktop links --}}
            <div class="hidden md:flex items-center gap-8 font-medium text-sm text-lake-900/70">
                @foreach($navLinks as $link)
                @php
                    $isActive = isset($link['route']) && str_starts_with($currentRoute, $link['route']);
                @endphp
                <a href="{{ $link['href'] }}"
                   class="hover:text-ulos-maroon transition-colors focus-ring rounded {{ $isActive ? 'text-ulos-maroon font-semibold' : '' }}">
                    {{ $link['label'] }}
                </a>
                @endforeach
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">

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
                @php
                    $dashboardRoute = match(true) {
                        Auth::user()->hasRole('admin')   => route('admin.dashboard'),
                        Auth::user()->hasRole('sales')   => route('sales.dashboard'),
                        Auth::user()->hasRole('courier') => route('courier.dashboard'),
                        default                          => route('welcome'),
                    };
                @endphp

                {{-- Keranjang icon (customer only) --}}
                @role('customer')
                <a href="{{ route('customer.keranjang.index') }}"
                   class="relative p-2 rounded-lg text-lake-900/60 hover:text-lake-900 hover:bg-lake-900/5 transition-colors focus-ring
                          {{ str_starts_with($currentRoute, 'customer.keranjang') ? 'text-lake-900 bg-lake-900/5' : '' }}">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </a>
                @endrole

                {{-- User dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.outside="open = false"
                            class="flex items-center gap-2 text-sm font-semibold text-lake-900 bg-lake-900/5 px-4 py-2.5 rounded-lg hover:bg-lake-900/10 transition-colors focus-ring">
                        <span class="hidden sm:inline">{{ Auth::user()->nama ?? 'Pengguna' }}</span>
                        <svg class="w-4 h-4 text-lake-900/50" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="{'rotate-180': open}"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-paper rounded-xl shadow-xl border border-lake-900/10 py-1 z-30"
                         style="display: none;">

                        {{-- Info user --}}
                        <div class="px-4 py-3 border-b border-lake-900/8">
                            <p class="text-xs font-semibold text-ink">{{ Auth::user()->nama }}</p>
                            <p class="text-xs text-ink/40 truncate">{{ Auth::user()->email }}</p>
                        </div>

                        @role('customer')
                        <a href="{{ route('customer.keranjang.index') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors
                                  {{ str_starts_with($currentRoute, 'customer.keranjang') ? 'text-lake-900 bg-lake-900/5 font-medium' : 'text-ink/70 hover:bg-lake-900/5' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            Keranjang
                        </a>
                        <a href="{{ route('customer.pesanan.riwayat') }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm transition-colors
                                  {{ str_starts_with($currentRoute, 'customer.pesanan') ? 'text-lake-900 bg-lake-900/5 font-medium' : 'text-ink/70 hover:bg-lake-900/5' }}">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Pesanan Saya
                        </a>
                        <hr class="border-lake-900/8 my-1">
                        @endrole

                        <a href="{{ $dashboardRoute }}"
                           class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink/70 hover:bg-lake-900/5 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a>

                        <hr class="border-lake-900/8 my-1">

                        @role('customer')
                            <a href="{{ route('daftar.sales') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink/70 hover:bg-lake-900/5 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                                </svg>
                                Jadi Penjual
                            </a>
                            <a href="{{ route('daftar.kurir') }}"
                            class="flex items-center gap-3 px-4 py-2.5 text-sm text-ink/70 hover:bg-lake-900/5 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                </svg>
                                Jadi Kurir
                            </a>
                            <hr class="border-lake-900/8 my-1">
                            @endrole

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-ulos-maroon hover:bg-ulos-maroon/5 font-medium transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <button @click="mobileOpen = !mobileOpen"
                    class="md:hidden ml-2 p-2 rounded-lg text-lake-900/60 hover:bg-lake-900/5 transition-colors">
                <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </nav>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-cloak
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden border-t border-lake-900/8 bg-paper px-6 py-4 space-y-1">
            @foreach($navLinks as $link)
            <a href="{{ $link['href'] }}"
               class="block py-2.5 text-sm font-medium text-ink/70 hover:text-lake-900 transition-colors">
                {{ $link['label'] }}
            </a>
            @endforeach
            @auth
            <hr class="border-lake-900/8 my-2">
            @role('customer')
            <a href="{{ route('customer.keranjang.index') }}" class="block py-2.5 text-sm font-medium text-ink/70 hover:text-lake-900">Keranjang</a>
            <a href="{{ route('customer.pesanan.riwayat') }}" class="block py-2.5 text-sm font-medium text-ink/70 hover:text-lake-900">Pesanan Saya</a>
            @endrole
            @endauth
        </div>
    </header>
    @endunless
    {{-- ══ END NAVBAR ══════════════════════════════════════════════ --}}

    {{ $slot ?? '' }}
    @yield('content')
    @stack('scripts')
</body>
</html>
