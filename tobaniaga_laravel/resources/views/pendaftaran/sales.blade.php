@extends('layouts.guest')
@section('title', 'Daftar Jadi Penjual — TobaNiaga')
@section('hide_navbar', true)

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <div class="text-center mb-8">
        <h1 class="font-display text-2xl font-semibold text-lake-900">Daftar Jadi Penjual UMKM</h1>
        <p class="text-sm text-ink/50 mt-1">Lengkapi data diri dan tokomu untuk mulai berjualan di TobaNiaga</p>
    </div>

    @if($errors->any())
    <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        <ul class="space-y-0.5">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('daftar.sales.store') }}" method="POST" enctype="multipart/form-data"
          class="bg-white rounded-xl border border-lake-100 overflow-hidden"
          x-data="{
              kecamatanList: [], desaList: [],
              kecamatanDipilih: '{{ old('kecamatan') }}', loadingKec: false, loadingDesa: false,
              async loadKecamatan() {
                  this.loadingKec = true;
                  try {
                      const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/districts/1206.json');
                      this.kecamatanList = await res.json();
                  } catch(e) {}
                  this.loadingKec = false;
              },
              async loadDesa() {
                  this.desaList = [];
                  const kec = this.kecamatanList.find(k => k.name === this.kecamatanDipilih);
                  if (!kec) return;
                  this.loadingDesa = true;
                  try {
                      const res = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/villages/' + kec.id + '.json');
                      this.desaList = await res.json();
                  } catch(e) {}
                  this.loadingDesa = false;
              },
              init() { this.loadKecamatan(); }
          }">
        @csrf

        {{-- Section: Data Pribadi --}}
        <div class="px-6 py-5 border-b border-lake-100">
            <h2 class="text-sm font-semibold text-lake-900 mb-1">Data Pribadi</h2>
            <p class="text-xs text-ink/40 mb-4">Data ini bersifat rahasia dan hanya digunakan untuk verifikasi.</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">NIK (16 digit)</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" inputmode="numeric"
                           placeholder="3201xxxxxxxxxxxx"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                               class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">No. HP Pribadi</label>
                        <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                               placeholder="08xxxxxxxxxx"
                               class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Alamat Sesuai KTP</label>
                    <textarea name="alamat_ktp" rows="2"
                              class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 resize-none">{{ old('alamat_ktp') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Foto KTP</label>
                        <input type="file" name="foto_ktp" accept="image/*"
                               class="w-full text-xs border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lake-900/20 file:mr-3 file:py-1 file:px-2 file:rounded file:border-0 file:bg-lake-50 file:text-lake-800 file:text-xs">
                        <p class="text-[11px] text-ink/40 mt-1">JPG/PNG, maks 2MB</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Foto Kartu Keluarga</label>
                        <input type="file" name="foto_kk" accept="image/*"
                               class="w-full text-xs border border-lake-900/15 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-lake-900/20 file:mr-3 file:py-1 file:px-2 file:rounded file:border-0 file:bg-lake-50 file:text-lake-800 file:text-xs">
                        <p class="text-[11px] text-ink/40 mt-1">JPG/PNG, maks 2MB</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Data UMKM --}}
        <div class="px-6 py-5">
            <h2 class="text-sm font-semibold text-lake-900 mb-4">Data Toko / UMKM</h2>

            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Nama UMKM</label>
                    <input type="text" name="nama_umkm" value="{{ old('nama_umkm') }}"
                           placeholder="Contoh: Kopi Lintong Asli"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                </div>

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Kategori UMKM</label>
                    <select name="kategori_id"
                            class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 bg-white">
                        <option value="">Pilih kategori...</option>
                        @foreach($kategoriUmkm as $k)
                        <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Deskripsi Singkat <span class="text-ink/40 font-normal">(opsional)</span></label>
                    <textarea name="deskripsi" rows="2"
                              class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 resize-none">{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">No. WhatsApp Toko</label>
                    <input type="text" name="no_hp_wa" value="{{ old('no_hp_wa') }}"
                           placeholder="08xxxxxxxxxx"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Provinsi</label>
                        <input type="text" name="provinsi" value="Sumatera Utara" readonly
                               class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm bg-lake-50 text-ink/50 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Kabupaten</label>
                        <input type="text" name="kabupaten" value="Kabupaten Toba" readonly
                               class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm bg-lake-50 text-ink/50 cursor-not-allowed">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Kecamatan</label>
                        <select name="kecamatan" x-model="kecamatanDipilih" @change="loadDesa()"
                                :disabled="loadingKec"
                                class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 bg-white">
                            <option value="" x-text="loadingKec ? 'Memuat...' : 'Pilih kecamatan...'"></option>
                            <template x-for="kec in kecamatanList" :key="kec.id">
                                <option :value="kec.name" x-text="kec.name" :selected="kec.name === kecamatanDipilih"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-lake-900 mb-1">Desa/Kelurahan</label>
                        <select name="desa"
                                :disabled="desaList.length === 0 || loadingDesa"
                                class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 bg-white disabled:opacity-50">
                            <option value="" x-text="loadingDesa ? 'Memuat...' : 'Pilih desa...'"></option>
                            <template x-for="desa in desaList" :key="desa.id">
                                <option :value="desa.name" x-text="desa.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Alamat Lengkap Toko</label>
                    <textarea name="alamat" rows="2"
                              class="w-full border border-lake-900/15 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 resize-none">{{ old('alamat') }}</textarea>
                </div>
            </div>
        </div>

        <div class="px-6 py-5 bg-lake-50 border-t border-lake-100">
            <button type="submit"
                    class="w-full px-5 py-3 bg-lake-900 text-paper text-sm font-semibold rounded-lg hover:bg-lake-800 transition-colors">
                Kirim Pendaftaran
            </button>
            <p class="text-xs text-ink/40 text-center mt-3">
                Dengan mendaftar, kamu setuju datamu diverifikasi oleh admin TobaNiaga.
            </p>
        </div>
    </form>
</div>
@endsection
