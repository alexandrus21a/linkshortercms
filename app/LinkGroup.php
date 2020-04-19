<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkGroup extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer'
    ];

    public function links() {
        return $this->belongsToMany(Link::class, 'link_group_link');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
