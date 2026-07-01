@extends('layouts.guest')

@section('title', 'Tentang TobaNiaga — Pasar Digital UMKM Danau Toba')
@section('meta_description', 'Pelajari latar belakang, misi, dan nilai-nilai TobaNiaga — platform e-commerce yang lahir untuk mempertemukan UMKM Toba dengan pembeli dari mana saja.')

@section('content')

{{-- ── Hero ────────────────────────────────────────────────── --}}
<section class="bg-lake-900 text-paper relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1 ulos-stripe opacity-80"></div>
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
        <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-gold font-medium mb-5">Tentang Kami</p>
        <h1 class="font-display text-4xl lg:text-6xl font-medium leading-[1.05] max-w-3xl">
            Lahir dari kecintaan pada<br>
            <span class="italic text-ulos-gold">budaya dan produk Toba.</span>
        </h1>
        <p class="mt-7 text-paper/65 text-lg max-w-xl leading-relaxed">
            TobaNiaga bukan sekadar platform belanja online. Ini adalah jembatan antara kearifan lokal dan pasar modern — antara penenun ulos di tepi danau dan pembeli yang mencari sesuatu yang otentik.
        </p>
    </div>
    <div class="h-1 ulos-stripe opacity-80"></div>
</section>

{{-- ── Latar Belakang ──────────────────────────────────────── --}}
<section class="bg-paper">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28 grid lg:grid-cols-2 gap-16 items-start">
        <div>
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-5">Latar Belakang</p>
            <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 leading-snug mb-6">
                Banyak produk UMKM luar biasa yang tak pernah dikenal dunia.
            </h2>
            <div class="space-y-4 text-ink/65 leading-relaxed">
                <p>
                    Kawasan Danau Toba menyimpan kekayaan budaya dan produk lokal yang luar biasa — ulos tenun tangan, kopi arabika Lintong, ukiran kayu Batak, andaliman, dan masih banyak lagi. Namun, banyak UMKM di sini belum memiliki akses pasar yang memadai.
                </p>
                <p>
                    Mereka mengandalkan toko kecil di pinggir jalan, pasar mingguan, atau dari mulut ke mulut. Padahal, kualitas produk mereka tidak kalah dengan produk yang dijual di kota besar.
                </p>
                <p>
                    TobaNiaga lahir untuk mengubah itu — memberikan ruang digital yang dirancang khusus untuk UMKM sekitar Toba agar bisa menjangkau pembeli yang lebih luas, tanpa harus meninggalkan kampung halaman.
                </p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 gap-4 lg:mt-12">
            <div class="bg-lake-50 border border-lake-900/10 rounded-2xl p-7">
                <p class="font-display text-4xl font-medium text-lake-900 mb-2">7</p>
                <p class="text-sm text-ink/60 leading-snug">Kabupaten/kota sekitar kawasan Danau Toba</p>
            </div>
            <div class="bg-ulos-maroon/5 border border-ulos-maroon/15 rounded-2xl p-7">
                <p class="font-display text-4xl font-medium text-ulos-maroon mb-2">UMKM</p>
                <p class="text-sm text-ink/60 leading-snug">Lokal terverifikasi yang bisa bergabung</p>
            </div>
            <div class="bg-ulos-gold/8 border border-ulos-gold/20 rounded-2xl p-7">
                <p class="font-display text-4xl font-medium text-ulos-gold mb-2">100%</p>
                <p class="text-sm text-ink/60 leading-snug">Produk asli buatan pengrajin lokal Toba</p>
            </div>
            <div class="bg-lake-900 rounded-2xl p-7">
                <p class="font-display text-4xl font-medium text-paper mb-2">Gratis</p>
                <p class="text-sm text-paper/60 leading-snug">Pendaftaran untuk semua pelaku UMKM</p>
            </div>
        </div>
    </div>
</section>

