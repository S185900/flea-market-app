<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Transaction;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    // プロフィール画面の表示
    public function showProfileIndex(Request $request)
    {
        $user = Auth::user();
        $page = $request->get('page', 'sell');

        $listedItems = collect();
        $purchasedItems = collect();

        if ($page === 'sell') {
            $listedItems = Item::with('images')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        if ($page === 'buy') {
            $purchasedItems = Transaction::with('item.images')
                ->where('buyer_id', $user->id)
                ->where('status', 'completed')
                ->orderByDesc('completed_at')
                ->get()
                ->pluck('item');
        }

        return view('profile_index', compact('user', 'listedItems', 'purchasedItems', 'page'));
    }

    // プロフィール編集画面の表示
    public function showEditProfile()
    {
        $user = Auth::user();
        $profile = $user->getProfile();

        return view('profile_edit', [
            'user' => $user,
            'profile' => $profile,
            'isFirstLogin' => false,
        ]);
    }

    // プロフィール編集の保存
    public function updateProfile(ProfileRequest $request)
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

        $user->save();

        return redirect()->route('mypage.index');
    }

    // プロフィール設定画面(初回)の表示
    public function showCreateProfileForm()
    {
        $user = Auth::user();
        $profile = $user->getProfile();

        return view('profile_edit', [
            'user' => $user,
            'profile' => $profile,
            'isFirstLogin' => true,
        ]);
    }

    // プロフィール設定の保存(初回)
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

        Auth::login($user);

        return redirect()->route('items.index');
    }
}
