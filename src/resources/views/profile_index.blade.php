@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_index.css')}}">
@endsection

<!-- プロフィール画面(/mypage) -->
@section('content')

<div class="profile-header">
    <div class="profile-info">
        <div class="profile-image">
            @if ($user->profile_image_url)
                <img src="{{ $user->profile_image_url }}" alt="プロフィール画像" class="user-icon" />
            @else
                <img src="{{ 'default-icon.png' }}" alt="プロフィール画像" class="user-icon" />
            @endif
        </div>
    </div>
    <p class="user-name">{{ $user->name }}</p>
    <div class="profile-edit-link">
        <a href="{{ route('mypage.profile') }}" class="btn-edit-profile">プロフィールを編集</a>
    </div>
</div>

<div class="profile-index">

    <!-- タブ切り替え -->
    <div class="items-tabs">
        <a href="{{ route('mypage.index', ['page' => 'sell']) }}" class="items-tab {{ request('page') === 'sell' || request('page') === null ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage.index', ['page' => 'buy']) }}" class="items-tab {{ request('page') === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    <div class="items-divider">
        @if (request('page') === 'buy')
            <!-- 購入商品 -->
            <div class="items-index">
                <div class="items-grid">
                    @forelse ($purchasedItems as $item)
                        <a href="{{ route('item.detail', ['item_id' => $item->id]) }}" class="item-card-link">
                            <div class="item-card">
                                <div class="item-image">
                                    @if ($item->images->isNotEmpty())
                                        <img class="item-image__display" src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->title }}">
                                    @else
                                        <div class="item-image__fallback">商品画像</div>
                                    @endif
                                    <!-- sold表示は要件にないため非表示 -->
                                    @if ($item->status === 'sold' && $page !== 'buy')
                                        <div class="sold-label">sold</div>
                                    @endif
                                </div>
                                <div class="item-name">{{ strip_tags($item->title) }}</div>
                            </div>
                        </a>
                    @empty
                        <p>購入した商品はありません。</p>
                    @endforelse
                </div>
            </div>
        @else
            <!-- 出品商品 -->
            <div class="items-index">
                <div class="items-grid">
                    @forelse ($listedItems as $item)
                        <a href="{{ route('item.detail', ['item_id' => $item->id]) }}" class="item-card-link">
                            <div class="item-card">
                                <div class="item-image">
                                    @if ($item->images->isNotEmpty())
                                        <img class="item-image__display" src="{{ asset('storage/' . $item->images->first()->image_path) }}" alt="{{ $item->title }}">
                                    @else
                                        <div class="item-image__fallback">商品画像</div>
                                    @endif
                                    @if ($item->status === 'sold')
                                        <div class="sold-label">sold</div>
                                    @endif
                                </div>
                                <div class="item-name">{{ strip_tags($item->title) }}</div>
                            </div>
                        </a>
                    @empty
                        <p>出品した商品はありません。</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>

@endsection