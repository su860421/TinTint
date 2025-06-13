<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderCurrencyEnum;
use Illuminate\Foundation\Http\FormRequest;

class OrderTransformRequest extends FormRequest
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
            'id' => ['required', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:255'],
            'address.city' => ['required', 'string', 'max:50'],
            'address.district' => ['required', 'string', 'max:50'],
            'address.street' => ['required', 'string', 'max:100'],
            'price' => ['required', 'integer', 'min:0'],
            'currency' => ['required', 'string', 'size:3', 'in:' . implode(',', OrderCurrencyEnum::values())],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'id.required' => '訂單 ID 為必填',
            'id.max' => '訂單 ID 不能超過 10 個字元',
            'name.required' => '姓名為必填',
            'name.max' => '姓名不能超過 255 個字元',
            'address.city.required' => '城市為必填',
            'address.city.max' => '城市不能超過 50 個字元',
            'address.district.required' => '區域為必填',
            'address.district.max' => '區域不能超過 50 個字元',
            'address.street.required' => '街道為必填',
            'address.street.max' => '街道不能超過 100 個字元',
            'price.required' => '價格為必填',
            'price.integer' => '價格必須為整數',
            'price.min' => '價格不能小於 0',
            'currency.required' => '幣別為必填',
            'currency.size' => '幣別必須為 3 個字元',
            'currency.in' => '無效的幣別',
        ];
    }
}
