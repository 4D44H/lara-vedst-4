<?php

namespace Lara;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

abstract class BaseSoftDelete extends Model
{
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
