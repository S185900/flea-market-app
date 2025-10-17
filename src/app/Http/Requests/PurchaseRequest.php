<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // 認証済みユーザーのみ許可
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => 'required|in:card,convenience',
            'shipping_address' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'payment_method.in' => '有効な支払い方法を選択してください',
            'shipping_address.required' => '配送先住所を入力してください',
            'shipping_address.string' => '配送先住所は文字列で入力してください',
            'shipping_address.max' => '配送先住所は255文字以内で入力してください',
        ];
    }
}
