@extends('layouts.backoffice')

@section('title', 'Kelola Pengguna')
@section('role_label', 'Administrator')
@section('page_title', 'Kelola Pengguna')

@section('content')

    <div class="mb-8">
        <p class="font-mono text-xs text-ink/40 uppercase tracking-widest mb-1">Manajemen</p>
        <h2 class="font-display text-2xl font-medium text-lake-900">Kelola Pengguna</h2>
    </div>

    @if (session('status'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-lake-50 border border-lake-400/20 text-sm text-lake-800">
            {{ session('status') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-5 px-4 py-3 rounded-lg bg-ulos-maroon/5 border border-ulos-maroon/20 text-sm text-ulos-maroon">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-paper border border-lake-900/10 rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b border-lake-900/8">
            <h3 class="font-display text-base font-medium text-lake-900">Daftar Pengguna</h3>
        </div>

        @php
            $nonAdminUsers = $users->getCollection()->reject(fn($u) => $u->hasRole('admin'));
        @endphp

        @if ($nonAdminUsers->isEmpty())
            <div class="px-6 py-14 text-center">
                <p class="text-sm text-ink/40">Tidak ada pengguna untuk ditampilkan.</p>
            </div>
        @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-lake-900/8 text-left">
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Nama</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Email</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Role</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3">Status</th>
                        <th class="font-mono text-xs text-ink/40 uppercase tracking-widest px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-lake-900/6">
                    @foreach ($nonAdminUsers as $user)
                        <tr x-data="{ suspendId: null }">
                            <td class="px-6 py-3.5 text-ink/80 font-medium">{{ $user->nama }}</td>
                            <td class="px-6 py-3.5 text-ink/60">{{ $user->email }}</td>
                            <td class="px-6 py-3.5 text-ink/60 capitalize">{{ $user->roles->pluck('name')->join(', ') }}</td>
                            <td class="px-6 py-3.5">
                                @if ($user->status?->kode === 'aktif')
                                    <span class="font-mono text-[10px] bg-lake-50 text-lake-800 px-2 py-0.5 rounded-full uppercase tracking-wider">Aktif</span>
                                @else
                                    <span class="font-mono text-[10px] bg-ulos-maroon/10 text-ulos-maroon px-2 py-0.5 rounded-full uppercase tracking-wider">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 text-right whitespace-nowrap">
                                @if ($user->status?->kode === 'nonaktif')
                                    <form action="{{ route('admin.users.aktivasi', $user->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Aktifkan kembali akun &quot;{{ $user->nama }}&quot;?')">
                                        @csrf
                                        <button type="submit" class="font-mono text-xs text-lake-800 hover:underline mr-3">Aktifkan</button>
                                    </form>
                                @else
                                    <button @click="suspendId = {{ $user->id }}"
                                            class="font-mono text-xs text-ulos-maroon hover:underline mr-3">Suspend</button>
                                @endif

                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Hapus akun &quot;{{ $user->nama }}&quot;? Tindakan ini tidak bisa dibatalkan.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-mono text-xs text-ink/30 hover:text-ulos-maroon hover:underline">Hapus</button>
                                </form>

                                {{-- Modal suspend --}}
                                <div x-show="suspendId === {{ $user->id }}" x-cloak
                                    class="fixed inset-0 z-40 flex items-center justify-center bg-ink/30 px-4">
                                    <div @click.outside="suspendId = null"
                                        class="bg-paper rounded-xl shadow-xl border border-lake-900/10 w-full max-w-sm p-6">
                                        <h4 class="font-display text-base font-medium text-lake-900 mb-1">Suspend Akun</h4>
                                        <p class="text-sm text-ink/60 mb-4">{{ $user->nama }}</p>
                                        <form action="{{ route('admin.users.suspend', $user->id) }}" method="POST"
                                            x-data="{ pilihan: '' }">
                                            @csrf
                                            <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Alasan Suspend</label>
                                            <select name="alasan_pilihan" x-model="pilihan" required
                                                    class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink bg-paper focus:outline-none focus:ring-2 focus:ring-lake-900/20 mb-3">
                                                <option value="">Pilih alasan...</option>
                                                <option>Pelanggaran ketentuan layanan</option>
                                                <option>Aktivitas mencurigakan / penipuan</option>
                                                <option>Laporan dari pengguna lain</option>
                                                <option>Data akun tidak valid</option>
                                                <option>Permintaan pengguna sendiri</option>
                                                <option>Lainnya</option>
                                            </select>
                                            <div x-show="pilihan === 'Lainnya'" x-cloak>
                                                <label class="block font-mono text-[10px] uppercase tracking-widest text-ink/40 mb-1.5">Tulis Alasan</label>
                                                <textarea name="alasan_manual" rows="3"
                                                        class="w-full border border-lake-900/15 rounded-lg px-3.5 py-2.5 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-lake-900/20 mb-3"
                                                        placeholder="Jelaskan alasan suspend..."></textarea>
                                            </div>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="suspendId = null"
                                                        class="px-4 py-2 text-sm text-ink/60 hover:text-ink transition-colors">Batal</button>
                                                <button type="submit"
                                                        class="px-4 py-2 bg-ulos-maroon text-paper text-sm font-medium rounded-lg hover:bg-ulos-maroon/90 transition-colors">Suspend</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="px-6 py-4 border-t border-lake-900/8">
                {{ $users->links() }}
            </div>
        @endif
    </div>

@endsection
