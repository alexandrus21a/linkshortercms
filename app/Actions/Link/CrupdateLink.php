<?php

namespace App\Actions\Link;

use Auth;
use App\Link;
use Common\Core\HttpClient;
use Common\Tags\Tag;
use Exception;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Arr;
use Symfony\Component\DomCrawler\Crawler;

class CrupdateLink
{
    /**
     * @var Link
     */
    private $link;

    /**
     * @var HttpClient
     */
    private $http;

    /**
     * @param Link $link
     */
    public function __construct(Link $link)
    {
        $this->link = $link;
        $this->http = new HttpClient(['verify' => false]);
    }

    /**
     * @param array $data
     * @param Link $link
     * @return Link
     */
    public function execute($data, $link = null)
    {
        $initial = [];
        $userTitle = Arr::get($data, 'title');
        $userDescription = Arr::get($data, 'description');
        if ( ! $link) {
            if ( ! $userTitle && ! $userDescription) {
                $initial = $this->getMetaFromUrl($data['long_url']);
            }
            // only set default hash if we are creating new link
            $initial['hash'] = str_random(5);
            $link = $this->link->newInstance($initial);
        }

        $domainId = Arr::get($data, 'domain_id');
        $attributes = [
            'long_url' => $data['long_url'],
            'description' => $userDescription ?: Arr::get($initial, 'description'),
            'title' => $userTitle ?: Arr::get($initial, 'title'),
            'expires_at' => Arr::get($data, 'expires_at'),
            'password' => Arr::get($data, 'password'),
            'disabled' => Arr::get($data, 'disabled') ?: false,
            'type' => Arr::get($data, 'type') ?: 'direct',
            'type_id' => Arr::get($data, 'type_id'),
            'user_id' => Arr::get($data, 'user_id', Auth::id()),
            'domain_id' => is_integer($domainId) ? $domainId : null, // can be 0
            'alias' => Arr::get($data, 'alias') ?: null,
        ];

        $link->fill($attributes)->save();

        if ($rules = Arr::get($data, 'rules')) {
            $link->rules()->delete();
            $rules = $link->rules()->createMany($rules);
            $link->setRelation('rules', $rules);
        }

        if ($tagNames = Arr::get($data, 'tags')) {
            $tags = collect($tagNames)->map(function($name) {
                return ['name' => $name, 'type' => Tag::DEFAULT_TYPE];
            });
            $tags = app(Tag::class)->insertOrRetrieve($tags);
            $link->tags()->sync($tags);
            $link->setRelation('tags', $tags);
        }

        if ($pixels = Arr::get($data, 'pixels')) {
            $link->pixels()->sync($pixels);
        }

        if ($groupId = Arr::get($data, 'group_id')) {
            $link->groups()->syncWithoutDetaching([$groupId]);
        }

        return $link;
    }

    private function getMetaFromUrl($url)
    {
        $default = ['title' => null, 'description' => null];

        // in case url is not reachable
        try {
            $content = $this->http->get($url);
        } catch (Exception $e) {
            return $default;
        }

        // if JSON response was returned
        if (is_array($content)) {
            return $default;
        }

        $crawler = new Crawler($content);
        $title = head($crawler->filter('title')->extract(['_text'])) ?: '';
        $description = head($crawler->filter('meta[name="description"]')->extract(['content'])) ?: '';

        return [
            'title' => str_limit($title, 100),
            'description' => str_limit($description, 200)
        ];
    }
}