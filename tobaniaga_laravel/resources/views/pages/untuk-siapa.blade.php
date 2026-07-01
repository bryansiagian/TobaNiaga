@extends('layouts.guest')

@section('title', 'Untuk Siapa TobaNiaga — Pembeli, Pemilik UMKM & Kurir')
@section('meta_description', 'TobaNiaga hadir untuk tiga peran: pembeli yang ingin produk lokal otentik, pemilik UMKM yang ingin berjualan online, dan kurir yang ingin penghasilan tambahan.')

@section('content')

{{-- ── Hero ─────────────────────────────────────────────────── --}}
<section class="bg-lake-900 text-paper relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1 ulos-stripe opacity-80"></div>
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
        <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-gold font-medium mb-5">Untuk Siapa</p>
        <h1 class="font-display text-4xl lg:text-6xl font-medium leading-[1.05] max-w-3xl">
            Tiga peran,<br>
            <span class="italic text-ulos-gold">satu ekosistem.</span>
        </h1>
        <p class="mt-7 text-paper/65 text-lg max-w-xl leading-relaxed">
            TobaNiaga dirancang untuk semua pihak yang terlibat dalam rantai perdagangan lokal — dari yang membuat, yang mengantarkan, hingga yang membeli.
        </p>
    </div>
    <div class="h-1 ulos-stripe opacity-80"></div>
</section>

{{-- ── Tiga Peran ───────────────────────────────────────────── --}}

{{-- 01 Pembeli --}}
<section class="bg-paper">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28 grid lg:grid-cols-2 gap-16 items-center">
        <div>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-gold font-medium mb-4">01 — Pembeli</p>
            <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 leading-snug mb-5">
                Temukan produk asli Toba, langsung dari sumbernya.
            </h2>
            <p class="text-ink/65 leading-relaxed mb-8">
                Apakah kamu perantau Batak yang kangen produk kampung, atau pencinta produk lokal yang ingin belanja langsung dari pengrajinnya — TobaNiaga adalah tempat yang tepat.
            </p>
            <ul class="space-y-4">
                @foreach([
                    ['icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z', 'text' => 'Jelajahi ratusan produk dari berbagai UMKM terverifikasi di kawasan Danau Toba'],
                    ['icon' => 'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z', 'text' => 'Pesan dengan mudah — pilih produk, isi alamat, bayar lewat berbagai metode pembayaran'],
                    ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z', 'text' => 'Lacak status pengirimanmu secara real-time hingga produk tiba di tanganmu'],
                    ['icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z', 'text' => 'Beri ulasan untuk membantu UMKM berkembang dan bantu pembeli lain'],
                ] as $item)
                <li class="flex items-start gap-3">
                    <span class="w-8 h-8 rounded-lg bg-lake-50 border border-lake-900/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4 text-lake-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                        </svg>
                    </span>
                    <span class="text-sm text-ink/70 leading-relaxed pt-1">{{ $item['text'] }}</span>
                </li>
                @endforeach
            </ul>
            <a href="{{ route('produk.index') }}"
               class="inline-flex items-center gap-2 mt-8 bg-lake-900 text-paper font-semibold px-6 py-3 rounded-lg hover:bg-lake-800 transition-colors text-sm">
                Mulai Belanja
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        <div class="hidden lg:block">
            <div class="bg-lake-50 border border-lake-900/10 rounded-2xl p-8 space-y-3">
                <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 mb-4">Produk Populer</p>
                @foreach(['Ulos Batak Toba', 'Kopi Lintong', 'Ukiran Kayu Batak', 'Andaliman Toba'] as $i => $produk)
                <div class="flex items-center gap-3 py-2.5 border-b border-lake-900/6 last:border-0">
                    <span class="w-8 h-8 rounded-lg bg-lake-900/8 flex items-center justify-center font-mono text-xs text-lake-900/50">{{ sprintf('%02d', $i + 1) }}</span>
                    <span class="text-sm font-medium text-ink/80">{{ $produk }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- 02 Pemilik UMKM --}}
