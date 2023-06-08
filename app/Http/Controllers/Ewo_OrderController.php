<?php

namespace App\Http\Controllers;

use App\Api\Controllers\ConsumablesAPIController;
use App\Repositories\Customer\CustomerBaseRepository;
use App\Repositories\Customer\CustomerRepository;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Validator;
use \Log;
use Session;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Ewo_LoginController;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Login\LoginRepository;
use App\Repositories\Consumables\ConsumablesRepository;
use App\Services\User;

class Ewo_OrderController extends Controller
{

    private $statusType = array('0.預約','1.開通','1.分派','1.控制','2.改約');
    private $objTools;

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

        $this->objTools = new ewoToolsController();
    }


    public function index(Request $request, $cns='')
    {
        $time_s = microtime('true');
        $run_title = Session::get('userId').'_'.time();
        $userId = Session::get('userId');

        $redis = app('redis.connection');
        $rKey = 'lsImg_'.date('Ymd').'_'.$userId;
//        $rKeyCOImg = 'coImg_'.date('Ymd').'_'.$userId;

        // 勞安照片，先上傳
        $p_data['lsImg'] = 'Y';
        if(empty($redis->get($rKey))) {
            $p_data['lsImg'] = 'N';
        }

        try {

            $permissions = $this->User->checkPermissions($cns);

            if (!$permissions) {
                // return redirect('/ewo/login');
            }

            if(empty($cns)) {
                $p_token = $this->LoginRepository->getUserToken($userId);
                $cns = $p_token->token;
            }

            if (!$userId) {
                return redirect('/ewo/login');
            }

//            // 出班檢查
//            if(empty($redis->get($rKeyCOImg))) {
//                return redirect('ewo/func/ewoCheckOut');
//            }

            $limit = 10;
            $finPage = 1;
            $unFinPage = 1;
            $unplanPage = 1;

            $orders_unfinish = array();
            $orders_unPlan = array();
            $orders_finish = array();
            $total_unfin = 0;
            $total_unPlan = 0;
            $total_fin = 0;
            $data = array(
                'userId' => $userId,
                'bookdate_s' => date('Y-m-d 00:00:00'),
                'bookdate_e' => date('Y-m-d 00:00:00',strtotime('+3 day')),
//                'notBookingNo' => 'U 到宅取設備',
//                'assignsheet' => 'A202210AI22645',
            );
            $orderList = $this->OrderRepository->getOrderList($data);

            $orderList = $this->checkData($orderList);



            $alertList = array();
            // [MS0200].[CellPhone01] 簡訊手機
            // [MS0200].[CellPhone02] 手機(2)
            // [MS0200].[TeleNum01] 電話(1)
            // [MS0200].[TeleNum02] 電話(2)
            // [MS0200].[TeleNum03] 電話(3)
            $phone_type_mapping = [
                'CallinTele' =>  '來電電話',
                'CellPhone01' =>  '簡訊手機',
                'CellPhone02' =>  '手機(2)',
                'TeleNum01' =>  '電話(1)',
                'TeleNum02' =>  '電話(2)',
                'TeleNum03' =>  '電話(3)',
            ];
            foreach ($orderList as $k => $t) {
                /** 單筆加取資料開始 */
                //加取電話資料 start
                //建立暫存變數 Array
                $cust_phone_summary = array();
                //  MS0300 的驗證電話資料當中可用的電話放入 $available_phone 電話中
                foreach ( ['CallinTele'] as $ms300_phone_key ) {
                    $ms0300_phone_number = ewoToolsController::transferPhoneNumberToVerifyFormat($t['MSCityA']??'' , data_get($t,$ms300_phone_key)??'');
                    if ($ms0300_phone_number == '') {
                        continue;
                    }
                    $cust_phone_summary[$phone_type_mapping[$ms300_phone_key]] = $ms0300_phone_number;
                }
                $customer_id =  $t['CustID'];
                // 取得此客戶的 MS0200 表當中的電話不符合規則會被濾掉。
                $MS0200Phones = $this->CustomerRepository->getEnableCustomerByCustomerId($customer_id,data_get($t,'CompanyNo'));

                // ms0200 會有多筆客戶電話資料
                foreach ($MS0200Phones as $index => $MS0200Phone) {
                    foreach (['CellPhone01','CellPhone02','TeleNum01','TeleNum02','TeleNum03'] as $ms200_phone_key ) {
                        $ms0200_phone_number = ewoToolsController::transferPhoneNumberToVerifyFormat($MS0200Phone->MailCity??'', $MS0200Phone->$ms200_phone_key ?? '');
                        if ($ms0200_phone_number == '') {
                            continue;
                        }
                        // 第一筆 ms0200資料全部加入顯示的array
                        if ($index == 0) {
                            $cust_phone_summary[$phone_type_mapping[$ms200_phone_key]] = $ms0200_phone_number;
                        }
                        //非第一筆資料，且電話不再原先的電話 List 內，且電話也非空值，也要將資料加上去
                        if ($index !== 0 && !in_array($ms0200_phone_number,$cust_phone_summary)) {
                            $cust_phone_summary[sprintf('%s[%s]',$phone_type_mapping[$ms200_phone_key],$index)] = $ms0200_phone_number;
                        }
                    }
                }
                // 電話資料寫入工單
                data_set($t,'CustPhoneSummary',$cust_phone_summary);
                // 加取電話資料 end

                // 加取客戶Customer標籤，全部取回來。只有維修工單在 Blade 顯示
                // 建立 $customer_tag_contents 的暫存Array， 預設空 Array
                // 目前 api 回傳的資料 data 有可能是 null(無資料) 、 string (error)、 array(正常)
                $customer_tag_contents = [];
                $get_customer_tag_api_response = (new ewoToolsController())->getCacheData([
                    'companyNo' => data_get($t,'CompanyNo'),
                    'custId' => $customer_id,
                    'infoType' => 'alertInfo',
                ]);
                $api_response_data = data_get($get_customer_tag_api_response,'data');
                // 判斷 API 回傳的 Code
                // 註:目前只回傳　API　回傳成功的資料 code = 0000 的 ，異常狀態不顯示。　
                if (data_get($get_customer_tag_api_response,'code') === '0000') {
                    foreach (data_get($get_customer_tag_api_response,'data') ?? [] as $content) {
                            $customer_tag_contents[] = $content;
                    }
                }

                // 客戶標籤資料寫入工單
                data_set($t,'CustTagContents',$customer_tag_contents);

                /** 單筆加取資料結束 */

                $id = data_get($t,'Id');
                $companyno = data_get($t,'CompanyNo');
                $worksheet = $k;
                $custid = data_get($t,'CustID');
                $sheetstatus = data_get($t,'SheetStatus');
                $expected = data_get($t,'expected');
                $bookdate = data_get($t,'BookDate');
                $demolition = data_get($t,'demolition');
                $statusAry = data_get($t,'sheetstatusAry');
                $mailtitle = data_get($t,'MailTitle');
                $p_id = $companyno.'-'.$worksheet;
                $workkind = data_get($t,'WorkKind');
                $worker1 = data_get($t,'Worker1');
                $brokerKind = data_get($t,'BrokerKind');

                // 到宅取設備。
                $chkDeviceGet = substr($worksheet,-1);
                if($chkDeviceGet == 'U') {
                    $data = array(
                        'so' => $companyno,
                        'worksheet' => $worksheet,
                    );
                    $chkDeviceGetOrder = $this->OrderRepository->getOrderInfo($data);
                    $id = data_get($chkDeviceGetOrder,'Id');
                    data_set($t,'Id',$id);
                } else {
                    $chargeNameAry = data_get($t,'chargenameAry');
                    if(in_array($workkind,['3 拆機'])) {
                        if(!in_array('C0001 拆機工單',$chargeNameAry)) {
//                            continue;
                        }
                    }
                }

                // 判斷 wm_orderlist，自動建立
                if(empty($id)) {
                    list($pdf_v,$pdfTerms) = $this->objTools->getPDFParams(['bookDate'=>$bookdate,'workKind'=>$workkind]);

                    // 判斷 遠傳 & 2023-03-25之後
                    if($brokerKind == '789 遠傳' && $bookdate >= '2023-03-25') {
                        $pdf_v = config('order.PDF_CODE_FET_V');
                    }
                    $worker1Ary = explode(' ',$worker1);
                    $data = [
                        'WorkerNum' => data_get($worker1Ary,0),
                        'WorkerName' => data_get($worker1Ary,1),
                        'so' => $companyno,
                        'worksheet' => $worksheet,
                        'custid' => $custid,
                        'bookdate' => $bookdate,
                        'saleAP' => '考慮中',
                        'pdf_v' => $pdf_v,
                        'pdfTerms' => $pdfTerms,
                    ];
                    $wmOrderListId = $this->OrderRepository->addOrderlist($data);
                    data_set($t,'Id',$wmOrderListId);
                }

                // 判斷條件，主工單號，狀態同時有取消、未完工項目
                $statusType = $this->statusType;
                $chkSheetStatus = substr($sheetstatus,0,1);
                foreach ($statusType as $k2 => $t2) {
                    if(in_array($t2,$statusAry) === true)
                        $chkSheetStatus = substr($t2,0,1);
                }

                // 工單依狀態分組 $t為工單資料
                if($chkSheetStatus == '4') { // 完工 [信用卡+完工 => 4 結案>>>4.結款]
                    $orders_finish[] = $t;
                    $total_fin++;
                } else if($chkSheetStatus == 'A') { // 取消
                    $orders_finish[] = $t;
                    $total_fin++;
                } else if($demolition === 'F') { // 拆機工單 ChargeNmae='C0001 拆機工單' && SheetStatus=4..
                    $orders_finish[] = $t;
                    $total_fin++;
                } else if(strlen($expected) < 1) { // 未約件
                    $orders_unPlan[] = $t;
                    $total_unPlan++;
                    $alert = $this->getAlertTime($t);
                    if(count($alert) > 0) $alertList = array_merge($alertList,$alert);
//                    if(count($alert) > 0) $alertList[$p_id] = $alert;
                } else if(date('Y-m-d',strtotime($expected)) != date('Y-m-d',strtotime($bookdate))) { // 未約件，日期不同
                    data_set($t,'expected','');
                    $orders_unPlan[] = $t;
                    $total_unPlan++;
                    $alert = $this->getAlertTime($t);
                    if(count($alert) > 0) $alertList = array_merge($alertList,$alert);
//                    if(count($alert) > 0) $alertList[$p_id] = $alert;
                } else { // 未完工
                    $orders_unfinish[] = $t;
                    $total_unfin++;
                    $alert = $this->getAlertTime($t);
                    if(count($alert) > 0) $alertList = array_merge($alertList,$alert);
//                    if(count($alert) > 0) $alertList[$p_id] = $alert;
                }
            }


            /*
             * // 2022-01-22停用
            // 勞安-檢點表
            $query_data = array(
                'type' => 'A.檢點表',
                'companyno' => $companyno,
                //'workernum' => $userId,
                //'bookdatestart' => date('Y-m-d 00:00:00'),
                //'bookdateend' => date('Y-m-d 23:59:59'),
            );
            $laborsafety = $this->OrderRepository->getLaborsafetyCheckList($query_data);
            $laborsafetyList = array();
            $laborsafetyHead = array();
            foreach($laborsafety as $k => $t) {
                $id = data_get($t,'Id');
                $desc1 = data_get($t,'Desc1');
                $desc2 = data_get($t,'Desc2');
                if(!in_array($desc1,$laborsafetyHead))
                    $laborsafetyHead[] = $desc1;
                $laborsafetyList[$desc1][$id] = $desc2;
            }
            $laborsafety = array(
                'companyno' => $companyno,
                'head' => $laborsafetyHead,
                'list' => $laborsafetyList,
            );
            */

            // 補簽名清單
//            $query_data = array(
//                'worknum'=>$userId,
//                'signauthorization'=>'signauthorization',
//            );
//            $addSignList = $this->OrderRepository->getOrderInfo($query_data,'all');
//            foreach ($addSignList as $t) {
//                $worksheet = data_get($t,'WorkSheet');
//
//                $servicename = data_get($t,'ServiceName');
////                $servicename = json_decode($servicename,1);
////                $servicename = explode(',',$servicename);
//                $servicename = str_replace('"','',$servicename);
//                $servicename = str_replace('[','',$servicename);
//                $servicename = str_replace(']','',$servicename);
//                $servicenameAry = explode(',',$servicename);
//                data_set($t,'servicenamelist',$servicename);
//                data_set($t,'servicenameary',$servicenameAry);
//
//                $custid = data_get($t,'CustID');
//                $bookdate = data_get($t,'BookDate');
//                $bookdateStr = str_replace('-','',$bookdate);
//                $bookdateStr = substr($bookdateStr,0,8);
//                $forder = $custid.'_'.$bookdateStr;
//                data_set($t,'forder',$forder);
//
//                $telenum01_200 = data_get($t,'TeleNum01_200');
//                $telenum01 = data_get($t,'TeleNum01');
//                $telenum02_200 = data_get($t,'TeleNum02_200');
//                $phoneAry = array($telenum01_200,$telenum01,$telenum02_200);
//                $phoneAry = array_unique($phoneAry);
//                $phoneAry = array_values($phoneAry);
//                $phonelist = implode(',',$phoneAry);
//                data_set($t,'phonelist',$phonelist);
//
//
//            }


            $unFinPageAll = 1;
            $finPageAll = 1;
            $unPlanPageAll = 1;

            $p_data['header'] = 'list';

            $p_data['orderList'] = $orderList;
            $p_data['userId'] = $userId;
            $p_data['userName'] = Session::get('userName');
            $p_data['fin_list'] = $orders_finish;
            $p_data['finPage'] = $finPage;
            $p_data['finPageAll'] = $finPageAll;
            $p_data['finish_count'] = $total_fin;

            $p_data['unFin_list'] = $orders_unfinish;
            $p_data['unFinPage'] = $unFinPage;
            $p_data['unFinPageAll'] = $unFinPageAll;
            $p_data['unfinish_count'] = $total_unfin;

            $p_data['unplan_list'] = $orders_unPlan;
            $p_data['unplanPage'] = $unplanPage;
            $p_data['unplanPageAll'] = $unPlanPageAll;
            $p_data['unplan_count'] = $total_unPlan;
            $p_data['BandwidthH'] = config('order.BandwidthH'); // 頻寬
            $p_data['BandwidthL'] = config('order.BandwidthL'); // 頻寬
            $p_data['tt'] = $cns;
            $p_data['select'] = data_get($request,'sel');
//            $p_data['addsign'] = $addSignList;
//            $p_data['laborsafety'] = $laborsafety;
            $p_data['alertList'] = $alertList;
//            $p_data['laborsafetyHead'] = $laborsafetyHead;
//            $p_data['laborsafetyList'] = $laborsafetyList;


            Log::channel('ewoLog')->info('EWO order_List '.$run_title.' 讀取時間='.number_format(microtime(true) - $time_s,5)."\n");

            return view('ewo.order_list', compact('p_data'));

        }  catch (MyException $e) {
            Session::put('error_msg',$e->getMessage());

        } catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

        }

        return redirect('/ewo/login');
    }


    // 區故
    public function getFault()
    {
        $client = new Client();
        $url = config('order.EWO_URL').'/api/EWO/getFault';
        $paramsAry = array();
        $postAry = array(
            'body' => json_encode($paramsAry),
            'headers' => ['Content-Type' => 'application/json',],
        );
        $result = $client->request('POST', $url, $postAry);
        $resultAry = json_decode($result->getBody(), true);

        $code = data_get($resultAry,'code');

        $ret = $code =='0000'? data_get($resultAry,'data') : [];

        return $ret;
    }


    public function search(Request $request, $cns)
    {

        $o_data = array();
        try {

            $userId = Session::get('userId');

            if (!$userId) {
                return redirect('/ewo/login');
            }

            $validator = Validator::make($request->all(), [
                'workKind'  => 'required',
                'finish'    => 'required',
                'finPage'   => 'required',
                'unFinPage' => 'required',
                'unPlanPage' => 'required',
            ],
            [
                'workKind.required' => '請選擇類別',
                'finish.required'   => '請選擇狀態',
                'finPage.required'  => '請選擇頁數finPage',
                'unFinPage.required'=> '請選擇頁數unFinPage',
                'unPlanPage.required'=> '請選擇頁數unPlanPage',
            ]);


            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };


            $filter = $request->all();
            $limit = 10;

            $finPage = data_get($filter,'finPage');
            $unFinPage = data_get($filter,'unFinPage');
            $unPlanPage = data_get($filter,'unPlanPage');
            $workKind = data_get($filter,'workKind');

            switch ($workKind) {
                case '1 裝機':
                    $workKind = array('1 裝機','2 復機','6 移機','8 工程收費','9 停後復機','A 加裝','C 換機');
                    break;
                case '3 拆除':
                    $workKind = array('3 拆機','4 停機','7 移拆','H 退拆設備','I 退拆分機','K 退次週期項');
                    break;
                case '5 維修':
                    $workKind = array('5 維修');
                    break;
            }


            $orders_unPlan = array();
            $orders_unfinish = array();
            $orders_finish = array();
            $total_unfin = 0;
            $total_unPlan = 0;
            $total_fin = 0;


            $data = array(
                'userId' => $userId,
                'bookdate_s' => date('Y-m-d 00:00:00'),
                'bookdate_e' => date('Y-m-d 00:00:00',strtotime('+2 day')),
                //'assignsheet' => 'A2021090020527',
            );

            if($workKind !== 'all')
                $data['workkind'] = $workKind;

            $orderList = $this->OrderRepository->getOrderList($data);

            $orderList = $this->checkData($orderList);

            foreach ($orderList as $k => $t) {

                $id = data_get($t,'Id');
                $companyno = data_get($t,'CompanyNo');
                $worksheet = $k;
                $custid = data_get($t,'CustID');
                $sheetstatus = data_get($t,'SheetStatus');
                $expected = data_get($t,'expected');
                $bookdate = data_get($t,'BookDate');
                $demolition = data_get($t,'demolition');

                if(substr($sheetstatus,0,1) == '4') { // 完工 [信用卡+完工 => 4 結案>>>4.結款]
                    $orders_finish[] = $t;
                    $total_fin++;
                } else if(substr($sheetstatus,0,1) == 'A') { // 取消
                    $orders_finish[] = $t;
                    $total_fin++;
                } else if($demolition === 'F') { // 拆機工單 ChargeNmae='C0001 拆機工單' && SheetStatus=4..
                    $orders_finish[] = $t;
                    $total_fin++;
                } else if(strlen($expected) < 1) { // 未約件
                    $orders_unPlan[] = $t;
                    $total_unPlan++;
                } else if(date('Y-m-d',strtotime($expected)) != date('Y-m-d',strtotime($bookdate))) { // 未約件，日期不同
                    data_set($t,'expected','');
                    $orders_unPlan[] = $t;
                    $total_unPlan++;
                } else { // 未完工
                    $orders_unfinish[] = $t;
                    $total_unfin++;
                }
            }

            $unFinPageAll = 1;
            $finPageAll = 1;
            $unPlanPageAll = 1;

            $o_data['unFin_list'] = $orders_unfinish;
            $o_data['unFinPage'] = $unFinPage;
            $o_data['unFinPageAll'] = $unFinPageAll;
            $o_data['unfinish_count'] = $total_unfin;

            $o_data['fin_list'] = $orders_finish;
            $o_data['finPage'] = $finPage;
            $o_data['finPageAll'] = $finPageAll;
            $o_data['finish_count'] = $total_fin;

            $o_data['unplan_list'] = $orders_unPlan;
            $o_data['unplanPage'] = $unPlanPage;
            $o_data['unplanPageAll'] = $unPlanPageAll;
            $o_data['unplan_count'] = $total_unPlan;

            $code = '0000';
            $status = 'OK';
            $meg = '';

        }  catch (MyException $e) {
            $code = '05001';
            $status = 'error';
            $meg = $e->getMessage();
        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '05002';
            $status = 'error';
            $meg = '資料錯誤';
        }

        $ret = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => $meg,
            'data' => $o_data,
        );


        return $ret;
    }


    private function checkData($orders)
    {

        $orderNew = array();

        $solist = config('company.compCode');
        $solist = array_keys($solist);
        $solist = implode(',',$solist);
        $solist = explode(',',$solist);
        $deviceAry = config('device.ChargeName');
        $cleanCauseAry = array();
        $chkChargeName0701 = array(
            '24094 安裝設定(紅外線智慧遙控器)',
            '24095 安裝設定(智慧小音箱)',
            '26829 紅外線智慧遙控器',
            '26834 GOOGLE智慧小音箱(NEST-MINI)',
        );
        $saleCampaignAry = array(); // 方案別
        $fault = $this->getFault();
        $wifiChargeNameLis = $this->getWiFiForChargeName();

        foreach ($orders as $k => $t) {
            $assignsheet = data_get($t,'AssignSheet');
            $subsid = data_get($t,'SubsID');
            $worksheet = data_get($t,'WorkSheet');
            $workkind = data_get($t,'WorkKind');
            $workkindAry = explode(' ',$workkind);
            $billamt = data_get($t,'BillAmt');
            $printbillyn = data_get($t,'PrintBillYN');
            $servicename = data_get($t,'ServiceName');
            $chargename = data_get($t,'ChargeName');
            $sheetstatus = data_get($t,'SheetStatus');
            $mscomment1 = data_get($t,'MSComment1');
            $msremark = data_get($t,'MSRemark');
            $brokerkind = data_get($t,'BrokerKind');
            $companyno = data_get($t,'CompanyNo');
            $bookingNo = data_get($t,'BookingNo');
            $packagename = data_get($t,'PackageName');
            $chargekind = data_get($t,'ChargeKind');

            // MESH、AP數量統計
            if(in_array($chargekind,['40'])) {
                if(strpos($chargename,'MESH(')) {
                    if(!isset($saleCampaignAry[$assignsheet]))
                        $saleCampaignAry[$assignsheet]['MESH'][] = $packagename;
                    elseif(!in_array($packagename,$saleCampaignAry[$assignsheet]))
                        $saleCampaignAry[$assignsheet]['MESH'][] = $packagename;
                }
                if(strpos($chargename,'AP(')) {
                    if(!isset($saleCampaignAry[$assignsheet]))
                        $saleCampaignAry[$assignsheet]['AP'][] = $packagename;
                    elseif(!in_array($packagename,$saleCampaignAry[$assignsheet]))
                        $saleCampaignAry[$assignsheet]['AP'][] = $packagename;
                }
            }

            // 設備清單
            $orderNew[$assignsheet]['planDevice']['null'] = '';
            $workkindType = config('order.CahrgeBackType.'.$workkind);
            if($sheetstatus < '4' && $workkindType !== '11') {
                $deviceStr = data_get($deviceAry,$chargename);
                if(!empty($deviceStr))
                    $orderNew[$assignsheet]['planDevice'][$chargename] = $deviceStr;
            }

            if(!in_array($companyno,$solist))
                continue;

            // U 宅取設備
            if($bookingNo == 'U 到宅取設備') {
                if(!in_array($assignsheet,$cleanCauseAry)) {
                    array_push($cleanCauseAry,$assignsheet);
                    $orders[] = $t;
                }
            }

            // [取消]，不計算[MS0301.BillAmt]
            if(substr($sheetstatus,0,1) === 'A') {
                $billamt = 0;
            }

            // 判斷2 CM、D TWMBB，ChargeKind=20, 搜尋CMValue用
            if(in_array($servicename,array('2 CM','D TWMBB')) !== false) {
                if($chargekind == '20') {
                    $orderNew[$assignsheet]['cmvalue2'] = $chargename;
                }
            }

            $orderNew[$assignsheet]['subsidAry'][] = $subsid;
            $orderNew[$assignsheet]['worksheetAry'][] = $worksheet;
            $orderNew[$assignsheet]['worksheet_workkindAry'][] = $worksheet.'_'.data_get($workkindAry,'1');
            $orderNew[$assignsheet]['billamtAry'][] = $billamt;
            $orderNew[$assignsheet]['printbillynAry'][$worksheet] = $printbillyn;
            $orderNew[$assignsheet]['servicenameAry'][] = $servicename;
            $orderNew[$assignsheet]['chargenameAry'][] = $chargename;
            $orderNew[$assignsheet]['sheetstatusAry'][] = $sheetstatus;
            $orderNew[$assignsheet]['mscomment1Ary'][] = $mscomment1;
            if($brokerkind == '789 遠傳') {
                $orderNew[$assignsheet]['mscomment1Ary'][] = $msremark;
            }
            $orderNew[$assignsheet]['delayStatus'] = 'N';

            if($chargename == 'C0001 拆機工單') { //拆機工單 ChargeNmae='C0001 拆機工單' && SheetStatus=4..
                if(substr($sheetstatus,0,1) == '4' || substr($sheetstatus,0,1) == 'A') {
                    $orderNew[$assignsheet]['demolition'] = 'F';
                }
            }

            // 07-01符合條件，就需要出現提示文字
            if(in_array($chargename,$chkChargeName0701)) {
                $orderNew[$assignsheet]['chkChargeNameAlert0701'] = 'Y';
            } else {
                $orderNew[$assignsheet]['chkChargeNameAlert0701'] = '';
            }

            // 插入原始資料
            foreach($t as $k2 => $t2) {
                $orderNew[$assignsheet][$k2] = $t2;
                if($bookingNo == 'U 到宅取設備') {
                    $orderNew[$assignsheet.'U'][$k2] = $t2;
                    $orderNew[$assignsheet.'U']['WorkKind'] = 'U 到宅取設備';
                    $orderNew[$assignsheet.'U']['servicenameAry'][] = $servicename;
                    $orderNew[$assignsheet.'U']['sheetstatusAry'][] = $sheetstatus;
                    $orderNew[$assignsheet.'U']['billamtAry'] = [];
                    $orderNew[$assignsheet.'U']['printbillynAry'] = [];
                    $orderNew[$assignsheet.'U']['subsidAry'][] = $subsid;
                    $orderNew[$assignsheet.'U']['worksheetAry'][] = $worksheet;
                    $orderNew[$assignsheet.'U']['mscomment1Ary'] = [];
                    $orderNew[$assignsheet.'U']['worksheet_workkindAry'][] = $worksheet.'_'.data_get($workkindAry,'1');
                    $orderNew[$assignsheet.'U']['chkChargeNameAlert0701'][] = $orderNew[$assignsheet]['chkChargeNameAlert0701'];
                    if($k2 == 'AssignSheet') {
                        $orderNew[$assignsheet.'U']['AssignSheet'] = $assignsheet.'U';
                    }
                }
            }
        }

        foreach ($orderNew as $k => $t) {
            $companyno = data_get($t,'CompanyNo');
            if(in_array($companyno,$solist) === false)
                continue;

            // 預設欄位
            $orderNew[$k]['RecvAmtSum'] = '';
            $orderNew[$k]['CMValue'] = '';
            $orderNew[$k]['CMValueBox'] = array();
            $orderNew[$k]['TotalAmt'] = '';
            $orderNew[$k]['fault'] = '';
            $orderNew[$k]['alert135'] = '';
            $orderNew[$k]['PackageName2'] = '';
            $orderNew[$k]['fault'] = '';
            $orderNew[$k]['SaleCampaignAry'] = array(
                'MESH'=>isset($saleCampaignAry[$k]['MESH'])? sizeof($saleCampaignAry[$k]['MESH']):0,
                'AP'=>isset($saleCampaignAry[$k]['AP'])? sizeof($saleCampaignAry[$k]['AP']):0,
            );

            // 判斷[135 VIP-重點維繫大樓用戶]
            $mailtitle = data_get($t,'MailTitle');
            $chkMailtitle = array('135 VIP-重點維繫大樓用戶','136_VIP-1G到期重點維繫用戶');
            if(in_array($mailtitle,$chkMailtitle)) {
                $orderNew[$k]['alert135'] = $mailtitle;
            }

            // 宅取設備
            $getDeviceFH = substr($k,-1);

            $custid = data_get($t,'CustID');
            $servicenameAry = data_get($t,'servicenameAry');
            $chargenameAry = data_get($t,'chargenameAry');
            $companyno = data_get($t,'CompanyNo');
            $BillItem2 = (empty(data_get($t,'cmvalue2')))? data_get($t,'BillItem2') : data_get($t,'cmvalue2');
            $workkind = data_get($t,'WorkKind');
            $expected = data_get($t,'expected');
            $bookdate = data_get($t,'BookDate');
            $checkin = data_get($t,'checkin');
            $packagename = data_get($t,'PackageName');

            // 區故
            if(isset($fault[$companyno][$bookdate][$custid])) {
                $vFaultData = $fault[$companyno][$bookdate][$custid];
                $vEventResan = $vFaultData['EventResan'];
                $vEventKind = $vFaultData['EventKind'];
                $vEventTime = $vFaultData['EventTime'];
                $vWishTime = $vFaultData['WishTime'];
                $vTime = $vFaultData['time'];
                $p_fault = "$vEventResan#$vEventKind#預估時間:$vEventTime~$vWishTime($vTime)";
                $orderNew[$k]['fault'] = $p_fault;
            }

            // CMValue，供盒原則
            $p_servicenameChk = '2 CM';
            if(in_array($p_servicenameChk,$servicenameAry) >= 0 && !empty($BillItem2)) {
                $data = array(
                    'so' => $companyno,
                    'chargename' => $BillItem2,
                    'servicename' => $p_servicenameChk,
                );
                $queyrMS0400 = $this->OrderRepository->getMS0400($data);

                if(!empty(data_get($queyrMS0400[0],'CMValue'))) {
                    $orderNew[$k]['CMValue'] = data_get($queyrMS0400[0],'CMValue');
                    $p_cmvalue = $orderNew[$k]['CMValue'];

                    $p_cmvalue = explode('/',$p_cmvalue);
                    $p_cmvalue = intval(data_get($p_cmvalue,0));
                    if($p_cmvalue <= 200) {
                        $orderNew[$k]['CMValueBox'] = array('CM供盒順序原則：','CGNF','ARRIS_860P2');
                    } elseif($p_cmvalue >= 201 && $p_cmvalue <= 600) {
                        $orderNew[$k]['CMValueBox'] = array('CM供盒順序原則：','CGN5-AP2','CBW384G4');
                    } elseif($p_cmvalue >= 601) {
                        $orderNew[$k]['CMValueBox'] = array('CM供盒順序原則：','CGN5','CGNM3550','CG3368');
                    }
                }

                // 單購wifi案，供盒原則
                $whereAry = array(
                    'companyno' => $companyno,
                    'custid' => $custid,
                    'select' => array(
                        ['column' => 'ChargeName', 'asName' => 'ms0211'],
                    ),
                    'custstatusNotIn' => ['3 已拆'],
                    'leftMS0211' => 'Y',
                );

                // 用戶設備清單
                $queryMS0200 = $this->ConsumablesRepository->getMS0200($whereAry);
                $deviceNow = '無';
                if(is_array($queryMS0200) && count($queryMS0200) > 0) {
                    foreach ($queryMS0200 as $k700 => $t700) {
                        $v_chargeName = $t700->ChargeName;
                        if (in_array($v_chargeName, data_get($wifiChargeNameLis, 'data'))) {
                            $valAry = explode(' ', $v_chargeName);
                            $deviceNow = $valAry[1];
                        }
                    }
                }

                // 比對方案
                $p_dnStream = data_get($queyrMS0400[0],'DnStream');
                if(intval($p_dnStream) >= 500) {
                    $valAry = array('##Wi-Fi分享器單購案：','WIFI6數據機設備','##現有設備：',$deviceNow);
                } else {
                    $valAry = array('##Wi-Fi分享器單購案：','WIFI5數據機設備','##現有設備：',$deviceNow);
                }
                if(is_array($chargenameAry) && count($chargenameAry) > 0) {
                    foreach($chargenameAry as $k714 => $t714) {
                        if(in_array($t714,data_get($wifiChargeNameLis,'data'))) {
                            if(!in_array($p_servicenameChk,['1 CATV','3 DSTB'])) {
                                $orderNew[$k]['CMValueBox'] = array_merge($orderNew[$k]['CMValueBox'],$valAry);
                            }
                        }
                    }
                }
            }

            // 訂編
            $subsidAry = data_get($t,'subsidAry');
            $subsidAry = array_unique($subsidAry);
            $subsidAry = array_values($subsidAry);
            $orderNew[$k]['subsidAry'] = $subsidAry;

            // 工單號碼
            $worksheetAry = data_get($t,'worksheetAry');
            $worksheetAry = array_unique($worksheetAry);
            $worksheetAry = array_values($worksheetAry);
            $orderNew[$k]['worksheetAry'] = $worksheetAry;

            // ServiceName
            $servicenameAry = data_get($t,'servicenameAry');
            $servicenameAry = array_unique($servicenameAry);
            $servicenameAry = array_values($servicenameAry);
            $orderNew[$k]['servicenameAry'] = $servicenameAry;

            // 備註
            $mscomment1Ary = data_get($t,'mscomment1Ary');
            $mscomment1Ary = array_unique($mscomment1Ary);
            $mscomment1Ary = array_values($mscomment1Ary);
            $orderNew[$k]['MSComment1'] = implode(';',$mscomment1Ary);

            // 工單號碼+workkind
            $worksheet_workkindAry = data_get($t,'worksheet_workkindAry');
            $worksheet_workkindAry = array_unique($worksheet_workkindAry);
            $worksheet_workkindAry = array_values($worksheet_workkindAry);
            $orderNew[$k]['worksheet_workkindAry'] = $worksheet_workkindAry;

            // 遲到判斷
            $nowNum = (empty($checkin))? time() : strtotime($checkin);
            $delayStatus = 'N';
            if(empty($expected)) {
                $bookdateStr = strtotime($bookdate)+1800;
                if($bookdateStr < $nowNum)
                    $delayStatus = 'Y';
            } else {
                $expected2 = strtotime($expected)+1800;
                if($expected2 < $nowNum)
                    $delayStatus = 'Y';
            }
            if(in_array($workkind,array('3 拆機','4 停機','7 移拆','H 退拆設備','I 退拆分機','K 退次週期項'))) {
                // 拆機跳過
                $delayStatus = 'N';
            }
            $orderNew[$k]['delayStatus'] = $delayStatus;

            // 判斷[C 換機]加上 PackageName
            if($workkind == 'C 換機') {
                $orderNew[$k]['PackageName'] = $packagename;
            }

            // 貼標[C000003]檢查
            $data =  $data = array(
                'custid' => $custid,
                'announcer' => 'C0120 行銷部',
                'mssubject' => '高風險(品質)自動貼標',
                'announcet01Start' => $bookdate,
                'announcet01End' => $bookdate,
            );
            $alert_C000003 = $this->OrderRepository->getMS0392($data);
            if($alert_C000003['count'] > 0) $alert_C000003 = 'Y';
            $orderNew[$k]['alert_C000003'] = $alert_C000003;


            // 退單、完工判斷
            $sheetstatusAry = data_get($t,'sheetstatusAry');
            $sheetstatusAry = array_unique($sheetstatusAry);
            $sheetstatusAry = array_values($sheetstatusAry);
            $statusType = $this->statusType;
            $statusChk = true;
            foreach($statusType as $t4) {
                if(in_array($t4,$sheetstatusAry) === false) {
                    $statusChk = false;
                    break;
                }
            }
            if($statusChk) {
                $receiveMoney = data_get($t,'receiveMoney');
                $orderNew[$k]['TotalAmt'] = $receiveMoney;
                continue;
            }


            // MS0301金額合計
            $BillAmt = data_get($t,'billamtAry');
            $p_billamtSum = 0;
            foreach ($BillAmt as $k2 => $t2) {
                $p_billamtSum += (int)$t2;
            }
            $orderNew[$k]['BillAmt'] = $p_billamtSum;


            // MS3200金額合計
            $companyno = data_get($t,'CompanyNo');
            $printbillyn = data_get($t,'printbillynAry');
            $p_worksheetAry = array();
            $p_recvamtSum = '';
            foreach($printbillyn as $k2 => $t2) {
                if ($t2 == 'Y')
                    $p_worksheetAry[] = $k2;
            }
            if(count($p_worksheetAry)) {
                $data = array(
                    'so' => $companyno,
                    'inworksheet' => $p_worksheetAry,
                    'recvyn' => 'N',
                );
                $p_recvamtSum = $this->OrderRepository->getMS3200RecvAmttSum($data);
                $p_recvamtSum = $p_recvamtSum[0]->RecvAmtSum;
                $orderNew[$k]['RecvAmtSum'] = $p_recvamtSum;
            }


            // MS0301+MS3200金額合計
            $orderNew[$k]['TotalAmt'] = (int)$p_billamtSum + (int)$p_recvamtSum;

            // Expected[bokdate，前4後4]
            $orderNew[$k]['BookDateHS'] = (substr($bookdate,11,2) - 4) < 0? 0 : (substr($bookdate,11,2) - 4);
            $orderNew[$k]['BookDateHE'] = (substr($bookdate,11,2) + 4) > 23? 23 : (substr($bookdate,11,2) + 4);


        }

        $ret = $orderNew;

        return $ret;
    }

    // 區故，篩選信息
    public function getFaultDetil($data)
    {
        $event = '';
        $eventreason = '';
        $eventkind = '';
        foreach ($data as $k => $t) {
            $eventreason = data_get($t,'EventReason');
            $eventkind = data_get($t,'EventKind');
            if(!empty($eventkind) && empty($event))
                $event = $eventkind;
        }
        if(empty($event))
            $event = $eventreason;

        $eventAry = explode(' ',$event);

        $ret = data_get($eventAry,1);

        return $ret;
    }


    private function checkSO($so,$companyNo,$orders)
    {

        foreach ($orders as $key => $order) {

            $company_no = data_get($order, "CompanyNo");

            if (array_key_exists($company_no, $so)) {
                continue;
            }

            $so[$company_no] = $companyNo[$company_no];
        }

        return $so;
    }

