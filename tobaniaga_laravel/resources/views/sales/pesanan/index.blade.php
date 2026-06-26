@extends('layouts.backoffice')

@section('title', 'Pesanan Masuk')
@section('role_label', 'Sales')
@section('page_title', 'Pesanan Masuk')

@section('content')

<div class="mb-8 flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">{{ $umkm->nama_umkm }}</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">Pesanan Masuk</h2>
    </div>
</div>

@if(session('status'))
<div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
    {{ session('status') }}
</div>
@endif

{{-- Tab status --}}
<div class="flex gap-2 flex-wrap mb-5">
    <a href="{{ route('sales.pesanan.index', array_merge(request()->except('status', 'page'), [])) }}"
       class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition-colors
              {{ !request('status') ? 'bg-lake-900 text-paper border-lake-900' : 'bg-paper text-ink/60 border-lake-900/15 hover:border-lake-900/30' }}">
        Semua
        <span class="ml-1 font-mono">{{ $hitungStatus->sum() }}</span>
    </a>
    @foreach($statusList as $s)
    @php
        $aktif = request('status') === $s->kode;
        $warna = match($s->kode) {
            'menunggu_pembayaran' => 'text-yellow-700 border-yellow-200 bg-yellow-50',
            'diproses'            => 'text-blue-700 border-blue-200 bg-blue-50',
            'dikirim'             => 'text-purple-700 border-purple-200 bg-purple-50',
            'selesai'             => 'text-green-700 border-green-200 bg-green-50',
            'batal'               => 'text-red-700 border-red-200 bg-red-50',
            default               => 'text-gray-600 border-gray-200 bg-gray-50',
        };
    @endphp
    <a href="{{ route('sales.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => $s->kode])) }}"
       class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition-colors
              {{ $aktif ? 'bg-lake-900 text-paper border-lake-900' : $warna . ' hover:opacity-80' }}">
        {{ $s->label }}
        <span class="ml-1 font-mono">{{ $hitungStatus[$s->kode] ?? 0 }}</span>
    </a>
    @endforeach
</div>

{{-- Filter pencarian & tanggal --}}
<form method="GET" action="{{ route('sales.pesanan.index') }}"
      class="flex flex-wrap gap-2 mb-5">
    @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif
    <input type="text" name="cari" value="{{ request('cari') }}"
           placeholder="Cari no. pesanan / nama pembeli..."
           class="flex-1 min-w-48 border border-lake-900/15 rounded-lg px-3.5 py-2 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
    <input type="date" name="dari" value="{{ request('dari') }}"
           class="border border-lake-900/15 rounded-lg px-3.5 py-2 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
    <input type="date" name="sampai" value="{{ request('sampai') }}"
           class="border border-lake-900/15 rounded-lg px-3.5 py-2 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
    <button type="submit"
            class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90">
        Cari
    </button>
    @if(request()->hasAny(['cari', 'dari', 'sampai']))
    <a href="{{ route('sales.pesanan.index', request()->only('status')) }}"
       class="px-4 py-2 border border-lake-900/15 text-ink/60 text-sm rounded-lg hover:bg-lake-50">
        Reset
    </a>
    @endif
</form>

