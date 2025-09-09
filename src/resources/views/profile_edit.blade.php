@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css')}}">
@endsection

<!-- プロフィール編集ページ -->
@section('content')
<h2 class="profile-edit-title">プロフィール設定</h2>
<div class="profile-edit">
    <form class="profile-edit-form" method="POST" action="{{ route('mypage.profile') }}" novalidate>
        @csrf

        <input type="file" class="profile-edit-input__file @error('image') is-invalid @enderror" name="image" accept="image/*">
        @error('image')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror

        <div class="profile-edit-item">
            <label for="name" class="profile-edit-label">ユーザー名</label>
            <input id="name" type="text" class="profile-edit-input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="profile-edit-item">
            <label for="postal_code" class="profile-edit-label">郵便番号</label>
            <input id="postal_code" type="text" class="profile-edit-input @error('postal_code') is-invalid @enderror" name="postal_code" value="{{ old('postal_code') }}" required autocomplete="postal_code">
            @error('postal_code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="profile-edit-item">
            <label for="address" class="profile-edit-label">住所</label>
            <input id="address" type="text" class="profile-edit-input @error('address') is-invalid @enderror" name="address" value="{{ old('address') }}" required autocomplete="address">
            @error('address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="profile-edit-item">
            <label for="building_name" class="profile-edit-label">建物名</label>
            <input id="building_name" type="text" class="profile-edit-input @error('building_name') is-invalid @enderror" name="building_name" value="{{ old('building_name') }}" required autocomplete="building_name">
            @error('building_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="profile-edit-item">
            <button class="profile-edit-button" type="submit">
                登録する
            </button>
        </div>
    </form>
</div>
@endsection