<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'state' => 'required',
            'suburb' => 'required',
            'address' => 'required',
            'postcode' => 'required',
            'contact_name' => 'required',
            'contact_phone' => 'required',
        ];
    }
}
