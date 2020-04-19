<?php

namespace App\Console\Commands;

use App\Actions\Link\DeleteLinks;
use App\Link;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteExpiredLinks extends Command
{
    /**
     * @var string
     */
    protected $signature = 'links:delete_expired';

    /**
     * @var string
     */
    protected $description = 'Delete expired links.';

    /**
     * @var Link
     */
    private $link;

    /**
     * @param Link $link
     */
    public function __construct(Link $link)
    {
        parent::__construct();
        $this->link = $link;
    }

    public function handle()
    {
        $linkIds = $this->link->where('expires_at', '<', Carbon::now())->pluck('id');
        app(DeleteLinks::class)->execute($linkIds);
        $this->info('Deleted all expired links');
    }
}
