<?php

namespace App\Http\Controllers;

use App\Api\Controllers\ConsumablesAPIController;
use App\Model\MS021A;
use App\Repositories\Consumables\ConsumablesRepository;
use App\Repositories\Log\LogRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Validator;
use \Log;
use Session;
use Exception;
use App\Model\wm_usermang;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MyException;
use \App\Http\Controllers\Ewo_EventController;
use App\Http\Controllers\Ewo_LoginController;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Reason\ReasonRepository;
use App\Services\User;
use function Symfony\Component\Translation\t;


class Ewo_OrderInfoController extends Controller
{

    public function __construct(
        OrderRepository $OrderRepository,
        CustomerRepository $CustomerRepository,
        User $User,
        LogRepository $LogRepository,
        ReasonRepository $ReasonRepository,
        Ewo_LoginController $loginController
    )
    {
        $this->OrderRepository = $OrderRepository;
        $this->CustomerRepository = $CustomerRepository;
        $this->User = $User;
        $this->LogRepository = $LogRepository;
        $this->ReasonRepository = $ReasonRepository;
        $this->loginController = $loginController;
        $this->toolsEwo = new ewoToolsController();
    }

    public function index(Request $request, $ws, $cns)
    {

        $time_s = microtime('true');
        $run_title = Session::get('userId').'_'.rand();
        $p_userId = Session::get('userId');
        $p_uuid = Session::get('uuid');
        $p_istest = Session::get('IsTest');

        //Log::channel('ewoLog')->info('chk detail function index() '.$run_title.' run start=='.$time_s);

        try {

            // 檢查UUID，0825停用
            //$this->loginController->checkUUID($p_userId,$p_uuid);

            $permissions = $this->User->checkPermissions($cns);

            if (!$permissions) {
                // return redirect('/ewo/login');
            }

            if (empty($ws)) {
                throw new Exception("資料錯誤");
            };

            $redis = app('redis.connection');
            $userId = Session::get('userId');
            $mobile = Session::get('mobile');
            $rKeyCOImg = 'coImg_'.date('Ymd').'_'.$userId;
            // $redis->set($rKeyCOImg,1); // for 開發本機測試，跳過出班檢查 上正式站請註解
            $printPDF = data_get($request,'printpdf');
            if($printPDF == 'Y') {
                // 模擬登入
                $userId = data_get($request,'userid');
                Session::put('userId',$userId);
            } elseif (!$userId) {
                // 沒有登入
                return redirect('/ewo/login');
            } elseif(empty($redis->get($rKeyCOImg))) {
                // 出班檢查
//                if(date('Ymd') < '20230331')
                return redirect('ewo/func/ewoCheckOut');
            }

            $ws = explode("-",$ws);
            $so = $ws[0];
            $worksheet = $ws[1];

            $data = array(
                'so' => $so,
                'worksheet' => $worksheet,
                'userId' => $userId,
            );

            $sessionKey = $so.'_'.$worksheet.'_';

            $wm_order_info = $this->OrderRepository->getOrderInfo($data);

            // 到宅取設備
            $chkDevicGet = substr($worksheet,-1);
            if($chkDevicGet == 'U') {
                $data['worksheet'] = str_replace('U','',$worksheet);
            }

            $order_info = $this->OrderRepository->getOrderInforNEW($data);

            //Log::channel('ewoLog')->info('chk orderinfocontroller index order_info=='.print_r($order_info,1));

            // pdf 版本、條款
            list($p_pdf_v,$p_pdfterms) = (new ewoToolsController())->getPDFParams(['bookDate'=>data_get($order_info[0],'BookDate'),'workKind'=>data_get($order_info[0],'WorkKind')]);

            $p_data['pdf_v'] = $p_pdf_v;
            $p_data['pdfTerms'] = $p_pdfterms;

            // WM 缺失資料
            if(!isset($wm_order_info->Id)) {
                $data['custid'] = data_get($order_info[0],'CustID');

                $data['pdf_v'] = $p_data['pdf_v'];
                $data['pdfTerms'] = $p_data['pdfTerms'];

                $wmOrderListId = $this->OrderRepository->addOrderlist($data);
                $wm_order_info = $this->OrderRepository->getOrderInfo($data);
            }
            $order_info = $this->checkData($worksheet,$order_info);
            $workKindIns = config('order.workKindIns');

            $order_info['Id'] = $wm_order_info->Id;
            $order_info['WorkSheet'] = $worksheet;
            $order_info['checkin'] = $wm_order_info->checkin;
            $order_info['delatedesc'] = $wm_order_info->delatedesc;
            $order_info['chargeback'] = $wm_order_info->chargeback;

            $order_info['id01'] = $wm_order_info->id01;
            $order_info['id02'] = $wm_order_info->id02;
            $order_info['id03Photo'] = $wm_order_info->id03Photo;
            $order_info['cert01'] = $wm_order_info->cert01;
            $order_info['cert02'] = $wm_order_info->cert02;
            $order_info['constructionPhoto'] = $wm_order_info->constructionPhoto;
            $order_info['serviceResonTime'] = $wm_order_info->serviceResonTime;
            $order_info['receiveType'] = $wm_order_info->receiveType;
            $order_info['receiveMoney'] = $wm_order_info->receiveMoney;
            $order_info['serviceResonFirst'] = $wm_order_info->serviceResonFirst;
            $order_info['serviceResonLast'] = $wm_order_info->serviceResonLast;
            $order_info['sentmail'] = $wm_order_info->sentmail;
            $order_info['sign_dstb'] = $wm_order_info->sign_dstb;
            $order_info['sign_cm'] = $wm_order_info->sign_cm;
            $order_info['sign_twmbb'] = $wm_order_info->sign_twmbb;
            $order_info['sign_mengineer'] = $wm_order_info->sign_mengineer;
            $order_info['sign_mcust'] = $wm_order_info->sign_mcust;
            $order_info['sign_ccadaf'] = $wm_order_info->sign_ccadaf;
            $order_info['etf_ccada'] = $wm_order_info->etf_ccada;
            $order_info['openApi'] = $wm_order_info->openApi;
            $order_info['PaperPDF'] = $wm_order_info->PaperPDF;
            $order_info['delate'] = $wm_order_info->delate;
            $order_info['certified'] = $wm_order_info->certified;
            $order_info['saleAP'] = $wm_order_info->saleAP;
            $order_info['termsi'] = $wm_order_info->termsi;
            $order_info['termsd'] = $wm_order_info->termsd;
//            $order_info['ccadaf'] = data_get($order_info,'ccadaf');
//            $order_info['ach'] = data_get($order_info,'ach');
            $order_info['custGps'] = data_get($wm_order_info,'custGps') ?? data_get($order_info,'custGps');
//            $order_info['custAddress'] = data_get($wm_order_info,'custAddress') ?? data_get($order_info,'custAddress');
            $order_info['gps'] = data_get($wm_order_info,'gps');
            $order_info['gpsRefAddres'] = data_get($wm_order_info,'gpsRefAddres');
            $order_info['gpsDistance'] = data_get($wm_order_info,'gpsDistance');
            $order_info['etf_ach'] = data_get($wm_order_info,'etf_ach');
            $order_info['lineWill'] = data_get($wm_order_info,'lineWill');
            $order_info['BorrowmingList'] = data_get($wm_order_info,'BorrowmingList');
            $order_info['RetrieveList'] = data_get($wm_order_info,'RetrieveList');
            $order_info['deviceChk'] = data_get($wm_order_info,'deviceChk');
            $order_info['pdfTerms'] = data_get($wm_order_info,'pdfTerms');

            Session()->put($sessionKey.'orderInfo', $order_info);

            $cust_Id = data_get($order_info, "CustID");
            $companyNo = data_get($order_info, "CompanyNo");

            $workKind = data_get($order_info, "WorkKind");
            $finsh = data_get($order_info, "finsh");
            $workerName = data_get($order_info, "WorkerName");
            $workerNum = data_get($order_info, "WorkerNum");
            $serviceName = data_get($order_info, "ServiceName");
            $bookDate = data_get($order_info, "BookDate");
            $createTime = data_get($order_info, "CreateTime");
            $sheetStatus = data_get($order_info, "SheetStatus");
            $saleAP = data_get($order_info, "saleAP");

            if($chkDevicGet == 'U') {
                $workKind = 'U 到宅取設備';
                data_set($order_info, 'WorkKind', 'U 到宅取設備');
            }

            $cus_data = array(
                'custId' => $cust_Id,
                'companyNo' => $companyNo,
                'custStatusNotIn' => array('3 已拆'),
                // 'subsId' => $subsId,
            );

            // oets 統計
            $parAry = array(
                'companyNo' => $companyNo,
                'custId' => $cust_Id,
            );
            $getOETS = $this->toolsEwo->qryOETSLabel01($parAry);
            $statAry = array();
            foreach($getOETS as $t) {
                if(isset($statAry[$t->label]))
                    $statAry[$t->label] += 1;
                else
                    $statAry[$t->label] = 1;
            }
            $str = '';
            foreach($statAry as $k => $t) {
                if($t)
                $str .= ", $k";
            }
            $str = empty($str)? '' : "客戶申告 $str 請多加注意";
            $order_info['getOETS'] = $str;

            // 不滿意統計 統計
            $parAry = array(
                'companyNo' => $companyNo,
                'custId' => $cust_Id,
            );
            $qryDissatisfied = $this->toolsEwo->qryDissatisfied($parAry);
            if($qryDissatisfied) {
                $qryDissatisfiedStr = '';
                foreach($qryDissatisfied as $k => $t) {
                    if($k == '統計筆數' || empty($t)) continue;
                    $qryDissatisfiedStr .= "$k, ";
                }
                $qryDissatisfiedStr = "滿意度(評價) $qryDissatisfiedStr (不滿意) 請用心服務";
            }
            $qryDissatisfiedStr = empty($qryDissatisfiedStr)? '' : "$qryDissatisfiedStr";
            $order_info['qryDissatisfied'] = $qryDissatisfiedStr;

            //******* 撈取收費細項  *******//


            $orderCharge = $this->OrderRepository->getOrderCharge($data);

            list($charges, $recvAmt,$worksheetAry, $finshSheet) = $this->classifyCharge($orderCharge);

            //順收金額
//            $itemBill = 0;
//            $PrintBillYNAry = data_get($order_info, "PrintBillYNAry");
//            foreach ($worksheetAry as $itemWorkSheet) {
//
//                if (array_key_exists($itemWorkSheet, $PrintBillYNAry)) {
//                    $PrintBillYN = $PrintBillYNAry[$itemWorkSheet];
//
//                    $data['worksheet'] = $itemWorkSheet;
//
//                    if ($PrintBillYN == 'Y') {
//                        $orderBill = $this->OrderRepository->getOrderBill($data,true);
//                        $orderBills = $this->OrderRepository->getOrderBill($data);
//
//                        foreach ($orderBill as $key => $item) {
//                            $amt = data_get($item,'RecvAmt');
//
//                            $itemBill += $amt;
//                        }
//
//                        foreach ($orderBills as $key => $item) {
//                            $amt = data_get($item,'RecvAmt');
//                            $itemChargeName = data_get($item,'ChargeName');
//                            $order_info['ChargeName'][] = $itemChargeName.' $'.$amt;
//
//                        }
//                    }
//                }
//            }


//            $data = array(
//                'so' => $so,
//                'inworksheet' => $worksheetAry,
//                'workrecvyn' => 'Y',
//            );
//            $p_recvamtSum = $this->OrderRepository->getMS3200RecvAmttSum($data);
//            $p_recvamtSum = $p_recvamtSum[0]->RecvAmtSum;
//            if(is_null($p_recvamtSum)) {
//                $p_recvamtSum = 0;
//            }

            $sheetStatus = substr($sheetStatus, 0,1);
            if ($sheetStatus < '4') {

                //順收金額
                $data = array(
                    'so' => $so,
                    'inworksheet' => $worksheetAry,
                    'workrecvyn' => 'Y',
                );
                $p_recvamtSum = $this->OrderRepository->getMS3200RecvAmttSum($data);
                $p_recvamtSum = $p_recvamtSum[0]->RecvAmtSum;
                if(is_null($p_recvamtSum)) {
                    $p_recvamtSum = 0;
                }
                $recvAmt += $p_recvamtSum;

            } else {
                $recvAmt = data_get($wm_order_info,'receiveMoney');
            }



            //******* 撈取收費細項 END *******//

            //******* 撈取用戶設備  *******//

            $custDives = array();
            $custDives = $this->CustomerRepository->getCustDevices($cus_data);
            $custDives2 = array();
            $srviceReason = '';
            $jsonDeviceCount = null;
            $jsonMaintainHistory = null;
            $jsonSWVersion = null;
            $maintainHistory = null;
            $devicChkData = null;
            $deviceDSTBCount = 0;

            if(strpos($order_info['WorkKindAryStr'],'換機')
                || strpos($order_info['WorkKindAryStr'],'維修') ) {
                $p_subsid_285 = '';
                foreach($custDives as $k => $t) {
                    $deviceDSTBCount += ($t->ServiceName == '3 DSTB' && !empty($t->SingleSN))? 1 : 0;
                    $p_subsid_285 = $t->SubsID;
                }
                // 只有一個DSTB設備，預設寫入。
                if(empty($order_info['deviceChk'])
                    && $deviceDSTBCount == 1
                    && !in_array($order_info['SheetStatus'],['4 結案','4.結款','A.取消'])) {
                    $p_value = array(
                        'subsid' => $p_subsid_285,
                        'time' => date('Y-m-d H:i:s'),
                    );
                    $p_updataTime = array(
                        'p_id' => $order_info['Id'],
                        'p_columnName' => 'deviceChk',
                        'p_userCode' => 'ewo sys',
                        'p_userName' => '系統自動新增',
                        'p_value' => json_encode($p_value),
                    );
                    $obj = New Ewo_EventController();
                    $obj->reqUpdataTime($p_updataTime);
                }
            }

            if (strpos($workKind,'維修')) {

                // 查詢AP類設備清單
                if(count($custDives) > 0 && 0) {
                    $custSubsID = array();
                    foreach($custDives as $k => $t) {
                        $p_subsid = data_get($t,'SubsID');
                        if(!empty($p_subsid))
                            $custSubsID[] = $p_subsid;
                    }

                    if(count($custSubsID) > 0) {
                        $dbDevice = new ConsumablesRepository();
                        $qryParam = array(
                            'companyNoIn' => array($companyNo),
                            'subsIDIn' => $custSubsID,
//                            'chargeNameLike' => '%AP%',
                            'chargeNameIn' => ['03670 bb-無線AP(借)','02360 DTV借用-無線AP','07150 無線AP','25187 TP-LINK-ARCHER-A6-AC1200-AP(買斷)'],
                        );
                        $qrygetCustDeviceMS0211 = $this->CustomerRepository->getCustDeviceMS0211($qryParam);
                        foreach ($qrygetCustDeviceMS0211 as $k => $t) {
                            $p_chargename = data_get($t, 'ChargeName');
                            $p_chargenameary = explode(' ', $p_chargename);
                            $p_singlesn = data_get($t, 'SingleSN');
                            $qryParam = array(
                                'singlesn' => $p_singlesn,
                                'companyno' => $companyNo,
                            );
                            $qryModel = $dbDevice->getDevLisFroPla($qryParam);
                            $qryModel = data_get($qryModel, 'list');
                            $qryModel = data_get($qryModel, '0');

                            $model = data_get($qryModel, 'CSModel');
                            $mtspec = data_get($qryModel, 'MTSpec');
                            $model = (is_null($model) || empty($model))
                                ? $mtspec : $model;
                            $custDives2[] = array(
                                'chargename' => $p_chargenameary[1],
                                'singlesn' => $p_singlesn,
                                'model' => $model,
                            );
                        }

                    }

                }

                $serviceName = explode(',', str_replace('C HS','2 CM',$serviceName));

                //維修原因
                $reasonData = array(
                    'services'=>$serviceName
                );

                $srviceReason = $this->ReasonRepository->getServiceReasonFirst($reasonData);

                $deviceCount =  $this->OrderRepository->getMaintainDeviceCount($cus_data);
                $maintainHistory =  $this->OrderRepository->getMaintainHistory($cus_data);
                $swVersion =  $this->OrderRepository->getMaintainDeviceSWVersion($cus_data);

                $swVersion = $this->checkMaintainVersion($swVersion);
                $deviceCount = $this->checkDeviceCount($deviceCount);

                $jsonDeviceCount =  json_encode($deviceCount);
                $jsonMaintainHistory =  json_encode($maintainHistory);
                $jsonSWVersion =  json_encode($swVersion);

            }

            //******* 撈取用戶設備 END *******//

            //******* 撈取退件原因  *******//

            $backReason = $this->ReasonRepository->getBackReason();

            //******* 撈取退件原因 END *******//

            //******* 完工檢核表[清單]  *******//

            $finishChkListAry = $this->getFinishCheckList();
            $value = $wm_order_info->dataList;
            $valueAry = json_decode($value,true);
            $chkFinishCheckList = empty(data_get($valueAry,'finishCheckList'))? 'N' : 'Y';

            //******* 完工檢核表[清單] END *******//

            //******* 撈取拆機流向  *******//

            $demolitionFlow = (in_array($workKind,['3 拆機','4 停機','7 移拆','H 退拆設備','I 退拆分機','K 退次週期項']))? $this->ReasonRepository->getDemolitionFlow() : '';

            //******* 撈取拆機流向 END *******//

//            //******* 同戶服務狀態  *******//
//
//            $sameAccountService = (in_array($workKind,['3 拆機','4 停機','7 移拆','H 退拆設備','I 退拆分機','K 退次週期項']))? $this->ReasonRepository->getSameAccountService($cust_Id,$companyNo) : '';
//            $sameAccountServiceHead = [
//                'SWVersion' => 'SWVersion',
//                'SingleSN' => 'SingleSN',
//                'ServiceName' => 'ServiceName',
//                'sameaddress' => '同址兩戶',
//            ];
//
//            //******* 同戶服務狀態 END *******//

            //******* 撈取工程人員貨倉  *******//

//            $placeData = array(
//                'so' => $so,
//                'placeNo' => $workerName,
//            );
//            $placeData['so'] = '250';
//            $placeData['placeNo'] = '陳泰全';
//            $workerPlace = $this->OrderRepository->getworkerPlaceInfo($placeData);
            $workerPlace = '';

            //******* 撈取工程人員貨倉 END *******//

            //******* 順推商品  *******//


            $serviceName_str = json_encode($serviceName);
            if(strpos($serviceName_str,'3 DSTB') !== false) {
                $chargeProduct[] = array('code'=>'16005','chargeName'=>'DTV-遙控器','baseAmt'=>'300','chargeKind'=>'40');
                $chargeProduct[] = array('code'=>'16083','chargeName'=>'DTV-遙控器(良品)','baseAmt'=>'100','chargeKind'=>'40');
                $chargeProduct[] = array('code'=>'16085','chargeName'=>'藍芽遙控器(買)','baseAmt'=>'500','chargeKind'=>'40');
                $chargeProduct[] = array('code'=>'16094','chargeName'=>'藍芽遙控器(良品)','baseAmt'=>'100','chargeKind'=>'40');
            } else
                $chargeProduct = [];


            //******* 順推商品 END *******//


            //******* 撈取五金耗料  *******//

                // 存檔內容
            $hardcons = $this->OrderRepository->getOrderHardcons($wm_order_info->Id);

                // 下拉選單，物料清單
            $hardconsList = $this->OrderRepository->getHardconsList($companyNo);


            //******* 撈取五金耗料 END *******//

            //******* 來電紀錄  *******//

            $callRecord = $this->OrderRepository->getCallRecord($cus_data);

            //******* 來電紀錄 END  *******//

            //******* 更新工單順收資料 *******//

            $finsh = data_get($order_info,'finsh');

            if (empty($finsh)) {
                $paidList = array();

                foreach ($worksheetAry as $item) {

                    $paidData = array(
                        'so' => $so,
                        'worksheet' => $item,
                    );

                    $orderBill = $this->OrderRepository->getOrderBill($paidData);

                    if (count($orderBill) >= 1) {
                        foreach ($orderBill as $key => $value) {
                            $RecvYN = data_get($value,'RecvYN');

                            if ($RecvYN == 'Y') {
                                $paidList[] = $value;
                            }
                        }
                    }
                }

                if (count($paidList) > 0) {
                    $jsonPaidList = json_encode($paidList);
                    $updatePaid = array(
                        'so' => $so,
                        'worksheet' => $worksheet,
                        'paidList' => $jsonPaidList,
                    );

                    $this->OrderRepository->updateOrderListPaidList($updatePaid);
                }

            }



            //******* 更新工單順收資料 END *******//

            //******* 更新工單資料 *******//

            $jsonServiceName = data_get($order_info,'ServiceName');
            $jsonServiceNameAry = explode(',', $jsonServiceName);
            $jsonServiceName = json_encode($jsonServiceNameAry);
            // [I]服務別
            $jsonServiceNameAry2 = $jsonServiceNameAry;
            if (($key = array_search('1 CATV', $jsonServiceNameAry2)) !== false) {
                unset($jsonServiceNameAry2[$key]);
            }
            if (($key = array_search('3 DSTB', $jsonServiceNameAry2)) !== false) {
                unset($jsonServiceNameAry2[$key]);
            }
            if (($key = array_search('C HS', $jsonServiceNameAry2)) !== false) {
                unset($jsonServiceNameAry2[$key]);
            }
            if (($key = array_search('F CML', $jsonServiceNameAry2)) !== false) {
                unset($jsonServiceNameAry2[$key]);
            }

            $jsonSubsID = data_get($order_info,'SubsID');
            $jsonSubsID = json_encode($jsonSubsID);

            $jsonSubsCP = data_get($order_info,'ivr');
            $jsonSubsCP = json_encode($jsonSubsCP);


            $subsidStr = implode(',',data_get($order_info,'SubsID'));
            data_set($order_info, 'substrStr',  $subsidStr);

            // 判斷 遠傳 &&　bookdate >= 2023-03-25
            $brokerKind = data_get($order_info,'BrokerKind');
            if($brokerKind == '789 遠傳' && substr($bookDate,0,10) >= '2023-03-25') {
                $p_pdf_v = config('order.PDF_CODE_FET_V');
            }
            data_set($order_info,'pdf_v',$p_pdf_v);

            $order_list_data = array(
                'so' => $so,
                'worksheet' => $worksheet,
                'finsh' => data_get($order_info,'finsh'),
                'AssignSheet' => data_get($order_info,'AssignSheet') ?? null,
                'ServiceName' => $jsonServiceName,
                'CustID' => data_get($order_info,'CustID'),
                'SubsID' => $jsonSubsID,
                'BookDate' => data_get($order_info,'BookDate'),
                'WorkKind' => data_get($order_info,'WorkKind'),
                'NetID' => data_get($order_info,'NetID'),
                'SaleCampaign' => data_get($order_info,'SaleCampaign'),
                'WorkerNum' => data_get($order_info,'WorkerNum'),
                'WorkerName' => data_get($order_info,'WorkerName'),
                'WorkTeam' => data_get($order_info,'WorkTeam'),
                'SubsCP' => $jsonSubsCP,
                'MSComment1' => data_get($order_info,'MSComment'),
                'deviceCount' => $jsonDeviceCount,
                'maintainHistory' => $jsonMaintainHistory,
                'deviceSWVersion' => $jsonSWVersion,
                'WorkCause' => data_get($order_info,'WorkCause'),
                'CustName' => data_get($order_info,'CustName'),
                'InstAddrName' => data_get($order_info,'InstAddrName'),
                'TeleNum' => data_get($order_info,'TeleNum'),
                'TeleCod01' => data_get($order_info,'TeleCod01'),
                'TeleNum01' => data_get($order_info,'TeleNum01'),
                'TeleCod02' => data_get($order_info,'TeleCod02'),
                'TeleNum02' => data_get($order_info,'TeleNum02'),
                'MSContract' => data_get($order_info,'MSContract'),
                'MSContract2' => data_get($order_info,'MSContract2'),
                'CreateName' => data_get($order_info,'CreateName'),
                'CustBroker' => data_get($order_info,'CustBroker'),
                'AcceptDate' => data_get($order_info,'AcceptDate'),
                'BrokerKind' => data_get($order_info,'BrokerKind'),
                'pdf_v' => $p_pdf_v,
                'pdfTerms' => $p_pdfterms,
            );

            $this->OrderRepository->updateOrderListData($order_list_data);

            //******* 更新工單資料 END *******//

            //******* 完工繳費簡訊[手機]     *******//
            $phoneNum = '';
            if(substr(data_get($order_info,'TeleNum'),0,2) === '09')
                $phoneNum = data_get($order_info,'TeleNum');
            elseif(substr(data_get($order_info,'CellPhone01'),0,2) === '09')
                $phoneNum = data_get($order_info,'CellPhone01');
            elseif(substr(data_get($order_info,'TeleNum01'),0,2) === '09')
                $phoneNum = data_get($order_info,'TeleNum01');
            elseif(substr(data_get($order_info,'TeleNum02'),0,2) === '09')
                $phoneNum = data_get($order_info,'TeleNum02');
            //******* 完工繳費簡訊[手機] END *******//

            //******* Wifi環境測試 Point Ary      *******//

            $wifiTestPointAry = ['裝機位置','客廳','廚房','臥室(起居室)','最遠位置'];
            $wifiTestFloorAry = ['單層','1F','2F','3F','4F','5F','B1'];

            //******* Wifi環境測試 Point Ary END  *******//


            //******* 授權刷卡   *******//
            $swipeAuthorization = data_get($wm_order_info,'SwipeAuthorization');
            //******* 授權刷卡 END******//


            //******* 勞安-危險地點     ******//
            $laborsafety_dangerplace = array();
            $instAddrName = data_get($order_list_data,'InstAddrName');

            $query_data = array(
                'so' => $companyNo,
                'type' => 'B.危險地點',
                'isenable' => 'Y',
            );
            $laborsafetyQry = $this->OrderRepository->getLaborsafetyDangerplace($query_data);
            foreach($laborsafetyQry as $k => $t) {
                $id = data_get($t,'Id');
                $desc1 = data_get($t,'Desc1');
                $desc2 = data_get($t,'Desc2');
                $desc3 = data_get($t,'Desc3');
                if(strpos($instAddrName,$desc1) !== false) {
                    $laborsafety_dangerplace = array($desc1,$desc2,$desc3,$id);
                }
            }

            //******* 勞安-危險地點 END ******//


            //******* 勞安-檢點表     ******//
            $laborsafetyCheckList = data_get($order_info,'laborsafetyCheckList');
            $query_data = array(
                'companyno' => $companyNo,
//                'worksheet' => $worksheet,
//                'workernum' => $workerNum,
                'type' => 'A.檢點表',
                'isenable' => 'Y',
            );
            $laborsafetyCheckList = data_get($wm_order_info,'laborsafetyCheckList');

            if($laborsafetyCheckList > 1) {
                $query_data['worksheet'] = $worksheet;
                $query_data['workernum'] = $workerNum;
                $query_data['bookdate'] = $bookDate;
            }
            $laborsafety = $this->OrderRepository->getLaborsafetyCheckList($query_data);

            $laborsafetyList = array();
            $laborsafetyHead = array();
            foreach($laborsafety as $k => $t) {
                $id = data_get($t,'Id');
                $desc1 = data_get($t,'Desc1');
                $desc2 = data_get($t,'Desc2');
                $reply = ($laborsafetyCheckList > 1)? data_get($t,'Reply') : '';
                if(!in_array($desc1,$laborsafetyHead))
                    $laborsafetyHead[] = $desc1;
                $laborsafetyList[$desc1][$id] =array('desc'=>$desc2,'reply'=>$reply);
            }

            $laborsafety_checklist = array(
                'companyno' => $companyNo,
                'head' => $laborsafetyHead,
                'list' => $laborsafetyList,
            );

            //******* 勞安-檢點表 END ******//


            //******* 區故   *******//

            $fault = $faultCreateDay = '無紀錄';

            // 區故-預約日
            $whereAry = array(
                'companyNo' => $companyNo,
                'custId' => $cust_Id,
                'date' => $bookDate,
            );
            $faultQuery = $this->getFault($whereAry);
            if(isset($faultQuery[$companyNo][$bookDate][$cust_Id])) {
                $vFaultData = $faultQuery[$companyNo][$bookDate][$cust_Id];
                $vEventResan = $vFaultData['EventResan'];
                $vEventKind = $vFaultData['EventKind'];
                $vEventTime = $vFaultData['EventTime'];
                $vWishTime = $vFaultData['WishTime'];
                $vTime = $vFaultData['time'];
                $fault = "$vEventResan#$vEventKind#預估時間:$vEventTime~$vWishTime($vTime)";
            }
            // 區故-受理日
            $faultOld = $this->getFault();
            if(isset($faultOld[$companyNo][$createTime][$cust_Id])) {
                $vFaultData = $faultOld[$companyNo][$bookDate][$cust_Id];
                $vEventResan = $vFaultData['EventResan'];
                $vEventKind = $vFaultData['EventKind'];
                $vEventTime = $vFaultData['EventTime'];
                $vWishTime = $vFaultData['WishTime'];
                $vTime = $vFaultData['time'];
                $faultCreateDay = "$vEventResan#$vEventKind#預估時間:$vEventTime~$vWishTime($vTime)";
            }
            //******* 區故 END  *******//

            //******* 客戶標籤訊息 Start   *******//
            // 加取客戶Customer標籤，全部取回來。只有維修工單在 Blade 顯示
            $customer_tag_contents = [];
            $get_customer_tag_api_response = (new ewoToolsController())->getCacheData(
                [
                    'companyNo' => $companyNo,
                    'custId' => $cust_Id,
                    'infoType' => 'alertInfo',
                ]
            );
            // 判斷 API 回傳的 Code
            // 註:目前只回傳　API　回傳成功的資料 code = 0000 的 ，異常狀態不顯示。　
            if (data_get($get_customer_tag_api_response,'code') === '0000') {
                foreach (data_get($get_customer_tag_api_response,'data') ?? [] as $content) {
                    $customer_tag_contents[] = $content;
                }
            }

            //******* 客戶標籤訊息 End  *******//


            // 設備借用/取回清單
            $qryData = array(
                'enable' => '1',
                'selectType' => '1',
            );
            $queryEquipment = $this->OrderRepository->get_wm_equipment($qryData);
            $equipmentList = array();
            foreach($queryEquipment as $k => $t) {
                $vType = $t->type;
                $equipmentList[$vType][] = (array)$t;
            }

            $company = config('company.database.name');

            $assignSheetAry = data_get($order_info,'AssignSheetAry');
            $assignSheetAry01 = data_get($assignSheetAry,$worksheet);
            $cmInfo = data_get($assignSheetAry01,'2 CM');
            $cmSubsId = data_get($cmInfo,'subsid');



            $companyName = $companyNo . " " . data_get($company, $companyNo);

            data_set($order_info, 'CompanyNoName',  $companyName);
            data_set($order_info, 'finshSheet',  $finshSheet);

            $user_info = array(
                'userId'=>$userId,
                'mobile'=>$mobile
            );

            Session()->put($sessionKey.'dstbcheck', $wm_order_info->dstbcheck);
            Session()->put($sessionKey.'cmcheck', $wm_order_info->cmcheck);
            Session()->put($sessionKey.'twmbbcheck', $wm_order_info->twmbbcheck);
            Session()->put($sessionKey.'BorrowmingList', $wm_order_info->BorrowmingList);
            Session()->put($sessionKey.'RetrieveList', $wm_order_info->RetrieveList);

            $p_data['header'] = 'info';
            $p_data['info'] = (object)$order_info;
            $p_data['assignSheetAryJson'] = json_encode($assignSheetAry);
            $p_data['user_info'] = $user_info;
            $p_data['cmSubsid'] = $cmSubsId;
            $p_data['tt'] = $cns;
            $p_data['dstbcheck'] = json_decode($wm_order_info->dstbcheck);
            $p_data['cmcheck'] = json_decode($wm_order_info->cmcheck);
            $p_data['twmbbcheck'] = json_decode($wm_order_info->twmbbcheck);
            $p_data['sign_mcust_select'] = data_get($p_data['twmbbcheck'],'sign_mcust_select');
            $p_data['borrowmingList'] = json_decode($wm_order_info->BorrowmingList);
            $p_data['retrieveList'] = json_decode($wm_order_info->RetrieveList);
            $p_data['retrieveListShow'] = (in_array($workKind,array('3 拆機','4 停機','7 移拆','H 退拆設備','I 退拆分機','K 退次週期項','5 維修','C 換機')) === true)? "Y" : "N";
            $p_data['custDives'] = $custDives;
            $p_data['deviceDSTBCount'] = $deviceDSTBCount;
            $p_data['custDives2'] = $custDives2;
            $p_data['srviceReasonFirst'] = $srviceReason;
            $p_data['backReason'] = $backReason;
            $p_data['finishChkList'] = $finishChkListAry;
            $p_data['chkFinishCheckList'] = $chkFinishCheckList;
//            $p_data['sameAccountService'] = $sameAccountService;
//            $p_data['sameAccountServiceHead'] = $sameAccountServiceHead;
            $p_data['wifiTestPointAry'] = $wifiTestPointAry;
            $p_data['wifiTestFloorAry'] = $wifiTestFloorAry;
            $p_data['demolitionFlow'] = $demolitionFlow;
            $p_data['workerPlace'] = $workerPlace;
            $p_data['chargeProduct'] = $chargeProduct;
            $p_data['uploaddir'] = $cust_Id.'_'.date('Ymd',strtotime($bookDate));
            $p_data['ymd'] = date('Ymd');
            $p_data['hardcons'] = $hardcons;
            $p_data['hardconsList'] = $hardconsList;
            $p_data['chargeInfo'] = $charges;
            $p_data['recvAmt'] = $recvAmt;
            $p_data['history'] = $maintainHistory;
            $p_data['callRecord'] = $callRecord;
            $p_data['phoneNum'] = $phoneNum;
            $p_data['saleAP'] = $saleAP;
            $p_data['IsTest'] = $p_istest;
            $p_data['fault'] = $fault;
            $p_data['faultCreateTime'] = $faultCreateDay;
            $p_data['swipeAuthorization'] = $swipeAuthorization;
            $p_data['instAddrName'] = $instAddrName;
            $p_data['laborsafety_dangerplace'] = $laborsafety_dangerplace;
            $p_data['laborsafety_checklist'] = $laborsafety_checklist;
            $p_data['equipmentList'] = $equipmentList;
            $p_data['serviceNameAry'] = $jsonServiceNameAry;
            $p_data['serviceNameAry2'] = implode('',$jsonServiceNameAry2);
            $p_data['BandwidthH'] = config('order.BandwidthH'); // 頻寬
            $p_data['BandwidthL'] = config('order.BandwidthL'); // 頻寬
            $p_data['CustTagContents'] = $customer_tag_contents; // 客戶標籤資料，目前只有維修工單要放


            if($printPDF == 'Y') {
                return 'OK';
            }

            $orderWorkKind = config('order.workKind');
            $orderType = $orderWorkKind[$workKind];

            if($chkDevicGet == 'U') {
                $orderType = '到宅取設備';
            }

            // 檢查後複製，app工程簽名
            $toolsObj = new ewoToolsController();
            $dataAry02 = array(
                'companyNo' => $companyNo,
                'custId' => $cust_Id,
                'workSheet' => $worksheet,
                'userCode' => $p_userId,
                'userName' => $p_data['info']->WorkerName,
                'bookDate' => $bookDate,
                'id' => $wm_order_info->Id,
            );

            if(date('Ymd') >= '20230316')
            $toolsObj->cpSignMengineer($dataAry02);
            //Log::channel('ewoLog')->info('chk detail function index() '.$run_title.' run end=='.number_format(microtime(true) - $time_s,5));
            switch ($orderType) {
                case '裝機':
                    return view('ewo.installed_order_info',compact('p_data'));
                case '維修':
                    return view('ewo.maintain_order_info',compact('p_data'));
                case '拆機':
                    return view('ewo.demolition_order_info',compact('p_data'));
                case '到宅取設備':
                    return view('ewo.deviceget_order_info',compact('p_data'));
            }


        }  catch (MyException $e) {
            Session::put('error_msg',$e->getMessage());
        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
        }

        //Log::channel('ewoLog')->info('chk detail function index() '.$run_title.' run end=='.number_format(microtime(true) - $time_s,5));
        return redirect('/ewo/order_list/'+$cns);
    }

