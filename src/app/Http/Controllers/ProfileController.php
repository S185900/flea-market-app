<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Transaction;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    // プロフィール画面（一覧・概要）
    public function showProfileIndex(Request $request)
    {
        $user = Auth::user();
        // $page = $request->get('page');
        $page = $request->get('page', 'sell'); // デフォルト表示'sell'

        $listedItems = collect();
        $purchasedItems = collect();

        if ($page === 'sell') {
            // 出品商品のみ取得
            $listedItems = Item::with('images')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($page === 'buy') {
            // 購入商品のみ取得
            $purchasedItems = Transaction::with('item.images')
                ->where('buyer_id', $user->id)
                ->where('status', 'completed')
                ->orderByDesc('completed_at')
                ->get()
                ->pluck('item');
        }

        return view('profile_index', compact('user', 'listedItems', 'purchasedItems', 'page'));
    }

    // プロフィール編集画面（設定）
    public function showEditProfile()
    {
        $user = Auth::user();
        return view('profile_edit', compact('user'));
    }

    public function updateProfile(ProfileRequest $request)
    {
        // dd($request->file('image'));
        
        $user = Auth::user();

        $user->name = $request->name;
        $user->postal_code = $request->postal_code;
        $user->shipping_address = $request->shipping_address;
        $user->building_name = $request->building_name ?? null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile_images', 'public');

            // dd($path);

            $user->profile_image_url = $path;
        }

        $user->save();

        return redirect()->route('mypage.index');

    }

    // 初回プロフィール登録画面
    public function showCreateProfileForm()
    {
        $user = Auth::user();
        return view('profile_create', compact('user'));
    }

    // 初回登録
    public function storeProfile(ProfileRequest $request)
    {
        $user = Auth::user();

        $user->name = $request->name;
        $user->postal_code = $request->postal_code;
        $user->shipping_address = $request->shipping_address;
        $user->building_name = $request->building_name ?? null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile_images', 'public');
            $user->profile_image_url = $path;
        }

        $user->profile_completed = true;
        $user->save();

        return redirect()->route('mypage.index');
    }

}
