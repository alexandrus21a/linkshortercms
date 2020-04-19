<?php

namespace App\Http\Controllers;

use App\Actions\Link\GenerateLinkReport;
use App\Actions\Link\PaginateLinks;
use App\LinkGroup;
use Auth;
use Common\Core\BaseController;
use Common\Database\Paginator;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class LinkGroupController extends BaseController
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
     * @param LinkGroup $linkGroup
     * @param Request $request
     */
    public function __construct(LinkGroup $linkGroup, Request $request)
    {
        $this->linkGroup = $linkGroup;
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function index()
    {
        $userId = $this->request->get('userId');
        $this->authorize('index', [LinkGroup::class, $userId]);

        $paginator = new Paginator($this->linkGroup, $this->request->all());
        $paginator->withCount('links');

        if ($userId) {
            $paginator->where('user_id', $userId);
        } else {
            $paginator->with('user');
        }

        return $this->success(
            ['pagination' => $paginator->paginate()]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param LinkGroup $linkGroup
     * @return Response
     */
    public function show(LinkGroup $linkGroup)
    {
        $this->authorize('show', $linkGroup);

        $reports = app(GenerateLinkReport::class)->execute($this->request->all(), $linkGroup);

        if ( ! $this->request->get('reportsOnly')) {
            $params = $this->request->all();
            $params['groupId'] = $linkGroup->id;
            $linkPagination = app(PaginateLinks::class)->execute($params);
        }

        return $this->success([
            'group' => $linkGroup,
            'reports' => $reports,
            'links' => isset($linkPagination) ? $linkPagination : null
        ]);
    }

    /**
     * @return Response
     */
    public function store()
    {
        $this->authorize('store', LinkGroup::class);

        $this->validate($this->request, [
            'name' => [
                'required', 'min:3', 'max:250',
                Rule::unique('link_groups')->where('user_id', Auth::id())
            ]
        ]);

        $group = $this->linkGroup->create([
            'name' => $this->request->get('name'),
            'user_id' => Auth::id(),
        ]);

        return $this->success(['group' => $group]);
    }

    /**
     * @param LinkGroup $linkGroup
     * @return Response
     */
    public function update(LinkGroup $linkGroup)
    {
       $this->authorize('update', $linkGroup);

        $this->validate($this->request, [
            'name' => [
                'required', 'min:3', 'max:250',
                Rule::unique('link_groups')
                    ->where('user_id', Auth::id())
                    ->ignore($linkGroup->id)
            ]
        ]);

        $linkGroup->fill([
            'name' => $this->request->get('name'),
        ])->save();

        return $this->success(['group' => $linkGroup]);
    }

    /**
     * @param string $ids
     * @return Response
     */
    public function destroy($ids)
    {
        $groupIds = explode(',', $ids);
        $this->authorize('destroy', [LinkGroup::class, $groupIds]);

        $this->linkGroup->whereIn('id', $groupIds)->delete();
        DB::table('link_group_link')->whereIn('link_group_id', $groupIds)->delete();

        return $this->success();
    }
}
