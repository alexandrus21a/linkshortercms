<?php

return [
    // LINKS
    ['method' => 'DELETE', 'name' => 'link/{link}', 'origin' => 'admin'],
    ['method' => 'PUT', 'name' => 'link/{link}', 'origin' => 'admin'],

    // LINK GROUPS
    ['method' => 'DELETE', 'name' => 'link-group/{link_group}', 'origin' => 'admin'],
    ['method' => 'PUT', 'name' => 'link-group/{link_group}', 'origin' => 'admin'],

    // LINK OVERLAYS
    ['method' => 'DELETE', 'name' => 'link-overlay/{link_overlay}', 'origin' => 'admin'],
    ['method' => 'PUT', 'name' => 'link-overlay/{link_overlay}', 'origin' => 'admin'],

    // TRACKING PIXELS
    ['method' => 'DELETE', 'name' => 'tracking-pixel/{tracking_pixel}', 'origin' => 'admin'],
    ['method' => 'PUT', 'name' => 'tracking-pixel/{tracking_pixel}', 'origin' => 'admin'],
];
