<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Order extends Model
{
	const REFUND_STATUS_PENDING = 'pending';
	const REFUND_STATUS_APPLIED = 'applied';
	const REFUND_STATUS_PROCESSING = 'processing';
	const REFUND_STATUS_SUCCESS = 'success';
	const REFUND_STATUS_FAILED = 'failed';

	const SHIP_STATUS_PENDING = 'pending';
	const SHIP_STATUS_DELIVERED = 'delivered';
	const SHIP_STATUS_RECEIVED = 'received';

	const TYPE_SECKILL = 'seckill';

	public static $refundStatusMap = [
		self::REFUND_STATUS_PENDING => 'REFUND PENDING',
		self::REFUND_STATUS_APPLIED => 'REFUND APPLIED',
		self::REFUND_STATUS_PROCESSING => 'REFUND PROCESSING',
		self::REFUND_STATUS_SUCCESS => 'REFUND SUCCESS',
		self::REFUND_STATUS_FAILED => 'REFUND FAILED',
	];

	public static $shipStatusMap = [
		self::SHIP_STATUS_PENDING => 'SHIP PENDING',
		self::SHIP_STATUS_DELIVERED => 'SHIP DELIVERED',
		self::SHIP_STATUS_RECEIVED => 'SHIP RECEIVED',
	];

	const TYPE_NORMAL = 'normal';
	const TYPE_CROWDFUNDING = 'crowdfunding';

	public static $typeMap = [
        self::TYPE_NORMAL => 'normal product order',
        self::TYPE_CROWDFUNDING => 'crowd-funding product order',
        self::TYPE_SECKILL => 'Second-skill product order',
    ];

	protected $fillable = [
	    'type',
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];

    protected $casts = [
        'closed'    => 'boolean',
        'reviewed'  => 'boolean',
        'address'   => 'json',
        'ship_data' => 'json',
        'extra'     => 'json',
    ];

    protected $dates = [
        'paid_at',
    ];

    protected static function boot() {
    	parent::boot();
    	static::creating(function ($model)
    	{
    		if (!$model->no){
    			$model->no = static::findAvailableNo();
    			if (!$model->no) {
    				return false;
    			}
    		}
    	});
    }

    public static function findAvailableNo()
    {
    	$prefix = date('YmdHis');
    	for ($i = 0;$i < 10;$i++) {
    		$no = $prefix.str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    		if (!static::query()->where('no', $no)->exists()) {
    			return $no;
    		}
    	}
    	\Log::warning('find order no failed');

    	return false;
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function items()
    {
    	return $this->hasMany(OrderItem::class);
    }

    public function couponCode()
    {
        return $this->belongsTo(CouponCode::class);
    }

    public static function getAvailableRefundNo() {
        do {
            $no = Uuid::uuid4()->getHex();
        } while (self::query()->where('refund_no', $no)->exists());

        return $no;
    }
}
