<?php

use App\Http\Controllers\SetCostFlight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post("set_cost_flight", [SetCostFlight::class, "handle"])->name("set-cost-flight");