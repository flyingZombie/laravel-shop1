<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSku;
use Illuminate\Validation\Rule;
use Illuminate\AuthenticationException;
use App\Exceptions\InvalidRequestException;


class SeckillOrderRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [

            'address.state' => 'required',

            'address.suburb' => 'required',

            'address.address' => 'required',

            'address.postcode' => 'required',

            'address.contact_name' => 'required',

            'address.contact_phone' => 'required',

            'sku_id' => [

                'required',

                function($attribute, $value, $fail) {
                    /*
                    if (!$sku = ProductSku::find($value)) {
                        return $fail('This product doesn\'t exist');
                    }
                    */

                    $stock = \Redis::get('seckill_sku_'.$value);

                    if (is_null($stock)) {
                        return $fail('This product doesn\'t exist');
                    }

                    if ($stock < 1) {
                        return $fail('This product is sold out');
                    }

                    $sku = ProductSku::find($value);

                    /*
                    if ($sku->product->type !== Product::TYPE_SECKILL) {
                        return $fail('This product doesn\'t support second-skill');
                    }
                    */

                    if ($sku->product->seckill->is_before_start) {
                        return $fail('Second-kill not started yest');
                    }
                    if ($sku->product->seckill->is_after_end) {
                        return $fail('Second-kill is already finished!');
                    }
                    if (!$sku->product->on_sale) {
                        return $fail('This product is not for sale yet');
                    }
                    if ($sku->stock < 1) {
                        return $fail('This product is sold out');
                    }

                    if (!$user = \Auth::user()) {
                        throw new AuthenticationException('Please log in first');
                    }

                    if (!$user->email_verified) {
                        throw new InvalidRequestException('Please verify email first');
                    }

                    if ($order = Order::query()
                        ->where('user_id', $this->user()->id)
                        ->whereHas('items', function ($query) use ($value) {
                            $query->where('product_sku_id', $value);
                        })
                        ->where(function ($query) {
                            $query->whereNotNull('paid_at')->orWhere('closed', false);
                        })
                        ->first()) {
                        if ($order->paid_at) {
                            return $fail('You already brought this product');
                        }
                        return $fail('You already ordered this product, please make the payment at order page');
                    }
                },
            ]
        ];
    }
}
