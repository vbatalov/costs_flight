<?php

namespace App\Http\Controllers;

use App\Models\Costs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetCostFlightController extends Controller
{
    public function handle(Request $request)
    {

        $items = $this->filterSameLong(items: $request->post(), pkkey: $request->get("pkkey"));
        print_r("Count items:" . count($items) . "\n");
        dd(count($items), $items);

        /** @var $count_items integer Количество кортежей */
        $count_items = count($items);
        /** @var $count_updated integer Количество выполненных обновлений */
        $count_updated = 0;

        foreach ($items as $data => $long) {
            $explode = explode("_", $data);

            $PK_KEY = $explode[0];
            $AirlineAndFlight = $explode[1];
            $dateflight = $explode[2];
            $cost = $explode[3];

            try {
                $CH_KEY = DB::table('Charter as charter')
                    ->whereExists(function ($query) use ($PK_KEY, $dateflight, $AirlineAndFlight) {
                        $query->from('tbl_Costs')
                            ->whereRaw('CS_CODE = CH_KEY AND CS_PKKEY = ?', [$PK_KEY])
                            ->whereRaw('CS_DATEEND > CURRENT_TIMESTAMP AND CS_DATE = ?', [$dateflight]);
                    })->where(DB::raw('charter.CH_AIRLINECODE + charter.CH_FLIGHT'), $AirlineAndFlight)
                    ->pluck("CH_KEY");
            } catch (\Throwable $throwable) {
                print_r("DB::table('Charter as charter')\n");
                print_r($data);
                dd($throwable->getMessage());
            }


            /** DEBUG */
            if ($CH_KEY->isEmpty()) {
                //TODO Сделать ЛОГ, если пустой CH_KEY
                continue;
            }

            // Обновление записей
            $rows_updated = Costs::where('CS_SVKEY', 1)
                ->where('CS_PKKEY', $PK_KEY)
                ->where('CS_CODE', $CH_KEY)
                ->where('CS_DATE', $dateflight)
                ->where('CS_DATEEND', $dateflight)
                ->whereNotIn('CS_SUBCODE1', function ($query) {
                    $query->select('AS_KEY')
                        ->from('AirService')
                        ->whereRaw('AS_NAMERUS like ?', ['%Блочный%']);
                })
                ->whereIn('CS_LONG', $long)
                ->whereIn('CS_LONGMIN', $long)
                ->update(['CS_COST' => $cost, 'CS_COSTNETTO' => $cost ?? 0, 'CS_CHECKINDATEBEG' => null, 'CS_CHECKINDATEEND' => null]);


            if ($rows_updated) $count_updated++;

        }

        print_r("Count updated: $count_updated");

        return response("ok");
    }

    /** Фильтр: поиск одинаковых значений LONG
     * Задача: принять POST данные
     * 1. Если совпадают все значения, кроме LONG, записать в отдельный массив для использования в IN
     * 2. Удалить из POST запроса все обработанные данные из п.1
     */
    private function filterSameLong(array $items, $pkkey)
    {
        $duplicated_longs = [];
        $itemsForDelete = [];


        // фильтрация
        foreach ($items as $key => $item) {
            dd($item);
            $key_name = $pkkey . "_" . $item['AirlineAndFlight']
                . "_" . $item['dateflight'] . "_" . $item['cost'];

            dd($key_name);
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
