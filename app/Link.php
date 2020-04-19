<?php

namespace App;

use Common\Pages\CustomPage;
use Common\Settings\Settings;
use Hash;
use Eloquent;
use Carbon\Carbon;
use Common\Tags\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Link
 *
 * @property int $id
 * @property string $hash
 * @property string $alias
 * @property string $long_url
 * @property string|null $password
 * @property string|null $expires_at
 * @property string|null $description
 * @property string|LinkOverlay|null $type
 * @property int|null $type_id
 * @property int $user_id
 * @property integer $domain_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|LinkClick[] $clicks
 * @property-read string $short_url
 * @property-read Collection|LinkRule[] $rules
 * @property-read Collection|Tag[] $tags
 * @property-read User $user
 * @property-read LinkOverlay|null $custom_page
 * @property-read TrackingPixel[]|Collection $pixels
 * @mixin Eloquent
 */
class Link extends Model
{
    protected $guarded = ['id'];
    protected $hidden = ['password'];
    protected $appends = ['short_url', 'has_password'];
    protected $attributes = ['type' => 'default'];

    protected $casts = [
        'id' => 'integer',
        'domain_id' => 'integer',
        'user_id' => 'integer',
        'disabled' => 'boolean',
        'has_password' => 'boolean',
    ];

    public function rules()
    {
        return $this->hasMany(LinkRule::class);
    }

    public function clicks() {
        return $this->hasMany(LinkClick::class);
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function image() {
        return $this->hasOne(LinkImage::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function groups() {
        return $this->belongsToMany(LinkGroup::class, 'link_group_link');
    }

    public function pixels() {
        return $this->belongsToMany(TrackingPixel::class, 'link_tracking_pixel');
    }

    public function custom_page()
    {
        $namespace = $this->type === 'overlay' ?
            LinkOverlay::class :
            CustomPage::class;

        return $this->belongsTo($namespace, 'type_id');
    }

    public function getHasPasswordAttribute()
    {
        return $this->attributes['password'];
    }

    public function getShortUrlAttribute()
    {
        $defaultHost = app(Settings::class)->get('custom_domains.default_host') ?: config('app.url');
        return $defaultHost . '/' . ($this->alias ?: $this->hash);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value ? Hash::make($value) : null;
    }
}
