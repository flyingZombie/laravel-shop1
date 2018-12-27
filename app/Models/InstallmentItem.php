<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Moontoast\Math\BigNumber;

class InstallmentItem extends Model
{
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING => 'Pending',
        self::REFUND_STATUS_PROCESSING => 'Processing',
        self::REFUND_STATUS_SUCCESS => 'Success',
        self::REFUND_STATUS_FAILED => 'Failed',
    ];

    protected $fillable = [
        'sequence',
        'base',
        'fee',
        'fine',
        'due_date',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
    ];

    protected $dates = ['due_date', 'paid_at'];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    public function getTotalAttribute()
    {
        //$total = bcadd($this->base, $this->fee, 2);
        //$total = (new BigNumber($this->base, 2))->add($this->fee);
        $total = big_number($this->base)->add($this->fee);

        if (!is_null($this->fine)) {
            //$total = bcadd($total, $this->fine, 2);
            $total->add($this->fine);
        }
        //return $total;
        return $total->getValue();
    }

    public function getIsOverDueAttribute()
    {
        return Carbon::now()->gt($this->due_date);
    }
}
