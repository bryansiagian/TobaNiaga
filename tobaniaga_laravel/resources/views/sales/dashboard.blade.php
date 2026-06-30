@extends('layouts.backoffice')

@section('title', 'Dashboard Sales')
@section('role_label', 'Sales / UMKM')
@section('page_title', 'Dashboard Sales')

@section('content')

    {{-- Greeting --}}
    <div class="mb-8">
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Selamat datang kembali</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">{{ auth()->user()->nama ?? 'Sales' }}</h2>
        @if($umkm)
        <p class="text-sm text-ink/50 mt-1">{{ $umkm->nama_umkm }}</p>
        @endif
    </div>

    {{-- Status verifikasi --}}
    @if($umkm)
    @php
        $verifikasiKode = $umkm->statusVerifikasi?->kode ?? '';
    @endphp
    @if($verifikasiKode !== 'verified')
    <div class="mb-6 flex items-start gap-4 px-5 py-4 rounded-xl border
        {{ $verifikasiKode === 'rejected' ? 'bg-red-50 border-red-200' : 'bg-yellow-50 border-yellow-200' }}">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0 {{ $verifikasiKode === 'rejected' ? 'text-red-500' : 'text-yellow-500' }}"
             fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div>
            @if($verifikasiKode === 'rejected')
            <p class="text-sm font-semibold text-red-700">Verifikasi Ditolak</p>
            <p class="text-xs text-red-600 mt-0.5">{{ $umkm->catatan_penolakan ?? 'Silakan hubungi admin untuk informasi lebih lanjut.' }}</p>
            @else
            <p class="text-sm font-semibold text-yellow-700">Menunggu Verifikasi</p>
            <p class="text-xs text-yellow-600 mt-0.5">Tokomu sedang ditinjau oleh tim TobaNiaga. Fitur berjualan akan aktif setelah terverifikasi.</p>
            @endif
        </div>
    </div>
    @endif
    @endif

    {{-- Banner kelengkapan dokumen --}}
    @php
        $statusDokumen = auth()->user()->statusVerifikasiDokumen?->kode;
        $dokumenLengkap = auth()->user()->dokumenLengkap();
    @endphp

    @if(!$dokumenLengkap)
    <div class="mb-6 px-5 py-4 rounded-xl bg-yellow-50 border border-yellow-200 flex items-start gap-3">
        <svg class="w-5 h-5 text-yellow-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm font-semibold text-yellow-700">Dokumen identitas belum dilengkapi</p>
            <p class="text-xs text-yellow-600 mt-0.5">Lengkapi NIK, foto KTP, dan KK untuk proses verifikasi akun penjual.</p>
        </div>
        <a href="{{ route('sales.dokumen') }}"
           class="flex-shrink-0 px-4 py-2 bg-yellow-500 text-white text-xs font-semibold rounded-lg hover:bg-yellow-600 transition-colors">
            Lengkapi Sekarang
        </a>
    </div>
    @elseif($statusDokumen === 'pending')
    <div class="mb-6 px-5 py-4 rounded-xl bg-blue-50 border border-blue-200 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <div>
            <p class="text-sm font-semibold text-blue-700">Dokumen sedang diverifikasi</p>
            <p class="text-xs text-blue-600 mt-0.5">Admin sedang memeriksa dokumenmu. Estimasi 1-2 hari kerja.</p>
        </div>
    </div>
    @elseif($statusDokumen === 'rejected')
    <div class="mb-6 px-5 py-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm font-semibold text-red-700">Dokumen ditolak</p>
            @if(auth()->user()->catatan_penolakan_dokumen)
            <p class="text-xs text-red-600 mt-0.5">Alasan: {{ auth()->user()->catatan_penolakan_dokumen }}</p>
            @endif
        </div>
        <a href="{{ route('sales.dokumen') }}"
           class="flex-shrink-0 px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition-colors">
            Upload Ulang
        </a>
    </div>
    @endif

    {{-- Stat cards --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Produk Aktif</p>
            <p class="font-display text-3xl font-medium text-lake-900">{{ $totalProdukAktif }}</p>
            <p class="text-xs text-ink/50 mt-1">{{ $totalProdukAktif === 0 ? 'Belum ada produk' : 'Produk tersedia' }}</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Pesanan Baru</p>
            <p class="font-display text-3xl font-medium text-lake-900">{{ $totalPesananBaru }}</p>
            <p class="text-xs text-ink/50 mt-1">{{ $totalPesananBaru === 0 ? 'Belum ada pesanan' : 'Menunggu konfirmasi' }}</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Diproses</p>
            <p class="font-display text-3xl font-medium {{ $totalDiproses > 0 ? 'text-ulos-maroon' : 'text-lake-900' }}">
                {{ $totalDiproses }}
            </p>
            <p class="text-xs text-ink/50 mt-1">{{ $totalDiproses === 0 ? 'Tidak ada' : 'Sedang diproses' }}</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Pendapatan</p>
            <p class="font-display text-2xl font-medium text-lake-900">
                Rp{{ number_format($pendapatanBulan, 0, ',', '.') }}
            </p>
            <p class="text-xs text-ink/50 mt-1">{{ now()->translatedFormat('F Y') }}</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_300px] gap-6">

        {{-- Pesanan terbaru --}}
        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
                <h3 class="font-display text-base font-medium text-lake-900">Pesanan Terbaru</h3>
                <a href="#" class="font-mono text-xs text-lake-800 hover:underline">Lihat semua</a>
            </div>

            @if($pesananTerbaru->isEmpty())
            <div class="px-6 py-14 text-center">
                <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-sm text-ink/40">Belum ada pesanan masuk.</p>
            </div>
            @else
            <div class="divide-y divide-lake-900/6">
                @foreach($pesananTerbaru as $p)
                @php
                    $kode = $p->status->kode ?? '';
                    $badge = match($kode) {
                        'menunggu_pembayaran' => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Menunggu Bayar'],
                        'diproses'            => ['bg-blue-50 text-blue-700 border-blue-200',       'Diproses'],
                        'dikirim'             => ['bg-purple-50 text-purple-700 border-purple-200', 'Dikirim'],
                        'selesai'             => ['bg-green-50 text-green-700 border-green-200',    'Selesai'],
                        'batal'               => ['bg-red-50 text-red-700 border-red-200',          'Dibatalkan'],
                        default               => ['bg-gray-50 text-gray-600 border-gray-200',       $p->status->label ?? '-'],
                    };
                @endphp
                <div class="px-6 py-4 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-mono font-semibold text-ink truncate">{{ $p->no_pesanan }}</p>
                        <p class="text-xs text-ink/40 mt-0.5">{{ $p->customer->nama ?? '-' }} · {{ $p->created_at->diffForHumans() }}</p>
                        <p class="text-xs text-ink/50 mt-0.5">
                            {{ $p->detail->count() }} produk
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                        <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full border {{ $badge[0] }}">
                            {{ $badge[1] }}
                        </span>
                        <p class="text-sm font-semibold text-lake-900">
                            Rp{{ number_format($p->total_harga, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Quick actions + status toko --}}
        <div class="space-y-4">
            <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-lake-900/8">
                    <h3 class="font-display text-base font-medium text-lake-900">Aksi Cepat</h3>
                </div>
                <div class="p-4 space-y-2">
                    <a href="{{ route('sales.produk.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-lake-50 transition-colors">
                        <span class="w-8 h-8 rounded-lg bg-lake-800 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-paper" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-lake-900">Kelola Produk</p>
                            <p class="text-xs text-ink/40">Tambah & atur produk tokomu</p>
                        </div>
                    </a>

                    <a href="{{ route('sales.pendapatan.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-lake-50 transition-colors">
                        <span class="w-8 h-8 rounded-lg bg-lake-50 border border-lake-900/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-lake-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-lake-900">Analisis Pendapatan</p>
                            <p class="text-xs text-ink/40">Lihat tren penjualan & keuangan</p>
                        </div>
                    </a>

                    <a href="{{ route('sales.profil.index') }}" class="flex items-center gap-3 p-3 rounded-lg hover:bg-lake-50 transition-colors">
                        <span class="w-8 h-8 rounded-lg bg-lake-50 border border-lake-900/10 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3.5 h-3.5 text-lake-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-medium text-lake-900">Edit Profil UMKM</p>
                            <p class="text-xs text-ink/40">Perbarui info tokomu</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Status toko --}}
            @if($umkm)
            @php
                $vKode = $umkm->statusVerifikasi?->kode ?? 'pending';
                $vStyle = match($vKode) {
                    'verified' => ['bg-lake-900', 'text-paper/40', 'bg-green-400', 'Terverifikasi',   'text-paper/60'],
                    'rejected' => ['bg-ulos-maroon', 'text-paper/40', 'bg-red-300', 'Ditolak',        'text-paper/60'],
                    default    => ['bg-lake-900', 'text-paper/40', 'bg-ulos-gold', 'Menunggu Verifikasi', 'text-paper/50'],
                };
            @endphp
            <div class="{{ $vStyle[0] }} rounded-xl p-5 text-paper">
                <p class="font-mono text-[10px] uppercase tracking-widest {{ $vStyle[1] }} mb-2">Status Toko</p>
                <div class="flex items-center gap-2 mb-1">
                    <span class="w-2 h-2 rounded-full {{ $vStyle[2] }}"></span>
                    <p class="text-sm font-semibold">{{ $vStyle[3] }}</p>
                </div>
                <p class="text-xs {{ $vStyle[4] }} leading-relaxed">
                    @if($vKode === 'verified')
                        Tokomu aktif dan bisa menerima pesanan.
                    @elseif($vKode === 'rejected')
                        {{ $umkm->catatan_penolakan ?? 'Hubungi admin untuk info lebih lanjut.' }}
                    @else
                        Tokomu sedang ditinjau. Kamu akan dihubungi segera.
                    @endif
                </p>
            </div>
            @endif
        </div>
    </div>

@endsection