    private function checkData($worksheet,$orders)
    {
        $newOrder = array();
        $serviceNameAry = array();
        $workKindAry = array();
        $subsIDAry = array();
        $assignSheetAry = array();
        $assignSheetItem = array();
        $ivr = array();
        $comment = array();
        $MSContract = array();
        $MSContract2 = array();
        $NetPoint = array();
        $userId = Session::get('userId');
        $PrintBillYNAry = array();
        $inpDevSin = array();
        $deviceGetList = array();
        $orgSingleSnList = array();
        $maintainCost = 0;
        $ccadaf = '';
        $ach = '';
        $subsIdForI = '';
        $serviceNameForI = '';
        $companyNo = '';
        $chkDeviceGet = substr($worksheet,'-1') == 'U'? 'Y' : 'N';
        $chkChargeNameAlert0701 = '';
        $chkChargeName0701 = array(
            '24094 安裝設定(紅外線智慧遙控器)',
            '24095 安裝設定(智慧小音箱)',
            '26829 紅外線智慧遙控器',
            '26834 GOOGLE智慧小音箱(NEST-MINI)',
        );
        $chkAlertMSG20221215 = '';

        $wifiChargeNameLis = $this->getWiFiForChargeName();
        $CahrgeBackType = config('order.CahrgeBackType');
        $orderWorkKind = config('order.workKind');

        foreach ($orders as $key => $order) {
            $workers = data_get($order,'Worker1');
            $workers = trim($workers);
            // $worker = explode(' ', $workers);
            $WorkerNum = '';

            $strIndex = strpos($workers,$userId);
            $orgSingleSN = data_get($order,'OrgSingleSN');
            $chargeKind = data_get($order,'ChargeKind');
            $serviceName = data_get($order,'ServiceName');
            $chargeName = data_get($order,'ChargeName');
            $workKind = data_get($order,'WorkKind');

            if ($strIndex !== false) {
                $WorkerNum = substr($workers, 0,strlen($userId));
                $WorkerName = substr($workers, strlen($userId));

                $WorkerNum = trim($WorkerNum);
                $WorkerName = trim($WorkerName);
                // $WorkerName = $worker[1] ?? $workers;
                // $WorkerNum = $worker[0] ?? $workers;
            }

            if ($WorkerNum != $userId) {
                continue;
            }

            //維修費用
            if(10) {
                if(in_array($chargeName, ['019 維修費','19991 維修服務費',      '19997 維修服務費(100)']) != false) {
                    if($chargeKind == '19') {
                        $maintainCost = intval(data_get($order,'BillAmt'));
                    }
                }
            }

            // 檢查 裝機、CM、高頻寬，提示訊息
            if($orderWorkKind[$workKind] == '裝機' && $serviceName == '2 CM' && $chargeKind == '20') {
                $parmAry = array(
                    'so' => $companyNo,
                    'chargename' => $chargeName,
                    'servicename' => $serviceName,
                );
                $query = $this->OrderRepository->getMS0400($parmAry);
                $query01 = data_get($query,'0');
                $dnStream = data_get($query01,'DnStream');
                if(intval($dnStream) > 500) {
                    $chkAlertMSG20221215 = 'Y';
                }
            }

            $newOrder['WorkerName'] = $WorkerName;
            $newOrder['WorkerNum'] = $WorkerNum;

            $newOrder['custGps'] = data_get($order,'custGps');
            $newOrder['custAddress'] = data_get($order,'custAddress');

            $newOrder['NodeNo'][] = data_get($order,'NodeNo');

            if (!in_array($serviceName, $serviceNameAry)) {
                $serviceNameAry[] = $serviceName;
            }

            // error_log(print_r($order,true));

            if (!in_array($workKind, $workKindAry)) {
                array_push($workKindAry,$workKind);
            }

            $workTeam = data_get($order,'WorkTeam');
            $newOrder['WorkTeam'] = $workTeam;


            $netID = data_get($order,'NetID');
            $newOrder['NetID'] = $netID;

            $saleCampaign = data_get($order,'SaleCampaign');
            $saleCampaign2 = data_get($order,'ServiceName') .'_'. data_get($order,'WorkKind');
            $newOrder['SaleCampaign'] = (empty($saleCampaign))? $saleCampaign2 : $saleCampaign;

            $custBrokers = data_get($order,'CustBroker');
            $custBroker = explode(' ', $custBrokers);
            $newOrder['custBroker'] = $custBroker[1] ?? $custBrokers;

            $subsCP2 = data_get($order,'SubsCP2');
            $smartCard = data_get($order,'SmartCard');
            $assignSheet = data_get($order,'WorkSheet');
            $IncludeHD = (in_array($chargeName,['02300 外接硬碟(借)','02301 外接硬碟(借)'])) ? '外接硬碟' : '';

            $ivr[$assignSheet] = $subsCP2;

            if (!in_array($assignSheet, $assignSheetItem) && $worksheet != $assignSheet) {
                $assignSheetItem[] = $assignSheet;
            }


            $finishDate = data_get($order,'FinishTime');
            $newOrder['finsh'] = $finishDate;

            $custID = data_get($order,'CustID');
            $newOrder['CustID'] = $custID;

            $companyNo = data_get($order,'CompanyNo');
            $newOrder['CompanyNo'] = $companyNo;

            $ADDRSORT = data_get($order,'ADDRSORT');
            $newOrder['ADDRSORT'] = $ADDRSORT;

            $bookDate = data_get($order,'BookDate');
            $newOrder['BookDate'] = $bookDate;

            $custName = data_get($order,'CustName');
            $newOrder['CustName'] = $custName;

            $custBroker = data_get($order,'CustBroker');
            $newOrder['CustBroker'] = $custBroker;

            $mailTitle = data_get($order,'MailTitle');
            $newOrder['MailTitle'] = $mailTitle;

            $instAddrName = data_get($order,'InstAddrName');
            $newOrder['InstAddrName'] = $instAddrName;

            $CellPhone01 = data_get($order,'CellPhone01');
            $newOrder['CellPhone01'] = $CellPhone01;

            $teleNum = data_get($order,'TeleNum');
            $newOrder['TeleNum'] = $teleNum;

            $teleCod01 = data_get($order,'TeleCod01');
            $newOrder['TeleCod01'] = $teleCod01;

            $teleNum01 = data_get($order,'TeleNum01');
            $newOrder['TeleNum01'] = $teleNum01;

            $teleCod02 = data_get($order,'TeleCod02');
            $newOrder['TeleCod02'] = $teleCod02;

            $teleNum02 = data_get($order,'TeleNum02');
            $newOrder['TeleNum02'] = $teleNum02;

            $createName = data_get($order,'CreateName');
            $newOrder['CreateName'] = $createName;

            $sheetStatus = data_get($order,'SheetStatus');
            $newOrder['SheetStatus'] = $sheetStatus;

            $acceptDate = data_get($order,'AcceptDate');
            $newOrder['AcceptDate'] = $acceptDate;

            $brokerKind = data_get($order,'BrokerKind');
            $newOrder['BrokerKind'] = $brokerKind;

            $InvUnifyNo = data_get($order,'InvUnifyNo');
            $newOrder['InvUnifyNo'][] = $InvUnifyNo;

            $subsID = data_get($order,'SubsID');
            $subsIDAry[] = (string)$subsID;

            $MSComment = data_get($order,'MSComment1');
            $comment[] = $MSComment;

            $LinkID = data_get($order,'LinkID');
            $NetPoint[] = $LinkID;
            $newOrder['LinkID'][] = $LinkID;

            $etf_ach = data_get($order,'etf_ach');
            $newOrder['etf_ach'] = $etf_ach;

            $chargeNameAry = explode(' ',$chargeName);
            $billAmt = data_get($order,'BillAmt');
            $PrintBillYN = data_get($order,'PrintBillYN');
            $payName = data_get($order,'PayName');

            $PrintBillYNAry[$assignSheet] = $PrintBillYN;

            if (!array_key_exists('ChargeName', $newOrder)) {
                $newOrder['ChargeName'] = array();
            }

            /*
             * 2022-07-01
             * 安裝內容提示
             * */
            if(in_array($chargeName,$chkChargeName0701) && empty($chkChargeNameAlert0701)) {
                $chkChargeNameAlert0701 = 'Y';
            }

            if ($chargeKind != '50' && $PrintBillYN == 'Y') {
                $newOrder['ChargeName'][] = $chargeName.' $'.$billAmt;
            }

            // 到宅取設備，清單
            if($chkDeviceGet == 'Y') {
                if(in_array($chargeKind,['17','40','50']) && $chargeName <> '00470 借用單') {
                    $deviceGetList[$serviceName][] = array(
                        'subsId' => $subsID,
                        'chargeName' => $chargeName,
                        'orgSingleSn' => $orgSingleSN,
                    );
                    if(strlen($orgSingleSN))
                        $orgSingleSnList[] = $orgSingleSN;
                }
            }

            // 信用卡定扣
            if ($chargeKind === '20' &&  $ccadaf === '') {
                $billPrd = data_get($order,'BillPrd');
                $saleCampaignAry = explode(' ',$saleCampaign);
                if(in_array($saleCampaignAry[0],['6050081','6100502']) && in_array($billPrd,['3'])) {
                    $ccadaf = 'Y';
                }
            }

            $assignSheetAry[$assignSheet][$serviceName]['subsid'] = $subsID;
            $assignSheetAry[$assignSheet][$serviceName]['subscp2'] = $subsCP2;
            $assignSheetAry[$assignSheet][$serviceName]['smartcard'] = $smartCard;
            $assignSheetAry[$assignSheet][$serviceName]['workKind'] = $workKind;

            // 外接硬碟
            if(isset($assignSheetAry[$assignSheet][$serviceName]['IncludeHD'])) {
                $assignSheetAry[$assignSheet][$serviceName]['IncludeHD'] .= $IncludeHD;
            } else {
                $assignSheetAry[$assignSheet][$serviceName]['IncludeHD'] = $IncludeHD;
            }

            $assignsheetKeys = array_keys($assignSheetAry);

            $workCause = data_get($order,'WorkCause');
            $newOrder['WorkCause'] = $workCause;

            $saleKind = data_get($order,'SaleKind');
            if ($saleKind != 'Z 退項') {
                $MSContract[] = data_get($order,'MSContract');
                $MSContract2[] = data_get($order,'MSContract2');
            }

            // 檢查設備[CahrgeName]回填序號>>裝機類
            if($CahrgeBackType[$workKind] === '4') {
                $chkChargeName = config('order.SEtDeviceChargeName');
                $singleSn = data_get($order,'SingleSN');
                $swVersion = data_get($order,'SWVersion');
                if(in_array($chargeName,$chkChargeName)) {
                    $inpDevSin[] = array('subsId'=>$subsID,'chargeKind'=>$chargeKind,'chargeName'=>$chargeName,'singleSn'=>$singleSn,'swVersion'=>$swVersion);
                }
                elseif(in_array($chargeName,data_get($wifiChargeNameLis,'data'))) {
                    $inpDevSin[] = array('subsId'=>$subsID,'chargeKind'=>$chargeKind,'chargeName'=>$chargeName,'singleSn'=>$singleSn,'swVersion'=>$swVersion);
                }
                // 模糊比對，wifi 6 mesh
                elseif(strpos($chargeName,'WIFI-6-(MESH)')){
                    $inpDevSin[] = array('subsId'=>$subsID,'chargeKind'=>$chargeKind,'chargeName'=>$chargeName,'singleSn'=>$singleSn,'swVersion'=>$swVersion);
                }
                // 模糊比對，wifi 5 mesh
                elseif(strpos($chargeName,'WIFI-5-(MESH)')){
                    $inpDevSin[] = array('subsId'=>$subsID,'chargeKind'=>$chargeKind,'chargeName'=>$chargeName,'singleSn'=>$singleSn,'swVersion'=>$swVersion);
                }
                // ChargeName Code，wifi 5 mesh
                elseif(in_array($chargeNameAry[0],['06380','06381','06390','06391'])){
                    $inpDevSin[] = array('subsId'=>$subsID,'chargeKind'=>$chargeKind,'chargeName'=>$chargeName,'singleSn'=>$singleSn,'swVersion'=>$swVersion);
                }
            }

            // 判斷[I]服務別
            if(empty($serviceNameForI) && !in_array($serviceName,['1 CATV','3 DSTB'])) {
                $serviceNameForI = $serviceName;
                $subsIdForI = $subsID;
            }

        } // foreach END

        $orgSingleSnList = array_values($orgSingleSnList);

        // 工單同戶設備
        $query_data = array(
            'companyno' => $companyNo,
            'custid' => $custID,
        );
        $deviceQuery = $this->OrderRepository->getMS0200($query_data);
        $newOrder['deviceList'] = $deviceQuery;


        // 同址設備清單
        $whereAry = array(
            'companyNo' => $companyNo,
            'custId' => $custID,
            'ADDRSORT' => $newOrder['ADDRSORT'],
        );
        $deviceOnAddr = $this->OrderRepository->getDeviceListOnAddr($whereAry);
        $newOrder['deviceOnAddr'] = $deviceOnAddr;


        // 固[I]資訊
        $fixedIP = array('訂編['.$subsIdForI.']'=>'查無資料');
        if(!empty($serviceNameForI)) {
            $queryMS021A = MS021A::select('CpeMac','FixedIP')
            ->where([
                'CompanyNo' => $companyNo,
                'SubsID' => $subsIdForI,
                'StopYN' => 'Y',
            ])
            ->first();
            if(($queryMS021A)) {
                $fixedIP = array(
                    'CpeMac'=>$queryMS021A->CpeMac,
                    'FixedIP'=>$queryMS021A->FixedIP,
                );
            }
        }
        $newOrder['serviceNameForI'] = $serviceNameForI;
        $newOrder['fixedIP'] = $fixedIP;

        $newOrder['ServiceName'] = implode(',', $serviceNameAry);

        $newOrder['ivr'] = $ivr;

        $ivr = implode('; ', array_map(
            function ($v, $k) { return sprintf("%s=%s", $k, $v); },
            $ivr,
            array_keys($ivr)
        ));

        $newOrder['SubsCP'] = $ivr;

        sort($workKindAry);
        $newOrder['WorkKind'] = $workKindAry[0];
        $newOrder['WorkKindAry'] = $workKindAry;
        $newOrder['WorkKindAryStr'] = implode(',',$workKindAry);

        $subsIDAry = array_unique($subsIDAry);
        $subsIDAry = array_values($subsIDAry);
        $newOrder['SubsID'] = $subsIDAry;

        $newOrder['AssignSheetAry'] = $assignSheetAry;

        $newOrder['AssignSheet'] = '';
        if (count($assignSheetItem) >= 1) {
            $newOrder['AssignSheet'] = implode(',', $assignSheetItem);
        }

        $comment = array_unique($comment);
        $comment = array_values($comment);
        $newOrder['MSComment'] = implode('###', $comment);

        $MSContract = array_unique($MSContract);
        $MSContract = array_values($MSContract);
        $newOrder['MSContract'] = implode('', $MSContract);

        $MSContract2 = array_unique($MSContract2);
        $MSContract2 = array_values($MSContract2);
        $newOrder['MSContract2'] = implode('', $MSContract2);

        $NetPoint = array_unique($NetPoint);
        $NetPoint = array_values($NetPoint);
        $newOrder['NetPoint'] = implode('', $NetPoint);
        $newOrder['MSRemark'] = data_get($order,'MSRemark');

        $newOrder['PrintBillYNAry'] = $PrintBillYNAry;
        $newOrder['MaintainCost'] = $maintainCost;
        $newOrder['ccadaf'] = $ccadaf;
        $newOrder['ach'] = $ach;
        $newOrder['worksheet2'] = $assignsheetKeys[0]; // 合併工單;不含主單號=>取清單第一個
        $newOrder['inpDevSin'] = $inpDevSin;
        $newOrder['deviceGetList'] = $deviceGetList;
        $newOrder['orgSingleSnList'] = implode(',',$orgSingleSnList);
        $newOrder['chkChargeNameAlert0701'] = $chkChargeNameAlert0701;
        $newOrder['chkAlertMSG20221215'] = $chkAlertMSG20221215;

        $newOrder['NodeNo'] = implode(',',array_unique(data_get($newOrder,'NodeNo')));
        $newOrder['InvUnifyNo'] = implode(',',array_unique(data_get($newOrder,'InvUnifyNo')));
        $newOrder['LinkID'] = implode(',',array_unique(data_get($newOrder,'LinkID')));

        return $newOrder;
    }

