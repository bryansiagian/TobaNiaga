<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\AlamatCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AlamatCustomerController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'label'           => ['required', 'string', 'max:50'],
            'nama_penerima'   => ['required', 'string', 'max:100'],
            'no_hp_penerima'  => ['required', 'string', 'max:20'],
            'provinsi'        => ['required', 'string', 'max:100'],
            'kota'            => ['required', 'string', 'max:100'],
            'kecamatan'       => ['required', 'string', 'max:100'],
            'kelurahan'       => ['required', 'string', 'max:100'],
            'kode_pos'        => ['nullable', 'string', 'max:10'],
            'alamat_lengkap'  => ['required', 'string', 'max:500'],
            'is_utama'        => ['nullable', 'boolean'],
        ]);

        $validated['user_id'] = Auth::id();

        $sudahAdaAlamat = AlamatCustomer::where('user_id', Auth::id())->exists();
        $validated['is_utama'] = $request->boolean('is_utama') || !$sudahAdaAlamat;

        if ($validated['is_utama']) {
            AlamatCustomer::where('user_id', Auth::id())->update(['is_utama' => false]);
        }

        AlamatCustomer::create($validated);

        // Redirect ke URL yang diminta kalau ada, fallback ke back()
        $redirectTo = $request->input('redirect_to');
        if ($redirectTo && str_starts_with($redirectTo, config('app.url'))) {
            return redirect($redirectTo)->with('status', 'Alamat berhasil ditambahkan.');
        }

        return back()->with('status', 'Alamat berhasil ditambahkan.');
    }

    public function destroy(AlamatCustomer $alamat): RedirectResponse
    {
        abort_unless($alamat->user_id === Auth::id(), 403);
        $alamat->delete();

        return back()->with('status', 'Alamat berhasil dihapus.');
    }
}
