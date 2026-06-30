<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\RekeningBank;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesRekeningController extends Controller
{
    private function umkm()
    {
        return Auth::user()->umkm;
    }

    public function index(): View
    {
        $umkm     = $this->umkm();
        $rekening = $umkm
            ? RekeningBank::where('umkm_id', $umkm->id)->orderByDesc('is_utama')->get()
            : collect();

        return view('sales.rekening.index', compact('rekening', 'umkm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $umkm = $this->umkm();
        abort_if(!$umkm, 403);

        $validated = $request->validate([
            'nama_bank'    => ['required', 'string', 'max:50'],
            'nama_pemilik' => ['required', 'string', 'max:100'],
            'no_rekening'  => ['required', 'string', 'max:50'],
        ]);

        $sudahAda = RekeningBank::where('umkm_id', $umkm->id)->exists();

        $rekening = RekeningBank::create([
            'umkm_id'      => $umkm->id,
            'nama_bank'    => $validated['nama_bank'],
            'nama_pemilik' => $validated['nama_pemilik'],
            'no_rekening'  => $validated['no_rekening'],
            'is_utama'     => !$sudahAda, // rekening pertama otomatis jadi utama
        ]);

        return back()->with('status', 'Rekening berhasil ditambahkan.');
    }

    public function update(Request $request, RekeningBank $rekeningBank): RedirectResponse
    {
        abort_if($rekeningBank->umkm_id !== $this->umkm()?->id, 403);

        $validated = $request->validate([
            'nama_bank'    => ['required', 'string', 'max:50'],
            'nama_pemilik' => ['required', 'string', 'max:100'],
            'no_rekening'  => ['required', 'string', 'max:50'],
        ]);

        $rekeningBank->update($validated);

        return back()->with('status', 'Rekening berhasil diperbarui.');
    }

    public function setUtama(RekeningBank $rekeningBank): RedirectResponse
    {
        $umkm = $this->umkm();
        abort_if($rekeningBank->umkm_id !== $umkm?->id, 403);

        RekeningBank::where('umkm_id', $umkm->id)->update(['is_utama' => false]);
        $rekeningBank->update(['is_utama' => true]);

        return back()->with('status', 'Rekening utama diperbarui.');
    }

    public function destroy(RekeningBank $rekeningBank): RedirectResponse
    {
        $umkm = $this->umkm();
        abort_if($rekeningBank->umkm_id !== $umkm?->id, 403);

        // Cegah hapus kalau masih dipakai di pencairan yang belum selesai
        $dipakai = $rekeningBank->pencairanDana()
            ->whereIn('status', ['diajukan', 'diproses'])
            ->exists();

        if ($dipakai) {
            return back()->with('error', 'Rekening ini sedang dipakai di pengajuan pencairan yang masih berjalan.');
        }

        $wasUtama = $rekeningBank->is_utama;
        $rekeningBank->delete();

        // Kalau yang dihapus adalah rekening utama, jadikan rekening lain (kalau ada) sebagai utama
        if ($wasUtama) {
            $penggantiUtama = RekeningBank::where('umkm_id', $umkm->id)->first();
            $penggantiUtama?->update(['is_utama' => true]);
        }

        return back()->with('status', 'Rekening berhasil dihapus.');
    }
}
