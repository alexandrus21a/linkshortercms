<?php

namespace App\Actions\Overlay;

use Auth;
use App\Link;
use App\LinkOverlay;

class CrupdateLinkOverlay
{
    /**
     * @var Link
     */
    private $overlay;

    /**
     * @param LinkOverlay $overlay
     */
    public function __construct(LinkOverlay $overlay)
    {
        $this->overlay = $overlay;
    }

    /**
     * @param array $data
     * @param LinkOverlay $overlay
     * @return LinkOverlay
     */
    public function execute($data, $overlay = null)
    {
        if ( ! $overlay) {
            $overlay = $this->overlay->newInstance();
        }

        $attributes = [
            'name' => $data['name'],
            'position' => $data['position'],
            'message' => $data['message'],
            'label' => $data['label'],
            'btn_link' => $data['btn_link'],
            'btn_text' => $data['btn_text'],
            'colors' => $data['colors'],
            'user_id' => Auth::id(),
        ];

        $overlay->fill($attributes)->save();

        return $overlay;
    }
}