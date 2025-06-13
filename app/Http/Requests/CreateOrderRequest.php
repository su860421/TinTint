<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => '使用者 ID 為必填',
            'user_id.exists' => '使用者不存在',
            'items.required' => '訂單項目為必填',
            'items.array' => '訂單項目必須為陣列',
            'items.min' => '訂單至少要有一個項目',
            'items.*.product_id.required' => '商品 ID 為必填',
            'items.*.product_id.exists' => '商品不存在',
            'items.*.quantity.required' => '商品數量為必填',
            'items.*.quantity.integer' => '商品數量必須為整數',
            'items.*.quantity.min' => '商品數量必須大於 0',
        ];
    }
}
