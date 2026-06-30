<?php

namespace App\Http\Controllers\Courier;

use App\Http\Controllers\Controller;
use App\Models\RekeningBankKurir;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourierRekeningController extends Controller
{
    public function index(): View
    {
        $rekening = RekeningBankKurir::where('courier_id', Auth::id())
            ->orderByDesc('is_utama')
            ->get();

        return view('courier.rekening.index', compact('rekening'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_bank'    => ['required', 'string', 'max:50'],
            'nama_pemilik' => ['required', 'string', 'max:100'],
            'no_rekening'  => ['required', 'string', 'max:50'],
        ]);

        $sudahAda = RekeningBankKurir::where('courier_id', Auth::id())->exists();

        RekeningBankKurir::create([
            'courier_id'   => Auth::id(),
            'nama_bank'    => $validated['nama_bank'],
            'nama_pemilik' => $validated['nama_pemilik'],
            'no_rekening'  => $validated['no_rekening'],
            'is_utama'     => !$sudahAda,
        ]);

        return back()->with('status', 'Rekening berhasil ditambahkan.');
    }

    public function update(Request $request, RekeningBankKurir $rekeningBankKurir): RedirectResponse
    {
        abort_if($rekeningBankKurir->courier_id !== Auth::id(), 403);

        $validated = $request->validate([
            'nama_bank'    => ['required', 'string', 'max:50'],
            'nama_pemilik' => ['required', 'string', 'max:100'],
            'no_rekening'  => ['required', 'string', 'max:50'],
        ]);

        $rekeningBankKurir->update($validated);

        return back()->with('status', 'Rekening berhasil diperbarui.');
    }

    public function setUtama(RekeningBankKurir $rekeningBankKurir): RedirectResponse
    {
        abort_if($rekeningBankKurir->courier_id !== Auth::id(), 403);

        RekeningBankKurir::where('courier_id', Auth::id())->update(['is_utama' => false]);
        $rekeningBankKurir->update(['is_utama' => true]);

        return back()->with('status', 'Rekening utama diperbarui.');
    }

    public function destroy(RekeningBankKurir $rekeningBankKurir): RedirectResponse
    {
        abort_if($rekeningBankKurir->courier_id !== Auth::id(), 403);

        $dipakai = $rekeningBankKurir->pencairanDanaKurir()
            ->whereIn('status', ['diajukan', 'diproses'])
            ->exists();

        if ($dipakai) {
            return back()->with('error', 'Rekening ini sedang dipakai di pengajuan pencairan yang masih berjalan.');
        }

        $wasUtama = $rekeningBankKurir->is_utama;
        $rekeningBankKurir->delete();

        if ($wasUtama) {
            $pengganti = RekeningBankKurir::where('courier_id', Auth::id())->first();
            $pengganti?->update(['is_utama' => true]);
        }

        return back()->with('status', 'Rekening berhasil dihapus.');
    }
}
