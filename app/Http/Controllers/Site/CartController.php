<?php
namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $items = collect(session('cart', []));
        return view('pages.cart', compact('items'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'grade'      => ['nullable', 'in:A,B,C'],
            'qty'        => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $grade   = $request->grade ?? $product->grade;
        $qty     = max(1, (int)$request->get('qty', 1));
        $key     = $product->id . '_' . $grade;

        $cart = session('cart', []);

        if (isset($cart[$key])) {
            $cart[$key]['qty'] += $qty;
        } else {
            $cart[$key] = [
                'product_id'   => $product->id,
                'product_name' => $product->name,
                'product_slug' => $product->slug,
                'sku'          => $product->sku,
                'grade'        => $grade,
                'qty'          => $qty,
                'price'        => $product->price,
                'thumb'        => $product->thumb,
            ];
        }

        session(['cart' => $cart]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok'    => true,
                'count' => collect($cart)->sum('qty'),
                'msg'   => "'{$product->name}' added to your quote.",
            ]);
        }

        return back()->with('cart_added', "'{$product->name}' added to your quote.");
    }

    public function update(Request $request, string $key)
    {
        $request->validate(['qty' => ['required', 'integer', 'min:0']]);
        $cart = session('cart', []);

        if (isset($cart[$key])) {
            if ($request->qty < 1) {
                unset($cart[$key]);
            } else {
                $cart[$key]['qty'] = $request->qty;
            }
            session(['cart' => $cart]);
        }

        return back();
    }

    public function remove(string $key)
    {
        $cart = session('cart', []);
        unset($cart[$key]);
        session(['cart' => $cart]);
        return back()->with('success', 'Item removed from quote.');
    }

    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Quote cleared.');
    }
}
