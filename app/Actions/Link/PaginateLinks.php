<?php

namespace App\Actions\Link;

use App\Link;
use Common\Database\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class PaginateLinks
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
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function execute($params)
    {
        $paginator = new Paginator($this->link, $params);
        $paginator->filterColumns = ['password', 'disabled', 'expires_at', 'type'];
        $paginator->with(['rules', 'tags', 'pixels']);
        $paginator->withCount('clicks');

        $paginator->searchCallback = function(Builder $builder, $query) {
            return $builder->where('long_url', 'like', "%$query%")
                ->orWhere('hash', 'like', "%$query%");
        };

        if ($groupId = Arr::get($params, 'groupId')) {
            // get only links that either belong to specified group or belong to any group besides it
            $operator = str_contains($groupId, '!') ? '<' : '>=';
            $groupId = str_replace('!', '', $groupId);
            $paginator->query()->whereHas('groups', function(Builder $builder) use($groupId) {
                $builder->where('link_group_id', $groupId);
            }, $operator);
        }

        if ($userId = $paginator->param('userId')) {
            $paginator->where('user_id', $userId);
        }

        return $paginator->paginate();
    }
}