<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Costs extends Model
{
    use HasFactory;

    protected $table = "tbl_Costs";
    protected $fillable =
        [
            "PKKEY",
            "AirlineAndFlight",
            "date_flight",
            "cost",
            "long",
        ];

    protected $casts =
        [
//            "date_flight" => "date",
        ];

    public $timestamps = false;
    public $primaryKey = "CS_ID";
}
