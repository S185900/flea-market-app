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
    // 商品一覧画面の表示
    public function index(Request $request)
    {
        $title = $request->input('title');
        $tab = $request->input('tab', 'recommend');
        $items = collect();

        if ($tab === 'recommend') {
            $query = Item::with(['images', 'transaction']);

            if (auth()->check()) {
                $query->where('user_id', '!=', auth()->id());
            }

            if (!empty($title)) {
                $query->where('title', 'like', '%' . $title . '%');
            }

            $items = $query->get();

        } elseif ($tab === 'mylist' && auth()->check()) {
            $items = MyList::favoriteItems(auth()->id(), $title);
        }

        return view('items_index', compact('items', 'tab', 'title'));
    }

    // 商品詳細画面の表示
    public function showItemDetail($item_id)
    {
        $item = Item::with([
            'images',
            'transaction',
            'brand',
            'categories',
            'likes',
            'comments' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'comments.user'
        ])->findOrFail($item_id);

        $likesCount = $item->likes()->count();
        $commentsCount = $item->comments()->count();

        return view('item_show', compact('item', 'likesCount', 'commentsCount'));
    }

    // コメントの投稿
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

    // いいねの投稿・削除
    public function postLike(Request $request, Item $item)
    {
        $user = Auth::user();

        $existingLike = Like::where('item_id', $item->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            Like::create([
                'item_id' => $item->id,
                'user_id' => $user->id,
            ]);
            $liked = true;
        }

        $likesCount = $item->likes()->count();

        return redirect()->route('item.detail', $item->id);
    }
}

