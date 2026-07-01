@extends('layouts.guest')

@section('title', 'Cara Kerja TobaNiaga — Panduan Lengkap Mulai Berjualan & Berbelanja')
@section('meta_description', 'Panduan lengkap cara mulai di TobaNiaga: cara daftar, cara berjualan untuk UMKM, cara berbelanja untuk pembeli, dan cara kerja kurir.')

@section('content')

{{-- ── Hero ─────────────────────────────────────────────────── --}}
<section class="bg-lake-900 text-paper relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1 ulos-stripe opacity-80"></div>
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
        <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-gold font-medium mb-5">Cara Kerja</p>
        <h1 class="font-display text-4xl lg:text-6xl font-medium leading-[1.05] max-w-3xl">
            Mudah dimulai,<br>
            <span class="italic text-ulos-gold">dalam beberapa langkah.</span>
        </h1>
        <p class="mt-7 text-paper/65 text-lg max-w-xl leading-relaxed">
            Panduan lengkap untuk pembeli, pemilik UMKM, dan kurir — dari pendaftaran hingga transaksi pertamamu.
        </p>

        {{-- Quick nav --}}
        <div class="flex flex-wrap gap-3 mt-10">
            @foreach(['Untuk Pembeli', 'Untuk UMKM', 'Untuk Kurir'] as $tab)
            <a href="#{{ Str::slug($tab) }}"
               class="font-mono text-xs px-4 py-2 rounded-full border border-paper/20 text-paper/70 hover:bg-paper/10 hover:text-paper transition-colors">
                {{ $tab }}
            </a>
            @endforeach
        </div>
    </div>
    <div class="h-1 ulos-stripe opacity-80"></div>
</section>

{{-- ── Untuk Pembeli ────────────────────────────────────────── --}}
<section id="untuk-pembeli" class="bg-paper scroll-mt-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
        <div class="flex items-center gap-4 mb-12">
            <span class="w-10 h-10 rounded-xl bg-lake-900 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </span>
            <div>
                <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30">Panduan</p>
                <h2 class="font-display text-2xl font-medium text-lake-900">Untuk Pembeli</h2>
            </div>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach([
                ['no' => '01', 'title' => 'Buat Akun', 'desc' => 'Daftar dengan email dan pilih role "Pembeli". Verifikasi email lewat kode OTP yang dikirim ke inboxmu.', 'color' => 'bg-lake-50 border-lake-900/10'],
                ['no' => '02', 'title' => 'Jelajahi Produk', 'desc' => 'Cari produk berdasarkan kategori, nama, atau UMKM. Filter sesuai kebutuhanmu dan lihat detail produk.', 'color' => 'bg-lake-50 border-lake-900/10'],
                ['no' => '03', 'title' => 'Checkout', 'desc' => 'Masukkan ke keranjang, isi alamat pengiriman, pilih metode kirim, lalu bayar lewat transfer bank atau QRIS.', 'color' => 'bg-lake-50 border-lake-900/10'],
                ['no' => '04', 'title' => 'Terima & Ulasan', 'desc' => 'Pantau status pengiriman secara real-time. Setelah terima paket, beri ulasan untuk produk yang kamu beli.', 'color' => 'bg-lake-50 border-lake-900/10'],
            ] as $step)
            <div class="bg-lake-50 border border-lake-900/10 rounded-2xl p-7 relative overflow-hidden">
                <p class="font-mono text-5xl font-medium text-lake-900/8 absolute top-4 right-5 leading-none select-none">{{ $step['no'] }}</p>
                <p class="font-mono text-sm font-medium text-ulos-gold mb-3 relative">{{ $step['no'] }}</p>
                <h3 class="font-display text-lg font-medium text-lake-900 mb-3 relative">{{ $step['title'] }}</h3>
                <p class="text-sm text-ink/60 leading-relaxed relative">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="mt-10 flex items-center gap-4 p-5 bg-lake-50 border border-lake-900/10 rounded-xl">
            <svg class="w-5 h-5 text-lake-800 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-ink/60">Pembayaran diproses melalui Midtrans — platform pembayaran terpercaya yang mendukung transfer bank, virtual account, dan QRIS.</p>
        </div>
    </div>
</section>

