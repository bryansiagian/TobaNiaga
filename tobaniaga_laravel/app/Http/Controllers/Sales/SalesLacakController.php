<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SalesLacakController extends Controller
{
    public function show(Pesanan $pesanan): View
    {
        abort_unless($pesanan->umkm_id === Auth::user()->umkm?->id, 403);
        abort_unless($pesanan->pengiriman, 404);

        $pesanan->load([
            'pengiriman.status',
            'pengiriman.log.status',
            'pengiriman.kurir',
            'customer',
            'alamat',
            'detail',
            'status',
        ]);

        return view('sales.lacak.show', compact('pesanan'));
    }
}
