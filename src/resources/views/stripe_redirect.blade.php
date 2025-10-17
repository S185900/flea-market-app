@extends('layouts.app_nav')

@section('content')
    <div class="redirect-message">
        <p>Stripeの決済ページへ移動しています…</p>
        <p>自動で遷移しない場合は、<a href="{{ $stripe_url }}">こちらをクリック</a>してください。</p>
    </div>

    <meta http-equiv="refresh" content="1;url={{ $stripe_url }}">
@endsection


