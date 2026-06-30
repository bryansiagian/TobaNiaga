@extends('layouts.backoffice')
@section('title', 'Pencairan Dana Kurir — TobaNiaga')
@section('page_title', 'Pencairan Dana Kurir')

@section('content')

{{-- Flash --}}
@if(session('status'))
<div class="mb-5 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
    {{ session('status') }}
</div>
@endif
@if(session('error'))
<div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
    {{ session('error') }}
</div>
@endif

{{-- Tabs filter status --}}
<div class="flex items-center gap-1 mb-6 border-b border-lake-900/10">
    @php
        $tabs = [
            'diajukan' => 'Menunggu',
            'diproses' => 'Diproses',
            'selesai'  => 'Selesai',
            'ditolak'  => 'Ditolak',
            'semua'    => 'Semua',
        ];
    @endphp
    @foreach($tabs as $key => $label)
    <a href="{{ route('admin.pencairan-kurir.index', ['status' => $key]) }}"
       class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
              {{ $status === $key ? 'border-lake-900 text-lake-900' : 'border-transparent text-ink/50 hover:text-ink' }}">
        {{ $label }}
        @if($key === 'diajukan' && $countDiajukan > 0)
            <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-ulos-maroon text-paper">{{ $countDiajukan }}</span>
        @endif
        @if($key === 'diproses' && $countDiproses > 0)
            <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-ulos-gold text-paper">{{ $countDiproses }}</span>
        @endif
    </a>
    @endforeach
</div>

@if($pencairan->isEmpty())
<div class="bg-paper border border-lake-900/10 rounded-xl p-16 text-center">
    <p class="text-sm text-ink/40">Tidak ada pengajuan pencairan di kategori ini.</p>
</div>
@else
<div class="space-y-3">
    @foreach($pencairan as $pc)
    @php
        $badge = match($pc->status) {
            'diajukan' => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Menunggu Diproses'],
            'diproses' => ['bg-blue-50 text-blue-700 border-blue-200', 'Sedang Diproses'],
            'selesai'  => ['bg-green-50 text-green-700 border-green-200', 'Selesai'],
            'ditolak'  => ['bg-red-50 text-red-700 border-red-200', 'Ditolak'],
            default    => ['bg-gray-50 text-gray-600 border-gray-200', $pc->status],
        };
    @endphp
    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
        <div class="px-5 py-4 flex items-center justify-between gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-mono font-semibold text-ink">{{ $pc->no_pencairan }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full border {{ $badge[0] }}">{{ $badge[1] }}</span>
                </div>
                <p class="text-sm text-ink/70 mt-1">{{ $pc->courier->nama ?? '-' }}</p>
                <p class="text-xs text-ink/40 mt-0.5">
                    {{ $pc->created_at->format('d M Y, H:i') }} · {{ $pc->detail->count() }} pengiriman
                    · {{ $pc->rekeningBankKurir->nama_bank ?? '-' }} a.n {{ $pc->rekeningBankKurir->nama_pemilik ?? '-' }}
                </p>
                @if($pc->status === 'diproses' && $pc->diprosesOleh)
                <p class="text-xs text-ulos-gold mt-1">Sedang ditangani oleh {{ $pc->diprosesOleh->nama }}</p>
                @endif
                @if($pc->status === 'ditolak' && $pc->catatan_admin)
                <p class="text-xs text-red-600 mt-1">Alasan ditolak: {{ $pc->catatan_admin }}</p>
                @endif
            </div>
            <div class="flex flex-col items-end gap-2 flex-shrink-0">
                <p class="text-base font-bold text-lake-900">Rp{{ number_format($pc->jumlah, 0, ',', '.') }}</p>
                <a href="{{ route('admin.pencairan-kurir.show', $pc) }}"
                   class="text-xs text-lake-800 font-medium hover:underline">Lihat Detail</a>
            </div>
        </div>
    </div>
    @endforeach
</div>
<div class="mt-5">{{ $pencairan->links() }}</div>
@endif

@endsection
