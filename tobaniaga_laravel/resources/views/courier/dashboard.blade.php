@extends('layouts.backoffice')

@section('title', 'Dashboard Kurir')
@section('role_label', 'Kurir')
@section('page_title', 'Dashboard Kurir')

@section('content')

    {{-- Greeting --}}
    <div class="mb-8">
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Kurir Aktif</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">Halo, {{ auth()->user()->nama ?? 'Kurir' }}</h2>
    </div>

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
            <p class="text-xs text-yellow-600 mt-0.5">Lengkapi NIK, foto KTP, dan KK untuk mulai menerima tugas pengiriman.</p>
        </div>
        <a href="{{ route('courier.dokumen') }}"
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
        <a href="{{ route('courier.dokumen') }}"
        class="flex-shrink-0 px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700 transition-colors">
            Upload Ulang
        </a>
    </div>
    @endif

    {{-- Stat cards --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Tugas Aktif</p>
            <p class="font-display text-3xl font-medium text-lake-900">{{ $tugasAktif->count() }}</p>
            <p class="text-xs text-ink/50 mt-1">Sedang ditangani</p>
        </div>
        <div class="stat-card border-l-2 border-l-ulos-gold">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Tersedia</p>
            <p class="font-display text-3xl font-medium text-ulos-gold" style="color:#c49044">{{ $pool->count() }}</p>
            <p class="text-xs text-ink/50 mt-1">Menunggu diambil</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Selesai</p>
            <p class="font-display text-3xl font-medium text-lake-900">{{ $riwayat->count() }}</p>
            <p class="text-xs text-ink/50 mt-1">Berhasil dikirim</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_280px] gap-6">

        {{-- Tugas pengiriman --}}
        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
                <h3 class="font-display text-base font-medium text-lake-900">Tugas Aktif Saya</h3>
                <span class="font-mono text-[10px] bg-lake-50 text-lake-800 border border-lake-900/10 px-2 py-0.5 rounded-full uppercase tracking-wider">{{ $tugasAktif->count() }} aktif</span>
            </div>

            @if($tugasAktif->isEmpty())
                <div class="px-6 py-14 text-center">
                    <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                        </svg>
                    </div>
                    <p class="text-sm text-ink/40">Belum ada tugas aktif.</p>
                </div>
            @else
                <div class="divide-y divide-lake-900/5">
                    @foreach($tugasAktif as $t)
                    @php
                        $kode = $t->status->kode ?? '';
                        $nextMap = [
                            'dijemput' => ['kode' => 'diantar', 'label' => 'Tandai Sedang Diantar'],
                            'diantar'  => ['kode' => 'selesai', 'label' => 'Konfirmasi Terkirim'],
                        ];
                        $next = $nextMap[$kode] ?? null;
                    @endphp
                    <div class="px-6 py-4">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <p class="font-mono text-xs text-ink/40">{{ $t->pesanan->no_pesanan }}</p>
                                <p class="font-medium text-lake-900">{{ $t->pesanan->umkm->nama_umkm ?? '—' }}</p>
                                <p class="text-sm text-ink/60">Pembeli: {{ $t->pesanan->customer->nama ?? '—' }}</p>
                            </div>
                            <span class="text-xs font-medium px-2.5 py-1 rounded-full flex-shrink-0
                                {{ $kode === 'dijemput' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
                                {{ $t->status->label ?? '—' }}
                            </span>
                        </div>
                        <p class="text-sm text-ink/60 mb-3">
                            {{ $t->pesanan->alamat->alamat_lengkap ?? '—' }},
                            {{ $t->pesanan->alamat->kota ?? '' }}
                        </p>
                        @if($next)
                        <form action="{{ route('courier.pengiriman.status', $t) }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="status_kode" value="{{ $next['kode'] }}">
                            <input type="text" name="catatan" placeholder="Catatan (opsional)"
                                class="flex-1 border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                            <button type="submit"
                                    class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90 whitespace-nowrap">
                                {{ $next['label'] }}
                            </button>
                        </form>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Status & info kurir --}}
        <div class="space-y-4">
            {{-- Status kurir --}}
            <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-lake-900/8">
                    <h3 class="font-display text-base font-medium text-lake-900">Status Saya</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-ink/60">Ketersediaan</span>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-lake-800"></span>
                            <span class="text-sm font-medium text-lake-900">Aktif</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-ink/60">Tugas aktif</span>
                        <span class="font-mono text-xs text-ink/70 font-semibold">{{ $tugasAktif->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-ink/60">Total selesai</span>
                        <span class="font-mono text-xs text-ink/70 font-semibold">{{ $riwayat->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Panduan singkat --}}
            <div class="bg-lake-900 rounded-xl p-5 text-paper">
                <p class="font-mono text-[10px] uppercase tracking-widest text-paper/40 mb-3">Cara Kerja</p>
                <div class="space-y-2.5">
                    <div class="flex gap-2.5">
                        <span class="font-mono text-xs text-ulos-gold mt-0.5 flex-shrink-0">01</span>
                        <p class="text-xs text-paper/70 leading-relaxed">Ambil tugas dari pool yang tersedia</p>
                    </div>
                    <div class="flex gap-2.5">
                        <span class="font-mono text-xs text-ulos-gold mt-0.5 flex-shrink-0">02</span>
                        <p class="text-xs text-paper/70 leading-relaxed">Jemput paket dari alamat UMKM</p>
                    </div>
                    <div class="flex gap-2.5">
                        <span class="font-mono text-xs text-ulos-gold mt-0.5 flex-shrink-0">03</span>
                        <p class="text-xs text-paper/70 leading-relaxed">Antar ke pembeli dan konfirmasi selesai</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Job Pool --}}
    <div class="mt-6">
        <div class="flex items-center justify-between mb-3">
            <h2 class="font-display text-lg font-medium text-lake-900">Tugas Tersedia</h2>
            <span class="font-mono text-xs text-ink/40">{{ $pool->count() }} pesanan</span>
        </div>
        @if($pool->isEmpty())
            <div class="bg-paper border border-lake-900/10 rounded-xl px-6 py-10 text-center">
                <p class="text-sm text-ink/30">Tidak ada tugas tersedia saat ini.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($pool as $t)
                <div class="bg-paper border border-lake-900/10 rounded-xl p-5 flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-0">
                        <p class="font-mono text-xs text-ink/40">{{ $t->pesanan->no_pesanan }}</p>
                        <p class="font-medium text-lake-900">{{ $t->pesanan->umkm->nama_umkm ?? '—' }}</p>
                        <p class="text-sm text-ink/60 truncate">
                            {{ $t->pesanan->alamat->alamat_lengkap ?? '—' }}, {{ $t->pesanan->alamat->kota ?? '' }}
                        </p>
                        <p class="text-xs text-ink/40 mt-1">{{ $t->pesanan->detail->count() }} item · Rp{{ number_format($t->pesanan->total_harga, 0, ',', '.') }}</p>
                    </div>
                    <form action="{{ route('courier.pengiriman.claim', $t) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90">
                            Ambil Tugas
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Riwayat --}}
    @if($riwayat->isNotEmpty())
    <div class="mt-6">
        <h2 class="font-display text-lg font-medium text-lake-900 mb-3">Riwayat Terakhir</h2>
        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-lake-900/8 text-left">
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-3">No. Pesanan</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-3">UMKM</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-3">Pembeli</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-5 py-3">Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lake-900/5">
                    @foreach($riwayat as $r)
                    <tr>
                        <td class="px-5 py-3 font-mono text-xs text-ink/70">{{ $r->pesanan->no_pesanan }}</td>
                        <td class="px-5 py-3 text-ink/70">{{ $r->pesanan->umkm->nama_umkm ?? '—' }}</td>
                        <td class="px-5 py-3 text-ink/70">{{ $r->pesanan->customer->nama ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-ink/40">{{ $r->waktu_selesai?->translatedFormat('d M Y, H:i') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

@endsection
