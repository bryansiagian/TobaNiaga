@extends('layouts.guest')

@section('title', 'Profil Saya — TobaNiaga')

@section('content')
<div class="max-w-3xl mx-auto py-10 px-4" x-data="{ tab: 'profil' }">

    <h1 class="font-display text-2xl font-semibold text-ink mb-6">Profil Saya</h1>

    {{-- Tab navigation --}}
    <div class="flex gap-1 bg-lake-50 rounded-xl p-1 mb-8 border border-lake-100">
        @foreach([['profil','Profil'],['email','Email'],['password','Password'],['alamat','Alamat']] as [$key,$label])
        <button @click="tab = '{{ $key }}'"
                :class="tab === '{{ $key }}' ? 'bg-white shadow-sm text-lake-900 font-semibold' : 'text-ink/50 hover:text-ink'"
                class="flex-1 text-sm py-2 px-3 rounded-lg transition-all">
            {{ $label }}
        </button>
        @endforeach
    </div>

    {{-- ── Tab Profil ── --}}
    <div x-show="tab === 'profil'" x-cloak>
        @if(session('status_profil'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status_profil') }}
        </div>
        @endif

        <div class="bg-white rounded-xl border border-lake-100 p-6">
            <form action="{{ route('customer.profil.update') }}" method="POST"
                  enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Foto profil --}}
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-lake-50 border border-lake-100 flex-shrink-0">
                        @if($user->foto_profil)
                            <img src="{{ Storage::url($user->foto_profil) }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-xl font-bold text-lake-900/30">
                                {{ Str::upper(Str::substr($user->nama, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-ink mb-1">Foto Profil</p>
                        <input type="file" name="foto_profil" accept="image/*"
                               class="text-xs text-ink/60 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-lake-50 file:text-lake-800 hover:file:bg-lake-100">
                        <p class="text-xs text-gray-400 mt-1">JPG/PNG, maks. 2MB</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="{{ old('nama', $user->nama) }}"
                           class="w-full border border-lake-100 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                    @error('nama')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Nomor HP / WA</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                           class="w-full border border-lake-100 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    @error('no_hp')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir"
                           value="{{ old('tanggal_lahir', $user->tanggal_lahir?->format('Y-m-d')) }}"
                           class="w-full border border-lake-100 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    @error('tanggal_lahir')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-lake-900 text-white text-sm font-semibold rounded-lg hover:bg-lake-800">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tab Email ── --}}
    <div x-show="tab === 'email'" x-cloak>
        @if(session('status_email'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status_email') }}
        </div>
        @endif

        <div class="bg-white rounded-xl border border-lake-100 p-6 space-y-6">

            {{-- Info email sekarang --}}
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Email Saat Ini</p>
                <p class="text-sm font-medium text-ink">{{ $user->email }}</p>
            </div>

            {{-- Step 1: minta OTP --}}
            <div class="border-t border-lake-100 pt-5">
                <p class="text-sm font-semibold text-ink mb-3">Langkah 1 — Masukkan email baru</p>
                <form action="{{ route('customer.profil.email.otp') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="email" name="email_baru"
                           placeholder="emailbaru@contoh.com"
                           value="{{ old('email_baru') }}"
                           class="flex-1 border border-lake-100 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                    <button type="submit"
                            class="px-4 py-2.5 bg-lake-900 text-white text-sm font-semibold rounded-lg hover:bg-lake-800 whitespace-nowrap">
                        Kirim OTP
                    </button>
                </form>
                @error('email_baru')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Step 2: verifikasi OTP --}}
            <div class="border-t border-lake-100 pt-5">
                <p class="text-sm font-semibold text-ink mb-3">Langkah 2 — Masukkan kode OTP</p>
                <form action="{{ route('customer.profil.email.verifikasi') }}" method="POST" class="flex gap-2">
                    @csrf
                    <input type="text" name="otp" maxlength="6"
                           placeholder="6 digit kode OTP"
                           class="flex-1 border border-lake-100 rounded-lg px-3.5 py-2.5 text-sm text-ink font-mono tracking-widest focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                    <button type="submit"
                            class="px-4 py-2.5 bg-ulos-maroon text-white text-sm font-semibold rounded-lg hover:opacity-90 whitespace-nowrap">
                        Verifikasi
                    </button>
                </form>
                @error('otp')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    {{-- ── Tab Password ── --}}
    <div x-show="tab === 'password'" x-cloak>
        @if(session('status_password'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status_password') }}
        </div>
        @endif

        <div class="bg-white rounded-xl border border-lake-100 p-6">
            <form action="{{ route('customer.profil.password') }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Password Lama</label>
                    <input type="password" name="password_lama"
                           class="w-full border border-lake-100 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                    @error('password_lama')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Password Baru</label>
                    <input type="password" name="password_baru"
                           class="w-full border border-lake-100 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                    @error('password_baru')<p class="text-xs text-ulos-maroon mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_baru_confirmation"
                           class="w-full border border-lake-100 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                           required>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="px-6 py-2.5 bg-lake-900 text-white text-sm font-semibold rounded-lg hover:bg-lake-800">
                        Ganti Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Tab Alamat ── --}}
    <div x-show="tab === 'alamat'" x-cloak
         x-data="{
             modalTambah: false,
             modalEdit: false,
             editData: {},
             openEdit(data) { this.editData = { ...data }; this.modalEdit = true; }
         }">

        @if(session('status_alamat'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status_alamat') }}
        </div>
        @endif

        <div class="flex justify-between items-center mb-4">
            <p class="text-sm text-ink/60">{{ $alamat->count() }} alamat tersimpan</p>
            <button @click="modalTambah = true"
                    class="px-4 py-2 bg-lake-900 text-white text-sm font-semibold rounded-lg hover:bg-lake-800">
                + Tambah Alamat
            </button>
        </div>

        {{-- Daftar alamat --}}
        @if($alamat->isEmpty())
        <div class="bg-white rounded-xl border border-lake-100 p-10 text-center">
            <p class="text-sm text-gray-400">Belum ada alamat tersimpan.</p>
        </div>
        @else
        <div class="space-y-3">
            @foreach($alamat as $a)
            <div class="bg-white rounded-xl border {{ $a->is_utama ? 'border-lake-400' : 'border-lake-100' }} p-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-sm font-semibold text-ink">{{ $a->label }}</p>
                            @if($a->is_utama)
                            <span class="inline-block text-[10px] font-medium px-2 py-0.5 rounded-full bg-lake-50 text-lake-800 border border-lake-200">
                                Utama
                            </span>
                            @endif
                        </div>
                        <p class="text-sm text-ink/70">{{ $a->nama_penerima }} · {{ $a->no_hp_penerima }}</p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $a->alamat_lengkap }}, {{ $a->kelurahan }}, {{ $a->kecamatan }}, {{ $a->kota }}, {{ $a->provinsi }}
                            @if($a->kode_pos) {{ $a->kode_pos }}@endif
                        </p>
                    </div>
                    <div class="flex flex-col gap-1.5 flex-shrink-0">
                        <button @click="openEdit({
                            id: {{ $a->id }},
                            label: '{{ addslashes($a->label) }}',
                            nama_penerima: '{{ addslashes($a->nama_penerima) }}',
                            no_hp_penerima: '{{ addslashes($a->no_hp_penerima) }}',
                            provinsi: '{{ addslashes($a->provinsi) }}',
                            kota: '{{ addslashes($a->kota) }}',
                            kecamatan: '{{ addslashes($a->kecamatan) }}',
                            kelurahan: '{{ addslashes($a->kelurahan) }}',
                            kode_pos: '{{ addslashes($a->kode_pos) }}',
                            alamat_lengkap: '{{ addslashes($a->alamat_lengkap) }}'
                        })"
                        class="text-xs text-lake-800 hover:underline font-mono">Edit</button>

                        @if(!$a->is_utama)
                        <form action="{{ route('customer.profil.alamat.utama', $a) }}" method="POST">
                            @csrf
                            <input type="hidden" name="_method" value="PATCH">
                            <button type="submit" class="text-xs text-ink/40 hover:text-ink font-mono">Utamakan</button>
                        </form>
                        <form action="{{ route('customer.alamat.destroy', $a) }}" method="POST"
                              onsubmit="return confirm('Hapus alamat ini?')">
                            @csrf
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="text-xs text-ulos-maroon hover:underline font-mono">Hapus</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- Modal Tambah Alamat --}}
        <div x-show="modalTambah" x-cloak
             class="fixed inset-0 z-40 flex items-start justify-center bg-ink/30 px-4 py-8 overflow-y-auto">
            <div @click.outside="modalTambah = false"
                 class="bg-white rounded-xl shadow-xl border border-lake-100 w-full max-w-lg p-6 my-auto">
                <h4 class="font-display text-base font-medium text-ink mb-4">Tambah Alamat</h4>
                <form action="{{ route('customer.alamat.store') }}" method="POST" class="space-y-3">
                    @csrf

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Label</label>
                            <input type="text" name="label" placeholder="Rumah, Kantor..."
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Nama Penerima</label>
                            <input type="text" name="nama_penerima"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">No. HP Penerima</label>
                        <input type="text" name="no_hp_penerima"
                               class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                               required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Provinsi</label>
                            <input type="text" name="provinsi"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Kota / Kabupaten</label>
                            <input type="text" name="kota"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Kecamatan</label>
                            <input type="text" name="kecamatan"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Kelurahan / Desa</label>
                            <input type="text" name="kelurahan"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Kode Pos</label>
                        <input type="text" name="kode_pos"
                               class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" rows="2"
                                  placeholder="Nama jalan, nomor rumah, RT/RW..."
                                  class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20 resize-none"
                                  required></textarea>
                    </div>

                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_utama" value="1" class="w-4 h-4 rounded accent-lake-900">
                        <span class="text-sm text-gray-600">Jadikan alamat utama</span>
                    </label>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="modalTambah = false"
                                class="px-4 py-2 text-sm text-ink/60">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 bg-lake-900 text-white text-sm font-semibold rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Modal Edit Alamat --}}
        <div x-show="modalEdit" x-cloak
             class="fixed inset-0 z-40 flex items-start justify-center bg-ink/30 px-4 py-8 overflow-y-auto">
            <div @click.outside="modalEdit = false"
                 class="bg-white rounded-xl shadow-xl border border-lake-100 w-full max-w-lg p-6 my-auto">
                <h4 class="font-display text-base font-medium text-ink mb-4">Edit Alamat</h4>
                <form :action="`/customer/profil/alamat/${editData.id}`" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Label</label>
                            <input type="text" name="label" x-bind:value="editData.label"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Nama Penerima</label>
                            <input type="text" name="nama_penerima" x-bind:value="editData.nama_penerima"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">No. HP Penerima</label>
                        <input type="text" name="no_hp_penerima" x-bind:value="editData.no_hp_penerima"
                               class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                               required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Provinsi</label>
                            <input type="text" name="provinsi" x-bind:value="editData.provinsi"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Kota / Kabupaten</label>
                            <input type="text" name="kota" x-bind:value="editData.kota"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Kecamatan</label>
                            <input type="text" name="kecamatan" x-bind:value="editData.kecamatan"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Kelurahan / Desa</label>
                            <input type="text" name="kelurahan" x-bind:value="editData.kelurahan"
                                   class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Kode Pos</label>
                        <input type="text" name="kode_pos" x-bind:value="editData.kode_pos"
                               class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">Alamat Lengkap</label>
                        <textarea name="alamat_lengkap" rows="2" x-bind:value="editData.alamat_lengkap"
                                  placeholder="Nama jalan, nomor rumah, RT/RW..."
                                  class="w-full border border-lake-100 rounded-lg px-3 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20 resize-none"
                                  required></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="modalEdit = false"
                                class="px-4 py-2 text-sm text-ink/60">Batal</button>
                        <button type="submit"
                                class="px-4 py-2 bg-lake-900 text-white text-sm font-semibold rounded-lg">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
