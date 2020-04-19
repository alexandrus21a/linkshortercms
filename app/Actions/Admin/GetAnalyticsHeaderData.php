<?php

namespace App\Actions\Admin;

use App\Link;
use App\LinkGroup;
use App\LinkOverlay;
use App\User;
use Common\Admin\Analytics\Actions\GetAnalyticsHeaderDataAction;

class GetAnalyticsHeaderData implements GetAnalyticsHeaderDataAction
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Link
     */
    private $link;

    /**
     * @param Link $link
     * @param User $user
     */
    public function __construct(Link $link, User $user)
    {
        $this->user = $user;
        $this->link = $link;
    }

    public function execute($channel)
    {
        return [
            [
                'icon' => 'link',
                'name' => 'Total Links',
                'type' => 'number',
                'value' => $this->link->count(),
            ],
            [
                'icon' => 'copy-link',
                'name' => 'Total Groups',
                'type' => 'number',
                'value' => app(LinkGroup::class)->count(),
            ],
            [
                'icon' => 'tooltip',
                'name' => 'Total Overlays',
                'type' => 'number',
                'value' => app(LinkOverlay::class)->count(),
            ],
            [
                'icon' => 'people',
                'name' => 'Total Users',
                'type' => 'number',
                'value' => $this->user->count(),
            ],
        ];
    }
}