<?php

namespace App\Http\Requests;

use Laravel\Fortify\Fortify;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // メールアドレス：入力必須、メール形式
            Fortify::username() => ['required', 'email'],

            // パスワード：入力必須
            'password' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            // 1. 未入力の場合
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
        ];
    }

    // 2. 入力情報が誤っている場合
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->errors()->isEmpty()) {
                $credentials = $this->only('email', 'password');

                if (!Auth::attempt($credentials, $this->filled('remember'))) {
                    $validator->errors()->add('auth', 'ログイン情報が登録されていません');
                }
            }
        });
    }

}
