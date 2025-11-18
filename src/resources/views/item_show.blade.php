@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_show.css')}}">
@endsection

<!-- 商品詳細ページ -->
@section('content')

<div class="item-show">

    <section class="item-show__image-area">
        <div class="item-show__image">
            @if ($item->images->isNotEmpty())
                @foreach ($item->images as $image)
                    <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $item->title }}" class="item-show__image-display">
                    @break
                @endforeach
            @else
                <div class="item-image__fallback">商品画像なし</div>
            @endif
        </div>
    </section>

    <section class="item-show__info-area">

        <div class="item-show__info">
            <h2 class="item-show__info-title">
                {{ $item->title }}
            </h2>
            @if (!empty($item->brand?->brand_name))
                <p class="item-show__brand-name">
                    {{ $item->brand->brand_name }}
                </p>
            @endif
            <p class="item-show__price">
                <span class="amount">¥{{ number_format($item->price) }}</span><span class="spacer"> </span>(税込)
            </p>
            <div class="item-show__meta-info">
                <div class="icon-block">
                    <form action="{{ route('item.like', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="like-button">
                            <img
                                src="{{ $item->likes->contains('user_id', Auth::id()) ? '/images/like-icon-filled.png' : '/images/like-icon-outline.png' }}"
                                alt="いいね"
                                class="like-icon"
                            />
                        </button>
                    </form>
                    <span class="count">
                        {{ $likesCount }}
                    </span>
                </div>
                <div class="icon-block">
                    <img src="/images/comment-icon.png" alt="コメント" class="comment-icon" />
                    <span class="count">
                        {{ $commentsCount }}
                    </span>
                </div>
            </div>

            <a href="{{ route('purchase.form', $item->id) }}" class="checkout-button">
                購入手続きへ
            </a>

        </div>

        <div class="item-show__description">
            <h3 class="item-show__description-title">
                商品説明
            </h3>
            <p class="item-show__description-text">
                {!! nl2br(e($item->description)) !!}
            </p>
        </div>

        <div class="item-show__product-info">
            <h3 class="item-show__product-info-title">
                商品の情報
            </h3>
            <div class="tags-area-1">
                <p class="tags-title">
                    カテゴリー
                </p>
                @foreach ($item->categories->chunk(3) as $chunk)
                    <div class="tag-row">
                        @foreach ($chunk as $category)
                            <span class="category-tag">{{ $category->category_name }}</span>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="tags-area-2">
                <p class="tags-title">
                    商品の状態
                </p>
                <div class="tag-wrapper">
                    <span class="tag-label">{{ $item->condition_label }}</span>
                </div>
            </div>
        </div>

        <div class="item-show__comment">
            <h3 class="item-show__comment-title">
                コメント({{ $commentsCount }})
            </h3>

            @foreach ($item->comments as $comment)
                <div class="comment-area">
                    <div class="user-info">
                        @if ($comment->user && $comment->user->profile_image_url)
                            <img src="{{ $comment->user->profile_image_url }}" alt="" class="user-icon" />
                            <p class="user-name">
                                {{ $comment->user->name }}
                            </p>
                        @else
                            <img src="{{ asset('images/default-icon.png') }}" alt="" class="user-icon" />
                            <p class="user-name">
                                {{ $comment->user ? $comment->user->name : 'Admin' }}
                            </p>
                        @endif
                    </div>

                    <div class="comment-label">
                        <p class="comment-submit">{{ $comment->comment }}</p>
                    </div>
                </div>
            @endforeach

            <p class="comment-textarea-title">商品へのコメント</p>

            <form action="{{ route('item.comment', $item->id) }}" method="POST" novalidate>
                @csrf
                <textarea name="comment" class="comment-textarea"
                    placeholder=""
                    @guest readonly @endguest>{{ old('comment') }}</textarea>

                @error('comment')
                    <p class="error">{{ $message }}</p>
                @enderror

                <button type="submit" class="comment-button">
                    コメントを送信する
                </button>
            </form>
        </div>

    </section>

</div>
@endsection