<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Costs;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Tests\TestCase;

class CostTest extends TestCase
{


    public function test_data(int $chunk = 500)
    {
        $reader = Reader::createFromPath(Storage::disk("public")->path("log_main.csv"), 'r');
        $reader->setHeaderOffset(0);
//        iterator_to_array($reader, true);
        $reader->setDelimiter("\t");
        $records = $reader->getRecords();


        $first_day = [];

        $i = 0;
        foreach ($records as $offset => $record) {
            $record['dateflight'] = date("YYYY-mm-dd", strtotime($record['dateflight']) );
            dd($record);
            $first_day[] = $record;
            $i++;
            if ($i > 500) {
                break;
            }
        }
        dd($first_day);
        $first_day = array_chunk($first_day, $chunk);

        return $first_day;

    }

//    public function test_createSecondDay()
//    {
//
//        $items = $this->test_data(10);
//        foreach ($items['second_day'] as $key => $item) {
//
//            foreach ($item as $data) {
//                Costs::create([
//                    "PKKEY" => $data['pkkey'],
//                    "AirlineAndFlight" => $data['AirlineAndFlight'],
//                    "date_flight" => $data['dateflight'],
//                    "long" => $data['long'],
//                    "cost" => $data['cost'],
//                ]);
//            }
//
//        }
//    }

//    public function test_firstDay(): void
//    {
//
//        $items = $this->test_data(5000);
//
//        foreach ($items['first_day'] as $key => $item) {
////            if ($key != 14) continue;
////dd($item);
//
//            $pkkey = $item[0]['pkkey'];
//            $response = $this->post(uri: "/api/set_cost_flight?pkkey=$pkkey",
//                data: $item)->withHeaders([
//                "Content-Type: application/json",
//            ]);
//
//            if ($response->status() == 200) {
//                $this->assertTrue(true);
//            }
//
////            foreach ($item as $data) {
////                Costs::create([
////                    "PKKEY" => $item[$key]['pkkey'],
////                    "AirlineAndFlight" => $data['AirlineAndFlight'],
////                    "dateflight" => $data['dateflight'],
////                    "long" => $data['long'],
////                    "cost" => $data['cost'],
////                ]);
////            }
//
//
//        }
//
//    }

//    public function test_detectSomeLong()
//    {
//
//
//        for ($i = 3; $i < 10; $i++) {
//            Costs::create(
//                ["PKKEY" => "30814", "AirlineAndFlight" => "GDS005", "date_flight" => "2024-06-09", "cost" => 0, "long" => $i],
//            );
//        }
//
//        $this->assertDatabaseCount("tbl_costs", 7);
//
//        $post_data =
//            [
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "dateflight" => "2024-06-09", "cost" => 422, "long" => 3],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "dateflight" => "2024-06-09", "cost" => 422, "long" => 4],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "dateflight" => "2024-06-09", "cost" => 351, "long" => 5],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "dateflight" => "2024-06-09", "cost" => 500, "long" => 6],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "dateflight" => "2024-06-09", "cost" => 500, "long" => 7],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "dateflight" => "2024-06-09", "cost" => 422, "long" => 8],
//                ["pkkey" => "30814", "AirlineAndFlight" => "GDS005", "dateflight" => "2024-06-09", "cost" => 422, "long" => 9],
//
//            ];
//
//        $response = $this->post(uri: "/api/set_cost_flight?pkkey=30814",
//            data: $post_data)->withHeaders([
//            "Content-Type: application/json",
//        ]);
//
//        $this->assertTrue(true);
//    }

    public function test_select_from_charter()
    {
        $post_data = $this->test_data(10000);
        $post_data = $post_data[0];
        $response = $this->post(uri: "/api/set_cost_flight?pkkey=30814",
            data: $post_data)->withHeaders([
            "Content-Type: application/json",
        ]);

        $this->assertTrue(true);
    }
}
