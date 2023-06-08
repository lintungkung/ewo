<?php

namespace App\Api\Controllers;

use App\Repositories\Log\LogRepository;
use Validator;
use \Log;
use Exception;
use domPDF;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Api\Controllers\Controller;
use App\Http\Controllers\MyException;

use \App\Http\Controllers\Ewo_EventController;
use App\Repositories\Consumables\ConsumablesRepository;
use App\Repositories\Order\OrderRepository;
use GuzzleHttp\Client;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PDF_v3fet extends Controller
{

    public function __construct(OrderRepository $OrderRepository)
    {
        $this->OrderRepository = $OrderRepository;
    }

    public function createPDF($source,$version,$orderId,$params='')
    {
        set_time_limit(300);
        $p_time_start = microtime(true);
        $p_run_title = "PDF_$version"."_ID_$orderId";
        $run = array(); // 執行時間資訊

        // 判斷設備取回單、借用單
        $chkEquipment = data_get($params,'equipment');

        // 判斷Command
        $chkCmd = data_get($params,'cmd');

        $data = array();
        $data['chkEquipment'] = $chkEquipment;
        $data['chkCmd'] = $chkCmd;
        $ewo_url = config('order.EWO_URL');
        $companyNo = $workSheet = '';
        $p_orderId = $orderId;


        if(substr($p_orderId,0,4) === 'NULL') {
            $valAry = explode('-',$orderId);
            $companyNo = data_get($valAry,'1');
            $workSheet = data_get($valAry,'2');
            $orderId = '';
        }

        try {
            $qryData = array(
                'id' => $orderId,
                'so' => $companyNo,
                'worksheet' => $workSheet,
            );
            $order_info = $this->OrderRepository->getOrderInfo($qryData);
            $workKind = data_get($order_info,'WorkKind');
            $companyNo = empty($companyNo)? data_get($order_info,'CompanyNo') : $companyNo;
            $workSheet = empty($workSheet)? data_get($order_info,'WorkSheet') : $workSheet;
            $orderId = empty($orderId)? data_get($order_info,'Id') : $orderId;

            // 建立wm_orderlist資料
            if (empty($orderId) || empty($workKind)) {

                // MS0301，Info
                $qryData = array(
                    'so' => $companyNo,
                    'worksheet' => $workSheet,
                );
                $selAry = true;
                $orderBy = array(['name' =>'worker1', 'type' =>'desc']);
                $ms0301ByAssignSheet = $this->OrderRepository->getOrderCharge($qryData,$selAry,$orderBy);

    //            $qryData = array(
    //                'so' => $companyNo,
    //                'worksheet' => $workSheet,
    //            );
    //            $selAry = array('worker1','custid','bookdate');
    //            $orderBy = array(['name' =>'worker1', 'type' =>'desc']);
    //            $ms0301ByAssignSheet = $this->OrderRepository->getOrderCharge($qryData,$selAry,$orderBy);

                $ms0301ByAssignSheet = $ms0301ByAssignSheet[0];
                $custId = data_get($ms0301ByAssignSheet,'CustID');
                $bookDate = data_get($ms0301ByAssignSheet,'BookDate');
                $worker1 = data_get($ms0301ByAssignSheet,'Worker1');
                $worker1Ary = explode(' ',$worker1);
                $workerNum = data_get($worker1Ary,0);
                $workerName = data_get($worker1Ary,1);
                $qryData = [
                    'WorkerNum' => $workerNum,
                    'WorkerName' => $workerName,
                    'so' => $companyNo,
                    'worksheet' => $workSheet,
                    'custid' => $custId,
                    'bookdate' => $bookDate,
                    'pdf_v' => config('order.PDF_CODE_FET_V'),
                    'pdfTerms' => config('order.PDF_TERMS_V'),
                ];
                if(empty($orderId))
                    $orderId = $this->OrderRepository->addOrderlist($qryData);

                // 模擬登入，更新資料
                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
                $url = $protocol.$_SERVER['HTTP_HOST']."/ewo/order_info/$companyNo-$workSheet/dd?printpdf=Y&userid=$workerNum";
                $obj = New ConsumablesAPIController();
                $apiData = array(
                    'url' => $url,
                    'method' => 'GET',
                    'header'    => '',
                    'post_data' => array(),
                );
                $apiChk = $obj->curl($apiData);
                if($apiChk == 'OK') {
                    $order_info = $this->OrderRepository->getOrderInfoById($orderId);
                } else {
                    exit("生成失敗[code:0570]，so:$companyNo,住編:$custId;");
                    //throw new Exception('生成失敗'.print_r($apiChk,1),'0570');
                }
            }

            $order_info = (array)$order_info;

            // logo img url
            $companyNo = data_get($order_info,'CompanyNo');
            $logo_url = $ewo_url."/img/logo$companyNo.png?".date('Ymd');

            $finish = data_get($order_info,'finsh');
            $zj_finsh = data_get($order_info, "zj_finsh");
            $assignSheet = data_get($order_info,'AssignSheet');
            $serviceName = data_get($order_info,'ServiceName');
            $serviceNameAry = json_decode($serviceName);
            $TeleNum01_200 = data_get($order_info,'TeleNum01_200');
            $custId = data_get($order_info,'CustID');
            $SubsID = data_get($order_info,'SubsID');
            $bookDate = data_get($order_info,'BookDate');
            $WorkKind = data_get($order_info,'WorkKind');
            $CustName = data_get($order_info,'CustName');
            $CustName = mb_substr($CustName,0,1).'*'.mb_substr($CustName,-1);
            data_set($order_info,'CustName',$CustName);
            $NetID = data_get($order_info,'NetID');
            $SaleCampaign = data_get($order_info,'SaleCampaign');
            $CreateName = data_get($order_info,'CreateName');
            $CreateTime = data_get($order_info,'CreateTime');
            $WorkerName = data_get($order_info,'WorkerName');
            $WorkTeam = data_get($order_info,'WorkTeam');
            $assignSheet = data_get($order_info,'AssignSheet');
            $create_at = data_get($order_info,'create_at');
            $MSComment1 = data_get($order_info,'MSComment1');
            $deviceCount = data_get($order_info,'deviceCount');
            $deviceSWVersion = data_get($order_info,'deviceSWVersion');
            $maintainHistory = data_get($order_info,'maintainHistory');
            $InstAddrName = data_get($order_info,'InstAddrName');
//            $InstAddrName = mb_substr($InstAddrName,0,3).'***'.mb_substr($InstAddrName,-3);
            if(strpos($InstAddrName,'街'))
                $InstAddrName = mb_substr($InstAddrName,0,mb_strpos($InstAddrName,'街')+1).'***';
            elseif(strpos($InstAddrName,'路'))
                $InstAddrName = mb_substr($InstAddrName,0,mb_strpos($InstAddrName,'路')+1).'***';
            elseif(strpos($InstAddrName,'道'))
                $InstAddrName = mb_substr($InstAddrName,0,mb_strpos($InstAddrName,'道')+1).'***';
            elseif(strpos($InstAddrName,'巷'))
                $InstAddrName = mb_substr($InstAddrName,0,mb_strpos($InstAddrName,'巷')+1).'***';
            data_set($order_info,'InstAddrName',$InstAddrName);
            $WorkCause = data_get($order_info,'WorkCause');
            $MSContract = data_get($order_info,'MSContract');
            $MSContract2 = data_get($order_info,'MSContract2');
            $saleAP = data_get($order_info,'saleAP');
            $checkDSTB = data_get($order_info,'dstbcheck');
            $checkCM = data_get($order_info,'cmcheck');
            $checkTWMBB = data_get($order_info,'twmbbcheck');
            $checkTWMBBAry = json_decode($checkTWMBB,true);
            $borrowmingList = data_get($order_info,'BorrowmingList');
            $retrieveList = data_get($order_info,'RetrieveList');
            $custBroker = data_get($order_info,'CustBroker');
            $custBrokerAry = explode(' ',$custBroker);
            data_set($order_info,'custBrokerAry',$custBrokerAry);
            $so = data_get($order_info,'CompanyNo');
            $workSheet = data_get($order_info,'WorkSheet');
            $finish = data_get($order_info,'finsh');
            $zj_finsh = data_get($order_info, "zj_finsh");
            $custName = data_get($order_info, "CustName");
            $sign_mcust = data_get($order_info, "sign_mcust");
            $sign_mengineer = data_get($order_info, "sign_mengineer");
            $assignSheet = data_get($order_info,'AssignSheet');
            $assignSheet = explode(",", $assignSheet);
            $assignSheet[] = $workSheet;
            $assignSheet = array_filter($assignSheet);
            $assignSheet = array_values($assignSheet);
            $finsh = data_get($order_info,'finsh');
            $pdfTerms = data_get($order_info,'pdfTerms');
            if(strlen($pdfTerms) != 8) $pdfTerms = config('order.PDF_TERMS_V');
            $pdf_v = data_get($order_info,'pdf_v');
            $pdf_v = 'v3fet';
            $brokerKind = data_get($order_info,'BrokerKind');
            $workKind = data_get($order_info,'WorkKind');
            $worker1 = data_get($order_info,'WorkerNum').' '.data_get($order_info,'WorkerName');
            $worker1 = trim($worker1); // 移除前後多的空白
            $bookDateStr = date('Ymd',strtotime($bookDate));

            // 執行command
            if($chkCmd == 'Y') {
                $directory = public_path("upload/$custId"."_$bookDateStr");
                if (!is_dir($directory)) {
                    // 建立目錄
                    mkdir($directory,0777,true);
                    chmod($directory,0777);
                }

                $time = time();
                // 建立設備借用單
                $command2 = "/usr/local/bin/wkhtmltopdf $ewo_url/api/createpdf/web/$pdf_v/$orderId?equipment=Y#$time";
                $command2 .= " $directory/$workSheet".'_equipment.pdf';
                $createPDFTable = shell_exec($command2);
                if(!empty($createPDFTable)) {
                    exit("PDF生成(設備借用單、取回單)失敗[code:5248]，so:$companyNo,住編:$custId;");
                    //throw new Exception('PDF生成(設備借用單、取回單)失敗','5248');
                }

                $chgCompanyo = 'v3fet';
                $command  = '/usr/local/bin/wkhtmltopdf --header-html '.$ewo_url."/ewo/pdfV3Header/$chgCompanyo ";
                $command .= ' --header-spacing 5 "'.$ewo_url.'/api/createpdf/web/'.$pdf_v.'/'.$orderId.'"';
                $command .= " $directory/$workSheet".'_head.pdf';

                // 建立Table
                $createPDFTable = shell_exec($command);
                if(empty($createPDFTable)) {
                    $p_time_end = microtime(true);
                    $p_run_time = $p_time_end - $p_time_start;
                    $run = array(
                        'title' => $p_run_title,
                        'm_start' => $p_time_start,
                        'm_end' => $p_time_end,
                        'run_time' => $p_run_time,
                        'command' => $command,
                        'command2' => $command2,
                    );
                    // 合併檔案
                    $fileMerge = self::mergePDF($companyNo,$workSheet,$serviceNameAry,$directory,$pdfTerms);
                    if($fileMerge) {
                        $fileName = "$workSheet.pdf";
                        $ret = array(
                            'code' => '0000',
                            'status' => 'OK',
                            'meg' => '',
                            'data' => "$directory/$fileName",
                            'run' => json_encode($run),
                            'date' => date('Y-m-d H:i:s')
                        );

                        return $ret;
                    } else {
                        exit("PDF合併失敗[code:5225]，so:$companyNo,住編:$custId;");
                        //throw new Exception('PDF合併失敗','5225');
                    }
                }
                else {
                    exit("PDF生成(Table)失敗[code:5234]，so:$companyNo,住編:$custId;");
                    //throw new Exception('PDF生成(Table)失敗','5234');
                }
            }

            //家電及手機號碼
            $TeleCod02_200 = data_get($order_info,'TeleCod02_200');
            $TeleNum02_200 = data_get($order_info,'TeleNum02_200');
            $TeleNum01_200 = data_get($order_info,'TeleNum01_200');
            $TeleCod01_200 = data_get($order_info,'TeleCod01_200');
            if (strlen($TeleCod02_200) > 9) {
                $hometel =  $TeleCod02_200.$TeleNum02_200;
                $phonetel = $TeleNum01_200;
            } else {
                $hometel = $TeleCod01_200.$TeleNum01_200;
                $phonetel = $TeleNum02_200;
            }
            $hometel = substr($hometel,0,2).'***'.substr($hometel,-3);
            $phonetel = substr($phonetel,0,2).'***'.substr($phonetel,-3);
            data_set($order_info,'hometel',$hometel);
            data_set($order_info,'phonetel',$phonetel);

            // 用戶簽名
            $data['sign_mcust_url'] = '';
            if(!empty($sign_mcust)) {
                $sign_mcust_url = "/upload/$custId"."_$bookDateStr/sign_mcust_$workSheet.jpg?".time();
                $data['sign_mcust_url'] = $sign_mcust_url;
            }

            // 用戶簽名選擇對象
            $data['sign_mcust_select'] = isset($checkTWMBBAry['sign_mcust_select'])? $checkTWMBBAry['sign_mcust_select'] : '';

            // 工程簽名
            $data['sign_mengineer_url'] = '';//
            if(!empty($sign_mengineer)) {
                $sign_mengineer_url = "/upload/$custId"."_$bookDateStr/sign_mengineer_$workSheet.jpg?".time();
                $data['sign_mengineer_url'] = $sign_mengineer_url;
            }

            // 設備取回單、借用單
            if($chkEquipment == 'Y') {
                $data['info'] = $order_info;

                $borrowmingListJSON = json_decode($borrowmingList,1);
                $retrieveListJSON = json_decode($retrieveList,1);
                $data['BorrowmingListSel'] = data_get($borrowmingListJSON,'select');
                $data['RetrieveListSel'] = data_get($retrieveListJSON,'select');

    //            $data['BorrowmingListDevJson'] = json_encode($data['BorrowmingListDev']);
    //            $data['RetrieveListDevJson'] = json_encode($data['RetrieveListDev']);
                $data['BorrowmingListSelJson'] = json_encode($data['BorrowmingListSel']);
                $data['RetrieveListSelJson'] = json_encode($data['RetrieveListSel']);

                // 設備清單
                $qryData = array(
                    'enable' => '1',
                );

                $query = $this->OrderRepository->get_wm_equipment($qryData);

                $equipmentList = array();
                foreach($query as $k => $t) {
                    $vType = $t->type;
                    $equipmentList[$vType][] = (array)$t;
                }
                $data['equipment'] = $equipmentList;

                // 更新設備序號至借用單、取回單
                $equipmentList = $this->setDeviceToEquipmentList($companyNo,$workSheet,$workKind);
                $data['BorrowmingListDev'] = data_get($equipmentList,'borrowmingList');
                $data['RetrieveListDev'] = data_get($equipmentList,'retrieveList');
                $data['BorrowmingListDevCount'] = data_get($equipmentList,'borrowmingListCunt');
                $data['RetrieveListDevCount'] = data_get($equipmentList,'retrieveListCunt');
                $data['BorrowmingSelListCount'] = data_get($equipmentList,'borrowmingSelListCunt');
                $data['RetrieveSelListCount'] = data_get($equipmentList,'retrieveSelListCunt');
                $data['BorrowmingSelList'] = data_get($equipmentList,'borrowmingSelList');
                $data['RetrieveSelList'] = data_get($equipmentList,'retrieveSelList');
                return view('pdf.table_v3_device',compact('data'));
            }

            $bill = array();
            $custId02 = ''; // 同一合併工單，出現兩個住編
            if(is_Null($finsh) === true) {
                // 未完工，MS3200
                $chargeData = array(
                    'so' => $so,
                    'worksheetin' => $assignSheet,
                    'recvyn' => '',
                );

                $orderBill = $this->OrderRepository->getOrderBill($chargeData);

                foreach ($orderBill as $key => $itemBill) {
                    data_set($itemBill, 'DB', '3200');
                    $bill[] = $itemBill;
                }
                // 未完工，MS0301
                $chargeData = array(
                    'so' => $so,
                    'worksheet' => $workSheet,
                    //'worker1' => "$worker1",
                    'statusNotIn' => ['A.取消']
                );
                $chkMS0301List = '';

                $ms0301List = $this->OrderRepository->getOrderCharge($chargeData);
                foreach ($ms0301List as $key => $itemBill) {
                    $SheetStatus = data_get($itemBill,'SheetStatus');
                    $vCustId = data_get($itemBill,'CustID');
                    if(empty($custId02) && $vCustId != $custId) $custId02 = $vCustId;
                    if ($SheetStatus == 'A.取消') continue;
                    $bill[] = $itemBill;
                    $chkMS0301List = 'Y';
                }

                if(empty($chkMS0301List)) {
                    exit("震江[0301]資料異常，找不到工單[code:5326]，so:$companyNo,住編:$custId;");
                    //throw new Exception('資料錯誤[MS0301]，找不到工單', '5326');
                }

            } else {
                // 完工，MS3200
                $ms3200Data = data_get($order_info,'finshMS3200');
                $ms3200List = json_decode($ms3200Data,1);
                foreach ($ms3200List as $k => $t) {
                    data_set($t, 'DB', '3200');
                    $chkRecvYN = data_get($t,'RecvYN');
                    if($chkRecvYN == 'Y') continue;
                    $bill[] = $t;
                }
                // 完工，MS0301
                $ms0301Data = data_get($order_info,'orderInfoList');
                $ms0301List = json_decode($ms0301Data, true);
                foreach ($ms0301List as $k => $t) {
                    $vSheetStatus = data_get($t,'SheetStatus');
                    $vCustId = data_get($t,'CustID');
                    if(empty($custId02) && $vCustId != $custId) $custId02 = $vCustId;
                    if($vSheetStatus == 'A.取消') continue;
                    $bill[] = $t;
                }
            }

            $charges = $this->classifyCharge($bill,$workKind);
            $data['charges'] = $charges;
            $serviceName = array_keys($charges);
            $data['serviceNameAry'] = $serviceName;

            // 收費清單
            if(TRUE)
            {
                $page_data = array();
                // 收費項目
                $chargeNameAry = array();
                foreach ($charges as $key => $charge) {
                    $serviceNameType = self::serviceNameType($key);
                    foreach ($charge as $value) {
                        $chargeName = data_get($value,'ChargeName');
                        $chargeNameAry[$serviceNameType][] = $chargeName;
                    }

                }
                $page_data['chargeNameAry'] = $chargeNameAry;

                // 收費期間
                $chargeDateAry = array();
                foreach ($charges as $key => $charge) {
                    $serviceNameType = self::serviceNameType($key);
                    foreach ($charge as $value) {
                        $fromDate = data_get($value,'FromDate');
                        $tillDate = data_get($value,'TillDate');
                        if (!empty($fromDate) && !empty($tillDate)) {
                            $chargeDate = date("Y-m-d",strtotime($fromDate)) .'～'.date("Y-m-d",strtotime($tillDate));
                        } elseif (!empty($fromDate)) {
                            $chargeDate = date("Y-m-d",strtotime($fromDate));
                        } else {
                            $chargeDate = '～';
                        }
                        $chargeDateAry[$serviceNameType][] = $chargeDate;
                    }
                }
                $page_data['chargeDateAry'] = $chargeDateAry;

                // 金額
                $recvAmt = array();
                $billAmtAry = array();
                foreach ($charges as $key => $charge) {
                    $serviceNameType = self::serviceNameType($key);
                    if(!isset($recvAmt[$serviceNameType]))
                        $recvAmt[$serviceNameType] = 0;

                    foreach ($charge as $value) {
                        $billAmt = data_get($value,'BillAmt');

                        if (empty($billAmt)) {
                            $billAmt=0;
                        }
                        $billAmtAry[$serviceNameType][] = (int)$billAmt;
                        $recvAmt[$serviceNameType] +=(int)$billAmt;
                    }
                }
                $page_data['billAmtAry'] = $billAmtAry;
                $page_data['recvAmt'] = $recvAmt;
                $totalAmt = intval(data_get($page_data['recvAmt'],'C'))+intval(data_get($page_data['recvAmt'],'I'))+intval(data_get($page_data['recvAmt'],'D'));
                data_set($page_data['recvAmt'],'totalAmt',$totalAmt);

                $data['charges'] = $page_data;
            }

            // 判斷>>裝機&[I]
            $data['newIns'] = '';
            if(TRUE)
            {
                $serviceName2 = $serviceName;
                if (($key = array_search('1 CATV', $serviceName2)) !== false) {
                    unset($serviceName2[$key]);
                }
                if (($key = array_search('3 DSTB', $serviceName2)) !== false) {
                    unset($serviceName2[$key]);
                }
                if (($key = array_search('C HS', $serviceName2)) !== false) {
                    unset($serviceName2[$key]);
                }
                $serviceName2 = array_values($serviceName2);
                $serviceName2 = [data_get($serviceName2,0)];
                $serviceName2 = implode('',$serviceName2);
                $data['serviceName2'] = $serviceName2;
                if (in_array($workKind,array('1 裝機','6 移機','A 加裝','C 換機')) && strlen($serviceName2) > 0)
                {
                    $data['newIns'] = 'Y';

                    $paramsData = array(
                        'companyNo' => $companyNo,
                        'custId' => $custId,
                        'serviceName2' => $serviceName2,
                        'packageName' => ['isEmpty'],
                    );
                    list($billItem,$packageNameAry01,$billItem01,$aveamt) = self::getPackageName($paramsData);

                    if(empty($packageNameAry01)) {
                        if($custId02 != '') {
                            $paramsData = array(
                                'companyNo' => $companyNo,
                                'custId' => $custId02,
                                'serviceName2' => $serviceName2,
                                'packageName' => ['isEmpty'],
                            );
                            list($billItem,$packageNameAry01,$billItem01,$aveamt) = self::getPackageName($paramsData);
                        } else
                            exit("震江[0200]資料異常[code:5289]，so:$companyNo,住編:$custId;");
                        // throw new Exception('資料錯誤[MS0042]','5289');
                    }
                    data_set($order_info,'BillItem',$billItem01);
                    data_set($order_info,'Aveamt',$aveamt);

                    $qryData = array(
                        'companyNo' => $companyNo,
                        'packageCode' => $packageNameAry01,
                    );
                    $query = $this->OrderRepository->getMS0042($qryData);
                    $query01 = data_get($query,'0');
                    $packDuration = data_get($query01,'PackDuration');
                    data_set($order_info,'PackDuration',$packDuration);

                    $qryData = array(
                        'packageCode' => $packageNameAry01,
                        'serviceName' => $serviceName2,
                        'chargeName' => $billItem,
                    );
                    $query = $this->OrderRepository->getMS0043($qryData);
                    $query01 = data_get($query,'0');
                    $penalAmt01 = intval(data_get($query01,'PenalAmt01'));
                    data_set($order_info,'PenalAmt01',$penalAmt01);

                }
            }

            // 新增LOG
            $p_updataTime = array(
                'p_value' => date('Y-m-d H:i:s'),
                'p_columnName' => 'pdf',
                'p_id' => $orderId,
            );
            $obj = New Ewo_EventController();
            $obj->reqUpdataTime($p_updataTime);
            $company = config('company.name');

            $chkDeviceGet = substr($workSheet,'-1');
            if($chkDeviceGet == 'U') {
                data_set($order_info,'WorkKind','U 到宅取設備');
                $workKind = 'U 到宅取設備';
            }

            $data['info'] = $order_info;

            // 借用/取回 保管單 custody.blade.php
//            if(true)
//            {
//                $page_IO_data = array();
//
//                $page_IO_data['CustID'] = $custId;
//                $page_IO_data['WorkSheet'] = $workSheet;
//                $page_IO_data['company'] = $company[$companyNo];
//                $page_IO_data['CustName'] = $custName;
//                $page_IO_data['InstAddrName'] = $InstAddrName;
//
//                $WorkKindType = '';
//                if(in_array($workKind,array('1 裝機','2 復機','6 移機','8 工程收費','9 停後復機','A 加裝','C 換機')) === true)
//                    $WorkKindType = '裝機';
//                elseif(in_array($workKind,array('3 拆機','4 停機','7 移拆','H 退拆設備','I 退拆分機','K 退次週期項')) === true)
//                    $WorkKindType = '拆機';
//                else
//                    $WorkKindType = '維修';
//                $page_IO_data['WorkKindType'] = $WorkKindType;
//
//                $page_IO_data['Cable_modem_port'] = data_get($borrowmingList,'Cable_modem_port');
//                $page_IO_data['Cable_modem_two_way'] = data_get($borrowmingList,'Cable_modem_two_way');
//                $page_IO_data['Basic_digital_set_top_box'] = data_get($borrowmingList,'Basic_digital_set_top_box');
//                $page_IO_data['Digital_set_top_box_two_way_type'] = data_get($borrowmingList,'Digital_set_top_box_two_way_type');
//                $page_IO_data['camera'] = data_get($borrowmingList,'camera');
//                $page_IO_data['Door_and_window_sensor'] = data_get($borrowmingList,'Door_and_window_sensor');
//                $page_IO_data['Smoke_detector'] = data_get($borrowmingList,'Smoke_detector');
//
//                $page_IO_data['get_Cable_modem_port'] = data_get($retrieveList,'get_Cable_modem_port');
//                $page_IO_data['get_Cable_modem_two_way'] = data_get($retrieveList,'get_Cable_modem_two_way');
//                $page_IO_data['get_Basic_digital_set_top_box'] = data_get($retrieveList,'get_Basic_digital_set_top_box');
//                $page_IO_data['get_Digital_set_top_box_two_way_type'] = data_get($retrieveList,'get_Digital_set_top_box_two_way_type');
//                $page_IO_data['get_camera'] = data_get($retrieveList,'get_camera');
//                $page_IO_data['get_Door_and_window_sensor'] = data_get($retrieveList,'get_Door_and_window_sensor');
//                $page_IO_data['get_Smoke_detector'] = data_get($retrieveList,'get_Smoke_detector');
//
//                $page_IO_data['Cable_accessories_wireless_anti_frequency_sharing_device'] = data_get($borrowmingList,'Cable_accessories_wireless_anti_frequency_sharing_device');
//                $page_IO_data['Cable_accessories_transformer_power_cord'] = data_get($borrowmingList,'Cable_accessories_transformer_power_cord');
//                $page_IO_data['Cable_accessories_Ethernet_cable'] = data_get($borrowmingList,'Cable_accessories_Ethernet_cable');
//                $page_IO_data['Cable_accessories_USB_wireless_anti_frequency_network_card'] = data_get($borrowmingList,'Cable_accessories_USB_wireless_anti_frequency_network_card');
//
//                $page_IO_data['get_Cable_accessories_wireless_anti_frequency_sharing_device'] = data_get($retrieveList,'get_Cable_accessories_wireless_anti_frequency_sharing_device');
//                $page_IO_data['get_Cable_accessories_transformer_power_cord'] = data_get($retrieveList,'get_Cable_accessories_transformer_power_cord');
//                $page_IO_data['get_Cable_accessories_Ethernet_cable'] = data_get($retrieveList,'get_Cable_accessories_Ethernet_cable');
//                $page_IO_data['get_Cable_accessories_USB_wireless_anti_frequency_network_card'] = data_get($retrieveList,'get_Cable_accessories_USB_wireless_anti_frequency_network_card');
//
//                $page_IO_data['Set_top_box_accessories_remote_control'] = data_get($borrowmingList,'Set_top_box_accessories_remote_control');
//                $page_IO_data['Set_top_box_accessories_HDI'] = data_get($borrowmingList,'Set_top_box_accessories_HDI');
//                $page_IO_data['Set_top_box_accessories_AV_cable'] = data_get($borrowmingList,'Set_top_box_accessories_AV_cable');
//                $page_IO_data['Set_top_box_accessories_Chromatic_aberration_line'] = data_get($borrowmingList,'Set_top_box_accessories_Chromatic_aberration_line');
//                $page_IO_data['Set_top_box_accessories_transformer_power_cord'] = data_get($borrowmingList,'Set_top_box_accessories_transformer_power_cord');
//
//                $page_IO_data['get_Set_top_box_accessories_remote_control'] = data_get($retrieveList,'get_Set_top_box_accessories_remote_control');
//                $page_IO_data['get_Set_top_box_accessories_HDI'] = data_get($retrieveList,'get_Set_top_box_accessories_HDI');
//                $page_IO_data['get_Set_top_box_accessories_AV_cable'] = data_get($retrieveList,'get_Set_top_box_accessories_AV_cable');
//                $page_IO_data['get_Set_top_box_accessories_Chromatic_aberration_line'] = data_get($retrieveList,'get_Set_top_box_accessories_Chromatic_aberration_line');
//                $page_IO_data['get_Set_top_box_accessories_transformer_power_cord'] = data_get($retrieveList,'get_Set_top_box_accessories_transformer_power_cord');
//
//                $page_IO_data['Set_top_box_accessories_smart_card'] = data_get($borrowmingList,'Set_top_box_accessories_smart_card');
//                $page_IO_data['Set_top_box_accessories_external_hard_disk'] = data_get($borrowmingList,'Set_top_box_accessories_external_hard_disk');
//                $page_IO_data['Set_top_box_accessories_USB_wireless_anti_frequency_network_card'] = data_get($borrowmingList,'Set_top_box_accessories_USB_wireless_anti_frequency_network_card');
//                $page_IO_data['Set_top_box_accessories_ATV_set_top_box'] = data_get($borrowmingList,'Set_top_box_accessories_ATV_set_top_box');
//                $page_IO_data['Set_top_box_accessories_Bluetooth_remote_control'] = data_get($borrowmingList,'Set_top_box_accessories_Bluetooth_remote_control');
//
//                $page_IO_data['get_Set_top_box_accessories_smart_card'] = data_get($retrieveList,'get_Set_top_box_accessories_smart_card');
//                $page_IO_data['get_Set_top_box_accessories_external_hard_disk'] = data_get($retrieveList,'get_Set_top_box_accessories_external_hard_disk');
//                $page_IO_data['get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card'] = data_get($retrieveList,'get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card');
//                $page_IO_data['get_Set_top_box_accessories_ATV_set_top_box'] = data_get($retrieveList,'get_Set_top_box_accessories_ATV_set_top_box');
//                $page_IO_data['get_Set_top_box_accessories_Bluetooth_remote_control'] = data_get($retrieveList,'get_Set_top_box_accessories_Bluetooth_remote_control');
//
//                $page_IO_data['Smart_home_accessories_transformer_power_cord'] = data_get($borrowmingList,'Smart_home_accessories_transformer_power_cord');
//                $page_IO_data['get_Smart_home_accessories_transformer_power_cord'] = data_get($retrieveList,'get_Smart_home_accessories_transformer_power_cord');
//
//                $page_IO_data['Fiber_Modem_HomeOnt'] = data_get($borrowmingList,'Fiber_Modem_HomeOnt');
//                $page_IO_data['get_Fiber_Modem_HomeOnt'] = data_get($retrieveList,'get_Fiber_Modem_HomeOnt');
//
//                $page_name = 'CUSTODY';
//
//                $page_list[] = $page_name;
//
//                $data[$page_name] = $page_IO_data;
//
//            }

            return view('pdf.table_v3_fet',compact('data'));

        } catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
            $code = (empty($e->getCode()))? '0500': substr('000'.$e->getCode(),-4);
            $p_data = array(
                'code' => $code,
                'status' => 'error',
                'meg' => '資料錯誤='.$e->getMessage(),
                'data' => '',
                'date' => date('Y-m-d H:i:s')
            );
        }

        return $p_data;
    }


    // PDF 合併[條款]
    private function mergePDF($companyNo,$workSheet,$serviceName,$directory,$pdfTerms)
    {
        $fileName = "$workSheet.pdf";
        $fileNameHead = $workSheet.'_head.pdf';
        $workFile = "$directory/$fileNameHead";
        $saveFile = "$directory/$fileName";
        $password = '0000';

        // TermsFile
        $dtvTermsFile = '';
        $dtvPCLTermsFile = '';
        $cmTermsFile = '';
        $cmPCLTermsFile = '';
        $equipmentFile = "$directory/".$workSheet.'_equipment.pdf';

        $serviceName2 = $serviceName;
        if (($key = array_search('1 CATV', $serviceName2)) !== false) {
            unset($serviceName2[$key]);
        }
        if (($key = array_search('3 DSTB', $serviceName2)) !== false) {
            unset($serviceName2[$key]);
        }

        // 條款目錄
//        if(date('Ymd') >= '20230213')
            $forder02 = "pdfTerms/$pdfTerms";
//        else
//            $forder02 = "pdfTerms";

        if(in_array('1 CATV',$serviceName) || in_array('3 DSTB',$serviceName)) {
            $dtvTermsFile = public_path("$forder02/dtv$companyNo.pdf");
            $dtvPCLTermsFile = ($companyNo == '209') ?
                public_path("$forder02/dtv_pcl_209.pdf") :
                public_path("$forder02/dtv_pcl.pdf");
        }

        if(count($serviceName2)) {
            $cmTermsFile = ($companyNo == '209') ?
                public_path("$forder02/cm209.pdf") :
                public_path("$forder02/cmdef.pdf");
            $cmPCLTermsFile = ($companyNo == '209') ?
                public_path("$forder02/cm_pcl_209.pdf") :
                public_path("$forder02/cm_pcl.pdf");
        }

        $command = '/usr/bin/python3 ';
        $command .= escapeshellcmd(public_path('python/pdf_merge/pdf_merge.py'));
        $command .= ' --file1-path "'.$workFile.'"';
        $command .= ' --file2-path "'.$dtvTermsFile.'"';
        $command .= ' --file3-path "'.$dtvPCLTermsFile.'"';
        $command .= ' --file4-path "'.$cmTermsFile.'"';
        $command .= ' --file5-path "'.$cmPCLTermsFile.'"';
        $command .= ' --file6-path "'.$equipmentFile.'"';
        $command .= ' --output-path "'.$saveFile.'"';
        $command .= ' --password "'.$password.'"';

        $output = shell_exec($command);
        unlink($workFile); // 刪除-table
        unlink($equipmentFile); // 刪除-設備取用單
        if(empty($output)) {
            return true;
        }
        else {
            exit("PDF合併失敗[code:0590]");
//            throw new Exception("PDF合併失敗[command=$command]" . print_r($output, 1), '0590');
        }

    }


    // 設備清單更新借用單、取回單
    private function setDeviceToEquipmentList($companyNo,$workSheet,$workKind)
    {
        $borrowmingListCunt = array();
        $retrieveListCunt = array();
        $borrowmingSelListCunt = array();
        $retrieveSelListCunt = array();
        $retrieveList = array();
        $borrowmingList = array();
        $retrieveSelList = array();
        $borrowmingSelList = array();

//        $chargeData = array(
//            'so' => $companyNo,
//            'worksheet' => $workSheet,
//        );
//        $ms0301List = $this->OrderRepository->getOrderCharge($chargeData);

        $whereAry = array(
            'companyNo' => $companyNo,
            'workSheet' => $workSheet,
            'select' => array(
                ['column' => 'WorkKind','asName' => 'MS300.'],
                ['column' => 'CompanyNo','asName' => 'MS301.'],
                ['column' => 'CustID','asName' => 'MS301.'],
                ['column' => 'SubsID','asName' => 'MS301.'],
                ['column' => 'WorkSheet','asName' => 'MS301.'],
                ['column' => 'ServiceName','asName' => 'MS301.'],
                ['column' => 'ChargeName','asName' => 'MS301.'],
                ['column' => 'BillAmt','asName' => 'MS301.'],
                ['column' => 'FromDate','asName' => 'MS301.'],
                ['column' => 'TillDate','asName' => 'MS301.'],
                ['column' => 'NextDate','asName' => 'MS301.'],
                ['column' => 'SheetStatus','asName' => 'MS301.'],
                ['column' => 'Worker1','asName' => 'MS301.'],
                ['column' => 'AssignSheet','asName' => 'MS301.'],
                ['column' => 'BrokerKind','asName' => 'MS301.'],
                ['column' => 'BookingNo','asName' => 'MS301.'],
                ['column' => 'ServiceName','asName' => 'MS301.'],
                ['column' => 'ChargeKind','asName' => 'MS301.'],
                ['column' => 'SingleSn','asName' => 'MS301.'],
                ['column' => 'OrgSingleSn','asName' => 'MS301.'],
                ['column' => 'SheetSNo','asName' => 'MS301.'],
            ),
            'orderBy' => array(
                ['name'=>'BookDate','type'=>'desc','asName'=>'MS301.']
            )
        );
        $ms0301List = $this->OrderRepository->getWorksheetList($whereAry);

        foreach($ms0301List as $k => $t) {
            $vSubsID = data_get($t,'SubsID');
            $vChargeKind = data_get($t,'ChargeKind');
            $vSingleSN = data_get($t,'SingleSn');
            $vServiceName = data_get($t,'ServiceName');
            $vOrgSingleSn = data_get($t,'OrgSingleSn');
            $vWorkKind = data_get($t,'WorkKind');
            if(!in_array($vChargeKind,['40','50','57','4A'])) continue;
            if($vSubsID == $vSingleSN) continue;

            if(!empty($vSingleSN) || !empty($vOrgSingleSn)) {
                $vSingleSNInfo = (empty($vSingleSN))? [] : self::getDeviceInfo($companyNo,$vSingleSN);
                // SingleSn
                $singleEquInfo = self::getEquipmentInfo(data_get($vSingleSNInfo,'MTNo'));
                if(empty(data_get($singleEquInfo,'id')))
                    $singleEquInfo = self::getEquipmentInfo_2($vServiceName);
                $vSingleSnData = array(
                    'id' => data_get($singleEquInfo,'id'),
                    'equType' => data_get($singleEquInfo,'type'),
                    'selectType' => data_get($singleEquInfo,'selectType'),
                    'singlesn' => $vSingleSN,
                    'model' => data_get($vSingleSNInfo,'CSModel'),
                    'desc' => 'subsid='.$vSubsID.'#mtno='.data_get($vSingleSNInfo,'MTNo'),
                );
                // OrgSingleSn
                $vOrgSingleSNInfo = (empty($vOrgSingleSn))? [] : self::getDeviceInfo($companyNo,$vOrgSingleSn);
                $orgsingleEquInfo = self::getEquipmentInfo(data_get($vOrgSingleSNInfo,'MTNo'));
                if(empty(data_get($orgsingleEquInfo,'id')))
                    $orgsingleEquInfo = self::getEquipmentInfo_2($vServiceName);
                $vOrgSingleSnData = array(
                    'id' => data_get($orgsingleEquInfo,'id'),
                    'equType' => data_get($orgsingleEquInfo,'type'),
                    'selectType' => data_get($orgsingleEquInfo,'selectType'),
                    'singlesn' => $vOrgSingleSn,
                    'model' => data_get($vOrgSingleSNInfo,'CSModel'),
                    'desc' => 'subsid='.$vSubsID.'#mtno='.data_get($vOrgSingleSNInfo,'MTNo'),
                );

                switch ($vWorkKind) {
                    //('1 裝機','2 復機','6 移機','8 工程收費','9 停後復機','A 加裝','C 換機')
                case '1 裝機':
                case '2 復機':
                case '6 移機':
                case '9 停後復機':
                    if(!empty($vSingleSN) && !empty($vSingleSnData['id'])) {
                        if($vSingleSnData['selectType']) {
                            $borrowmingSelList[$vSingleSnData['id']][] = $vSingleSnData;
                            if(!isset($borrowmingSelListCunt[$vSingleSnData['equType']]))
                                $borrowmingSelListCunt[$vSingleSnData['equType']][$vSingleSnData['id']] = 1;
                            else
                                $borrowmingSelListCunt[$vSingleSnData['equType']][$vSingleSnData['id']] += 1;
                        } else {
                            $borrowmingList[$vSingleSnData['id']][] = $vSingleSnData;
                            if(!isset($borrowmingListCunt[$vSingleSnData['equType']]))
                                $borrowmingListCunt[$vSingleSnData['equType']] = 1;
                            else
                                $borrowmingListCunt[$vSingleSnData['equType']] += 1;
                        }
                    }
                    break;
                case 'C 換機':
                    // 借用
                    if(!empty($vSingleSN) && !empty($vSingleSnData['id'])) {
                        if($vSingleSnData['selectType']) {
                            $borrowmingSelList[$vSingleSnData['id']][] = $vSingleSnData;
                            if(!isset($borrowmingSelListCunt[$vSingleSnData['equType']]))
                                $borrowmingSelListCunt[$vSingleSnData['equType']][$vSingleSnData['id']] = 1;
                            else
                                $borrowmingSelListCunt[$vSingleSnData['equType']][$vSingleSnData['id']] += 1;
                        } else {
                            $borrowmingList[$vSingleSnData['id']][] = $vSingleSnData;
                            if(!isset($borrowmingListCunt[$vSingleSnData['equType']]))
                                $borrowmingListCunt[$vSingleSnData['equType']] = 1;
                            else
                                $borrowmingListCunt[$vSingleSnData['equType']] += 1;
                        }

                    }
                    // 取回
                    if(!empty($vOrgSingleSn) && !empty($vOrgSingleSnData['id'])) {
                        if($vOrgSingleSnData['selectType']) {
                            $retrieveSelList[$vOrgSingleSnData['id']][] = $vOrgSingleSnData;
                            if(!isset($retrieveSelListCunt[$vOrgSingleSnData['equType']]))
                                $retrieveSelListCunt[$vOrgSingleSnData['equType']][$vOrgSingleSnData['id']] = 1;
                            else
                                $retrieveSelListCunt[$vOrgSingleSnData['equType']][$vOrgSingleSnData['id']] += 1;
                        } else {
                            $retrieveList[$vOrgSingleSnData['id']][] = $vOrgSingleSnData;
                            if(!isset($retrieveListCunt[$vOrgSingleSnData['equType']]))
                                $retrieveListCunt[$vOrgSingleSnData['equType']] = 1;
                            else
                                $retrieveListCunt[$vOrgSingleSnData['equType']] += 1;
                        }
                    }
                    break;
                }
            }
        }

        // 更新，wm_orderlist borrowmingList retrieveList
        if(count($borrowmingList) || count($retrieveList)) {
            $query = $this->OrderRepository->getOrderInfo(['so' => $companyNo,'worksheet' => $workSheet,]);
            $vBorrowmingList = data_get($query,'BorrowmingList');
            $vRetrieveList = data_get($query,'RetrieveList');

            $objUpd = new LogRepository();
            $vBorrowmingListJson = json_decode($vBorrowmingList,true);
            $vBorrowmingListJson['device'] = $borrowmingList;
            $vBorrowmingListNew = json_encode($vBorrowmingListJson);
            $vBorrowmingListData = array(
                'p_columnName' => 'BorrowmingList',
                'p_value' => $vBorrowmingListNew,
                'p_companyNo' => $companyNo,
                'p_workSheet' => $workSheet,
            );
            $objUpd->updateEventTime($vBorrowmingListData);

            $vRetrieveListJosn = json_decode($vRetrieveList,true);
            $vRetrieveListJosn['device'] = $retrieveList;
            $vRetrieveListNew = json_encode($vRetrieveListJosn);
            $vRetrieveListData = array(
                'p_columnName' => 'RetrieveList',
                'p_value' => $vRetrieveListNew,
                'p_companyNo' => $companyNo,
                'p_workSheet' => $workSheet,
            );
            $objUpd->updateEventTime($vRetrieveListData);
        }

        $ret = array(
            'borrowmingList' => $borrowmingList,
            'retrieveList' => $retrieveList,
            'borrowmingListCunt' => $borrowmingListCunt,
            'retrieveListCunt' => $retrieveListCunt,
            'borrowmingSelList' => $borrowmingSelList,
            'retrieveSelList' => $retrieveSelList,
            'borrowmingSelListCunt' => $borrowmingSelListCunt,
            'retrieveSelListCunt' => $retrieveSelListCunt,
        );
        return $ret;
    }


    // 設備料號>>ID [wm_equipment.mtnoList] 2.0，查無清單，改用其他
    private function getEquipmentInfo_2($serviceName)
    {
        switch ($serviceName) {
        case '1 CATV':
        case '3 DSTB':
            $id = '27';
            $type = 'D';
            break;

        default:
            $id = '28';
            $type = 'I';
            break;
        }

        $ret = array(
            'id' => $id,
            'type' => $type,
            'selectType' => '0',
        );
        return $ret;
    }


    // 設備料號>>ID [wm_equipment.mtnoList]
    private function getEquipmentInfo($mtno)
    {
        if(empty($mtno)) return '';

        $qryData = array(
            'enable' => '1',
            'mtnoLike' => $mtno,
        );
        $query = $this->OrderRepository->get_wm_equipment($qryData);
        $query01 = data_get($query,'0');
        $id = data_get($query01,'Id');
        $type = data_get($query01,'type');
        $selectType = data_get($query01,'selectType');

        $ret = array(
            'id' => $id,
            'type' => $type,
            'selectType' => $selectType,
        );
        return $ret;
    }


    // 取得序號Info
    private function getDeviceInfo($companyNo,$singleSN)
    {
        $obj = new ConsumablesRepository();
        $qryData = array("companyno"=>$companyNo,"singlesn"=>$singleSN);
        $query = $obj->getDevLisFroPla($qryData);
        $query01 = data_get($query['list'],'0');

        $ret = (array)$query01;
        return $ret;
    }


    // 服務別>>類別
    private function serviceNameType($serviceName)
    {
        switch ($serviceName) {
        case '1 CATV':
            $ret = 'C';
            break;
        case '3 DSTB':
            $ret = 'D';
            break;
        default:
            $ret = 'I';
            break;
        }

        return $ret;
    }


    private function classifyCharge($orderCharge,$workKind='')
    {
        $data = array();
        $chkRepeat = array();

        foreach ($orderCharge as $charge) {
            $serviceName = data_get($charge,'ServiceName');
            $db = data_get($charge,'DB');

            $amt = (int)data_get($charge,'BillAmt');
            $vSubsid = data_get($charge,'SubsID');

            // 判斷重複項目[key]
            $vWorksheet = data_get($charge,'WorkSheet');
            $vSheetsno = data_get($charge,'SheetSNo');
            $vChargename = data_get($charge,'ChargeName');
            $repeatKey = "$vWorksheet#$vSheetsno#$vChargename";

            if ($db == '3200') {
                $companyNo = data_get($charge,'CompanyNo');
                $workSheet = data_get($charge,'WorkSheet');
                $chargeName = data_get($charge,'ChargeName');
                $infoData = array(
                    'so' => $companyNo,
                    'worksheet2' => $workSheet,
                    'chargename' => $chargeName
                );

                $FromDate = '';
                $TillDate = '';
                if(in_array($workKind,['6 移機','A 加裝','C 換機']))
                {
                    $FromDate = data_get($charge,'RecvStart');
                    $TillDate = data_get($charge,'RecvExpire');
                    $TillDate = date('Y-m-d',strtotime('-1 day',strtotime($TillDate)));
                }
                else
                {
                    $info = $this->OrderRepository->getOrderCharge($infoData);

                    if (count($info) > 0) {
                        $FromDate = data_get($info[0],'FromDate');
                        $TillDate = data_get($info[0],'TillDate');
                    }
                }

                $amt = (int)data_get($charge,'RecvAmt');
                $WorkSheet = data_get($charge,'WorkSheet');
                $RecvNo = data_get($charge,'RecvNo');
                $WorkRecvYN = data_get($charge,'WorkRecvYN');

                if ($WorkSheet != $RecvNo && $WorkRecvYN != 'Y') {
                    continue;
                }

                data_set($charge, 'FromDate', $FromDate);
                data_set($charge, 'TillDate', $TillDate);

                // 判斷重複項目[key]
                $recvno = data_get($charge,'RecvNo');
                $recvsn = data_get($charge,'RecvSN');
                $chargename = data_get($charge,'ChargeName');
                $repeatKey = "$recvno#$recvsn#$chargename";
            }

            // 檢查重複
            if(in_array($repeatKey,$chkRepeat))
                continue;
            else
                array_push($chkRepeat,$repeatKey);

            data_set($charge, 'BillAmt', $amt);
            $ret[$serviceName][] = $charge;
        }

        return $ret;
    }


    private function replace_symbol($string, $symbol, $begin_num = 0, $end_num = 0) {
        $length = strlen($string);
        $begin_num = (int) $begin_num;
        $end_num = (int) $end_num;
        $str1 = substr( $string ,0, $begin_num );
        $str2 = substr( $string , -$end_num );
        $reduce_num = $length - $begin_num - $end_num;
        $str3 ="";
        for ($i = 0; $i <= $reduce_num; $i++) {
            $str3 .= $symbol;
        }

        return $str1.$str3.$str2;
    }


    private function replace_symbol_address($string, $symbol, $begin_num = 0, $end_num = 0) {
        $length = mb_strlen($string);
        $begin_num = (int) $begin_num;
        $end_num = (int) $end_num;
        $str1 = mb_substr( $string ,0, $begin_num,"utf-8" );
        $str2 = mb_substr( $string ,-$end_num, null,"utf-8" );
        $reduce_num = $length - $begin_num - $end_num;
        $str3 ="";

        for ($i = 0; $i <= $reduce_num; $i++) {
            $str3 .= $symbol;
        }

        return $str1.$str3.$str2;
    }


    private function getPackageName($data)
    {
        $companyNo = $data['companyNo'];
        $custId = $data['custId'];
        $serviceName2 = $data['serviceName2'];
        $packageName = $data['packageName'];

        $qryData = array(
            'companyno' => $companyNo,
            'custid' => $custId,
            'servicenameIn' => ['name'=>'ServiceName','ary'=>$serviceName2],
            'packageNameNotIn' => ['name'=>'PackageName','ary'=>$packageName],
            'order' => array(
                ['column' => 'CustStatus','type' => 'asc',],
                ['column'=>'PackageName','type'=>'desc'],
            ),
        );
        $query = $this->OrderRepository->getMS0200($qryData);
        $query01 = data_get($query,'0');
        $billItem = data_get($query01, 'BillItem');
        $billItemAry = explode(' ',$billItem);
        $billItem01 = data_get($billItemAry,'1');
        $aveamt = intval(data_get($query01, 'Aveamt'));

        $packageName = data_get($query01,'PackageName');
        $packageNameAry = explode(' ',$packageName);
        $packageNameAry01 = data_get($packageNameAry,'0');

        $ret = array($billItem,$packageNameAry01,$billItem01,$aveamt);
        return $ret;
    }


}
