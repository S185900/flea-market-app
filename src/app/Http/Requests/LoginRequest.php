<?php

namespace App\Http\Requests;

use Laravel\Fortify\Fortify;
use Illuminate\Foundation\Http\FormRequest;

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
            Fortify::username() => ['required', 'email'], // ここでメール形式を指定
            'password' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            // 未入力の場合
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',

            // 入力情報が誤っている場合
            'email.email' => 'ログイン情報が登録されていません',
        ];
    }
}
