<?php

namespace App\Http\Requests;

use App\Models\CrowdfundingProduct;
use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Validation\Rule;

class CrowdFundingOrderRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    /*
    public function authorize()
    {
        return false;
    }
    */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'sku_id' => [
                'required',
                function ($attribute, $value, $fail) {

                    if (!$sku = ProductSku::find($value)) {
                        return $fail('This sku doesn\'t exist ');
                    }

                    if ($sku->product->type !== Product::TYPE_CROWDFUNDING) {
                        return $fail('This product doesn\'t support crowd funding');
                    }

                    if (!$sku->product->on_sale) {
                        return $fail('This product is\'t for sale');
                    }

                    if ($sku->product->crowdfunding->status !== CrowdfundingProduct::STATUS_FUNDING) {
                        return $fail('This crowd funding is finished');
                    }

                    if ($sku->stock === 0) {
                        return $fail('This product is sold out');
                    }

                    if ($this->input('amount') > 0 && $sku->stock < $this->input('amount')) {
                        return $fail('This product is out of stock');
                    }
                },
            ],
            'amount' => ['required', 'integer', 'min:1'],
            'address_id' => [
                'required',
                Rule::exists('user_addresses', 'id')->where('user_id',$this->user()->id),
            ],
        ];
    }
}
