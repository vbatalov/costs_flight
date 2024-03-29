<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Costs;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
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


        }

    }

//    use RefreshDatabase;
    public function test_detectSomeLong()
    {
        Costs::truncate();

        for ($i = 3; $i<10; $i++) {
            Costs::create(
                ["PKKEY" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 1, "long" => $i],
            );
        }

        $this->assertDatabaseCount("tbl_costs", 7);

        $post_data =
            [
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 422, "long" => 3],
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 422, "long" => 4],
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 351, "long" => 5],
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 500, "long" => 6],
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 500, "long" => 7],
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 422, "long" => 8],
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 422, "long" => 9],

        ];

        $response = $this->post(uri: "/api/set_cost_flight?pkkey=30814",
            data: $post_data)->withHeaders([
            "Content-Type: application/json",
        ]);

        dd($response);
    }
}
