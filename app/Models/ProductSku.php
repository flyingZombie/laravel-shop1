<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\InternalException;

class ProductSku extends Model
{
	protected $fillable = ['title', 'description', 'price', 'stock'];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function decreaseStock($amount)
	{
		if ($amount < 0) {
			throw new InternalException('Stock must stay above zero. No decrease allowed!');
		}

		return $this->newQuery()->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
	}

	public function addStock($amount)
	{
		if ($amount < 0) {
			throw new InternalException('No negative figure allowed for adding stock!');
		}
		$this->increment('stock', $amount);
	}
}
