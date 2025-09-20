@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_confirm.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

<!-- 商品購入ページ -->
@section('content')

<div class="purchase_confirm">

    <div class="purchase_confirm__item-section">
        <div class="item-section product">
            <div class="item-section__flex-1">
                <img src="product.jpg" alt="商品画像" class="product-image">
            </div>
            <div class="item-section__flex-2">
                <h3 class="product-title">商品名</h3>
                <p class="product-price">¥47,000</p>
            </div>
        </div>

        <div class="item-section">
            <h3 class="section-title">支払い方法</h3>
            <select>
                <option>選択してください</option>
                <option selected>コンビニ払い</option>
                <option>クレジットカード</option>
                <option>銀行振込</option>
            </select>
        </div>

        <div class="item-section">
            <div class="item-section-3__flex">
                <h3 class="section-title">配送先</h3>
                <button class="change-address">変更する</button>
            </div>
            <p>〒XXX-YYYY<br>ここには住所と建物名が入ります</p>
        </div>
    </div>

    <div class="purchase_confirm__payment-method-section">
        <section class="summary">
            <p>商品代金: ¥47,000</p>
            <p>支払い方法: コンビニ払い</p>
        </section>
        <button class="purchase-button">購入する</button>
    </div>

</div>
@endsection