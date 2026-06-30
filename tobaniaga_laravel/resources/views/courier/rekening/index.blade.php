@extends('layouts.backoffice')
@section('title', 'Rekening Bank — TobaNiaga')
@section('page_title', 'Rekening Bank')

@section('content')
<div x-data="rekeningPage()" x-init="init()">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="font-display text-xl font-medium text-lake-900">Rekening Bank</h2>
            <p class="text-sm text-ink/50 mt-0.5">Rekening tujuan pencairan dana hasil pengirimanmu</p>
        </div>
        <button @click="openCreate()"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-800 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Rekening
        </button>
    </div>

    {{-- Flash --}}
    @if(session('status'))
    <div class="mb-5 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('status') }}
    </div>
    @endif
    @if(session('error'))
    <div class="mb-5 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        {{ session('error') }}
    </div>
    @endif

    {{-- Daftar rekening --}}
    @if($rekening->isEmpty())
    <div class="bg-paper border border-lake-900/10 rounded-xl p-16 text-center">
        <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
            <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
        </div>
        <p class="text-sm text-ink/40 mb-4">Belum ada rekening tersimpan.</p>
        <p class="text-xs text-ink/30 mb-4">Tambahkan rekening agar bisa mengajukan pencairan dana.</p>
        <button @click="openCreate()"
                class="inline-block px-5 py-2 rounded-lg bg-lake-900 text-paper text-sm font-medium hover:bg-lake-800">
            Tambah Rekening Pertama
        </button>
    </div>
    @else
    <div class="space-y-3">
        @foreach($rekening as $r)
        <div class="bg-paper border rounded-xl overflow-hidden {{ $r->is_utama ? 'border-lake-800/30' : 'border-lake-900/10' }}">
            <div class="px-5 py-4 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-10 h-10 rounded-lg bg-lake-50 border border-lake-900/10 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4.5 h-4.5 text-lake-800" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-sm font-semibold text-ink">{{ $r->nama_bank }}</p>
                            @if($r->is_utama)
                            <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-lake-800 text-paper">UTAMA</span>
                            @endif
                        </div>
                        <p class="text-xs text-ink/60 mt-0.5">{{ $r->nama_pemilik }}</p>
                        <p class="text-xs font-mono text-ink/50 mt-0.5">{{ $r->no_rekening }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    @if(!$r->is_utama)
                    <form action="{{ route('courier.rekening.utama', $r) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-xs text-lake-800 font-medium hover:underline">Jadikan Utama</button>
                    </form>
                    @endif
                    <button @click="openEdit({{ $r->toJson() }})"
                            class="text-xs text-lake-800 font-medium hover:underline">Edit</button>
                    <form action="{{ route('courier.rekening.destroy', $r) }}" method="POST"
                          onsubmit="return confirm('Hapus rekening {{ $r->nama_bank }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-ulos-maroon font-medium hover:underline">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
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
                <h3 class="font-display text-lg font-medium text-lake-900" x-text="isEdit ? 'Edit Rekening' : 'Tambah Rekening'"></h3>
                <button @click="modalOpen = false" class="p-1.5 rounded-lg text-ink/40 hover:bg-lake-50 hover:text-ink transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form :action="isEdit ? editUrl : '{{ route('courier.rekening.store') }}'"
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

                <input type="hidden" name="nama_bank"    :value="form.nama_bank">
                <input type="hidden" name="nama_pemilik" :value="form.nama_pemilik">
                <input type="hidden" name="no_rekening"  :value="form.no_rekening">

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Nama Bank</label>
                    <input type="text" x-model="form.nama_bank"
                           placeholder="Contoh: BCA, BNI, Mandiri"
                           list="bank-list"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    <datalist id="bank-list">
                        <option value="BCA">
                        <option value="BNI">
                        <option value="BRI">
                        <option value="Mandiri">
                        <option value="CIMB Niaga">
                        <option value="BSI">
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Nama Pemilik Rekening</label>
                    <input type="text" x-model="form.nama_pemilik"
                           placeholder="Sesuai buku rekening"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                </div>

                <div>
                    <label class="block text-xs font-medium text-lake-900 mb-1">Nomor Rekening</label>
                    <input type="text" x-model="form.no_rekening"
                           @input="form.no_rekening = form.no_rekening.replace(/[^0-9]/g, '')"
                           placeholder="1234567890"
                           class="w-full border border-lake-900/15 rounded-lg px-3 py-2 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                </div>

                <div class="flex gap-3 pt-2 pb-2">
                    <button type="submit"
                            class="flex-1 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-800 transition-colors"
                            x-text="isEdit ? 'Simpan Perubahan' : 'Tambah Rekening'">
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
function rekeningPage() {
    return {
        modalOpen: false,
        isEdit: false,
        editUrl: '',
        form: { nama_bank: '', nama_pemilik: '', no_rekening: '' },

        init() {
            @if($errors->any())
                this.form = {
                    nama_bank:    '{{ old('nama_bank') }}',
                    nama_pemilik: '{{ old('nama_pemilik') }}',
                    no_rekening:  '{{ old('no_rekening') }}',
                };
                this.modalOpen = true;
            @endif
        },

        openCreate() {
            this.isEdit = false;
            this.editUrl = '';
            this.form = { nama_bank: '', nama_pemilik: '', no_rekening: '' };
            this.modalOpen = true;
        },

        openEdit(rekening) {
            this.isEdit = true;
            this.editUrl = `/courier/rekening/${rekening.id}`;
            this.form = {
                nama_bank:    rekening.nama_bank,
                nama_pemilik: rekening.nama_pemilik,
                no_rekening:  rekening.no_rekening,
            };
            this.modalOpen = true;
        },
    }
}
</script>
@endpush
@endsection
