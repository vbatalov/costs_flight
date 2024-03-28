<?php

namespace App\Jobs;

use App\Models\Costs;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateCostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $items, public string $pkkey)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Costs::updateOrCreate(
            [
                "PKKEY" => $this->pkkey,
                "AirlineAndFlight" => $this->items['AirlineAndFlight'],
                "date_flight" => $this->items['dateflight'],
                "long" => $this->items['long'],
            ],
            [
                "cost" => $this->items['cost'],
            ]);
    }

}
