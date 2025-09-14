<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {

        $title = $request->input('title');
        $tab = $request->input('tab', 'recommend'); // デフォルトはおすすめタブ一覧

        $items = collect(); // 初期化（未ログイン時のマイリストタブ一覧対策）

        if ($tab === 'recommend') {
            // おすすめタブ
            $query = Item::with(['images', 'transactions'])
                ->where('status', 'available');

            if (auth()->check()) {
                // ログイン時は自分の出品を除外
                $query->where('user_id', '!=', auth()->id());
            }

            if (!empty($title)) {
                $query->where('title', 'like', '%' . $title . '%');
            }

            $items = $query->get();

        } elseif ($tab === 'mylist') {
            // マイリストタブ
            if (auth()->check()) {
                $query = Item::with(['images', 'transactions'])
                    ->whereHas('likes', function ($q) {
                        $q->where('user_id', auth()->id());
                    })
                    ->where('user_id', '!=', auth()->id()); // 自分の出品は除外

                if (!empty($title)) {
                    $query->where('title', 'like', '%' . $title . '%');
                }

                $items = $query->get();
            }
            // 未ログイン時は空のコレクションのまま
        }

        return view('items_index', compact('items', 'tab', 'title'));

    }
}
