@extends('layouts.backoffice')

@section('title', 'UMKM Ditolak')
@section('role_label', 'Administrator')
@section('page_title', 'UMKM Ditolak')

@section('content')

    <div>

        <div class="mb-8">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Manajemen</p>
            <h2 class="font-display text-2xl font-medium text-lake-900">UMKM Ditolak</h2>
        </div>

        @if (session('status'))
            <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-lake-900/8">
                <h3 class="font-display text-base font-medium text-lake-900">Daftar UMKM Ditolak</h3>
            </div>

            @if ($umkm->isEmpty())
                <div class="px-6 py-14 text-center">
                    <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3"/>
                        </svg>
                    </div>
                    <p class="text-sm text-ink/40">Tidak ada UMKM yang ditolak.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-lake-900/8 text-left">
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Nama UMKM</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Pemilik</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Kategori</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Alasan Penolakan</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lake-900/6">
                        @foreach ($umkm as $item)
                            <tr>
                                <td class="px-6 py-3.5 text-ink/80 font-medium">
                                    <a href="{{ route('admin.umkm.detail', $item->id) }}" class="hover:underline">{{ $item->nama_umkm }}</a>
                                </td>
                                <td class="px-6 py-3.5 text-ink/60">
                                    {{ $item->owner->nama ?? '—' }}<br>
                                    <span class="text-xs text-ink/40">{{ $item->owner->email ?? '' }}</span>
                                </td>
                                <td class="px-6 py-3.5 text-ink/60">{{ $item->kategori->nama ?? '—' }}</td>
                                <td class="px-6 py-3.5 text-ink/60 max-w-xs">
                                    <span class="text-xs line-clamp-2">{{ $item->catatan_penolakan ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <form action="{{ route('admin.umkm.reactivate', $item->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Aktifkan kembali UMKM &quot;{{ $item->nama_umkm }}&quot;?')">
                                        @csrf
                                        <button type="submit" class="font-mono text-xs text-lake-800 hover:underline">Aktifkan Kembali</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="px-6 py-4 border-t border-lake-900/8">
                    {{ $umkm->links() }}
                </div>
            @endif
        </div>

    </div>

@endsection
