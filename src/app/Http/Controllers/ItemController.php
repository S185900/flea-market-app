<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // return view('items_index');

        $items = Item::all();
        $tab = 'recommend'; // 固定でおすすめタブ

        return view('items_index', compact('items', 'tab'));



        // 商品一覧を取得（例：新しい順）
        // $items = Item::orderBy('created_at', 'desc')->get();

    }
}
