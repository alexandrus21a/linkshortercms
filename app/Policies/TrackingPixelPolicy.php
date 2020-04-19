<?php

namespace App\Policies;

use Common\Auth\BaseUser;
use Illuminate\Http\Request;
use App\TrackingPixel;
use Illuminate\Auth\Access\HandlesAuthorization;

class TrackingPixelPolicy
{
    use HandlesAuthorization;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(BaseUser $user, $userId = null)
    {
        return $user->hasPermission('tracking_pixels.view') || $user->id === (int) $userId;
    }

    public function show(BaseUser $user, TrackingPixel $trackingPixel)
    {
        return $user->hasPermission('tracking_pixels.view') || $trackingPixel->user_id === $user->id;
    }

    public function store(BaseUser $user)
    {
        return $user->hasPermission('tracking_pixels.create');
    }

    public function update(BaseUser $user, TrackingPixel $trackingPixel)
    {
        return $user->hasPermission('tracking_pixels.update') || $trackingPixel->user_id === $user->id;
    }

    public function destroy(BaseUser $user, $trackingPixelIds)
    {
        if ($user->hasPermission('tracking_pixels.delete')) {
            return true;
        } else {
            $dbCount = app(TrackingPixel::class)
                ->whereIn('id', $trackingPixelIds)
                ->where('user_id', $user->id)
                ->count();
            return $dbCount === count($trackingPixelIds);
        }
    }
}
