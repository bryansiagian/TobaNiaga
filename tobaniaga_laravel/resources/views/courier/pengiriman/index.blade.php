@extends('layouts.backoffice')

@section('title', 'Daftar Pengiriman')
@section('role_label', 'Kurir')
@section('page_title', 'Daftar Pengiriman')

@section('content')

<div class="mb-8 flex flex-wrap items-start justify-between gap-4">
    <div>
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Kurir TobaNiaga</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">Daftar Pengiriman</h2>
    </div>
    <a href="{{ route('courier.dashboard') }}"
       class="px-4 py-2 border border-lake-900/15 text-ink/60 text-sm rounded-lg hover:bg-lake-50">
        ← Dashboard
    </a>
</div>

@if(session('status'))
<div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
    {{ session('status') }}
</div>
@endif

{{-- Tab status --}}
<div class="flex gap-2 flex-wrap mb-5">
    <a href="{{ route('courier.pengiriman.index', request()->except('status', 'tersedia', 'page')) }}"
       class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition-colors
              {{ !request('status') && !request('tersedia') ? 'bg-lake-900 text-paper border-lake-900' : 'bg-paper text-ink/60 border-lake-900/15 hover:border-lake-900/30' }}">
        Semua
    </a>
    <a href="{{ route('courier.pengiriman.index', array_merge(request()->except('status', 'tersedia', 'page'), ['tersedia' => 1])) }}"
       class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition-colors
              {{ request('tersedia') ? 'bg-lake-900 text-paper border-lake-900' : 'bg-yellow-50 text-yellow-700 border-yellow-200 hover:opacity-80' }}">
        Tersedia
    </a>
    @foreach($statusList as $s)
    @php
        $aktif = request('status') === $s->kode;
        $warna = match($s->kode) {
            'menunggu_kurir' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
            'dijemput'       => 'bg-blue-50 text-blue-700 border-blue-200',
            'diantar'        => 'bg-purple-50 text-purple-700 border-purple-200',
            'selesai'        => 'bg-green-50 text-green-700 border-green-200',
            default          => 'bg-gray-50 text-gray-600 border-gray-200',
        };
    @endphp
    <a href="{{ route('courier.pengiriman.index', array_merge(request()->except('status', 'tersedia', 'page'), ['status' => $s->kode])) }}"
       class="px-3.5 py-1.5 rounded-full text-xs font-medium border transition-colors
              {{ $aktif ? 'bg-lake-900 text-paper border-lake-900' : $warna . ' hover:opacity-80' }}">
        {{ $s->label }}
    </a>
    @endforeach
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('courier.pengiriman.index') }}"
      class="flex flex-wrap gap-2 mb-5">
    @if(request('status'))
        <input type="hidden" name="status" value="{{ request('status') }}">
    @endif
    @if(request('tersedia'))
        <input type="hidden" name="tersedia" value="1">
    @endif
    <input type="text" name="cari" value="{{ request('cari') }}"
           placeholder="Cari no. pesanan / nama pembeli..."
           class="flex-1 min-w-48 border border-lake-900/15 rounded-lg px-3.5 py-2 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
    <input type="date" name="dari" value="{{ request('dari') }}"
           class="border border-lake-900/15 rounded-lg px-3.5 py-2 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
    <input type="date" name="sampai" value="{{ request('sampai') }}"
           class="border border-lake-900/15 rounded-lg px-3.5 py-2 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20">
    <button type="submit"
            class="px-4 py-2 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90">
        Cari
    </button>
    @if(request()->hasAny(['cari', 'dari', 'sampai']))
    <a href="{{ route('courier.pengiriman.index', request()->only(['status', 'tersedia'])) }}"
       class="px-4 py-2 border border-lake-900/15 text-ink/60 text-sm rounded-lg hover:bg-lake-50">
        Reset
    </a>
    @endif
</form>

