<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateCostJob;
use App\Models\Costs;
use Illuminate\Http\Request;

class SetCostFlightController extends Controller
{
    public function handle(Request $request)
    {
        return $this->filterSameLong($request->post());

        foreach ($request->post() as $key => $item) {
            UpdateCostJob::dispatch(items: $item, pkkey: $request->query->get("pkkey"));
        }

        return response("ok");
    }

    private function filterSameLong(array $items)
    {
        $duplicated = [];
        $itemsForDelete = [];

        foreach ($items as $key => $item) {
            $cost = $item['cost'];
            foreach ($items as $item2) {
                if (
                    /** Ищу все совпадения, кроме LONG. LONG должен отличаться */
                    $item['pkkey'] == $item2['pkkey']
                    and $item['AirlineAndFlight'] == $item2['AirlineAndFlight']
                    and $item['date_flight'] == $item2['date_flight']
                    and $item['cost'] == $item2['cost']
                    and $item['long'] != $item2['long']

                ) {
                    // Если ключ $cost (цена) уже есть, добавляю LONG в массив
                    if (isset($duplicated[$cost])) {
                        // чтобы не было дублирования в массиве LONG'ов, проверяю, есть ли такой LONG уже в массиве с ценой
                        // если нет, добавляю LONG
                        if (!in_array( $item['long'], $duplicated[$cost])) {
                            array_push($duplicated[$cost], $item['long']);
                        }
                    } else {
                        // если ещё нет ключа $cost, создаю
                        $duplicated[$cost] = [
                            $item['long']
                        ];
                    }

                    // собираю ключи для удаления из общего POST запроса.
                    // Кортежи, которые не совпадают по LONG'у, я оставляю для обновления без "IN"
                    $itemsForDelete[] = $key;
                }
            }
        }

        foreach ($itemsForDelete as $key) {
            unset($items[$key]);
        }

        dd($duplicated, $items);
    }
}
