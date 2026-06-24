@extends('layouts.backoffice')

@section('title', 'Verifikasi UMKM')
@section('role_label', 'Administrator')
@section('page_title', 'Verifikasi UMKM')

@section('content')

    <div x-data="{ rejectId: null }">

        <div class="mb-8">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Manajemen</p>
            <h2 class="font-display text-2xl font-medium text-lake-900">UMKM Menunggu Verifikasi</h2>
        </div>

        @if (session('status'))
            <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
                {{ session('status') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-5 px-4 py-3 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-sm text-ulos-maroon">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-lake-900/8">
                <h3 class="font-display text-base font-medium text-lake-900">Antrian Verifikasi</h3>
            </div>

            @if ($umkm->isEmpty())
                <div class="px-6 py-14 text-center">
                    <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3"/>
                        </svg>
                    </div>
                    <p class="text-sm text-ink/40">Tidak ada antrian verifikasi.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-lake-900/8 text-left">
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Nama UMKM</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Pemilik</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Kategori</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Kota</th>
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
                                <td class="px-6 py-3.5 text-ink/60">{{ $item->kota ?? '—' }}</td>
                                <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                    <form action="{{ route('admin.umkm.approve', $item->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Setujui UMKM &quot;{{ $item->nama_umkm }}&quot;?')">
                                        @csrf
                                        <button type="submit" class="font-mono text-xs text-lake-800 hover:underline mr-3">Setujui</button>
                                    </form>
                                    <button @click="rejectId = {{ $item->id }}" class="font-mono text-xs text-ulos-maroon hover:underline">Tolak</button>
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

        {{-- Modal reject --}}
        @foreach ($umkm as $item)
            <div x-show="rejectId === {{ $item->id }}" x-cloak
                 class="fixed inset-0 z-40 flex items-center justify-center bg-ink/30 px-4">
                <div @click.outside="rejectId = null" class="bg-paper rounded-xl shadow-xl border border-lake-900/10 w-full max-w-sm p-6">
                    <h4 class="font-display text-base font-medium text-lake-900 mb-1">Tolak UMKM</h4>
                    <p class="text-sm text-ink/60 mb-4">{{ $item->nama_umkm }}</p>
                    <form action="{{ route('admin.umkm.reject', $item->id) }}" method="POST">
                        @csrf
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Alasan Penolakan</label>
                        <textarea name="alasan" rows="3" required
                                  class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus-ring mb-4"
                                  placeholder="Contoh: foto tidak jelas, alamat tidak valid, dll."></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="rejectId = null"
                                    class="px-4 py-2 text-sm text-ink/60 hover:text-ink transition-colors">Batal</button>
                            <button type="submit"
                                    class="px-4 py-2 bg-ulos-maroon text-paper text-sm font-medium rounded-lg hover:bg-ulos-maroon/90 transition-colors">Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach

    </div>

@endsection
