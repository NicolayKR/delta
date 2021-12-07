<?php

namespace App\Http\Controllers;

use App\Models\Receipts;
use Illuminate\Http\Request;

class DataGraph extends Controller
{
    public function getData(Request $request){
        //Сформируем данные по оплатам.
        $final_array = $this->data();
        return $final_array;
    }
    public function getGraph(Request $request){
        $params = $request->query('params');
        $first_date = 'DATE_SUB(DATE(NOW()), INTERVAL 7 DAY)';
        $second_date = 'DATE(now())';
        $arrayData = array();
        $dataset = array();
        //Сформируем данные по оплатам.
        $arrayReceiptsDate = Receipts::distinct()->selectRaw('date_rec')
            ->whereRaw('date_rec BETWEEN '.$first_date.' and '.$second_date.'')
            ->orderByDesc('date_rec')
            ->get()->toArray();
        foreach ($arrayReceiptsDate as $date){
            $arrayData['labels'][] = $date['date_rec'];
        }
        $final_array = $this->data();
        foreach ($final_array as $item){
            $dataset[] = $item[$params];
        }
        $object = array();
        $object['label'] = $this->getName($params);
        $object['backgroundColor'] ='#2b87db';
        $object['borderColor'] ='#2b87db';
        //$object['borderWidth'] = 2;
        $object['fill'] = false;
        $object['data'] = $dataset;
        $object['pointRadius'] = 4;
        $arrayData['datasets'][] = $object;
        return $arrayData;
    }
    public function createReceipts(){
        $date = [
            '1'=> date("Y-m-d", strtotime("-4 day")),
            '2' => date("Y-m-d", strtotime("-3 day")),
            '3' => date("Y-m-d", strtotime("-2 day")),
            '4'=> date("Y-m-d", strtotime("-1 day")),
            '5' => date("Y-m-d"),
            '6' => date("Y-m-d", strtotime("+1 day"))
        ];
        foreach ($date as $curr_date){
            $summ = 2000;
            for($i=1;$i<4;$i++){
                $newObject = Receipts::create(array(
                    'payment_types' =>$i,
                    'amount'=>$summ,
                    'count_guests'=>$i,
                    'remove_before_payment'=>50,
                    'remove_after_payment'=>10,
                    'date_rec'=> $curr_date
                ));
                $newObject->save();
                $summ = $summ + 100;
            }
        }
    }
    public function p($array){
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
    public function cmp($a, $b)
    {
        if ($a == $b) {
            return 0;
        }
        return ($a < $b) ? -1 : 1;
    }
    public function data(){
        //По умолчанию будем использовать данные за крайнюю неделю.
        $first_date = 'DATE_SUB(DATE(NOW()), INTERVAL 7 DAY)';
        $second_date = 'DATE(now())';
        $arrayReceiptsPayment = Receipts::selectRaw('payment_types, SUM(amount) as sum_rec, date_rec')
            ->whereRaw('date_rec BETWEEN '.$first_date.' and '.$second_date.'')
            ->groupBy('date_rec')->groupBy('payment_types')
            ->orderByDesc('date_rec')
            ->get()->toArray();
        //Сформируем остальной массив данных.
        $arrayReceipts = Receipts::selectRaw('SUM(amount) as full_sum,ROUND(AVG(amount)) AS avg_rec, ROUND(AVG(amount/count_guests)) AS avg_guest, COUNT(*) as count_rec, SUM(count_guests) as count_guest,SUM(remove_after_payment) as del_after,SUM(remove_before_payment) as del_before, date_rec')
            ->whereRaw('date_rec BETWEEN '.$first_date.' and '.$second_date.'')
            ->groupBy('date_rec')
            ->orderByDesc('date_rec')
            ->get()->toArray();
        $final_array = array();
        foreach($arrayReceiptsPayment as $rec){
            $type = '';
            foreach ($rec as $key=>$value){
                if($key == 'payment_types'){
                    $type = $key.'_'.$value;
                }else{
                    if($key != 'date_rec'){
                        $new_date = date("d.m.Y", strtotime($rec['date_rec']));
                        $final_array[$new_date][$type] = $value;
                    }
                }
            }
        }
        foreach($arrayReceipts as $key=>$rec){
            foreach ($rec as $key=>$value){
                $new_date = date("d.m.Y", strtotime($rec['date_rec']));
                if($new_date == date('d.m.Y') && !array_key_exists('class', $final_array[$new_date])){
                    $final_array[$new_date]['class'] = 'current_day';
                }
                if($key != 'date_rec'){
                    $final_array[$new_date][$key] = $value;
                }else{
                    $final_array[$new_date][$key] = $new_date;
                }
            }
        }
        return $final_array;
    }
    public function getName($name){
        switch ($name){
            case 'payment_types_1':
                return 'Оплата наличными';
            case 'payment_types_2':
                return 'Безналичный расчет';
            case 'payment_types_3':
                return 'Кредитные карты';
            case 'full_sum':
                return 'Выручка';
            case 'avg_rec':
                return 'Средний чек';
            case 'avg_guest':
                return 'Средний гость';
            case 'del_after':
                return 'Удаление из чека (после оплаты), руб';
            case 'del_before':
                return 'Удаление из чека (до оплаты), руб';
            case 'count_rec':
                return 'Количество чеков';
            case 'count_guest':
                return 'Количество гостей';


        }
    }
}
