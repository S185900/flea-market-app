@extends('layouts.app_nav')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address_edit.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

<!-- 住所変更ページ -->
@section('content')
<h2 class="address-edit-title">住所の変更</h2>
<div class="address-edit">
    <form class="address-edit-form" method="POST" action="{{ route('address.update', ['item_id' => $item->id]) }}" novalidate>
        @csrf

        <div class="address-edit-item">
            <label for="postal_code" class="address-edit-label">郵便番号</label>
            <input id="postal_code" type="text" class="address-edit-input @error('postal_code') is-invalid @enderror"
                name="postal_code" value="{{ $oldValues['postal_code'] }}" required autocomplete="postal_code">
            @error('postal_code')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="address-edit-item">
            <label for="address" class="address-edit-label">住所</label>
            <input id="address" type="text" class="address-edit-input @error('address') is-invalid @enderror"
                name="address" value="{{ $oldValues['shipping_address'] }}" required autocomplete="address">
            @error('address')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="address-edit-item">
            <label for="building_name" class="address-edit-label">建物名</label>
            <input id="building_name" type="text" class="address-edit-input @error('building_name') is-invalid @enderror"
                name="building_name" value="{{ $oldValues['building_name'] }}" autocomplete="building_name">
            @error('building_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="address-edit-item">
            <button class="address-edit-button" type="submit">
                更新する
            </button>
        </div>
    </form>


</div>
@endsection