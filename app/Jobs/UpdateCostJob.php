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
    public function __construct(public $item, public string $pkkey)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $upd = Costs::where(
            [
                "PKKEY" => $this->pkkey,
                "AirlineAndFlight" => $this->item['AirlineAndFlight'],
                "date_flight" => $this->item['date_flight'],
                "long" => $this->item['long'],
            ])
            ->update(
            [
                "cost" => $this->item['cost'],
            ]);

    }

}
