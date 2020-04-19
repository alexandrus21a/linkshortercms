<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LinkClick
 *
 * @property int $id
 * @property int $link_id
 * @property string $link_type
 * @property string|null $ip
 * @property string|null $referrer
 * @property string|null $platform
 * @property string|null $device
 * @property string|null $browser
 * @property string|null $location
 * @property int $crawler
 * @property \Carbon\Carbon $created_at
 * @mixin \Eloquent
 */
class LinkClick extends Model
{
    const UPDATED_AT = null;

    protected $guarded = ['id'];
}
