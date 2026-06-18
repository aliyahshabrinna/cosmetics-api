<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Produk;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $carts = Cart::with('produk')
            ->where('user_id', $request->user()->id)
            ->get();

        return response()->json(['success' => true, 'data' => $carts]);
    }

    public function store(Request $request)
    {
        $produkId = $request->id_produk ?? $request->produk_id;
        $request->merge(['produk_id' => $produkId]);

        $request->validate([
            'produk_id' => 'required|exists:produk,id_produk',
            'qty' => 'required|integer|min:1',
        ]);

        $produk = Produk::find($request->produk_id);
        if (!$produk) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
        }

        $cart = Cart::where('user_id', $request->user()->id)
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($cart) {
            $cart->qty += $request->qty;
            $cart->save();
        } else {
            $cart = Cart::create([
                'user_id' => $request->user()->id,
                'produk_id' => $request->produk_id,
                'qty' => $request->qty,
              ]);
        }

        return response()->json(['success' => true, 'message' => 'Item berhasil ditambahkan ke cart.', 'data' => $cart], 201);
    }

    // UPDATE: Cari berdasarkan kolom 'id'
    public function update(Request $request, $id)
    {
        $request->validate([
            'qty' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Item cart tidak ditemukan.'], 404);
        }

        $cart->qty = $request->qty;
        $cart->save();

        return response()->json(['success' => true, 'message' => 'Jumlah kuantitas berhasil diperbarui.', 'data' => $cart]);
    }

    // DESTROY: Cari berdasarkan kolom 'id'
    public function destroy(Request $request, $id)
    {
        $cart = Cart::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$cart) {
            return response()->json(['success' => false, 'message' => 'Item cart tidak ditemukan.'], 404);
        }

        $cart->delete();

        return response()->json(['success' => true, 'message' => 'Item cart berhasil dihapus.']);
    }
}