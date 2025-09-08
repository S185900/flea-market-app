@extends('layouts.app_simple')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css')}}">
@endsection

<!-- 会員登録ページ -->
@section('content')
<h2 class="register-title">会員登録</h2>
<div class="register">
    <form class="register-form" method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="register-item">
            <label for="name" class="register-label">ユーザー名</label>
            <input id="name" type="text" class="register-input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="register-item">
            <label for="email" class="register-label">メールアドレス</label>
            <input id="email" type="email" class="register-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="register-item">
            <label for="password" class="register-label">パスワード</label>
            <input id="password" type="password" class="register-input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="register-item">
            <label for="password-confirm" class="register-label">確認用パスワード</label>
            <input id="password-confirm" type="password" class="register-input" name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="register-item">
            <button class="register-button" type="submit">
                登録する
            </button>
        </div>
    </form>
    <a class="login-link" href="{{ route('login') }}">ログインはこちら</a>
</div>
@endsection