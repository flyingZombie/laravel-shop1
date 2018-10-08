<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
	protected $fillable = [

		'state',
		'city',
		'suburb',
		'address',
		'postcode',
		'contact_name',
		'contact_phone',
		'last_used_at',
	];
	protected $dates = ['last_used_at'];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function getFullAddressAttribute()
	{
		return "{$this->address},{$this->suburb},{$this->state}";
	}
}
