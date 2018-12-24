<?php

namespace App\Console\Commands\Cron;

use App\Jobs\RefundCrowdfundingOrders;
use Illuminate\Console\Command;
use App\Models\CrowdfundingProduct;
use App\Models\Order;
use Carbon\Carbon;
use App\Services\OrderService;


class FinishCrowdfunding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:finish-crowdfunding';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'End crowd-funding';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        CrowdfundingProduct::query()->with(['product'])
                                    ->where('end_at', '<=', Carbon::now())
                                    ->where('status', CrowdfundingProduct::STATUS_FUNDING)
                                    ->get()
                                    ->each(function (CrowdfundingProduct $crowdfundingProduct) {
                                      if ($crowdfundingProduct->target_amount > $crowdfundingProduct->total_amount) {
                                          $this->crowdfundingFailed($crowdfundingProduct);
                                      } else {
                                          $this->crowdfundingSucceed($crowdfundingProduct);
                                      }
                                    });
    }

    protected function crowdfundingSucceed(CrowdfundingProduct $crowdfundingProduct)
    {
      $crowdfundingProduct->update([
        'status' => CrowdfundingProduct::STATUS_SUCCESS,
      ]);
    }

    protected function crowdfundingFailed(CrowdfundingProduct $crowdfundingProduct)
    {
        $crowdfundingProduct->update([
            'status' => CrowdfundingProduct::STATUS_FAIL,
        ]);
        /*
        $orderService = app(OrderService::class);
        Order::query()
            ->where('type', Order::TYPE_CROWDFUNDING)
            ->whereNotNull('paid_at')
            ->whereHas('items', function ($query) use ($crowdfundingProduct) {
                $query->where('product_id', $crowdfundingProduct->product_id);
            })
            ->get()
            ->each(function (Order $order) use($orderService) {
                $orderService->refundOrder($order);
            });
        */
        dispatch(new RefundCrowdfundingOrders($crowdfundingProduct));
    }
}
