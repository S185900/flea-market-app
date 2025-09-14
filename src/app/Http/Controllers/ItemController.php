<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {

        // 検索機能
        $title = $request->input('title');
        $tab = $request->input('tab', 'recommend'); // デフォルトはおすすめ

        $items = Item::with(['images', 'transactions'])->where('status', 'available');

        if (!empty($title)) {
            $items->where('title', 'like', '%' . $title . '%');
        }

        if ($tab === 'mylist' && auth()->check()) {
            $items->where('user_id', auth()->id());
        }

        $items = $items->get();

        return view('items_index', compact('items', 'tab'));



        // 商品一覧を取得（例：新しい順）
        // $items = Item::orderBy('created_at', 'desc')->get();

    }
}
