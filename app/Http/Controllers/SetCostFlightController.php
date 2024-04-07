<?php

namespace App\Http\Controllers;

use App\Models\Costs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetCostFlightController extends Controller
{
    public function handle(Request $request)
    {
        if (empty($request->post())) {
            trigger_error("POST data is empty");
        }

        if ($PK_KEY = $request->get("pkkey") == null) {
            trigger_error("PK_KEY does not exists");
        }

        /** Группировка */
        $items = $this->group($request->post());

        /** Количество выполненных обновлений */
        $count_updated = 0;

        foreach ($items as $data => $long) {
            $explode = explode("_", $data);
            $AirlineAndFlight = $explode[0];
            $dateflight = $explode[1];
            $cost = $explode[2];

            $CH_KEY = DB::table('Charter as charter')
                ->whereExists(function ($query) use ($PK_KEY, $dateflight, $AirlineAndFlight) {
                    $query->from('tbl_Costs')
                        ->whereRaw('CS_CODE = CH_KEY AND CS_PKKEY = ?', [$PK_KEY])
                        ->whereRaw('CS_DATEEND > CURRENT_TIMESTAMP AND CS_DATE = ?', [$dateflight]);
                })->where(DB::raw('charter.CH_AIRLINECODE + charter.CH_FLIGHT'), $AirlineAndFlight)
                ->pluck("CH_KEY");

            if ($CH_KEY->isEmpty()) {
                dump($PK_KEY, $dateflight, $AirlineAndFlight);
                trigger_error("CH_KEY not found");
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


            if ($rows_updated) {
                $count_updated = $count_updated + $rows_updated;
            }
        }

        return response([
            "items_count" => count($request->post()),
            "items_count_after_group" => count($items),
            "count_rows_updated" => $count_updated,
        ]);
    }

    /** Фильтр: поиск одинаковых значений LONG
     * Задача: принять POST данные
     * 1. Если совпадают все значения, кроме LONG, записать в отдельный массив для использования в IN
     */
    public function group($items): array
    {
        if (!is_array($items)) $items = json_decode($items, true);

        return array_reduce($items, function ($carry, $item) {
            // Создаем ключ для итогового массива, объединяя первые три значения через "_"
            $key = implode('_', array_slice($item, 0, 3));

            if (count($item) != 4) trigger_error("The number of records in the sent tuple does not match.");

            // Добавляем текущее значение четвертого ключа к массиву значений для этого ключа
            if (isset($carry[$key])) {
                $carry[$key][] = $item[3];
            } else {
                $carry[$key] = [$item[3]];
            }

            return $carry;
        }, []);
    }
}
