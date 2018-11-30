<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\UserAddress;
use App\Policies\UserAddressPolicy;
use Monolog\Logger;
use Yansongda\Pay\Pay;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
        $this->app->singleton('alipay', function ()
        {
            $config = config('pay.alipay');
            //$config['notify_url'] = route('payment.alipay.notify');
            $config['notify_url'] = 'http://requestbin.leo108.com/xrr3noxr';
            $config['return_url'] = route('payment.alipay.return');
            if (app()->environment() !== 'product') {
                $config['mode'] = 'dev';
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            return Pay::alipay($config);
        });

        $this->app->singleton('wechat_pay', function ()
        {
            $config = config('pay.wechat');
            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            return Pay::wechat($config);
        });
        
    }

    protected $policies = [

    ];
}
