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
                <img src="{{ asset('storage/' . $user->profile_image_url) }}" alt="プロフィール画像">
            @else
                <div class="profile-image__fallback">No Image</div>
            @endif
        </div>
    </div>
    <h2 class="profile-edit-title">ユーザー名</h2>
    <div class="profile-edit-link">
        <a href="{{ route('mypage.profile') }}" class="btn-edit-profile">プロフィールを編集</a>
    </div>
</div>

<div class="profile-index">

    

    <!-- タブ切り替え -->
    <div class="items-tabs">
        <a href="#listed" class="items-tab active">出品した商品</a>
        <a href="#purchased" class="items-tab">購入した商品</a>
    </div>

    <div class="items-divider"></div>

        <!-- 出品商品 -->
        <div id="listed" class="items-index">
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
                    <p></p>
                @endforelse
            </div>
        </div>

            <!-- 購入商品 -->
            <div id="purchased" class="items-index">
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
                                    @if ($item->status === 'sold')
                                        <div class="sold-label">sold</div>
                                    @endif
                                </div>
                                <div class="item-name">{{ strip_tags($item->title) }}</div>
                            </div>
                        </a>
                    @empty
                        <p></p>
                    @endforelse
                </div>
            </div>

            <a href="" class="item-card-link">
                <div class="item-card">
                    <div class="item-image">
                        <div class="item-image__fallback">商品画像</div>
                    </div>
                    <div class="item-name">商品名</div>
                </div>
            </a>

            <a href="" class="item-card-link">
                <div class="item-card">
                    <div class="item-image">
                        <div class="item-image__fallback">商品画像</div>
                    </div>
                    <div class="item-name">商品名</div>
                </div>
            </a>

            <a href="" class="item-card-link">
                <div class="item-card">
                    <div class="item-image">
                        <div class="item-image__fallback">商品画像</div>
                    </div>
                    <div class="item-name">商品名</div>
                </div>
            </a>

            <a href="" class="item-card-link">
                <div class="item-card">
                    <div class="item-image">
                        <div class="item-image__fallback">商品画像</div>
                    </div>
                    <div class="item-name">商品名</div>
                </div>
            </a>
</div>

@endsection