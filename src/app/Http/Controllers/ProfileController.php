<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Transaction;

class ProfileController extends Controller
{

    // プロフィールのトップ画面（一覧や概要）
    public function showProfileIndex()
    {
        $user = Auth::user();

        // 出品した商品
        $listedItems = Item::with('images')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // 購入した商品（transactionsテーブルからbuyer_idで取得）
        $purchasedItems = Item::with('images')
            ->whereIn('id', function ($query) use ($user) {
                $query->select('item_id')
                    ->from('transactions')
                    ->where('buyer_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('profile_index', compact('user', 'listedItems', 'purchasedItems'));
    }

    // プロフィール編集画面（設定）
    public function showEditProfile()
    {
        $user = Auth::user();
        return view('profile_edit', compact('user'));
    }
}
