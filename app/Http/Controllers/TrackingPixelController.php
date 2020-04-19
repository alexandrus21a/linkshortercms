<?php

namespace App\Http\Controllers;

use App\Actions\TrackingPixel\CrupdateTrackingPixel;
use Auth;
use Common\Core\BaseController;
use Common\Database\Paginator;
use Illuminate\Http\Request;
use App\TrackingPixel;
use Illuminate\Http\Response;
use App\Http\Requests\CrupdateTrackingPixelRequest;

class TrackingPixelController extends BaseController
{
    /**
     * @var TrackingPixel
     */
    private $trackingPixel;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param TrackingPixel $trackingPixel
     * @param Request $request
     */
    public function __construct(TrackingPixel $trackingPixel, Request $request)
    {
        $this->trackingPixel = $trackingPixel;
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function index()
    {
        $userId = $this->request->get('userId');
        $this->authorize('index', [TrackingPixel::class, $userId]);

        $paginator = (new Paginator($this->trackingPixel, $this->request->all()));

        if ($userId) {
            $paginator->where('user_id', $userId);
        } else {
            $paginator->with('user');
        }

        $pagination = $paginator->paginate();

        return $this->success(['pagination' => $pagination]);
    }

    /**
     * @param TrackingPixel $trackingPixel
     * @return Response
     */
    public function show(TrackingPixel $trackingPixel)
    {
        $this->authorize('show', $trackingPixel);

        return $this->success(['trackingPixel' => $trackingPixel]);
    }

    /**
     * @param CrupdateTrackingPixelRequest $request
     * @return Response
     */
    public function store(CrupdateTrackingPixelRequest $request)
    {
        $this->authorize('store', TrackingPixel::class);

        $pixel = app(CrupdateTrackingPixel::class)->execute($request->all());

        return $this->success(['pixel' => $pixel]);
    }

    /**
     * @param TrackingPixel $trackingPixel
     * @param CrupdateTrackingPixelRequest $request
     * @return Response
     */
    public function update(TrackingPixel $trackingPixel, CrupdateTrackingPixelRequest $request)
    {
        $this->authorize('store', $trackingPixel);

        $pixel = app(CrupdateTrackingPixel::class)->execute($request->all(), $trackingPixel);

        return $this->success(['pixel' => $pixel]);
    }

    /**
     * @param string $ids
     * @return Response
     */
    public function destroy($ids)
    {
        $trackingPixelIds = explode(',', $ids);
        $this->authorize('store', [TrackingPixel::class, $trackingPixelIds]);

        $this->trackingPixel->whereIn('id', $trackingPixelIds)->delete();

        return $this->success();
    }
}
