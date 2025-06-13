<?php

namespace App\Http\Requests;

use App\Enums\OrderStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in(OrderStatusEnum::values())
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => '訂單狀態為必填',
            'status.in' => '無效的訂單狀態'
        ];
    }
}
