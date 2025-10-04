@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell_form.css')}}">
@endsection

<!-- 出品ページ -->
@section('content')
<h2 class="sell-form-title">商品の出品</h2>
<div class="sell-form">
    <form class="sell-form" method="POST" action="{{ route('mypage.profile') }}" novalidate>
        @csrf

        <div class="profile-edit-image-area">

            <label for="name" class="sell-form-label">商品画像</label>
            <input id="name" type="text" class="sell-form-input-1 @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <label for="image" class="profile-edit-custom-file">
                画像を選択する
            </label>
            <input id="image" type="file" class="profile-edit-input__file @error('image') is-invalid @enderror" name="image" accept="image/*">
            @error('image')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

        </div>

        <div class="sell-form-detail-label">
            <h3 class="sell-form-detail-title">商品の詳細</h3>
        </div>

        <div class="sell-form-item">
            <label for="name" class="sell-form-label">カテゴリー</label>
        </div>

        <div class="sell-form-item">
            <label for="postal_code" class="sell-form-label">商品の状態</label>
            <input id="postal_code" type="text" class="sell-form-input-2 @error('postal_code') is-invalid @enderror" name="postal_code" value="{{ old('postal_code') }}" required autocomplete="postal_code">
        </div>


        <div class="sell-form-detail-label">
            <h3 class="sell-form-detail-title">商品名と説明</h3>
        </div>

        <div class="sell-form-item">
            <label for="address" class="sell-form-label">商品名</label>
            <input id="address" type="text" class="sell-form-input-3 @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" required autocomplete="address">
            @error('address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="sell-form-item">
            <label for="building_name" class="sell-form-label">ブランド名</label>
            <input id="building_name" type="text" class="sell-form-input-3 @error('building_name') is-invalid @enderror" name="building_name" value="{{ old('building_name') }}" required autocomplete="building_name">
            @error('building_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="sell-form-item">
            <label for="description" class="sell-form-label">商品の説明</label>
            <input id="description" type="text" class="sell-form-input-4 @error('description') is-invalid @enderror" name="description" value="{{ old('description') }}" required autocomplete="description">
            @error('description')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="sell-form-item">
            <label for="price" class="sell-form-label">販売価格</label>
            <input id="price" type="text" class="sell-form-input-3 @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}" required autocomplete="price">
            @error('price')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="sell-form-item">
            <button class="profile-edit-button" type="submit">
                出品する
            </button>
        </div>
    </form>
</div>
@endsection