<section class="bg-lake-50 border-t border-lake-900/8">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28 grid lg:grid-cols-2 gap-16 items-center">
        <div class="hidden lg:block order-last lg:order-first">
            <div class="bg-paper border border-lake-900/10 rounded-2xl p-8 space-y-4">
                <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 mb-2">Dashboard UMKM</p>
                @foreach([
                    ['label' => 'Produk Aktif',      'val' => '12 produk',    'color' => 'text-lake-900'],
                    ['label' => 'Pesanan Baru',       'val' => '3 pesanan',    'color' => 'text-ulos-gold'],
                    ['label' => 'Rating Toko',        'val' => '4.8 / 5.0',   'color' => 'text-green-700'],
                    ['label' => 'Saldo Tersedia',     'val' => 'Rp 1.250.000','color' => 'text-lake-900'],
                ] as $stat)
                <div class="flex items-center justify-between py-2.5 border-b border-lake-900/6 last:border-0">
                    <span class="text-sm text-ink/50">{{ $stat['label'] }}</span>
                    <span class="text-sm font-semibold {{ $stat['color'] }}">{{ $stat['val'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-4">02 — Pemilik UMKM</p>
            <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 leading-snug mb-5">
                Kelola toko dan jangkau pembeli lebih luas.
            </h2>
            <p class="text-ink/65 leading-relaxed mb-8">
                Tidak perlu tahu coding atau desain. Cukup daftarkan UMKM-mu, unggah produk, dan mulai terima pesanan dari pembeli di mana saja.
            </p>
            <ul class="space-y-4">
                @foreach([
                    ['text' => 'Kelola katalog produk sendiri — tambah, edit, atau nonaktifkan kapan saja'],
                    ['text' => 'Pantau pesanan masuk dan status pengiriman dari satu halaman'],
                    ['text' => 'Buat promo diskon sendiri atau ikut promo platform-wide dari admin'],
                    ['text' => 'Analisis pendapatan per periode dan cairkan dana hasil penjualan ke rekening bank'],
                ] as $item)
                <li class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full bg-ulos-maroon/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-ulos-maroon" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </span>
                    <span class="text-sm text-ink/70 leading-relaxed pt-0.5">{{ $item['text'] }}</span>
                </li>
                @endforeach
            </ul>
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 mt-8 bg-ulos-maroon text-paper font-semibold px-6 py-3 rounded-lg hover:bg-[#7a1f2c] transition-colors text-sm">
                Daftar sebagai UMKM
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- 03 Kurir --}}
<section class="bg-paper border-t border-lake-900/8">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28 grid lg:grid-cols-2 gap-16 items-center">
        <div>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-gold font-medium mb-4">03 — Kurir</p>
            <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 leading-snug mb-5">
                Antar pesanan, dapatkan penghasilan.
            </h2>
            <p class="text-ink/65 leading-relaxed mb-8">
                Ingin penghasilan tambahan di sekitar kawasan Toba? Daftar sebagai kurir TobaNiaga dan mulai ambil tugas pengiriman kapan saja, sesuai waktu luangmu.
            </p>
            <ul class="space-y-4">
                @foreach([
                    ['text' => 'Sistem job pool — ambil tugas pengiriman sesuai kapasitas dan lokasi, tanpa paksaan'],
                    ['text' => 'Pantau semua riwayat pengiriman dari dashboard khusus kurir'],
                    ['text' => 'Cairkan ongkos kirim yang kamu kumpulkan kapan saja ke rekening bank pilihanmu'],
                    ['text' => 'Proses pendaftaran online — cukup isi data diri dan upload dokumen identitas'],
                ] as $item)
                <li class="flex items-start gap-3">
                    <span class="w-5 h-5 rounded-full bg-ulos-gold/15 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-3 h-3 text-ulos-gold" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </span>
                    <span class="text-sm text-ink/70 leading-relaxed pt-0.5">{{ $item['text'] }}</span>
                </li>
                @endforeach
            </ul>
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 mt-8 bg-ulos-gold text-lake-900 font-semibold px-6 py-3 rounded-lg hover:bg-[#d3a059] transition-colors text-sm">
                Daftar sebagai Kurir
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
        <div class="hidden lg:block">
            <div class="bg-lake-50 border border-lake-900/10 rounded-2xl p-8">
                <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30 mb-5">Alur Kerja Kurir</p>
                <div class="space-y-5">
                    @foreach([
                        ['step' => '01', 'label' => 'Ambil Tugas', 'desc' => 'Pilih pesanan yang tersedia di pool pengiriman'],
                        ['step' => '02', 'label' => 'Jemput Paket', 'desc' => 'Kunjungi lokasi UMKM dan ambil paket'],
                        ['step' => '03', 'label' => 'Antar ke Pembeli', 'desc' => 'Kirimkan paket dan konfirmasi penerimaan'],
                        ['step' => '04', 'label' => 'Cairkan Dana', 'desc' => 'Kumpulkan ongkir dan cairkan ke rekeningmu'],
                    ] as $s)
                    <div class="flex items-start gap-4">
                        <span class="font-mono text-xs font-medium text-ulos-gold flex-shrink-0 w-6 pt-0.5">{{ $s['step'] }}</span>
                        <div>
                            <p class="text-sm font-semibold text-lake-900">{{ $s['label'] }}</p>
                            <p class="text-xs text-ink/50 mt-0.5">{{ $s['desc'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ── CTA ────────────────────────────────────────────────────── --}}
<section class="bg-lake-900 text-paper relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1 ulos-stripe opacity-80"></div>
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 lg:py-20 text-center">
        <h2 class="font-display text-2xl lg:text-3xl font-medium max-w-xl mx-auto mb-8">
            Mulai hari ini — gratis, cepat, dan mudah.
        </h2>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 bg-ulos-gold text-lake-900 font-semibold px-7 py-3.5 rounded-lg hover:bg-[#d3a059] transition-colors">
                Daftar Sekarang
            </a>
            <a href="{{ route('cara-kerja') }}"
               class="inline-flex items-center gap-2 text-paper font-semibold px-7 py-3.5 rounded-lg border border-paper/25 hover:bg-paper/10 transition-colors">
                Lihat Cara Kerja
            </a>
        </div>
    </div>
</section>

{{-- ── Footer ─────────────────────────────────────────────────── --}}
<footer class="bg-paper border-t border-lake-900/10">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-10 flex flex-col sm:flex-row items-center justify-between gap-4">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 focus-ring rounded">
            <img src="{{ asset('images/logo-tobaniaga.png') }}" alt="TobaNiaga" class="h-10 w-10 rounded-lg object-contain">
        </a>
        <p class="font-mono text-xs text-ink/50">&copy; {{ date('Y') }} TobaNiaga</p>
    </div>
</footer>

@endsection