{{-- ── Misi & Nilai ──────────────────────────────────────────── --}}
<section class="bg-lake-50 border-t border-lake-900/8">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">
        <div class="max-w-xl mb-14">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-5">Misi Kami</p>
            <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 leading-snug">
                Membangun ekosistem perdagangan yang adil dan berkelanjutan.
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            <div class="bg-paper border border-lake-900/10 rounded-2xl p-8">
                <div class="w-11 h-11 rounded-xl bg-lake-800 flex items-center justify-center mb-6">
                    <svg class="w-5 h-5 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-display text-lg font-medium text-lake-900 mb-3">Memperluas Jangkauan</h3>
                <p class="text-sm text-ink/60 leading-relaxed">
                    Membantu UMKM lokal menjangkau pembeli di luar daerah — termasuk diaspora Batak yang rindu produk kampung.
                </p>
            </div>

            <div class="bg-paper border border-lake-900/10 rounded-2xl p-8">
                <div class="w-11 h-11 rounded-xl bg-ulos-maroon flex items-center justify-center mb-6">
                    <svg class="w-5 h-5 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <h3 class="font-display text-lg font-medium text-lake-900 mb-3">Menjaga Kepercayaan</h3>
                <p class="text-sm text-ink/60 leading-relaxed">
                    Setiap UMKM diverifikasi sebelum bisa berjualan. Pembeli bisa berbelanja dengan tenang karena keaslian produk terjamin.
                </p>
            </div>

            <div class="bg-paper border border-lake-900/10 rounded-2xl p-8">
                <div class="w-11 h-11 rounded-xl bg-ulos-gold flex items-center justify-center mb-6">
                    <svg class="w-5 h-5 text-lake-900" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m8.66-9h-1M4.34 12h-1m15.07-6.07l-.71.71M6.34 17.66l-.71.71m12.02 0l-.71-.71M6.34 6.34l-.71-.71M12 8a4 4 0 100 8 4 4 0 000-8z"/>
                    </svg>
                </div>
                <h3 class="font-display text-lg font-medium text-lake-900 mb-3">Melestarikan Budaya</h3>
                <p class="text-sm text-ink/60 leading-relaxed">
                    Setiap transaksi di TobaNiaga turut mendukung kelangsungan hidup pengrajin dan pelestarian produk budaya Batak/Toba.
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ── Masalah yang Dipecahkan ─────────────────────────────────── --}}
<section class="bg-paper border-t border-lake-900/8">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 lg:py-28">

        {{-- Heading --}}
        <div class="max-w-2xl mb-16">
            <p class="font-mono text-xs uppercase tracking-[0.2em] text-ulos-maroon font-medium mb-5">Masalah yang Kami Pecahkan</p>
            <h2 class="font-display text-3xl lg:text-4xl font-medium text-lake-900 leading-snug">
                UMKM Toba itu ada — tapi banyak yang tidak tahu mereka ada.
            </h2>
            <p class="mt-5 text-ink/60 leading-relaxed">
                Kawasan Danau Toba menyimpan ratusan UMKM — dari yang berada di pusat kota hingga yang tersebar di pelosok pinggiran danau. Produk mereka nyata, berkualitas, dan penuh nilai budaya. Tapi ada satu masalah yang terus menghambat mereka berkembang.
            </p>
        </div>

        {{-- Dua masalah utama --}}
        <div class="grid md:grid-cols-2 gap-6 mb-12">

            {{-- Masalah 1 --}}
            <div class="bg-lake-50 border border-lake-900/10 rounded-2xl p-8">
                <div class="w-10 h-10 rounded-xl bg-ulos-maroon/10 flex items-center justify-center mb-6">
                    <svg class="w-5 h-5 text-ulos-maroon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </div>
                <h3 class="font-display text-xl font-medium text-lake-900 mb-3">
                    Tidak dikenal, bukan karena tidak layak
                </h3>
                <p class="text-sm text-ink/60 leading-relaxed mb-4">
                    Banyak masyarakat — termasuk turis lokal dan internasional yang datang ke Toba — tidak mengetahui keberadaan UMKM di sekitarnya. Kalau pun tahu, hampir selalu karena dua alasan saja: pernah melewatinya secara kebetulan, atau mendengar dari mulut ke mulut.
                </p>
                <p class="text-sm text-ink/60 leading-relaxed">
                    Tidak ada kanal yang membuat UMKM ini <em>bisa ditemukan</em> oleh orang yang memang sedang mencarinya. Akibatnya, banyak usaha yang sebenarnya bisa maju justru stagnan — bukan karena produknya kurang bagus, tapi karena <strong class="text-ink/80">tidak ada yang tahu mereka ada</strong>.
                </p>
            </div>

            {{-- Masalah 2 --}}
            <div class="bg-lake-50 border border-lake-900/10 rounded-2xl p-8">
                <div class="w-10 h-10 rounded-xl bg-ulos-gold/15 flex items-center justify-center mb-6">
                    <svg class="w-5 h-5 text-ulos-gold" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="font-display text-xl font-medium text-lake-900 mb-3">
                    Tahu pun, jarak dan waktu jadi penghalang
                </h3>
                <p class="text-sm text-ink/60 leading-relaxed mb-4">
                    Bagi masyarakat yang sudah mengenal suatu UMKM, hambatan berikutnya adalah jarak dan waktu. Kawasan Danau Toba luas — perjalanan antar kecamatan bisa memakan waktu yang cukup lama, terutama untuk area pinggiran.
                </p>
                <p class="text-sm text-ink/60 leading-relaxed">
                    Niat membeli ada, tapi kemalasan untuk menempuh perjalanan jauh sering mengalahkannya. <strong class="text-ink/80">Keinginan membeli tidak selalu berujung pada pembelian</strong> kalau tidak ada cara yang mudah untuk bertransaksi dari jarak jauh.
                </p>
            </div>
        </div>

        {{-- Solusi --}}
        <div class="bg-lake-900 rounded-2xl overflow-hidden">
            <div class="px-8 py-3 ulos-stripe opacity-60"></div>
            <div class="px-8 py-8 lg:py-10 grid lg:grid-cols-[1fr_auto] gap-8 items-center">
                <div>
                    <p class="font-mono text-xs uppercase tracking-widest text-paper/40 mb-4">Solusi TobaNiaga</p>
                    <h3 class="font-display text-xl lg:text-2xl font-medium text-paper leading-snug mb-3">
                        Satu platform untuk ditemukan, dipesan, dan diantarkan.
                    </h3>
                    <p class="text-paper/60 text-sm leading-relaxed max-w-xl">
                        TobaNiaga menjawab kedua masalah ini sekaligus — UMKM terdaftar dan bisa ditemukan oleh siapa saja yang mencarinya, sementara sistem pemesanan dan pengiriman menghilangkan hambatan jarak bagi pembeli. Cukup akses website, pilih produk, dan pesanan datang ke depan pintu.
                    </p>
                </div>
                <a href="{{ route('register') }}"
                   class="flex-shrink-0 inline-flex items-center gap-2 bg-ulos-gold text-lake-900 font-semibold px-6 py-3.5 rounded-xl hover:bg-[#d3a059] transition-colors text-sm whitespace-nowrap">
                    Mulai Sekarang
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </a>
            </div>
        </div>

    </div>
</section>

{{-- ── CTA ────────────────────────────────────────────────────── --}}
<section class="bg-lake-900 text-paper relative overflow-hidden">
    <div class="absolute top-0 left-0 right-0 h-1 ulos-stripe opacity-80"></div>
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 lg:py-20 text-center">
        <h2 class="font-display text-2xl lg:text-3xl font-medium max-w-xl mx-auto mb-8">
            Tertarik bergabung sebagai UMKM atau pembeli?
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
