<?php

namespace App\Actions;

use App\LinkOverlay;
use App\TrackingPixel;
use Common\Core\Values\ValueLists;
use Illuminate\Contracts\Auth\Access\Gate;

class AppValueLists extends ValueLists
{
    public function overlays($params = [])
    {
        if ( ! isset($params['userId'])) {
            return collect([]);
        }

        app(Gate::class)->authorize('index', [LinkOverlay::class, $params['userId']]);

        return app(LinkOverlay::class)
            ->select(['id', 'name'])
            ->where('user_id', $params['userId'])
            ->get();
    }

    public function pixels($params = [])
    {
        if ( ! isset($params['userId'])) {
            return collect([]);
        }

        app(Gate::class)->authorize('index', [TrackingPixel::class, $params['userId']]);

        return app(TrackingPixel::class)
            ->select(['id', 'name'])
            ->where('user_id', $params['userId'])
            ->get();
    }
}
