<?php

namespace App\Http\Controllers;

use App\Models\Costs;
use Illuminate\Http\Request;

class SetCostFlight extends Controller
{
    public function handle(Request $request)
    {
        foreach ($request->all() as $item) {
            Costs::updateOrCreate(
                [
                    "PKKEY" => $request->query->get("pkkey"),
                    "AirlineAndFlight" => $item['AirlineAndFlight'],
                    "date_flight" => $item['dateflight'],
                    "long" => $item['long'],
                ],
                [
                    "cost" => $item['cost'],
                ]
            );
        }
    }
}
