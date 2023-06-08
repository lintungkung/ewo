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
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Reason\ReasonRepository;
use App\Repositories\Order\OrderRepository;


class PDF_v2 extends Controller
{

    public function __construct(OrderRepository $OrderRepository)
    {
        $this->OrderRepository = $OrderRepository;
    }

    public function createPDF($source,$version,$orderId)
    {
        set_time_limit(300);
        $p_time_start = microtime(true);
        $p_run_title = "PDF_$version"."_ID_$orderId";

        $page_list = array();
        $page_data = array();
        $ewo_url = config('order.EWO_URL');
        $logo_url = $ewo_url.'/img/logo_04.png';
        $linkQrCodeUrl = $ewo_url.'/img/LineQrCode/qrCodeM.png?'.date('Ymd');
        $linkQrCodeUrl = public_path('img/LineQrCode/qrCodeM.png');
        $logo_url_fet = $ewo_url.'/img/logo_fet.png';

        $p_orderId = $orderId;
        if(substr($p_orderId,0,4) === 'NULL') {
            $valAry = explode('-',$orderId);
            $companyNo = $valAry[1];
            $workSheet = $valAry[2];
            $orderId = '0';

        }

        $pdf_info = $this->OrderRepository->getOrderPdfInfo($orderId);

        if($source == 'app') {
            $pdf_info = null;
        }

        if (!$pdf_info) {


            if(substr($p_orderId,0,4) === 'NULL') {
                $data = array(
                    'so' => $companyNo,
                    'worksheet' => $workSheet,
                );
                $order_info = $this->OrderRepository->getOrderInfo($data);
            } else {
                $order_info = $this->OrderRepository->getOrderInfoById($orderId);
            }

            if(!$order_info) {
                $data = array(
                    'so' => $companyNo,
                    'worksheet' => $workSheet,
                );
                $selAry = array('worker1','custid','bookdate');
                $ms0301ByAssignSheet = $this->OrderRepository->getOrderCharge($data,$selAry);

                $ms0301ByAssignSheet = $ms0301ByAssignSheet[0];
                $custid = data_get($ms0301ByAssignSheet,'custid');
                $bookdate = data_get($ms0301ByAssignSheet,'bookdate');
                $worker1 = data_get($ms0301ByAssignSheet,'worker1');
                $worker1Ary = explode(' ',$worker1);
                $data = [
                    'WorkerNum' => data_get($worker1Ary,0),
                    'WorkerName' => data_get($worker1Ary,1),
                    'so' => $companyNo,
                    'worksheet' => $workSheet,
                    'custid' => $custid,
                    'bookdate' => $bookdate,
                    'pdfTerms' => config('order.PDF_TERMS_V'),
                    'pdf_v' => config('order.PDF_CODE_V'),
                ];
                $orderId = $this->OrderRepository->addOrderlist($data);

                $order_info = $this->OrderRepository->getOrderInfoById($orderId);

            }

            $orderId = data_get($order_info,'Id');
            $so = data_get($order_info,'CompanyNo');
            $workSheet = data_get($order_info,'WorkSheet');
            $serviceName = data_get($order_info,'ServiceName');
            $orkerNum = data_get($order_info,'WorkerNum');
            $pdfTerms = data_get($order_info,'pdfTerms');
            $pdfTerms = empty($pdfTerms)? config('order.PDF_TERMS_V') : $pdfTerms;

            if(empty($serviceName)) {

                $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
                $url = $protocol.$_SERVER['HTTP_HOST']."/ewo/order_info/$so-$workSheet/dd?printpdf=Y&userid=$orkerNum";
                $obj = New ConsumablesAPIController();
                $data = array(
                    'url' => $url,
                    'method' => 'GET',
                    'header'    => '',
                    'post_data' => array(),
                );
                $a = $obj->curl($data);

                if($a == 'OK') {
                    $order_info = $this->OrderRepository->getOrderInfoById($orderId);

                    $pdf_info = $this->OrderRepository->getOrderPdfInfo($orderId);

                } else {
                    throw new Exception('生成失敗'.print_r($a,1),'0570');
                }
            }

            $finish = data_get($order_info,'finsh');
            $zj_finsh = data_get($order_info, "zj_finsh");
            $assignSheet = data_get($order_info,'AssignSheet');
            $serviceName = json_decode($order_info->ServiceName);
            $IVR = json_decode($order_info->SubsCP);

            $TeleNum01_200 = data_get($order_info,'TeleNum01_200');
            $CustID = data_get($order_info,'CustID');
            $SubsID = data_get($order_info,'SubsID');
            $BookDate = data_get($order_info,'BookDate');
            $WorkKind = data_get($order_info,'WorkKind');
            $CustName = data_get($order_info,'CustName');
            $NetID = data_get($order_info,'NetID');
            $SaleCampaign = data_get($order_info,'SaleCampaign');
            $CreateName = data_get($order_info,'CreateName');
            $CreateTime = data_get($order_info,'CreateTime');
            $WorkerName = data_get($order_info,'WorkerName');
            $WorkTeam = data_get($order_info,'WorkTeam');
            $assignSheet = data_get($order_info,'AssignSheet');
            $create_at = data_get($order_info,'create_at');
            $MSComment1 = data_get($order_info,'MSComment1');

            $checkDSTB = json_decode($order_info->dstbcheck);
            $checkCM = json_decode($order_info->cmcheck);
            $checkTWMBB = json_decode($order_info->twmbbcheck);
            $borrowmingList = json_decode($order_info->BorrowmingList);
            $retrieveList = json_decode($order_info->RetrieveList);

            $deviceCount = data_get($order_info,'deviceCount');
            $deviceSWVersion = data_get($order_info,'deviceSWVersion');
            $maintainHistory = data_get($order_info,'maintainHistory');

            $InstAddrName = data_get($order_info,'InstAddrName');
            $WorkCause = data_get($order_info,'WorkCause');

            $MSContract = data_get($order_info,'MSContract');
            $MSContract2 = data_get($order_info,'MSContract2');
            $saleAP = data_get($order_info,'saleAP');

            $pdfData = array(
                'CompanyNo' => $so,
                'WorkSheet' => $workSheet,
                'finsh' => $finish,
                'ServiceName' => $serviceName,
                'TeleNum01_200' => $TeleNum01_200,
                'CustID' => $CustID,
                'SubsID' => $SubsID,
                'BookDate' => $BookDate,
                'WorkKind' => $WorkKind,
                'CustName' => $CustName,
                'NetID' => $NetID,
                'SaleCampaign' => $SaleCampaign,
                'CreateName' => $CreateName,
                'CreateTime' => $CreateTime,
                'WorkerName' => $WorkerName,
                'WorkTeam' => $WorkTeam,
                'AssignSheet' => $assignSheet,
                'dstbcheck' => $checkDSTB,
                'cmcheck' => $checkCM,
                'twmbbcheck' => $checkTWMBB,
                'BorrowmingList' => $borrowmingList,
                'SubsCP' => $IVR,
                'create_at' => $create_at,
                'RetrieveList' => $retrieveList,
                'MSComment1' => $MSComment1,
                'deviceCount' => $deviceCount,
                'deviceSWVersion' => $deviceSWVersion,
                'maintainHistory' => $maintainHistory,
                'InstAddrName' => $InstAddrName,
                'WorkCause' => $WorkCause,
                'MSContract' => $MSContract,
                'MSContract2' => $MSContract2,
                'saleAP' => $saleAP,
            );

            $insertData = array(
                'CompanyNo' => $so,
                'WorkSheet' => $workSheet,
                'Version' => $version,
                'AssegnUser' => 'app',
                'Data' => json_encode($pdfData),
                'orderListId' => $orderId,
                'pdfTerms' => $pdfTerms,
            );

            $pdf_info = $this->OrderRepository->getOrderPdfInfo($orderId);

            if (!$pdf_info) {
                $this->OrderRepository->insertOrderPdfInfo($insertData);
            }

        } else {
            $order_info = json_decode(data_get($pdf_info,'Data'));
        }

        // logo img url
        $companyNo = data_get($order_info,'CompanyNo');
        $logo_url = $ewo_url."/img/logo$companyNo.png?".date('Ymd');
        $logo_url = public_path("/img/logo$companyNo.png");

        // terms
        $pdfTerms = data_get($order_info,'pdfTerms');
        $pdfTerms = empty($pdfTerms)? '20221101' : $pdfTerms;

        if (gettype($order_info->dstbcheck) == 'string' ||
            gettype($order_info->cmcheck) == 'string' ||
            gettype($order_info->twmbbcheck) == 'string' ||
            gettype($order_info->BorrowmingList) == 'string' ||
            gettype($order_info->RetrieveList) == 'string')
        {
            $checkDSTB = json_decode($order_info->dstbcheck);
            $checkCM = json_decode($order_info->cmcheck);
            $checkTWMBB = json_decode($order_info->twmbbcheck);
            $borrowmingList = json_decode($order_info->BorrowmingList);
            $retrieveList = json_decode($order_info->RetrieveList);
        } else {
            $checkDSTB = data_get($order_info,'dstbcheck');
            $checkCM = data_get($order_info,'cmcheck');
            $checkTWMBB = data_get($order_info,'twmbbcheck');
            $borrowmingList = data_get($order_info,'BorrowmingList');
            $retrieveList = data_get($order_info,'RetrieveList');
        }

        $so = data_get($order_info,'CompanyNo');
        $workSheet = data_get($order_info,'WorkSheet');
        $finish = data_get($order_info,'finsh');
        $zj_finsh = data_get($order_info, "zj_finsh");
        $assignSheet = data_get($order_info,'AssignSheet');
        $assignSheet = explode(",", $assignSheet);
        $assignSheet[] = $workSheet;
        $assignSheet = array_filter($assignSheet);
        $assignSheet = array_values($assignSheet);


        $BrokerKind = '';
        $bill = array();

        //WM_orderlist
        $data = array(
            'so' => $so,
            'worksheet' => $workSheet,
        );
        $selAry = array('finshMS3200','finsh','WorkerNum','WorkerName');
        $finshMS3200 = $this->OrderRepository->getOrderInfo($data,'',$selAry);
        $finsh = data_get($finshMS3200,'finsh');
        $workKind = data_get($order_info,'WorkKind');
        $worker1 = data_get($finshMS3200,'WorkerNum').' '.data_get($finshMS3200,'WorkerName');
        $worker1 = trim($worker1);


        //3200
        if(is_Null($finsh) === true) {
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

        } else {
            $finshMS3200 = data_get($finshMS3200,'finshMS3200');
            $finshMS3200 = json_decode($finshMS3200,1);

            if(is_array($finshMS3200))
            foreach ($finshMS3200 as $key => $itemBill) {
                data_set($itemBill, 'DB', '3200');
                $chkRecvYN = data_get($itemBill,'RecvYN');

                if($chkRecvYN === 'Y') continue;

                $bill[] = $itemBill;
            }

        }


        //MS0301
        $chargeData = array(
            'so' => $so,
            'worksheetin' => $assignSheet,
            'worker1like' => "$worker1%",
        );
        $chargeBill = $this->OrderRepository->getOrderCharge($chargeData);
        foreach ($chargeBill as $key => $itemBill) {
            $SheetStatus = data_get($itemBill,'SheetStatus');

            if ($SheetStatus == 'A.取消') {
                continue;
            }

            $bill[] = $itemBill;

        }


        $charges = $this->classifyCharge($bill,$workKind);

        $serviceName = array_keys($charges);

        if (empty($borrowmingList)) {

            $borrowmingList = '{
                "Cable_modem_port": "0"
                ,"Cable_modem_two_way": "0"
                ,"Basic_digital_set_top_box": "0"
                ,"Digital_set_top_box_two_way_type": "0"
                ,"camera": "0"
                ,"Door_and_window_sensor": "0"
                ,"Smoke_detector": "0"
                ,"Cable_accessories_wireless_anti_frequency_sharing_device": "0"
                ,"Cable_accessories_transformer_power_cord": "0"
                ,"Cable_accessories_Ethernet_cable": "0"
                ,"Cable_accessories_USB_wireless_anti_frequency_network_card": "0"
                ,"Set_top_box_accessories_remote_control": "0"
                ,"Set_top_box_accessories_HDI": "0"
                ,"Set_top_box_accessories_AV_cable": "0"
                ,"Set_top_box_accessories_Chromatic_aberration_line": "0"
                ,"Set_top_box_accessories_transformer_power_cord": "0"
                ,"Set_top_box_accessories_smart_card": "0"
                ,"Set_top_box_accessories_external_hard_disk": "0"
                ,"Set_top_box_accessories_USB_wireless_anti_frequency_network_card": "0"
                ,"Set_top_box_accessories_ATV_set_top_box": "0"
                ,"Set_top_box_accessories_Bluetooth_remote_control": "0"
                ,"Smart_home_accessories_transformer_power_cord": "0"
                ,"Fiber_Modem_HomeOnt": "0"
            }';

            $borrowmingList = json_decode($borrowmingList);
        }

