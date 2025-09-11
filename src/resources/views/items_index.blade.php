@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_index.css')}}">
@endsection

<!-- 商品一覧ページ(トップ画面) -->
@section('content')

<!-- タブ切り替え -->
<div class="items-tabs">
    <button class="items-tab active">おすすめ</button>
    <button class="items-tab">マイリスト</button>
</div>
<div class="items-divider"></div>

<div class="items-index">

    <!-- 商品一覧 -->
    <div class="items-grid">
        <!-- foreach ($i = 0; $i < 8; $i++) -->
            <div class="item-card">
                <div class="item-image">商品画像</div>
                <div class="item-name">商品名</div>
            </div>
            <div class="item-card">
                <div class="item-image">商品画像</div>
                <div class="item-name">商品名</div>
            </div>
            <div class="item-card">
                <div class="item-image">商品画像</div>
                <div class="item-name">商品名</div>
            </div>
            <div class="item-card">
                <div class="item-image">商品画像</div>
                <div class="item-name">商品名</div>
            </div>
        <!-- endforeach -->
    </div>


</div>
@endsection