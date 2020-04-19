<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use Common\Core\BaseController;

class LinkUsageController extends BaseController
{
    public function getUsage()
    {
        /** @var User $user */
        $user = Auth::user();

        $usage = [];

        $linkTotal = $user->getRestrictionValue('links.create', 'count');
        if ( ! is_null($linkTotal)) {
            $usage['links'] = [
                'used' => $user->links()->count(),
                'total' => $linkTotal,
            ];
        }

        $clickTotal = $user->getRestrictionValue('links.create', 'click_count');
        if ( ! is_null($clickTotal)) {
            $usage['clicks'] = [
                'used' => $user->links()->count(),
                'total' => $clickTotal,
            ];
        }

        $overlayTotal = $user->getRestrictionValue('link_overlays.create', 'count');
        if ( ! is_null($overlayTotal)) {
            $usage['overlays'] = [
                'used' => $user->link_overlays()->count(),
                'total' => $overlayTotal,
            ];
        }

        $pageTotal = $user->getRestrictionValue('custom_pages.create', 'count');
        if ( ! is_null($pageTotal)) {
            $usage['custom_pages'] = [
                'used' => $user->link_custom_pages()->count(),
                'total' => $pageTotal,
            ];
        }

        $domainTotal = $user->getRestrictionValue('custom_domains.create', 'count');
        if ( ! is_null($domainTotal)) {
            $usage['custom_domains'] = [
                'used' => $user->custom_domains()->count(),
                'total' => $domainTotal,
            ];
        }

        $groupTotal = $user->getRestrictionValue('link_groups.create', 'count');
        if ( ! is_null($groupTotal)) {
            $usage['link_groups'] = [
                'used' => $user->link_groups()->count(),
                'total' => $groupTotal,
            ];
        }

        $pixelTotal = $user->getRestrictionValue('tracking_pixels.create', 'count');
        if ( ! is_null($pixelTotal)) {
            $usage['tracking_pixels'] = [
                'used' => $user->tracking_pixels()->count(),
                'total' => $pixelTotal,
            ];
        }

        return $this->success(['usage' => $usage]);
    }

}