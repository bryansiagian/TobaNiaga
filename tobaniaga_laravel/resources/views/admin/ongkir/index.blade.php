@extends('layouts.backoffice')
@section('title', 'Ongkos Kirim — TobaNiaga')
@section('page_title', 'Ongkos Kirim')

@section('content')
<div x-data="ongkirPage()" x-init="init()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-display text-xl font-medium text-lake-900">Trayek Ongkos Kirim</h2>
            <p class="text-sm text-ink/50 mt-0.5">Tentukan ongkir antar kecamatan. Trayek otomatis berlaku dua arah.</p>
        </div>
        <button @click="openCreate()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Buat Trayek
        </button>
    </div>

    {{-- Flash --}}
    @if(session('status'))
    <div class="mb-5 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('status') }}
    </div>
    @endif

    {{-- Info ongkir default --}}
    <div class="mb-6 px-5 py-4 rounded-xl bg-blue-50 border border-blue-200 flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <p class="text-xs text-blue-700">
            Kecamatan yang belum punya trayek akan dikenakan <strong>ongkir gratis (Rp0) sementara</strong> saat checkout, sampai trayeknya dibuat di sini.
        </p>
    </div>

    {{-- Tabel trayek --}}
    @if($trayekUnik->isEmpty())
    <div class="bg-paper border border-lake-900/10 rounded-xl p-16 text-center">
        <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-sm text-ink/40 mb-4">Belum ada trayek ongkir.</p>
        <button @click="openCreate()"
                class="inline-block px-5 py-2 rounded-lg bg-lake-900 text-paper text-sm font-medium hover:bg-lake-800">
            Buat Trayek Pertama
        </button>
    </div>
    @else
    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-lake-900/8 text-left">
                    <th class="px-5 py-3.5 font-mono text-xs text-ink/40 uppercase tracking-wide">Trayek</th>
                    <th class="px-5 py-3.5 font-mono text-xs text-ink/40 uppercase tracking-wide">Ongkir</th>
                    <th class="px-5 py-3.5 font-mono text-xs text-ink/40 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-lake-900/6">
                @foreach($trayekUnik as $t)
                <tr class="hover:bg-lake-50/50 transition-colors">
                    <td class="px-5 py-4">
                        <p class="font-medium text-ink">{{ $t->lokasi_asal }} ⇄ {{ $t->lokasi_tujuan }}</p>
                        <p class="text-xs text-ink/40 mt-0.5">Berlaku dua arah</p>
                    </td>
                    <td class="px-5 py-4 font-semibold text-lake-900">
                        Rp{{ number_format($t->ongkos, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-4">
                        @if($t->is_aktif)
                            <span class="text-xs px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200">Aktif</span>
                        @else
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500 border border-gray-200">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <button @click="openEdit({{ $t->toJson() }})"
                                    class="text-xs text-lake-800 font-medium hover:underline">Edit</button>
                            <form action="{{ route('admin.ongkir.toggle', $t) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs text-ink/50 font-medium hover:underline">
                                    {{ $t->is_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.ongkir.destroy', $t) }}" method="POST"
                                  onsubmit="return confirm('Hapus trayek {{ $t->lokasi_asal }} ⇄ {{ $t->lokasi_tujuan }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-ulos-maroon font-medium hover:underline">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Modal --}}
    <div x-show="modalOpen" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
         @keydown.escape.window="modalOpen = false">
        <div class="absolute inset-0 bg-ink/40" @click="modalOpen = false"></div>
        <div class="relative w-full sm:max-w-md bg-paper rounded-t-2xl sm:rounded-2xl shadow-2xl max-h-[92vh] overflow-y-auto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="flex justify-center pt-3 pb-1 sm:hidden">
                <div class="w-10 h-1 rounded-full bg-lake-900/20"></div>
            </div>

            <div class="flex items-center justify-between px-5 pt-4 pb-3 border-b border-lake-900/8">
                <h3 class="font-display text-lg font-medium text-lake-900" x-text="isEdit ? 'Edit Trayek' : 'Buat Trayek Baru'"></h3>
                <button @click="modalOpen = false" class="p-1.5 rounded-lg text-ink/40 hover:bg-lake-50 hover:text-ink transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="isEdit ? editUrl : '{{ route('admin.ongkir.store') }}'"
                  method="POST" class="px-5 py-4 space-y-4">
                @csrf

                @if($errors->any())
                <div class="px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
                    <ul class="space-y-0.5">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
                @endif

                <template x-if="isEdit">
                    <input name="_method" type="hidden" value="PUT">
                </template>

                <input type="hidden" name="lokasi_asal"   :value="form.lokasi_asal">
                <input type="hidden" name="lokasi_tujuan" :value="form.lokasi_tujuan">
                <input type="hidden" name="ongkos"        :value="form.ongkos">
                <template x-if="form.is_aktif">
                    <input type="hidden" name="is_aktif" value="1">
                </template>

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Lokasi Asal</label>
                    <select x-model="form.lokasi_asal"
                            class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 bg-paper">
                        <option value="">Pilih kecamatan...</option>
                        @foreach($semuaKecamatan as $kec)
                        <option value="{{ $kec }}">{{ $kec }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center justify-center">
                    <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 16V4m0 0L3 8m4-4l4 4m6 4v12m0 0l4-4m-4 4l-4-4"/>
                    </svg>
                </div>

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Lokasi Tujuan</label>
                    <select x-model="form.lokasi_tujuan"
                            class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20 bg-paper">
                        <option value="">Pilih kecamatan...</option>
                        @foreach($semuaKecamatan as $kec)
                        <option value="{{ $kec }}">{{ $kec }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Ongkos Kirim (Rp)</label>
                    <input type="number" x-model="form.ongkos" min="0" step="500"
                           placeholder="5000"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    <p class="text-xs text-ink/40 mt-1">Berlaku otomatis untuk kedua arah.</p>
                </div>

                <div x-show="isEdit">
                    <label class="flex items-center gap-2.5 cursor-pointer">
                        <input type="checkbox" :checked="form.is_aktif"
                               @change="form.is_aktif = !form.is_aktif"
                               class="w-4 h-4 accent-lake-800 rounded">
                        <div>
                            <p class="text-sm font-medium text-lake-900">Trayek Aktif</p>
                            <p class="text-xs text-ink/40">Nonaktifkan untuk menghentikan sementara tanpa menghapus</p>
                        </div>
                    </label>
                </div>

                <div class="flex gap-3 pt-2 pb-2">
                    <button type="submit"
                            class="flex-1 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-800 transition-colors"
                            x-text="isEdit ? 'Simpan Perubahan' : 'Buat Trayek'">
                    </button>
                    <button type="button" @click="modalOpen = false"
                            class="px-5 py-2.5 border border-lake-200 text-lake-900 text-sm font-medium rounded-lg hover:bg-lake-50 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function ongkirPage() {
    return {
        modalOpen: false,
        isEdit: false,
        editUrl: '',
        form: { lokasi_asal: '', lokasi_tujuan: '', ongkos: '', is_aktif: true },

        init() {
            @if($errors->any())
                this.form = {
                    lokasi_asal:   '{{ old('lokasi_asal') }}',
                    lokasi_tujuan: '{{ old('lokasi_tujuan') }}',
                    ongkos:        '{{ old('ongkos') }}',
                    is_aktif:      true,
                };
                this.modalOpen = true;
            @endif
        },

        openCreate() {
            this.isEdit = false;
            this.editUrl = '';
            this.form = { lokasi_asal: '', lokasi_tujuan: '', ongkos: '', is_aktif: true };
            this.modalOpen = true;
        },

        openEdit(trayek) {
            this.isEdit = true;
            this.editUrl = `/admin/ongkir/${trayek.id}`;
            this.form = {
                lokasi_asal:   trayek.lokasi_asal,
                lokasi_tujuan: trayek.lokasi_tujuan,
                ongkos:        trayek.ongkos,
                is_aktif:      trayek.is_aktif,
            };
            this.modalOpen = true;
        },
    }
}
</script>
@endpush
@endsection
