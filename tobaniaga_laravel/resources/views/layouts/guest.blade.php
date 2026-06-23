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

    <style>
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
    </style>
    @stack('styles')
</head>
<body class="font-sans text-ink antialiased">
    {{ $slot ?? '' }}
    @yield('content')
    @stack('scripts')
</body>
</html>
