<?php

namespace App\Actions\Link;

use DB;
use Storage;
use App\Link;
use App\LinkRule;
use App\LinkClick;
use App\LinkImage;
use Illuminate\Support\Collection;

class DeleteLinks
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

    /**
     * @param Collection|array $ids
     * @return bool
     */
    public function execute($ids)
    {
        $this->link->whereIn('id', $ids)->delete();

        // delete clicks
        app(LinkClick::class)->whereIn('link_id', $ids)->delete();

        // delete images
        $paths = app(LinkImage::class)->whereIn('link_id', $ids)->pluck('url');
        app(LinkImage::class)->whereIn('link_id', $ids)->delete();
        Storage::disk('public')->delete($paths);

        // delete rules
        app(LinkRule::class)->whereIn('link_id', $ids)->delete();

        // detach from groups
        DB::table('link_group_link')->whereIn('link_id', $ids)->delete();

        return true;
    }
}