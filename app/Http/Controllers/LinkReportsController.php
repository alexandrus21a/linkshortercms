<?php

namespace App\Http\Controllers;

use Common\Core\BaseController;
use Illuminate\Http\Request;
use App\Actions\Link\GenerateLinkReport;

class LinkReportsController extends BaseController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function show()
    {
        $reports = app(GenerateLinkReport::class)
            ->execute($this->request->all());

        return $this->success(['reports' => $reports]);
    }
}
