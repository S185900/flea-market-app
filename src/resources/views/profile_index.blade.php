@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_index.css')}}">
@endsection

<!-- プロフィール画面 -->
@section('content')

<div class="profile-header">
    <div class="profile-info">
        <div class="profile-image">
            @if ($user->profile_image_url)
                <img src="{{ $user->profile_image_url }}" alt="" class="user-icon" />
            @else
                <img src="{{ 'default-icon.png' }}" alt="" class="user-icon" />
            @endif
        </div>
    </div>
    <p class="user-name">
        {{ $user->name }}
    </p>
    <div class="profile-edit-link">
        <a href="{{ route('mypage.profile') }}" class="btn-edit-profile">プロフィールを編集</a>
    </div>
</div>

<div class="items-tabs">
    <a href="{{ route('mypage.index', ['page' => 'sell']) }}" class="items-tab {{ $page === 'sell' || $page === null ? 'active' : '' }}">
        出品した商品
    </a>
    <a href="{{ route('mypage.index', ['page' => 'buy']) }}" class="items-tab {{ $page === 'buy' ? 'active' : '' }}">
        購入した商品
    </a>
</div>

<hr class="items-divider" role="separator">

<section class="profile-index">

    <div class="items-index-content">
        @if ($page === 'buy')

            <div id="purchased-items" class="items-index">
                <div class="items-grid">

                    @forelse ($purchasedItems as $item)
                        <a href="{{ route('item.detail', ['item_id' => $item->id]) }}" class="item-card-link">
                            <article class="item-card">
                                <div class="item-image">

                                    @if ($item->images->isNotEmpty())
                                        <img class="item-image__display" src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->title }}">
                                    @else
                                        <div class="item-image__fallback">商品画像</div>
                                    @endif

                                    @if ($item->status === 'sold' && $page !== 'buy')
                                        <div class="sold-label">sold</div>
                                    @endif

                                </div>
                                <div class="item-name">{{ strip_tags($item->title) }}</div>
                            </article>
                        </a>
                    @empty
                        <p>購入した商品はありません</p>
                    @endforelse

                </div>
            </div>

        @else

            <div id="listed-items" class="items-index">
                <div class="items-grid">

                    @forelse ($listedItems as $item)
                        <a href="{{ route('item.detail', ['item_id' => $item->id]) }}" class="item-card-link">
                            <article class="item-card">
                                <div class="item-image">

                                    @if ($item->images->isNotEmpty())
                                        <img class="item-image__display" src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->title }}">
                                    @else
                                        <div class="item-image__fallback">商品画像</div>
                                    @endif

                                    @if ($item->status === 'sold')
                                        <div class="sold-label">Sold</div>
                                    @endif

                                </div>
                                <div class="item-name">{{ strip_tags($item->title) }}</div>
                            </article>
                        </a>
                    @empty
                        <p>出品した商品はありません</p>
                    @endforelse

                </div>
            </div>

        @endif
    </div>
</section>

@endsection