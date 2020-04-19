<?php

namespace App\Http\Controllers;

use App\Link;
use Carbon\Carbon;
use Common\Core\BaseController;
use Common\Core\HttpClient;
use GuzzleHttp\Exception\ConnectException;
use Storage;

class LinkImageController extends BaseController
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @param Link $link
     */
    public function __construct(Link $link)
    {
        $this->link = $link;
        $this->httpClient = new HttpClient(['timeout' => 5]);
    }

    /**
     * @param string $hash
     * @return string
     */
    public function show($hash)
    {
        $link = $this->link->where('hash', $hash)->firstOrFail();
        $this->authorize('show', $link);

        // Don't create image if request is coming from a crawler
        if (defined('SHOULD_PRERENDER')) {
            return $link->image;
        }

        $path = "link_images/{$link->hash}.jpg";

        if ( ! $link->image) {
            $this->createImage($link, $path);
            $link->image()->create(['url' => $path]);

        // update every week
        } else if ($link->updated_at->lessThan(Carbon::now()->subWeek()))  {
            $this->createImage($link, $path);
        }

        return Storage::disk('public')->get($path);
    }

    private function createImage(Link $link, $path)
    {
        try {
            $googlePagespeedData = $this->httpClient->get("https://www.googleapis.com/pagespeedonline/v2/runPagespeed?url={$link->long_url}&screenshot=true");
        } catch (ConnectException $e) {
            // timeout
            $defaultImage = file_get_contents(public_path('client/assets/images/default-link-image.jpg'));
            Storage::disk('public')->put($path, $defaultImage);
            return;
        }

        if (isset($googlePagespeedData['screenshot']['data'])) {
            $data = $googlePagespeedData['screenshot']['data'];
            $data = str_replace(['_','-'], ['/','+'], $data);
            $data = base64_decode($data);

            Storage::disk('public')->put($path, $data);
        }
    }
}
