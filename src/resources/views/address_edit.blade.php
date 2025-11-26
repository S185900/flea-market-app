@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address_edit.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

<!-- 送付先住所変更画面 -->
@section('content')
<h2 class="address-edit-title">住所の変更</h2>
<section class="address-edit">
    <form class="address-edit-form" method="POST" action="{{ route('address.update', ['item_id' => $item->id]) }}" novalidate>
        @csrf

        <div class="address-edit-item">
            <label for="postal_code" class="address-edit-label">
                郵便番号
            </label>

            <input id="postal_code" type="text" class="address-edit-input"
                name="postal_code" value="{{ old('postal_code', $address->postal_code) }}" required autocomplete="postal_code">

            @error('postal_code')
                <p class="form-error-message" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="address-edit-item">
            <label for="address" class="address-edit-label">
                住所
            </label>

            <input id="address" type="text" class="address-edit-input"
                name="address" value="{{ old('address', $address->shipping_address) }}" required autocomplete="address">

            @error('address')
                <p class="form-error-message" role="alert">
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="address-edit-item">
            <label for="building_name" class="address-edit-label">
                建物名
            </label>

            <input id="building_name" type="text" class="address-edit-input"
                name="building_name" value="{{ old('building_name', $address->building_name) }}" autocomplete="building_name">
        </div>

        <div class="address-edit-item">
            <button class="address-edit-button" type="submit">
                更新する
            </button>
        </div>
    </form>
</section>
@endsection