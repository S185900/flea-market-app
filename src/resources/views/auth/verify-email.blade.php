@extends('layouts.app_simple')

@section('css')
<link rel="stylesheet" href="{{ asset('css/verify-email.css')}}">
@endsection

<!-- メール認証誘導ページ -->
@section('content')
<p class="notice-text">登録していただいたメールアドレスに認証メールを送付しました。<br>メール認証を完了してください。</p>
<div class="verify-email">
    <a class="verify-email__link" href="/" class="button">認証はこちらから</a>
    <form class="resend-verification__form" method="POST" action="{{ route('verification.send') }}" novalidate>
        @csrf
        <button class="resend-verification__link" type="submit">認証メールを再送する</button>
    </form>
</div>
@endsection