{{-- ── Untuk UMKM ───────────────────────────────────────────── --}}
<section id="untuk-umkm" class="bg-lake-50 border-t border-lake-900/8 scroll-mt-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
        <div class="flex items-center gap-4 mb-12">
            <span class="w-10 h-10 rounded-xl bg-ulos-maroon flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3m16 0h-5m-7 0h7"/>
                </svg>
            </span>
            <div>
                <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30">Panduan</p>
                <h2 class="font-display text-2xl font-medium text-lake-900">Untuk Pemilik UMKM</h2>
            </div>
        </div>

        <div class="space-y-4">
            @foreach([
                ['no' => '01', 'title' => 'Daftar & Verifikasi', 'desc' => 'Buat akun dengan pilih role "Pemilik UMKM", isi data toko (nama, kategori, lokasi, nomor WA), lalu verifikasi email. Setelah login, lengkapi dokumen identitas (NIK, KTP, KK) untuk proses verifikasi admin.', 'badge' => '~5 menit'],
                ['no' => '02', 'title' => 'Tunggu Verifikasi Admin', 'desc' => 'Admin TobaNiaga akan memeriksa data UMKM dan dokumen identitasmu. Proses ini membutuhkan 1-2 hari kerja. Kamu akan melihat notifikasi status di dashboard setelah selesai.', 'badge' => '1-2 hari kerja'],
                ['no' => '03', 'title' => 'Tambah Produk', 'desc' => 'Setelah akun aktif, mulai upload produk — isi nama, deskripsi, harga, stok, kategori, dan foto. Kamu bisa kelola semua produk dari menu "Produk Saya" di dashboard.', 'badge' => 'Setelah aktif'],
                ['no' => '04', 'title' => 'Kelola Pesanan', 'desc' => 'Saat ada pesanan masuk, kamu akan melihatnya di menu "Pesanan Masuk". Konfirmasi pesanan, siapkan paket, dan kurir akan menjemput setelah kamu setujui.', 'badge' => 'Otomatis'],
                ['no' => '05', 'title' => 'Cairkan Pendapatan', 'desc' => 'Saldo dari pesanan yang sudah selesai bisa kamu cairkan kapan saja ke rekening bank yang kamu daftarkan. Pengajuan pencairan diproses oleh admin dalam 1-2 hari kerja.', 'badge' => 'Kapan saja'],
            ] as $step)
            <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
                <div class="flex items-start gap-5 px-6 py-5">
                    <span class="font-mono text-sm font-medium text-ulos-maroon flex-shrink-0 w-6 pt-0.5">{{ $step['no'] }}</span>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <h3 class="text-sm font-semibold text-lake-900">{{ $step['title'] }}</h3>
                            <span class="font-mono text-[10px] px-2 py-0.5 rounded-full bg-ulos-maroon/8 text-ulos-maroon">{{ $step['badge'] }}</span>
                        </div>
                        <p class="text-sm text-ink/60 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── Untuk Kurir ──────────────────────────────────────────── --}}
<section id="untuk-kurir" class="bg-paper border-t border-lake-900/8 scroll-mt-20">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
        <div class="flex items-center gap-4 mb-12">
            <span class="w-10 h-10 rounded-xl bg-ulos-gold flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-lake-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </span>
            <div>
                <p class="font-mono text-[10px] uppercase tracking-widest text-ink/30">Panduan</p>
                <h2 class="font-display text-2xl font-medium text-lake-900">Untuk Kurir</h2>
            </div>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-8">
            @foreach([
                ['no' => '01', 'title' => 'Daftar & Verifikasi', 'desc' => 'Buat akun dengan pilih role "Kurir". Setelah verifikasi email, lengkapi dokumen identitas (NIK, KTP, KK) dari dashboard untuk diverifikasi admin.'],
                ['no' => '02', 'title' => 'Ambil Tugas dari Pool', 'desc' => 'Setelah akun aktif, kamu bisa melihat dan mengambil tugas pengiriman yang tersedia di menu "Tugas Aktif". Pilih sesuai lokasi dan kapasitasmu.'],
                ['no' => '03', 'title' => 'Jemput & Antar Paket', 'desc' => 'Kunjungi lokasi UMKM untuk jemput paket, lalu antar ke alamat pembeli. Update status pengiriman di dashboard saat setiap tahap selesai.'],
                ['no' => '04', 'title' => 'Cairkan Ongkos Kirim', 'desc' => 'Ongkos kirim dari pengiriman yang selesai masuk ke saldomu. Daftarkan rekening bank dan cairkan kapan saja dari menu "Pencairan Dana".'],
            ] as $step)
            <div class="bg-lake-50 border border-lake-900/10 rounded-2xl p-7 relative overflow-hidden">
                <p class="font-mono text-5xl font-medium text-lake-900/8 absolute top-4 right-5 leading-none select-none">{{ $step['no'] }}</p>
                <p class="font-mono text-sm font-medium text-ulos-gold mb-3 relative">{{ $step['no'] }}</p>
                <h3 class="font-display text-lg font-medium text-lake-900 mb-3 relative">{{ $step['title'] }}</h3>
                <p class="text-sm text-ink/60 leading-relaxed relative">{{ $step['desc'] }}</p>
            </div>
            @endforeach
        </div>

        <div class="flex items-start gap-4 p-5 bg-ulos-gold/8 border border-ulos-gold/20 rounded-xl">
            <svg class="w-5 h-5 text-ulos-gold flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-sm text-ink/60">Sistem kurir TobaNiaga menggunakan model <strong class="text-ink/80">job pool</strong> — artinya tidak ada penugasan paksa. Kamu bebas memilih pengiriman mana yang ingin diambil, kapan saja kamu siap.</p>
        </div>
    </div>