//private $test_id = 1;
    // 鬧鈴時間計算
    public function getAlertTime($data)
    {
        $custid = data_get($data,'CustID');
        $bookdate = data_get($data,'BookDate');
        $expected = data_get($data,'expected');
        $chkDate = empty($expected)? $bookdate : $expected;
        $chkDate_sec = strtotime($chkDate);
        $interval = 30 * 60;
        $p_time_sec = time();
        $p_id = $custid;
        if(($chkDate_sec - $interval) > ($p_time_sec)) {
            $sec = $chkDate_sec - $interval - $p_time_sec;
        } else {
            return array();
        }

        $title = '用戶:'.$custid;
        $body = '預約時間'.substr($chkDate,0,19).'請留意時間。';

        $sec_range = 60 * 5;

        $ret = array();
        for($i = 1; $i <= 4; $i++) {
            $sec -= $sec_range;
            if($sec < 1) continue;

            $ret[$p_id.$i] = array('title' => $title, 'body' => $body, 'sec' => $sec, 'id' => $p_id.$i);
        }

        return $ret;
    }


    public function test_print($data=array()){

        echo '<pre>';
        print_r($data);
        echo '</pre>';
        exit('end');

    }


    // wifi 單購方案
    public function getWiFiForChargeName()
    {
        $redis = app('redis.connection');
        $p_time = date('Y-m-d H:i:s');
        $sec = 60 * 60;
        $vKey = 'getWiFiForChargeNameList';

        if($redis->exists($vKey)) {
            $data = $redis->get($vKey);

            if(empty($data)) {
                $code = '0548';
                $data = '查無DB';
            } else {
                $code = '0000';
                $data = json_decode($data,true);
            }

            $ret = array(
                'code' => $code,
                'data' => $data,
                'date' => $p_time,
            );
            return $ret;
        }

        $sql = <<<EOF
SELECT
    DISTINCT b.ChargeName
FROM cossdb.dbo.MS00430 a with (nolock)
INNER JOIN cossdb.dbo.ms00431 b with (nolock) ON a.addcode=b.addcode
WHERE 1=1
    AND A.CompanyNo=B.CompanyNo
    AND A.AddKind IN('2 加購')
    AND a.StopYN='N'
    AND b.ServiceName IN('2 CM','B FTTH','D TWMBB')
    AND (a.AddCode LIKE'PUB0%')
    AND (b.ChargeName LIKE'%租借%')
EOF;

        $query = DB::connection('COSSDB')->select($sql);
        $list = array();
        foreach($query as $k => $t) {
            $list = array_merge($list,[$t->ChargeName]);
        }
        $listJson = json_encode($list);
        $redis->set($vKey,$listJson);
        $redis->expire($vKey,$sec);

        $ret = array(
            'code' => '0000',
            'data' => $list,
            'date' => $p_time,
        );

        return $ret;
    }


}
