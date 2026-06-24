@extends('layouts.backoffice')

@section('title', 'Kategori Produk')
@section('role_label', 'Admin')
@section('page_title', 'Kategori Produk')

@section('content')

<div x-data="{
    modalTambah: false,
    modalEdit: false,
    modalHapus: false,
    editData: {},
    hapusId: null,
    hapusNama: '',
    openEdit(id, nama) {
        this.editData = { id, nama };
        this.modalEdit = true;
    },
    openHapus(id, nama) {
        this.hapusId = id;
        this.hapusNama = nama;
        this.modalHapus = true;
    }
}">

    <div class="mb-8 flex items-center justify-between">
        <div>
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Master Data</p>
            <h2 class="font-display text-2xl font-medium text-lake-900">Kategori Produk</h2>
        </div>
        <button @click="modalTambah = true"
                class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90 transition-colors">
            + Tambah Kategori
        </button>
    </div>

    @if (session('status'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
            <h3 class="font-display text-base font-medium text-lake-900">Daftar Kategori</h3>
            <span class="font-mono text-xs text-ink/40">{{ $kategori->total() }} kategori</span>
        </div>

        @if ($kategori->isEmpty())
            <div class="px-6 py-14 text-center">
                <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2z"/>
                    </svg>
                </div>
                <p class="text-sm text-ink/40">Belum ada kategori. Tambahkan kategori pertama.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-lake-900/8 text-left">
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">No</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Nama Kategori</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lake-900/6">
                    @foreach ($kategori as $item)
                        <tr>
                            <td class="px-6 py-3 font-mono text-xs text-ink/30">{{ $kategori->firstItem() + $loop->index }}</td>
                            <td class="px-6 py-3 text-ink/80 font-medium">{{ $item->nama }}</td>
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <button @click="openEdit({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                                        class="font-mono text-xs text-lake-800 hover:underline mr-3">Edit</button>
                                <button @click="openHapus({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                                        class="font-mono text-xs text-ink/30 hover:text-ulos-maroon hover:underline">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-lake-900/8">
                {{ $kategori->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Tambah --}}
    <div x-show="modalTambah" x-cloak
         class="fixed inset-0 z-40 flex items-center justify-center bg-ink/30 px-4">
        <div @click.outside="modalTambah = false"
             class="bg-paper rounded-xl shadow-xl border border-lake-900/10 w-full max-w-sm p-6">
            <h4 class="font-display text-base font-medium text-lake-900 mb-4">Tambah Kategori</h4>
            <form action="{{ route('admin.kategori-produk.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Nama Kategori</label>
                    <input type="text" name="nama" autofocus
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           placeholder="Contoh: Kain Ulos, Makanan Ringan..."
                           required>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="modalTambah = false"
                            class="px-4 py-2 text-sm text-ink/60 hover:text-ink transition-colors">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div x-show="modalEdit" x-cloak
         class="fixed inset-0 z-40 flex items-center justify-center bg-ink/30 px-4">
        <div @click.outside="modalEdit = false"
             class="bg-paper rounded-xl shadow-xl border border-lake-900/10 w-full max-w-sm p-6">
            <h4 class="font-display text-base font-medium text-lake-900 mb-4">Edit Kategori</h4>
            <form :action="`/admin/kategori-produk/${editData.id}`" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Nama Kategori</label>
                    <input type="text" name="nama"
                           x-bind:value="editData.nama"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                </div>
                <div class="flex justify-end gap-2 pt-1">
                    <button type="button" @click="modalEdit = false"
                            class="px-4 py-2 text-sm text-ink/60 hover:text-ink transition-colors">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div x-show="modalHapus" x-cloak
         class="fixed inset-0 z-40 flex items-center justify-center bg-ink/30 px-4">
        <div @click.outside="modalHapus = false"
             class="bg-paper rounded-xl shadow-xl border border-lake-900/10 w-full max-w-sm p-6">
            <h4 class="font-display text-base font-medium text-lake-900 mb-1">Hapus Kategori</h4>
            <p class="text-sm text-ink/60 mb-5">Hapus kategori <span class="font-medium text-ink" x-text="hapusNama"></span>? Produk yang menggunakan kategori ini tidak akan terhapus.</p>
            <form :action="`/admin/kategori-produk/${hapusId}`" method="POST" class="flex justify-end gap-2">
                @csrf
                @method('DELETE')
                <button type="button" @click="modalHapus = false"
                        class="px-4 py-2 text-sm text-ink/60 hover:text-ink transition-colors">Batal</button>
                <button type="submit"
                        class="px-4 py-2 bg-ulos-maroon text-paper text-sm font-medium rounded-lg hover:bg-ulos-maroon/90">Hapus</button>
            </form>
        </div>
    </div>

</div>

@endsection
