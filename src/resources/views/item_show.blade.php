@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/item_show.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

<!-- 商品詳細ページ -->
@section('content')

<div class="item-show">
    <section class="item-show__image-area">
        <div class="item-show__image">
            商品画像
        </div>
    </section>

    <section class="item-show__info-area">

        <div class="item-show__info">
            <h2 class="item-show__info-title">
                商品名がここに入る
            </h2>
            <p class="item-show__brand-name">ブランド名</p>
            <p class="item-show__price">
                <span class="amount">¥47,000</span><span class="spacer"> </span>(税込)
            </p>
            <div class="item-show__likes__comment__count">
                <div class="icon-block">
                    <img src="/images/like-icon.png" alt="いいね" class="like-icon" />
                    <span class="count">3</span>
                </div>
                <div class="icon-block">
                    <img src="/images/comment-icon.png" alt="コメント" class="comment-icon" />
                    <span class="count">1</span>
                </div>
            </div>

            <button class="checkout-button">
                購入手続きへ
            </button>

        </div>

        <div class="item-show__description">
            <h3 class="item-show__description-title">
                商品説明
            </h3>
            <p class="item-show__description-text">
                カラー：グレー<br>新品<br>商品の状態は良好です。傷もありません。<br><br>購入後、即発送いたします。
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
                <span class="tag-deco">おもちゃ</span>
                <span class="tag-deco">おもちゃ</span>
                <span class="tag-deco">おもちゃ</span>
            </div>
            <div class="tags-area-2">
                <p class="tags-title">
                    商品の状態
                </p>
                <span class="tag">良好</span>
            </div>

        </div>

        <div class="item-show__comment">
            <h3 class="item-show__comment-title">
                コメント(1)
            </h3>
            <div class="comment-area">
                <div class="user-info">
                    <img src="" alt="アイコン" class="user-icon" />
                    <p class="user-name">admin</p>
                </div>
                <lavel class="comment-lavel">
                    <input type="text" class="comment-submit" placeholder="こちらにコメントが入ります。">
                </lavel>
            </div>
            <p class="comment-textarea-title">商品のへコメント</p>
            <textarea class="comment-textarea" placeholder=""></textarea>
            <button class="comment-button">コメントを送信する</button>
        </div>

    </section>

</div>
@endsection