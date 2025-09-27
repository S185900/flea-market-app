<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class PurchaseController extends Controller
{
    public function showPurchaseForm($item_id)
    {
        $item = Item::with('images', 'brand')->findOrFail($item_id);
        $user = Auth::user();

        return view('purchase_confirm', [
            'item' => $item,
            'user' => $user,
            'address' => $user->address, // プロフィールに登録済みの住所
        ]);
    }

    public function confirm(Request $request, Item $item)
    {
        $selectedMethod = $request->input('payment_method');

        return view('purchase_confirm', [
            'item' => $item,
            'user' => auth()->user(),
            'selectedMethod' => $selectedMethod,
        ]);
    }

}
