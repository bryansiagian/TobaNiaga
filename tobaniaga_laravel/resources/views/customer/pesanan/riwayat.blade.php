@extends('layouts.guest')

@section('title', 'Riwayat Pesanan — TobaNiaga')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4">

    <div class="flex items-center justify-between mb-6">
        <h1 class="font-display text-2xl font-semibold text-ink">Pesanan Saya</h1>
        <a href="{{ route('produk.index') }}"
           class="text-sm text-lake-600 hover:text-lake-900 font-medium">
            + Belanja Lagi
        </a>
    </div>

    @if($pesanan->isEmpty())
        <div class="bg-white rounded-xl border border-lake-100 p-16 text-center">
            <div class="w-16 h-16 rounded-full bg-lake-50 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-lake-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                </svg>
            </div>
            <p class="text-gray-600 font-medium mb-1">Belum ada pesanan</p>
            <p class="text-gray-400 text-sm mb-6">Yuk, temukan produk UMKM Danau Toba favoritmu!</p>
            <a href="{{ route('welcome') }}"
               class="inline-block px-6 py-2.5 rounded-lg bg-lake-900 text-white text-sm font-medium hover:bg-lake-800">
                Mulai Belanja
            </a>
        </div>

    @else
        <div class="space-y-4">
            @foreach($pesanan as $item)
            @php
                $statusKode     = $item->status->kode ?? '';
                $bayarKode      = $item->pembayaran?->status?->kode ?? '';
                $sudahBayar     = $bayarKode === 'settlement';
                $menungguBayar  = $statusKode === 'menunggu_pembayaran';
                $adaPembayaran  = $item->pembayaran !== null;

                $badgePesanan = match($statusKode) {
                    'menunggu_pembayaran' => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Menunggu Pembayaran'],
                    'diproses'            => ['bg-blue-50 text-blue-700 border-blue-200',       'Diproses Penjual'],
                    'dikirim'             => ['bg-purple-50 text-purple-700 border-purple-200', 'Sedang Dikirim'],
                    'selesai'             => ['bg-green-50 text-green-700 border-green-200',    'Selesai'],
                    'batal'               => ['bg-red-50 text-red-700 border-red-200',          'Dibatalkan'],
                    default               => ['bg-gray-50 text-gray-600 border-gray-200',       $item->status->label ?? '-'],
                };
            @endphp

            <div class="bg-white rounded-xl border border-lake-100 overflow-hidden">

                {{-- Header --}}
                <div class="flex items-start justify-between px-5 py-4 border-b border-lake-100">
                    <div>
                        <p class="text-xs text-gray-400 mb-0.5">{{ $item->created_at->translatedFormat('d M Y, H:i') }}</p>
                        <p class="text-sm font-semibold text-ink font-mono">{{ $item->no_pesanan }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $item->umkm->nama_umkm ?? '-' }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1.5">
                        <span class="inline-block text-xs font-medium px-2.5 py-1 rounded-full border {{ $badgePesanan[0] }}">
                            {{ $badgePesanan[1] }}
                        </span>
                        @if($item->pengiriman && $item->pengiriman->status)
                        @php
                            $pgKode = $item->pengiriman->status->kode;
                            $pgBadge = match($pgKode) {
                                'menunggu_kurir' => 'bg-yellow-50 text-yellow-600 border-yellow-200',
                                'dijemput'       => 'bg-blue-50 text-blue-600 border-blue-200',
                                'diantar'        => 'bg-purple-50 text-purple-600 border-purple-200',
                                'selesai'        => 'bg-green-50 text-green-600 border-green-200',
                                default          => 'bg-gray-50 text-gray-500 border-gray-200',
                            };
                        @endphp
                        <span class="inline-block text-[10px] font-medium px-2.5 py-0.5 rounded-full border {{ $pgBadge }}">
                            🚚 {{ $item->pengiriman->status->label }}
                        </span>
                        @endif
                        {{-- Status pembayaran --}}
                        @if($sudahBayar)
                            <span class="inline-flex items-center gap-1 text-xs text-green-600 font-medium">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Sudah Dibayar
                            </span>
                        @elseif($menungguBayar)
                            <span class="inline-flex items-center gap-1 text-xs text-yellow-600 font-medium">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                Belum Dibayar
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Produk --}}
                <div class="px-5 py-3 space-y-2">
                    @foreach($item->detail as $d)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="w-5 h-5 rounded bg-lake-50 text-lake-600 text-xs flex items-center justify-center font-medium flex-shrink-0">
                                {{ $d->jumlah }}
                            </span>
                            <span class="text-gray-700 truncate">{{ $d->nama_produk_snapshot }}</span>
                        </div>
                        <span class="text-gray-700 font-medium ml-4 flex-shrink-0">
                            Rp{{ number_format($d->subtotal, 0, ',', '.') }}
                        </span>
                    </div>
                    @endforeach
                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-between px-5 py-3.5 bg-lake-50 border-t border-lake-100">
                    <div>
                        <p class="text-xs text-gray-400">Total Pembayaran</p>
                        <p class="text-base font-bold text-lake-900">
                            Rp{{ number_format($item->total_harga, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        @if($menungguBayar && !$sudahBayar)
                            <a href="{{ route('customer.payment.show', $item) }}"
                            class="px-4 py-2 rounded-lg bg-ulos-maroon text-white text-xs font-semibold hover:opacity-90">
                                Bayar Sekarang
                            </a>
                        @endif
                        @if(in_array($statusKode, ['dikirim', 'selesai']) && $item->pengiriman)
                            <a href="{{ route('customer.pesanan.lacak', $item) }}"
                            class="px-4 py-2 rounded-lg border border-lake-200 text-lake-900 text-xs font-medium hover:bg-white">
                                Lacak
                            </a>
                        @endif
                        <a href="{{ route('customer.pesanan.show', $item) }}"
                        class="px-4 py-2 rounded-lg border border-lake-200 text-lake-900 text-xs font-medium hover:bg-white">
                            Lihat Detail
                        </a>
                        @if($statusKode === 'selesai')
                            @php
                                $sudahUlasSemuaProduk = $item->ulasan->count() >= $item->detail->count();
                            @endphp
                            @if(!$sudahUlasSemuaProduk)
                                <a href="{{ route('customer.pesanan.ulasan.create', $item) }}"
                                class="px-4 py-2 rounded-lg bg-ulos-gold text-white text-xs font-semibold hover:opacity-90">
                                    Beri Ulasan
                                </a>
                            @endif
                        @endif
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $pesanan->links() }}
        </div>
    @endif
</div>
@endsection
