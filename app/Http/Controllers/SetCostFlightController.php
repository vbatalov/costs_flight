<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateCostJob;
use App\Models\Costs;
use Illuminate\Http\Request;

class SetCostFlightController extends Controller
{
    public function handle(Request $request)
    {

        foreach($request->all() as $key => $item) {
            if($key == "pkkey") continue;

            UpdateCostJob::dispatch(items: $item, pkkey: $request->query->get("pkkey"));
        }

        return response("ok");
    }
}
