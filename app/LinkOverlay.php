<?php

namespace App;

use Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin Eloquent
 */
class LinkOverlay extends Model
{
    protected $guarded = ['id'];
    protected $casts = ['id' => 'integer'];

    public function setColorsAttribute($value)
    {
        if ($value && is_array($value)) {
            $this->attributes['colors'] = json_encode($value);
        }
    }

    public function getColorsAttribute($value)
    {
        if ($value && is_string($value)) {
            return json_decode($value, true);
        } else {
            return [];
        }
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