{{-- Tabel --}}
<div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
        <h3 class="font-display text-base font-medium text-lake-900">Daftar Pesanan</h3>
        <span class="font-mono text-xs text-ink/40">{{ $pesanan->total() }} pesanan</span>
    </div>

    @if($pesanan->isEmpty())
        <div class="px-6 py-16 text-center">
            <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <p class="text-sm text-ink/40">Tidak ada pesanan ditemukan.</p>
        </div>
    @else
        {{-- Desktop table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-lake-900/8 text-left">
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">No. Pesanan</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Pembeli</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Produk</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Total</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Pembayaran</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Status</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Waktu</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lake-900/5">
                    @foreach($pesanan as $p)
                    @php
                        $kode      = $p->status->kode ?? '';
                        $bayarKode = $p->pembayaran?->status?->kode ?? '';
                        $badge = match($kode) {
                            'menunggu_pembayaran' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'diproses'            => 'bg-blue-50 text-blue-700 border-blue-200',
                            'dikirim'             => 'bg-purple-50 text-purple-700 border-purple-200',
                            'selesai'             => 'bg-green-50 text-green-700 border-green-200',
                            'batal'               => 'bg-red-50 text-red-700 border-red-200',
                            default               => 'bg-gray-50 text-gray-600 border-gray-200',
                        };
                    @endphp
                    <tr>
                        <td class="px-6 py-3 font-mono text-xs text-ink/70 whitespace-nowrap">{{ $p->no_pesanan }}</td>
                        <td class="px-6 py-3 text-ink/80">{{ $p->customer->nama ?? '—' }}</td>
                        <td class="px-6 py-3 text-ink/60">
                            <span class="text-xs">{{ $p->detail->count() }} item</span>
                            <p class="text-xs text-ink/40 truncate max-w-32">
                                {{ $p->detail->pluck('nama_produk_snapshot')->implode(', ') }}
                            </p>
                        </td>
                        <td class="px-6 py-3 font-semibold text-lake-900 whitespace-nowrap">
                            Rp{{ number_format($p->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3">
                            @if($bayarKode === 'settlement')
                                <span class="inline-flex items-center gap-1 text-xs text-green-600 font-medium">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Lunas
                                </span>
                            @elseif($bayarKode === 'pending')
                                <span class="text-xs text-yellow-600">Menunggu</span>
                            @elseif(in_array($bayarKode, ['expire', 'cancel', 'failed']))
                                <span class="text-xs text-red-500">{{ $p->pembayaran->status->label ?? '—' }}</span>
                            @else
                                <span class="text-xs text-ink/30">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full border {{ $badge }}">
                                {{ $p->status->label ?? '—' }}
                            </span>
                            @if($p->pengiriman && $p->pengiriman->status)
                            @php
                                $pgKode = $p->pengiriman->status->kode;
                                $pgBadge = match($pgKode) {
                                    'menunggu_kurir' => 'bg-yellow-50 text-yellow-600 border-yellow-200',
                                    'dijemput'       => 'bg-blue-50 text-blue-600 border-blue-200',
                                    'diantar'        => 'bg-purple-50 text-purple-600 border-purple-200',
                                    'selesai'        => 'bg-green-50 text-green-600 border-green-200',
                                    default          => 'bg-gray-50 text-gray-500 border-gray-200',
                                };
                            @endphp
                            <span class="mt-1 inline-block text-[10px] font-medium px-2 py-0.5 rounded-full border {{ $pgBadge }}">
                                {{ $p->pengiriman->status->label }}
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-xs text-ink/40 whitespace-nowrap">
                            {{ $p->created_at->translatedFormat('d M Y') }}<br>
                            <span class="text-ink/30">{{ $p->created_at->translatedFormat('H:i') }}</span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">

                                {{-- Approve --}}
                                @if($kode === 'diproses' && $bayarKode === 'settlement')
                                <form action="{{ route('sales.pesanan.approve', $p) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                            class="font-mono text-xs text-green-700 hover:underline"
                                            onclick="return confirm('Approve pesanan {{ $p->no_pesanan }}?')">
                                        Approve
                                    </button>
                                </form>
                                @endif

                                {{-- Lacak --}}
                                @if(in_array($kode, ['dikirim', 'selesai']) && $p->pengiriman)
                                <a href="{{ route('sales.lacak.show', $p) }}"
                                class="font-mono text-xs text-lake-800 hover:underline">
                                    Lacak
                                </a>
                                @endif

                                {{-- Cancel --}}
                                @if(in_array($kode, ['menunggu_pembayaran', 'diproses']))
                                <form action="{{ route('sales.pesanan.update', $p) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="_method" value="PATCH">
                                    <input type="hidden" name="status_id"
                                        value="{{ $statusList->firstWhere('kode', 'batal')->id }}">
                                    <button type="submit"
                                            class="font-mono text-xs text-red-500 hover:underline"
                                            onclick="return confirm('Batalkan pesanan {{ $p->no_pesanan }}?')">
                                        Batalkan
                                    </button>
                                </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-lake-900/5">
            @foreach($pesanan as $p)
            @php
                $kode      = $p->status->kode ?? '';
                $bayarKode = $p->pembayaran?->status?->kode ?? '';
                $badge = match($kode) {
                    'menunggu_pembayaran' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                    'diproses'            => 'bg-blue-50 text-blue-700 border-blue-200',
                    'dikirim'             => 'bg-purple-50 text-purple-700 border-purple-200',
                    'selesai'             => 'bg-green-50 text-green-700 border-green-200',
                    'batal'               => 'bg-red-50 text-red-700 border-red-200',
                    default               => 'bg-gray-50 text-gray-600 border-gray-200',
                };
            @endphp
            <div class="px-5 py-4">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div>
                        <p class="font-mono text-xs font-semibold text-ink">{{ $p->no_pesanan }}</p>
                        <p class="text-xs text-ink/40 mt-0.5">{{ $p->customer->nama ?? '—' }} · {{ $p->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full border {{ $badge }} flex-shrink-0">
                            {{ $p->status->label ?? '—' }}
                        </span>
                        @if($p->pengiriman && $p->pengiriman->status)
                        @php
                            $pgKode = $p->pengiriman->status->kode;
                            $pgBadge = match($pgKode) {
                                'menunggu_kurir' => 'bg-yellow-50 text-yellow-600 border-yellow-200',
                                'dijemput'       => 'bg-blue-50 text-blue-600 border-blue-200',
                                'diantar'        => 'bg-purple-50 text-purple-600 border-purple-200',
                                'selesai'        => 'bg-green-50 text-green-600 border-green-200',
                                default          => 'bg-gray-50 text-gray-500 border-gray-200',
                            };
                        @endphp
                        <span class="inline-block text-[10px] font-medium px-2 py-0.5 rounded-full border {{ $pgBadge }} flex-shrink-0">
                            {{ $p->pengiriman->status->label }}
                        </span>
                        @endif
                    </div>
                </div>
                <p class="text-xs text-ink/50 mb-2">{{ $p->detail->count() }} item · {{ $p->detail->pluck('nama_produk_snapshot')->implode(', ') }}</p>
                <div class="flex items-center justify-between">
                    <p class="text-sm font-bold text-lake-900">Rp{{ number_format($p->total_harga, 0, ',', '.') }}</p>
                    <div class="flex items-center gap-3">

                        {{-- Approve --}}
                        @if($kode === 'diproses' && $bayarKode === 'settlement')
                        <form action="{{ route('sales.pesanan.approve', $p) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="font-mono text-xs text-green-700 hover:underline"
                                    onclick="return confirm('Approve pesanan {{ $p->no_pesanan }}?')">
                                Approve
                            </button>
                        </form>
                        @endif

                        {{-- Lacak --}}
                        @if(in_array($kode, ['dikirim', 'selesai']) && $p->pengiriman)
                        <a href="{{ route('sales.lacak.show', $p) }}"
                        class="font-mono text-xs text-lake-800 hover:underline">
                            Lacak
                        </a>
                        @endif

                        {{-- Cancel --}}
                        @if(in_array($kode, ['menunggu_pembayaran', 'diproses']))
                        <form action="{{ route('sales.pesanan.update', $p) }}" method="POST">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="status_id"
                                value="{{ $statusList->firstWhere('kode', 'batal')->id }}">
                            <button type="submit"
                                    class="font-mono text-xs text-red-500 hover:underline"
                                    onclick="return confirm('Batalkan pesanan {{ $p->no_pesanan }}?')">
                                Batalkan
                            </button>
                        </form>
                        @endif

                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-6 py-4 border-t border-lake-900/8">
            {{ $pesanan->links() }}
        </div>
    @endif
</div>

@endsection
