<?php

namespace App;

use Eloquent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * App\TrackingPixel
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin Eloquent
 */
class TrackingPixel extends Model
{
    protected $guarded = ['id'];

     protected $casts = [
         'id' => 'integer',
         'user_id' => 'integer',
     ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
