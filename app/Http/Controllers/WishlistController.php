<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Produk;

class WishlistController extends Controller
{
    public function index(Request $request)
    {
        $wishlist = Wishlist::with('produk')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json(['success' => true, 'data' => $wishlist]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produk,id_produk',
        ]);

        $produk = Produk::find($request->produk_id);
        if (!$produk) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'produk_id' => $request->produk_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Produk berhasil ditambahkan ke wishlist.', 'data' => $wishlist], 201);
    }

    public function destroy(Request $request, $id)
    {
        $wishlist = Wishlist::where('id_wishlist', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$wishlist) {
            return response()->json(['success' => false, 'message' => 'Wishlist tidak ditemukan.'], 404);
        }

        $wishlist->delete();

        return response()->json(['success' => true, 'message' => 'Wishlist berhasil dihapus.']);
    }
}
