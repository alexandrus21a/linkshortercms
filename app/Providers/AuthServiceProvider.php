<?php

namespace App\Providers;

use App\Link;
use App\LinkGroup;
use App\Policies\LinkGroupPolicy;
use App\Policies\LinkPolicy;
use App\LinkOverlay;
use App\Policies\LinkOverlayPolicy;
use App\Policies\TrackingPixelPolicy;
use App\TrackingPixel;
use Common\Admin\Appearance\Themes\CssTheme;
use Common\Admin\Appearance\Themes\CssThemePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        CssTheme::class => CssThemePolicy::class,
        LinkOverlay::class => LinkOverlayPolicy::class,
        Link::class => LinkPolicy::class,
        LinkGroup::class => LinkGroupPolicy::class,
        TrackingPixel::class => TrackingPixelPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
