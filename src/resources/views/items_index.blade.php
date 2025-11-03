@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_index.css')}}">
@endsection

<!-- 商品一覧ページ(トップ画面) -->
@section('content')

<!-- タブ切り替え -->
<div class="items-tabs">
    <a href="{{ route('items.index', ['tab' => 'recommend', 'title' => request('title')]) }}" class="items-tab {{ request('tab', 'recommend') === 'recommend' ? 'active' : '' }}">
        おすすめ
    </a>
    <a href="{{ route('items.index', ['tab' => 'mylist', 'title' => request('title')]) }}" class="items-tab {{ request('tab') === 'mylist' ? 'active' : '' }}">
        マイリスト
    </a>
</div>

<div class="items-divider"></div>

<div class="items-index">

    <!-- 商品一覧 -->
    <div class="items-grid">

        @foreach ($items as $item)
            <a href="{{ route('item.detail', ['item_id' => $item->id]) }}" class="item-card-link">
                <div class="item-card">
                    <div class="item-image">

                        @if ($item->images->isNotEmpty())
                            <img class="item-image__display" src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->title }}">
                        @else
                            <div class="item-image__fallback">商品画像</div>
                        @endif

                        <!-- sold表示 -->
                        @if ($item->status === 'sold')
                            <div class="sold-label">Sold</div>
                        @endif

                    </div>
                    <div class="item-name">{{ strip_tags($item->title) }}</div>
                </div>
            </a>
        @endforeach


    </div>


</div>
@endsection