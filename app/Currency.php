<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $primarykey = 'iso';
    public $incrementing = false;

    protected $fillable = [
        'iso',
    ];
}
