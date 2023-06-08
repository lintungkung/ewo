<?php

namespace App\Http\Controllers\Tests;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Ewo_LoginController;
use App\Http\Controllers\ewoToolsController;
use App\Repositories\Customer\CustomerBaseRepository;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Login\LoginRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Consumables\ConsumablesRepository;
use App\Services\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;


class ShouController extends Controller
{

    private $statusType = array('0.預約','1.開通','1.分派','1.控制','2.改約');

    public function __construct(
        OrderRepository $OrderRepository,
        LoginRepository $LoginRepository,
        User $User,
        Ewo_LoginController $loginController
    )
    {
        $this->OrderRepository = $OrderRepository;
        $this->LoginRepository = $LoginRepository;
        $this->User = $User;
        $this->ConsumablesRepository = new ConsumablesRepository();
        $this->CustomerRepository = new CustomerRepository((new CustomerBaseRepository()));

        $this->loginController = $loginController;
        date_default_timezone_set('Asia/Taipei');
    }

    public function orderListForShou()
    {
        $Carbon = Carbon::now();

        dump('eeee');
        dd('end');
//        //忽略 max 執行時間
//        set_time_limit(0);
//
//        $time_s = microtime('true');
//        $run_title = Session::get('userId').'_'.time();
//        $userId = Session::get('userId');
//
//        $data = array(
//                'bookdate_s' => date('Y-m-d 00:00:00',strtotime('-1 day')),
//                'bookdate_e' => date('Y-m-d 00:00:00',strtotime('+3 day')),
//        );
//            $orderList = $this->OrderRepository->getOrderList($data);
//            foreach ($orderList as $index => $order) {
//                // 只跑 9000 筆
//                if ($index > 9000 ) {
//                    continue;
//                }
//
//
//                $ms0300_phone = [];
//                // 驗證將MS0300 當中可用的電話放入 $available_phone 電話中
//                foreach ( ['TeleNum01','CellPhone01','TeleNum2','CellPhone2','CallinTele'] as $ms300_phone_key ) {
//                    $ms0300_phone_number = ewoToolsController::transferPhoneNumberToVerifyFormat($order->MSCityA ??'', $order->$ms300_phone_key??'');
//                    // 有電話且電話不重覆，
//                    if ($ms0300_phone_number !== '' && !in_array($ms0300_phone_number,$ms0300_phone)) {
//                        Log::info('ms0300_phone:'.$ms0300_phone_number.','.'WorkSheet:'.$order->WorkSheet.','.'CompanyNo:'.$order->CompanyNo.',');
//                        array_push($ms0300_phone,$ms0300_phone_number);
//                    }
//                }
//                // 建立ms0200表可用電話Array
//                $ms0200_phone = [];
//                $customer_id =  (string)$order->CustID;
//                // 取得此客戶的 MS0200 表當中的電話
//                $MS0200Phones = $this->CustomerRepository->getEnableCustomerByCustomerId($customer_id,$order->CompanyNo);
//                foreach ($MS0200Phones as $MS0200Phone) {
//                    foreach (['CellPhone01','CellPhone02','TeleNum01','TeleNum02','TeleNum03'] as $ms200_phone_key ) {
//                        $ms0200_phone_number = ewoToolsController::transferPhoneNumberToVerifyFormat($MS0200Phone->MailCity??'', $MS0200Phone->$ms200_phone_key ?? '');
//                        // 有電話且電話不重覆，
//                        if ($ms0200_phone_number !== '' && !in_array($ms0200_phone_number,$ms0300_phone) && !in_array($ms0200_phone_number,$ms0200_phone)) {
//                            Log::info('ms0200_phone:'.$ms0200_phone_number.','.'CustId:'.$customer_id.','.'CompanyNo:'.$order->CompanyNo.','.'WorkSheet:'.$order->WorkSheet);
//                            array_push($ms0200_phone,$ms0200_phone_number);
//                        }
//                    }
//                }
//
//            }


        //return redirect('/ewo/login');
    }
}
