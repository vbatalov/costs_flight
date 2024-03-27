<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;
use Tests\TestCase;

class CostTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $flights = [
            "S17545",
            "S37545",
            "S17545",
            "SU1231",
            "Z37674",
            "ZF1345",
        ];

        $date_flights = [];
        for ($i = 0; $i < 10; $i++) {
            $date_flights[] = fake()->date;
        }
        $data = [];
        for ($i = 0; $i < 1000; $i++) {
            $data[] = [
//                "PKKEY" => rand(1, 500),
                "AirlineAndFlight" => $flights[rand(0, (count($flights)) -1)],
                "date_flight" => $date_flights[rand(0, (count($date_flights)) -1)],
                "cost" => rand(1,30),
                "long" => rand(1,50),
            ];
        }


        $response = $this->postJson(uri: "/api/set_cost_flight?pkkey=3300",
            data: $data)->withHeaders([
            "Content-Type: application/json",
        ]);
    }
}
