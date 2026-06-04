<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate(['product_id' => ['required', 'integer', 'exists:products,id']]);

        if (!Auth::check()) {
            return response()->json(['error' => 'login_required'], 401);
        }

        $userId    = Auth::id();
        $productId = $request->product_id;

        $existing = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();

        if ($existing) {
            $existing->delete();
            return response()->json(['saved' => false]);
        }

        Wishlist::create(['user_id' => $userId, 'product_id' => $productId]);
        return response()->json(['saved' => true]);
    }
}
