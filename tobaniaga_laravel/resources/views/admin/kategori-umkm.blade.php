@extends('layouts.backoffice')

@section('title', 'Kategori UMKM')
@section('role_label', 'Administrator')
@section('page_title', 'Kategori UMKM')

@section('content')

    <div x-data="{ open: false }">

        {{-- Greeting + tombol tambah --}}
        <div class="mb-8 flex items-center justify-between">
            <div>
                <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Manajemen</p>
                <h2 class="font-display text-2xl font-medium text-lake-900">Kategori UMKM</h2>
            </div>
            <button @click="open = true"
                    class="flex items-center gap-2 bg-lake-900 text-paper text-sm font-medium px-4 py-2.5 rounded-lg hover:bg-lake-800 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Kategori
            </button>
        </div>

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-5 px-4 py-3 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-sm text-ulos-maroon">
                {{ session('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-5 px-4 py-3 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-sm text-ulos-maroon">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Tabel kategori --}}
        <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-lake-900/8">
                <h3 class="font-display text-base font-medium text-lake-900">Daftar Kategori</h3>
            </div>

            @if ($kategori->isEmpty())
                <div class="px-6 py-14 text-center">
                    <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2M5 21H3"/>
                        </svg>
                    </div>
                    <p class="text-sm text-ink/40">Belum ada kategori UMKM.</p>
                </div>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-lake-900/8 text-left">
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Nama Kategori</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Jumlah UMKM</th>
                            <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-lake-900/6">
                        @foreach ($kategori as $item)
                            <tr>
                                <td class="px-6 py-3.5 text-ink/80 font-medium">{{ $item->nama }}</td>
                                <td class="px-6 py-3.5 text-ink/60">{{ $item->umkm_count }}</td>
                                <td class="px-6 py-3.5 text-right">
                                    <div x-data="{ openEdit: false }" class="inline-flex items-center">
                                        <button @click="openEdit = true" class="font-mono text-xs text-lake-800 hover:underline mr-3">Edit</button>

                                        <form action="{{ route('admin.kategori-umkm.destroy', $item->id) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Hapus kategori &quot;{{ $item->nama }}&quot;?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="font-mono text-xs text-ulos-maroon hover:underline">Hapus</button>
                                        </form>

                                        {{-- Modal edit --}}
                                        <div x-show="openEdit" x-cloak
                                             class="fixed inset-0 z-40 flex items-center justify-center bg-ink/30 px-4">
                                            <div @click.outside="openEdit = false" class="bg-paper rounded-xl shadow-xl border border-lake-900/10 w-full max-w-sm p-6">
                                                <h4 class="font-display text-base font-medium text-lake-900 mb-4">Edit Kategori</h4>
                                                <form action="{{ route('admin.kategori-umkm.update', $item->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Nama Kategori</label>
                                                    <input type="text" name="nama" value="{{ $item->nama }}" required
                                                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus-ring mb-4">
                                                    <div class="flex justify-end gap-2">
                                                        <button type="button" @click="openEdit = false"
                                                                class="px-4 py-2 text-sm text-ink/60 hover:text-ink transition-colors">Batal</button>
                                                        <button type="submit"
                                                                class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-800 transition-colors">Simpan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Modal tambah kategori --}}
        <div x-show="open" x-cloak
             class="fixed inset-0 z-40 flex items-center justify-center bg-ink/30 px-4">
            <div @click.outside="open = false" class="bg-paper rounded-xl shadow-xl border border-lake-900/10 w-full max-w-sm p-6">
                <h4 class="font-display text-base font-medium text-lake-900 mb-4">Tambah Kategori</h4>
                <form action="{{ route('admin.kategori-umkm.store') }}" method="POST">
                    @csrf
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Nama Kategori</label>
                    <input type="text" name="nama" required placeholder="Contoh: Kain Tenun"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus-ring mb-4">
                    <div class="flex justify-end gap-2">
                        <button type="button" @click="open = false"
                                class="px-4 py-2 text-sm text-ink/60 hover:text-ink transition-colors">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-800 transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

@endsection
