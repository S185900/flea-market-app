<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'address' => ['required', 'string'],
            'description' => ['required', 'string', 'max:255'],
            'image' => ['required', 'file', 'mimes:jpeg,png'],
            'categories' => ['required', 'array', 'min:1'],
            'condition' => ['required', 'in:1,2,3,4'],
            'price' => ['required', 'numeric', 'min:0'],
            'brand_name' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'address.required' => '商品名は必須です。',
            'description.required' => '商品の説明は必須です。',
            'description.max' => '商品の説明は255文字以内で入力してください。',
            'image.required' => '商品画像は必須です。',
            'image.mimes' => '画像は.jpegまたは.png形式でアップロードしてください。',
            'categories.required' => 'カテゴリーを1つ以上選択してください。',
            'condition.required' => '商品の状態を選択してください。',
            'price.required' => '販売価格は必須です。',
            'price.numeric' => '販売価格は数値で入力してください。',
            'price.min' => '販売価格は0円以上で入力してください。',
            'brand_name.required' => 'ブランド名は必須です。',
        ];
    }
}
