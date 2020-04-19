<?php

namespace App\Providers;

use App\Actions\Admin\GetAppAnalyticsData;
use App\Actions\AppBootstrapData;
use App\Actions\AppValueLists;
use Common\Admin\Analytics\Actions\GetAnalyticsData;
use Common\Core\Values\ValueLists;
use Illuminate\Support\ServiceProvider;
use Common\Core\Bootstrap\BootstrapData;
use App\Actions\Admin\GetAnalyticsHeaderData;
use Common\Admin\Analytics\Actions\GetAnalyticsHeaderDataAction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * @return void
     */
    public function register()
    {
        // bind analytics
        $this->app->bind(
            GetAnalyticsHeaderDataAction::class,
            GetAnalyticsHeaderData::class
        );

        $this->app->bind(
            GetAnalyticsData::class,
            GetAppAnalyticsData::class
        );

//        $this->app->bind(
//            AppUrlGenerator::class,
//            UrlGenerator::class
//        );

        $this->app->bind(
            BootstrapData::class,
            AppBootstrapData::class
        );

        $this->app->bind(ValueLists::class, AppValueLists::class);
    }
}
