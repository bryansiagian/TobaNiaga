@extends('layouts.backoffice')

@section('title', 'Dashboard Admin')
@section('role_label', 'Administrator')
@section('page_title', 'Dashboard Admin')

@section('content')

    {{-- Greeting --}}
    <div class="mb-8">
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Panel Kontrol</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">Halo, {{ auth()->user()->nama ?? 'Admin' }}</h2>
    </div>

    {{-- Stat cards --}}
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Total Pengguna</p>
            <p class="font-display text-3xl font-medium text-lake-900">{{ $stats['total_user'] }}</p>
            <p class="text-xs text-ink/50 mt-1">Semua role</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">UMKM Terdaftar</p>
            <p class="font-display text-3xl font-medium text-lake-900">{{ $stats['total_umkm'] }}</p>
            <p class="text-xs text-ink/50 mt-1">Aktif di platform</p>
        </div>
        <div class="stat-card border-l-2 border-l-ulos-maroon">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Menunggu Verifikasi</p>
            <p class="font-display text-3xl font-medium text-ulos-maroon">{{ $stats['pending_umkm'] }}</p>
            <p class="text-xs text-ink/50 mt-1">Perlu ditinjau</p>
        </div>
        <div class="stat-card">
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-3">Total Pesanan</p>
            <p class="font-display text-3xl font-medium text-lake-900">{{ $stats['total_pesanan'] }}</p>
            <p class="text-xs text-ink/50 mt-1">Sepanjang waktu</p>
        </div>
    </div>

    <div class="grid lg:grid-cols-[1fr_300px] gap-6">

        {{-- UMKM menunggu verifikasi --}}
        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <h3 class="font-display text-base font-medium text-lake-900">UMKM Menunggu Verifikasi</h3>
                    @if ($stats['pending_umkm'] > 0)
                        <span class="font-mono text-[10px] bg-ulos-maroon/10 text-ulos-maroon px-2 py-0.5 rounded-full uppercase tracking-wider">{{ $stats['pending_umkm'] }} Perlu Aksi</span>
                    @endif
                </div>
                <a href="{{ route('admin.umkm.pending') }}" class="font-mono text-xs text-lake-800 hover:underline">Lihat semua</a>
            </div>

            @if ($umkmPending->isEmpty())
                <div class="px-6 py-14 text-center">
                    <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3"/>
                        </svg>
                    </div>
                    <p class="text-sm text-ink/40">Tidak ada antrian verifikasi.</p>
                    <p class="text-xs text-ink/30 mt-1">UMKM yang mendaftar akan muncul di sini.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-lake-900/8 text-left">
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Nama UMKM</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Pemilik</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Kategori</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lake-900/6">
                        @foreach ($umkmPending as $item)
                            <tr>
                                <td class="px-6 py-3.5 text-ink/80 font-medium">{{ $item->nama_umkm }}</td>
                                <td class="px-6 py-3.5 text-ink/60">{{ $item->owner->nama ?? '—' }}</td>
                                <td class="px-6 py-3.5 text-ink/60">{{ $item->kategori->nama ?? '—' }}</td>
                                <td class="px-6 py-3.5 text-right">
                                    <a href="{{ route('admin.umkm.pending', $item->id) }}"
                                       class="font-mono text-xs text-lake-800 hover:underline">Tinjau</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Kolom kanan --}}
        <div class="space-y-4">

            {{-- Pengguna per role --}}
            <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-lake-900/8">
                    <h3 class="font-display text-base font-medium text-lake-900">Pengguna per Role</h3>
                </div>
                <div class="divide-y divide-lake-900/6">
                    @foreach([
                        ['label' => 'Customer', 'color' => '#1a4a6b'],
                        ['label' => 'Sales',    'color' => '#c49044'],
                        ['label' => 'Courier',  'color' => '#0f2d45'],
                        ['label' => 'Admin',    'color' => '#8f2333'],
                    ] as $role)
                        <div class="flex items-center justify-between px-5 py-3">
                            <div class="flex items-center gap-2.5">
                                <span class="w-2 h-2 rounded-full" style="background:{{ $role['color'] }}"></span>
                                <span class="text-sm text-ink/70">{{ $role['label'] }}</span>
                            </div>
                            <span class="font-mono text-xs text-ink/40">{{ $perRole[$role['label']] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Aksi cepat admin --}}
            <div class="bg-lake-900 rounded-xl p-5 text-paper">
                <p class="font-mono text-[10px] uppercase tracking-widest text-paper/40 mb-3">Aksi Cepat</p>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-2.5 py-2 text-sm text-paper/80 hover:text-paper transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Kelola pengguna
                </a>
                <a href="{{ route('admin.umkm.pending') }}" class="flex items-center gap-2.5 py-2 text-sm text-paper/80 hover:text-paper transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Proses verifikasi UMKM
                </a>
                <a href="{{ route('admin.kategori-umkm.index') }}" class="flex items-center gap-2.5 py-2 text-sm text-paper/80 hover:text-paper transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 11V6a3 3 0 013-3z"/>
                    </svg>
                    Kelola kategori UMKM
                </a>
            </div>

        </div>
    </div>

@endsection
