<?php

namespace App\Http\Controllers;

use App\Actions\Link\CrupdateLink;
use App\Actions\Link\DeleteLinks;
use App\Actions\Link\GenerateLinkReport;
use App\Actions\Link\PaginateLinks;
use App\Http\Requests\CrupdateLinkRequest;
use App\Link;
use Common\Core\BaseController;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LinkController extends BaseController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Link
     */
    private $link;

    /**
     * @param Request $request
     * @param Link $link
     */
    public function __construct(Request $request, Link $link)
    {
        $this->request = $request;
        $this->link = $link;
    }

    /**
     * @return JsonResponse|Response
     */
    public function index()
    {
        $userId = $this->request->get('userId');
        $this->authorize('index', [Link::class, $userId]);

        $pagination = app(PaginateLinks::class)
            ->execute($this->request->all());

        return $this->success(
            ['pagination' => $pagination]
        );
    }

    /**
     * @param Link $link
     * @return Response
     */
    public function show(Link $link)
    {
        $this->authorize('show', $link);

        $reports = app(GenerateLinkReport::class)->execute($this->request->all(), $link);

        return $this->success(['link' => $link, 'reports' => $reports]);
    }

    /**
     * @param CrupdateLinkRequest $request
     * @return Response
     */
    public function store(CrupdateLinkRequest $request)
    {
        $this->authorize('store', Link::class);
        $data = $request->all();

        if ($multipleUrls = $request->get('multiple_urls')) {
            $multipleUrls = collect($multipleUrls)->unique()->map(function($longUrl) use($data) {
                $data['long_url'] = $longUrl;
                try {
                    return app(CrupdateLink::class)->execute($data);
                } catch (Exception $e) {
                    return null;
                }
            })->filter();
            return $this->success(['links' => $multipleUrls]);
        } else {
            $link = app(CrupdateLink::class)->execute($request->all());
            return $this->success(['link' => $link]);
        }
    }

    /**
     * @param CrupdateLinkRequest $request
     * @param Link $link
     * @return Response
     */
    public function update(CrupdateLinkRequest $request, Link $link)
    {
        $this->authorize('update', $link);

        $link = app(CrupdateLink::class)->execute($request->all(), $link);

        return $this->success(['link' => $link]);
    }

    /**
     * @param string $ids
     * @return Response
     */
    public function destroy($ids)
    {
        $linkIds = explode(',', $ids);
        $this->authorize('destroy', [Link::class, $linkIds]);

        app(DeleteLinks::class)->execute($linkIds);

        return $this->success();
    }
}
