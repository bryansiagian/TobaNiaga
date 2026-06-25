@extends('layouts.guest')

@section('title', 'Detail Pesanan — TobaNiaga')

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">

    {{-- Back --}}
    <a href="{{ route('customer.pesanan.riwayat') }}"
       class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-ink mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Riwayat Pesanan
    </a>

    @php
        $statusKode    = $pesanan->status->kode ?? '';
        $bayarKode     = $pesanan->pembayaran?->status?->kode ?? '';
        $sudahBayar    = $bayarKode === 'settlement';
        $menungguBayar = $statusKode === 'menunggu_pembayaran';

        $badgePesanan = match($statusKode) {
            'menunggu_pembayaran' => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Menunggu Pembayaran'],
            'diproses'            => ['bg-blue-50 text-blue-700 border-blue-200',       'Diproses Penjual'],
            'dikirim'             => ['bg-purple-50 text-purple-700 border-purple-200', 'Sedang Dikirim'],
            'selesai'             => ['bg-green-50 text-green-700 border-green-200',    'Selesai'],
            'batal'               => ['bg-red-50 text-red-700 border-red-200',          'Dibatalkan'],
            default               => ['bg-gray-50 text-gray-600 border-gray-200',       $pesanan->status->label ?? '-'],
        };

        // Stepper
        $steps = [
            ['kode' => 'menunggu_pembayaran', 'label' => 'Menunggu\nPembayaran'],
            ['kode' => 'diproses',            'label' => 'Diproses'],
            ['kode' => 'dikirim',             'label' => 'Dikirim'],
            ['kode' => 'selesai',             'label' => 'Selesai'],
        ];
        $urutanMap    = ['menunggu_pembayaran' => 1, 'diproses' => 2, 'dikirim' => 3, 'selesai' => 4, 'batal' => 0];
        $urutanAktif  = $urutanMap[$statusKode] ?? 0;
    @endphp

    <div class="bg-white rounded-xl border border-lake-100 overflow-hidden">

        {{-- Header --}}
        <div class="bg-lake-900 text-white px-6 py-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-lake-300 mb-0.5">No. Pesanan</p>
                    <p class="font-mono font-semibold text-base">{{ $pesanan->no_pesanan }}</p>
                    <p class="text-xs text-lake-300 mt-1">{{ $pesanan->created_at->translatedFormat('d M Y, H:i') }}</p>
                </div>
                <span class="inline-block text-xs font-medium px-2.5 py-1 rounded-full border {{ $badgePesanan[0] }}">
                    {{ $badgePesanan[1] }}
                </span>
            </div>
        </div>

        {{-- Stepper (sembunyi kalau batal) --}}
        @if($statusKode !== 'batal')
        <div class="px-6 py-5 border-b border-lake-100">
            <div class="flex items-center">
                @foreach($steps as $i => $step)
                @php
                    $urutanStep = $i + 1;
                    $done       = $urutanAktif > $urutanStep;
                    $aktif      = $urutanAktif === $urutanStep;
                @endphp
                <div class="flex flex-col items-center flex-1 relative">
                    {{-- Garis kiri --}}
                    @if($i > 0)
                    <div class="absolute top-3.5 right-1/2 w-full h-0.5 {{ $done || $aktif ? 'bg-lake-900' : 'bg-gray-200' }}"></div>
                    @endif
                    {{-- Dot --}}
                    <div class="relative z-10 w-7 h-7 rounded-full border-2 flex items-center justify-center
                        {{ $done  ? 'bg-lake-900 border-lake-900' :
                           ($aktif ? 'bg-white border-lake-900' : 'bg-white border-gray-200') }}">
                        @if($done)
                            <svg class="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        @elseif($aktif)
                            <div class="w-2.5 h-2.5 rounded-full bg-lake-900"></div>
                        @endif
                    </div>
                    {{-- Label --}}
                    <p class="text-center text-xs mt-1.5 leading-tight
                        {{ $aktif ? 'text-lake-900 font-semibold' : ($done ? 'text-gray-500' : 'text-gray-300') }}">
                        {{ str_replace('\n', "\n", $step['label']) }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Status Pembayaran Banner --}}
        @if($sudahBayar)
        <div class="mx-6 mt-5 flex items-center gap-3 bg-green-50 border border-green-200 rounded-lg px-4 py-3">
            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-green-700">Pembayaran Berhasil</p>
                @if($pesanan->pembayaran?->paid_at)
                <p class="text-xs text-green-600">{{ $pesanan->pembayaran->paid_at->translatedFormat('d M Y, H:i') }}</p>
                @endif
            </div>
        </div>
        @elseif($menungguBayar)
        <div class="mx-6 mt-5 flex items-center justify-between gap-3 bg-yellow-50 border border-yellow-200 rounded-lg px-4 py-3">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                <p class="text-sm font-semibold text-yellow-700">Menunggu Pembayaran</p>
            </div>
            <a href="{{ route('customer.payment.show', $pesanan) }}"
               class="px-3 py-1.5 rounded-lg bg-ulos-maroon text-white text-xs font-semibold hover:opacity-90 flex-shrink-0">
                Bayar Sekarang
            </a>
        </div>
        @endif

        {{-- Produk --}}
        <div class="px-6 pt-5 pb-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Produk</p>
            <div class="space-y-3">
                @foreach($pesanan->detail as $d)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg bg-lake-50 border border-lake-100 flex items-center justify-center flex-shrink-0">
                            <span class="text-xs font-bold text-lake-600">{{ $d->jumlah }}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm text-gray-800 truncate">{{ $d->nama_produk_snapshot }}</p>
                            <p class="text-xs text-gray-400">Rp{{ number_format($d->harga_satuan_snapshot, 0, ',', '.') }} / pcs</p>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-gray-800 ml-4 flex-shrink-0">
                        Rp{{ number_format($d->subtotal, 0, ',', '.') }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Ringkasan biaya --}}
        <div class="mx-6 mb-5 border-t border-dashed border-lake-100 pt-4 space-y-2">
            <div class="flex justify-between text-sm text-gray-500">
                <span>Subtotal Produk</span>
                <span>Rp{{ number_format($pesanan->total_harga - $pesanan->ongkos_kirim + $pesanan->diskon, 0, ',', '.') }}</span>
            </div>
            @if($pesanan->ongkos_kirim > 0)
            <div class="flex justify-between text-sm text-gray-500">
                <span>Ongkos Kirim</span>
                <span>Rp{{ number_format($pesanan->ongkos_kirim, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($pesanan->diskon > 0)
            <div class="flex justify-between text-sm text-green-600">
                <span>Diskon Promo</span>
                <span>- Rp{{ number_format($pesanan->diskon, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between font-bold text-ink pt-1 border-t border-lake-100">
                <span>Total</span>
                <span class="text-lake-900">Rp{{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Info pengiriman --}}
        @if($pesanan->alamat || $pesanan->metodePengiriman)
        <div class="px-6 pb-5 border-t border-lake-100 pt-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Pengiriman</p>
            <div class="space-y-1.5 text-sm text-gray-600">
                @if($pesanan->metodePengiriman)
                <div class="flex gap-2">
                    <span class="text-gray-400 w-24 flex-shrink-0">Metode</span>
                    <span>{{ $pesanan->metodePengiriman->label }}</span>
                </div>
                @endif
                @if($pesanan->alamat)
                <div class="flex gap-2">
                    <span class="text-gray-400 w-24 flex-shrink-0">Alamat</span>
                    <span>{{ $pesanan->alamat->alamat_lengkap }}</span>
                </div>
                @endif
                @if($pesanan->pengiriman?->no_resi)
                <div class="flex gap-2">
                    <span class="text-gray-400 w-24 flex-shrink-0">No. Resi</span>
                    <span class="font-mono font-semibold">{{ $pesanan->pengiriman->no_resi }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Catatan customer --}}
        @if($pesanan->catatan_customer)
        <div class="px-6 pb-5 border-t border-lake-100 pt-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Catatan</p>
            <p class="text-sm text-gray-600 italic">"{{ $pesanan->catatan_customer }}"</p>
        </div>
        @endif

        {{-- UMKM --}}
        <div class="px-6 pb-5 border-t border-lake-100 pt-4">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2">Penjual</p>
            <p class="text-sm font-medium text-ink">{{ $pesanan->umkm->nama_umkm ?? '-' }}</p>
        </div>

    </div>
</div>
@endsection
