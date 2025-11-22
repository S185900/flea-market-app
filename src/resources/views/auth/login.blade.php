@extends('layouts.app_simple')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css')}}">
@endsection

<!-- ログイン画面 -->
@section('content')
<h2 class="login-title">ログイン</h2>
<section class="login">
    <form class="login-form" method="POST" action="{{ route('login') }}" novalidate>
        @csrf

        <div class="login-item">
            <label for="email" class="login-label">メールアドレス</label>
            <input id="email" type="email" class="login-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>
                        {{ $message }}
                    </strong>
                </span>
            @enderror
        </div>

        <div class="login-item">
            <label for="password" class="login-label">パスワード</label>
            <input id="password" type="password" class="login-input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>
                        {{ $message }}
                    </strong>
                </span>
            @enderror
            @error('auth')
                <span class="auth-error" role="alert">
                    <strong>
                        {{ $message }}
                    </strong>
                </span>
            @enderror
        </div>

        <div class="login-item">
            <button class="login-button" type="submit">
                ログインする
            </button>
        </div>
    </form>
    <nav class="login-nav">
        <a class="login-link" href="{{ route('register') }}">会員登録はこちら</a>
    </nav>
</section>
@endsection