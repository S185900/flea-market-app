<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            // ユーザー名:入力必須、20文字以内
            'name' => ['required', 'string', 'max:20'],

            // メールアドレス：入力必須、メール形式
            'email' => ['required', 'email'],

            // パスワード：入力必須、8文字以上
            // 確認用パスワード：入力必須、8文字以上、「パスワード」との重複のみ可
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages()
    {
        return [
            // 1. 未入力の場合
            'name.required' => 'お名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスはメール形式で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password_confirmation.required' => 'パスワードを入力してください',

            // 2. パスワードの入力規則違反の場合
            'password.min' => 'パスワードは8文字以上で入力してください',

            // 3. 確認用パスワードの入力規則違反の場合
            'password.confirmed' => 'パスワードと一致しません',

            // その他の規則違反の場合
            'name.max' => 'お名前は20文字以内で入力してください',
        ];
    }
}
