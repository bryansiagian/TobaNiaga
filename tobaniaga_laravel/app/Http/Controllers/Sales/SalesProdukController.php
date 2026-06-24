<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\FotoProduk;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\StatusProduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SalesProdukController extends Controller
{
    private function umkmSales()
    {
        $umkm = Auth::user()->umkm;

        if (!$umkm) {
            abort(redirect()->route('sales.profil.index')->with('status', 'Lengkapi profil UMKM kamu terlebih dahulu.'));
        }

        return $umkm;
    }

    public function index(): View
    {
        $umkm      = $this->umkmSales();
        $produk    = Produk::with(['kategori', 'status', 'fotoProduk'])
                        ->where('umkm_id', $umkm->id)
                        ->latest()
                        ->paginate(15);
        $kategori  = KategoriProduk::orderBy('nama')->get();
        $statusList = StatusProduk::all();

        return view('sales.produk.index', compact('produk', 'kategori', 'statusList', 'umkm'));
    }

    public function store(Request $request): RedirectResponse
    {
        $umkm = $this->umkmSales();

        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:kategori_produk,id'],
            'deskripsi'   => ['required', 'string'],
            'harga'       => ['required', 'numeric', 'min:0'],
            'stok'        => ['required', 'integer', 'min:0'],
            'satuan'      => ['required', 'string', 'max:50'],
            'foto'        => ['nullable', 'array', 'max:10'],
            'foto.*'      => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $statusTersedia = StatusProduk::where('kode', 'tersedia')->value('id');

        $produk = Produk::create([
            'umkm_id'     => $umkm->id,
            'kategori_id' => $validated['kategori_id'],
            'status_id'   => $statusTersedia,
            'nama_produk' => $validated['nama_produk'],
            'deskripsi'   => $validated['deskripsi'],
            'harga'       => $validated['harga'],
            'stok'        => $validated['stok'],
            'satuan'      => $validated['satuan'],
        ]);

        if ($request->hasFile('foto')) {
            foreach ($request->file('foto') as $i => $file) {
                $path = $file->store('produk', 'public');
                FotoProduk::create([
                    'produk_id' => $produk->id,
                    'url_foto'  => $path,
                    'urutan'    => $i,
                ]);
            }
        }

        return back()->with('status', "Produk \"{$produk->nama_produk}\" berhasil ditambahkan.");
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        abort_if($produk->umkm_id !== $this->umkmSales()->id, 403);

        $validated = $request->validate([
            'nama_produk' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:kategori_produk,id'],
            'status_id'   => ['required', 'exists:status_produk,id'],
            'deskripsi'   => ['required', 'string'],
            'harga'       => ['required', 'numeric', 'min:0'],
            'stok'        => ['required', 'integer', 'min:0'],
            'satuan'      => ['required', 'string', 'max:50'],
            'foto'        => ['nullable', 'array'],
            'foto.*'      => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $produk->update($validated);

        if ($request->hasFile('foto')) {
            $totalFoto = $produk->fotoProduk()->count();
            foreach ($request->file('foto') as $i => $file) {
                if ($totalFoto >= 10) break;
                $path = $file->store('produk', 'public');
                FotoProduk::create([
                    'produk_id' => $produk->id,
                    'url_foto'  => $path,
                    'urutan'    => $totalFoto + $i,
                ]);
                $totalFoto++;
            }
        }

        return back()->with('status', "Produk \"{$produk->nama_produk}\" berhasil diperbarui.");
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        abort_if($produk->umkm_id !== $this->umkmSales()->id, 403);

        foreach ($produk->fotoProduk as $foto) {
            Storage::disk('public')->delete($foto->url_foto);
        }

        $produk->delete();

        return back()->with('status', "Produk berhasil dihapus.");
    }

    public function destroyFoto(FotoProduk $foto): RedirectResponse
    {
        abort_if($foto->produk->umkm_id !== $this->umkmSales()->id, 403);

        Storage::disk('public')->delete($foto->url_foto);
        $foto->delete();

        return back()->with('status', "Foto berhasil dihapus.");
    }
}
