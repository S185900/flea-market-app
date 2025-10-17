@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_confirm.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

<!-- 商品購入ページ -->
@section('content')

<div class="purchase_confirm">

    <div class="purchase_confirm__item-section">
        <div class="item-section-1">
            <div class="item-section__flex-1">
                <div class="purchase_confirm__image">
                    @if ($item->images->isNotEmpty())
                        @foreach ($item->images as $image)
                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $item->title }}" class="purchase_confirm__image__display">
                            @break
                        @endforeach
                    @else
                        <div class="item-image__fallback">商品画像なし</div>
                    @endif
                </div>
            </div>
            <div class="item-section__flex-2">
                <h3 class="product-title">{{ $item->title }}</h3>
                <p class="product-price">
                    <span class="price-mark">¥</span>{{ number_format($item->price) }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('purchase.confirm', ['item' => $item->id]) }}">
            @csrf

            <div class="item-section-2">
                <h3 class="section-title">支払い方法</h3>

                <select class="payment-method-select" name="payment_method" onchange="this.form.submit()">
                    <option value="">選択してください</option>
                    <option value="convenience" {{ ($selectedMethod ?? '') === 'convenience' ? 'selected' : '' }}>コンビニ支払い</option>
                    <option value="card" {{ ($selectedMethod ?? '') === 'card' ? 'selected' : '' }}>カード支払い</option>
                </select>
                @error('payment_method')
                    <span class="invalid-feedback-1" role="alert">
                        <strong class="error-message">{{ $message }}</strong>
                    </span>
                @enderror

            </div>
        </form>

        <div class="item-section-3">
            <div class="item-section-3__flex">
                <h3 class="section-title">配送先</h3>
                <a class="change-address" href="{{ route('address.edit', ['item_id' => $item->id]) }}">変更する</a>
            </div>
            <p class="address-info">
                <span>〒</span>
                {{ $user->postal_code }}<br>
                {{ $user->shipping_address }}<br>
                {{ $user->building_name }}
            </p>
        </div>

    </div>

    <div class="purchase_confirm__payment-method-section">
        <section class="summary">
            <p class="summary-item">商品代金: ¥{{ number_format($item->price) }}</p>

            <p class="summary-item">支払い方法: 
                @switch($selectedMethod)
                    @case('card')
                        カード支払い
                        @break
                    @case('convenience')
                        コンビニ支払い
                        @break
                    @default
                        未選択
                @endswitch
            </p>
            @error('payment_method')
                <span class="invalid-feedback" role="alert">
                    <strong class="error-message">{{ $message }}</strong>
                </span>
            @enderror


        </section>
        <form id="purchase-form" method="POST" action="{{ route('purchase.stripe', ['item' => $item->id]) }}" target="_blank">
            @csrf
            <input type="hidden" name="payment_method" value="{{ $selectedMethod }}">
            <button class="purchase-button">購入する</button>
        </form>
        <script>
            document.getElementById('purchase-form').addEventListener('submit', function () {
                setTimeout(function () {
                    window.location.href = "{{ route('items.index') }}";
                }, 1000); // 1秒後に商品一覧へ遷移
            });
        </script>
    </div>

</div>
@endsection