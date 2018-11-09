<?php

namespace App\Http\Requests;

use App\Models\ProductSku;

class AddCartRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'amount' => ['required', 'integer', 'min:1'],

            'sku_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!$sku = ProductSku::find($value)) {
                        $fail('this product does not exist');
                        return;
                    }
                    if (!$sku->product->on_sale) {
                        $fail('this product is not for sale');
                        return;
                    }
                    if ($sku->stock === 0) {
                        $fail('this product is sold out');
                        return;
                    }
                    if ($this->input('amount') > 0 && $sku->stock < $this->input('amount')) {
                        $fail('this product has no enough stock');
                        return;
                    }
                },
            ],
        ];
    }

    public function attributes()
    {
        return [
            'amount' => 'Quantity of Goods'
        ];
    }

    public function message()
    {
        return [
            'sku_id.required' => 'Please select the product'

        ];
    }
}
