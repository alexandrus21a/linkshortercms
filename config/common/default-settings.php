<?php

return [
    // LINKS
    ['name' => 'links.default_type', 'value' => 'direct'],
    ['name' => 'links.enable_type', 'value' => true],
    ['name' => 'links.redirect_time', 'value' => 10],
    ['name' => 'links.geo_targeting', 'value' => true],
    ['name' => 'links.device_targeting', 'value' => true],
    ['name' => 'links.pixels', 'value' => true],
    ['name' => 'links.homepage_creation', 'value' => true],
    ['name' => 'links.homepage_stats', 'value' => true],

    // HOMEPAGE APPEARANCE
    ['name' => 'homepage.appearance', 'value' => json_encode([
        'headerTitle' => 'One short link, infinite possibilities.',
        'headerSubtitle' => 'BeLink helps you maximize the impact of every digital initiative with industry-leading features and tools.',
        'footerTitle' => 'The Ultimate URL Shortener that\'s simple, powerful, and easy.',
        'footerSubtitle' => 'Unleash the Power of the Link',
        'actions' => [
            'inputText' => 'Paste a long url',
            'inputButton' => 'Shorten',
            'cta1' => 'Get Started',
            'cta2' => 'Learn More',
        ],
        'primaryFeatures' => [
            [
                'title' => 'Password Protect',
                'subtitle' => 'Set a password to protect your links from unauthorized access.',
                'image' => 'client/assets/images/landing/lock.svg',
            ],
            [
                'title' => 'Retargeting',
                'subtitle' => 'Add retargeting pixels to your links and turn every URL into perfectly targeted ads.',
                'image' => 'client/assets/images/landing/globe.svg',
            ],
            [
                'title' => 'Groups',
                'subtitle' => 'Group links together for easier management and analytics for a group as well as individual links.',
                'image' => 'client/assets/images/landing/campaign.svg',
            ]
        ],
        'secondaryFeatures' => [
            [
                'title' => 'Track each and every user who clicks a link.',
                'subtitle' => 'COMPLETE ANALYTICS',
                'description' => 'Full analytics for individual links and link groups, including geo and device information, referrers, browser, ip and more.',
                'image' => 'client/assets/images/landing/stats.png',
            ],
            [
                'title' => 'One dashboard to manage everything.',
                'subtitle' => 'POWERFUL DASHBOARD',
                'description' => 'Control everything from the dashboard. Manage your URLs, groups, custom pages, pixels, custom domains and more.',
                'image' => 'client/assets/images/landing/dashboard.png',
            ]
        ]
    ])],

    // menus
    ['name' => 'menus', 'value' => json_encode([
        [
            'name' => 'User Dashboard',
            'position' => 'dashboard-sidebar',
            'items' => [
                ['type' => 'route', 'order' => 1, 'position' => 0, 'activeExact' => true, 'label' => 'Dashboard', 'action' => 'dashboard', 'icon' => 'home'],
                ['type' => 'route', 'order' => 1, 'position' => 1, 'label' => 'Links', 'action' => 'dashboard/links', 'icon' => 'link'],
                ['type' => 'route', 'order' => 1, 'position' => 2, 'label' => 'Link Groups', 'action' => 'dashboard/link-groups', 'icon' => 'dashboard'],
                ['type' => 'route', 'order' => 1, 'position' => 3, 'label' => 'Custom Domains', 'action' => 'dashboard/custom-domains', 'icon' => 'www'],
                ['type' => 'route', 'order' => 1, 'position' => 4, 'label' => 'Link Overlays', 'action' => 'dashboard/link-overlays', 'icon' => 'tooltip'],
                ['type' => 'route', 'order' => 1, 'position' => 5, 'label' => 'Link Pages', 'action' => 'dashboard/custom-pages', 'icon' => 'page'],
                ['type' => 'route', 'order' => 1, 'position' => 6, 'label' => 'Tracking Pixels', 'action' => 'dashboard/pixels', 'icon' => 'tracking']
            ]
        ],
        [
            'name' => 'footer',
            'position' => 'footer',
            'items' => [
                ['type' => 'link', 'order' => 1, 'position' => 1, 'label' => 'Privacy Policy', 'action' => '/pages/1/privacy-policy'],
                ['type' => 'link', 'order' => 1, 'position' => 2, 'label' => 'Terms of Service', 'action' => '/pages/2/terms-of-service'],
                ['type' => 'link', 'order' => 1, 'position' => 3, 'label' => 'Contact Us', 'action' => '/contact']
            ],
        ]
    ])],

    // custom domains
    ['name' => 'custom_domains.allow_select', 'value' => true],
];
