@extends('layouts.backoffice')

@section('title', 'Profil UMKM')
@section('role_label', 'Sales')
@section('page_title', 'Profil UMKM')

@section('content')

<div x-data="{
    kecamatan: '{{ $umkm->kecamatan ?? '' }}',
    desa: '{{ $umkm->desa ?? '' }}',
    kecamatanList: [],
    desaList: [],
    kecamatanDipilih: '{{ $umkm->kecamatan ?? '' }}',

    async init() {
        await this.loadKecamatan();
        if (this.kecamatanDipilih) await this.loadDesa();
    },

    async loadKecamatan() {
        try {
            const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/districts/1206.json');
            this.kecamatanList = await res.json();
        } catch (e) {
            console.error('Gagal memuat kecamatan:', e);
        }
    },

    async loadDesa() {
        this.desaList = [];
        const kec = this.kecamatanList.find(k => k.name === this.kecamatanDipilih);
        if (!kec) return;
        try {
            const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${kec.id}.json`);
            this.desaList = await res.json();
        } catch (e) {
            console.error('Gagal memuat desa:', e);
        }
    }
}">

    <div class="mb-8">
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Akun</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">Profil UMKM</h2>
    </div>

    @if (session('status'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-5 px-4 py-3 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-sm text-ulos-maroon">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sales.profil.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid lg:grid-cols-3 gap-6">

            {{-- Kolom kiri: foto --}}
            <div class="space-y-4">

                {{-- Foto Profil --}}
                <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-3">Foto Profil</p>
                    <div class="flex flex-col items-center gap-3">
                        @if ($umkm->foto_profil)
                            <img src="{{ Storage::url($umkm->foto_profil) }}"
                                 class="w-24 h-24 rounded-full object-cover border-2 border-lake-900/10">
                        @else
                            <div class="w-24 h-24 rounded-full bg-lake-50 border-2 border-dashed border-lake-900/20 flex items-center justify-center">
                                <svg class="w-8 h-8 text-lake-900/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        @endif
                        <input type="file" name="foto_profil" accept="image/*"
                               class="w-full text-xs text-ink/60 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-lake-50 file:text-lake-800 hover:file:bg-lake-100">
                        <p class="text-[11px] text-ink/30 text-center">jpg/png/webp, maks. 2MB</p>
                    </div>
                </div>

                {{-- Foto Banner --}}
                <div class="bg-paper border border-lake-900/10 rounded-xl p-5">
                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-3">Foto Banner</p>
                    @if ($umkm->foto_banner)
                        <img src="{{ Storage::url($umkm->foto_banner) }}"
                             class="w-full h-24 rounded-lg object-cover border border-lake-900/10 mb-3">
                    @else
                        <div class="w-full h-24 rounded-lg bg-lake-50 border-2 border-dashed border-lake-900/20 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-lake-900/20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    <input type="file" name="foto_banner" accept="image/*"
                           class="w-full text-xs text-ink/60 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-lake-50 file:text-lake-800 hover:file:bg-lake-100">
                    <p class="text-[11px] text-ink/30 mt-2">jpg/png/webp, maks. 2MB</p>
                </div>

            </div>

            {{-- Kolom kanan: info --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Info Dasar --}}
                <div class="bg-paper border border-lake-900/10 rounded-xl p-5 space-y-4">
                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40">Informasi Dasar</p>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Nama UMKM</label>
                        <input type="text" name="nama_umkm" value="{{ old('nama_umkm', $umkm->nama_umkm) }}"
                               class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                               required>
                    </div>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Deskripsi</label>
                        <textarea name="deskripsi" rows="4"
                                  class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">{{ old('deskripsi', $umkm->deskripsi) }}</textarea>
                    </div>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">No. HP / WA</label>
                        <input type="text" name="no_hp_wa" value="{{ old('no_hp_wa', $umkm->no_hp_wa) }}"
                               class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>
                </div>

                {{-- Alamat --}}
                <div class="bg-paper border border-lake-900/10 rounded-xl p-5 space-y-4">
                    <p class="font-mono text-[10px] uppercase tracking-widest text-ink/40">Alamat</p>

                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Alamat Lengkap</label>
                        <input type="text" name="alamat" value="{{ old('alamat', $umkm->alamat) }}"
                               class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>

                    <input type="hidden" name="provinsi" value="Sumatera Utara">
                    <input type="hidden" name="kabupaten" value="Kabupaten Toba">

                    <div class="grid grid-cols-2 gap-3">

                        {{-- Kecamatan --}}
                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">
                                Kecamatan
                            </label>
                            <select name="kecamatan"
                                    x-model="kecamatanDipilih"
                                    @change="loadDesa()"
                                    class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                                <option value="">Pilih kecamatan</option>
                                <template x-for="k in kecamatanList" :key="k.id">
                                    <option :value="k.name" :selected="k.name === kecamatanDipilih" x-text="k.name"></option>
                                </template>
                            </select>
                        </div>

                        {{-- Desa --}}
                        <div>
                            <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">
                                Desa/Kelurahan
                            </label>
                            <select name="desa"
                                    :disabled="desaList.length === 0"
                                    class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20 disabled:opacity-40">
                                <option value="">Pilih desa</option>
                                <template x-for="d in desaList" :key="d.id">
                                    <option :value="d.name" :selected="d.name === desa" x-text="d.name"></option>
                                </template>
                            </select>
                        </div>

                    </div>
                </div>

                {{-- Submit --}}
                <div class="flex justify-end">
                    <button type="submit"
                            class="px-5 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>

            </div>
        </div>
    </form>

</div>

@endsection
