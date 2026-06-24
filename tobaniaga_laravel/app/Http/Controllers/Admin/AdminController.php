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
            'total_user'      => User::count(),
            'total_umkm'      => Umkm::count(),
            'pending_umkm'    => Umkm::whereHas('statusVerifikasi', fn($q) => $q->where('kode', 'pending'))->count(),
            'total_pesanan'   => DB::table('pesanan')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // ── Kelola UMKM ────────────────────────────────────────────

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

    // ── Kelola User ────────────────────────────────────────────

    public function users(): View
    {
        $users = User::with(['status', 'roles'])
            ->latest()
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Nonaktifkan / aktifkan user.
     */
    public function toggleUserStatus(User $user): RedirectResponse
    {
        $kodeSekarang = $user->status?->kode;
        $kodeBaru     = $kodeSekarang === 'aktif' ? 'nonaktif' : 'aktif';
        $statusBaruId = DB::table('status_user')->where('kode', $kodeBaru)->value('id');

        $user->update(['status_id' => $statusBaruId]);

        return back()->with('status', "Status user \"{$user->nama}\" diubah menjadi {$kodeBaru}.");
    }
}
