<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PencairanDanaKurir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPencairanKurirController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->get('status', 'diajukan');

        $pencairan = PencairanDanaKurir::query()
            ->when($status !== 'semua', fn($q) => $q->where('status', $status))
            ->with(['courier', 'rekeningBankKurir', 'diprosesOleh', 'detail'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $countDiajukan = PencairanDanaKurir::where('status', 'diajukan')->count();
        $countDiproses = PencairanDanaKurir::where('status', 'diproses')->count();

        return view('admin.pencairan-kurir.index', compact('pencairan', 'status', 'countDiajukan', 'countDiproses'));
    }

    public function show(PencairanDanaKurir $pencairanDanaKurir): View
    {
        $pencairanDanaKurir->load(['courier', 'rekeningBankKurir', 'diprosesOleh', 'detail.pengiriman.pesanan']);

        return view('admin.pencairan-kurir.show', compact('pencairanDanaKurir'));
    }

    public function proses(PencairanDanaKurir $pencairanDanaKurir): RedirectResponse
    {
        $adminId = Auth::id();

        try {
            DB::transaction(function () use ($pencairanDanaKurir, $adminId) {
                $locked = PencairanDanaKurir::where('id', $pencairanDanaKurir->id)
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

    public function selesai(Request $request, PencairanDanaKurir $pencairanDanaKurir): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_admin' => ['nullable', 'string', 'max:300'],
        ]);

        $adminId = Auth::id();

        try {
            DB::transaction(function () use ($pencairanDanaKurir, $adminId, $validated) {
                $locked = PencairanDanaKurir::where('id', $pencairanDanaKurir->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($locked->status !== 'diproses') {
                    throw new \RuntimeException('Status pencairan ini bukan "Sedang Diproses".');
                }

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

    public function tolak(Request $request, PencairanDanaKurir $pencairanDanaKurir): RedirectResponse
    {
        $validated = $request->validate([
            'catatan_admin' => ['required', 'string', 'max:300'],
        ]);

        $adminId = Auth::id();

        try {
            DB::transaction(function () use ($pencairanDanaKurir, $adminId, $validated) {
                $locked = PencairanDanaKurir::where('id', $pencairanDanaKurir->id)
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

        return back()->with('status', 'Pengajuan pencairan ditolak. Pengiriman terkait bisa diajukan ulang oleh kurir.');
    }
}
