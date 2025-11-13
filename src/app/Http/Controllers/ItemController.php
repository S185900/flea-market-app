<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Comment;
use App\Models\Like;
use App\Models\MyList;
use App\Http\Requests\CommentRequest;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $title = $request->input('title');
        $tab = $request->input('tab', 'recommend'); // デフォルトはおすすめ
        $items = collect(); // 初期化

        if ($tab === 'recommend') {
            $query = Item::with(['images', 'transaction']);

            // ログイン時は自分の出品を除外
            if (auth()->check()) {
                $query->where('user_id', '!=', auth()->id());
            }

            // タイトル検索
            if (!empty($title)) {
                $query->where('title', 'like', '%' . $title . '%');
            }

            $items = $query->get();

        } elseif ($tab === 'mylist' && auth()->check()) {
            $items = MyList::favoriteItems(auth()->id(), $title);
        }

        // dd($items);
        return view('items_index', compact('items', 'tab', 'title'));
    }


    public function showItemDetail($item_id)
    {
        $item = Item::with([
            'images',
            'transaction',
            'brand',
            'categories',
            'likes',
            // コメントを新しい順で取得
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.user'
        ])->findOrFail($item_id);

        // dd($item->brand_id, $item->brand);

        // いいね数・コメント数：リレーションからリアルタイム集計
        $likesCount = $item->likes()->count();
        $commentsCount = $item->comments()->count();

        return view('item_show', compact('item', 'likesCount', 'commentsCount'));
    }

    public function postComment(CommentRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        Comment::create([
            'item_id' => $item->id,
            'commenter_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()->route('item.detail', $item_id)->with('success', 'コメントを投稿しました');
    }

    public function postLike(Request $request, Item $item)
    {
        $user = Auth::user();

        $existingLike = Like::where('item_id', $item->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete(); // いいね解除
            $liked = false;
        } else {
            Like::create([
                'item_id' => $item->id,
                'user_id' => $user->id,
            ]);
            $liked = true;
        }

        // 最新のいいね数を返す
        $likesCount = $item->likes()->count();

        return redirect()->route('item.detail', $item->id);
    }

}

