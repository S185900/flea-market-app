@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell_form.css')}}">
@endsection

<!-- 出品ページ -->
@section('content')
<h2 class="sell-form-title">商品の出品</h2>
<div class="sell-form">
    <form class="sell-form" method="POST" action="{{ route('sell.store') }}" enctype="multipart/form-data" novalidate>
        @csrf

        <!-- 商品画像 -->
        <div class="profile-edit-image-area">
            <label for="image" class="sell-form-label">商品画像</label>

            <div class="input-wrapper">

                <!-- 装飾用 -->
                <input type="text" class="sell-form-input-1" value="" readonly>

                <label for="image" class="profile-edit-custom-file">画像を選択する</label>
                <input id="image" type="file" class="profile-edit-input__file @error('image') is-invalid @enderror" name="image" accept="image/*">

                @error('image')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror

            </div>
        </div>


        <!-- 商品の詳細 -->
        <div class="sell-form-detail-label">
            <h3 class="sell-form-detail-title">商品の詳細</h3>
        </div>

        <!-- カテゴリー -->
        <div class="sell-form-item">
            <label class="sell-form-label">カテゴリー</label>
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
        </div>

        <!-- 商品の状態 -->
        <div class="sell-form-item">
            <label for="condition" class="sell-form-label">商品の状態</label>
            <select id="condition" name="condition" class="sell-form-input-2 @error('condition') is-invalid @enderror" required>
                <option value="">選択してください</option>
                <option value="1" {{ old('condition') == 1 ? 'selected' : '' }}>良好</option>
                <option value="2" {{ old('condition') == 2 ? 'selected' : '' }}>目立った傷や汚れなし</option>
                <option value="3" {{ old('condition') == 3 ? 'selected' : '' }}>やや傷や汚れあり</option>
                <option value="4" {{ old('condition') == 4 ? 'selected' : '' }}>状態が悪い</option>
            </select>
        </div>

        <!-- 商品名と説明 -->
        <div class="sell-form-detail-label">
            <h3 class="sell-form-detail-title">商品名と説明</h3>
        </div>

        <!-- 商品名 -->
        <div class="sell-form-item">
            <label for="address" class="sell-form-label">商品名</label>
            <input id="address" type="text" class="sell-form-input-3 @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" required autocomplete="address">
            @error('address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- ブランド名 -->
        <div class="sell-form-item">
            <label for="brand_name" class="sell-form-label">ブランド名</label>
            <input id="brand_name" type="text" class="sell-form-input-3 @error('brand_name') is-invalid @enderror" name="brand_name" value="{{ old('brand_name') }}" required>
            @error('brand_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- 商品説明 -->
        <div class="sell-form-item">
            <label for="description" class="sell-form-label">商品の説明</label>
            <textarea id="description" class="sell-form-input-4 @error('description') is-invalid @enderror" name="description" required>{{ old('description') }}</textarea>
            @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- 販売価格 -->
        <div class="sell-form-item">
            <label for="price" class="sell-form-label">販売価格</label>
            <input id="price" type="number" class="sell-form-input-3 @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}" required placeholder="¥">
            @error('price')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- 出品ボタン -->
        <div class="sell-form-item">
            <button class="profile-edit-button" type="submit">
                出品する
            </button>
        </div>
    </form>
</div>
@endsection