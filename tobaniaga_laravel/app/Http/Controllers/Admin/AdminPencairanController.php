<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PencairanDana;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPencairanController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'diajukan');

        $pencairan = PencairanDana::query()
            ->when($status !== 'semua', fn($q) => $q->where('status', $status))
            ->with(['umkm', 'rekeningBank', 'diprosesOleh', 'detail'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $countDiajukan = PencairanDana::where('status', 'diajukan')->count();
        $countDiproses = PencairanDana::where('status', 'diproses')->count();

        return view('admin.pencairan.index', compact('pencairan', 'status', 'countDiajukan', 'countDiproses'));
    }

    public function show(PencairanDana $pencairanDana): View
    {
        $pencairanDana->load(['umkm', 'rekeningBank', 'diprosesOleh', 'detail.pesanan.detail']);

        return view('admin.pencairan.show', compact('pencairanDana'));
    }

    /**
     * Admin mulai memproses — di sinilah row locking terjadi.
     * Hanya satu admin yang bisa berhasil mengubah status diajukan -> diproses.
     */
    public function proses(PencairanDana $pencairanDana): RedirectResponse
    {
        $adminId = Auth::id();

        try {
            DB::transaction(function () use ($pencairanDana, $adminId) {
                // Lock baris ini — admin lain yang coba akses baris yang sama
                // di waktu bersamaan akan DITAHAN oleh database sampai transaction ini selesai.
                $locked = PencairanDana::where('id', $pencairanDana->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($locked->status !== 'diajukan') {
                    throw new \RuntimeException('Pencairan ini sudah diproses oleh admin lain.');
                }

                $locked->update([
                    'status'        => 'diproses',
                    'diproses_oleh' => $adminId,
                    'diproses_at'   => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Pencairan mulai diproses. Silakan lakukan transfer manual lalu tandai selesai.');
    }

    /**
     * Admin menandai transfer sudah dilakukan.
     */
    public function selesai(Request $request, PencairanDana $pencairanDana): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:300'],
        ]);

        $adminId = Auth::id();

        try {
            DB::transaction(function () use ($pencairanDana, $adminId, $validated) {
                $locked = PencairanDana::where('id', $pencairanDana->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($locked->status !== 'diproses') {
                    throw new \RuntimeException('Status pencairan ini bukan "Sedang Diproses".');
                }

                // Pastikan admin yang menyelesaikan adalah admin yang sama yang memulai proses
                if ($locked->diproses_oleh !== $adminId) {
                    throw new \RuntimeException('Pencairan ini sedang ditangani admin lain (' .
                        ($locked->diprosesOleh->nama ?? 'admin lain') . ').');
                }

                $locked->update([
                    'status'        => 'selesai',
                    'catatan_admin' => $validated['catatan_admin'] ?? $locked->catatan_admin,
                    'selesai_at'    => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Pencairan ditandai selesai.');
    }

    /**
     * Tolak pengajuan — pesanan otomatis kembali eligible (lihat Umkm::pesananEligibleDicairkan()).
     */
    public function tolak(Request $request, PencairanDana $pencairanDana): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_admin' => ['required', 'string', 'max:300'],
        ]);

        $adminId = Auth::id();

        try {
            DB::transaction(function () use ($pencairanDana, $adminId, $validated) {
                $locked = PencairanDana::where('id', $pencairanDana->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!in_array($locked->status, ['diajukan', 'diproses'])) {
                    throw new \RuntimeException('Pencairan ini sudah final dan tidak bisa ditolak.');
                }

                $locked->update([
                    'status'        => 'ditolak',
                    'catatan_admin' => $validated['catatan_admin'],
                    'diproses_oleh' => $adminId,
                    'diproses_at'   => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', 'Pengajuan pencairan ditolak. Pesanan terkait bisa diajukan ulang oleh sales.');
    }
}
