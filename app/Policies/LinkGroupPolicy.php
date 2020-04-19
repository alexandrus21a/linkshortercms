<?php

namespace App\Policies;

use App\LinkGroup;
use Common\Auth\BaseUser;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Http\Request;

class LinkGroupPolicy
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

    public function index(BaseUser $user)
    {
        return $user->hasPermission('link_groups.view');
    }

    public function show(BaseUser $user, LinkGroup $linkGroup)
    {
        return $user->hasPermission('link_groups.view') || $linkGroup->user_id === $user->id;
    }

    public function store(BaseUser $user)
    {
        return $user->hasPermission('link_groups.create');
    }

    public function update(BaseUser $user)
    {
        return $user->hasPermission('link_groups.update');
    }

    public function destroy(BaseUser $user, $groupIds)
    {
        if ($user->hasPermission('link_groups.delete')) {
            return true;
        } else {
            $dbCount = app(LinkGroup::class)
                ->whereIn('id', $groupIds)
                ->where('user_id', $user->id)
                ->count();
            return $dbCount === count($groupIds);
        }
    }
}
