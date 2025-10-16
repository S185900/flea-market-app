<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use App\Models\CategoryItem;
use App\Models\ItemImage;
use App\Http\Requests\ExhibitionRequest;

class SellController extends Controller
{
    public function showCreateItem()
    {
        return view('sell_form');
    }

    public function storeItem(ExhibitionRequest $request)
    {
        // バリデーション済みのデータ取得
        $validated = $request->validated();

        // ブランド登録または取得（任意項目なので条件分岐）
        $brand = null;
        if (!empty($validated['brand_name'])) {
            $brand = Brand::firstOrCreate(['brand_name' => $validated['brand_name']]);
        }

        // 商品登録
        $item = Item::create([
            'user_id' => Auth::id(),
            'title' => $validated['address'],
            'brand_id' => $brand ? $brand->id : null,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'condition' => $validated['condition'],
            'status' => 'available',
        ]);

        // カテゴリー紐付け
        foreach ($validated['categories'] as $categoryName) {
            $category = Category::firstOrCreate(['category_name' => $categoryName]);
            CategoryItem::create([
                'item_id' => $item->id,
                'category_id' => $category->id,
            ]);
        }

        // 画像保存
        if ($request->hasFile('image')) {

            // 画像を保存（storage/app/public/product_images）
            $path = $request->file('image')->store('product_images', 'public');

            // DBに保存
            ItemImage::create([
                'item_id' => $item->id,
                'image_path' => $path, // Storage::url($path) にするとURL形式になる
            ]);
        }

        return redirect()->route('mypage.index')->with('status', '商品を出品しました！');
    }

}
