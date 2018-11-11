<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\Models\ProductSku;

class OrderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address_id' => ['required', Rule::exists('user_addresses', 'id')->where('user_id', $this->user()->id)],
            'items' => ['required', 'array'],
            'items.*.sku_id' => [
                'required',
                function ($attribute, $value, $fail)
                {
                    if (!$sku = ProductSku::find($value)) {
                        $fail('This product does not exist');
                        return;
                    }
                    if (!$sku->product->on_sale) {
                        $fail('This product is not for sale');
                        return;
                    }
                    if ($sku->stock === 0) {
                        $fail('This product is sold out');
                        return;
                    }
                    preg_match('/items\.(\d+)\.sku_id/', $attribute, $m);
                    $index = $m[1];

                    $amount = $this->input('items')[$index]['amount'];

                    if ($amount > 0 && $amount > $sku->stock) {
                        $fail('This product is out of stock!');
                        return;
                    } 
                },
            ],
            'items.*.amount' => ['required', 'integer', 'min:1'],
        ];
    }
}