    // 取得完工檢核表
    public function getFinishCheckList()
    {
        $cacheKey = "getFinishCheckList";
        $sec = 60 * 60;

        if (Cache::store('redis')->has($cacheKey)) {
            $data = Cache::store('redis')->get($cacheKey);
            $ret = json_decode($data,1);
            return $ret;
        }

        $whereAry = array(
            'tableType' => 'chkList',
            'enable' => '1',
        );
        $finishChkList = $this->OrderRepository->getQAList($whereAry);
        $finishChkListAry = array();
        foreach($finishChkList as $k => $t) {
            $title = $t->title;
            $body = $t->bodydesc;
            $finishChkListAry[$title][] = $body;
        }

        $jsonData = json_encode($finishChkListAry);
        Cache::store('redis')->put($cacheKey, $jsonData, $sec);
        $ret = $finishChkListAry;

        return $ret;
    }


    public function uploadImage(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'image' => 'required',
                'fileName'=> 'required',
                'p_CompanyNo'=> 'required',
                'p_WorkSheet'=> 'required',
            ],
            [
                'id.required' => '請確認Id',
                'image.required' => '請上傳圖片',
                'fileName.required' => '請確認檔名',
                'p_CompanyNo.required'=> '請確認SO',
                'p_WorkSheet.required'=> '請確認工單',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0]);
            };

            $id = data_get($request,'id');
            $so = data_get($request,'p_CompanyNo');
            $worksheet = data_get($request,'p_WorkSheet');
            $fileName = data_get($request,'fileName');
            $custID = data_get($request,'p_CustID');
            $bookDate = data_get($request,'p_BookDate');
            $sessionKey = $so.'_'.$worksheet.'_';
            $p_columnName = data_get($request,'p_columnName');
            $p_user_code = data_get($request,'p_userCode');
            $p_fname = data_get($request,'p_fname');
            $blob_num = data_get($request,'blob_num');
            $total_blob_num = data_get($request,'total_blob_num');
            $file_name = data_get($request,'fileName');
            $folder = 'EWO_Folder';
            $p_code = '0000';
            $p_status = 'ok';
            $p_data = array();

            switch ($p_columnName) {
                case 'lsImg':
                    // 勞安照片
                    $file_name = 'lsImg_'.date('Ymd').'_'.$p_user_code.'.jpg';
                    break;
                case 'coImg':
                    // 出班檢查，照片
                    $file_name = 'coImg_'.date('Ymd').'_'.$p_fname.'_'.$p_user_code.'.jpg';
                    break;
                case 'checkin':
                    // 打卡
                    $file_name = "checkin_$worksheet.jpg";
                    break;
            }

            // img，存入 folder
            Storage::putFileAs($folder, $request->file('image'), $file_name .'_'.$custID. '_' . $blob_num);

            // 合併img
            if ($blob_num == $total_blob_num) {
                // 合併，img
                $content = '';
                for ($i = 1; $i <= $total_blob_num; $i++) {
                    $content .= Storage::get($folder.'/' . $file_name .'_'.$custID. '_' . $i);
                }
                Storage::put("$folder/$file_name", $content);
                for ($i = 1; $i <= $total_blob_num; $i++) {
                    // 刪除，img
                    Storage::delete($folder.'/' . $file_name .'_'.$custID. '_' . $i);
                }

                $directoryNew =config('filesystems.disks.upload.root').'/'.$custID.'_'.date('Ymd',strtotime($bookDate));

                if($p_columnName === 'lsImg') {
                    // 勞安設備照片
                    $directoryNew = config('filesystems.disks.upload.lsImg').'/'.date('Ym').'/'.date('Ymd');
                } else if($p_columnName === 'coImg') {
                    // 出班檢查
                    $directoryNew = config('filesystems.disks.upload.coImg').'/'.date('Ym').'/'.date('Ymd');
                }

                // 建立目錄
                if (is_dir($directoryNew) === false) {
                    mkdir($directoryNew,0777,true);
                    chmod($directoryNew,0777);
                }

                // 移動檔案
                if($p_columnName !== "id03Photo")
                rename(config('filesystems.disks.upload.storageEWOFile')."/$file_name", $directoryNew.'/'.$file_name);

                // 更新，wm_orderlist
                $params = array();
                $params['p_columnName'] = $p_columnName;
                $params['p_value'] = date('Y-m-d H:i:s');
                $params['p_id'] = $id;
                if($params['p_columnName'] === "checkin" ) {
                    // 打卡
                    $p_lat = data_get($request,'lat');
                    $p_lng = data_get($request,'lng');
//$p_lat = '22.9860548';
//$p_lng = '120.2144898';

                    $params['p_value_gps'] = "lat:$p_lat,lng:$p_lng";
                    $curlobj = New ConsumablesAPIController();

                    // 用戶[GPS]
//                    $url = '/api/EWO/getAddresToGPS';
//                    $protocol = 'https://';
//                    $url = $protocol.$_SERVER['HTTP_HOST'].$url;
//                    $post_data = array(
//                        'companyNo' => $so,
//                        'custId' => $custID,
//                    );
//                    $data = array(
//                        'url'       => $url,
//                        'method'    => 'post',
//                        'header'    => 'json',
//                        'post_data' => $post_data,
//                    );
//                    $qryCustGPS = $curlobj->curl($data);
//                    $qryCustGPS = json_decode($qryCustGPS,1);
//                    $custGPSData = data_get($qryCustGPS,'data');
//                    $custAddress = data_get($custGPSData,'address');
                    $custGps = data_get($request,'custGps');

                    $checkInData = [];
                    $checkInData['custGps'] = $custGps;
//                    $checkInData['custAddres'] = $custAddress;
                    $checkInData['checkInGps'] = "$p_lat,$p_lng";
                    $checkInData['checkInAddres'] = 'NoUserGPS';
                    $checkInData['gpsDistance'] = 'NoUserGPS';

                    // 工程[打卡地址]
                    $url = '/api/EWO/getAddresToGPS';
                    if($p_lat !== '111' && $p_lng !== '222') {
                        $post_data = array(
                            'lat' => $p_lat,
                            'lng' => $p_lng,
                        );
                        $data = array(
                            'url'       => $url,
                            'method'    => 'post',
                            'header'    => 'json',
                            'post_data' => $post_data,
                        );
                        $qryCheckInAddres = $curlobj->curl($data);
                        $qryCheckInAddres = json_decode($qryCheckInAddres,1);
                        $checkInAddres = data_get($qryCheckInAddres,'data');
                        $checkInData['checkInAddres'] = $checkInAddres;

                        $custGPSAry = explode(',',$custGps);
                        if(sizeof($custGPSAry) === 2) {
                            // 領點距離[GPS]
                            $url = '/api/EWO/getDistanceGPS';
                            $protocol = 'https://';
                            $url = $protocol.$_SERVER['HTTP_HOST'].$url;
                            $post_data = array(
                                'lat1' => $p_lat,
                                'lng1' => $p_lng,
                                'lat2' => $custGPSAry[0],
                                'lng2' => $custGPSAry[1],
                            );
                            $data = array(
                                'url'       => $url,
                                'method'    => 'post',
                                'header'    => 'json',
                                'post_data' => $post_data,
                            );
                            $qryDistanceGPS = $curlobj->curl($data);
                            $qryDistanceGPS = json_decode($qryDistanceGPS,1);
                            $distanceGPS = data_get($qryDistanceGPS,'data');
                            $checkInData['gpsDistance'] = $distanceGPS;
                        }
                    }
                    $p_data['checkInData'] = $checkInData;
                    $params['checkInData'] = $checkInData;
                    // 改名
                    $fileName = $file_name;

                } elseif($params['p_columnName'] === "constructionPhoto") {
                    // 施工照片
                    $p_names = data_get($request,'names');
                    $p_fileName = $file_name;
                    if(strlen($p_names) < 1) {
                        $p_names = array($p_fileName);
                    } elseif(is_string($p_names) === true) {
                        $p_names = json_decode($p_names,1);
                        if(is_array($p_names) === false) {
                            $p_names = explode(',', data_get($request,'names'));
                        }
                    }
                    if(in_array($p_fileName, $p_names) === false) {
                        array_push($p_names, $p_fileName);
                    }
                    sort($p_names);
                    $params['p_value'] = json_encode($p_names);

                } elseif($params['p_columnName'] === "id03Photo") {
                    // 第二證件
                    $p_names = data_get($request,'names');

                    $p_fileName = $file_name;
                    $chk_count = 'Y';
                    if(strlen($p_names) < 1) {
                        $p_namesAry = array($p_fileName);

                    } elseif(is_string($p_names) === true) {
                        $p_namesAry = json_decode($p_names,1);
                        if(!is_array($p_namesAry)) {
                            $p_namesAry = explode(',', $p_names);
                        }

                    }

                    if(in_array($p_fileName, $p_namesAry) === false) {
                        array_push($p_namesAry, $p_fileName);
                        $chk_count = 'N';
                    }
                    sort($p_namesAry);
                    $params['p_value'] = json_encode($p_namesAry);

                    // 移動照片順序
                    if(count($p_namesAry) >= 3 && $chk_count === 'Y') {
                        rename($directoryNew.'/'.$p_namesAry[1], $directoryNew.'/'.$p_namesAry[2]);
                        rename($directoryNew.'/'.$p_namesAry[0], $directoryNew.'/'.$p_namesAry[1]);
                        rename(config('filesystems.disks.upload.storageEWOFile')."/$file_name", $directoryNew.'/'.$file_name);
                    } else {
                        rename(config('filesystems.disks.upload.storageEWOFile')."/$file_name", $directoryNew.'/'.$file_name);
                    }

                }

                $params['p_companyNo'] = data_get($request,'companyNo');
                $params['p_workSheet'] = data_get($request,'workSheet');
                $params['p_userCode'] = $p_user_code;
                $params['p_userName'] = data_get($request,'p_userName');
                $params['p_sign_chs'] = data_get($request,'p_sign_chs');
                $params['p_sign_bftth'] = data_get($request,'p_sign_bftth');

                $objEvent = new Ewo_EventController();

                if($p_columnName === 'lsImg') {
                    // 勞安設備照片
                    $params['EventType'] = 'laborsafetyImg';
                    $params['Responses'] = '勞安設備照片;上傳成功';
                    $data = $objEvent->insertLog($params);

                    // 新增[勞安]紀錄
                    $userInfo = wm_usermang::select('CompanyNo')
                        ->where('Account', $p_user_code)
                        ->first();
                    $userInfo_companyNoStr = data_get($userInfo, 'CompanyNo');
                    $userInfo_companyNoAry = explode(
                        ',', $userInfo_companyNoStr
                    );
                    $userInfo_companyNo = data_get($userInfo_companyNoAry, 1);
                    $lsLogObj = new LogRepository();
                    $lsLogAry = array(
                        'CompanyNo' => $userInfo_companyNo,
                        'UserCode' => $p_user_code,
                        'UserName' => $params['p_userName'],
                        'Desc1' => date('Y-m-d'),
                        'Type' => 'C.勞安設備',
                    );
                    $lsLogObj->laborsafetyLogDel($lsLogAry);
                    $lsLogObj->laborsafetyLogAdd($lsLogAry);

                } elseif($p_columnName === 'coImg') {
                    // 出班檢查
                    $fnameAry = config('order.checkOutImg');
                    $params['EventType'] = 'checkOutImg';
                    $params['Responses'] = '出班檢查，照片['.$fnameAry[$p_fname].'];上傳成功';
                    $data = $objEvent->insertLog($params);

                } else {
                    $data = $objEvent->reqUpdataTime($params);

                }

                $file_url = '/upload/'.$custID."_".date("Ymd",strtotime($bookDate)).'/'.$fileName;
                $p_data['file_url'] = $file_url;
                $p_data['src'] = $file_url.'?i='.date("His");

                // 檢查簽名檔案大小
                if(in_array($p_columnName,['sign_mcust','sign_mengineer'])) {
                    $file_size = filesize(public_path($file_url));
                    $p_data['file_size'] = $file_size;
                    // 小於1.5kb存log
                    if($file_size < 25000) {
                        $p_code = '0101';
                        $params['EventType'] = 'imgSizeAbnormal';
                        $params['Responses'] = "size=$file_size; url=$file_url;";
                        $params['CompanyNo'] = $so;
                        $params['WorkSheet'] = $worksheet;
                        $params['CustID'] = $custID;
                        $data = $objEvent->insertLog($params);
                    }
                }

                if(in_array($p_columnName, array('constructionPhoto','id03Photo'))) {
                    // 施工照片 OR 第二證件
                    $p_data['names'] = $data->original['data'];
                    $p_data['img'] = $file_url.'?i='.date("His");
                    $p_data['orc'] = '';

                } else if(in_array($p_columnName, array('lsImg'))) {
                    // 勞安設備照片
                    $p_data['src'] = '/ewo_lsImg/'.date('Ym').'/'.date('Ymd').'/'.$file_name.'?'.date('is');

                } else if(in_array($p_columnName, array('coImg'))) {
                    // 出班檢查
                    $p_data['src'] = '/ewo_coImg/'.date('Ym').'/'.date('Ymd').'/'.$file_name.'?'.date('is');

                } else if(in_array($p_columnName, array('id01','id02'))) {
                    // 身分證[正/反]
                    $obj = new ConsumablesAPIController();
                    $url = config('order.EWO_226_URL').'/api/EWO/getImgScanStr';

                    $postData = array(
                        'p_id' => $p_columnName,
                        'companyno' => $so,
                        'custid' => $custID,
                        'worksheet' => $worksheet,
                        'bookdate' => $bookDate
                    );
                    $urlData = array(
                        'url'       => $url,
                        'method'    => 'post',
                        'header'    => 'json',
                        'post_data' => json_encode($postData),
                    );

                    $orc = $obj->curl($urlData);
                    $orc = json_decode($orc,1);
                    $orc_data = data_get($orc,'data');
                    $orc_code = data_get($orc,'code');
                    if($orc_code === '0000') {
                        $orc = data_get($orc_data,'src');
                    } else {
                        $orc = $orc_data;
                    }
                    $p_data['orc'] = $orc;

                }

                $p_meg = '上傳完成';

            } else {
                $p_meg = "上傳部件:$blob_num/$total_blob_num";
                $p_data = 'uploading';
            }


        } catch (Exception $e) {
            Log::channel('ewoLog')->info('func uploadImage() error=='.print_r($e->getMessage(),true));
            $p_code = '0400';
            $p_status = 'error';
            $p_meg = '資料錯誤';
            $p_data = $e->getMessage();
        }

        $p_json = array(
            'code' => $p_code,
            'status' => $p_status,
            'meg' => $p_meg,
            'data' => $p_data,
            'date' => date('Y-m-d H:i:s')
        );

        return Response()->json($p_json);


    }


    public function getChargeRecvAmt($chargeCM,$chargeDSTB,$chargeTWMBB)
    {
        $amt = 0;

        foreach ($chargeCM as $item) {
            $amount = data_get($item,'RecvAmt');
            $amt+=(int)$amount;
        }


        foreach ($chargeDSTB as $item) {
            $amount = data_get($item,'RecvAmt');
            $amt+=(int)$amount;
        }

        foreach ($chargeTWMBB as $item) {
            $amount = data_get($item,'RecvAmt');
            $amt+=(int)$amount;
        }

        return $amt;

    }


    private function classifyCharge($orderCharge)
    {
        $data = array();
        $worksheet = array();
        $finshSheet = array();

        $TotalAmt = 0;
        $userId = Session::get('userId');
        foreach ($orderCharge as $charge) {
            $serviceName = data_get($charge,'ServiceName');
            $amt = (int)data_get($charge,'BillAmt');
            $sheetStatus = data_get($charge,'SheetStatus');
            $assignsheetVal = data_get($charge,'AssignSHeet');
            $worksheetVal = data_get($charge,'WorkSheet');

            if($assignsheetVal == $worksheetVal) {
                if(substr($sheetStatus,0,1) < 4)
                    array_unshift($finshSheet,$assignsheetVal);
            } else {
                if(substr($sheetStatus,0,1) < 4)
                    $finshSheet[] = $worksheetVal;
            }

            $workers = data_get($charge,'Worker1');
            $workers = trim($workers);
            // $worker = explode(' ', $workers);
            $WorkerNum = '';

            $strIndex = strpos($workers,$userId);

            if ($strIndex !== false) {
                $WorkerNum = substr($workers, 0,strlen($userId));
                $WorkerName = substr($workers, strlen($userId));

                $WorkerNum = trim($WorkerNum);
                $WorkerName = trim($WorkerName);
                // $WorkerName = $worker[1] ?? $workers;
                // $WorkerNum = $worker[0] ?? $workers;
            }

            if ($WorkerNum != $userId) {
                continue;
            }

            $amtStatus = array('0.預約','1.分派','1.控制','1.開通','2.改約','1.報竣','4 結案');


            if (in_array($sheetStatus,$amtStatus) >= 1) {
                data_set($charge, 'BillAmt', $amt);
                $TotalAmt+=$amt;
            }

            $checklStatus = array('3.退單','A.取消','2.回報');

            if (in_array($sheetStatus,$checklStatus) >= 1) {
                continue;
            }

            $worksheet[] =  data_get($charge,'WorkSheet');

            $data[$serviceName][] = $charge;

        }

        $worksheet = array_unique($worksheet);
        $worksheet = array_values($worksheet);

        // 主單已經完工，促變單沒完工，API送未完工的單號
//        if(isset($finshSheet['worksheet']) || )
//        $finshSheet['worksheet'] = array_unique($finshSheet['worksheet']);
//        $finshSheet['worksheet'] = array_values($finshSheet['worksheet']);
//        if(isset($finshSheet['assignsheet']) === false)
//            $finshSheet['assignsheet'] = array();
//
//        if(count($finshSheet['assignsheet']) < 1) {
//            $finshSheet = $finshSheet['worksheet'][0];
//        } else {
//            $finshSheet = $finshSheet['assignsheet'][0];
//        }

        return array($data, $TotalAmt, $worksheet, $finshSheet);
    }

    //合計 ms0301.billamt+ms3200.recvamt 金額
    public function sumBillamtRecvAMT($companyNo,$ms0301WorkSheetAryBillAmt,$ms3200WorkSheetAryRecvAmt) {

        if($ms0301WorkSheetAryBillAmt) {
            $ms0301WorkSheetAryBillAmtAry = explode(',',$ms0301WorkSheetAryBillAmt);
            $ms0301WorkSheetAryBillAmtSUM = $this->OrderRepository->getMs0301BillamtSum(['so'=> $companyNo,'inworksheet'=>$ms0301WorkSheetAryBillAmtAry]);
            $ms0301WorkSheetAryBillAmtSUM = intval($ms0301WorkSheetAryBillAmtSUM[0]->BillAmtSum);
        } else
            $ms0301WorkSheetAryBillAmtSUM = 0;

        if($ms3200WorkSheetAryRecvAmt) {
            $ms3200WorkSheetAryRecvAmtAry = explode(',',$ms3200WorkSheetAryRecvAmt);
            $ms3200WorkSheetAryRecvAmtArySUM = $this->OrderRepository->getMS3200RecvAmttSum(['so'=> $companyNo,'inworksheet'=>$ms3200WorkSheetAryRecvAmtAry]);
            $ms3200WorkSheetAryRecvAmtArySUM = intval($ms3200WorkSheetAryRecvAmtArySUM[0]->RecvAmtSum);
        } else
            $ms3200WorkSheetAryRecvAmtArySUM = 0;

        return $ms3200WorkSheetAryRecvAmtArySUM + $ms0301WorkSheetAryBillAmtSUM;
    }

    public function checkDeviceCount($devices)
    {
        $newDevices  = array();
        $i_cnt = 0;
        $d_dublecnt = 0;
        $d_singlecnt = 0;
        $pvr_cnt = 0;
        $cmbaudrate = '';

        foreach ($devices as $key => $device) {
            $CMBAUDRATE = data_get($device,'CMBAUDRATE');
            $I_CNT = data_get($device,'I_CNT');
            $D_DUBLECNT = data_get($device,'D_DUBLECNT');
            $D_SINGLECNT = data_get($device,'D_SINGLECNT');
            $PVR_CNT = data_get($device,'PVR_CNT');

            $i_cnt += $I_CNT;
            $d_dublecnt += $D_DUBLECNT;
            $d_singlecnt += $D_SINGLECNT;
            $pvr_cnt += $PVR_CNT;

            if (empty($cmbaudrate)) {
                $cmbaudrate = $CMBAUDRATE;
            }

        }

        $newDevices['I_CNT'] = $i_cnt;
        $newDevices['D_DUBLECNT'] = $d_dublecnt;
        $newDevices['D_SINGLECNT'] = $d_singlecnt;
        $newDevices['PVR_CNT'] = $pvr_cnt;
        $newDevices['CMBAUDRATE'] = $cmbaudrate;

        return $newDevices;
    }

    public function checkMaintainVersion($devices)
    {
        $newDevices  = array();

        foreach ($devices as $key => $device) {
            $ServiceName = data_get($device,'ServiceName');
            $FACISNO = data_get($device,'FACISNO');
            $MODELNAME = data_get($device,'MODELNAME');
            $NOTGET = data_get($device,'NOTGET');


            if(isset($newDevices[$ServiceName]['FACISNO'])) {
                $newDevices[$ServiceName]['FACISNO'] .= ' '.$FACISNO;
            } else {
                $newDevices[$ServiceName]['FACISNO'] = $FACISNO;
            }
            if(isset($newDevices[$ServiceName]['MODELNAME'])) {
                $newDevices[$ServiceName]['MODELNAME'] .= ' '.$MODELNAME;
            } else {
                $newDevices[$ServiceName]['MODELNAME'] = $MODELNAME;
            }
            if ($NOTGET) {
                $newDevices[$ServiceName]['FACISNO'] .= '(拆未回)';
            }
        }
        return $newDevices;
    }


    // 區故
    public function getFault($params=array())
    {
        $client = new Client();
        $url = config('order.EWO_URL').'/api/EWO/getFault';
        $paramsAry = $params;
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
