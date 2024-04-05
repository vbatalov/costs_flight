<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateCostJob;
use App\Models\Charter;
use App\Models\Costs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetCostFlightController extends Controller
{
    public function handle(Request $request)
    {

        $items = $this->filterSameLong($request->post());

        foreach ($items as $data => $long) {
            $explode = explode("_", $data);

            $PK_KEY = $explode[0];
            $AirlineAndFlight = $explode[1];
            $dateflight = $explode[2];
            $cost = $explode[3];

//            $results = DB::table('Charter as charter')
//                ->join('tbl_Costs as costs', 'charter.CH_KEY', '=', 'costs.CS_CODE')
//                ->where('costs.CS_PKKEY', $PK_KEY)
//                ->where('costs.CS_DATEEND', '>', DB::raw('GETDATE()'))
//                ->where('costs.CS_DATE', "$dateflight")
//                ->where(DB::raw('charter.CH_AIRLINECODE + charter.CH_FLIGHT'), $AirlineAndFlight)
//                ->first(['charter.CH_KEY']);

            $results = DB::table('Charter as charter')
                ->whereExists(function ($query) use ($PK_KEY, $dateflight, $AirlineAndFlight) {
                    $query->from('tbl_Costs')
                        ->whereRaw('CS_CODE = CH_KEY AND CS_PKKEY = ?', [$PK_KEY])
                        ->whereRaw('CS_DATEEND > CURRENT_TIMESTAMP AND CS_DATE = ?', [$dateflight]);
                })->where(DB::raw('charter.CH_AIRLINECODE + charter.CH_FLIGHT'), $AirlineAndFlight)
                ->pluck("CH_KEY");


            /** DEBUG */
            dd($results);
        }

//        foreach ($results as $key => $result) {
//            dd(json_decode($result, true));
//        }

        dd("EMPTY");


        return response("ok");
    }

    /** Фильтр: поиск одинаковых значений LONG
     * Задача: принять POST данные
     * 1. Если совпадают все значения, кроме LONG, записать в отдельный массив для использования в IN
     * 2. Удалить из POST запроса все обработанные данные из п.1
     */
    private
    function filterSameLong(array $items)
    {
        $duplicated_longs = [];
        $itemsForDelete = [];


        // фильтрация
        foreach ($items as $key => $item) {
            $key_name = $item['pkkey'] . "_" . $item['AirlineAndFlight']
                . "_" . $item['dateflight'] . "_" . $item['cost'];

            foreach ($items as $item2) {
                if (
                    /** Ищу все совпадения, кроме LONG. LONG должен отличаться */
                    $item['pkkey'] == $item2['pkkey']
                    and $item['AirlineAndFlight'] == $item2['AirlineAndFlight']
                    and $item['dateflight'] == $item2['dateflight']
                    and $item['cost'] == $item2['cost']
                    and $item['long'] != $item2['long']

                ) {
                    // Если ключ $cost (цена) уже есть, добавляю LONG в массив
                    if (isset($duplicated_longs[$key_name])) {
                        // чтобы не было дублирования в массиве LONG'ов, проверяю, есть ли такой LONG уже в массиве с ценой
                        // если нет, добавляю LONG
                        if (!in_array($item['long'], $duplicated_longs[$key_name])) {
                            array_push($duplicated_longs[$key_name], $item['long']);
                        }
                    } else {
                        // если ещё нет ключа $cost, создаю
                        $duplicated_longs[$key_name] = [
                            $item['long'],
                        ];
                    }
                    // Собираю ключи для удаления из общего POST запроса.
                    // Кортежи, которые не совпадают по LONG'у, я оставляю для обновления без "IN"
                    $itemsForDelete[] = $key;
                }
            }
        }

        // из POST запроса удаляю записи, в которых одинаковые все значения, кроме LONG
        foreach ($itemsForDelete as $key) {
            unset($items[$key]);
        }

        foreach ($items as $key => $item) {
            $key_name = $item['pkkey'] . "_" . $item['AirlineAndFlight']
                . "_" . $item['dateflight'] . "_" . $item['cost'];

            $duplicated_longs[$key_name] = [
                $item['long'],
            ];

            unset($items[$key]);
        }

//        dd($duplicated_longs, $items);


//        foreach ($duplicated_longs as $data => $long) {
//            $explode = explode("_", $data);
//            UpdateCostJob::dispatch(item: $explode, pkkey: $explode[0], long: $long);
//        }


        return $duplicated_longs;
    }
}
