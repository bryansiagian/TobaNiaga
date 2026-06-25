<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerPesananController extends Controller
{
    public function riwayat()
    {
        $pesanan = Pesanan::with(['status', 'pembayaran', 'detail', 'umkm'])
            ->where('customer_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('customer.pesanan.riwayat', compact('pesanan'));
    }

    public function show(Pesanan $pesanan): View
    {
        abort_if($pesanan->customer_id !== Auth::id(), 403);

        $pesanan->load([
            'status',
            'detail',
            'pembayaran.status',
            'umkm',
            'alamat',
            'metodePengiriman',
            'pengiriman.status',
        ]);

        return view('customer.pesanan.show', compact('pesanan'));
    }
}
