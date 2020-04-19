<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkImage extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'id' => 'integer',
        'link_id' => 'integer',
    ];
}