</section>

{{-- ── FAQ Singkat ──────────────────────────────────────────── --}}
<section class="bg-lake-50 border-t border-lake-900/8">
    <div class="max-w-4xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
        <div class="text-center mb-12">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-4">FAQ</p>
            <h2 class="font-display text-3xl font-medium text-lake-900">Pertanyaan umum.</h2>
        </div>

        <div class="space-y-3" x-data="{ open: null }">
            @foreach([
                ['q' => 'Apakah mendaftar di TobaNiaga gratis?', 'a' => 'Ya, pendaftaran untuk semua peran (pembeli, pemilik UMKM, maupun kurir) sepenuhnya gratis.'],
                ['q' => 'Berapa lama proses verifikasi UMKM?', 'a' => 'Proses verifikasi dokumen dan UMKM membutuhkan 1-2 hari kerja setelah data lengkap dikirimkan.'],
                ['q' => 'Metode pembayaran apa yang tersedia?', 'a' => 'TobaNiaga mendukung transfer bank (BCA, BNI, BRI, Mandiri), virtual account, dan QRIS melalui Midtrans.'],
                ['q' => 'Bagaimana kalau dokumen saya ditolak?', 'a' => 'Admin akan memberikan alasan penolakan yang bisa kamu lihat di dashboard. Kamu bisa memperbaiki dan mengupload ulang dokumen kapan saja.'],
                ['q' => 'Apakah saya bisa jadi UMKM sekaligus kurir?', 'a' => 'Untuk saat ini, setiap akun hanya bisa memiliki satu peran aktif. Daftarkan akun terpisah untuk peran berbeda.'],
            ] as $i => $faq)
            <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
                <button @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-lake-50/50 transition-colors">
                    <span class="text-sm font-semibold text-lake-900 pr-4">{{ $faq['q'] }}</span>
                    <svg class="w-4 h-4 text-lake-900/40 flex-shrink-0 transition-transform duration-200"
                         :class="open === {{ $i }} ? 'rotate-180' : ''"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div x-show="open === {{ $i }}" x-cloak
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="px-6 pb-4">
                    <p class="text-sm text-ink/60 leading-relaxed">{{ $faq['a'] }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ── CTA ────────────────────────────────────────────────────── --}}
<section class="bg-lake-900 text-paper relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1 ulos-stripe opacity-80"></div>
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 lg:py-20 text-center">
        <h2 class="font-display text-2xl lg:text-3xl font-medium max-w-xl mx-auto mb-8">
            Siap memulai perjalananmu di TobaNiaga?
        </h2>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('register') }}"
               class="inline-flex items-center gap-2 bg-ulos-gold text-lake-900 font-semibold px-7 py-3.5 rounded-lg hover:bg-[#d3a059] transition-colors">
                Daftar Sekarang
            </a>
            <a href="{{ route('tentang') }}"
               class="inline-flex items-center gap-2 text-paper font-semibold px-7 py-3.5 rounded-lg border border-paper/25 hover:bg-paper/10 transition-colors">
                Tentang TobaNiaga
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
