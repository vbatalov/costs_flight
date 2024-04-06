<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Controllers\SetCostFlightController;
use App\Models\Costs;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use Tests\TestCase;

class CostTest extends TestCase
{


    public function test_data(int $count, int $chunk = 500)
    {
        $reader = Reader::createFromPath(Storage::disk("public")->path("30814_mac.csv"), 'r');
        $reader->setHeaderOffset(0);
//        iterator_to_array($reader, true);
//        $reader->setDelimiter(";");
        $records = $reader->getRecords();


        $data = [];

        $i = 0;
        foreach ($records as $offset => $record) {
            foreach ($record as $value) {
                $explode = explode(";", $value);

                $explode[1] = date("Y-m-d", strtotime($explode[1]));
                $data[] = $explode;
            }
            $i++;
            if ($i == $count) {
                break;
            }
        }
        return $data;
    }


    public function test_select_from_charter()
    {
        $post_data = $this->test_data(3);

        $response = $this->postJson(uri: "/api/set_cost_flight?pkkey=30814",
            data: $post_data);

//        dd(json_encode($post_data));

        print_r($response->content());
        $this->assertTrue(true);
    }

    public function test_groupItems()
    {
        $items = $this->test_data(20000);
//        dd($items);

        $controller = new SetCostFlightController();
//        $group = $controller->groupSameLong(items: $items, pkkey: 0);
        $group = $controller->group($items);

        dd($group);
    }
}
