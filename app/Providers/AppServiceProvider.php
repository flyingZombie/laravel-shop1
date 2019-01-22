<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\UserAddress;
use App\Policies\UserAddressPolicy;
use Monolog\Logger;
use Yansongda\Pay\Pay;
use Elasticsearch\ClientBuilder as ESClientBuilder;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \View::composer(['products.index', 'products.show'], \App\Http\ViewComposers\CategoryTreeComposer::class);
        if (app()->environment('local')) {
            \DB::listen(function ($query) {
                \Log::info(Str::replaceArray('?', $query->bindings, $query->sql));
            });
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app->singleton('alipay', function ()
        {
            $config = config('pay.alipay');
            $config['notify_url'] = ngrok_url('payment.alipay.notify');
            //$config['notify_url'] = route('payment.alipay.notify');
            //$config['notify_url'] = 'http://requestbin.leo108.com/q48zsvq4';
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
            //$config['notify_url'] = route('payment.wechat.notify');
            $config['notify_url'] = ngrok_url('payment.wechat.notify');

            if (app()->environment() !== 'production') {
                $config['log']['level'] = Logger::DEBUG;
            } else {
                $config['log']['level'] = Logger::WARNING;
            }
            return Pay::wechat($config);
        });

        $this->app->singleton('es', function () {
          $builder = ESClientBuilder::create()->setHosts(config('database.elasticsearch.hosts'));

          if (app()->environment() === 'local') {
              $builder->setLogger(app('log')->getMonolog());
          }

          return $builder->build();

        });
        
    }

    protected $policies = [

    ];
}
