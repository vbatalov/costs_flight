<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Charter extends Model
{
    protected $table = "Charter";
    protected $fillable =
        [
            "CH_KEY"
        ];


    public $timestamps = false;
}
