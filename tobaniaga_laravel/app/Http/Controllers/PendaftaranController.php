<?php

namespace App\Http\Controllers;

use App\Models\KategoriUmkm;
use App\Models\StatusUmkm;
use App\Models\StatusVerifikasiDokumen;
use App\Models\StatusVerifikasiUmkm;
use App\Models\Umkm;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    // ── SALES ────────────────────────────────────────────────

    public function formSales(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole('sales') || $user->hasRole('admin') || $user->hasRole('courier')) {
            return redirect()->route('welcome')->with('error', 'Akun ini sudah memiliki role lain.');
        }

        if ($user->status_verifikasi_dokumen_id && $user->statusVerifikasiDokumen?->kode === 'pending') {
            return redirect()->route('welcome')->with('error', 'Pendaftaranmu sedang menunggu verifikasi admin.');
        }

        $kategoriUmkm = KategoriUmkm::orderBy('nama')->get();

        return view('pendaftaran.sales', compact('kategoriUmkm'));
    }

    public function storeSales(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            // Data pribadi
            'nik'            => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'tanggal_lahir'  => ['required', 'date', 'before:-17 years'],
            'no_hp'          => ['required', 'string', 'max:20'],
            'alamat_ktp'     => ['required', 'string', 'max:500'],
            'foto_ktp'       => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'foto_kk'        => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],

            // Data UMKM
            'nama_umkm'      => ['required', 'string', 'max:150'],
            'kategori_id'    => ['required', 'exists:kategori_umkm,id'],
            'deskripsi'      => ['nullable', 'string', 'max:1000'],
            'no_hp_wa'       => ['required', 'string', 'max:20'],
            'provinsi'       => ['required', 'string', 'max:100'],
            'kabupaten'      => ['required', 'string', 'max:100'],
            'kecamatan'      => ['required', 'string', 'max:100'],
            'desa'           => ['required', 'string', 'max:100'],
            'alamat'         => ['required', 'string', 'max:500'],
        ], [
            'tanggal_lahir.before' => 'Pendaftar harus berusia minimal 17 tahun.',
            'nik.size'             => 'NIK harus terdiri dari 16 digit.',
        ]);

        DB::transaction(function () use ($user, $validated, $request) {
            // Simpan file ke private disk
            $pathKtp = $request->file('foto_ktp')->store('dokumen/' . $user->id, 'private');
            $pathKk  = $request->file('foto_kk')->store('dokumen/' . $user->id, 'private');

            $statusPending = StatusVerifikasiDokumen::where('kode', 'pending')->firstOrFail();

            $user->update([
                'nik'                          => $validated['nik'],
                'tanggal_lahir'                => $validated['tanggal_lahir'],
                'no_hp'                        => $validated['no_hp'],
                'alamat_ktp'                   => $validated['alamat_ktp'],
                'foto_ktp'                     => $pathKtp,
                'foto_kk'                      => $pathKk,
                'status_verifikasi_dokumen_id' => $statusPending->id,
                'catatan_penolakan_dokumen'    => null,
            ]);

            $statusUmkmAktif      = StatusUmkm::where('kode', 'aktif')->firstOrFail();
            $statusVerifikasiUmkm = StatusVerifikasiUmkm::where('kode', 'pending')->firstOrFail();

            Umkm::create([
                'owner_id'              => $user->id,
                'kategori_id'           => $validated['kategori_id'],
                'status_id'             => $statusUmkmAktif->id,
                'status_verifikasi_id'  => $statusVerifikasiUmkm->id,
                'nama_umkm'             => $validated['nama_umkm'],
                'slug'                  => Str::slug($validated['nama_umkm']) . '-' . Str::random(5),
                'deskripsi'             => $validated['deskripsi'] ?? null,
                'no_hp_wa'              => $validated['no_hp_wa'],
                'provinsi'              => $validated['provinsi'],
                'kabupaten'             => $validated['kabupaten'],
                'kecamatan'             => $validated['kecamatan'],
                'desa'                  => $validated['desa'],
                'alamat'                => $validated['alamat'],
            ]);

            // Assign role sales — tapi role baru "aktif penuh" setelah admin approve dokumen
            $user->assignRole('sales');
        });

        return redirect()->route('welcome')
            ->with('status', 'Pendaftaran berhasil dikirim! Tokomu dan dokumenmu akan diverifikasi admin dalam 1-2 hari kerja.');
    }

    // ── KURIR ────────────────────────────────────────────────

    public function formKurir(): View|RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasRole('sales') || $user->hasRole('admin') || $user->hasRole('courier')) {
            return redirect()->route('welcome')->with('error', 'Akun ini sudah memiliki role lain.');
        }

        if ($user->status_verifikasi_dokumen_id && $user->statusVerifikasiDokumen?->kode === 'pending') {
            return redirect()->route('welcome')->with('error', 'Pendaftaranmu sedang menunggu verifikasi admin.');
        }

        return view('pendaftaran.kurir');
    }

    public function storeKurir(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nik'           => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'tanggal_lahir' => ['required', 'date', 'before:-17 years'],
            'no_hp'         => ['required', 'string', 'max:20'],
            'alamat_ktp'    => ['required', 'string', 'max:500'],
            'foto_ktp'      => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'foto_kk'       => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'tanggal_lahir.before' => 'Pendaftar harus berusia minimal 17 tahun.',
            'nik.size'             => 'NIK harus terdiri dari 16 digit.',
        ]);

        DB::transaction(function () use ($user, $validated, $request) {
            $pathKtp = $request->file('foto_ktp')->store('dokumen/' . $user->id, 'private');
            $pathKk  = $request->file('foto_kk')->store('dokumen/' . $user->id, 'private');

            $statusPending = StatusVerifikasiDokumen::where('kode', 'pending')->firstOrFail();

            $user->update([
                'nik'                          => $validated['nik'],
                'tanggal_lahir'                => $validated['tanggal_lahir'],
                'no_hp'                        => $validated['no_hp'],
                'alamat_ktp'                   => $validated['alamat_ktp'],
                'foto_ktp'                     => $pathKtp,
                'foto_kk'                      => $pathKk,
                'status_verifikasi_dokumen_id' => $statusPending->id,
                'catatan_penolakan_dokumen'    => null,
            ]);

            $user->assignRole('courier');
        });

        return redirect()->route('welcome')
            ->with('status', 'Pendaftaran berhasil dikirim! Dokumenmu akan diverifikasi admin dalam 1-2 hari kerja.');
    }

    // ── Akses file dokumen (privat, terproteksi) ────────────

    public function lihatDokumen(Request $request, User $user, string $tipe): StreamedResponse
    {
        $pengakses = Auth::user();

        // Hanya pemilik dokumen sendiri atau admin yang boleh akses
        abort_unless(
            $pengakses->id === $user->id || $pengakses->hasRole('admin'),
            403,
            'Kamu tidak punya akses ke dokumen ini.'
        );

        $path = $tipe === 'ktp' ? $user->foto_ktp : $user->foto_kk;

        abort_if(!$path || !Storage::disk('private')->exists($path), 404, 'Dokumen tidak ditemukan.');

        return Storage::disk('private')->response($path);
    }
}