        if (empty($retrieveList)) {

            $retrieveList = '{
                "get_Cable_modem_port": "0"
                ,"get_Cable_modem_two_way": "0"
                ,"get_Basic_digital_set_top_box": "0"
                ,"get_Digital_set_top_box_two_way_type": "0"
                ,"get_camera": "0"
                ,"get_Door_and_window_sensor": "0"
                ,"get_Smoke_detector": "0"
                ,"get_Cable_accessories_wireless_anti_frequency_sharing_device": "0"
                ,"get_Cable_accessories_transformer_power_cord": "0"
                ,"get_Cable_accessories_Ethernet_cable": "0"
                ,"get_Cable_accessories_USB_wireless_anti_frequency_network_card": "0"
                ,"get_Set_top_box_accessories_remote_control": "0"
                ,"get_Set_top_box_accessories_HDI": "0"
                ,"get_Set_top_box_accessories_AV_cable": "0"
                ,"get_Set_top_box_accessories_Chromatic_aberration_line": "0"
                ,"get_Set_top_box_accessories_transformer_power_cord": "0"
                ,"get_Set_top_box_accessories_smart_card": "0"
                ,"get_Set_top_box_accessories_external_hard_disk": "0"
                ,"get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card": "0"
                ,"get_Set_top_box_accessories_ATV_set_top_box": "0"
                ,"get_Set_top_box_accessories_Bluetooth_remote_control": "0"
                ,"get_Smart_home_accessories_transformer_power_cord": "0"
                ,"get_Fiber_Modem_HomeOnt": "0"
            }';

