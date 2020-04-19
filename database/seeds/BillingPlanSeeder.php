<?php

use Common\Auth\Permissions\Permission;
use Common\Billing\BillingPlan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class BillingPlanSeeder extends Seeder
{
    /**
     * @var BillingPlan
     */
    private $plan;

    /**
     * @param BillingPlan $plan
     */
    public function __construct(BillingPlan $plan)
    {
        $this->plan = $plan;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if ($this->plan->count() === 0 && config('common.site.demo')) {
            $permissions = app(Permission::class)->pluck('id', 'name');

            $this->createPlan($permissions, [
                'name' => 'Basic',
                'amount' => 10,
                'position' => 1,
                'visitors' => 1000,
                'links' => 50,
                'pages' => 10,
                'groups' => 10,
                'overlays' => 10,
                'pixels' => 10,
            ]);

            $this->createPlan($permissions, [
                'name' => 'Standard',
                'amount' => 20,
                'position' => 2,
                'recommended' => true,
                'visitors' => 3000,
                'links' => 150,
                'pages' => 30,
                'groups' => 30,
                'overlays' => 30,
                'pixels' => 30,
            ]);

            $this->createPlan($permissions, [
                'name' => 'Pro',
                'amount' => 50,
                'position' => 3,
            ]);
        }
    }

    private function createPlan($permissions, $params)
    {
        $basic = $this->plan->create([
            'name' => $params['name'],
            'uuid' => str_random(36),
            'amount' => $params['amount'],
            'currency' => 'USD',
            'currency_symbol' => '$',
            'interval' => 'month',
            'interval_count' => 1,
            'position' => $params['position'],
            'recommended' => Arr::get($params, 'recommended', false),
            'features' => [
                isset($params['visitors']) ? "Up to {$params['visitors']} visitors / month" : 'Unlimited visitors / month',
                isset($params['links']) ? "Up to {$params['links']} links" : 'Unlimited links',
                isset($params['pages']) ? "Up to {$params['pages']} custom link pages" : 'Unlimited link pages',
                isset($params['groups']) ? "Up to {$params['groups']} link groups" : 'Unlimited link groups',
                isset($params['overlays']) ? "Up to {$params['overlays']} link overlays" : 'Unlimited link overlays',
                isset($params['pixels']) ? "Up to {$params['pixels']} tracking pixels" : 'Unlimited tracking pixels',
            ]
        ]);

        $newPermissions = [];

        if (isset($params['links']) && isset($params['visitors'])) {
            $newPermissions[$permissions['links.create']] = [
                'restrictions' => json_encode([['name' => 'count', 'value' => $params['links']], ['name' => 'click_count', 'value' => $params['visitors']]])
            ];
        }

        if (isset($params['custom_pages.create'])) {
            $newPermissions[$permissions['custom_pages.create']] = [
                'restrictions' => json_encode([['name' => 'count', 'value' => $params['links']]])
            ];
        }

        if (isset($params['link_groups.create'])) {
            $newPermissions[$permissions['link_groups.create']] = [
                'restrictions' => json_encode([['name' => 'count', 'value' => $params['links']]])
            ];
        }

        if (isset($params['link_overlays.create'])) {
            $newPermissions[$permissions['link_overlays.create']] = [
                'restrictions' => json_encode([['name' => 'count', 'value' => $params['links']]])
            ];
        }

        if (isset($params['tracking_pixels.create'])) {
            $newPermissions[$permissions['tracking_pixels.create']] = [
                'restrictions' => json_encode([['name' => 'count', 'value' => $params['pixels']]])
            ];
        }

        $basic->permissions()->sync($newPermissions);

        $this->plan->create([
            'name' => "6 Month Subscription",
            'uuid' => str_random(36),
            'parent_id' => $basic->id,
            'interval' => 'month',
            'interval_count' => 6,
            'amount' => ($params['amount'] * 6) * ((100 - 10) / 100), // 6 months - 10%
            'currency' => 'USD',
            'currency_symbol' => '$',
        ]);

        $this->plan->create([
            'name' => "1 Year Subscription",
            'uuid' => str_random(36),
            'parent_id' => $basic->id,
            'interval' => 'month',
            'interval_count' => 12,
            'amount' => ($params['amount'] * 12) * ((100 - 20) / 100), // 12 months - 20%,
            'currency' => 'USD',
            'currency_symbol' => '$',
        ]);
    }
}
