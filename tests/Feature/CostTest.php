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


        }

    }

    public function test_detectSomeLong()
    {
        $post_data =
            [
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "11.05.2024", "cost" => 422, "long" => 3], //0
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "11.05.2024", "cost" => 422, "long" => 4], //1
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 351, "long" => 5], //2
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 500, "long" => 6], //3
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "12.05.2024", "cost" => 500, "long" => 7], //4
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "11.05.2024", "cost" => 422, "long" => 8], //5
                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "11.05.2024", "cost" => 422, "long" => 9], //6
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "17.05.2024", "cost" => 389, "long" => 9],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "18.05.2024", "cost" => 406, "long" => 10],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "19.05.2024", "cost" => 379, "long" => 11],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "20.05.2024", "cost" => 361, "long" => 12],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "21.05.2024", "cost" => 358, "long" => 13],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "22.05.2024", "cost" => 358, "long" => 14],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "23.05.2024", "cost" => 358, "long" => 15],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "24.05.2024", "cost" => 386, "long" => 16],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "25.05.2024", "cost" => 386, "long" => 17],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "26.05.2024", "cost" => 377, "long" => 18],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "27.05.2024", "cost" => 358, "long" => 19],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "28.05.2024", "cost" => 355, "long" => 20],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "29.05.2024", "cost" => 352, "long" => 21],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "30.05.2024", "cost" => 374, "long" => 22],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "31.05.2024", "cost" => 378, "long" => 23],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "01.06.2024", "cost" => 422, "long" => 24],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "01.06.2024", "cost" => 422, "long" => 25],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "01.06.2024", "cost" => 422, "long" => 26],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "02.06.2024", "cost" => 372, "long" => 27],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "02.06.2024", "cost" => 372, "long" => 28],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "02.06.2024", "cost" => 372, "long" => 29],
        ];

        $response = $this->post(uri: "/api/set_cost_flight?pkkey=30814",
            data: $post_data)->withHeaders([
            "Content-Type: application/json",
        ]);

        dd($response);
    }
}
