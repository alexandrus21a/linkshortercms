<?php

namespace App\Policies;

use App\User;
use App\LinkOverlay;
use Common\Core\Policies\BasePolicy;

class LinkOverlayPolicy extends BasePolicy
{
    public function index(User $user, $userId = null)
    {
        return $user->hasPermission('link_overlays.view') || $user->id === (int) $userId;
    }

    public function show(User $user, LinkOverlay $linkOverlay)
    {
        return $user->hasPermission('link_overlays.view') || $linkOverlay->user_id === $user->id;
    }

    public function store(User $user)
    {
        return $this->storeWithCountRestriction($user, LinkOverlay::class);
    }

    public function update(User $user, LinkOverlay $linkOverlay)
    {
        return $user->hasPermission('link_overlays.update') || $linkOverlay->user_id === $user->id;
    }

    public function destroy(User $user, $linkOverlayIds)
    {
        if ($user->hasPermission('link_overlays.delete')) {
            return true;
        } else {
            $dbCount = app(LinkOverlay::class)
                ->whereIn('id', $linkOverlayIds)
                ->where('user_id', $user->id)
                ->count();
            return $dbCount === count($linkOverlayIds);
        }
    }
}
