@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items_index.css')}}">
@endsection

<!-- 商品一覧画面（トップ画面） -->
@section('content')
<div class="items-tabs">
    <a href="{{ route('items.index', ['tab' => 'recommend', 'title' => request('title')]) }}" class="items-tab {{ request('tab', 'recommend') === 'recommend' ? 'items-tab--active' : '' }}">
        おすすめ
    </a>
    <a href="{{ route('items.index', ['tab' => 'mylist', 'title' => request('title')]) }}" class="items-tab {{ request('tab') === 'mylist' ? 'items-tab--active' : '' }}">
        マイリスト
    </a>
</div>

<hr class="items-divider" role="separator">

<section class="items-index">

    <div class="items-grid">

        @foreach ($items as $item)
            <a href="{{ route('item.detail', ['item_id' => $item->id]) }}" class="item-card__link">
                <div class="item-card">
                    <div class="item-image">

                        @if ($item->images->isNotEmpty())
                            <img class="item-image__display" src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->title }}">
                        @else
                            <div class="item-image__fallback">商品画像</div>
                        @endif

                        @if ($item->status === 'sold')
                            <div class="item-image__sold-label">Sold</div>
                        @endif

                    </div>
                    <div class="item-name">{{ strip_tags($item->title) }}</div>
                </div>
            </a>
        @endforeach

    </div>
</section>
@endsection