<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
    {
        return view('items_index');

        // 商品一覧を取得（例：新しい順）
        // $items = Item::orderBy('created_at', 'desc')->get();

        // ビューにデータを渡して表示
        // return view('items_index', compact('items'));
    }
}
