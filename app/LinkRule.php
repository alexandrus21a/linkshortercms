<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\LinkRule
 *
 * @property int $id
 * @property string $type
 * @property int $link_id
 * @property string $key
 * @property string $value
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkRule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkRule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkRule whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkRule whereLinkId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkRule whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkRule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\LinkRule whereValue($value)
 * @mixin \Eloquent
 */
class LinkRule extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'link_id' => 'integer',
    ];
}
