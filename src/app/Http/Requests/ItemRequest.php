<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
    public function rules() // 認証不要なら true にしてOK
    {
        return [
            'tab' => 'nullable|in:recommend,mylist',
            // 今後、検索や絞り込みを追加するならここに追加
            // 'category' => 'nullable|exists:categories,id',
            // 'keyword' => 'nullable|string|max:100',
        ];
    }

    public function validatedTab(): string
    {
        // デフォルトを 'recommend' に固定
        return $this->input('tab', 'recommend');
    }
}
