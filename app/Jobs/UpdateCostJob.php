<?php

namespace App\Jobs;

use App\Models\Costs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateCostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $item, public string $pkkey, public array $long)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $update = Costs::where([
            "PKKEY" => $this->item[0],
            "AirlineAndFlight" => $this->item[1],
            "date_flight" => $this->item[2],
        ])
            ->whereIn("long", $this->long)
            ->update([
                "cost" => $this->item[3]
            ]);
//        dd($this->item, $this->long, $update);

//        Log::alert($update->getChanges());
    }

}
