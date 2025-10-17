<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    public function showEditAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        return view('address_edit', [
            'item' => $item,
            'user' => $user,
            'oldValues' => [
                'postal_code' => old('postal_code', $user->postal_code),
                'shipping_address' => old('address', $user->shipping_address),
                'building_name' => old('building_name', $user->building_name),
            ],
        ]);
    }

    public function updateAddress(AddressRequest $request, $item_id)
    {
        $user = Auth::user();

        $user->update([
            'postal_code' => $request->postal_code,
            'shipping_address' => $request->address,
            'building_name' => $request->building_name,
        ]);

        return redirect()->route('purchase.form', ['item_id' => $item_id])
                         ->with('message', '住所を更新しました');
    }

}
