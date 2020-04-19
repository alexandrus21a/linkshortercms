<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Closure;
use App\Link;
use App\LinkRule;
use App\LinkClick;
use Common\Core\Prerender\HandlesSeo;
use Common\Domains\CustomDomain;
use Common\Settings\Settings;
use Illuminate\Http\Request;
use App\Actions\Link\LogLinkClick;

class RedirectIfLink
{
    use HandlesSeo;

    const CLIENT_ROUTES = [
        'dashboard',
        'billing',
        'admin',
        'contact',
        'update',
        'pages'
    ];

    /**
     * @var Link
     */
    private $link;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var CustomDomain
     */
    private $customDomain;

    /**
     * @param Link $link
     * @param Settings $settings
     * @param CustomDomain $customDomain
     */
    public function __construct(Link $link, Settings $settings, CustomDomain $customDomain)
    {
        $this->link = $link;
        $this->settings = $settings;
        $this->customDomain = $customDomain;
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $path = $this->getPath($request);
        if ($this->isLink($path)) {
            $link = $this->link
                ->with('pixels')
                ->where('hash', $path)
                ->orWhere('alias', $path)
                ->first();
            if ($link && $this->isValidDomain($request, $link) && !$this->overClickQuota($link)) {
                // need to call this after link has already been loaded
                $link->load('custom_page');
                $click = app(LogLinkClick::class)->execute($link);
                $link = $this->applyRules($link, $click);

                // set link on route so it can be used in blade redirect templates and frontend
                if ($link) {
                    // set seo meta tags on link response
                    $linkResponse = ['link' => $link];
                    $this->handleSeo($linkResponse, [
                        'prerender' => [
                            'config' => 'link.show',
                        ]
                    ]);
                    $request->route()->setParameter('linkResponse', $linkResponse);
                }

                // redirect to long url instantly if link type is "direct"
                if ($link && $link->type === 'direct' && !$link->password) {
                    return response()->view('redirects.direct', [], 301);
                }

                // pre-render links for crawlers
                if (isset($linkResponse)) {
                    if (defined('SHOULD_PRERENDER')) {
                        return response(view('prerender.link.show')->with('meta', $linkResponse['seo']));
                    } else {
                        // need to cast to array when returning data to client
                        // as laravel does not cast it correctly for some reason
                        $linkResponse['seo'] = $linkResponse['seo']->toArray();
                        $request->route()->setParameter('linkResponse', $linkResponse);
                    }
                }

                // other link types will be handled by frontend so
                // we can just continue with booting app normally
            }
        }

        return $next($request);
    }

    private function isValidDomain(Request $request, Link $link)
    {
        if ( ! $defaultHost = $this->settings->get('custom_domains.default_host')) {
            $defaultHost = config('app.url');
        }

        $currentHost = $this->removeWWWAndPort($request->getSchemeAndHttpHost());
        $defaultHost = $this->removeWWWAndPort($defaultHost);

        // link should only be accessible via single domain
        if ($link->domain_id > 0) {
            $domain = $this->customDomain->forUser($link->user_id)->find($link->domain_id);
            return $domain && $domain->host === $currentHost;
        }

        // link should be accessible via default domain only
        else if ($link->domain_id === 0) {
            return $currentHost === $defaultHost;
        }

        // link should be accessible via default and all user connected domains
        else {
            if ($currentHost === $defaultHost) return true;
            $domains = $this->customDomain->forUser($link->user_id)->get();
            return $domains->contains(function(CustomDomain $domain) use($currentHost) {
                return $domain->host === $currentHost;
            });
        }
    }

    private function removeWWWAndPort($url)
    {
        //remove scheme and port
        $url = parse_url($url)['host'];

        //remove www
        return preg_replace('/^www\./', '', $url);
    }

    /**
     * @param Link $link
     * @param LinkClick $click
     * @return Link
     */
    private function applyRules(Link $link, LinkClick $click)
    {
        // only apply the first matching rule
        $first = $link->rules->first(function(LinkRule $rule) use($click) {
            if ($rule->type === 'geo' && $this->settings->get('links.geo_targeting')) {
                return $click->location === $rule->key;
            } else if ($rule->type === 'device' && $this->settings->get('links.device_targeting')) {
                return $click->device === $rule->key;
            } else {
                return false;
            }
        });

        if ($first) {
            $link->long_url = $first->value;
        }

        return $link;
    }

    private function isLink($path)
    {
        return !str_contains($path, '/') && array_search($path, self::CLIENT_ROUTES) === false;
    }

    private function getPath(Request $request)
    {
        // if original url is specified get path from that url
        // this allows testing locally via bootstrap-data url
        if ($original = $request->get('original_url')) {
            $path = parse_url($original)['path'];
        } else {
            $path = parse_url($request->getUri())['path'];
        }

        return ltrim($path, '/');
    }

    private function overClickQuota(Link $link)
    {
        // link might not be attached to user
        if ( ! $link->user) {
            return false;
        }
        $quota = $link->user->getRestrictionValue('links.create', 'click_count');

        $range = CarbonPeriod::create(
            Carbon::now()->startOfMonth(),
            '1 month',
            Carbon::now()->endOfMonth()
        );

        $totalClicks = $link->clicks()
            ->where('crawler', false)
            ->whereBetween(
                'link_clicks.created_at',
                [$range->getStartDate(), $range->getEndDate()]
            )->count();

        return $quota < $totalClicks;
    }
}
