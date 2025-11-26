@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell_form.css')}}">
@endsection

<!-- 商品出品画面 -->
@section('content')
<h2 class="sell-form-title">商品の出品</h2>
<section class="sell-form">
    <form class="sell-form" method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="profile-edit-image-area">
            <label for="image" class="sell-form-label">商品画像</label>

            <div class="input-wrapper">

                <input type="text" class="sell-form-input-1" value="" readonly>
                <label for="image" class="profile-edit-custom-file">画像を選択する</label>
                <input id="image" type="file" class="profile-edit-input__file" name="image" accept="image/*">

                @error('image')
                    <p class="form-error-message" role="alert">
                        {{ $message }}
                    </p>
                @enderror

            </div>
        </div>

        <div class="sell-form-detail-label">
            <h3 class="sell-form-detail-title">
                商品の詳細
            </h3>
        </div>

        <div class="sell-form-item">
            <label class="sell-form-label">
                カテゴリー
            </label>
            <div class="category-checkbox-group">
                @foreach([
                    'ファッション','家電','インテリア','レディース','メンズ','コスメ',
                    '本','ゲーム','スポーツ','キッチン','ハンドメイド',
                    'アクセサリー','おもちゃ','ベビー・キッズ'
                ] as $category)
                    <input type="checkbox" name="categories[]" id="cat-{{ $loop->index }}" value="{{ $category }}" class="category-checkbox">
                    <label for="cat-{{ $loop->index }}" class="category-label">{{ $category }}</label>
                @endforeach
            </div>
            @error('categories')
                <p class="form-error-message" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="sell-form-item">
            <label for="condition" class="sell-form-label">
                商品の状態
            </label>
            <select id="condition" name="condition" class="sell-form-input-2" required>
                <option value="">選択してください</option>
                <option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
                <option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                <option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                <option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
            </select>
            @error('condition')
                <p class="form-error-message" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="sell-form-detail-label">
            <h3 class="sell-form-detail-title">
                商品名と説明
            </h3>
        </div>

        <div class="sell-form-item">
            <label for="product_name" class="sell-form-label">
                商品名
            </label>
            <input id="product_name" type="text" class="sell-form-input-3" name="product_name" value="{{ old('product_name') }}" required autocomplete="product_name">

            @error('product_name')
                <p class="form-error-message" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="sell-form-item">
            <label for="brand_name" class="sell-form-label">
                ブランド名
            </label>
            <input id="brand_name" type="text" class="sell-form-input-3" name="brand_name" value="{{ old('brand_name') }}" required>
        </div>

        <div class="sell-form-item">
            <label for="description" class="sell-form-label">
                商品の説明
            </label>
            <textarea id="description" class="sell-form-input-4" name="description" required>{{ old('description') }}</textarea>

            @error('description')
                <p class="form-error-message" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="sell-form-item">
            <label for="price" class="sell-form-label">
                販売価格
            </label>
            <input id="price" type="text" class="sell-form-input-3" name="price" value="{{ old('price') }}" required placeholder="¥">

            @error('price')
                <p class="form-error-message" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="sell-form-item">
            <button class="profile-edit-button" type="submit">
                出品する
            </button>
        </div>
    </form>
</sec>
@endsection