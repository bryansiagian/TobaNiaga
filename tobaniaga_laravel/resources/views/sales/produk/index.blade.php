@extends('layouts.backoffice')

@section('title', 'Produk Saya')
@section('role_label', 'Sales')
@section('page_title', 'Produk Saya')

@section('content')

<div x-data="{
    modalTambah: false,
    modalEdit: false,
    modalHapus: false,
    editData: {},
    hapusId: null,
    hapusNama: '',
    openEdit(data) {
        this.editData = { ...data };
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
            <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">{{ $umkm->nama_umkm }}</p>
            <h2 class="font-display text-2xl font-medium text-lake-900">Produk Saya</h2>
        </div>
        <button @click="modalTambah = true"
                class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90 transition-colors">
            + Tambah Produk
        </button>
    </div>

    @if (session('status'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status') }}
        </div>
    @endif

    {{-- Tabel produk --}}
    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
            <h3 class="font-display text-base font-medium text-lake-900">Daftar Produk</h3>
            <span class="font-mono text-xs text-ink/40">{{ $produk->total() }} produk</span>
        </div>

        @if ($produk->isEmpty())
            <div class="px-6 py-14 text-center">
                <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10"/>
                    </svg>
                </div>
                <p class="text-sm text-ink/40">Belum ada produk. Tambahkan produk pertama kamu.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-lake-900/8 text-left">
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Foto</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Nama Produk</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Kategori</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Harga</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Stok</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Status</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lake-900/6">
                    @foreach ($produk as $item)
                        <tr>
                            <td class="px-6 py-3">
                                @if ($item->fotoProduk->first())
                                    @php $foto = $item->fotoProduk->first()->url_foto; @endphp
                                    <img src="{{ Str::startsWith($foto, ['http://', 'https://']) ? $foto : Storage::url($foto) }}"
                                        class="w-10 h-10 rounded-lg object-cover border border-lake-900/10">
                                @else
                                    <div class="w-10 h-10 rounded-lg bg-lake-50 border border-lake-900/10 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-lake-900/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-ink/80 font-medium">{{ $item->nama_produk }}</td>
                            <td class="px-6 py-3 text-ink/60">{{ $item->kategori->nama ?? '—' }}</td>
                            <td class="px-6 py-3 text-ink/60">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-3 text-ink/60">{{ $item->stok }} {{ $item->satuan }}</td>
                            <td class="px-6 py-3">
                                @php $kode = $item->status?->kode; @endphp
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium
                                    {{ $kode === 'tersedia' ? 'bg-lake-50 text-lake-800 border border-lake-400/20' : '' }}
                                    {{ $kode === 'habis'    ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                    {{ $kode === 'nonaktif' ? 'bg-ink/5 text-ink/40 border border-ink/10' : '' }}
                                ">
                                    {{ $item->status?->label ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right whitespace-nowrap">
                                <button @click="openEdit({
                                    id: {{ $item->id }},
                                    nama_produk: '{{ addslashes($item->nama_produk) }}',
                                    kategori_id: {{ $item->kategori_id }},
                                    status_id: {{ $item->status_id }},
                                    deskripsi: '{{ addslashes($item->deskripsi) }}',
                                    harga: {{ $item->harga }},
                                    stok: {{ $item->stok }},
                                    satuan: '{{ $item->satuan }}',
                                    foto_count: {{ $item->fotoProduk->count() }},
                                    foto: @json($item->fotoProduk->map(fn($f) => ['id' => $f->id, 'url' => Storage::url($f->url_foto)]))
                                })"
                                class="font-mono text-xs text-lake-800 hover:underline mr-3">Edit</button>

                                <button @click="openHapus({{ $item->id }}, '{{ addslashes($item->nama_produk) }}')"
                                        class="font-mono text-xs text-ink/30 hover:text-ulos-maroon hover:underline">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-lake-900/8">
                {{ $produk->links() }}
            </div>
        @endif
    </div>

    {{-- ── Modal Tambah ── --}}
    <div x-show="modalTambah" x-cloak
         class="fixed inset-0 z-40 flex items-start justify-center bg-ink/30 px-4 py-8 overflow-y-auto">
        <div @click.outside="modalTambah = false"
             class="bg-paper rounded-xl shadow-xl border border-lake-900/10 w-full max-w-lg p-6 my-auto">
            <h4 class="font-display text-base font-medium text-lake-900 mb-4">Tambah Produk</h4>
            <form action="{{ route('sales.produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Nama Produk</label>
                    <input type="text" name="nama_produk"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Kategori</label>
                        <select name="kategori_id" class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20" required>
                            <option value="">Pilih kategori</option>
                            @foreach ($kategori as $kat)
                                <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Satuan</label>
                        <input type="text" name="satuan" placeholder="pcs, kg, porsi..."
                               class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                               required>
                    </div>
                </div>

                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                              class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                              required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Harga (Rp)</label>
                        <input type="number" name="harga" min="0" step="100"
                               class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                               required>
                    </div>
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Stok</label>
                        <input type="number" name="stok" min="0"
                               class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                               required>
                    </div>
                </div>

                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">
                        Foto Produk <span class="normal-case text-ink/30">(maks. 10 foto, jpg/png/webp, maks. 2MB/foto)</span>
                    </label>
                    <input type="file" name="foto[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="w-full text-sm text-ink/60 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-lake-50 file:text-lake-800 hover:file:bg-lake-100">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalTambah = false"
                            class="px-4 py-2 text-sm text-ink/60 hover:text-ink transition-colors">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal Edit ── --}}
    <div x-show="modalEdit" x-cloak
         class="fixed inset-0 z-40 flex items-start justify-center bg-ink/30 px-4 py-8 overflow-y-auto">
        <div @click.outside="modalEdit = false"
             class="bg-paper rounded-xl shadow-xl border border-lake-900/10 w-full max-w-lg p-6 my-auto">
            <h4 class="font-display text-base font-medium text-lake-900 mb-4">Edit Produk</h4>
            <form :action="`/sales/produk/${editData.id}`" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Nama Produk</label>
                    <input type="text" name="nama_produk"
                           x-bind:value="editData.nama_produk"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Kategori</label>
                        <select name="kategori_id" class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20" required>
                            <option value="">Pilih kategori</option>
                            @foreach ($kategori as $kat)
                                <option value="{{ $kat->id }}"
                                    x-bind:selected="editData.kategori_id == {{ $kat->id }}">
                                    {{ $kat->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Status</label>
                        <select name="status_id" class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20" required>
                            @foreach ($statusList as $st)
                                <option value="{{ $st->id }}"
                                    x-bind:selected="editData.status_id == {{ $st->id }}">
                                    {{ $st->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                              x-effect="$el.value = editData.deskripsi ?? ''"
                              class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                              required></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Harga (Rp)</label>
                        <input type="number" name="harga" min="0" step="100"
                               x-bind:value="editData.harga"
                               class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                               required>
                    </div>
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Stok</label>
                        <input type="number" name="stok" min="0"
                               x-bind:value="editData.stok"
                               class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                               required>
                    </div>
                </div>

                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Satuan</label>
                    <input type="text" name="satuan"
                           x-bind:value="editData.satuan"
                           class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                </div>

                {{-- Foto existing --}}
                <div x-show="editData.foto && editData.foto.length > 0">
                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-2">Foto Saat Ini</p>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="foto in editData.foto" :key="foto.id">
                            <div class="relative">
                                <img :src="foto.url" class="w-16 h-16 rounded-lg object-cover border border-lake-900/10">
                                <form :action="`/sales/produk/foto/${foto.id}`" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-ulos-maroon text-paper rounded-full text-xs flex items-center justify-center hover:bg-ulos-maroon/80">×</button>
                                </form>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Tambah foto baru --}}
                <div x-show="(editData.foto_count ?? 0) < 10">
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">
                        Tambah Foto <span class="normal-case text-ink/30">(sisa slot: <span x-text="10 - (editData.foto_count ?? 0)"></span>)</span>
                    </label>
                    <input type="file" name="foto[]" multiple accept="image/jpg,image/jpeg,image/png,image/webp"
                           class="w-full text-sm text-ink/60 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-lake-50 file:text-lake-800 hover:file:bg-lake-100">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="modalEdit = false"
                            class="px-4 py-2 text-sm text-ink/60 hover:text-ink transition-colors">Batal</button>
                    <button type="submit"
                            class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Modal Hapus ── --}}
    <div x-show="modalHapus" x-cloak
         class="fixed inset-0 z-40 flex items-center justify-center bg-ink/30 px-4">
        <div @click.outside="modalHapus = false"
             class="bg-paper rounded-xl shadow-xl border border-lake-900/10 w-full max-w-sm p-6">
            <h4 class="font-display text-base font-medium text-lake-900 mb-1">Hapus Produk</h4>
            <p class="text-sm text-ink/60 mb-5">Hapus <span class="font-medium text-ink" x-text="hapusNama"></span>? Semua foto akan ikut terhapus dan tidak bisa dikembalikan.</p>
            <form :action="`/sales/produk/${hapusId}`" method="POST" class="flex justify-end gap-2">
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