            $retrieveList = json_decode($retrieveList);
        }

        try {

            if (!$pdf_info) {
                $order_info_id = $order_info->Id;
            } else {
                $order_info_id = data_get($pdf_info,'orderListId');
            }

            $p_updataTime = array(
                'p_value' => date('Y-m-d H:i:s'),
                'p_columnName' => 'pdf',
                'p_id' => $order_info_id,
            );
            $obj = New Ewo_EventController();
            $obj->reqUpdataTime($p_updataTime);

            $companyAddress = config('company.addres');
            $company = config('company.name');


            //訂戶生日
            // $CustBirth = $order_info->CustBirth;
            // $birthYear = date("Y",strtotime($CustBirth)) - 1911;
            // $birthMonth = date("m",strtotime($CustBirth));
            // $birthDay = date("d",strtotime($CustBirth));


            //性別
            // $PersonID = $order_info->PersonID;
            // $checkSex = substr($PersonID, 1,1);

            // IVR簡碼
            if (gettype($order_info->SubsCP) == 'string') {
                $IVR = json_decode($order_info->SubsCP);
            } else {
                $IVR = data_get($order_info,'SubsCP');
            }

            $dstbIVR = '';
            $ivrAry = array();
            $editIVR = (array)$IVR;
            if(count($editIVR)) {
                $ivrAry = array_values($editIVR);
                $dstbIVR = implode(',',$ivrAry);
            }

            //家電及手機號碼
            $TeleCod02_200 = data_get($order_info,'TeleCod02_200');
            $TeleNum02_200 = data_get($order_info,'TeleNum02_200');
            $TeleNum01_200 = data_get($order_info,'TeleNum01_200');
            $TeleCod01_200 = data_get($order_info,'TeleCod01_200');

            if (strlen($TeleCod02_200) > 9) {
                $hometel =  $this->replace_symbol($TeleCod02_200.$TeleNum02_200,"*",0,3);
                $phonetel = $this->replace_symbol($TeleNum01_200,"*",0,3);
            } else {
                $hometel = $this->replace_symbol($TeleCod01_200.$TeleNum01_200,"*",0,3);
                $phonetel = $this->replace_symbol($TeleNum02_200,"*",0,3);
            }

            $InstAddrName = data_get($order_info,'InstAddrName');

            $InstAddrName = $this->replace_symbol_address($InstAddrName,"*",3,3);

            // 各公司別的網址
            $homeUrl = 'https://www.homeplus.net.tw/';
            $homeUrlData = array(
                '209' => 'https://www.skydigital.com.tw/',
//                '210' => 'https://www.homeplus.net.tw/so/KL/so-news-1_15_26.html',
//                '220' => 'https://www.homeplus.net.tw/so/EL/so-news-1_15_40.html',
//                '230' => 'https://www.homeplus.net.tw/so/WD/so-news-1_15_60.html',
//                '240' => 'https://www.homeplus.net.tw/so/LG/so-news-1_15_49.html',
//                '250' => 'https://www.homeplus.net.tw/so/NVW/so-news-1_15_69.html',
//                '270' => 'https://www.homeplus.net.tw/so/GH/so-news-1_15_78.html',
//                '310' => 'https://www.homeplus.net.tw/so/T1/so-news-1_15_90.html',
//                '610' => 'https://www.homeplus.net.tw/so/TS/so-news-1_15_108.html',
//                '620' => 'https://www.homeplus.net.tw/so/SUN/so-news-1_15_99.html',
//                '720' => 'https://www.homeplus.net.tw/so/CL/so-news-1_15_117.html',
//                '730' => 'https://www.homeplus.net.tw/so/GD/so-news-1_15_126.html',
            );

            if (array_key_exists($so, $homeUrlData)) {
                $homeUrl = $homeUrlData[$so];
            }

            // 各公司別的服務電話

            $serviceNum = '412-8811';
            $numberData = array(
                '209' => '(02)2165-3123',
                '210' => '(02)2165-3152',
                '220' => '(02)2165-3153',
                '230' => '(02)2165-3156',
                '240' => '(02)2165-3157',
                '250' => '(02)2165-3688',
                '270' => '(02)2165-3366',
                '310' => '(02)412-8813',
                '610' => '(02)412-8812',
                '620' => '(02)412-8833',
                '720' => '(02)412-8801',
                '730' => '(02)412-8891',
            );


            if (array_key_exists($so, $numberData)) {
                $serviceNum = $numberData[$so];
            }

            $chkDeviceGet = substr($workSheet,'-1');
            if($chkDeviceGet == 'U') {
                data_set($order_info,'WorkKind','U 到宅取設備');
            }


            $data = array();

            if (in_array($order_info->WorkKind,['5 維修']))
            {
                /******維修單******/

                $page_maintain_data = array();


                $deviceCount = json_decode(data_get($order_info,'deviceCount'));
                $maintainHistory = json_decode(data_get($order_info,'maintainHistory'));
                $deviceSWVersion = json_decode(data_get($order_info,'deviceSWVersion'));

                $serviceNameStr =  implode(' ', $serviceName);


                $page_maintain_data['head_logo_img'] = $logo_url;
                $page_maintain_data['lineQrCode'] = (data_get($order_info,'CompanyNo') === '209')? '' : $linkQrCodeUrl;
                $page_maintain_data['head_title01'] = $company[$order_info->CompanyNo];
                if($companyNo == '209') $page_maintain_data['head_title01'] = '寶島聯網股份有限公司';
                $page_maintain_data['head_title02'] = $serviceNameStr;
                $page_maintain_data['head_tel'] = '(02)'.$serviceNum;
                $page_maintain_data['head_addres'] = $companyAddress[$order_info->CompanyNo];
                $page_maintain_data['head_homeURL'] = $homeUrl;
                $page_maintain_data['head_worksheet'] = $order_info->WorkSheet;
                $page_maintain_data['dstbIVR'] = $dstbIVR;

                $page_maintain_data['CustID'] = data_get($order_info,'CustID');
                $page_maintain_data['CustName'] = data_get($order_info,'CustName');
                $page_maintain_data['hometel'] = $hometel;
                $page_maintain_data['phonetel'] = $phonetel;
                $page_maintain_data['InstAddrName'] = $InstAddrName;
                $page_maintain_data['InstAddrName'] = $InstAddrName;
                $page_maintain_data['WorkCause'] = data_get($order_info,'WorkCause');

                $page_maintain_data['WorkSheet'] = data_get($order_info,'WorkSheet');
                $page_maintain_data['CustID'] = data_get($order_info,'CustID');

                $page_maintain_data['create_at'] = data_get($order_info,'create_at');
                $page_maintain_data['BookDate'] = data_get($order_info,'BookDate');
                $page_maintain_data['CreateName'] = data_get($order_info,'CreateName');
                $page_maintain_data['WorkTeam'] = data_get($order_info,'WorkTeam');
                $page_maintain_data['WorkerName'] = data_get($order_info,'WorkerName');

                $page_maintain_data['MSComment1'] = data_get($order_info,'MSComment1');

                $page_maintain_data['CMBAUDRATE'] = data_get($deviceCount,'CMBAUDRATE');
                $page_maintain_data['I_CNT'] = data_get($deviceCount,'I_CNT');
                $page_maintain_data['D_DUBLECNT'] = data_get($deviceCount,'D_DUBLECNT');
                $page_maintain_data['D_SINGLECNT'] = data_get($deviceCount,'D_SINGLECNT');
                $page_maintain_data['PVR_CNT'] = data_get($deviceCount,'PVR_CNT');

                $page_maintain_data['maintainHistory'] = $maintainHistory;


                $CMMODELNAME = '';
                $CMFACISNO = '';
                $DSTBMODELNAME = '';
                $DSTBFACISNO = '';
                foreach ($deviceSWVersion as $key => $SWVersion) {
                    if ($key =='2 CM') {
                        $CMMODELNAME = data_get($SWVersion,'MODELNAME');
                        $CMFACISNO = empty(data_get($SWVersion,'FACISNO'))? '' : data_get($SWVersion,'FACISNO');
                    } elseif ($key =='3 DSTB') {
                        $DSTBMODELNAME = data_get($SWVersion,'MODELNAME');
                        $DSTBFACISNO = empty(data_get($SWVersion,'FACISNO'))? '' : data_get($SWVersion,'FACISNO');
                    }
                }
                $page_maintain_data['CMMODELNAME'] = explode(' ',$CMMODELNAME);
                $page_maintain_data['CMFACISNO'] = explode(' ',$CMFACISNO);
                $page_maintain_data['DSTBMODELNAME'] = explode(' ',$DSTBMODELNAME);
                $page_maintain_data['DSTBFACISNO'] = explode(' ',$DSTBFACISNO);


                //維修簽名檔URL-客戶
                $mcustSignUrl = '';
                $signFile = "upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_mcust_".$workSheet.".jpg";
                if (file_exists(public_path($signFile)) && filesize(public_path($signFile)) > 0) {
                    $mcustSignUrl = public_path($signFile);
                }

                //維修簽名檔URL-工程人員
                $mengineeSignUrl = '';
                $signFile = "upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_mengineer_".$workSheet.".jpg";
                if (file_exists(public_path($signFile)) && filesize(public_path($signFile)) > 0) {
                    $mengineeSignUrl = public_path($signFile);
                }

                $page_maintain_data['mcustSignUrl'] = $mcustSignUrl;
                $page_maintain_data['mengineeSignUrl'] = $mengineeSignUrl;

                $sign_mcust_select = data_get($checkTWMBB,'sign_mcust_select');
                $page_maintain_data['sign_mcust_select'] = $sign_mcust_select;

                $page_maintain_data['head_no'] = '';

                $page_name = 'MAINTAIN';
                $page_list[] = $page_name;
                $data[$page_name] = $page_maintain_data;

                /******維修單 END******/

            }
            else if (in_array($order_info->WorkKind,['U 到宅取設備']))
            {
                $page_name = 'DEVICEGET';
                $page_data = array();

                $page_data['head_title01'] = $company[$order_info->CompanyNo];
                if($companyNo == '209') $page_data['head_title01'] = '寶島聯網股份有限公司';
                $page_data['head_logo_img'] = $logo_url;
                $page_data['lineQrCode'] = (data_get($order_info,'CompanyNo') === '209')? '' : $linkQrCodeUrl;
                $page_data['head_tel'] = $serviceNum;
                $page_data['head_addres'] = $companyAddress[$order_info->CompanyNo];
                $page_data['head_worksheet'] = $order_info->WorkSheet;
                $page_data['head_no'] = '';
                $page_data['head_homeURL'] = $homeUrl;
                $page_data['logo_url_fet'] = $logo_url_fet;

                $page_data['WorkKind'] = $order_info->WorkKind;
                $page_data['dstbIVR'] = $ivrAry;
                $page_data['WorkSheet'] = $order_info->WorkSheet;

                $page_data['CustID'] = $order_info->CustID;
                $page_data['CustName'] = $order_info->CustName;
                $page_data['hometel'] = $hometel;
                $page_data['phonetel'] = $phonetel;
                $page_data['InstAddrName'] = $InstAddrName;
                $page_data['InstAddrName'] = $InstAddrName;

                $page_data['WorkTeam'] = $order_info->WorkTeam;
                $page_data['NetID'] = $order_info->NetID;
                $page_data['SaleCampaign'] = $order_info->SaleCampaign;
                $page_data['CreateName'] = $order_info->CreateName;
                $page_data['CreateTime'] = $order_info->CreateTime;
                $page_data['BookDate'] = substr($order_info->BookDate,0,19);

                $page_data['create_at'] = substr($order_info->create_at,0,19);
                $page_data['WorkerName'] = $order_info->WorkerName;
                $page_data['MSComment1'] = $order_info->MSComment1;

                // 取回清單
                $getDeviceList = array();
                foreach($chargeBill as $k => $t) {
                    $pServiceName = data_get($t,'ServiceName');
                    $pChargeKind = data_get($t,'ChargeKind');
                    $pChargeName = data_get($t,'ChargeName');
                    $pBookingNo = data_get($t,'BookingNo');
                    $pSheetStatus = data_get($t,'SheetStatus');
                    $pSubsId = data_get($t,'SubsID');
                    $pOrgSingleSn = data_get($t,'OrgSingleSn');
                    if($pBookingNo == 'U 到宅取設備' && in_array($pChargeKind,['40','50'])) {
                        if($pChargeName == '00470 借用單') continue;
                        if($pSheetStatus == 'A.取消') continue;
                        $getDeviceList[] = array(
                            'subsId' => $pSubsId,
                            'serviceName' => $pServiceName,
                            'chargeName' => $pChargeName,
                            'orgSingleSn' => $pOrgSingleSn,
                        );
                    }
                }
                $page_data['deviceList'] = $getDeviceList;

                //簽名檔URL-客戶
                $mcustSignUrl = '';
                $signFile = "upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_mcust_".$workSheet.".jpg";
                if (file_exists(public_path($signFile)) && filesize(public_path($signFile)) > 0) {
                    $mcustSignUrl = $ewo_url.'/'.$signFile;
                }

                //簽名檔URL-工程人員
                $mengineeSignUrl = '';
                $signFile = "upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_mengineer_".$workSheet.".jpg";
                if (file_exists(public_path($signFile)) && filesize(public_path($signFile)) > 0) {
                    $mengineeSignUrl = $ewo_url.'/'.$signFile;
                }
                $page_data['mcustSignUrl'] = $mcustSignUrl;
                $page_data['mengineeSignUrl'] = $mengineeSignUrl;

                $data[$page_name] = $page_data;

                $page_list[] = $page_name;

            }
            else
            {

                if (in_array('1 CATV', $serviceName) || in_array('3 DSTB', $serviceName)) {

                    $page_catv_data = array();

                    $title = $company[$order_info->CompanyNo];
                    if($companyNo == '209') $title = '數位天空服務股份有限公司';
                    $page_catv_data['title'] = $title;

                    $page_catv_data['head_title01'] = $title;
                    $page_catv_data['head_title02'] = 'CATV有線電視/DTV數位電視加值服務申請書';
                    $page_catv_data['head_tel'] = $serviceNum;
                    $page_catv_data['head_addres'] = $companyAddress[$order_info->CompanyNo];
                    $page_catv_data['head_worksheet'] = $order_info->WorkSheet;
                    $page_catv_data['head_no'] = '';
                    $page_catv_data['head_homeURL'] = $homeUrl;
                    $page_catv_data['head_logo_img'] = $logo_url;
                    $page_catv_data['lineQrCode'] = (data_get($order_info,'CompanyNo') === '209')? '' : $linkQrCodeUrl;

                    $page_catv_data['WorkKind'] = $order_info->WorkKind;
                    $page_catv_data['dstbIVR'] = $ivrAry;
                    $page_catv_data['WorkSheet'] = $order_info->WorkSheet;

                    $page_catv_data['CustID'] = $order_info->CustID;
                    $page_catv_data['CustName'] = $order_info->CustName;
                    $page_catv_data['hometel'] = $hometel;
                    $page_catv_data['phonetel'] = $phonetel;
                    $page_catv_data['InstAddrName'] = $InstAddrName;
                    $page_catv_data['InstAddrName'] = $InstAddrName;


                    $page_catv_data['WorkTeam'] = $order_info->WorkTeam.'('.$order_info->WorkerName.')';
                    $page_catv_data['NetID'] = $order_info->NetID;
                    $page_catv_data['SaleCampaign'] = $order_info->SaleCampaign;
                    $page_catv_data['CreateName'] = $order_info->CreateName;
                    $page_catv_data['CreateTime'] = $order_info->CreateTime;
                    $page_catv_data['BookDate'] = $order_info->BookDate;


                    $chargeNameAry = array();
                    foreach ($charges as $key => $charge) {
                        if ($key != '3 DSTB' && $key != '1 CATV') {
                            continue;
                        }
                        foreach ($charge as $value) {
                            $chargeName = data_get($value,'ChargeName');

                            $chargeNameAry[] = $chargeName;
                        }

                    }
                    $page_catv_data['chargeNameAry'] = $chargeNameAry;

                    $chargeDateAry = array();
                    foreach ($charges as $key => $charge) {
                        if ($key != '3 DSTB' && $key != '1 CATV') {
                            continue;
                        }

                        foreach ($charge as $value) {
                            $fromDate = data_get($value,'FromDate');
                            $tillDate = data_get($value,'TillDate');

                            if (!empty($fromDate) && !empty($tillDate)) {
                                $chargeDate = date("Y-m-d",strtotime($fromDate)) .' ~ '.date("Y-m-d",strtotime($tillDate));
                            } elseif (!empty($fromDate)) {
                                $chargeDate = date("Y-m-d",strtotime($fromDate));
                            } else {
                                $chargeDate = '~';
                            }

                            $chargeDateAry[] = $chargeDate;
                        }
                    }
                    $page_catv_data['chargeDateAry'] = $chargeDateAry;

                    $recvAmt = 0;
                    $billAmtAry = array();
                    foreach ($charges as $key => $charge) {
                        if ($key != '3 DSTB' && $key != '1 CATV') {
                            continue;
                        }

                        foreach ($charge as $value) {
                            $billAmt = data_get($value,'BillAmt');

                            if (empty($billAmt)) {
                                $billAmt=0;
                            }
                            $billAmtAry[] = (int)$billAmt;
                            $recvAmt +=(int)$billAmt;
                        }

                    }
                    $page_catv_data['billAmtAry'] = $billAmtAry;
                    $page_catv_data['recvAmt'] = $recvAmt;

                    $page_catv_data['WorkerName'] = $order_info->WorkerName;

                    $page_catv_data['MSComment1'] = $order_info->MSComment1;
                    $page_catv_data['MSContract'] = data_get($order_info,'MSContract');;
                    $page_catv_data['checkId'] = empty(data_get($checkDSTB,'dstb_check_id'))? '' : 'checked';
                    $page_catv_data['checkHealth'] = empty(data_get($checkDSTB,'dstb_check_health'))? '' : 'checked';
                    $page_catv_data['checkDriver'] = empty(data_get($checkDSTB,'dstb_check_driver'))? '' : 'checked';
                    $page_catv_data['checkCompany'] = empty(data_get($checkDSTB,'dstb_check_company'))? '' : 'checked';
                    $page_catv_data['checkOther'] = empty(data_get($checkDSTB,'dstb_check_other'))? '' : 'checked';
                    $page_catv_data['checkDriverRem'] = data_get($checkDSTB,'dstb_check_driver_desc');
                    $page_catv_data['checkOtherRem'] = data_get($checkDSTB,'dstb_check_other_desc');

                    $signFile = 'upload/'.$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_dstb_".$workSheet.".jpg";
                    $dstbSignUrl = '';
                    if (file_exists(public_path($signFile)) && filesize(public_path($signFile)) > 0) {
                        $dstbSignUrl = public_path($signFile);
                    }

                    $page_catv_data['signImage'] = $dstbSignUrl;
                    $page_catv_data['checkLegal'] = empty(data_get($checkDSTB,'dstb_check_legal'))? '' : data_get($checkDSTB,'dstb_check_legal');
                    $checkPersonal = data_get($checkDSTB,'dstb_check_personal');
                    $page_catv_data['checkPersonalon'] = ($checkPersonal === 'on')? 'checked' : '';
                    $page_catv_data['checkPersonaloff'] = ($checkPersonal === 'off')? 'checked' : '';
                    $page_catv_data['checkTitle'] = data_get($checkDSTB,'dstb_check_title') ?? '無';


                    $page_name = 'CATV';
                    $page_list[] = $page_name;
                    $data[$page_name] = $page_catv_data;

                }


                if (in_array('D TWMBB', $serviceName)) {

                    $page_twmbb_data = array();

                    $page_twmbb_data['head_title01'] = $company[$companyNo];
                    if($companyNo == '209') $page_twmbb_data['head_title01'] = '寶島聯網股份有限公司';
                    $page_twmbb_data['head_title02'] = '光纖寬頻網路服務申請書';
                    $page_twmbb_data['head_logo_img'] = $logo_url;
                    $page_twmbb_data['lineQrCode'] = (data_get($order_info,'CompanyNo') === '209')? '' : $linkQrCodeUrl;
                    $page_twmbb_data['head_tel'] = $serviceNum;
                    $page_twmbb_data['head_addres'] = $companyAddress[$order_info->CompanyNo];
                    $page_twmbb_data['head_worksheet'] = $order_info->WorkSheet;
                    $page_twmbb_data['head_no'] = '';
                    $page_twmbb_data['head_homeURL'] = $homeUrl;
                    $page_twmbb_data['logo_url_fet'] = $logo_url_fet;

                    $page_twmbb_data['WorkKind'] = $order_info->WorkKind;
                    $page_twmbb_data['dstbIVR'] = $ivrAry;
                    $page_twmbb_data['WorkSheet'] = $order_info->WorkSheet;

                    $page_twmbb_data['CustID'] = $order_info->CustID;
                    $page_twmbb_data['CustName'] = $order_info->CustName;
                    $page_twmbb_data['hometel'] = $hometel;
                    $page_twmbb_data['phonetel'] = $phonetel;
                    $page_twmbb_data['InstAddrName'] = $InstAddrName;
                    $page_twmbb_data['InstAddrName'] = $InstAddrName;

                    $page_twmbb_data['WorkTeam'] = $order_info->WorkTeam.'('.$order_info->WorkerName.')';
                    $page_twmbb_data['NetID'] = $order_info->NetID;
                    $page_twmbb_data['SaleCampaign'] = $order_info->SaleCampaign;
                    $page_twmbb_data['CreateName'] = $order_info->CreateName;
                    $page_twmbb_data['CreateTime'] = $order_info->CreateTime;
                    $page_twmbb_data['BookDate'] = $order_info->BookDate;

                    $chargeNameAry = array();
                    foreach ($charges as $key => $charge) {

                        if ($key != 'D TWMBB') {
                            continue;
                        }

                        foreach ($charge as $value) {
                            if($BrokerKind == '')
                                $BrokerKind = data_get($value,'BrokerKind');

                            $chargeName = data_get($value,'ChargeName');
                            $chargeNameAry[] = $chargeName;
                        }
                    }
                    $page_twmbb_data['chargeNameAry'] = $chargeNameAry;

                    $chargeDateAry = array();
                    foreach ($charges as $key => $charge) {

                        if ($key != 'D TWMBB') {
                            continue;
                        }

                        foreach ($charge as $value) {
                            $fromDate = data_get($value,'FromDate');
                            $tillDate = data_get($value,'TillDate');

                            if (!empty($fromDate) && !empty($tillDate)) {
                                $chargeDate = date("Y-m-d",strtotime($fromDate)) .' ~ '.date("Y-m-d",strtotime($tillDate));
                            } elseif (!empty($fromDate)) {
                                $chargeDate = date("Y-m-d",strtotime($fromDate));
                            } else {
                                $chargeDate = '~';
                            }

                            $chargeDateAry[] = $chargeDate;
                        }

                    }
                    $page_twmbb_data['chargeDateAry'] = $chargeDateAry;


                    $billAmtAry = array();
                    $twmbbAmt = 0;
                    foreach ($charges as $key => $charge) {

                        if ($key != 'D TWMBB') {
                            continue;
                        }

                        foreach ($charge as $value) {
                            $billAmt = data_get($value,'BillAmt');
                            if (empty($billAmt)) {
                                $billAmt=0;
                            }
                            $billAmtAry[] = (int)$billAmt;

                            $twmbbAmt +=(int)$billAmt;
                        }

                    }
                    $page_twmbb_data['billAmtAry'] = $billAmtAry;

                    $page_twmbb_data['recvAmt'] = $twmbbAmt;

                    $page_twmbb_data['WorkerName'] = $order_info->WorkerName;

                    $page_twmbb_data['MSComment1'] = $order_info->MSComment1;
                    $page_twmbb_data['MSContract'] = $order_info->MSContract2;
                    $page_twmbb_data['checkId'] = empty(data_get($checkTWMBB,'twmbb_check_id'))? '' : 'checked';
                    $page_twmbb_data['checkHealth'] = empty(data_get($checkTWMBB,'twmbb_check_health'))? '' : 'checked';
                    $page_twmbb_data['checkDriver'] = empty(data_get($checkTWMBB,'twmbb_check_driver'))? '' : 'checked';
                    $page_twmbb_data['checkCompany'] = empty(data_get($checkTWMBB,'twmbb_check_company'))? '' : 'checked';
                    $page_twmbb_data['checkOther'] = empty(data_get($checkTWMBB,'twmbb_check_other'))? '' : 'checked';
                    $page_twmbb_data['checkDriverRem'] = data_get($checkTWMBB,'twmbb_check_driver_desc');
                    $page_twmbb_data['checkOtherRem'] = data_get($checkTWMBB,'twmbb_check_other_desc');


                    $signFile = 'upload/'.$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_twmbb_".$workSheet.".jpg";
                    $SignUrl = '';
                    if (file_exists(public_path($signFile)) && filesize(public_path($signFile)) > 0) {
                        $SignUrl = public_path($signFile);
                    }
                    $checkLegal = empty(data_get($checkTWMBB,'twmbb_check_legal'))? '' : data_get($checkTWMBB,'twmbb_check_legal');

                    $page_twmbb_data['signImage'] = $SignUrl;
                    $page_twmbb_data['checkLegal'] = $checkLegal;
                    $page_twmbb_data['checkPersonal'] = empty(data_get($checkTWMBB,'twmbb_check_personal'))? '' : 'checked';
                    $page_twmbb_data['checkTitle'] = data_get($checkTWMBB,'twmbb_check_title') ?? '無';

                    $page_twmbb_data['checkEquipment'] = empty(data_get($checkTWMBB,'twmbb_check_equipment'))? '' : 'checked';
                    $page_twmbb_data['check_domicile'] = empty(data_get($checkTWMBB,'twmbb_check_domicile'))? '' : 'checked';
                    $page_twmbb_data['check_notest'] = empty(data_get($checkTWMBB,'twmbb_check_notest'))? '' : 'checked';
                    $page_twmbb_data['check_standalone'] = empty(data_get($checkTWMBB,'twmbb_check_standalone'))? '' : 'checked';
                    $page_twmbb_data['check_standalone_desc'] = data_get($checkTWMBB,'twmbb_check_standalone_desc');
                    $page_twmbb_data['check_notest_standalone'] = empty(data_get($checkTWMBB,'twmbb_check_notest_standalone'))? '' : 'checked';
                    $page_twmbb_data['check_notest_standalone_desc'] = data_get($checkTWMBB,'twmbb_check_notest_standalone_desc');
                    $page_twmbb_data['check_equipmentdiscord_test'] = empty(data_get($checkTWMBB,'twmbb_check_equipmentdiscord_test'))? '' : 'checked';
                    $page_twmbb_data['check_equipmentdiscord_test_desc'] = data_get($checkTWMBB,'twmbb_check_equipmentdiscord_test_desc');

                    $page_name = 'TWMBB';

                    $p_page_name = '';
                    if($BrokerKind === '789 遠傳') {
                        $companyNo = data_get($order_info,'CompanyNo');
                        $companyNoStr = config("company.database.$companyNo");
                        $page_twmbb_data['head_title01'] = ($companyNo == '209') ? '寶島聯網股份有限公司' : "中嘉寬頻/$companyNoStr";
                        $page_twmbb_data['head_title02'] = '遠傳大雙網方案 派工/竣工單/收據';
                        $page_twmbb_data['head_tel'] = '遠傳免付費手機直撥123；市話付費專線449-5000';
                        $p_page_name = 'TWMBB_789';

                    }

                    $data[$page_name] = $page_twmbb_data;

                    $page_name = $p_page_name? $p_page_name : $page_name;
                    $page_list[] = $page_name;


                }


                // PDF_V2_CM
                if (in_array('2 CM', $serviceName) || in_array('C HS', $serviceName) || in_array('B FTTH', $serviceName) || in_array('5 FTTB', $serviceName)) {

                    $page_cm_data = array();
                    $chkServiceName = array('2 CM', 'C HS', 'B FTTH','5 FTTB');
                    $serviceName01Str = '';
                    if(in_array('2 CM', $serviceName))
                        $serviceName01Str = 'CM';
                    elseif(in_array('C HS', $serviceName))
                        $serviceName01Str = 'HS';
                    elseif(in_array('B FTTH', $serviceName))
                        $serviceName01Str = 'FTTH';
                    elseif(in_array('5 FTTB', $serviceName))
                        $serviceName01Str = 'FTTB';

                    $page_cm_data['head_title01'] = ($companyNo == '209') ? '寶島聯網股份有限公司' : "中嘉寬頻股份有限公司";
                    $page_cm_data['head_title02'] = "光纖寬頻網路服務申請書";
                    $page_cm_data['head_logo_img'] = $logo_url;
                    $page_cm_data['lineQrCode'] = (data_get($order_info,'CompanyNo') === '209')? '' : $linkQrCodeUrl;
                    $page_cm_data['head_tel'] = $serviceNum;
                    $page_cm_data['head_addres'] = $companyAddress[$order_info->CompanyNo];
                    $page_cm_data['head_worksheet'] = $order_info->WorkSheet;
                    $page_cm_data['head_no'] = '';
                    $page_cm_data['head_homeURL'] = $homeUrl;

                    $page_cm_data['WorkKind'] = $order_info->WorkKind;
                    $page_cm_data['dstbIVR'] = $ivrAry;
                    $page_cm_data['WorkSheet'] = $order_info->WorkSheet;

                    $page_cm_data['CustID'] = $order_info->CustID;
                    $page_cm_data['CustName'] = $order_info->CustName;
                    $page_cm_data['hometel'] = $hometel;
                    $page_cm_data['phonetel'] = $phonetel;
                    $page_cm_data['InstAddrName'] = $InstAddrName;
                    $page_cm_data['InstAddrName'] = $InstAddrName;

                    $page_cm_data['WorkTeam'] = $order_info->WorkTeam.'('.$order_info->WorkerName.')';
                    $page_cm_data['NetID'] = $order_info->NetID;
                    $page_cm_data['SaleCampaign'] = $order_info->SaleCampaign;
                    $page_cm_data['CreateName'] = $order_info->CreateName;
                    $page_cm_data['CreateTime'] = $order_info->CreateTime;
                    $page_cm_data['BookDate'] = $order_info->BookDate;
                    $page_cm_data['CompanyNo'] = $order_info->CompanyNo;

                    $chargeNameAry = array();
                    foreach ($charges as $key => $charge) {

                        if(!in_array($key,$chkServiceName)) {
                            continue;
                        }

                        foreach ($charge as $value) {
                            $chargeName = data_get($value,'ChargeName');
                            $chargeNameAry[] = $chargeName;
                        }
                    }
                    $page_cm_data['chargeNameAry'] = $chargeNameAry;


                    $chargeDateAry = array();
                    foreach ($charges as $key => $charge) {

                        if(!in_array($key,$chkServiceName)) {
                            continue;
                        }

                        foreach ($charge as $value) {
                            $fromDate = data_get($value,'FromDate');
                            $tillDate = data_get($value,'TillDate');
                            $chargeDate = '';

                            if (!empty($fromDate) && !empty($tillDate)) {
                                $chargeDate = date("Y-m-d",strtotime($fromDate)) .' ~ '.date("Y-m-d",strtotime($tillDate));
                            } elseif (!empty($fromDate)) {
                                $chargeDate = date("Y-m-d",strtotime($fromDate));
                            } else {
                                $chargeDate = '~';
                            }

                            $chargeDateAry[] = $chargeDate;
                        }

                    }
                    $page_cm_data['chargeDateAry'] = $chargeDateAry;


                    $billAmtAry = array();
                    $cmAmt = 0;
                    foreach ($charges as $key => $charge) {

                        if(!in_array($key,$chkServiceName)) {
                            continue;
                        }

                        foreach ($charge as $value) {
                            $billAmt = data_get($value,'BillAmt');
                            if (empty($billAmt)) {
                                $billAmt=0;
                            }
                            $billAmtAry[] = (int)$billAmt;

                            $cmAmt +=(int)$billAmt;
                        }

                    }
                    $page_cm_data['billAmtAry'] = $billAmtAry;

                    $page_cm_data['recvAmt'] = $cmAmt;

                    $page_cm_data['WorkerName'] = $order_info->WorkerName;

                    $page_cm_data['MSComment1'] = $order_info->MSComment1;
                    $page_cm_data['MSContract'] = $order_info->MSContract2;
                    $page_cm_data['checkId'] = empty(data_get($checkCM,'cm_check_id'))? '' : 'checked';
                    $page_cm_data['checkHealth'] = empty(data_get($checkCM,'cm_check_health'))? '' : 'checked';
                    $page_cm_data['checkDriver'] = empty(data_get($checkCM,'cm_check_driver'))? '' : 'checked';
                    $page_cm_data['checkCompany'] = empty(data_get($checkCM,'cm_check_company'))? '' : 'checked';
                    $page_cm_data['checkOther'] = empty(data_get($checkCM,'cm_check_other'))? '' : 'checked';
                    $page_cm_data['checkDriverRem'] = data_get($checkCM,'cm_check_driver_desc');
                    $page_cm_data['checkOtherRem'] = data_get($checkCM,'cm_check_other_desc');


                    $signFile = 'upload/'.$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_cm_".$workSheet.".jpg";
                    $SignUrl = '';
                    if (file_exists(public_path($signFile)) && filesize(public_path($signFile)) > 0) {
                        $SignUrl = public_path($signFile);
                    }
                    $checkLegal = empty(data_get($checkCM,'cm_check_legal'))? '' : data_get($checkCM,'cm_check_legal');

                    $page_cm_data['signImage'] = $SignUrl;
                    $page_cm_data['checkLegal'] = $checkLegal;
                    $page_cm_data['checkPersonal'] = empty(data_get($checkCM,'cm_check_personal'))? '' : 'checked';
                    $page_cm_data['checkTitle'] = data_get($checkCM,'cm_check_title') ?? '無';

                    $page_cm_data['checkEquipment'] = empty(data_get($checkCM,'cm_check_equipment'))? '' : 'checked';
                    $page_cm_data['check_domicile'] = empty(data_get($checkCM,'cm_check_domicile'))? '' : 'checked';
                    $page_cm_data['check_notest'] = empty(data_get($checkCM,'cm_check_notest'))? '' : 'checked';
                    $page_cm_data['check_standalone'] = empty(data_get($checkCM,'cm_check_standalone'))? '' : 'checked';
                    $page_cm_data['check_standalone_desc'] = data_get($checkCM,'cm_check_standalone_desc');
                    $page_cm_data['check_notest_standalone'] = empty(data_get($checkCM,'cm_check_notest_standalone'))? '' : 'checked';
                    $page_cm_data['check_notest_standalone_desc'] = data_get($checkCM,'cm_check_notest_standalone_desc');
                    $page_cm_data['check_equipmentdiscord_test'] = empty(data_get($checkCM,'cm_check_equipmentdiscord_test'))? '' : 'checked';
                    $page_cm_data['check_equipmentdiscord_test_desc'] = data_get($checkCM,'cm_check_equipmentdiscord_test_desc');

                    $page_cm_data['saleap'] = empty(data_get($order_info,'saleAP'))? '無' : data_get($order_info,'saleAP');

                    $page_name = 'CM';

                    if(in_array('B FTTH', $serviceName)) {
                        $page_name = 'FTTH';
                    }
                    if(in_array('5 FTTB', $serviceName)) {
                        $page_name = 'FTTB';
                    }

                    $page_list[] = $page_name;

                    $data[$page_name] = $page_cm_data;
                }

                // 條款
                $page_list[] = 'TERMS';

            }

            // 借用/取回 保管單 custody.blade.php
            if(true)
            {
                $page_IO_data = array();

                $page_IO_data['CustID'] = data_get($order_info,'CustID');
                $page_IO_data['WorkSheet'] = data_get($order_info,'WorkSheet');
                $page_IO_data['company'] = $company[$order_info->CompanyNo];
                if($companyNo == '209') $page_IO_data['company'] = '寶島聯網股份有限公司';
                $page_IO_data['CustName'] = data_get($order_info,'CustName');
                $page_IO_data['InstAddrName'] = $InstAddrName;

                $WorkKindType = '';
                if(in_array($order_info->WorkKind,array('1 裝機','2 復機','6 移機','8 工程收費','9 停後復機','A 加裝','C 換機')) === true)
                    $WorkKindType = '裝機';
                elseif(in_array($order_info->WorkKind,array('3 拆機','4 停機','7 移拆','H 退拆設備','I 退拆分機','K 退次週期項')) === true)
                    $WorkKindType = '拆機';
                else
                    $WorkKindType = '維修';
                $page_IO_data['WorkKindType'] = $WorkKindType;

                $page_IO_data['Cable_modem_port'] = data_get($borrowmingList,'Cable_modem_port');
                $page_IO_data['Cable_modem_two_way'] = data_get($borrowmingList,'Cable_modem_two_way');
                $page_IO_data['Basic_digital_set_top_box'] = data_get($borrowmingList,'Basic_digital_set_top_box');
                $page_IO_data['Digital_set_top_box_two_way_type'] = data_get($borrowmingList,'Digital_set_top_box_two_way_type');
                $page_IO_data['camera'] = data_get($borrowmingList,'camera');
                $page_IO_data['Door_and_window_sensor'] = data_get($borrowmingList,'Door_and_window_sensor');
                $page_IO_data['Smoke_detector'] = data_get($borrowmingList,'Smoke_detector');

                $page_IO_data['get_Cable_modem_port'] = data_get($retrieveList,'get_Cable_modem_port');
                $page_IO_data['get_Cable_modem_two_way'] = data_get($retrieveList,'get_Cable_modem_two_way');
                $page_IO_data['get_Basic_digital_set_top_box'] = data_get($retrieveList,'get_Basic_digital_set_top_box');
                $page_IO_data['get_Digital_set_top_box_two_way_type'] = data_get($retrieveList,'get_Digital_set_top_box_two_way_type');
                $page_IO_data['get_camera'] = data_get($retrieveList,'get_camera');
                $page_IO_data['get_Door_and_window_sensor'] = data_get($retrieveList,'get_Door_and_window_sensor');
                $page_IO_data['get_Smoke_detector'] = data_get($retrieveList,'get_Smoke_detector');

                $page_IO_data['Cable_accessories_wireless_anti_frequency_sharing_device'] = data_get($borrowmingList,'Cable_accessories_wireless_anti_frequency_sharing_device');
                $page_IO_data['Cable_accessories_transformer_power_cord'] = data_get($borrowmingList,'Cable_accessories_transformer_power_cord');
                $page_IO_data['Cable_accessories_Ethernet_cable'] = data_get($borrowmingList,'Cable_accessories_Ethernet_cable');
                $page_IO_data['Cable_accessories_USB_wireless_anti_frequency_network_card'] = data_get($borrowmingList,'Cable_accessories_USB_wireless_anti_frequency_network_card');

                $page_IO_data['get_Cable_accessories_wireless_anti_frequency_sharing_device'] = data_get($retrieveList,'get_Cable_accessories_wireless_anti_frequency_sharing_device');
                $page_IO_data['get_Cable_accessories_transformer_power_cord'] = data_get($retrieveList,'get_Cable_accessories_transformer_power_cord');
                $page_IO_data['get_Cable_accessories_Ethernet_cable'] = data_get($retrieveList,'get_Cable_accessories_Ethernet_cable');
                $page_IO_data['get_Cable_accessories_USB_wireless_anti_frequency_network_card'] = data_get($retrieveList,'get_Cable_accessories_USB_wireless_anti_frequency_network_card');

                $page_IO_data['Set_top_box_accessories_remote_control'] = data_get($borrowmingList,'Set_top_box_accessories_remote_control');
                $page_IO_data['Set_top_box_accessories_HDI'] = data_get($borrowmingList,'Set_top_box_accessories_HDI');
                $page_IO_data['Set_top_box_accessories_AV_cable'] = data_get($borrowmingList,'Set_top_box_accessories_AV_cable');
                $page_IO_data['Set_top_box_accessories_Chromatic_aberration_line'] = data_get($borrowmingList,'Set_top_box_accessories_Chromatic_aberration_line');
                $page_IO_data['Set_top_box_accessories_transformer_power_cord'] = data_get($borrowmingList,'Set_top_box_accessories_transformer_power_cord');

                $page_IO_data['get_Set_top_box_accessories_remote_control'] = data_get($retrieveList,'get_Set_top_box_accessories_remote_control');
                $page_IO_data['get_Set_top_box_accessories_HDI'] = data_get($retrieveList,'get_Set_top_box_accessories_HDI');
                $page_IO_data['get_Set_top_box_accessories_AV_cable'] = data_get($retrieveList,'get_Set_top_box_accessories_AV_cable');
                $page_IO_data['get_Set_top_box_accessories_Chromatic_aberration_line'] = data_get($retrieveList,'get_Set_top_box_accessories_Chromatic_aberration_line');
                $page_IO_data['get_Set_top_box_accessories_transformer_power_cord'] = data_get($retrieveList,'get_Set_top_box_accessories_transformer_power_cord');

                $page_IO_data['Set_top_box_accessories_smart_card'] = data_get($borrowmingList,'Set_top_box_accessories_smart_card');
                $page_IO_data['Set_top_box_accessories_external_hard_disk'] = data_get($borrowmingList,'Set_top_box_accessories_external_hard_disk');
                $page_IO_data['Set_top_box_accessories_USB_wireless_anti_frequency_network_card'] = data_get($borrowmingList,'Set_top_box_accessories_USB_wireless_anti_frequency_network_card');
                $page_IO_data['Set_top_box_accessories_ATV_set_top_box'] = data_get($borrowmingList,'Set_top_box_accessories_ATV_set_top_box');
                $page_IO_data['Set_top_box_accessories_Bluetooth_remote_control'] = data_get($borrowmingList,'Set_top_box_accessories_Bluetooth_remote_control');

                $page_IO_data['get_Set_top_box_accessories_smart_card'] = data_get($retrieveList,'get_Set_top_box_accessories_smart_card');
                $page_IO_data['get_Set_top_box_accessories_external_hard_disk'] = data_get($retrieveList,'get_Set_top_box_accessories_external_hard_disk');
                $page_IO_data['get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card'] = data_get($retrieveList,'get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card');
                $page_IO_data['get_Set_top_box_accessories_ATV_set_top_box'] = data_get($retrieveList,'get_Set_top_box_accessories_ATV_set_top_box');
                $page_IO_data['get_Set_top_box_accessories_Bluetooth_remote_control'] = data_get($retrieveList,'get_Set_top_box_accessories_Bluetooth_remote_control');

                $page_IO_data['Smart_home_accessories_transformer_power_cord'] = data_get($borrowmingList,'Smart_home_accessories_transformer_power_cord');
                $page_IO_data['get_Smart_home_accessories_transformer_power_cord'] = data_get($retrieveList,'get_Smart_home_accessories_transformer_power_cord');
                $page_IO_data['HP_M4_Mesh'] = data_get($borrowmingList,'HP_M4_Mesh');
                $page_IO_data['get_HP_M4_Mesh'] = data_get($retrieveList,'get_HP_M4_Mesh');

                $page_IO_data['Fiber_Modem_HomeOnt'] = data_get($borrowmingList,'Fiber_Modem_HomeOnt');
                $page_IO_data['get_Fiber_Modem_HomeOnt'] = data_get($retrieveList,'get_Fiber_Modem_HomeOnt');

                $page_name = 'CUSTODY';

                $page_list[] = $page_name;

                $data[$page_name] = $page_IO_data;

            }

            // domPDF
            if(TRUE) {
//                Log::channel('ewoLog')->info("chk $orderId page_list==".print_r($page_list,1));
//                Log::channel('ewoLog')->info("chk $orderId data==".print_r($data,1));

                $companyNo = data_get($order_info,'CompanyNo');
                $data['comapnyno'] = $companyNo;
                if($BrokerKind === '789 遠傳') {
                    $data['comapnyno'] = 'fet';
                }

                $domPDF = domPDF::loadView('pdf.PDFV2', compact('data', 'page_list'));

                $domPDF->setPaper('A4', 'landscape');

                if(10) {
                    $domPDF->setOptions(['adminPassword' => '','isRemoteEnabled'=>true]);

                    $fileName = $workSheet.'.pdf';
                    $fileNameHead = $workSheet.'_head.pdf';
                    $custid = data_get($order_info,'CustID');
                    $bookdate = data_get($order_info,'BookDate');
                    $bookdateStr = date('Ymd',strtotime($bookdate));

                    $directory = public_path("upload/$custid"."_$bookdateStr");
                    if (!is_dir($directory)) {
                        mkdir($directory,0777,true);
                        chmod($directory,0777);
                    }
                    $domPDF->save("$directory/$fileNameHead");

                } else {
                    //預覽PDF
                    return $domPDF->stream();

                }
            }

            // PDF 合併[條款]
            if(true) {
                $workFile = "$directory/$fileNameHead";
                $saveFile = "$directory/$fileName";
                $password = '0000';

                // TermsFile
                $dtvTermsFile = '';
                $dtvPCLTermsFile = '';
                $cmTermsFile = '';
                $cmPCLTermsFile = '';

//                $dtvTermsFile = public_path("pdfTerms/$companyNo.pdf");
//                $cmTermsFile = public_path("pdfTerms/$companyNo.pdf");
                // 條款目錄
//                if(date('Ymd') >= '20230213')
                    $forder02 = "pdfTerms/$pdfTerms";
//                else
//                    $forder02 = "pdfTerms";

                if(in_array('3 DSTB',$serviceName)) {
                    $dtvTermsFile = public_path("$forder02/dtv$companyNo.pdf");
                    $dtvPCLTermsFile = ($companyNo == '209') ?
                        public_path("$forder02/dtv_pcl_209.pdf") :
                        public_path("$forder02/dtv_pcl.pdf");
                }
                $chkCM = '';
                foreach (['2 CM', 'C HS', 'B FTTH', '5 FTTB', 'D TWMBB'] as $t) {
                    if(empty($chkCM) && in_array($t,$serviceName)) {
                        $chkCM = 'Y';
                    }
                }
                if($chkCM == 'Y') {
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
                $command .= ' --output-path "'.$saveFile.'"';
                $command .= ' --password "'.$password.'"';

                $output = shell_exec($command);
                unlink($workFile);
                if(!empty($output)) {
                    throw new Exception("PDF合併失敗[command=$command]".print_r($output,1),'0590');
                }
            }

            $p_time_end = microtime(true);
            $p_run_time = $p_time_end - $p_time_start;
            $run = array(
                'title' => $p_run_title,
                'm_start' => $p_time_start,
                'm_end' => $p_time_end,
                'run_time' => $p_run_time,
            );

            $p_data = array(
                'code' => '0000',
                'status' => 'OK',
                'meg' => '',
                'data' => "$directory/$fileName",
                'run' => json_encode($run),
                'date' => date('Y-m-d H:i:s')
            );




        } catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $p_data = array(
                'code' => '0400',
                'status' => 'error',
                'meg' => '資料錯誤='.$e->getMessage(),
                'data' => '',
                'date' => date('Y-m-d H:i:s')
            );

        }


        return $p_data;

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
                $workSheet = data_get($charge,'WorkSheet');
                $RecvNo = data_get($charge,'RecvNo');
                $WorkRecvYN = data_get($charge,'WorkRecvYN');

                if ($workSheet != $RecvNo && $WorkRecvYN != 'Y') {
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
            $data[$serviceName][] = $charge;
        }

        $ret = $data;

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


}
