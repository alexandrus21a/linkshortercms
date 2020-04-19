<?php

namespace App\Actions\TrackingPixel;

use Auth;
use App\TrackingPixel;
use Illuminate\Support\Arr;

class CrupdateTrackingPixel
{
    /**
     * @var TrackingPixel
     */
    private $trackingPixel;

    /**
     * @param TrackingPixel $trackingPixel
     */
    public function __construct(TrackingPixel $trackingPixel)
    {
        $this->trackingPixel = $trackingPixel;
    }

    /**
     * @param array $data
     * @param TrackingPixel $trackingPixel
     * @return TrackingPixel
     */
    public function execute($data, $trackingPixel = null)
    {
        if ( ! $trackingPixel) {
            $trackingPixel = $this->trackingPixel->newInstance([
                'user_id' => Auth::id()
            ]);
        }

        $attributes = [
            'name' => $data['name'],
            'type' => $data['type'],
            'pixel_id' => $data['pixel_id'],
            'head_code' => Arr::get($data, 'head_code'),
            'body_code' => Arr::get($data, 'body_code'),
        ];

        $trackingPixel->fill($attributes)->save();

        return $trackingPixel;
    }
}