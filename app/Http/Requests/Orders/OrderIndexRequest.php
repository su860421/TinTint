<?php

declare(strict_types=1);

namespace App\Http\Requests\Orders;

use Illuminate\Foundation\Http\FormRequest;

class OrderIndexRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'per_page' => ['integer', 'min:1', 'max:100'],
            'order_by' => ['in:order_number,status,total_amount,created_at,updated_at'],
            'order_direction' => ['in:asc,desc'],
            'filters' => ['array'],
            'filters.*' => ['array', 'size:3'],
            'filters.*.0' => ['string', 'in:order_number,status,total_amount,created_at'],
            'filters.*.1' => ['string', 'in:=,!=,>,>=,<,<=,like'],
            'filters.*.2' => ['required']
        ];
    }
}
