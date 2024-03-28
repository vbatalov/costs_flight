<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Costs;
use Carbon\Carbon;
use League\Csv\Reader;
use Storage;
use Tests\TestCase;

class CostTest extends TestCase
{

    public function test_data(int $chunk = 500)
    {
        $reader = Reader::createFromPath(Storage::disk("public")->path("log_main.csv"), 'r');
        $reader->setHeaderOffset(0);
//        iterator_to_array($reader, true);
        $reader->setDelimiter(";");
        $records = $reader->getRecords();


        $first_day = [];
        $second_day = [];
        $i = 0;
        foreach ($records as $offset => $record) {
            if ($i < 75000) {
                $first_day[] = $record;
            } else {
                $second_day[] = $record;
            }
            $i++;
            if ($i > 150_000) {
                break;
            }
        }

        $first_day = array_chunk($first_day, $chunk);
        $second_day = array_chunk($second_day, $chunk);
        return [
            "first_day" => $first_day,
            "second_day" => $second_day
        ];
    }

    public function test_firstDay(): void
    {

        $items = $this->test_data(5000);

        foreach ($items['first_day'] as $key => $item) {
//            if ($key>1) break;

            $pkkey = $item[$key]['pkkey'];
            $response = $this->post(uri: "/api/set_cost_flight?pkkey=$pkkey",
                data: $item)->withHeaders([
                "Content-Type: application/json",
            ]);

            if ($response->status() == 200) {
                $this->assertTrue(true);
            }

            $this->assertDatabaseCount("tbl_costs", 0);
        }

    }
}
