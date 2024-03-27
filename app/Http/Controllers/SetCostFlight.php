<?php

namespace App\Http\Controllers;

use App\Models\Costs;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SetCostFlight extends Controller
{
    public function handle(Request $request)
    {

        foreach ($request->all() as $item) {
            try {
                Costs::updateOrCreate(
                    [
                        "PKKEY" => $request->query->get("pkkey"),
                        "AirlineAndFlight" => $item['AirlineAndFlight'],
                        "date_flight" => $item['date_flight'],
                        "long" => $item['long'],
                    ],
                    [
                        "cost" => $item['cost'],
                    ]
                );
            } catch (\Throwable $throwable) {
                dd($throwable->getMessage(), $throwable->getTraceAsString());
            }
        }
    }
}
