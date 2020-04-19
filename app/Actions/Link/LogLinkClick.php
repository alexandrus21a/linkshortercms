<?php


namespace App\Actions\Link;

use App\Link;
use App\LinkClick;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class LogLinkClick
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Agent
     */
    private $agent;

    /**
     * @param Link $link
     * @param Request $request
     * @param Agent $agent
     */
    public function __construct(Link $link, Request $request, Agent $agent)
    {
        $this->link = $link;
        $this->request = $request;
        $this->agent = $agent;
    }

    /**
     * @param Link $link
     * @return LinkClick
     */
    public function execute(Link $link)
    {
        if ($link) {
            return $click = $this->log($link);
        }
    }

    /**
     * @param Link $link
     * @return LinkClick
     */
    private function log(Link $link)
    {
        $referrer = $this->request->server('HTTP_REFERER');

        $attributes = [
            'link_type' => $link->type,
            'location' => $this->getLocation(),
            'ip' => $this->request->ip(),
            'platform' => strtolower($this->agent->platform()),
            'device' => $this->getDevice(),
            'crawler' => $this->agent->isRobot(),
            'browser' => strtolower($this->agent->browser()),
            // if referrer was any page from our site set referrer as null
            'referrer' => str_contains($referrer, url('')) ? null : $referrer,
        ];

        return $link->clicks()->create($attributes);
    }

    private function getDevice() {
        if ($this->agent->isMobile()) {
            return 'mobile';
        } else if ($this->agent->isTablet()) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    private function getLocation()
    {
        return strtolower(geoip($this->request->ip())['iso_code']);
    }
}