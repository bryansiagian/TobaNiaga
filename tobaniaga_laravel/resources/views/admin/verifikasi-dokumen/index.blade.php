@extends('layouts.backoffice')
@section('title', 'Verifikasi Dokumen — TobaNiaga')
@section('page_title', 'Verifikasi Dokumen')

@section('content')

@if(session('status'))
<div class="mb-5 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
    {{ session('status') }}
</div>
@endif

{{-- Tabs --}}
<div class="flex items-center gap-1 mb-6 border-b border-lake-900/10">
    @php
        $tabs = ['pending' => 'Menunggu', 'verified' => 'Terverifikasi', 'rejected' => 'Ditolak', 'semua' => 'Semua'];
    @endphp
    @foreach($tabs as $key => $label)
    <a href="{{ route('admin.verifikasi.dokumen.index', ['status' => $key]) }}"
       class="px-4 py-2.5 text-sm font-medium border-b-2 transition-colors
              {{ $status === $key ? 'border-lake-900 text-lake-900' : 'border-transparent text-ink/50 hover:text-ink' }}">
        {{ $label }}
        @if($key === 'pending' && $countPending > 0)
            <span class="ml-1 text-[10px] px-1.5 py-0.5 rounded-full bg-ulos-maroon text-paper">{{ $countPending }}</span>
        @endif
    </a>
    @endforeach
</div>

@if($users->isEmpty())
<div class="bg-paper border border-lake-900/10 rounded-xl p-16 text-center">
    <p class="text-sm text-ink/40">Tidak ada pengajuan dokumen di kategori ini.</p>
</div>
@else
<div class="space-y-3">
    @foreach($users as $u)
    @php
        $kode  = $u->statusVerifikasiDokumen?->kode;
        $badge = match($kode) {
            'pending'  => ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Menunggu'],
            'verified' => ['bg-green-50 text-green-700 border-green-200',   'Terverifikasi'],
            'rejected' => ['bg-red-50 text-red-700 border-red-200',         'Ditolak'],
            default    => ['bg-gray-50 text-gray-600 border-gray-200',      $kode],
        };
        $role = $u->getRoleNames()->first();
    @endphp
    <div class="bg-paper border border-lake-900/10 rounded-xl px-5 py-4 flex items-center justify-between gap-4">
        <div class="min-w-0">
            <div class="flex items-center gap-2">
                <p class="text-sm font-semibold text-ink">{{ $u->nama }}</p>
                <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-lake-50 border border-lake-900/10 text-lake-800 uppercase">
                    {{ $role }}
                </span>
                <span class="text-xs px-2 py-0.5 rounded-full border {{ $badge[0] }}">{{ $badge[1] }}</span>
            </div>
            <p class="text-xs text-ink/40 mt-0.5">{{ $u->email }}</p>
            @if($kode === 'rejected' && $u->catatan_penolakan_dokumen)
            <p class="text-xs text-red-600 mt-1">Alasan: {{ $u->catatan_penolakan_dokumen }}</p>
            @endif
        </div>
        <a href="{{ route('admin.verifikasi.dokumen.show', $u) }}"
           class="flex-shrink-0 px-4 py-2 bg-lake-900 text-paper text-xs font-semibold rounded-lg hover:bg-lake-800 transition-colors">
            Periksa Dokumen
        </a>
    </div>
    @endforeach
</div>
<div class="mt-5">{{ $users->links() }}</div>
@endif

@endsection
