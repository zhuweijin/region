<?php

namespace App\Providers;

use App\Service\RegionService;
use Illuminate\Support\ServiceProvider;

class RegionServiceProvider extends ServiceProvider
{
    /**
     * 指定是否延缓提供者加载。
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * 注册服务提供者。
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('', function()
        {
            return new RegionService();
        });
    }

    /**
     * 取得提供者所提供的服务。
     *
     * @return array
     */
    public function provides()
    {
        return [RegionService::class];
    }

}
