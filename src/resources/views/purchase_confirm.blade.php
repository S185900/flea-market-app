@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/purchase_confirm.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

<!-- 商品購入画面 -->
@section('content')

<section class="purchase_confirm">

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
                        <div class="item-image__fallback">
                            商品画像なし
                        </div>
                    @endif

                </div>
            </div>
            <div class="item-section__flex-2">
                <h2 class="product-title">
                    {{ $item->title }}
                </h2>
                <p class="product-price">
                    <span class="price-mark">¥</span>{{ number_format($item->price) }}
                </p>
            </div>
        </div>

        <form method="POST" action="{{ route('purchase.confirm', ['item' => $item->id]) }}">
            @csrf

            <div class="item-section-2">
                <h3 class="section-title">
                    支払い方法
                </h3>

                <select class="payment-method-select" name="payment_method" onchange="this.form.submit()">
                    <option value="">
                        選択してください
                    </option>
                    <option value="convenience" {{ ($selectedMethod ?? '') === 'convenience' ? 'selected' : '' }}>
                        コンビニ支払い
                    </option>
                    <option value="card" {{ ($selectedMethod ?? '') === 'card' ? 'selected' : '' }}>
                        カード支払い
                    </option>
                </select>
                @error('payment_method')
                    <p class="form-error-message" role="alert">
                        {{ $message }}
                    </p>
                @enderror
                <div class="form-error-message" id="error-message-container"></div>

            </div>
        </form>

        <div class="item-section-3">
            <div class="item-section-3__flex">
                <h3 class="section-title">
                    配送先
                </h3>
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
            <table class="summary-table">
                <tr>
                    <th>商品代金</th>
                    <td>¥{{ number_format($item->price) }}</td>
                </tr>
                <tr>
                    <th>支払い方法</th>
                    <td>
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
                    </td>
                </tr>
            </table>

            <form id="purchase-form">
                @csrf
                @if($selectedMethod)
                    <input type="hidden" name="payment_method" value="{{ $selectedMethod }}">
                @endif
                <input type="hidden" name="shipping_address" value="{{ $fullAddress }}">
                <button type="submit" class="purchase-button">購入する</button>
            </form>

        </section>

        <script>
        document.getElementById('purchase-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const errorContainer = document.getElementById('error-message-container');
            errorContainer.innerHTML = '';

            fetch("{{ route('purchase.prepare', ['item' => $item]) }}", {
                method: "POST",
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(async response => {
                if (response.status === 422) {
                    const data = await response.json();
                    const errors = data.errors;

                    let messages = '';
                    for (const field in errors) {
                        messages += `<p>${errors[field].join('<br>')}</p>`;
                    }
                    errorContainer.innerHTML = messages;
                } else if (response.ok) {
                    const data = await response.json();
                    if (data.checkout_url) {
                        window.open(data.checkout_url, '_blank');
                        window.location.href = "{{ route('items.index') }}";
                    }
                } else {
                    errorContainer.innerHTML = '<p>サーバーエラーが発生しました。</p>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorContainer.innerHTML = '<p  class="form-error-message">支払い方法を選択してください</p>';
            });
        });
        </script>

    </div>

</section>
@endsection