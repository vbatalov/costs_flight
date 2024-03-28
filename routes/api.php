<?php

use App\Http\Controllers\SetCostFlightController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post("set_cost_flight", [SetCostFlightController::class, "handle"])->name("set-cost-flight");