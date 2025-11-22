@extends('layouts.app_simple')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css')}}">
@endsection

<!-- メール認証誘導画面 -->
@section('content')
<div class="notice">
    <p class="notice-text">
        <span class="break-tablet">登録していただいたメールアドレスに</span>認証メールを送付しました。
        <br>
        メール認証を完了してください。
    </p>
</div>
<section class="verify-email">

    <nav class="verify-email__nav">
        <a class="verify-email__link" href="{{ route('verification.notice.notice') }}"
                @if (App::environment('local'))
                onclick="window.open('http://localhost:8025', '_blank');"
            @endif
        >
            認証はこちらから
        </a>
    </nav>

    <form class="resend-verification__form" method="POST" action="{{ route('verification.send') }}" novalidate>
        @csrf
        <button class="resend-verification__link" type="submit">認証メールを再送する</button>
    </form>

</section>
@endsection