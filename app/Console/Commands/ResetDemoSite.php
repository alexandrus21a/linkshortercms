<?php

namespace App\Console\Commands;

use App\Actions\Link\CrupdateLink;
use App\Link;
use App\LinkClick;
use App\LinkGroup;
use App\User;
use Artisan;
use Common\Auth\Permissions\Permission;
use Common\Auth\Permissions\Traits\SyncsPermissions;
use Common\Localizations\Localization;
use DB;
use Hash;
use Illuminate\Console\Command;

class ResetDemoSite extends Command
{
    use SyncsPermissions;

    /**
     * @var string
     */
    protected $signature = 'demoSite:reset';

    /**
     * @return void
     */
    public function handle()
    {
        if ( ! config('common.site.demo')) {
            $this->error('This is not a demo site.');
            return;
        }

        // reset admin user
        $admin = $this->resetAdminUser('admin@admin.com');

        // remove all links and clicks
        DB::table('links')->truncate();
        DB::table('link_clicks')->truncate();
        DB::table('link_group_link')->truncate();
        DB::table('link_images')->truncate();
        DB::table('link_rules')->truncate();
        DB::table('link_tracking_pixel')->truncate();

        $this->seedAdminLinks($admin);

        // delete localizations
        app(Localization::class)->get()->each(function(Localization $localization) {
            if (strtolower($localization->name) !== 'english') {
                $localization->delete();
            }
        });

        Artisan::call('cache:clear');

        $this->info('Demo site reset successfully');
    }

    private function resetAdminUser($email)
    {
        $admin = app(User::class)
            ->where('email', $email)
            ->first();

        if ( ! $admin) return;

        $adminPermission = app(Permission::class)->where('name', 'admin')->first();

        $resourcePermissions = app(Permission::class)->whereIn('name', [
            'links.create', 'link_overlays.create', 'custom_pages.create',
            'custom_domains.create', 'link_groups.create', 'tracking_pixels.create',
        ])->get();

        $resourcePermissions = $resourcePermissions->map(function(Permission $permission) {
            switch ($permission['name']) {
                case 'links.create':
                    $permission['restrictions'] = [['name' => 'count', 'value' => 500], ['name' => 'click_count', 'value' => 10000]];
                    break;
                default:
                    $permission['restrictions'] = [['name' => 'count', 'value' => 100]];
            }
            return $permission;
        });

        $admin->avatar = null;
        $admin->username = 'admin';
        $admin->first_name = 'Demo';
        $admin->last_name = 'Admin';
        $admin->password = Hash::make('admin');
        $admin->save();
        $this->syncPermissions($admin, $resourcePermissions->push($adminPermission));

        $admin->link_groups()->delete();
        $admin->custom_domains()->delete();
        $admin->link_overlays()->delete();
        $admin->link_custom_pages()->delete();
        $admin->tracking_pixels()->delete();
        return $admin;
    }

    private function seedAdminLinks(User $admin)
    {
        $urls = [
            ['long_url' => 'https://google.com', 'title' => 'Google'],
            ['long_url' => 'https://yahoo.com', 'title' => 'Yahoo', 'description' => 'News, email and search are just the beginning. Discover more every day. Find your yodel.'],
            ['long_url' => 'https://youtube.com', 'title' => 'Youtube', 'description' => 'Enjoy the videos and music you love, upload original content, and share it all with friends, family, and the world on YouTube.'],
            ['long_url' => 'https://imdb.com', 'title' => 'Ratings and Reviews for New Movies and TV Shows - IMDb', 'description' => 'IMDb is the world\'s most popular and authoritative source for movie, TV and celebrity content. Find ratings and reviews for the newest movie and TV shows.'],
            ['long_url' => 'https://wikipedia.com', 'title' => 'Wikipedia', 'description' => 'Wikipedia is a free online encyclopedia, created and edited by volunteers around the world and hosted by the Wikimedia Foundation.'],
            ['long_url' => 'https://reddit.com', 'title' => 'reddit: the front page of the internet', 'description' => 'Reddit is a network of communities based on people\'s interests. Find communities you\'re interested in, and become part of an online community!'],
            ['long_url' => 'https://amazon.com', 'title' => 'Amazon.com: Online Shopping for Electronics, Apparel, Computers, Books, DVDs & more', 'description' => 'Online shopping from the earth\'s biggest selection of books, magazines, music, DVDs, videos, electronics, computers, software, apparel & accessories, shoes, jewelry, tools & hardware, housewares, furn...'],
            ['long_url' => 'https://twitter.com', 'title' => 'Twitter. It\'s what\'s happening.', 'description' => 'From breaking news and entertainment to sports and politics, get the full story with all the live commentary.'],
        ];

        $links = collect($urls)->map(function($urlData) use($admin) {
            $urlData['user_id'] = $admin->id;
            $link = app(CrupdateLink::class)->execute($urlData);
            factory(LinkClick::class, 50)->create([
                'link_id' => $link->id,
                'link_type' => $link->type,
            ]);
            return $link;
        });

        $group = app(LinkGroup::class)->create([
            'name' => 'Demo Links',
            'user_id' => $admin->id,
        ]);
        $group->links()->sync($links->pluck('id'));
    }
}
