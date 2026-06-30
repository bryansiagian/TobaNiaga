@extends('layouts.backoffice')
@section('title', 'Detail Pencairan — TobaNiaga')
@section('page_title', 'Detail Pencairan')

@section('content')
<div class="max-w-2xl">

    <a href="{{ route('admin.pencairan.index') }}"
       class="inline-flex items-center gap-1.5 text-sm text-ink/50 hover:text-ink mb-6">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali
    </a>

    @if(session('error'))
    <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        {{ session('error') }}
    </div>
    @endif

    @php
        $badge = match($pencairanDana->status) {
            'diajukan' => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Menunggu Diproses'],
            'diproses' => ['bg-blue-50 text-blue-700 border-blue-200', 'Sedang Diproses'],
            'selesai'  => ['bg-green-50 text-green-700 border-green-200', 'Selesai'],
            'ditolak'  => ['bg-red-50 text-red-700 border-red-200', 'Ditolak'],
            default    => ['bg-gray-50 text-gray-600 border-gray-200', $pencairanDana->status],
        };
    @endphp

    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden mb-6">
        <div class="bg-lake-900 text-paper px-6 py-5">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-paper/50 mb-0.5">No. Pencairan</p>
                    <p class="font-mono font-semibold text-base">{{ $pencairanDana->no_pencairan }}</p>
                    <p class="text-xs text-paper/50 mt-1">{{ $pencairanDana->created_at->translatedFormat('d M Y, H:i') }}</p>
                </div>
                <span class="text-xs px-2.5 py-1 rounded-full border {{ $badge[0] }}">{{ $badge[1] }}</span>
            </div>
        </div>

        {{-- Info UMKM & Rekening --}}
        <div class="px-6 py-5 border-b border-lake-900/8 grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-ink/40 mb-1">UMKM</p>
                <p class="text-sm font-medium text-ink">{{ $pencairanDana->umkm->nama_umkm }}</p>
            </div>
            <div>
                <p class="text-xs text-ink/40 mb-1">Rekening Tujuan</p>
                <p class="text-sm font-medium text-ink">{{ $pencairanDana->rekeningBank->nama_bank }}</p>
                <p class="text-xs text-ink/60">{{ $pencairanDana->rekeningBank->nama_pemilik }}</p>
                <p class="text-xs font-mono text-ink/60">{{ $pencairanDana->rekeningBank->no_rekening }}</p>
            </div>
        </div>

        {{-- Daftar pesanan --}}
        <div class="px-6 py-5 border-b border-lake-900/8">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">Pesanan Termasuk ({{ $pencairanDana->detail->count() }})</p>
            <div class="space-y-2">
                @foreach($pencairanDana->detail as $d)
                <div class="flex items-center justify-between text-sm">
                    <span class="font-mono text-ink/70">{{ $d->pesanan->no_pesanan ?? '-' }}</span>
                    <span class="font-medium text-ink">Rp{{ number_format($d->jumlah, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Total --}}
        <div class="px-6 py-5 bg-lake-50 flex justify-between items-center">
            <p class="text-sm font-semibold text-lake-900">Total Pencairan</p>
            <p class="text-xl font-bold text-lake-900">Rp{{ number_format($pencairanDana->jumlah, 0, ',', '.') }}</p>
        </div>

        @if($pencairanDana->diprosesOleh)
        <div class="px-6 py-4 border-t border-lake-900/8">
            <p class="text-xs text-ink/40">
                {{ $pencairanDana->status === 'ditolak' ? 'Ditolak' : 'Diproses' }} oleh
                <span class="font-medium text-ink/70">{{ $pencairanDana->diprosesOleh->nama }}</span>
                · {{ $pencairanDana->diproses_at?->format('d M Y, H:i') }}
            </p>
        </div>
        @endif

        @if($pencairanDana->catatan_admin)
        <div class="px-6 py-4 border-t border-lake-900/8">
            <p class="text-xs text-ink/40 mb-1">Catatan</p>
            <p class="text-sm text-ink/70">{{ $pencairanDana->catatan_admin }}</p>
        </div>
        @endif
    </div>

    {{-- Actions sesuai status --}}
    @if($pencairanDana->status === 'diajukan')
    <div class="flex gap-3">
        <form action="{{ route('admin.pencairan.proses', $pencairanDana) }}" method="POST" class="flex-1">
            @csrf
            <button type="submit"
                    class="w-full py-2.5 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-800 transition-colors">
                Mulai Proses
            </button>
        </form>
        <button type="button" onclick="document.getElementById('modalTolak').showModal()"
                class="px-6 py-2.5 border border-ulos-maroon/30 text-ulos-maroon text-sm font-medium rounded-lg hover:bg-ulos-maroon/5 transition-colors">
            Tolak
        </button>
    </div>
    @endif

    @if($pencairanDana->status === 'diproses')
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-4">
        <p class="text-sm font-semibold text-blue-700 mb-1">Lakukan transfer manual sekarang</p>
        <p class="text-xs text-blue-600">
            Transfer Rp{{ number_format($pencairanDana->jumlah, 0, ',', '.') }} ke
            {{ $pencairanDana->rekeningBank->nama_bank }} a.n {{ $pencairanDana->rekeningBank->nama_pemilik }}
            ({{ $pencairanDana->rekeningBank->no_rekening }}) di luar sistem, lalu tandai selesai di bawah.
        </p>
    </div>
    <form action="{{ route('admin.pencairan.selesai', $pencairanDana) }}" method="POST" class="flex gap-3">
        @csrf
        <input type="text" name="catatan_admin" placeholder="Catatan transfer (opsional, mis. no. referensi)"
               class="flex-1 border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
        <button type="submit"
                class="px-6 py-2.5 bg-green-700 text-paper text-sm font-semibold rounded-lg hover:bg-green-800 transition-colors flex-shrink-0">
            Tandai Selesai
        </button>
    </form>
    @endif

    {{-- Modal tolak --}}
    <dialog id="modalTolak" class="rounded-xl p-0 backdrop:bg-ink/40 w-full max-w-sm">
        <form action="{{ route('admin.pencairan.tolak', $pencairanDana) }}" method="POST" class="p-5 space-y-3">
            @csrf
            <h3 class="font-display text-base font-medium text-lake-900">Tolak Pengajuan</h3>
            <textarea name="catatan_admin" rows="3" required
                      placeholder="Alasan penolakan (wajib diisi, akan dilihat sales)"
                      class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 resize-none"></textarea>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 py-2 bg-ulos-maroon text-paper text-sm font-medium rounded-lg hover:opacity-90">
                    Tolak Pengajuan
                </button>
                <button type="button" onclick="document.getElementById('modalTolak').close()"
                        class="px-4 py-2 border border-lake-200 text-lake-900 text-sm rounded-lg hover:bg-lake-50">
                    Batal
                </button>
            </div>
        </form>
    </dialog>
</div>
@endsection
