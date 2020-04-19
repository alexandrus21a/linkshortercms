<?php

namespace App\Http\Controllers;

use App\LinkGroup;
use Common\Core\BaseController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LinkGroupAttachmentsController extends BaseController
{
    /**
     * @var LinkGroup
     */
    private $linkGroup;

    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     * @param LinkGroup $linkGroup
     */
    public function __construct(Request $request, LinkGroup $linkGroup)
    {
        $this->linkGroup = $linkGroup;
        $this->request = $request;
    }

    /**
     * @param LinkGroup $linkGroup
     * @return Response
     */
    public function attach(LinkGroup $linkGroup)
    {
        $this->authorize('update', $linkGroup);

        $this->validate($this->request, [
            'linkIds' => 'required|array',
            'linkIds.*' => 'required|integer'
        ]);

        $linkGroup->links()->syncWithoutDetaching($this->request->get('linkIds'));

        return $this->success();
    }

    /**
     * @param LinkGroup $linkGroup
     * @return Response
     */
    public function detach(LinkGroup $linkGroup)
    {
        $this->authorize('update', $linkGroup);

        $this->validate($this->request, [
            'linkIds' => 'required|array',
            'linkIds.*' => 'required|integer'
        ]);

        $linkGroup->links()->detach($this->request->get('linkIds'));

        return $this->success();
    }
}
