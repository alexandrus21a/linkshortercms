<?php

namespace App\Http\Controllers;

use App\Link;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\Image\Png;
use Common\Core\BaseController;

class QrCodeController extends BaseController
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

    public function show($linkHash)
    {
        $link = $this->link
            ->where('hash', $linkHash)
            ->orWhere('alias', $linkHash)
            ->firstOrFail();

        $this->authorize('show', $link);

        $renderer = new Png();
        $renderer->setWidth(160);
        $renderer->setHeight(160);

        $writer = new Writer($renderer);
        $response = $writer->writeString("{$link->short_url}?source=qr");

        return response()->stream(function() use($response) {
            echo $response;
        }, 200, ['Content-Type' => 'image/png', 'Content-Length: ' . strlen($response)]);
    }
}
