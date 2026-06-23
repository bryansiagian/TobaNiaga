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
                @yield('sidebar_nav')
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
