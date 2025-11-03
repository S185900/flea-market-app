<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
            'name' => 'required|max:20',
            'postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
            'shipping_address' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,png',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'ユーザー名は必須です',
            'name.max' => 'ユーザー名は20文字以内で入力してください',

            'postal_code.required' => '郵便番号は必須です',
            'postal_code.regex' => '郵便番号はハイフンありの8文字（例: 123-4567）で入力してください',

            'shipping_address.required' => '住所は必須です',

            'image.mimes' => 'プロフィール画像は.jpegまたは.png形式でアップロードしてください',
        ];
    }

}
