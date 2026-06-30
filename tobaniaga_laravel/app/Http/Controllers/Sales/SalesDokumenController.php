<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\StatusVerifikasiDokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesDokumenController extends Controller
{
    public function form(): View|RedirectResponse
    {
        $user = Auth::user();
        $statusDokumen = $user->statusVerifikasiDokumen?->kode;

        if ($statusDokumen === 'pending') {
            return redirect()->route('sales.dashboard')
                ->with('info', 'Dokumenmu sedang dalam proses verifikasi.');
        }

        return view('sales.dokumen', compact('user'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'nik'           => ['required', 'string', 'size:16', 'regex:/^[0-9]+$/'],
            'tanggal_lahir' => ['required', 'date', 'before:-17 years'],
            'no_hp'         => ['required', 'string', 'max:20'],
            'alamat_ktp'    => ['required', 'string', 'max:500'],
            'foto_ktp'      => [$user->foto_ktp ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'foto_kk'       => [$user->foto_kk  ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ], [
            'tanggal_lahir.before' => 'Pendaftar harus berusia minimal 17 tahun.',
            'nik.size'             => 'NIK harus terdiri dari 16 digit.',
        ]);

        $statusPending = StatusVerifikasiDokumen::where('kode', 'pending')->firstOrFail();

        $pathKtp = $user->foto_ktp;
        $pathKk  = $user->foto_kk;

        if ($request->hasFile('foto_ktp')) {
            $pathKtp = $request->file('foto_ktp')->store('dokumen/' . $user->id, 'private');
        }
        if ($request->hasFile('foto_kk')) {
            $pathKk = $request->file('foto_kk')->store('dokumen/' . $user->id, 'private');
        }

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

        return redirect()->route('sales.dashboard')
            ->with('status', 'Dokumen berhasil dikirim. Menunggu verifikasi admin dalam 1-2 hari kerja.');
    }
}
