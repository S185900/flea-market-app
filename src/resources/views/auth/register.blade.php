@extends('layouts.app_simple')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css')}}">
@endsection

<!-- 会員登録画面 -->
@section('content')
<h2 class="register-title">会員登録</h2>
<section class="register">
    <form class="register-form" method="POST" action="{{ route('register') }}" novalidate>
        @csrf

        <div class="register-item">
            <label for="name" class="register-label">ユーザー名</label>
            <input id="name" type="text" class="register-input @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

            @error('name')
                <p class="form-error-message" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="register-item">
            <label for="email" class="register-label">メールアドレス</label>
            <input id="email" type="email" class="register-input @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

            @error('email')
                <p class="form-error-message" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="register-item">
            <label for="password" class="register-label">パスワード</label>
            <input id="password" type="password" class="register-input @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

            @error('password')
                @if (str_contains($message, '一致しません'))
                @else
                    <p class="form-error-message" role="alert">
                        {{ $message }}
                    </p>
                @endif
            @enderror
        </div>

        <div class="register-item">
            <label for="password-confirm" class="register-label">確認用パスワード</label>
            <input id="password-confirm" type="password" class="register-input" name="password_confirmation" required autocomplete="new-password">

            @error('password')
                @if (str_contains($message, '一致しません'))
                    <p class="form-error-message" role="alert">
                        {{ $message }}
                    </p>
                @endif
            @enderror
        </div>

        <div class="register-item">
            <button class="register-button" type="submit">
                登録する
            </button>
        </div>
    </form>
    <nav class="register-nav">
        <a class="login-link" href="{{ route('login') }}">ログインはこちら</a>
    </nav>
</section>
@endsection