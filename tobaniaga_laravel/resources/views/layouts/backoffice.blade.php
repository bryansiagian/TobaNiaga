<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Backoffice') — TobaNiaga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'lake': {
                            50:  '#f0f4f8',
                            100: '#dce8f0',
                            600: '#2d6a8a',
                            800: '#1a4a6b',
                            900: '#0f2d45',
                        },
                        'ulos': {
                            maroon: '#8f2333',
                            gold:   '#c49044',
                        },
                        'paper': '#faf8f5',
                        'ink':   '#1a1a1a',
                    },
                    fontFamily: {
                        display: ['Fraunces', 'Georgia', 'serif'],
                        sans:    ['Inter', 'system-ui', 'sans-serif'],
                        mono:    ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0f4f8; }

        .ulos-stripe {
            background: repeating-linear-gradient(
                90deg,
                #8f2333 0px, #8f2333 8px,
                #c49044 8px, #c49044 14px,
                #1a4a6b 14px, #1a4a6b 22px,
                #faf8f5 22px, #faf8f5 26px,
                #1a4a6b 26px, #1a4a6b 34px,
                #c49044 34px, #c49044 40px,
                #8f2333 40px, #8f2333 48px
            );
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 0.875rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: #0f2d45cc;
            transition: background-color 0.15s, color 0.15s;
        }
        .sidebar-link:hover { background-color: #0f2d4510; color: #0f2d45; }
        .sidebar-link.active { background-color: #1a4a6b; color: #faf8f5; }
        .sidebar-link.active svg { opacity: 1; }
        .sidebar-link svg { opacity: 0.5; }
        .sidebar-link.active:hover { background-color: #1a4a6b; }

        .focus-ring:focus-visible { outline: 2px solid #1a4a6b; outline-offset: 2px; }

        .stat-card {
            background: #faf8f5;
            border: 1px solid #0f2d4512;
            border-radius: 0.875rem;
            padding: 1.5rem;
        }
    </style>

    @stack('styles')
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-lake-900/40 z-20 lg:hidden"></div>

    <div class="flex h-full min-h-screen">

        {{-- ============ SIDEBAR ============ --}}
        <aside class="fixed inset-y-0 left-0 z-30 w-64 bg-paper border-r border-lake-900/10 flex flex-col
                       transform transition-transform duration-200 ease-out
                       lg:translate-x-0 lg:static lg:inset-auto"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            {{-- Logo --}}
            <div class="px-5 py-5 border-b border-lake-900/10">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded">
                    <span class="w-2 h-6 rounded-sm inline-block" style="background: repeating-linear-gradient(180deg, #8f2333 0 4px, #c49044 4px 7px, #1a4a6b 7px 11px)"></span>
                    <span class="font-display text-xl font-semibold text-lake-900">TobaNiaga</span>
                </a>
                <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40 mt-1 ml-[22px]">
                    @yield('role_label', 'Backoffice')
                </p>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">

                {{-- Beranda — semua role --}}
                @php
                    $berandaRoute = match(true) {
                        auth()->user()->hasRole('admin')   => route('admin.dashboard'),
                        auth()->user()->hasRole('sales')   => route('sales.dashboard'),
                        auth()->user()->hasRole('courier') => route('courier.dashboard'),
                        default                            => route('welcome'),
                    };
                @endphp
                <a href="{{ $berandaRoute }}" class="sidebar-link {{ request()->routeIs('admin.dashboard', 'sales.dashboard', 'courier.dashboard') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Beranda
                </a>

                {{-- ── ADMIN ── --}}
                @role('admin')
                {{-- ── Grup Pengguna ── --}}
                <div x-data="{ open: {{ request()->routeIs('admin.users.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="sidebar-link w-full justify-between"
                            :class="open ? 'text-lake-900' : ''">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pengguna
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-40 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-0.5 ml-3 pl-3 border-l border-lake-900/10 space-y-0.5">
                        <a href="{{ route('admin.users.index') }}"
                        class="sidebar-link text-[13px] {{ request()->routeIs('admin.users.index') && !request()->has('status') ? 'active' : '' }}">
                            Semua Pengguna
                        </a>
                    </div>
                </div>
                {{-- ── Grup UMKM ── --}}
                <div x-data="{ open: {{ request()->routeIs('admin.umkm.*') ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="sidebar-link w-full justify-between"
                            :class="open ? 'text-lake-900' : ''">
                        <span class="flex items-center gap-3">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
                            </svg>
                            UMKM
                        </span>
                        <svg class="w-3.5 h-3.5 opacity-40 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div x-show="open"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="mt-0.5 ml-3 pl-3 border-l border-lake-900/10 space-y-0.5">
                        <a href="{{ route('admin.umkm.index') }}"
                        class="sidebar-link text-[13px] {{ request()->routeIs('admin.umkm.index') ? 'active' : '' }}">
                            Terdaftar
                        </a>
                        <a href="{{ route('admin.umkm.pending') }}"
                        class="sidebar-link text-[13px] {{ request()->routeIs('admin.umkm.pending') ? 'active' : '' }}">
                            Menunggu Verifikasi
                        </a>
                        <a href="{{ route('admin.umkm.rejected') }}"
                        class="sidebar-link text-[13px] {{ request()->routeIs('admin.umkm.rejected') ? 'active' : '' }}">
                            Ditolak
                        </a>
                    </div>
                </div>

                {{-- ── Standalone ── --}}
                <a href="{{ route('admin.kategori-umkm.index') }}"
                class="sidebar-link {{ request()->routeIs('admin.kategori-umkm.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 11V6a3 3 0 013-3z"/>
                    </svg>
                    Kategori UMKM
                </a>

                <a href="{{ route('admin.kategori-produk.index') }}" class="sidebar-link {{ request()->routeIs('admin.kategori-produk.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/>
                    </svg>
                    Kategori Produk
                </a>

                <a href="#" class="sidebar-link {{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                    </svg>
                    Semua Pesanan
                </a>

                <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-3 pt-5 pb-1.5">Sistem</p>

                <a href="#" class="sidebar-link">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    Pengaturan
                </a>
                @endrole

                {{-- ── SALES ── --}}
                @role('sales')
                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-3 pt-5 pb-1.5">Toko</p>

                    <a href="{{ route('sales.produk.index') }}" class="sidebar-link {{ request()->routeIs('sales.produk.*') ? 'active' : '' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Produk Saya
                    </a>

                    <a href="#" class="sidebar-link {{ request()->routeIs('sales.pesanan.*') ? 'active' : '' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Pesanan Masuk
                    </a>

                    <a href="{{ route('sales.pendapatan.index')}}" class="sidebar-link">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Pendapatan
                    </a>

                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-3 pt-5 pb-1.5">Akun</p>

                    <a href="{{ route('sales.profil.index') }}" class="sidebar-link {{ request()->routeIs('sales.profil.*') ? 'active' : '' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m16 0h-5m-7 0h7"/>
                        </svg>
                        Profil UMKM
                    </a>
                @endrole

                {{-- ── COURIER ── --}}
                @role('courier')
                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-3 pt-5 pb-1.5">Pengiriman</p>

                    <a href="#" class="sidebar-link {{ request()->routeIs('courier.tugas.*') ? 'active' : '' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                        Tugas Aktif
                    </a>

                    <a href="#" class="sidebar-link {{ request()->routeIs('courier.riwayat.*') ? 'active' : '' }}">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M5 12h14"/>
                        </svg>
                        Riwayat Pengiriman
                    </a>

                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-3 pt-5 pb-1.5">Akun</p>

                    <a href="#" class="sidebar-link">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Profil Saya
                    </a>
                @endrole

            </nav>

            {{-- User info + logout --}}
            <div class="px-3 py-4 border-t border-lake-900/10">
                <div class="flex items-center gap-3 px-2 py-2 mb-2">
                    <span class="w-8 h-8 rounded-full bg-lake-800 text-paper flex items-center justify-center text-xs font-bold uppercase flex-shrink-0">
                        {{ mb_substr(auth()->user()->nama ?? 'U', 0, 1) }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-lake-900 truncate">{{ auth()->user()->nama ?? 'User' }}</p>
                        <p class="font-mono text-[10px] text-ink/40 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="sidebar-link w-full text-ulos-maroon hover:!bg-ulos-maroon/8 hover:!text-ulos-maroon focus-ring">
                        <svg class="w-4 h-4 !opacity-70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        {{-- ============ MAIN CONTENT ============ --}}
        <div class="flex-1 flex flex-col min-w-0">

            {{-- Top bar --}}
            <header class="sticky top-0 z-10 bg-paper/80 backdrop-blur border-b border-lake-900/10">
                <div class="h-0.5 ulos-stripe"></div>
                <div class="px-6 py-3.5 flex items-center justify-between">
                    {{-- Mobile hamburger --}}
                    <button @click="sidebarOpen = !sidebarOpen"
                            class="lg:hidden p-2 rounded-lg hover:bg-lake-900/5 text-lake-900 focus-ring">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>

                    <div class="hidden lg:block">
                        <h1 class="font-display text-xl font-medium text-lake-900">@yield('page_title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center gap-3 ml-auto lg:ml-0">
                        {{-- Tanggal --}}
                        <span class="hidden sm:block font-mono text-xs text-ink/40">
                            {{ now()->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>
            </header>

            {{-- Page content --}}
            <main class="flex-1 px-6 py-8 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
