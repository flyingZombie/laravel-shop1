<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponCode extends Model
{
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [

        self::TYPE_FIXED => 'Fixed Amount',
        self::TYPE_PERCENT => 'Percentage',
    ];

    protected $fillable = [
      'name',
        'code',
        'type',
        'value',
        'total',
        'used',
        'min_amount',
        'not_before',
        'not_after',
        'enabled',
    ];

    protected $casts = [
      'enabled' => 'boolean',
    ];

    protected $dates = ['not_before', 'not_after'];

    public static function findAvailable($length = 16)
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::query()->where('code', $code)->exists());

        return $code;
    }

    protected $appends = ['description'];

    public function getDescriptionAttribute() {
        $str = '';

        if($this->min_amount > 0) {
            $str = 'If Over '.str_replace('.00', '',$this->min_amount);
        }

        if($this->type === self::TYPE_PERCENT) {
            return $str.' get discount by '.str_replace('.00', '', $this->value.'%');
        }
        return $str.' minus '.str_replace('.00', '', $this->value);
    }
}
