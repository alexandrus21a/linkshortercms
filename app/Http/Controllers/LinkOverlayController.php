<?php

namespace App\Http\Controllers;

use App\Actions\Overlay\CrupdateLinkOverlay;
use App\Http\Requests\CrupdateLinkOverlayRequest;
use App\Link;
use App\LinkOverlay;
use Common\Core\BaseController;
use Common\Database\Paginator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LinkOverlayController extends BaseController
{
    /**
     * @var LinkOverlay
     */
    private $linkOverlay;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param LinkOverlay $linkOverlay
     * @param Request $request
     */
    public function __construct(LinkOverlay $linkOverlay, Request $request)
    {
        $this->linkOverlay = $linkOverlay;
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function index()
    {
        $userId = $this->request->get('userId');
        $this->authorize('index', [LinkOverlay::class, $userId]);

        $paginator = new Paginator($this->linkOverlay, $this->request->all());

        if ($userId) {
            $paginator->where('user_id', $userId);
        } else {
            $paginator->with('user');
        }

        $pagination = $paginator->paginate();

        return $this->success(['pagination' => $pagination]);
    }

    /**
     * @param LinkOverlay $linkOverlay
     * @return Response
     */
    public function show(LinkOverlay $linkOverlay)
    {
        $this->authorize('show', $linkOverlay);

        return $this->success(['linkOverlay' => $linkOverlay]);
    }

    /**
     * @param CrupdateLinkOverlayRequest $request
     * @return Response
     */
    public function store(CrupdateLinkOverlayRequest $request)
    {
        $this->authorize('store', LinkOverlay::class);

        $linkOverlay = app(CrupdateLinkOverlay::class)->execute(
            $request->all()
        );

        return $this->success(['linkOverlay' => $linkOverlay]);
    }

    /**
     * @param CrupdateLinkOverlayRequest $request
     * @param LinkOverlay $linkOverlay
     * @return Response
     */
    public function update(CrupdateLinkOverlayRequest $request, LinkOverlay $linkOverlay)
    {
        $this->authorize('store', $linkOverlay);

        $linkOverlay = app(CrupdateLinkOverlay::class)->execute(
            $request->all(),
            $linkOverlay
        );

        return $this->success(['linkOverlay' => $linkOverlay]);
    }

    /**
     * @param string $ids
     * @return Response
     */
    public function destroy($ids)
    {
        $overlayIds = explode(',', $ids);
        $this->authorize('destroy', [LinkOverlay::class, $overlayIds]);

        $this->linkOverlay->whereIn('id', $overlayIds)->delete();

        // set links to which this overlay is attached to "direct" type
        app(Link::class)
            ->whereIn('type_id', $overlayIds)
            ->where('type', 'overlay')
            ->update(['type_id' => null, 'type' => 'direct']);

        return $this->success();
    }
}
