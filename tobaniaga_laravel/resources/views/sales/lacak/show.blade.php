@extends('layouts.backoffice')

@section('title', 'Lacak Pengiriman')
@section('role_label', 'Sales')
@section('page_title', 'Lacak Pengiriman')

@section('content')

@php
    $pg     = $pesanan->pengiriman;
    $kode   = $pg->status->kode ?? '';

    $labelMap = [
        'menunggu_kurir' => 'Paket menunggu dijemput kurir',
        'dijemput'       => 'Paket telah dijemput kurir',
        'diantar'        => 'Paket sedang dalam perjalanan',
        'selesai'        => 'Paket telah diterima',
    ];

    $ikonMap = [
        'menunggu_kurir' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',         // clock
        'dijemput'       => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8l1 12h12L19 8', // box
        'diantar'        => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0', // truck
        'selesai'        => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',        // check circle
    ];

    $urutan = ['menunggu_kurir', 'dijemput', 'diantar', 'selesai'];
    $indexAktif = array_search($kode, $urutan);
@endphp

<div class="mb-6">
    <a href="{{ route('sales.pesanan.index') }}"
       class="text-sm text-ink/50 hover:text-lake-900">← Kembali ke Pesanan</a>
</div>

<div class="max-w-2xl mx-auto space-y-5">

    {{-- Header pesanan --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
        <p class="font-mono text-xs text-ink/40 mb-1">{{ $pesanan->no_pesanan }}</p>
        <h2 class="font-display text-lg font-medium text-lake-900 mb-3">
            {{ $labelMap[$kode] ?? $pg->status->label ?? '—' }}
        </h2>
        <div class="grid grid-cols-2 gap-3 text-sm">
            <div>
                <p class="text-xs text-ink/40 mb-0.5">Pembeli</p>
                <p class="text-ink/80 font-medium">{{ $pesanan->customer->nama ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-ink/40 mb-0.5">Kurir</p>
                <p class="text-ink/80 font-medium">{{ $pg->kurir->nama ?? 'Belum ditugaskan' }}</p>
            </div>
            <div class="col-span-2">
                <p class="text-xs text-ink/40 mb-0.5">Alamat Tujuan</p>
                <p class="text-ink/70">
                    {{ $pesanan->alamat->alamat_lengkap ?? '—' }},
                    {{ $pesanan->alamat->kelurahan ?? '' }},
                    {{ $pesanan->alamat->kecamatan ?? '' }},
                    {{ $pesanan->alamat->kota ?? '' }}
                </p>
            </div>
        </div>
    </div>

    {{-- Timeline status --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-lake-900 mb-5">Status Pengiriman</h3>

        <div class="relative">
            {{-- Garis vertikal --}}
            <div class="absolute left-4 top-4 bottom-4 w-px bg-lake-900/10"></div>

            <div class="space-y-0">
                @foreach($urutan as $i => $u)
                @php
                    $sudah   = $indexAktif !== false && $i <= $indexAktif;
                    $aktif   = $i === $indexAktif;
                    $label   = $labelMap[$u] ?? $u;

                    // Cari log entry yang cocok
                    $logEntry = $pg->log->first(fn($l) => $l->status->kode === $u);

                    // Timestamp dari field khusus atau dari log
                    $waktu = match($u) {
                        'dijemput' => $pg->waktu_pickup,
                        'selesai'  => $pg->waktu_selesai,
                        default    => $logEntry?->created_at,
                    };
                @endphp
                <div class="relative flex items-start gap-4 pb-6 last:pb-0">
                    {{-- Dot --}}
                    <div class="relative z-10 flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center
                        {{ $aktif ? 'bg-lake-900 shadow-sm' : ($sudah ? 'bg-lake-700' : 'bg-lake-900/10') }}">
                        <svg class="w-4 h-4 {{ $sudah ? 'text-paper' : 'text-ink/20' }}"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $ikonMap[$u] ?? '' }}"/>
                        </svg>
                    </div>

                    {{-- Konten --}}
                    <div class="pt-1 flex-1">
                        <p class="text-sm font-medium {{ $aktif ? 'text-lake-900' : ($sudah ? 'text-ink/70' : 'text-ink/25') }}">
                            {{ $label }}
                        </p>
                        @if($waktu)
                            <p class="text-xs text-ink/40 mt-0.5">
                                {{ \Carbon\Carbon::parse($waktu)->translatedFormat('d M Y, H:i') }}
                            </p>
                        @elseif(!$sudah)
                            <p class="text-xs text-ink/25 mt-0.5">Menunggu</p>
                        @endif

                        {{-- Bukti serah terima (hanya di step selesai) --}}
                        @if($u === 'selesai' && $sudah && $pg->nama_penerima)
                        <div class="mt-2 p-3 bg-lake-50 rounded-lg border border-lake-900/10 space-y-1">
                            <p class="text-xs text-ink/60">
                                Diterima oleh: <span class="font-medium text-ink/80">{{ $pg->nama_penerima }}</span>
                                @if($pg->relasi_penerima)
                                    <span class="text-ink/40">({{ $pg->relasi_penerima }})</span>
                                @endif
                            </p>
                            @if($pg->foto_bukti)
                            <a href="{{ Storage::url($pg->foto_bukti) }}" target="_blank">
                                <img src="{{ Storage::url($pg->foto_bukti) }}"
                                     class="mt-2 w-32 h-24 object-cover rounded-lg border border-lake-900/10 hover:opacity-90 transition-opacity">
                            </a>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Detail produk --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
        <h3 class="text-sm font-semibold text-lake-900 mb-3">Isi Pesanan</h3>
        <div class="space-y-2">
            @foreach($pesanan->detail as $d)
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded bg-lake-50 text-lake-700 text-xs flex items-center justify-center font-medium">
                        {{ $d->jumlah }}
                    </span>
                    <span class="text-ink/70">{{ $d->nama_produk_snapshot }}</span>
                </div>
                <span class="text-ink/60 font-medium">Rp{{ number_format($d->subtotal, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
        <div class="mt-3 pt-3 border-t border-lake-900/8 flex justify-between text-sm font-semibold text-lake-900">
            <span>Total</span>
            <span>Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
        </div>
    </div>

</div>

@endsection
