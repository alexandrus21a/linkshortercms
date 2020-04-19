<?php

namespace App\Http\Controllers;

use App\Link;
use App\User;
use App\LinkClick;
use Common\Core\BaseController;

class HomepageStatsController extends BaseController
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @param Link $link
     */
    public function __construct(Link $link)
    {
        $this->link = $link;
    }

    public function getStats()
    {
        $data = ['stats' => [
            'links' => app(Link::class)->count(),
            'clicks' => app(LinkClick::class)->count(),
            'users' => app(User::class)->count(),
        ]];

        return $this->success($data, 200, [
            'prerender.config' => 'home.show',
        ]);
    }
}
