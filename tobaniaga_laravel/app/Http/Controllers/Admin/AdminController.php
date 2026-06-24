<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Umkm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        $stats = [
            'total_user'   => User::whereDoesntHave('roles', fn($q) => $q->where('name', 'admin'))->count(),
            'total_umkm'   => Umkm::whereHas('statusVerifikasi', fn($q) => $q->where('kode', 'verified'))->count(),
            'pending_umkm' => Umkm::whereHas('statusVerifikasi', fn($q) => $q->where('kode', 'pending'))->count(),
            'total_pesanan' => DB::table('pesanan')->count(),
        ];

        $perRole = [
            'Customer' => User::role('customer')->count(),
            'Sales'    => User::role('sales')->count(),
            'Courier'  => User::role('courier')->count(),
            'Admin'    => User::role('admin')->count(),
        ];

        $umkmPending = Umkm::with(['owner', 'kategori'])
            ->whereHas('statusVerifikasi', fn($q) => $q->where('kode', 'pending'))
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'perRole', 'umkmPending'));
    }

    // ── Kelola UMKM ────────────────────────────────────────────

    /**
     * Daftar semua UMKM verified.
     */
    public function umkmIndex(Request $request): View
    {
        $kategoriList = \App\Models\KategoriUmkm::orderBy('nama')->get();

        $umkm = Umkm::with(['owner', 'kategori', 'statusVerifikasi'])
            ->whereHas('statusVerifikasi', fn($q) => $q->where('kode', 'verified'))
            ->when($request->filled('kategori'), fn($q) => $q->where('kategori_id', $request->kategori))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.umkm.index', compact('umkm', 'kategoriList'));
    }

    /**
     * Daftar semua UMKM pending verifikasi.
     */
    public function umkmPending(): View
    {
        $umkm = Umkm::with(['owner', 'kategori', 'statusVerifikasi'])
            ->whereHas('statusVerifikasi', fn($q) => $q->where('kode', 'pending'))
            ->latest()
            ->paginate(15);

        return view('admin.umkm.pending', compact('umkm'));
    }

    /**
     * Detail UMKM untuk ditinjau admin.
     */
    public function umkmDetail(Umkm $umkm): View
    {
        $umkm->load(['owner', 'kategori', 'statusVerifikasi', 'statusUmkm']);
        return view('admin.umkm.detail', compact('umkm'));
    }

    /**
     * Setujui UMKM — aktifkan user sales & ubah status verifikasi.
     */
    public function umkmApprove(Umkm $umkm): RedirectResponse
    {
        $statusVerifVerifiedId = DB::table('status_verifikasi_umkm')->where('kode', 'verified')->value('id');
        $statusAktifId         = DB::table('status_user')->where('kode', 'aktif')->value('id');

        DB::transaction(function () use ($umkm, $statusVerifVerifiedId, $statusAktifId) {
            $umkm->update(['status_verifikasi_id' => $statusVerifVerifiedId]);
            $umkm->owner->update(['status_id' => $statusAktifId]);
        });

        // Kirim notifikasi email ke sales
        Mail::to($umkm->owner->email)->send(
            new \App\Mail\NotifikasiApprovalUmkm($umkm, 'approved')
        );

        return redirect()
            ->route('admin.umkm.pending')
            ->with('status', "UMKM \"{$umkm->nama_umkm}\" berhasil disetujui.");
    }

    /**
     * Tolak UMKM — simpan alasan penolakan.
     */
    public function umkmReject(Request $request, Umkm $umkm): RedirectResponse
    {
        $request->validate([
            'alasan' => ['required', 'string', 'max:500'],
        ], [
            'alasan.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $statusVerifRejectedId = DB::table('status_verifikasi_umkm')->where('kode', 'rejected')->value('id');

        $umkm->update([
            'status_verifikasi_id' => $statusVerifRejectedId,
            'catatan_penolakan'    => $request->alasan, // kolom opsional, lihat catatan di bawah
        ]);

        // Kirim notifikasi email ke sales
        Mail::to($umkm->owner->email)->send(
            new \App\Mail\NotifikasiApprovalUmkm($umkm, 'rejected', $request->alasan)
        );

        return redirect()
            ->route('admin.umkm.pending')
            ->with('status', "UMKM \"{$umkm->nama_umkm}\" ditolak.");
    }

    /**
     * Daftar UMKM yang ditolak.
     */
    public function umkmRejected(): View
    {
        $umkm = Umkm::with(['owner', 'kategori', 'statusVerifikasi'])
            ->whereHas('statusVerifikasi', fn($q) => $q->where('kode', 'rejected'))
            ->latest()
            ->paginate(15);

        return view('admin.umkm.rejected', compact('umkm'));
    }

    /**
     * Aktifkan kembali UMKM yang ditolak.
     */
    public function umkmReactivate(Umkm $umkm): RedirectResponse
    {
        $statusVerifVerifiedId = DB::table('status_verifikasi_umkm')->where('kode', 'verified')->value('id');
        $statusAktifId         = DB::table('status_user')->where('kode', 'aktif')->value('id');

        DB::transaction(function () use ($umkm, $statusVerifVerifiedId, $statusAktifId) {
            $umkm->update(['status_verifikasi_id' => $statusVerifVerifiedId]);
            $umkm->owner->update(['status_id' => $statusAktifId]);
        });

        Mail::to($umkm->owner->email)->send(
            new \App\Mail\NotifikasiRektivasiUmkm($umkm)
        );

        return redirect()
            ->route('admin.umkm.rejected')
            ->with('status', "UMKM \"{$umkm->nama_umkm}\" berhasil diaktifkan kembali.");
    }

    // ── Kelola User ────────────────────────────────────────────

    public function users(): View
    {
        $users = User::with(['status', 'roles'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Suspend user — simpan alasan & kirim email.
     */
    public function suspendUser(Request $request, User $user): RedirectResponse
    {
        if ($user->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'alasan_pilihan' => ['required', 'string'],
            'alasan_manual'  => ['nullable', 'string', 'max:500', 'required_if:alasan_pilihan,Lainnya'],
        ], [
            'alasan_pilihan.required' => 'Pilih alasan suspend.',
            'alasan_manual.required_if' => 'Isi alasan jika memilih Lainnya.',
        ]);

        $alasan = $request->alasan_pilihan === 'Lainnya'
            ? $request->alasan_manual
            : $request->alasan_pilihan;

        $statusNonaktifId = DB::table('status_user')->where('kode', 'nonaktif')->value('id');

        $user->update(['status_id' => $statusNonaktifId]);

        Mail::to($user->email)->send(
            new \App\Mail\NotifikasiSuspendUser($user, $alasan)
        );

        return back()->with('status', "Akun \"{$user->nama}\" berhasil disuspend.");
    }

    /**
     * Aktifkan kembali user yang disuspend — kirim email reaktivasi.
     */
    public function aktivasiUser(User $user): RedirectResponse
    {
        if ($user->hasRole('admin')) {
            abort(403);
        }

        $statusAktifId = DB::table('status_user')->where('kode', 'aktif')->value('id');

        $user->update(['status_id' => $statusAktifId]);

        Mail::to($user->email)->send(
            new \App\Mail\NotifikasiAktivasiUser($user)
        );

        return back()->with('status', "Akun \"{$user->nama}\" berhasil diaktifkan kembali.");
    }

    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->hasRole('admin')) {
            abort(403);
        }

        try {
            $user->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', "Pengguna \"{$user->nama}\" tidak bisa dihapus karena masih memiliki data terkait (UMKM, pesanan, dll).");
        }

        return redirect()->route('admin.users.index')->with('status', "Pengguna \"{$user->nama}\" berhasil dihapus.");
    }
}
