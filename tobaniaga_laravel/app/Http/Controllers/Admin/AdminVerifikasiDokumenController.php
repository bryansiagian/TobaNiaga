<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StatusVerifikasiDokumen;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminVerifikasiDokumenController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'pending');

        $users = User::query()
            ->whereHas('statusVerifikasiDokumen')
            ->when($status !== 'semua', function ($q) use ($status) {
                $q->whereHas('statusVerifikasiDokumen', fn($q2) => $q2->where('kode', $status));
            })
            ->with(['statusVerifikasiDokumen', 'roles'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $countPending = User::whereHas('statusVerifikasiDokumen',
            fn($q) => $q->where('kode', 'pending')
        )->count();

        return view('admin.verifikasi-dokumen.index', compact('users', 'status', 'countPending'));
    }

    public function show(User $user): View
    {
        abort_unless($user->statusVerifikasiDokumen !== null, 404);
        $user->load(['statusVerifikasiDokumen', 'roles', 'umkm']);
        return view('admin.verifikasi-dokumen.show', compact('user'));
    }

    public function approve(User $user): RedirectResponse
    {
        $statusVerified = StatusVerifikasiDokumen::where('kode', 'verified')->firstOrFail();

        $user->update([
            'status_verifikasi_dokumen_id' => $statusVerified->id,
            'catatan_penolakan_dokumen'    => null,
        ]);

        return redirect()->route('admin.verifikasi.dokumen.index')
            ->with('status', "Dokumen {$user->nama} berhasil diverifikasi.");
    }

    public function reject(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_penolakan_dokumen' => ['required', 'string', 'max:500'],
        ]);

        $statusRejected = StatusVerifikasiDokumen::where('kode', 'rejected')->firstOrFail();

        $user->update([
            'status_verifikasi_dokumen_id' => $statusRejected->id,
            'catatan_penolakan_dokumen'    => $validated['catatan_penolakan_dokumen'],
        ]);

        return redirect()->route('admin.verifikasi.dokumen.index')
            ->with('status', "Dokumen {$user->nama} ditolak.");
    }
}
