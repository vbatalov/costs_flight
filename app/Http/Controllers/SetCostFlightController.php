<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateCostJob;
use App\Models\Costs;
use Illuminate\Http\Request;

class SetCostFlightController extends Controller
{
    public function handle(Request $request)
    {
        $items = $this->filterSameLong($request->post());

        foreach ($items as $key => $item) {
//            $upd = Costs::where(
//                [
//                    "PKKEY" => $request->query->get("pkkey"),
//                    "AirlineAndFlight" => $item['AirlineAndFlight'],
//                    "date_flight" => $item['date_flight'],
//                    "long" => $item['long'],
//                ])
//                ->update(
//                    [
//                        "cost" => $item['cost'],
//                    ]);
            UpdateCostJob::dispatch(item: $item, pkkey: $request->query->get("pkkey"));
        }

        return response("ok");
    }

    /** Фильтр: поиск одинаковых значений LONG
     * Задача: принять POST данные
     * 1. Если совпадают все значения, кроме LONG, записать в отдельный массив для использования в IN
     * 2. Удалить из POST запроса все обработанные данные из п.1
     */
    private function filterSameLong(array $items)
    {
        $duplicated_longs = [];
        $itemsForDelete = [];

        // фильтрация
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
                    if (isset($duplicated_longs[$cost])) {
                        // чтобы не было дублирования в массиве LONG'ов, проверяю, есть ли такой LONG уже в массиве с ценой
                        // если нет, добавляю LONG
                        if (!in_array($item['long'], $duplicated_longs[$cost]['long'])) {
                            array_push($duplicated_longs[$cost]['long'], $item['long']);
                        }
                    } else {
                        // если ещё нет ключа $cost, создаю
                        $duplicated_longs[$cost] = [
                            "pkkey" => $item['pkkey'],
                            "AirlineAndFlight" => $item['AirlineAndFlight'],
                            "date_flight" => $item['date_flight'],
                            "cost" => $item['cost'],
                            "long" => [$item['long']]
                        ];
                    }

                    // собираю ключи для удаления из общего POST запроса.
                    // Кортежи, которые не совпадают по LONG'у, я оставляю для обновления без "IN"
                    $itemsForDelete[] = $key;
                }
            }
        }

        // из POST запроса удаляю записи, в которых одинаковые все значения, кроме LONG
        foreach ($itemsForDelete as $key) {
            unset($items[$key]);
        }

        foreach ($duplicated_longs as $item) {
            Costs::where([
                "PKKEY" => $item['pkkey'],
                "AirlineAndFlight" => $item['AirlineAndFlight'],
                "date_flight" => $item['date_flight'],
            ])
            ->whereIn("long", $item['long'])
            ->update([
                "cost" => $item['cost']
            ]);
        }
        

        return $items;
    }
}
