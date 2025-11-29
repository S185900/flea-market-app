<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Address;
use App\Http\Requests\AddressRequest;

class AddressController extends Controller
{
    // 送付先住所変更画面の表示
    public function showEditAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        $address = $user->getAddress();

        return view('address_edit', [
            'item' => $item,
            'user' => $user,
            'address' => $address,
            'oldValues' => [
                'postal_code' => old('postal_code', $address->postal_code),
                'shipping_address' => old('address', $address->shipping_address),
                'building_name' => old('building_name', $address->building_name ?? ''),
            ],
        ]);
    }

    // 送付先住所変更の保存
    public function updateAddress(AddressRequest $request, $item_id)
    {
        $user = Auth::user();

        $user->update([
            'postal_code' => $request->postal_code,
            'shipping_address' => $request->address,
            'building_name' => $request->building_name,
        ]);

        $fullAddress = $user->getAddress()->full();

        return redirect()->route('purchase.form', ['item_id' => $item_id])
            ->with('message', '住所を更新しました'); // テスト用に追加
    }
}
