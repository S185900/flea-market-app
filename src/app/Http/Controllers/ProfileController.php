<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Transaction;

class ProfileController extends Controller
{
    // プロフィール画面（一覧・概要）
    public function showProfileIndex(Request $request)
    {
        $user = Auth::user();
        $listedItems = collect();
        $purchasedItems = collect();

        if ($request->get('page') === 'sell') {
            $listedItems = Item::with('images')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        } elseif ($request->get('page') === 'buy') {
            $purchasedItems = Item::with('images')
                ->whereIn('id', function ($query) use ($user) {
                    $query->select('item_id')
                        ->from('transactions')
                        ->where('buyer_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // 両方表示
            $listedItems = Item::with('images')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            $purchasedItems = Item::with('images')
                ->whereIn('id', function ($query) use ($user) {
                    $query->select('item_id')
                        ->from('transactions')
                        ->where('buyer_id', $user->id);
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('profile_index', compact('user', 'listedItems', 'purchasedItems'));
    }

    // プロフィール編集画面（設定）
    public function showEditProfile()
    {
        $user = Auth::user();
        return view('profile_edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->postal_code = $validated['postal_code'];
        $user->shipping_address = $validated['shipping_address'];
        $user->building_name = $validated['building_name'] ?? null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile_images', 'public');
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
    public function storeProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'shipping_address' => 'required|string|max:255',
            'building_name' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->postal_code = $validated['postal_code'];
        $user->shipping_address = $validated['shipping_address'];
        $user->building_name = $validated['building_name'] ?? null;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile_images', 'public');
            $user->profile_image_url = $path;
        }

        $user->profile_completed = true;
        $user->save();

        return redirect()->route('mypage.index');
    }

    

}
