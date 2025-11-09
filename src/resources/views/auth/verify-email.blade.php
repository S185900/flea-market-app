@extends('layouts.app_simple')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css')}}">
@endsection

<!-- メール認証誘導ページ -->
@section('content')
<p class="notice-text">登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</p>
<div class="verify-email">

    <!-- テスト環境ではMailHogが別タグで開かれる。本番環境ではMailHogが開かれない -->
    <a class="verify-email__link" href="{{ route('verification.notice.notice') }}"
            @if (App::environment('local'))
            onclick="window.open('http://localhost:8025', '_blank');"
        @endif
    >
        認証はこちらから
    </a>
    <!-- <a class="verify-email__link" href="{{ url('/email/verify') }}">認証はこちらから</a> -->
    <form class="resend-verification__form" method="POST" action="{{ route('verification.send') }}" novalidate>
        @csrf
        <button class="resend-verification__link" type="submit">認証メールを再送する</button>
    </form>
</div>
@endsection