{{-- Tabel --}}
<div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b border-lake-900/8 flex items-center justify-between">
        <h3 class="font-display text-base font-medium text-lake-900">Pengiriman</h3>
        <span class="font-mono text-xs text-ink/40">{{ $pengiriman->total() }} data</span>
    </div>

    @if($pengiriman->isEmpty())
        <div class="px-6 py-16 text-center">
            <div class="w-12 h-12 rounded-xl bg-lake-50 border border-lake-900/10 flex items-center justify-center mx-auto mb-3">
                <svg class="w-5 h-5 text-lake-900/30" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
            <p class="text-sm text-ink/40">Tidak ada data pengiriman.</p>
        </div>
    @else

        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-lake-900/8 text-left">
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">No. Pesanan</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">UMKM</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Pembeli</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Alamat Tujuan</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Total</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Kurir</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3">Status</th>
                        <th class="font-mono text-[10px] uppercase tracking-widest text-ink/30 px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lake-900/5">
                    @foreach($pengiriman as $pg)
                    @php
                        $kode  = $pg->status->kode ?? '';
                        $badge = match($kode) {
                            'menunggu_kurir' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'dijemput'       => 'bg-blue-50 text-blue-700 border-blue-200',
                            'diantar'        => 'bg-purple-50 text-purple-700 border-purple-200',
                            'selesai'        => 'bg-green-50 text-green-700 border-green-200',
                            default          => 'bg-gray-50 text-gray-600 border-gray-200',
                        };
                        $bisaClaim = $kode === 'menunggu_kurir' && is_null($pg->courier_id);
                        $milikSaya = $pg->courier_id === auth()->id();
                        $nextMap = [
                            'dijemput' => ['kode' => 'diantar', 'label' => 'Tandai Diantar'],
                            'diantar'  => ['kode' => 'selesai', 'label' => 'Konfirmasi Selesai'],
                        ];
                        $next = ($milikSaya && isset($nextMap[$kode])) ? $nextMap[$kode] : null;
                    @endphp
                    <tr>
                        <td class="px-6 py-3 font-mono text-xs text-ink/70 whitespace-nowrap">
                            {{ $pg->pesanan->no_pesanan }}
                        </td>
                        <td class="px-6 py-3 text-ink/80">{{ $pg->pesanan->umkm->nama_umkm ?? '—' }}</td>
                        <td class="px-6 py-3 text-ink/70">{{ $pg->pesanan->customer->nama ?? '—' }}</td>
                        <td class="px-6 py-3 text-ink/60 max-w-48">
                            <p class="text-xs truncate">{{ $pg->pesanan->alamat->alamat_lengkap ?? '—' }}</p>
                            <p class="text-xs text-ink/40">{{ $pg->pesanan->alamat->kota ?? '' }}</p>
                        </td>
                        <td class="px-6 py-3 font-semibold text-lake-900 whitespace-nowrap">
                            Rp{{ number_format($pg->pesanan->total_harga, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-3 text-xs text-ink/60">
                            @if($pg->kurir)
                                <span class="{{ $milikSaya ? 'text-lake-900 font-medium' : '' }}">
                                    {{ $pg->kurir->nama }}{{ $milikSaya ? ' (Saya)' : '' }}
                                </span>
                            @else
                                <span class="text-ink/30">Belum ada</span>
                            @endif
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full border {{ $badge }}">
                                {{ $pg->status->label ?? '—' }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($bisaClaim)
                                    <form action="{{ route('courier.pengiriman.claim', $pg) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                                class="font-mono text-xs text-green-700 hover:underline"
                                                onclick="return confirm('Ambil tugas pengiriman ini?')">
                                            Ambil Tugas
                                        </button>
                                    </form>
                                @elseif($next)
                                    @if($next['kode'] === 'selesai')
                                        <button type="button"
                                                @click="$dispatch('buka-modal-selesai', { id: {{ $pg->id }}, action: '{{ route('courier.pengiriman.status', $pg) }}' })"
                                                class="font-mono text-xs text-lake-800 hover:underline whitespace-nowrap">
                                            Konfirmasi Selesai
                                        </button>
                                    @else
                                        <form action="{{ route('courier.pengiriman.status', $pg) }}" method="POST" class="flex items-center gap-1.5">
                                            @csrf
                                            <input type="hidden" name="_method" value="PATCH">
                                            <input type="hidden" name="status_kode" value="{{ $next['kode'] }}">
                                            <button type="submit"
                                                    class="font-mono text-xs text-lake-800 hover:underline whitespace-nowrap">
                                                {{ $next['label'] }}
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-xs text-ink/20">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="md:hidden divide-y divide-lake-900/5">
            @foreach($pengiriman as $pg)
            @php
                $kode  = $pg->status->kode ?? '';
                $badge = match($kode) {
                    'menunggu_kurir' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                    'dijemput'       => 'bg-blue-50 text-blue-700 border-blue-200',
                    'diantar'        => 'bg-purple-50 text-purple-700 border-purple-200',
                    'selesai'        => 'bg-green-50 text-green-700 border-green-200',
                    default          => 'bg-gray-50 text-gray-600 border-gray-200',
                };
                $bisaClaim = $kode === 'menunggu_kurir' && is_null($pg->courier_id);
                $milikSaya = $pg->courier_id === auth()->id();
                $nextMap = [
                    'dijemput' => ['kode' => 'diantar', 'label' => 'Tandai Diantar'],
                    'diantar'  => ['kode' => 'selesai', 'label' => 'Konfirmasi Selesai'],
                ];
                $next = ($milikSaya && isset($nextMap[$kode])) ? $nextMap[$kode] : null;
            @endphp
            <div class="px-5 py-4">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <div>
                        <p class="font-mono text-xs font-semibold text-ink">{{ $pg->pesanan->no_pesanan }}</p>
                        <p class="text-xs text-ink/40 mt-0.5">{{ $pg->pesanan->umkm->nama_umkm ?? '—' }}</p>
                    </div>
                    <span class="inline-block text-xs font-medium px-2 py-0.5 rounded-full border {{ $badge }} flex-shrink-0">
                        {{ $pg->status->label ?? '—' }}
                    </span>
                </div>
                <p class="text-xs text-ink/60 mb-0.5">Pembeli: {{ $pg->pesanan->customer->nama ?? '—' }}</p>
                <p class="text-xs text-ink/50 mb-2 truncate">{{ $pg->pesanan->alamat->alamat_lengkap ?? '—' }}, {{ $pg->pesanan->alamat->kota ?? '' }}</p>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-bold text-lake-900">Rp{{ number_format($pg->pesanan->total_harga, 0, ',', '.') }}</p>
                        <p class="text-xs text-ink/40">
                            {{ $pg->kurir ? $pg->kurir->nama . ($milikSaya ? ' (Saya)' : '') : 'Belum ada kurir' }}
                        </p>
                    </div>
                    @if($bisaClaim)
                        <form action="{{ route('courier.pengiriman.claim', $pg) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="px-3 py-1.5 bg-lake-900 text-paper text-xs font-medium rounded-lg"
                                    onclick="return confirm('Ambil tugas ini?')">
                                Ambil Tugas
                            </button>
                        </form>
                    @elseif($next)
                        @if($next['kode'] === 'selesai')
                            <button type="button"
                                    @click="$dispatch('buka-modal-selesai', { id: {{ $pg->id }}, action: '{{ route('courier.pengiriman.status', $pg) }}' })"
                                    class="px-3 py-1.5 bg-lake-900 text-paper text-xs font-medium rounded-lg">
                                Konfirmasi Selesai
                            </button>
                        @else
                            <form action="{{ route('courier.pengiriman.status', $pg) }}" method="POST">
                                @csrf
                                <input type="hidden" name="_method" value="PATCH">
                                <input type="hidden" name="status_kode" value="{{ $next['kode'] }}">
                                <button type="submit"
                                        class="px-3 py-1.5 bg-lake-900 text-paper text-xs font-medium rounded-lg">
                                    {{ $next['label'] }}
                                </button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-6 py-4 border-t border-lake-900/8">
            {{ $pengiriman->links() }}
        </div>
    @endif
</div>

{{-- Modal Konfirmasi Selesai --}}
<div x-data="{
        open: false,
        namaP: '',
        relasiP: '',
        fotoPreview: null,
        fotoFile: null,
        cameraReady: false,
        stream: null,

        buka(id, action) {
            this.namaP = '';
            this.relasiP = '';
            this.fotoPreview = null;
            this.fotoFile = null;
            this.cameraReady = false;
            this.stream = null;
            this.open = true;
            this.$nextTick(() => {
                this.$refs.formSelesai.action = action;
                this.startCamera();
            });
        },

        async startCamera() {
            try {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                this.$refs.video.srcObject = this.stream;
                this.cameraReady = true;
            } catch (e) {
                console.warn('Kamera tidak tersedia:', e);
                this.cameraReady = false;
            }
        },

        stopCamera() {
            if (this.stream) {
                this.stream.getTracks().forEach(t => t.stop());
                this.stream = null;
            }
            this.cameraReady = false;
        },

        capture() {
            const video = this.$refs.video;
            const canvas = this.$refs.canvas;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            this.fotoPreview = canvas.toDataURL('image/jpeg', 0.85);
            this.stopCamera();
            canvas.toBlob(blob => {
                this.fotoFile = new File([blob], 'bukti-serahterima.jpg', { type: 'image/jpeg' });
            }, 'image/jpeg', 0.85);
        },

        handleFoto(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.fotoFile = file;
            const reader = new FileReader();
            reader.onload = ev => this.fotoPreview = ev.target.result;
            reader.readAsDataURL(file);
        },

        async submit() {
            if (!this.namaP.trim()) { alert('Nama penerima wajib diisi.'); return; }
            if (!this.fotoFile)     { alert('Foto bukti serah terima wajib diambil.'); return; }
            const form = this.$refs.formSelesai;
            const fd = new FormData(form);
            fd.append('foto_bukti', this.fotoFile, 'bukti-serahterima.jpg');
            const res = await fetch(form.action, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.redirected) window.location.href = res.url;
        },

        closedModal() {
            this.stopCamera();
            this.open = false;
        }
     }"
     @buka-modal-selesai.window="buka($event.detail.id, $event.detail.action)"
     x-show="open"
     x-cloak
     class="fixed inset-0 z-50 flex items-end md:items-center justify-center bg-black/40 p-0 md:p-4">

    <div @click.outside="closedModal()"
         class="bg-paper rounded-t-2xl md:rounded-xl w-full max-w-md p-6 max-h-[90vh] overflow-y-auto">

        <h4 class="font-display text-base font-medium text-lake-900 mb-1">Konfirmasi Serah Terima</h4>
        <p class="text-xs text-ink/40 mb-5">Isi data penerima dan ambil foto sebagai bukti pengiriman selesai.</p>

        <form x-ref="formSelesai"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-4">
            @csrf
            <input type="hidden" name="_method" value="PATCH">
            <input type="hidden" name="status_kode" value="selesai">

            {{-- Nama Penerima --}}
            <div>
                <label class="text-xs text-ink/60 mb-1 block">
                    Nama Penerima <span class="text-ulos-maroon">*</span>
                </label>
                <input type="text" name="nama_penerima" x-model="namaP"
                       placeholder="Nama orang yang menerima paket"
                       class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-lake-800">
            </div>

            {{-- Relasi --}}
            <div>
                <label class="text-xs text-ink/60 mb-1 block">
                    Relasi / Jabatan <span class="text-ink/30">(opsional)</span>
                </label>
                <input type="text" name="relasi_penerima" x-model="relasiP"
                       placeholder="Contoh: Penerima sendiri, Istri, Satpam..."
                       class="w-full text-sm border border-lake-900/15 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-lake-800">
            </div>

            {{-- Foto Bukti --}}
            <div>
                <label class="text-xs text-ink/60 mb-2 block">
                    Foto Bukti Serah Terima <span class="text-ulos-maroon">*</span>
                </label>

                {{-- Preview hasil capture --}}
                <div x-show="fotoPreview" class="mb-3">
                    <img :src="fotoPreview"
                         class="w-full max-h-56 object-cover rounded-lg border border-lake-900/10">
                    <button type="button"
                            @click="fotoPreview = null; fotoFile = null; startCamera();"
                            class="mt-1.5 text-xs text-ink/40 hover:text-ulos-maroon">
                        Ambil ulang
                    </button>
                </div>

                {{-- Live camera --}}
                <div x-show="!fotoPreview" class="space-y-2">
                    <div class="relative w-full rounded-lg overflow-hidden bg-black aspect-video">
                        <video x-ref="video" autoplay playsinline
                               class="w-full h-full object-cover"></video>
                        <div x-show="!cameraReady"
                             class="absolute inset-0 flex items-center justify-center">
                            <p class="text-xs text-white/60">Memuat kamera...</p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="button" @click="startCamera()"
                                x-show="!cameraReady"
                                class="flex-1 px-3 py-2 bg-lake-900 text-paper text-xs font-medium rounded-lg">
                            Buka Kamera
                        </button>
                        <button type="button" @click="capture()"
                                x-show="cameraReady"
                                class="flex-1 px-3 py-2 bg-lake-900 text-paper text-xs font-medium rounded-lg">
                            📸 Ambil Foto
                        </button>
                    </div>
                    <label class="flex items-center justify-center gap-1.5 w-full py-2 border border-dashed border-lake-900/20 rounded-lg cursor-pointer hover:bg-lake-50 transition-colors">
                        <span class="text-xs text-ink/40">atau pilih dari galeri</span>
                        <input type="file" accept="image/*" class="hidden"
                               @change="handleFoto($event)">
                    </label>
                </div>

                <canvas x-ref="canvas" class="hidden"></canvas>
            </div>

            <div class="flex gap-2 pt-1">
                <button type="button" @click="closedModal()"
                        class="flex-1 px-4 py-2.5 border border-lake-900/15 text-ink/60 text-sm rounded-lg hover:bg-lake-50">
                    Batal
                </button>
                <button type="button" @click="submit()"
                        class="flex-1 px-4 py-2.5 bg-lake-900 text-paper text-sm font-medium rounded-lg hover:bg-lake-900/90">
                    Konfirmasi Selesai
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
