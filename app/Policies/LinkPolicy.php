<?php

namespace App\Policies;

use App\Link;
use App\User;
use Common\Core\Policies\BasePolicy;

class LinkPolicy extends BasePolicy
{
    public function index(User $user, $userId = null)
    {
        return $user->hasPermission('links.view') || $user->id === (int) $userId;
    }

    public function show(User $user, Link $link)
    {
        return $user->hasPermission('links.view') || $link->user_id === $user->id;
    }

    public function store(User $user)
    {
        return $this->storeWithCountRestriction($user, Link::class);
    }

    public function update(User $user)
    {
        return $user->hasPermission('links.update');
    }

    public function destroy(User $user, $linkIds)
    {
        if ($user->hasPermission('links.delete')) {
            return true;
        } else {
            $dbCount = app(Link::class)
                ->whereIn('id', $linkIds)
                ->where('user_id', $user->id)
                ->count();
            return $dbCount === count($linkIds);
        }
    }
}
