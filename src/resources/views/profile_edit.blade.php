@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css')}}">
@endsection

<!-- プロフィール編集画面(設定画面/初回) -->
@section('content')
<h2 class="profile-edit-title">プロフィール設定</h2>
<section class="profile-edit">
    <form class="profile-edit-form" method="POST" action="{{ $isFirstLogin ? route('mypage.store') : route('mypage.profile.update') }}" enctype="multipart/form-data" novalidate>
        @csrf

        @if (!$isFirstLogin)
            @method('PATCH')
        @endif

        <div class="profile-edit-image-area">
            <div class="profile-edit-image-preview">
                @if ($user->profile_image_url)
                    <img src="{{ $user->profile_image_url }}" alt="" class="user-icon" />
                @endif
            </div>

            <label for="image" class="profile-edit-custom-file">
                画像を選択する
            </label>
            <input id="image" type="file" class="profile-edit-input__file" name="image" accept="image/*">

            @error('image')
                <p class="form-error-message" role="alert">
                        {{ $message }}
                </p>
            @enderror
        </div>

        <div class="profile-edit-item">
            <label for="name" class="profile-edit-label">ユーザー名</label>
            <input id="name" type="text" class="profile-edit-input" name="name" value="{{ old('name', $profile->name) }}" required autocomplete="name" autofocus>

            @error('name')
                <p class="form-error-message" role="alert">
                        {{ $message }}
                </p>
            @enderror
        </div>

        <div class="profile-edit-item">
            <label for="postal_code" class="profile-edit-label">郵便番号</label>
            <input id="postal_code" type="text" class="profile-edit-input" name="postal_code" value="{{ old('postal_code', $profile->postal_code) }}" required autocomplete="postal_code">

            @error('postal_code')
                <p class="form-error-message" role="alert">
                        {{ $message }}
                </p>
            @enderror
        </div>

        <div class="profile-edit-item">
            <label for="shipping_address" class="profile-edit-label">住所</label>
            <input id="shipping_address" type="text" class="profile-edit-input" name="shipping_address" value="{{ old('shipping_address', $profile->shipping_address) }}" required autocomplete="shipping_address">

            @error('shipping_address')
                <p class="form-error-message" role="alert">
                        {{ $message }}
                </p>
            @enderror
        </div>

        <div class="profile-edit-item">
            <label for="building_name" class="profile-edit-label">建物名</label>
            <input id="building_name" type="text" class="profile-edit-input" name="building_name" value="{{ old('building_name', $profile->building_name) }}" required autocomplete="building_name">
        </div>

        <div class="profile-edit-item">
            <button class="profile-edit-button" type="submit">
                更新する
            </button>
        </div>
    </form>
</section>
@endsection