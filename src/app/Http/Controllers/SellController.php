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
use App\Models\Sell;
use App\Http\Requests\ExhibitionRequest;

class SellController extends Controller
{
    // 商品出品画面の表示
    public function showCreateItem()
    {
        return view('sell_form');
    }

    // 商品出品の保存
    public function storeItem(ExhibitionRequest $request)
    {
        $validated = $request->validated();

        $brand = null;
        if (!empty($validated['brand_name'])) {
            $brand = Brand::firstOrCreate(['brand_name' => $validated['brand_name']]);
        }

        $sell = Sell::create([
            'user_id' => Auth::id(),
            'title' => $validated['product_name'],
            'brand_id' => optional($brand)?->id,
            'description' => $validated['description'],
            'price' => $validated['price'],
            'condition' => $validated['condition'],
            'status' => 'available',
        ]);

        foreach ($validated['categories'] as $categoryName) {
            $category = Category::firstOrCreate(['category_name' => $categoryName]);
            CategoryItem::create([
                'item_id' => $sell->id,
                'category_id' => $category->id,
            ]);
        }

        if ($request->hasFile('image')) {

            $path = $request->file('image')->store('product_images', 'public');

            ItemImage::create([
                'item_id' => $sell->id,
                'image_path' => $path,
            ]);
        }

        return redirect()->route('mypage.index')->with('status', '商品を出品しました！');
    }
}
