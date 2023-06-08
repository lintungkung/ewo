<?php

namespace App\Api\Controllers;

use App\Repositories\Log\LogRepository;
use Validator;
use \Log;
use Exception;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Api\Controllers\Controller;
use App\Http\Controllers\MyException;

use \App\Http\Controllers\Ewo_EventController;
use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Reason\ReasonRepository;
use App\Repositories\Order\OrderRepository;


class PDF_v1 extends Controller
{

    public function __construct(OrderRepository $OrderRepository)
    {
        $this->OrderRepository = $OrderRepository;
    }

    public function createPDF($source,$version,$orderId)
    {

        $pdf_info = null;
        // if ($source == 'app') {

        set_time_limit(300);
        $p_time_start = microtime(true);
        $p_run_title = "PDF_$version"."_ID_$orderId";


        // } elseif ($source == 'web') {
        //     $pdf_info = $this->OrderRepository->getOrderPdfInfo($orderId);
        //     $order_info = json_decode(data_get($pdf_info,'Data'));
        // }

        $pdf_info = $this->OrderRepository->getOrderPdfInfo($orderId);

        if (!$pdf_info) {
            $order_info = $this->OrderRepository->getOrderInfoById($orderId);

            $so = data_get($order_info,'CompanyNo');
            $worksheet = data_get($order_info,'WorkSheet');
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

            $pdfData = array(
                'CompanyNo' => $so,
                'WorkSheet' => $worksheet,
                'finsh' => $finish,
                'AssignSheet' => $assignSheet,
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
            );

            $insertData = array(
                'CompanyNo' => $so,
                'WorkSheet' => $worksheet,
                'Version' => $version,
                'AssegnUser' => 'app',
                'Data' => json_encode($pdfData),
                'orderListId' => $orderId
            );

            $this->OrderRepository->insertOrderPdfInfo($insertData);
        } else {
            $order_info = json_decode(data_get($pdf_info,'Data'));
        }


        // $order_info = $this->OrderRepository->getOrderInfoById($orderId);
        // $chargeCMInfo = json_decode($order_info->ChargeDetailCM);
        // $chargeCATVInfo = json_decode($order_info->ChargeDetailCATV);
        // $chargeTWMBBInfo = json_decode($order_info->ChargeDetailTWMBB);

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
            $retrieveList = data_get($order_info,'cmchRetrieveListeck');
        }

        // $checkDSTB = json_decode($order_info->dstbcheck) ?? null;
        // $checkCM = json_decode($order_info->cmcheck) ?? null;
        // $checkTWMBB = json_decode($order_info->twmbbcheck) ?? null;
        // $borrowmingList = json_decode($order_info->BorrowmingList) ?? null;
        // $retrieveList = json_decode($order_info->RetrieveList) ?? null;



        $so = data_get($order_info,'CompanyNo');
        $worksheet = data_get($order_info,'WorkSheet');
        $finish = data_get($order_info,'finsh');
        $zj_finsh = data_get($order_info, "zj_finsh");
        $assignSheet = data_get($order_info,'AssignSheet');
        $assignSheet = explode(",", $assignSheet);
        $assignSheet[] = $worksheet;
        $assignSheet = array_filter($assignSheet);
        $assignSheet = array_values($assignSheet);



        //順收金額
        $bill = array();
        foreach ($assignSheet as $key => $value) {
            $chargeData = array(
                'so' => $so,
                'worksheet' => $value,
            );

            $chargeBill = $this->OrderRepository->getOrderCharge($chargeData);

            $itemOrder = $this->OrderRepository->getOrderWorkKind($chargeData);
            $itemWorkKind = data_get($itemOrder,'WorkKind');


            $itemStatus = false;
            foreach ($chargeBill as $key => $itemBill) {
                $SheetStatus = data_get($itemBill,'SheetStatus');
                $status = substr($SheetStatus, 0,1);

                if ($SheetStatus == 'A.取消') {
                    continue;
//                } else if ($status >= '4' && $itemWorkKind != 'C 換機') {
//                    $itemStatus = true;
//                    continue;
                }

                $bill[] = $itemBill;
            }

            // $orderBill = array();
            // if ($itemStatus) {
                $orderBill = $this->OrderRepository->getOrderBill($chargeData);
            // }

            foreach ($orderBill as $key => $itemBill) {
                data_set($itemBill, 'DB', '3200');
                $bill[] = $itemBill;
            }

        }

        $charges = $this->classifyCharge($bill);

        $serviceName = array_keys($charges);

        $showBill = 0;

        // if (gettype($order_info->ServiceName) == 'string') {
        //     $serviceName = json_decode($order_info->ServiceName);
        // } else {
        //     $serviceName = data_get($order_info,'ServiceName');
        // }

        if (in_array('1 CATV', $serviceName) || in_array('3 DSTB', $serviceName)) {
            $showBill = 1;
        } elseif (in_array('2 CM', $serviceName) || in_array('C HS', $serviceName)) {
            $showBill = 2;
        } elseif (in_array('D TWMBB', $serviceName)) {
            $showBill = 3;
        }

        if (empty($borrowmingList)) {

            $borrowmingList = '{
                "Cable_modem_port": "0",
                "Cable_modem_two_way": "0",
                "Basic_digital_set_top_box": "0",
                "Digital_set_top_box_two_way_type": "0",
                "camera": "0",
                "Door_and_window_sensor": "0",
                "Smoke_detector": "0",
                "Cable_accessories_wireless_anti_frequency_sharing_device": "0",
                "Cable_accessories_transformer_power_cord": "0",
                "Cable_accessories_Ethernet_cable": "0",
                "Cable_accessories_USB_wireless_anti_frequency_network_card": "0",
                "Set_top_box_accessories_remote_control": "0",
                "Set_top_box_accessories_HDI": "0",
                "Set_top_box_accessories_AV_cable": "0",
                "Set_top_box_accessories_Chromatic_aberration_line": "0",
                "Set_top_box_accessories_transformer_power_cord": "0",
                "Set_top_box_accessories_smart_card": "0",
                "Set_top_box_accessories_external_hard_disk": "0",
                "Set_top_box_accessories_USB_wireless_anti_frequency_network_card": "0",
                "Set_top_box_accessories_ATV_set_top_box": "0",
                "Set_top_box_accessories_Bluetooth_remote_control": "0",
                "Smart_home_accessories_transformer_power_cord": "0"
            }';

            $borrowmingList = json_decode($borrowmingList);
        }

        if (empty($retrieveList)) {

            $retrieveList = '{
                "get_Cable_modem_port": "0",
                "get_Cable_modem_two_way": "0",
                "get_Basic_digital_set_top_box": "0",
                "get_Digital_set_top_box_two_way_type": "0",
                "get_camera": "0",
                "get_Door_and_window_sensor": "0",
                "get_Smoke_detector": "0",
                "get_Cable_accessories_wireless_anti_frequency_sharing_device": "0",
                "get_Cable_accessories_transformer_power_cord": "0",
                "get_Cable_accessories_Ethernet_cable": "0",
                "get_Cable_accessories_USB_wireless_anti_frequency_network_card": "0",
                "get_Set_top_box_accessories_remote_control": "0",
                "get_Set_top_box_accessories_HDI": "0",
                "get_Set_top_box_accessories_AV_cable": "0",
                "get_Set_top_box_accessories_Chromatic_aberration_line": "0",
                "get_Set_top_box_accessories_transformer_power_cord": "0",
                "get_Set_top_box_accessories_smart_card": "0",
                "get_Set_top_box_accessories_external_hard_disk": "0",
                "get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card": "0",
                "get_Set_top_box_accessories_ATV_set_top_box": "0",
                "get_Set_top_box_accessories_Bluetooth_remote_control": "0",
                "get_Smart_home_accessories_transformer_power_cord": "0"
            }';

            $retrieveList = json_decode($retrieveList);
        }

        // $chargeCMInfo = array();
        // $chargeDSTBInfo = array();
        // $chargeTWMBBInfo = array();

        try {

            $cus_data = array(
                'custId' => $order_info->CustID,
                'subsId' => $order_info->SubsID,
                'companyNo' => $order_info->CompanyNo,
            );

            if (!$pdf_info) {
                $order_info_id = $order_info->Id;
            } else {
                $order_info_id = data_get($order_info,'orderListId');
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

            // $F = false;
            // $M = false;
            // if ($checkSex == '1') {
            //     $M = true;
            // } else {
            //     $F = true;
            // }

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

            //CM簽名檔URL
            $cmSignUrl = '';
            $signFile = config('order.DOCUMENT_ROOT')."/public/upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_cm_".$worksheet.".jpg";
            $SignUrl = config('order.STB_API').'/upload/'.$order_info->CustID.'_'.date("Ymd",strtotime($order_info->BookDate));

            $signSHFile = config('order.DOCUMENT_ROOT')."/public/upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_hs_".$worksheet.".jpg";

            if (file_exists($signFile)) {
                $cmSignUrl = "/upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate)).'/sign_cm_'.$worksheet.'.jpg';
            } else if(file_exists($signSHFile) && filesize($signSHFile) > 0) {
                $cmSignUrl = $SignUrl.'/sign_hs_'.$worksheet.'.jpg';
            }

            //DSTB簽名檔URL
            $dstbSignUrl = '';
            $signFile = config('order.DOCUMENT_ROOT')."/public/upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_dstb_".$worksheet.".jpg";

            if (file_exists($signFile) ) {
                $dstbSignUrl = "/upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate)).'/sign_dstb_'.$worksheet.'.jpg';
            }

            //TWMBB簽名檔URL
            $twmbbSignUrl = '';
            $signFile = config('order.DOCUMENT_ROOT')."/public/upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_twmbb_".$worksheet.".jpg";

            if (file_exists($signFile) && filesize($signFile) > 0) {
                $twmbbSignUrl = $SignUrl.'/sign_twmbb_'.$worksheet.'.jpg';
            }

            //維修簽名檔URL-客戶
            $mcustSignUrl = '';
            $signFile = config('order.DOCUMENT_ROOT')."/public/upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_mcust_".$worksheet.".jpg";
            $SignUrl = config('order.STB_API').'/upload/'.$order_info->CustID.'_'.date("Ymd",strtotime($order_info->BookDate));

            if (file_exists($signFile) && filesize($signFile) > 0) {
                $mcustSignUrl = $SignUrl.'/sign_mcust_'.$worksheet.'.jpg';
            }

            //維修簽名檔URL-工程人員
            $mengineeSignUrl = '';
            $signFile = config('order.DOCUMENT_ROOT')."/public/upload/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate))."/sign_mengineer_".$worksheet.".jpg";
            $SignUrl = config('order.STB_API').'/upload/'.$order_info->CustID.'_'.date("Ymd",strtotime($order_info->BookDate));

            if (file_exists($signFile) && filesize($signFile) > 0) {
                $mengineeSignUrl = $SignUrl.'/sign_mengineer_'.$worksheet.'.jpg';
            }

            //合約條款
            $MSContract = data_get($order_info,'MSContract') ?? '';

            // 各公司別的網址
            $homeUrl = 'https://www.homeplus.net.tw/';
            $homeUrlData = array(
                '209' => 'https://www.skydigital.com.tw/',
                '210' => 'https://www.homeplus.net.tw/so/KL/so-news-1_15_26.html',
                '220' => 'https://www.homeplus.net.tw/so/EL/so-news-1_15_40.html',
                '230' => 'https://www.homeplus.net.tw/so/WD/so-news-1_15_60.html',
                '240' => 'https://www.homeplus.net.tw/so/LG/so-news-1_15_49.html',
                '250' => 'https://www.homeplus.net.tw/so/NVW/so-news-1_15_69.html',
                '270' => 'https://www.homeplus.net.tw/so/GH/so-news-1_15_78.html',
                '310' => 'https://www.homeplus.net.tw/so/T1/so-news-1_15_90.html',
                '610' => 'https://www.homeplus.net.tw/so/TS/so-news-1_15_108.html',
                '620' => 'https://www.homeplus.net.tw/so/SUN/so-news-1_15_99.html',
                '720' => 'https://www.homeplus.net.tw/so/CL/so-news-1_15_117.html',
                '730' => 'https://www.homeplus.net.tw/so/GD/so-news-1_15_126.html',
            );

            if (array_key_exists($so, $homeUrlData)) {
                $homeUrl = $homeUrlData[$so];
            }

            // 各公司別的服務電話

            $serviceNum = '412-8811';
            $numberData = array(
                '209' => '2165-3123',
                '210' => '2165-3152',
                '220' => '2165-3153',
                '230' => '2165-3156',
                '240' => '2165-3157',
                '250' => '2165-3688',
                '270' => '2165-3366',
                '310' => '412-8813',
                '610' => '412-8812',
                '620' => '412-8833',
                '720' => '412-8801',
                '730' => '412-8891',
            );

            if (array_key_exists($so, $numberData)) {
                $serviceNum = $numberData[$so];
            }


            require(config('order.DOCUMENT_ROOT').'/public/TCPDF-main/tcpdf.php');

            // require_once('/home/www-devp/ewo/public/TCPDF-main/tcpdf.php');

            $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetHeaderData('', 0, '', '', array(255,255,255), array(255,255,255));
            $pdf->setFooterData( array(255,255,255), array(255,255,255));
            $pdf->SetMargins(10, 0, 10, true);



            if ($order_info->WorkKind != '5 維修')
            {

                /******第一頁******/
                if (in_array('1 CATV', $serviceName) || in_array('3 DSTB', $serviceName)) {
                    $pdf->AddPage();
                    $pdf->SetFont('droidsansfallback','',20);

                    $title = $company[$order_info->CompanyNo];
                    $pdf->Cell(50,5,'',0,0);
                    $pdf->Cell(0,5,$title,0,0);
                    $pdf->Ln();

                    $pdf->SetFont('droidsansfallback','',10);
                    $pdf->Cell(15,30,'',0,0);
                    $pdf->SetFont('droidsansfallback','',12);
                    $pdf->Cell(120,10,'CATV有線電視/DTV數位電視加值服務申請書',0,0,'R',0,'',0,false,'T','C');
                    $pdf->Ln();

                    if(data_get($order_info,'CompanyNo') === '209') {
                        //
                    } else {
                        $pdf->Image('/public/img/logo_01.png',10,15,40);
                    }

                    $pdf->SetFont('droidsansfallback','',11);
                    $pdf->Cell(50,10,'服務電話：(02)'.$serviceNum,0,0,'R',0,'',0,false,'T','B');
                    $pdf->Cell(70,10,'網址：'.$homeUrl,0,0,'L',0,'',0,false,'T','B');
                    $pdf->Ln();
                    $pdf->Cell(75,5,'地址：'.$companyAddress[$order_info->CompanyNo],0,0,'R',0,'',0,false,'T','B');
                    $pdf->Ln();
                    $pdf->Cell(56,10,'訂單編號：'.$order_info->WorkSheet,0,0,'R',0,'',0,false,'T','T');
                    $pdf->Cell(40,10,'NO：',0,0,'R',0,'',0,false,'T','T');
                    $pdf->Ln(5);


                    $tbl1 = '
                    <table  border="1" style="width:100%;">
                        <tr>
                            <td style="width:44%;">客戶基本資料</td>
                            <td style="width:15%;">派工類別</td>
                            <td style="width:15%;">IVR簡碼</td>
                            <td style="width:26%;">派工單序號</td>
                        </tr>
                        <tr>
                            <td rowspan="2" style="width:44%;font-size:8px">
                                客戶編號：'.$order_info->CustID.' <br>
                                姓名：'.$order_info->CustName.' <br>
                                電話(家)：'.$hometel.' <br>
                                行動電話：'.$phonetel.' <br>
                                裝機地址：'.$InstAddrName.' <br>
                                收費地址：'.$InstAddrName.' <br>
                                大樓(社區)名稱： <br>
                                移機舊址：
                            </td>
                            <td style="height:40px;text-align:center;line-height:40px;">'.$order_info->WorkKind.'</td>
                            <td style="height:40px;text-align:center;">'.$dstbIVR.'</td>
                            <td style="height:40px;text-align:center;line-height:40px;">'.$order_info->WorkSheet.'</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="border-right:1px solid white;font-size:8px">
                                工程組別：<br>'.$order_info->WorkTeam.'('.$order_info->WorkerName.') <br>
                                網路編號：'.$order_info->NetID.' <br>
                                下次收費日： <br>
                                方案別(合約起迄日)： <br>
                                <span style="font-size:6.2px">'.$order_info->SaleCampaign.'</span>
                            </td>
                            <td style="border-left:1px solid white;font-size:8px">
                                受理人:'.$order_info->CreateName.' <br>
                                受理日期時間:<br><span style="font-size:8px">'.$order_info->CreateTime.'</span><br>
                                預約日期時間:<br><span style="font-size:8px">'.$order_info->BookDate.'</span>
                            </td>

                        </tr>';
                        // <tr>
                        //     <td style="width:70%;border-top:1px solid black;border-right:1px solid white;">
                        //         備註：'.$order_info->MSComment1.'
                        //     </td>
                        //     <td style="width:30%;border-top:1px solid black;border-left:1px solid white;font-size:8px">
                        //         HubNode:'.$order_info->NetID.' <br>
                        //         客戶類別1:??? <br>
                        //         客戶類別2:???
                        //     </td>
                        // </tr>
                        $tbl1 .= '
                    </table>
                    <table border="1" cellpadding="0">
                        <tr>
                            <td style="width:10%">設備型號</td>
                            <td style="width:18%">設備序號</td>
                            <td style="width:21%">收費項目</td>
                            <td style="width:23%">收費期間</td>
                            <td style="width:9%">金額</td>
                            <td colspan="2" style="width:19%">總應收金額</td>
                        </tr>
                        <tr>
                            <td rowspan="8">
                            </td>
                            <td rowspan="8">
                            </td>
                            <td rowspan="8" style="font-size:7px;width:21%;line-height: 100%;">
                            ';


                            foreach ($charges as $key => $charge) {

                                if ($key != '3 DSTB' && $key != '1 CATV') {
                                    continue;
                                }
                                foreach ($charge as $value) {
                                    $chargeName = data_get($value,'ChargeName');

                                    $tbl1 .= '<p style="line-height:5px;">'.$chargeName.'</p>';
                                }

                            }

                            $tbl1 .='
                                <br>
                            </td>
                            <td rowspan="8" style="font-size:7px;width:23%;line-height: 100%;">
                            ';

                            foreach ($charges as $key => $charge) {

                                if ($key != '3 DSTB' && $key != '1 CATV') {
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
                                    }

                                    $tbl1 .= '<p style="line-height:5px;">'.$chargeDate.'</p>';
                                }

                            }

                            $tbl1 .='
                                <br>
                            </td>
                            <td rowspan="8" style="font-size:7px;width:9%;text-align:right;line-height: 100%;">
                            ';

                            $recvAmt = 0;


                            foreach ($charges as $key => $charge) {

                                if ($key != '3 DSTB' && $key != '1 CATV') {
                                    continue;
                                }

                                foreach ($charge as $value) {
                                    $billAmt = data_get($value,'BillAmt');

                                    if (empty($billAmt)) {
                                        $billAmt=0;
                                    }

                                    $tbl1 .= '<p style="line-height:5px;">'.(int)$billAmt.' </p>';
                                    $recvAmt +=(int)$billAmt;
                                }

                            }


                            $tbl1 .='
                                <br>
                            </td>
                            <td colspan="2">
                            $'.$recvAmt.'
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                總實收金額
                            </td>
                        </tr>
                        <tr>
                            <td>本票</td>
                            <td>金額</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>'.$recvAmt.'</td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                工程人員
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                '.$order_info->WorkerName.'
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                            </td>
                        </tr>
                    </table>
                    <table border="1" style="width:100%;font-size:7px">
                        <tr >
                            <td rowspan="2" style="width:38%;height:20px">備註二：'.$order_info->MSComment1.'</td>
                            <td style="width:20%">舊吊牌編碼</td>
                            <td style="width:15%"></td>
                            <td style="width:27%">設備/贈品/證件繳交確認</td>
                        </tr>
                        <tr>
                            <td style="width:20%;line-height:100%;text-align:center">
                                <p style="">未完工填寫代碼</p>
                                <p style="">(完工貼吊牌)</p>
                                <br>
                            </td>
                            <td style="width:15%">
                            </td>
                            <td style="line-height: 100%;">';

                                $checkId = empty(data_get($checkDSTB,'dstb_check_id')) ? '口' : '[v]';
                                $checkHealth = empty(data_get($checkDSTB,'dstb_check_health')) ? '口' : '[v]';
                                $checkDriver = empty(data_get($checkDSTB,'dstb_check_driver')) ? '口' : '[v]';
                                $checkDriverRem = data_get($checkDSTB,'dstb_check_driver_desc');
                                $checkCompany = empty(data_get($checkDSTB,'dstb_check_company')) ? '口' : '[v]';
                                $checkOther = empty(data_get($checkDSTB,'dstb_check_other')) ? '口' : '[v]';
                                $checkOtherRem = data_get($checkDSTB,'dstb_check_other_desc');

                                $tbl1 .='
                                <p style="line-height:2px;">'.$checkId.'身分證正反面影本</p>
                                <p style="line-height:2px;">'.$checkHealth.'健保卡 </p>
                                <p style="line-height:2px;">'.$checkDriver.'駕照影本('.$checkDriverRem.')</p>
                                <p style="line-height:2px;">'.$checkCompany.'公司變更登記事項表</p>
                                <p style="line-height:2px;">'.$checkOther.'其他 '.$checkOtherRem.'</p>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td style="width:100%;font-size:5px">
                                特約條款：
                                <span>'.$MSContract.'
                                </span>
                            </td>
                        </tr>
                    </table>
                    <table border="1">
                        <tr>
                            <td style="width:70%;font-size:5px;">
                                1.申裝設備內含(1)數位機上盒乙台(2)智慧卡乙張(3)遙控器乙個(4)變壓器乙組(5)AV端子線乙組(6)色差端子線乙組。2.申裝設備/月繳現付用戶須附上身分證正反面影本。3.請選擇「有線電視收視費」次期繳費週期：口月繳現付、口雙月繳、口季繳、口半年繳、口全年繳。4.用戶若選擇借用本設備，其所有權為本公司所有，用戶限於上述裝機地址使用。當雙方有線電視收視合約終止時，用戶應立即將本設備全數及完整歸還予本公司。借用人應善盡保管義務使用本設備，歸還時如發生部分設備或整組設備遺失、毀損之情事，借用人應按設備市價賠償予本公司。5.使用DTV數位加值服務之用戶必須同時為本公司有效之有線電視基本頻道收視戶。若與本公司之有線電視基本頻道收視契約終止時，本公司得不經預告立即終止本服務。6.已申裝銀行自動扣款者同意因訂購本公司之服務所生之一切費用，均由原自動扣繳方式支付。本人茲確認及同意1.本申裝方案及特約條款內容2.申請書背面之有線電視定型化契約3.貴公司保護及使用用戶資料權益聲明:為提供服務我們將保存及使用您所提供之「有線電視及加值服務」用戶資料，包括您與我們聯絡所提供之個人資料(例：姓名、電話、地址、聯絡人姓名、信用卡資料、付款帳務、資訊流服務調查等)。除了個人資料外，我們也將收集您使用機上盒、機上盒回傳本公司系統之資料(例：機上盒開關待機資訊、家戶收視頻道及時間、家戶收視習慣等)。用戶資料之保存與使用主要是用於提供我們的服務(包括基本及加值服務)、提昇產品服務品質(如：了解家戶收視習慣及偏好，以提供用戶更好的內容及服務)、加強個人化服務(例：提供個人化內容之推薦、個人化廣告之提供)、及停止服務後之服務產品訊息告知(包括以電郵、簡訊、語音及視訊等方式提供適合您的服務及行銷訊息，例：有線電視匯流相關服務產品、視訊服務之產品銷售訊息通知、節目及服務數位資訊流匯整等)，未經您同意，不會另外將您的用戶資料揭露於與本公司無關之第三人非上述目的以外之用途、或非在必要之範圍內。若您不想再收到我們的訊息或用戶資訊需要更新等，請與客服聯絡，我們將由專人為您服務。4.申裝數位電視服務，本人了解需負責保管貴公司數位機上盒及終止服務時至營業櫃檯返還之義務及法律責任。</td>
                            <td colspan="2" style="width:30%;font-size:15px">
                                數位機上盒&智慧卡條碼黏貼欄
                            </td>
                        </tr>
                        <tr>
                            <td style="line-height: 100%;font-size:8px">
                                <p style="line-height: 5px;">';

                                $checkInvoice = empty(data_get($checkDSTB,'dstb_check_invoice')) ? '口' : '[v]';
                                $checkPersonal = empty(data_get($checkDSTB,'dstb_check_personal')) ? '口同意；口不同意' : '[v]同意；口不同意';
                                $checkLegal = data_get($checkDSTB,'dstb_check_legal');
                                $checkTitle= data_get($checkDSTB,'dstb_check_title') ?? '無';

                                $signImage = '';
                                if ($dstbSignUrl) {
                                    $signImage = '<img src="'.$dstbSignUrl.'" height="50px" style="padding: 0 20px 0 20px;">';
                                }

                                $tbl1 .='</p>
                                <p style="line-height: 5px;">申裝人(簽名):    '.$signImage.'    ，法定代理人/代表人:'.$checkLegal.'</p>
                                <p style="font-size:6px">[請用戶務必勾選]本人'.$checkPersonal.':貴公司進行機上行頻道節目收視之資訊蒐集分析及個人化內容之推薦等。</p>
                                <p style="line-height: 5px;">關係或稱謂:__'.$checkTitle.'___</p>
                                <br>
                            </td>
                            <td style="width:3%;text-align:center">
                            裝置點
                            </td>
                        </tr>
                    </table>
                    ';

                    $pdf->writeHTML($tbl1, true, false, false, false, '');
                }

                /******第一頁 END******/

                /******第二頁******/

                $infoData = array(
                    'order_info'=>$order_info,
                    'hometel'=>$hometel,
                    'phonetel'=>$phonetel,
                    'charges'=>$charges,
                    'cmSignUrl'=>$cmSignUrl,
                    'twmbbSignUrl'=>$twmbbSignUrl,
                    // 'PersonID'=>$PersonID,
                    'showBill'=>$showBill,

                );

                if (in_array('D TWMBB', $serviceName)) {

                    $pdf->AddPage();
                    $pdf->SetFont('droidsansfallback','',20);

                    $title = $company[$order_info->CompanyNo];
                    $pdf->Cell(50,5,'',0,0);
                    $pdf->Cell(130,10,$title,0,0,'T',0,'',0,false,'T','C');
                    $pdf->SetFont('droidsansfallback','',12);
                    $pdf->Cell(10,10,'台灣之星委託件',0,0,'R',0,'',0,false,'T','C');
                    $pdf->Cell(0,20,'中嘉寬頻股份有限公司',0,0,'R',0,'',0,false,'T','C');
                    $pdf->Cell(0,30,'訂編：'.$order_info->WorkSheet,0,0,'R',0,'',0,false,'T','C');
                    $pdf->Cell(0,40,'NO：                               ',0,0,'R',0,'',0,false,'T','C');
                    $pdf->Ln(9);

                    // $pdf->SetFont('droidsansfallback','',10);
                    // $pdf->Cell(15,30,'',0,0);
                    $pdf->SetFont('droidsansfallback','',20);
                    $pdf->Cell(100,0,'CM 派工單',0,0,'R',0,'',0,false,'T','T');
                    $pdf->Ln();


                    if(data_get($order_info,'CompanyNo') === '209') {
                        //
                    } else {
                        $pdf->Image('/public/img/logo_01.png',10,15,40);
                    }

                    $pdf->SetFont('droidsansfallback','',11);
                    $pdf->Cell(50,10,'服務電話：(02)'.$serviceNum,0,0,'R',0,'',0,false,'T','B');
                    $pdf->Cell(70,10,'網址：'.$homeUrl,0,0,'L',0,'',0,false,'T','B');
                    $pdf->Ln();
                    $pdf->Cell(58,5,'地址：'.$companyAddress[$order_info->CompanyNo],0,0,'R',0,'',0,false,'T','T');
                    $pdf->Ln();

                    $tbl2 = $this->twmbbpdf($infoData,$checkTWMBB,$InstAddrName);
                    $pdf->writeHTML($tbl2, true, false, false, false, '');

                }

                if (in_array('2 CM', $serviceName) || in_array('C HS', $serviceName)) {

                    $pdf->AddPage();
                    $pdf->SetFont('droidsansfallback','',20);

                    $title = ($so == '209')? '寶島聯網股份有限公司___CM___派工單' : '中嘉寬頻股份有限公司___CM___派工單';
                    $pdf->Cell(35,5,'',0,0);
                    $pdf->Cell(0,5,$title,0,0);
                    $pdf->Ln();

                    $pdf->SetFont('droidsansfallback','',10);
                    $pdf->Cell(15,30,'',0,0);
                    $pdf->SetFont('droidsansfallback','',20);
                    //$pdf->Cell(100,10,'光纖寬頻網路',0,0,'R',0,'',0,false,'T','C');
                    $pdf->Ln();



                    if(data_get($order_info,'CompanyNo') === '209') {
                        //
                    } else {
                        $pdf->Image('/public/img/logo_01.png',10,15,40);

                    }

                    $pdf->SetFont('droidsansfallback','',11);
                    $pdf->Cell(50,10,'服務電話：(02)'.$serviceNum,0,0,'R',0,'',0,false,'T','B');
                    $pdf->Cell(70,10,'網址：'.$homeUrl,0,0,'L',0,'',0,false,'T','B');
                    $pdf->Ln();
                    $pdf->Cell(58,5,'地址：'.$companyAddress[$order_info->CompanyNo],0,0,'R',0,'',0,false,'T','T');
                    $pdf->Ln();
                    $pdf->Cell(56,10,'訂單編號：'.$order_info->WorkSheet,0,0,'R',0,'',0,false,'T','T');
                    $pdf->Cell(40,10,'NO：',0,0,'R',0,'',0,false,'T','T');
                    $pdf->Ln();

                    $tbl2 = $this->cmpdf($infoData,$checkCM,$InstAddrName);
                    $pdf->writeHTML($tbl2, true, false, false, false, '');
                }

                /******第二頁 END******/

                /******第三頁******/

                $pdf->AddPage();
                $pdf->SetFont('droidsansfallback','',20);

                $title = '口裝機 口維修 口拆機 設備借用／取回保管單';
                $pdf->Cell(30,5,'',0,0);
                $pdf->Cell(0,5,$title,0,0);
                $pdf->Ln();

                $pdf->SetFont('droidsansfallback','',10);
                $pdf->Cell(0,10,'客戶編號：'.$order_info->CustID.' 工單單號：'.$order_info->WorkSheet,0,0);
                $pdf->Ln();
                // $pdf->Cell(17,0,'',0,0);
                $pdf->Cell(0,10,'甲方:'.$company[$order_info->CompanyNo],0,0);
                $pdf->Ln();
                $pdf->Cell(0,10,'乙方：'.$order_info->CustName.' 收視用戶；裝設地址：'.$InstAddrName,0,0);
                $pdf->Ln();

                $pdf->Cell(0,10,'借用設備品名：(＊請親自勾選＊)',0,0);
                $pdf->Ln();

                $tenth = $this->tenthPage($borrowmingList,$retrieveList);
                $pdf->writeHTML($tenth, true, false, false, false, '');

                /******第三頁 END******/

                /******第四頁 ******/
/*
                $pdf->AddPage();
                $pdf->SetFont('droidsansfallback','',15);

                $pdf->Cell(0,10,'Home+申裝 / 異動 申請書',0,0,'C',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(0,10,'有線電視光纖網路服務',0,0,'C',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(0,10,'數位有線電視基本頻道服務',0,0,'C',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(0,10,'加值服務',0,0,'C',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Ln();

                $pdf->Cell(0,10,'設備/贈品/證件繳交確認欄',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Ln();

                $pdf->Cell(0,10,'申裝或異動：',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(0,10,'1. 此次總應收金額：',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(0,10,'2. 此次實收金額：',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(5,5,'',0,0);
                $pdf->Cell(0,10,'口現金 口非現金',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Ln();

                $pdf->Cell(10,10,'口Home+數位有線電視基本頻道服務',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(5,5,'',0,0);
                $pdf->Cell(0,10,'口必勾，已閱有線電視定型化契約及個人資料告知',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(5,5,'',0,0);
                $pdf->Cell(0,10,'口必勾，已閱有線電視基本頻道服務設備借用如上勾選',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(5,5,'',0,0);
                $pdf->Cell(0,10,'口建議勾，同意就遙控器使用、收視行為蒐集分析使用(提昇服務用)',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Ln();

                $pdf->Cell(0,10,'口Home+加值服務',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();

                $addedService = '<blockquote style="font-size:10px">1.本「智慧攝影機」服務專案之申裝使用期間須同時租用本公司「光纖上網」(1)該地址90天內退拆新裝，恕不適用。(2)依指定繳別預繳，並至少連續使用24個月。(3)1組智慧攝影機(借用)專案優惠價格：150元/月。(4)智慧攝影機裝機費優惠為0元。(5)約期屆滿後，智慧攝影機費用將恢復原一般價續出帳，若欲適用當時續約優惠價請至官網查詢。2.裝機時需填寫智慧攝影機等設備借用保管憑證，免收設備保證金，若因故須終止服務未歸還設備將衍生民事等相關法律問題。3.合約期間不得申請暫停，變更專案內容或退拆「光纖上網」服務：(1)需補繳智慧攝影機專案費用最高2,000元，將依未足日使用數及約期數之比例遞減，且須返還全額券類等值現金。(2)補繳專案補助之智慧攝影機裝機費最高500元。(3)需自行至門市繳回智慧攝影機等借用設備，並結清費用完成退租後，將退還預繳之費用。4.智慧攝影機服務，用戶需下載特定APP並綁定一個帳號，雲端存取規則依本公司最新規定。5.依「通訊交易解除權合理例外情事適用準則」，本服務無解除權之適用(非以有形媒介提供之數位內容或一經提供即為完成之線上服務，經消費者事先同意始提供)。</blockquote>';

                $pdf->writeHTML($addedService, true, false, false, false, '');
                $pdf->Ln();

                $pdf->AddPage();
                $pdf->SetFont('droidsansfallback','',15);

                $pdf->Cell(0,10,'口Home+有線電視光纖網路服務專案條款',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();

                $pdf->Cell(0,10,'口測速完成________ ，同意優惠專案條款如下：',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();

                $clause = '
                <blockquote style="font-size:10px">1. 本專案(1)限光纖上網用戶新申裝，該地址90天內退拆新裝，恕不適用。(2)依指定繳別預繳，並至少連續使用30個月。(3)各速率寬頻專案優惠價格：100M/30M 299元/月。(4)需繳納光纖上網裝機費500元。(5)約期屆滿後，光纖上網費用將恢復原申裝速率之一般價續出帳， 若欲適用當時續約優惠價請至官網查詢。</blockquote>
                <blockquote style="font-size:10px">2.本服務所示速率皆為供裝後最高可達速率。會因客戶使用時段、電腦效能、分享軟體等受限頻寬等因素影響資訊接取速率；免費建置家中Wi-Fi環境之服務，係提供一個內建或外接之Wi-Fi設備，訊號強弱會依申裝人居家牆壁阻隔等而異，恕無法指定無線上網設備機型或型號。</blockquote>
                <blockquote style="font-size:10px">3.裝機時需填寫數據機等設備借用保管憑證，免收設備保證金，若因故須終止服務未歸還設備將衍生民事等相關法律問題。</blockquote>
                <blockquote style="font-size:10px">4.合約期間不得申請暫停，變更專案內容或退拆：(1)需補繳光纖上網專案費用最高4,800元，將依未足日使用數及約期數之比例遞減，且須返還全額券類等值現金。(2)補繳專案補助之光纖上網裝機費最高1,500元。(3)需自行至門市繳回數據機等借用設備，並結清費用完成退租後，將退還預繳之費用。</blockquote>
                <blockquote style="font-size:10px">5.依「通訊交易解除權合理例外情事適用準則」，本服務無解除權之適用(非以有形媒介提供之數位內容或一經提供即為完成之線上服務，經消費者事先同意始提供)，若消費者仍欲行使解除權，須於裝機日起7日內攜帶設備至本公司門市櫃檯辦理，並補繳專案補助之光纖上網裝機費最高1,500元、使用天數之連線費及未拆封贈品，專案費用則由本公司吸收。</blockquote>';

                $pdf->writeHTML($clause, true, false, false, false, '');
                $pdf->Ln();


                $pdf->Cell(15,5,'',0,0);
                $pdf->Cell(0,10,'口必勾，已閱有線電視光纖網路服務定型化契約及個人資料告知',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(15,5,'',0,0);
                $pdf->Cell(0,10,'口必勾，已閱有線電視光纖網路服務設備借用品項如上勾選',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(15,5,'',0,0);
                $pdf->Cell(0,10,'口建議勾，同意就流量使用行為之分析及使用(提昇服務用)',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Ln();

                $signImage = ' 申裝人(客戶簽名)：<img src="'.$dstbSignUrl.'" width="30px" height="6px">';
                $pdf->writeHTML($signImage, true, false, false, false, '');

                $pdf->Cell(0,10,'申裝地址：'.$InstAddrName,0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(0,10,'申裝人個人資料及電話：(略，已入檔案)',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Ln();

                $pdf->Cell(0,10,'※請選擇『數位有線電視收視費』次期繳費週期：',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(10,5,'',0,0);
                $pdf->Cell(0,10,'(申辦光纖專案限依指定繳別預繳)',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();
                $pdf->Cell(0,10,'口全年繳、口半年繳、口季繳、口雙月繳、口月繳現付。',0,0,'L',0,'',0,false,'T','C');
                $pdf->Ln();

                $pdf->Cell(0,10,'註：為提供申裝人得以正常使用上述服務，同意Home+無償使用申請人外牆附掛纜線',0,0,'L',0,'',0,false,'T','C');
*/
                /******第四頁 END******/

                $title = $company[$order_info->CompanyNo];

                $pdf->AddPage();
                $pdf->SetFont('droidsansfallback','',5);
                $first = $this->firstPage($title);
                // error_log(print_r($first,true));
                $pdf->writeHTML($first, true, false, false, false, '');

                $pdf->AddPage();
                $pdf->SetFont('droidsansfallback','',10);
                $second = $this->secondPage($pdf);

                $pdf->writeHTML($second, true, false, false, false, '');

                $pdf->AddPage();
                $pdf->SetFont('droidsansfallback','',10);
                $third = $this->thirdPage();

                $pdf->writeHTML($third, true, false, false, false, '');

                $pdf->AddPage();

                $pdf->SetFont('droidsansfallback','',15);

                $fiveTitle = 'CM定型化契約';
                $pdf->Cell(85,5,'',0,0);
                $pdf->Cell(0,5,$fiveTitle,0,0);
                $pdf->Ln();

                $pdf->SetFont('droidsansfallback','',5);

                $five = $this->fivePage();
                $pdf->writeHTML($five, true, false, false, false, '');


                $pdf->AddPage();

                $pdf->SetFont('droidsansfallback','',8);

                $seven = $this->seventhPage();
                $pdf->writeHTML($seven, true, false, false, false, '');



                //**************證件**************//
                // $pdf->SetMargins(10, 10, 10, true);
                // $pdf->AddPage();
                // $pdf->SetFont('droidsansfallback','',20);

                // $tbl3 = '
                //     <table border="1" style="text-align:center">
                //         <tr>
                //             <td style="line-height:50px;border-bottom:1px solid white">
                //                 第一證件黏貼區
                //             </td>
                //         </tr>
                //         <tr>
                //             <td style="text-align:center;border-top:1px solid white">
                //                 <table cellspacing="6" cellpadding="4">
                //                     <tr>';

                //                     $imgUrl1 = config('filesystems.disks.upload.root').'/'.$order_info->CompanyNo.'_'.$order_info->WorkSheet.'/identity_01.jpg';

                //                     if(file_exists($imgUrl1)){
                //                         $tbl3 .= '<td border="1"><img src="'.$imgUrl1.'"></td>';
                //                     } else {
                //                         $tbl3 .= '<td border="1" style="line-height:180px"></td>';
                //                     }

                //                     $imgUrl2 = config('filesystems.disks.upload.root').'/'.$order_info->CompanyNo.'_'.$order_info->WorkSheet.'/identity_02.jpg';

                //                     if(file_exists($imgUrl2)){
                //                         $tbl3 .= '<td border="1"><img src="'.$imgUrl2.'"></td>';
                //                     } else {
                //                         $tbl3 .= '<td border="1" style="line-height:180px"></td>';
                //                     }

                //                     $tbl3 .= '
                //                     </tr>
                //                 </table>
                //             </td>

                //         </tr>
                //     </table>
                //     <table border="1" style="text-align:center">
                //         <tr>';


                //             $imgUrl3 = config('filesystems.disks.upload.root').'/'.$order_info->CompanyNo.'_'.$order_info->WorkSheet.'/identity_03.jpg';

                //             if(file_exists($imgUrl3)){
                //                 $tbl3 .= '<td style="line-height:0">';
                //                 $tbl3 .= '<img src="'.$imgUrl3.'" width="300" height="300">';
                //             } else {
                //                 $tbl3 .= '<td style="line-height:300px">';
                //                 $tbl3 .= '第二證件黏貼區';
                //             }

                //             $tbl3 .= '
                //             </td>
                //         </tr>
                //     </table>
                // ';

                // $pdf->writeHTML($tbl3, true, false, false, false, '');
                //**************證件 END**************//

            } else {

                $deviceCount = json_decode(data_get($order_info,'deviceCount'));
                $maintainHistory = json_decode(data_get($order_info,'maintainHistory'));
                $deviceSWVersion = json_decode(data_get($order_info,'deviceSWVersion'));

                $serviceNameStr =  implode(' ', $serviceName);
                /******維修單******/

                $pdf->AddPage();
                $pdf->SetFont('droidsansfallback','',20);

                $title = $company[$order_info->CompanyNo];
                $pdf->Cell(50,5,'',0,0);
                $pdf->Cell(0,5,$title,0,0);
                $pdf->Ln();

                $pdf->SetFont('droidsansfallback','',10);
                $pdf->Cell(15,30,'',0,0);
                $pdf->SetFont('droidsansfallback','',12);
                $pdf->Cell(100,10,'__'.$serviceNameStr.'__維修服務單',0,0,'R',0,'',0,false,'T','C');
                $pdf->Ln();

                if(data_get($order_info,'CompanyNo') === '209') {
                    //
                } else {
                    $pdf->Image('/public/img/logo_01.png',10,15,40);
                }

                $pdf->SetFont('droidsansfallback','',11);
                $pdf->Cell(50,10,'服務電話：(02)'.$serviceNum,0,0,'R',0,'',0,false,'T','B');
                $pdf->Cell(70,10,'網址：'.$homeUrl,0,0,'L',0,'',0,false,'T','B');
                $pdf->Ln();
                $pdf->Cell(58,5,'地址：'.$companyAddress[$order_info->CompanyNo],0,0,'R',0,'',0,false,'T','T');
                $pdf->Ln();
                $pdf->Cell(61,10,'工單單號：'.$order_info->WorkSheet,0,0,'R',0,'',0,false,'T','T');
                $pdf->Cell(40,10,'IVR簡碼：'.$dstbIVR,0,0,'R',0,'',0,false,'T','T');
                $pdf->Ln(5);


                $tbl1 = '
                <table  border="1" style="width:100%;">
                    <tr>
                        <td style="width:40%;text-align:center;">客戶基本資料</td>
                        <td colspan="2"  style="width:60%;text-align:center;">工程登錄資料</td>

                    </tr>
                    <tr>
                        <td style="font-size:8px">
                            客戶編號：'.$order_info->CustID.' <br>
                            姓名：'.$order_info->CustName.' <br>
                            電話(家)：'.$hometel.' <br>
                            行動電話：'.$phonetel.' <br>
                            裝機地址：'.$InstAddrName.' <br>
                            收費地址：'.$InstAddrName.' <br>
                            移機新址： <br>
                            電子郵件信箱： <br>
                            維修申告：'.data_get($order_info,'WorkCause').'<br>
                        </td>
                        <td style="width:30%;font-size:8px">
                            派工單序號：'.$order_info->WorkSheet.' <br>
                            客戶編號：'.$order_info->CustID.' <br>
                            服務區域： <br>
                            Node NO： <br>
                            客戶類別：<br>
                            大樓(社區)名稱： <br>
                        </td>
                        <td style="width:30%;font-size:8px">
                            受理日期時間：'.$order_info->create_at.' <br>
                            預約日期時間：'.$order_info->BookDate.' <br>
                            受理人員：'.$order_info->CreateName.' <br>
                            工程組別：'.$order_info->WorkTeam.' <br>
                            工程人員：'.$order_info->WorkerName.' <br>
                        </td>
                    </tr>
                </table>

                <table border="1" cellpadding="0">
                    <tr>
                        <td style="width:70%;height:100px">
                        維修來電備註：
                        <p style="font-size:8px">'.$order_info->MSComment1.'</p>
                        </td>
                        <td style="width:30%">
                        維修工單條碼：
                        <p></p>
                        </td>
                    </tr>
                </table>

                <table border="1" style="width:100%;font-size:10px;text-align:center">
                    <tr>
                        <td style="">CM訂購速率</td>
                        <td style="">CM設備台數</td>
                        <td style="">DTV雙向設備台數</td>
                        <td style="">DTV單向設備台數</td>
                        <td style="">PVR設備台數</td>
                    </tr>
                    <tr>
                        <td style="">'.data_get($deviceCount,'CMBAUDRATE').'</td>
                        <td style="">'.data_get($deviceCount,'I_CNT').'</td>
                        <td style="">'.data_get($deviceCount,'D_DUBLECNT').'</td>
                        <td style="">'.data_get($deviceCount,'D_SINGLECNT').'</td>
                        <td style="">'.data_get($deviceCount,'PVR_CNT').'</td>
                    </tr>
                </table>
                <table border="1">
                    <tr>
                        <td colspan="7" style="">歷史維修紀錄：</td>
                    </tr>
                    <tr style="text-align:center">
                        <td style="">維修紀錄</td>
                        <td style="">維修日期</td>
                        <td style="">結案人員</td>
                        <td style="">工程組別</td>
                        <td style="">維修申告</td>
                        <td style="">故障原因一</td>
                        <td style="">故障原因二</td>
                    </tr>';

                    if(!empty($maintainHistory))
                    foreach ($maintainHistory as $key => $history) {
                        $tbl1 .= '<tr style="text-align:center;font-size:9px;">';
                        $tbl1 .= '<td style="">'.$key.'</td>';

                        $FINTIME = data_get($history,'FINTIME');
                        $SIGNNAME = data_get($history,'SIGNNAME');
                        $GROUPNAME = data_get($history,'GROUPNAME');
                        $SERVICENAME = data_get($history,'SERVICENAME');
                        $MFNAME1 = data_get($history,'MFNAME1');
                        $MFNAME2 = data_get($history,'MFNAME2');

                        $tbl1 .= '<td style="">'.$FINTIME.'</td>';
                        $tbl1 .= '<td style="">'.$SIGNNAME.'</td>';
                        $tbl1 .= '<td style="">'.$GROUPNAME.'</td>';
                        $tbl1 .= '<td style="">'.$SERVICENAME.'</td>';
                        $tbl1 .= '<td style="">'.$MFNAME1.'</td>';
                        $tbl1 .= '<td style="">'.$MFNAME2.'</td>';

                         $tbl1 .= '</tr>';

                         if ($key > 6) {
                             break;
                         }
                    }

                    $CMMODELNAME = '';
                    $CMFACISNO = '';
                    $DSTBMODELNAME = '';
                    $DSTBFACISNO = '';
                    if(!empty($deviceSWVersion))
                    foreach ($deviceSWVersion as $key => $SWVersion) {
                        if ($key =='2 CM') {
                            $CMMODELNAME = data_get($SWVersion,'MODELNAME');
                            $CMFACISNO = data_get($SWVersion,'FACISNO');
                        } elseif ($key =='3 DSTB') {
                            $DSTBMODELNAME = data_get($SWVersion,'MODELNAME');
                            $DSTBFACISNO = data_get($SWVersion,'FACISNO');
                        }
                    }

                    $CMMODELNAME = explode(' ', $CMMODELNAME);
                    $CMFACISNO = explode(' ', $CMFACISNO);
                    $DSTBMODELNAME = explode(' ', $DSTBMODELNAME);
                    $DSTBFACISNO = explode(' ', $DSTBFACISNO);

                $tbl1 .= '
                </table>
                <table border="1">
                    <tr>
                        <td style="">設備型號序號：</td>
                    </tr>
                </table>
                <table border="1">
                    <tr style="font-size:9px;">
                        <td style="width:70%;border-right:0px solid white">
                            <table>
                                <tr>
                                    <td>I型號</td>
                                    <td>I序號</td>
                                    <td>D型號</td>
                                    <td>D序號</td>
                                </tr>
                                <tr>';

                                $tbl1 .= '<td>';
                                if (count($CMMODELNAME)>0) {
                                    foreach ($CMMODELNAME as $value) {
                                        $tbl1 .= $value.'<br>';
                                    }
                                }
                                $tbl1 .=  '</td>';

                                $tbl1 .= '<td>';
                                if (count($CMFACISNO)>0) {
                                    foreach ($CMFACISNO as $value) {
                                        $tbl1 .= $value.'<br>';
                                    }
                                }
                                $tbl1 .=  '</td>';

                                $tbl1 .= '<td>';
                                if (count($DSTBMODELNAME)>0) {
                                    foreach ($DSTBMODELNAME as $value) {
                                        $tbl1 .= $value.'<br>';
                                    }
                                }
                                $tbl1 .=  '</td>';

                                $tbl1 .= '<td>';
                                if (count($DSTBFACISNO)>0) {
                                    foreach ($DSTBFACISNO as $value) {
                                        $tbl1 .= $value.'<br>';
                                    }
                                }
                                $tbl1 .=  '</td>';

                                $tbl1 .= '</tr>
                            </table>
                        </td>
                        <td style="width:30%;border-left:0px solid white;">
                            <table border="1">
                                <tr>
                                    <td colspan="2" style="text-align:center">客戶簽名</td>

                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align:center">';
                                    $signImage = '';
                                    if ($mcustSignUrl) {
                                        $signImage = '<img src="'.$mcustSignUrl.'" height="50px" style="padding: 0 20px 0 20px;">';
                                    }
                                    $tbl1 .= $signImage.'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align:center">工程人員簽名</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align:center">';
                                    $signImage = '';
                                    if ($mengineeSignUrl) {
                                        $signImage = '<img src="'.$mengineeSignUrl.'" height="50px" style="padding: 0 20px 0 20px;">';
                                    }
                                    $tbl1 .= $signImage.'</td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align:center">完成工時</td>
                                </tr>
                                <tr>
                                    <td>1.車程時間(起)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>2.車程時間(迄)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>3.施工時間(起)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>4.施工時間(迄)</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align:center">維修代碼</td>
                                </tr>
                                <tr>
                                    <td>故障原因一</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>故障原因二</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>故障原因三</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="text-align:center">退單原因</td>
                                </tr>
                                <tr>
                                    <td></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>';

                // <table border="1" cellpadding="0">
                //     <tr>
                //         <td style="">
                //         工程維修備註：<br>


                //         </td>
                //     </tr>
                // </table>

                // ';



                $pdf->writeHTML($tbl1, true, false, false, false, '');

                /******維修單 END******/
            }


            // 加密
            $pdf->SetProtection(array('print','modify'),'0000','0000',0);

            $fileName = $worksheet.'.pdf';

            $directory = config('filesystems.disks.upload.root')."/".$order_info->CustID."_".date("Ymd",strtotime($order_info->BookDate));

            if (!is_dir($directory)) {
                mkdir($directory,0777,true);
                chmod($directory,0777);
            }

            $full_path = $directory.'/'.$fileName;

            $pdf->Output($full_path, 'F');

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
                'data' => $full_path,
                'run' => json_encode($run),
                'date' => date('Y-m-d H:i:s')
            );




        } catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $p_data = array(
                'code' => '0400',
                'status' => 'error',
                'meg' => '資料錯誤',
                'data' => '',
                'date' => date('Y-m-d H:i:s')
            );

        }


        return $p_data;
        // $pdf->Output($fileName.'.pdf','I');
        // $full_path = "/home/www-devp/ewo/public/upload/720_A2020100000071/".$fileName;

        // $pdf->Output($full_path, 'F');

    }

    private function classifyCharge($orderCharge)
    {
        $data = array();

        foreach ($orderCharge as $charge) {
            $serviceName = data_get($charge,'ServiceName');
            $db = data_get($charge,'DB');

            $amt = (int)data_get($charge,'BillAmt');

            if ($db == '3200') {

                $companyNo = data_get($charge,'CompanyNo');
                $workSheet = data_get($charge,'WorkSheet');
                $chargeName = data_get($charge,'ChargeName');
                $infoData = array(
                    'so' => $companyNo,
                    'worksheet2' => $workSheet,
                    'chargename' => $chargeName
                );

                $info = $this->OrderRepository->getOrderCharge($infoData);

                $FromDate = '';
                $TillDate = '';
                if (count($info) > 0) {
                    $FromDate = data_get($info[0],'FromDate');
                    $TillDate = data_get($info[0],'TillDate');
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

            }

            data_set($charge, 'BillAmt', $amt);
            $data[$serviceName][] = $charge;
        }



        return $data;
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

    private function tenthPage($borrowming,$retrieve)
    {
        $p = '
        <div>
            <table border="1" style="text-align:center;">
                <tr>
                    <td>借用設備</td>
                    <td colspan="2">纜線數據機</td>
                    <td colspan="2">數位機上盒</td>
                    <td colspan="3">智能家庭</td>
                </tr><tr>
                    <td>主機功能</td>
                    <td>單埠</td>
                    <td>WiFi</td>
                    <td>基本型</td>
                    <td>雙向型</td>
                    <td>攝影機</td>
                    <td>門窗感應器</td>
                    <td>煙霧感測器</td>
                </tr><tr>
                    <td>主機價值</td>
                    <td>1,500 元／台</td>
                    <td>2,500 元／台</td>
                    <td>2,000 元／台</td>
                    <td>3,000 元／台</td>
                    <td>1,500 元／台</td>
                    <td>500 元／台</td>
                    <td>800 元／台</td>
                </tr><tr>
                    <td>借用數量</td>
                    <td>'.$borrowming->Cable_modem_port.'</td>
                    <td>'.$borrowming->Cable_modem_two_way.'</td>
                    <td>'.$borrowming->Basic_digital_set_top_box.'</td>
                    <td>'.$borrowming->Digital_set_top_box_two_way_type.'</td>
                    <td>'.$borrowming->camera.'</td>
                    <td>'.$borrowming->Door_and_window_sensor.'</td>
                    <td>'.$borrowming->Smoke_detector.'</td>
                </tr><tr>
                    <td>取回數量</td>
                    <td>'.$retrieve->get_Cable_modem_port.'</td>
                    <td>'.$retrieve->get_Cable_modem_two_way.'</td>
                    <td>'.$retrieve->get_Basic_digital_set_top_box.'</td>
                    <td>'.$retrieve->get_Digital_set_top_box_two_way_type.'</td>
                    <td>'.$retrieve->get_camera.'</td>
                    <td>'.$retrieve->get_Door_and_window_sensor.'</td>
                    <td>'.$retrieve->get_Smoke_detector.'</td>
                </tr>
            </table>
        </div>
        <div>
            <table border="1" style="text-align:center;">
                <tr>
                    <td>纜線數據機配件</td>
                    <td>無線寬頻分享器</td>
                    <td>變壓器電源線</td>
                    <td>乙太網路線</td>
                    <td>USB無線寬頻網卡</td>
                    <td>&nbsp;</td>
                </tr><tr>
                    <td>配件價值</td>
                    <td>650 元／台</td>
                    <td>300 元／個</td>
                    <td>150 元／條</td>
                    <td>600 元／個</td>
                    <td>&nbsp;</td>
                </tr><tr>
                    <td>借用數量</td>
                    <td>'.$borrowming->Cable_accessories_wireless_anti_frequency_sharing_device.'</td>
                    <td>'.$borrowming->Cable_accessories_transformer_power_cord.'</td>
                    <td>'.$borrowming->Cable_accessories_Ethernet_cable.'</td>
                    <td>'.$borrowming->Cable_accessories_USB_wireless_anti_frequency_network_card.'</td>
                    <td>&nbsp;</td>
                </tr><tr>
                    <td>取回數量</td>
                    <td>'.$retrieve->get_Cable_accessories_wireless_anti_frequency_sharing_device.'</td>
                    <td>'.$retrieve->get_Cable_accessories_transformer_power_cord.'</td>
                    <td>'.$retrieve->get_Cable_accessories_Ethernet_cable.'</td>
                    <td>'.$retrieve->get_Cable_accessories_USB_wireless_anti_frequency_network_card.'</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        <div>
            <table border="1" style="text-align:center;">
                <tr>
                    <td>數位機上盒配件</td>
                    <td>遙控器</td>
                    <td>HDMI</td>
                    <td>AV 線(1.SM)</td>
                    <td>色差線(1.SM)</td>
                    <td>變壓器電源線</td>
                </tr><tr>
                    <td>配件價值</td>
                    <td>300 元／支</td>
                    <td>200 元／條</td>
                    <td>25 元／條</td>
                    <td>170 元／條</td>
                    <td>300 元／個</td>
                </tr><tr>
                    <td>取回數量</td>
                    <td>'.$borrowming->Set_top_box_accessories_remote_control.'</td>
                    <td>'.$borrowming->Set_top_box_accessories_HDI.'</td>
                    <td>'.$borrowming->Set_top_box_accessories_AV_cable.'</td>
                    <td>'.$borrowming->Set_top_box_accessories_Chromatic_aberration_line.'</td>
                    <td>'.$borrowming->Set_top_box_accessories_transformer_power_cord.'</td>
                </tr><tr>
                    <td>借用數量</td>
                    <td>'.$retrieve->get_Set_top_box_accessories_remote_control.'</td>
                    <td>'.$retrieve->get_Set_top_box_accessories_HDI.'</td>
                    <td>'.$retrieve->get_Set_top_box_accessories_AV_cable.'</td>
                    <td>'.$retrieve->get_Set_top_box_accessories_Chromatic_aberration_line.'</td>
                    <td>'.$retrieve->get_Set_top_box_accessories_transformer_power_cord.'</td>
                </tr>
            </table>
        </div>
        <div>
            <table border="1" style="text-align:center;">
                <tr>
                    <td>數位機上盒配件</td>
                    <td>智慧卡</td>
                    <td>外接式硬碟</td>
                    <td>USB無線寬頻綱卡</td>
                    <td>ATV 機上盒</td>
                    <td>藍芽遙控器</td>
                </tr><tr>
                    <td>配件價值</td>
                    <td>250 元／張</td>
                    <td>2,000 元／台</td>
                    <td>600 元／個</td>
                    <td>3,000元／台</td>
                    <td>500元／支</td>
                </tr><tr>
                    <td>借用數量</td>
                    <td>'.$borrowming->Set_top_box_accessories_smart_card.'</td>
                    <td>'.$borrowming->Set_top_box_accessories_external_hard_disk.'</td>
                    <td>'.$borrowming->Set_top_box_accessories_USB_wireless_anti_frequency_network_card.'</td>
                    <td>'.$borrowming->Set_top_box_accessories_ATV_set_top_box.'</td>
                    <td>'.$borrowming->Set_top_box_accessories_Bluetooth_remote_control.'</td>
                </tr><tr>
                    <td>取回數量</td>
                    <td>'.$retrieve->get_Set_top_box_accessories_smart_card.'</td>
                    <td>'.$retrieve->get_Set_top_box_accessories_external_hard_disk.'</td>
                    <td>'.$retrieve->get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card.'</td>
                    <td>'.$retrieve->get_Set_top_box_accessories_ATV_set_top_box.'</td>
                    <td>'.$retrieve->get_Set_top_box_accessories_Bluetooth_remote_control.'</td>
                </tr>
            </table>
        </div>
        <div>
            <table border="1" style="text-align:center;">
                <tr>
                    <td>智能家庭配件</td>
                    <td>變壓器電源線</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr><tr>
                    <td>配件價值</td>
                    <td>300 元／個</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr><tr>
                    <td>借用數量</td>
                    <td>'.$borrowming->Smart_home_accessories_transformer_power_cord.'</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr><tr>
                    <td>取回數量</td>
                    <td>'.$retrieve->get_Smart_home_accessories_transformer_power_cord.'</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </div>
        ';


        $page10 = [
            '<p>1、乙方借用之設備僅限申裝同址同人使用，不得移往他處，乙方使用期間應善盡保管之義務，如有不當處置、使用，致</p>',
            '<p style="text-indent:20px">本借用設備毀損、滅失、減少正常功能、遭扣押丶或為第三人佔有時，乙方慮負完全損害賠償之責，乙方並同意賠償</p>',
            '<p style="text-indent:20px">設備主機及配件價值如上。</p>',
            '<p>2、乙方使用期間，設備因可歸責乙方事由發生故障時致需送修時，甲方得酌收維修材料及工資。</p>',
            '<p>3、乙方停用/終止甲方有線電視（含數位加值）或 光纖寬頻網路之服務後 7 日內，應至甲方櫃檯完整歸回借用之設備，並經甲方</p>',
            '<p style="text-indent:20px">確認型號／序號無誤者，甲方將開立結清退租之證明，提醒您，如未歸還恐將構成刑法侵占之虞，請務必注意。</p>',
            '<p>4、退訂戶須憑【設備借用／取回保管單】丶【身分證】丶【印章】至本公司櫃檯辦理退租，請退訂戶於設備取回七日內至</p>',
            '<p style="text-indent:20px">本公司櫃檯辦理退費。（如有委託人要提供其身分證及印章）。</p>',
            '<p>5、退訂設備之主機及配件需完好無缺損，如發生損害或遺失時，本公司得自保證金及退還費用中酌扣材料費用。</p>',
        ];

        foreach ($page10 as $t) {
            $p .= $t;
        }

        return $p;
    }

    private function firstPage($title)
    {

        $p = '<style>
                .container {
            font-size: 0.1px;
            line-height: 1;
        }
        .page1-style {
            // display: table-row;
            line-height: 1.2;
        }
        .page2-style {
            display: table-row;
            /*font-size: xx-small;*/
            /*font-weight: 100;*/
            /*line-height: 1;*/
            /*letter-spacing: -1px;*/
        }
        .page2-style-head {
        }
        .page2-style-h1 {
            text-align: center;
            display: inherit;
            font-size: 20px;
            font-weight: bold;
        }
        </style>';
        $page1 = [
            '<p>'.$title.'</p>',
            '<p>茲有'.$title.'（以下簡稱甲方）提供貴用戶（以下簡稱乙方）收視、收聽及使用有線電視視訊及數位加值服務，雙方同意遵守下列條款：</p>',
            '<p>第一條   當事人之名稱、電話及住所</p>',
            '<p style="text-indent:20px">業者：（甲方）系統名稱：'.$title.'</p>',
            '<p style="text-indent:20px">代表人：揭朝華 營業地址同訂戶服務中心地址，乙方相關資料如本單據正面所示</p>',
            '<p>第二條   服務種類</p>',
            '<p style="text-indent:20px">一、「有線電視數位基本頻道服務」（下稱基本頻道服務）係甲方以數位訊號方式透過數位機上盒（簡稱DSTB）提供乙方收視、收聽有線電視視訊之服務。</p>',
            '<p style="text-indent:20px">二、「有線電視數位加值服務」（下稱數位加值服務）係甲方基本頻道服務收視戶透過數位機上盒所提供之有線電視加值型付費數位服務（服務內容包含但不限於數位付費頻道、計次付費隨選視訊VOD、互動服務）及數位免費頻道，</p>',
            '<p style="text-indent:30px">詳如數位電視服務申裝書及本公司官網（http://www.nvwtv.com.tw/之「頻道服務表」）所載，包括：</p>',
            '<p style="text-indent:30px">(一)數位付費頻道，係指於繳納基本頻道服務費用外，訂戶須定期支付數位付費頻道費用後，始可收視、收聽之頻道，訂戶訂購內容如正面申裝書所載。</p>',
            '<p style="text-indent:30px">(二)計次付費隨選視訊VOD，係指具雙向設備(依營運規劃模式，可能由本公司合作之第三人直接提供服務，以付費方式予第三人方式取得)數位付費頻道訂戶或DSTB 租用訂戶自DSTB 所連結顯示在電視之隨選視訊螢幕上所示可計</p>',
            '<p style="text-indent:40px">次付費之節目，自行操作確認訂購後始可計次收視、收聽之服務。</p>',
            '<p style="text-indent:30px">(三)互動服務，係指數位付費頻道訂戶或DSTB 租用訂戶定期支付該項互動服務費用後，始另提供予訂戶相關設備或服務介面（以下簡稱「互動服務設備」，依營運規劃模式，可能由本公司合作之第三人直接提供服務，以付費方式</p>',
            '<p style="text-indent:40px">予第三人方式取得）始可接收之其他影音、娛樂或資訊服務（互動服務包括：錄影管理服務PVR、卡拉OK 或遊戲等）。</p>',
            '<p>第三條   申請及異動程序</p>',
            '<p style="text-indent:20px">一、乙方申請本契約服務須填寫申裝書。申裝數位加值服務及選擇（或改用）月繳現付之基本頻道服務、辦理各項異動時，應依甲方規定辦理，於提供申裝證件時，檢附下列影本或以正本核資：</p>',
            '<p style="text-indent:30px">(一)自然人：擇一檢附有身分證號姓名地址(國民身分證為主；護照、外僑居留證或在台工作證影本等為輔)。</p>',
            '<p style="text-indent:30px">(二)公司行號：政府核發之登記事項卡等證明文件及代表人之身分證明文件。</p>',
            '<p style="text-indent:20px">二、乙方申裝基本頻道服務或數位加值服務後，如有異動或移機事項應辦理登記而未申辦，甲方得暫停提供該服務，或依本契約相關約定處理。若甲方於前述之期間繼續依照原契約提供服務期間租金、押金及本服務視、聽費用或互動服</p>',
            '<p style="text-indent:30px">務費用，乙方仍應照繳。</p>',
            '<p>第四條   契約有效期間、視訊服務頻道名稱</p>',
            '<p style="text-indent:20px">一、除非有依本契約終止之事由，契約有效期間係自裝機日起算，或依照本單正面所示收視期間（數位加值服務之有效期間各依其申裝書或派工單上所載期間為準）。甲方於契約有效期間內，依乙方選擇之服務，於約定處所（同本單正</p>',
            '<p style="text-indent:30px">面客戶裝機地址），接用服務之電視機（同本單正面裝機台數）台，提供收視服務。乙方選用數位加值服務者必須同時申裝基本頻道服務，如有終止或暫停使用基本頻道服務時，即視為提前終止或停止數位加值服務。甲方提供基本</p>',
            '<p style="text-indent:30px">頻道服務之基本頻道數(至少50個)（不包括0元付費頻道、免費頻道、試看推廣頻道、購物頻道、頻道總表專用頻道及重覆播送之頻道）。基本頻道表（頻道名稱，頻道授權契約到期日）請參閱數位電子節目表(EPG)所示。</p>',
            '<p style="text-indent:20px">二、甲方於契約有效期間內，如未依前項提供基本收視頻道表附件者，乙方得依據甲方所散發之基本收視頻道表主張權利。</p>',
            '<p style="text-indent:20px">三、基本頻道服務約滿前，當事人之一方已提出建議另定新約而未達成協議者，自約滿時起一個月內，甲方應與乙方進行協議。除乙方不同意與甲方進行協議者外，在一個月協議期間內，甲方應依當時所適用之契約條件供應節目、收</p>',
            '<p style="text-indent:30px">費。逾協議期間，雙方仍未達成協議簽訂新約者，本契約即行終止。</p>',
            '<p style="text-indent:20px">四、基本頻道服務約滿，雙方均未請求協議另定新約，甲方如繼續提供基本頻道服務，並將新節目表提供乙方，乙方未於一週內表示反對，仍繼續收視、收聽者，本契約自動展延一期。展延後契約條件如有變更，變更後契約條件有利於</p>',
            '<p style="text-indent:30px">乙方者，適用變更後之契約條件。</p>',
            '<p>第五條   繳費項目、金額及方式</p>',
            '<p style="text-indent:20px">一、服務費用：</p>',
            '<p style="text-indent:30px">(一)基本頻道之每戶收視費用：新台幣（下同）500 元；數位加值服務之訂購內容與每月收視費， 依申裝書、本單正面或甲方網站所載為準。</p>',
            '<p style="text-indent:30px">(二)裝機費單機費用：1,500 元，每一分機裝機費用：裝機時500 元／裝機後800元</p>',
            '<p style="text-indent:30px">(三)移機費：同戶移機：每機500 元，不同戶移機：每機800 元</p>',
            '<p style="text-indent:30px">(四)復機費：</p>',
            '<p style="text-indent:40px">1.乙方於本契約終止日起三個月後向甲方申請復機者，視為新裝機，比照一般裝機費收費(欠費拆機等事由，其後裝機適用一般裝機費收費)。</p>',
            '<p style="text-indent:40px">2.乙方於本契約終止日起三個月內（含）向甲方申請復機者，免收復機費。惟若乙方係因未依期 限繳交費用或逾越雙方約定範圍供自己或他人接受甲方視訊，經甲方拆機者，縱於拆機後三個月內（含）申請復機，除應先繳清欠</p>',
            '<p style="text-indent:50px">繳費用外，仍須比照一般裝機費收費。</p>',
            '<p style="text-indent:40px">3.乙方於本契約尚未終止且在收視有效期限內向甲方事前提出暫停收視之書面請求，甲方予以拆 機後三個月內（含）申請復機者免收復機費；超過三個月再申請復機者，復機費為200 元。</p>',
            '<p style="text-indent:30px">(五)數位機上盒：依據主管機關公布之收費標準或本契約第六條規定內容辦理。</p>',
            '<p style="text-indent:30px">(六)繳費期限及金額：</p>',
            '<p style="text-indent:40px">1.現付：當月繳基本頻道500元，2.季繳1,500元，半年繳3,000元，年繳6,000元(長繳期用戶依最新優惠方案，享數位套餐或CM優惠價，或裝機費減免)。</p>',
            '<p style="text-indent:40px">乙方對於繳費期限二個月以上訂戶之預收收視費用，已依主管機關公告之方式，於國泰世華商業銀行內湖分行成立履約保證準備金專戶，供主管機關不定期查核。</p>',
            '<p style="text-indent:30px">(七)如以社區或公寓大廈管理委員會之名義與甲方訂立本基本頻道服務契約者，屬於該社區或公寓大廈之住戶，亦得依本契約約定之事項行使乙方之權利。</p>',
            '<p style="text-indent:30px">(八)數位服務中，計次付費節目之訂購內容與價格，依電子選單內公告為準。</p>',
            '<p style="text-indent:30px">(九)乙方應於甲方完成數位服務收視設備（含DSTB 及「互動服務設備」）安裝當日，同時給付甲方安裝費用、第一期之收視費、租金、保證金/押金或互動服務費用。</p>',
            '<p style="text-indent:30px">(十)除另有約定外，數位加值服務之各項收費為預付制，乙方應於收到帳單後(例：email、TVMAIL 及簡訊帳務通知等，擇一之方式)，於帳單所載之繳別或繳期限內，依雙方所約定或帳單上所記載之方式及繳款週期繳費，並於每</p>',
            '<p style="text-indent:40px">週期起始之第1 日前預 繳該週期之所有費用。逾期五日（含以上）未為繳付時，甲方得暫停提供數位服務，停止時 間之原收視費或DSTB 租金費用等，乙方仍應繳納。</p>',
            '<p style="text-indent:30px">(十一)多元收視方案之收視內容，將依國家通訊傳播委員會備查核准內容，於甲方官網公告，提供多元視訊服務。</p>',
            '<p style="text-indent:20px">二、繳費方式：簡訊或實體帳單自行繳款、信用卡扣款、行動客服APP、銀行自動扣繳（金融機構ACH 媒體自動轉帳代收）、臨櫃代收（詳依繳款單所示，例：便利商店 7-11 、全家、 0K 、萊爾富）。甲方得改變上述金融機構、便</p>',
            '<p style="text-indent:30px">利商店等轉帳或代收服務機構，惟甲方應通知乙方。 基本頻道服務如未約定繳費期限、繳費方式者，則視同採當月繳，繳款方式由甲方依前項繳款方式中擇一定之，不足月者每日收視費以每月收視費三十分之一計算。</p>',
            '<p>第六條   數位機上盒之提供</p>',
            '<p style="text-indent:20px">一、如乙方申裝基本頻道服務或數位加值服務者，除雙方另有約定外，乙方得就押借及租用兩種方案中，擇一選用DSTB、雙向設備或「互動服務設備」。押借及租用方案如下：</p>',
            '<p style="text-indent:30px">(一)押借：乙方得以繳交押金方式向甲方借用DSTB或「互動服務設備」，甲方負責安裝、設定並提供維護(非甲方因素之設備障礙，相關維護費用或遙控器等換購設備費用由乙方負擔，詳甲方官網公告)。</p>',
            '<p style="text-indent:30px">(二)租用：乙方得單以繳交保證金及每月支付租金方式向甲方租用DSTB，以使用甲方所提供之計次付費隨選視訊VOD服務，甲方負責安裝、設定、提供維護並酌收費用(非甲方因素之設備障礙，相關維護費用或遙控器等換購設備費</p>',
            '<p style="text-indent:40px">用由乙方負擔，詳甲方官網公告)。</p>',
            '<p style="text-indent:20px">二、乙方向甲方租用／押借之DSTB、雙向設備或「互動服務設備」，於使用期間除應盡善良管理人之注意義務保管上開設備。未經甲方事前之書面同意，乙方不得擅自將上開設備出售、轉讓、出租、出借或其他任何負擔予第三人，亦</p>',
            '<p style="text-indent:30px">不得移離原安裝處所或提供他人使用，並不得對上開設備為任何改裝或更動，如有違反本項約定，甲方除得立即終止本服務外，如受有損害，並得向乙方求償。</p>',
            '<p style="text-indent:20px">三、乙方不得任意變更甲方之安裝，否則因而發生訊號中斷、收視不良或其他危險時，概由乙方自行負責，與甲方無涉，因此所生之修復費用由乙方負擔。</p>',
            '<p style="text-indent:20px">四、乙方如未經甲方同意而擅自移動裝機位置或提供他人使用時，甲方得終止提供本服務，並要求乙方立即返還DSTB 或「互動服務設備」予甲方。</p>',
            '<p style="text-indent:20px">五、乙方於基本頻道服務或數位加值服務終止或停止租用時，應於七日內將DSTB或「互動服務設備」攜至甲方營業處所服務櫃台歸還甲方，除正常使用狀態下所致之自然耗損外，DSTB或「互動服務設備」應有如同本服務契約起始時之</p>',
            '<p style="text-indent:30px">良好狀態與性能；乙方如於七日內未返還，或DSTB等設備毀損、滅失或任何人為因素致不堪用時，乙方應依市價賠償之。乙方如於七日內未賠償時，甲方得以保證金、押金抵充，並得向乙方請求損害賠償。</p>',
            '<p>第七條   收費標準調整及繳費方式變更</p>',
            '<p style="text-indent:20px">一、於本契約有效期限內，甲方經地方主管機關核定之基本頻道服務收費標準如有調整，乙方仍依第五條約定之應繳費用繳付。但如約定之應繳費用高於地方主管機關核定之（最高）基本頻道服務收費標準時，依地方主管機關核定之最</p>',
            '<p style="text-indent:30px">高基本頻道服務收費標準繳付。</p>',
            '<p style="text-indent:20px">二、乙方所約定之繳費期限及繳費方式如本單正面所載。</p>',
            '<p style="text-indent:20px">三、若數位服務各項費用於調整時，甲方將於獲得主管機關核准後將調整後之各項收費標準及節目單以連續五天於頻道中播出之方式通知乙方，並自次繳費期時生效。</p>',
            '<p style="text-indent:20px">四、前項契約繳費方式經約定後有變更時，乙方應於變更前十天通知甲方並填寫異動申裝書（數位服務為變更前三十天），甲方不得拒絕。</p>',
            '<p style="text-indent:20px">五、如乙方原採轉帳或信用卡扣繳方式或欲變更為轉帳或信用卡扣繳方式，則甲方得於次期辦理乙方之變更。</p>',
            '<p>第八條   節目完整性</p>',
            '<p style="text-indent:20px">一、甲方應依其所提供之節目表播放節目，並維持所提供頻道節目內容及畫面之完整性。但前開規定，於因公共利益或突發事件所必要，或發生有線電視系統經營者天然災害及緊急事故應變辦法所生情事之一者，不適用之。</p>',
            '<p style="text-indent:20px">二、甲方違反前項約定時，乙方得請求免除當日收視費（日收視費指月收視費之三十分之一，以下同）。</p>',
            '<p>第九條   營運及維修義務</p>',
            '<p style="text-indent:20px">乙方就基本頻道發生無法收視或收視不良狀況，甲方於接獲乙方通知時，應於24小時到修，並約定修復時間。倘無法收視或收視不良係因天然災害、不可抗力或其他不可歸責於甲方之誤失所致，甲方當竭力儘速修復，惟前開24小時到</p>',
            '<p style="text-indent:20px">修之限制在此不予適用。乙方同意提供甲方進入建築物及／或室內（倘若乙方係集合式住宅之收視戶時，則應採取合理行動使集合式住宅之管理委員會允許甲方進入），且為使甲方得以依約提供相關服務，乙方同意授權甲方於室內及室</p>',
            '<p style="text-indent:20px">外，安裝必要之相關設備，包括分接器、放大器、轉換器以及進線等。凡關於前開設備之裝設及／或甲方依照本契約提供之服務之合理需求下，乙方並同意不另外收費提供足夠之空間、電力（倘若乙方係集合式住宅之收視戶時，則應採</p>',
            '<p style="text-indent:20px">取合理行動使集合式住宅之管理委員會解決）。</p>',
            '<p>第十條   違反維修義務之責任</p>',
            '<p style="text-indent:20px">甲方違反前條到修時間或修復時間始行修復時，乙方得按日請求減少三十分之一之月收視費；如逾到修時間十天以上或逾修復時間十天以上始行修復時，乙方得請求免除當月收視費。</p>',
            '<p>第十一條 頻道更換或停止播送之責任</p>',
            '<p style="text-indent:20px">一、就頻道異動(包括基本頻道)，甲方應依國家通訊傳播委員會規定進行營運計劃中變更之頻道異動核准或備查後得更換或停止播送頻道。除購物頻道及數位加值服務之頻道外，甲方不得任意更換或全部、部分停止播送任一頻道。惟甲</p>',
            '<p style="text-indent:23px">方得因頻道授權契約調整更換頻道次序或因頻道授權契約到期而停止播送，而甲方應注意乙方權益之保障。</p>',
            '<p style="text-indent:20px">二、依前項約定，甲方得更換、調整或停止播送頻道時，應於前五天以書面或連續五天於該頻道中以播送方式（指連續五天於頻道總表及該頻道播出時間內每小時播送一次）通知乙方。</p>',
            '<p style="text-indent:20px">三、甲方違反前二項約定或未依有線電視系統經營者天然災害及緊急事故應變辦法等法規之規定時，乙方得請求減少五日之當月收視費或延展五日之收視、收聽及使用。惟國家通訊傳播委員會若訂有新規定，則依其規定為準</p>',
            '<p style="text-indent:20px">四、乙方於數位付費頻道更換或停止播出通知發布後五日內或更換之新頻道播出後五日內，得申請終止本數位加值服務或變更訂購項目，甲方將依實際服務提供期間按比例退還收視費。乙方如逾期未為通知而繼續收視、收聽或使用本數</p>',
            '<p style="text-indent:30px">位加值服務時，視為同意甲方所為之調整。</p>',
            '<p>第十二條 基本頻道頻道減少或停止播送之責任</p>',
            '<p style="text-indent:20px">因可歸責於甲方之事由而減少播送之頻道數或經中央主管機關予以頻道停播或為沒入之處分，致甲方所提供之基本頻道服務未達基本頻道數時，甲方應減收當月基本頻道服務收視費或為其他方式之賠償；如甲方所提供之基本頻道服務未</p>',
            '<p style="text-indent:20px">達基本頻道數三分之二，並達十天以上時，即應免除當月之基本頻道服務收視費用。</p>',
            '<p>第十三條 暫時停止服務</p>',
            '<p style="text-indent:20px">一、乙方於服務約定期間中，得向甲方申請暫時停止提供基本頻道服務，惟如暫停時間超過三個月始申請復機時，應依第五條約定支付復機費。</p>',
            '<p style="text-indent:20px">二、乙方依前項約定向甲方申請暫時停止提供基本頻道服務時，得同時以書面向甲方申請暫停提供數位服務，惟停止期間不得逾三個月，以月為單位並以一次為限。惟乙方如申裝或適用套餐優惠方案，則不得申請暫停服務。</p>',
            '<p>第十四條 訂戶基本資料之保密及利用</p>',
            '<p style="text-indent:20px">甲方僅得於履行契約之目的範圍內，使用乙方提供之各項基本資料。非經乙方書面同意，不得為目的範圍外之利用或洩漏。甲方蒐集、處理及利用前揭乙方之個人資料，應依「個人資料保護法」等規定辦理。</p>',
            '<p>第十五條 訂戶申訴專線</p>',
            '<p style="text-indent:20px">甲方應成立訂戶服務中心，地址為新北市永和區中和路345號16樓、電話號碼為(02)2165-3688，並指派專人 處理申訴案件。</p>',
            '<p>第十六條 契約終止及通知</p>',
            '<p style="text-indent:20px">一、乙方於契約有效期間內，得隨時以書面通知甲方終止有線電視基本頻道契約。惟將DSTB（含相關所屬配件）或「互動服務設備」交至甲方營業櫃檯，並結清相關費用，始完成終止本服務。</p>',
            '<p style="text-indent:20px">二、甲方得因下列原因終止提供數位加值服務，乙方並同意不向甲方請求賠償任何損害（含間接損害及所失利益等）：</p>',
            '<p style="text-indent:30px">(一)因法律、法院命令、行政規則或行政命令。</p>',
            '<p style="text-indent:30px">(二)因繼續提供本服務而須承受任何法律責任。</p>',
            '<p style="text-indent:30px">(三)因天然災害、不可抗力或其他非甲方得合理控制之因素，以致甲方無法繼續經營本服務。</p>',
            '<p style="text-indent:20px">三、本條第一項終止係因甲方違反本契約第四條第一項、第九條第一項而終止契約者，乙方並得請求相當月收視費二倍之懲罰性違約金。</p>',
            '<p style="text-indent:20px">四、甲方於乙方有下列情形時（即乙方違約時），得以書面或電話(例：簡訊、語音、電子郵件、TVMAIL、APP訊息推播，擇一之方式)通知乙方要求限期改正，假如乙方受甲方之通知後七日內仍未改正者，甲方得終止契約之全部或一</p>',
            '<p style="text-indent:30px">部並要求乙方給付收視費：</p>',
            '<p style="text-indent:30px">(一)乙方未依期限繳交費用。</p>',
            '<p style="text-indent:30px">(二)乙方逾越約定使用範圍，提供自己或他人使用。倘若乙方之任何違約情事造成甲方之損失或損害時，乙方除應依有線廣播電視法第五十四條規定負民事損害賠償責任外，並應補償甲方因此所產生之必要費用。</p>',
            '<p>第十七條 契約終止之損害賠償</p>',
            '<p style="text-indent:20px">本契約於甲方受撤銷許可處分確定、破產宣告及終止營業時終止。乙方因甲方受撤銷許可處分致收視、收聽基本頻道服務之權益產生損害時，乙方得請求一個月基本頻道服務收視費之賠償。甲方擬暫停或終止營業時，應於一個月前通知</p>',
            '<p style="text-indent:20px">乙方。甲方未盡通知義務時，乙方得請求一個月基本頻道服務收視費之賠償。</p>',
            '<p>第十八條 預付費用之返還</p>',
            '<p style="text-indent:20px">乙方通知甲方終止有線電視服務時應攜帶身分證、印章至甲方營業處所辦理退費手續。甲方應於終止日起十五日內無息償還之（依第五條月繳價計算退費）。如因可歸責甲方事由致逾期未償還者按年利率百分之十計算其利息。</p>',
            '<p>第十八條之一 系統經營者之保證</p>',
            '<p style="text-indent:20px">一、基本頻道服務：契約期間甲方已依中央主管機關公告之方式，於國泰世華商業銀行內湖分行成立履約保證準備金專戶提供保證，並報請中央主管機關備查，並供主管機關不定期查核。於甲方無法履行基本頻道服務契約義務時，就預</p>',
            '<p style="text-indent:20px">收未到期之收視費用，按契約存續期間比例退還予乙方。</p>',
            '<p style="text-indent:20px">二、甲方未依前項提供保證時，應以當月繳方式向乙方收取基本頻道服務收視費。</p>',
            '<p>第十九條 契約終止後設備之拆除</p>',
            '<p style="text-indent:20px">一、除乙方拒絕甲方進行拆機外，甲方應於基本頻道服務終止後一個月內將分接點至乙方屋內引戶線之纜線及設備拆除，乙方可恢復原有無線電視節目之收視、收聽；逾期不為拆除時，乙方得於自行拆除後，向甲方請求償還其所支出之</p>',
            '<p style="text-indent:20px">必要費用。但基本頻道服務係因可歸責乙方事由而終止者，不得請求償還。</p>',
            '<p style="text-indent:20px">二、甲方為前項恢復乙方原有無線電視節目之收視、收聽時，除基本頻道服務係因甲方違約而終止者外，甲方得向乙方請求支付必要器材費用。前項所稱之「纜線及設備」為甲方所有，除甲方事前書面同意外，乙方不得將該纜線及設備</p>',
            '<p style="text-indent:20px">借與他人使用。</p>',
            '<p>第二十條 著作權規範</p>',
            '<p style="text-indent:20px">一、就乙方使用有線電視視訊及數位服務，甲方無權亦未授權乙方得錄製電視節目供應業者於各電視頻道上公開播送之節目內容。</p>',
            '<p style="text-indent:20px">二、乙方須以供個人或其家庭觀賞之目的且在合理範圍內，而使用相關設備為電視節目內容之錄製（即乙方無論係錄製電視節目或觀賞錄製後之電視節目，都應遵守中華民國著作權法第五十一條之規範，不得就所錄製之電視節目內容進</p>',
            '<p style="text-indent:20px">行加密破解、流通或實體交易）。就乙方錄製於相關設備內之相關內容，甲方無權亦無義務確保該內容之保存。</p>',
        ];

        $p .= '<div id="page1" style="line-height:2.9px">';
        foreach ($page1 as $t) {
            $p .= $t;
        }
        $p .= '</div>';

        return $p;
    }

    private function secondPage($pdf)
    {

        $p = '';

        $page2 = [
            '<p style="">依據個人資料保護法（下稱「個資法」）第八條規定，向台端告知下列事項，請台端詳閱：</p>',
            '<p style="font-weight: bold;">一、蒐集之主體及目的：</p>',
            '<p style="text-indent:20px">為提供服務，<span style="font-weight: bold;">有線電視(依以上您申裝之區域)將蒐集</span>、保存及利用您所提供之「有線電視及</p>',
            '<p style="">機上盒加值服務」用戶資料，包括您與我們聯絡所提供之用戶資料(例：帳務、資訊流服務調查</p>',
            '<p style="">等)。用戶資料之保存與使用主要是用於提昇產品服務品質、加強個人化服務及停止服務後之服</p>',
            '<p style="">務產品訊息告知（含以電郵、簡訊、語音及視訊等方式提供適合您的服務及行銷訊息，例：有</p>',
            '<p style="">線電視匯流相關服務產品、視訊服務後之產品銷售訊息通知、節目及服務數位資訊流匯整等，</p>',
            '<p style="">並包括由本公司提供關係企業中嘉寬頻「有線電視寬頻上網服務」行銷資訊），未經您的同</p>',
            '<p style="">意，不會另外將您的用戶資料揭露於與本服務無關之第三人或非上述目的以外之用途。</p>',
            '<p style="text-indent:20px;font-weight: bold;">蒐集目的及項目</p>',
            '<p style="text-indent:20px">履行契約義務及行使契約權利</p>',
            '<p style="text-indent:20px">履行法定義務</p>',
            '<p style="text-indent:20px">消費者/客戶/會員管理服務</p>',
            '<p style="text-indent:20px">行銷</p>',
            '<p style="text-indent:20px">調查統計與研究分析(含滿意度及喜好頻道節目調查)</p>',
            '<p style="font-weight: bold;">二、蒐集之個人資料類別：</p>',
            '<p style="text-indent:20px">1.服務之基本資料：包括但不限於客戶本人（或其代表人）之姓名、出生年月日、身分證統</p>',
            '<p style="text-indent:30px">一編號、統一編號、代表人之相關資料(包括因設備租用所需之客戶證件影本)、通訊資</p>',
            '<p style="text-indent:30px">料</p>',
            '<p style="text-indent:20px">2.其他與您申辦的服務有關，或您在使用過程產生的資訊，例如我們指配給您的機上盒識別</p>',
            '<p style="text-indent:30px">碼、帳單紀錄、消費及繳費方式、收視紀錄、服務歷程紀錄，以及其他與您的帳戶相關</p>',
            '<p style="text-indent:30px">的個人資料。</p>',
            '<p style="text-indent:20px">3.帳務資料：付款相關資訊(信用卡卡號、銀行帳戶定扣帳號及戶名、電子支付帳號)</p>',
            '<p style="text-indent:20px">4.聯絡資料：其他足資辨識身分之證明文件、地址、電話及帳務相關等資料</p>',
            '<p style="text-indent:20px">5.特殊資格：因主管機關費率公告要求，如可適用低收入身分，欲享本公司費用補助減免，</p>',
            '<p style="text-indent:30px">本公司將依規定蒐集或註記資格(不申請或不適用者，本公司不蒐集)。</p>',
            '<p style="text-indent:20px">6.官網資料：若使用本公司網頁或行動客服APP，本公司將依情形使用網路Cookie等，若客</p>',
            '<p style="text-indent:30px">戶不願之網路Cookie之跟隨，請清理電腦、手機、平版等裝置之瀏覽紀錄或網站資料。</p>',
            '<p style="font-weight: bold;">三、個人資料蒐集、處理及利用之期間、地區、對象及方式：</p>',
            '<p style="text-indent:20px">(一) 期間：本公司會在您使用本服務（保有帳戶）的期間與地區內利用您的個人資料。當本</p>',
            '<p style="text-indent:45px">契約終止或解除（您不再使用本服務）後，我們會在法令要求或許可的範圍與期限內保</p>',
            '<p style="text-indent:45px">留及利用您的個人資料，並在該期限後，以無法識別您的身分之形式保存您使用本服務</p>',
            '<p style="text-indent:45px">期間所提供或產生的資料 (申裝書或異動資料紙本保存5年)。</p>',
            '<p style="text-indent:20px">(二) 地區對象：本國傳輸之接收所在地；本公司、業務委外機構、本公司之協力廠商、依法</p>',
            '<p style="text-indent:45px">之調查監理機關(包括檢調機關就特定個人之資料查詢)等。若本公司將您的個人資料提</p>',
            '<p style="text-indent:45px">供給受我們委託的第三人（例如行銷／分析／調查／廣告／公關業者、物流業者、金流</p>',
            '<p style="text-indent:45px">業者、資訊服務業者等），在受委託的範圍內協助我們達成蒐集目的。本公司會對受委</p>',
            '<p style="text-indent:45px">託的第三人執行必要的監督，以確保您的個人資料安全。</p>',
            '<p style="text-indent:20px">(三) 蒐集方式：進電CSR客服中心之蒐集、填寫申請書、契約書、官網資料填寫、臨櫃蒐集</p>',
            '<p style="text-indent:45px">等。</p>',
        ];

        $p .='<div id="page2" style="line-height:8.3px">';
        $p .='<p class="page2-style-head">106.06 初版；108.11.1 二版；109.02.1 三版；109.05.1 四版；109.07.7 五版</p>';
        $p .= '<div style="text-align:center;">';
        $p .='<p style="font-weight: bold;">HOME+ TV 有線電視 個人資料保護法之義務告知書(包括隱私權政策)</p>';
        $p .='<p style="font-weight: bold;">口吉隆口長德口萬象口麗冠口新視波口家和口北健口三冠王口雙子星口慶聯口港都</p>';
        $p .= '</div>';

        foreach ($page2 as $t) {
            $p .= $t;
        }

        $p .='</div>';

        return $p;
    }

    private function thirdPage()
    {
        $p = '';
        $page3 = [
            '<p style="text-indent:20px">(四)利用方式：</p>',
            '<p style="text-indent:45px">1.帳務及維持需要：每期帳務資訊推送通知(寄送帳單／催繳訊息至您的地址或電子郵</p>',
            '<p style="text-indent:50px">件信箱；藉由電話聯繫或簡訊通知您有關繳費／催繳之資訊)，或透過機上盒障礙代</p>',
            '<p style="text-indent:50px">碼之回傳分析等(分析您的資料以維持、保護、開發及增進我們的服務)。</p>',
            '<p style="text-indent:45px">2.行銷訊息：以您的各項聯繫方式向您提供行銷資訊，例如優惠方案、促銷活動、我</p>',
            '<p style="text-indent:50px">們 的其他服務，以及其他更適合您的服務方案等。</p>',
            '<p style="text-indent:45px">3.本公司得分析您藉由遙控器操作與府上雙向機上盒回傳之遙控器操作資料，並以統</p>',
            '<p style="text-indent:40px">計數據、趨勢或其他無法識別您的身分之形式產出結果。</p>',
            '<p style="font-weight: bold;">四、依據個資法第三條規定，台端就本公司保有 台端之個人資料得行使下列權利：</p>',
            '<p style="">（一）得向本公司臨櫃查詢、請求閱覽或請求製給複製本，而本公司依法得酌收必要成本費</p>',
            '<p style="text-indent:20px">用。</p>',
            '<p style="">（二）得向本公司請求補充或更正，惟依法台端應為適當之釋明，並提供為申裝人核資資訊。</p>',
            '<p style="">（三）得向本公司請求停止蒐集、處理或利用及請求刪除，惟依法本公司因執行業務所必須</p>',
            '<p style="text-indent:20px">者，得不依台端請求為之(包括稅務、視訊費，以及設備費用紀錄等)。</p>',
            '<p style="">（四）台端得不同意本公司提供關係企業中嘉寬頻「有線電視寬頻上網服務」行銷資訊。若您</p>',
            '<p style="text-indent:20px">不想再收到我們的訊息或用戶資訊需要更新等，請與客服(市話撥4128811)或本公司門</p>',
            '<p style="text-indent:20px">市聯絡，我們將由專人為您服務。</p>',
            '<p style="font-weight: bold;">五、蒐集目的以外的利用</p>',
            '<p style="text-indent:20px">本公司僅在蒐集目的之必要範圍內，依前述說明利用您的個人資料，惟以下情形除外 ：</p>',
            '<p style="text-indent:20px">法律明文規定者，例如：受司法機關或主管機關依法要求提供個人資料；為增進公共利益</p>',
            '<p style="text-indent:20px">所必要或為防止他人權益之重大危害，例如為偵測／預防詐欺或網路犯罪等違法行為；為</p>',
            '<p style="text-indent:20px">免除您的生命、身體、自由或財產上之危險；受公務機關或學術研究機構請託，基於公共</p>',
            '<p style="text-indent:20px">利益為統計或學術研究而有必要，以無法識別您的身分之形式，提供資料給該公務機關或</p>',
            '<p style="text-indent:20px">學術研究機構；或以可識別您的身分之形式提供資料，但該公務機關或學術研究機構保證</p>',
            '<p style="text-indent:20px">所產出並對外揭露之結果無法識別您的身分；依法得到您的同意，有利於您的權益)。</p>',
            '<p style="font-weight: bold;">六、收視使用行為之蒐集、處理及利用</p>',
            '<p style="text-indent:20px">機上盒頻道及節目之收視使用行為蒐集(包括轉台、點選行為之頻率、節目頻道收視類型</p>',
            '<p style="text-indent:20px">等)，經處理分析後(大數據方式)使用於</p>',
            '<p style="text-indent:20px">1.收視率統計</p>',
            '<p style="text-indent:20px">2.收視類型喜好統計</p>',
            '<p style="text-indent:20px">3.商品資訊商之露出建議(以依分析結果向您推薦我們提供的其他您可能有興趣或適合您</p>',
            '<p style="text-indent:30px">的商品／服務)。</p>',
            '<p style="text-indent:20px">4.頻道上下架之規劃、內容商及頻道商之購片或代理規劃等</p>',
            '<p style="text-indent:30px">若經收視使用行為之蒐集、處理及利用同意後，您仍可直接於府上機上盒(ATV機型)或進</p>',
            '<p style="text-indent:30px">電客服中心取消本同意。</p>',
            '<p style="font-weight: bold;">七、若拒絕提供相關個人資料</p>',
            '<p style="text-indent:20px">申裝書或異動之個人資料若未提供，本公司將無法完成必要之審核處理作業，將影響契約</p>',
            '<p style="text-indent:20px">之成立及服務之提供。</p>',
            '<p style="font-weight: bold;">八、本公司得修訂本告知書之內容</p>',
            '<p style="text-indent:20px">將於本公司網站上公告，或以簡訊或電郵等其他足以使台端知悉或可得知之方式告知台端</p>',
            '<p style="text-indent:20px">修訂內容。</p>',
            '<p><hr></p>',
        ];

        $p .='<div id="page3" style="line-height:8.3px">';
        $p .='<p class="page2-style-head">106.06 初版；108.11.1 二版；109.02.1 三版；109.05.1 四版；109.07.7 五版</p>';

        foreach ($page3 as $t) {
            $p .= $t;
        }

        $p .='</div>';

        return $p;
    }

    private function fivePage()
    {

        $p = '';
        $page5_1 = [
            '<p>為申請使用「中嘉寬頻股份有限公司寬頻網路服務」（以下簡稱本服務），用戶茲同意遵守下列各項約款</p>',
            '<p>第一條 提供證明文件</p>',
            '<p style="text-indent:20px">一、申請本服務時，申請人除為政府機關、公立學校及公營事業機構外，應提供下列證件</p>',
            '<p style="text-indent:30px">（一）自然人：身分證明文件。1於本國人者，應提供身分證影本及第二證件如全民健保卡／駕照／護照／軍人身分證等。2非本國人者，應提供境外居留證、護照影本或在台工作證影本。</p>',
            '<p style="text-indent:30px">（二）法人及非法人團體、商號 1政府主管機關核發之公司變更登記事項表或營利事業登記證或其他證明文件影本。2代表人之身分證明文件影本，並於申請書上加蓋公司大小章。</p>',
            '<p style="text-indent:20px">二、用戶委託代理人辦理申請時·除應提供本條第一項之證明文件外，代理人並應出示身分證正本及已得合法授權之資料或文件供本公司核對。</p>',
            '<p style="text-indent:20px">三、用戶申請本服務如有下列情形之一者，本公司得不受理申請</p>',
            '<p style="text-indent:30px">（一）用戶指定之裝機地址非本赧務之營業區域。</p>',
            '<p style="text-indent:30px">（二）用戶未符合本公司合作之有線罨視系統經營者就辦理本服務之申請／異動服務I繳費之相關規定。</p>',
            '<p style="text-indent:30px">（三）用戶提供不實資料／未依本公司規定辦理本服務之申請</p>',
            '<p style="text-indent:30px">（四）依法令規定應限制或禁止用戶之申請者。</p>',
            '<p>第二條 本服務之承租</p>',
            '<p style="text-indent:20px">一、本服務之経營區為本公司不定期於各營業場所或網頁上所公告之與本公司有合作關係之有線電親系統經營者經核可經營區為限。</p>',
            '<p style="text-indent:20px">二、用戶如終止與本公司合作之有線電視系統經營者間之收親契約時，本服務亦隨之終止，如未達本契約期間者，親為用戶提前終止。本服務終止時，用戶向本公司承租與本服務相關之產品及附加服務將一併終止。</p>',
            '<p style="text-indent:20px">三、乙方有關本服務之廣告或宣傳品，親為本契約之一部分。</p>',
            '<p style="text-indent:20px">四、用戶明瞭本服務僅提供個人或家庭使用，未經本公司書面同意，不得利用本服務從事任何營業行為。</p>',
            '<p>第三條 設備安裝及相關規定</p>',
            '<p style="text-indent:20px">一、為使本服務得以設立及運作，用戶明白並同意以下各點</p>',
            '<p style="text-indent:30px">（一）本公司得選定協力厰商配合本公司服務人員至用戶之指定裝機地點（以下簡稱「指定地點』）架設及安裝纜線及纜線數據機</p>',
            '<p style="text-indent:30px">（二）用戶必須自行具備相關設備，包括但不限於個人電腦及具有合法版權之相容作業系統等各項設備在內。</p>',
            '<p style="text-indent:20px">二、 造入許可</p>',
            '<p style="text-indent:30px">（一）用戶同意本公司之服務人員得因裝機、檢查、測試、維修、變更丶訪談、調査或移動各項設傭及器材等目的，造入用戶指定地點造行上述及其他必要行為，但所有行為皆必須在用戶同意之時間內造行。</p>',
            '<p style="text-indent:30px">（二）用戶保證其有權同意本公司之服務人員造入指定地點造行上迤及其他必要行為。倘若用戶並無權為上述之同意時，用戶同意賠償本公司因用戶無權為上述許可或為不實之上述許可及相關原因所遭受之一切損</p>',
            '<p style="text-indent:45px">害及相關費用。</p>',
            '<p style="text-indent:30px">（三）用戶同意於本公司履行本契約之必要範圍內，得無償利用用戶之土地、建物、管道設施等佈設纜線或各項設備以順利提供本服務。</p>',
            '<p style="text-indent:20px">三、重置或變更各項設備</p>',
            '<p style="text-indent:30px">用戶不得重新裝置或變更有關本服務之各項設備，但用戶得要求本公司及本公司之協力厰商造行，但用戶須支付上述重置或變更各項設備所發生之一切相關費用，如若因服務範圍所限或用戶提供之書面資料不完整</p>',
            '<p style="text-indent:30px">而無法更換時，本公司得不予受理。</p>',
            '<p style="text-indent:20px">四、數據機等各項設備之所有權及歸還</p>',
            '<p style="text-indent:30px">（一）有關本服務之各項設傭皆屬本公司所有，經本公司派員於用戶指定處安裝並經用戶確認已依用戶同意之方式造行線路牽引，完成安裝。用戶應盡善良管理人之注意義務保管各項設備，且用戶同意配合誓調翟</p>',
            '<p style="text-indent:45px">察機關辦案需求·僅於用戶申裝地址內使用纜線數據機等各項設備，用戶不得出售、轉讓、租賃或遷移全部或部分設備。</p>',
            '<p style="text-indent:30px">（二）用戶如欲終止使用本服務時，用戶需自行至本公司合作之有線電親經營區櫃擡返還各項設備，於確認未損壞後，本公司始將原保證金退回。如用戶未返還或各項設備全部或部分之遺失、遺竊及損害或因用戶</p>',
            '<p style="text-indent:45px">違反上述規定而發生一切有關之損失，用戶應負責賠償本公司。</p>',
            '<p style="text-indent:30px">（三）使用本服務期間，因用戶使用不當致發生纜線數據機（保固期間除外）或各項設備故障，用戶應立即通知本公司派員維修，本公司有權酌收維修費及車馬費，其金額依本公司另行訂定之維修服務收費標準，並</p>',
            '<p style="text-indent:45px">視實際修復情形定之，前述暨用由用戶當場以現金支付。</p>',
            '<p style="text-indent:20px">五、備份需要</p>',
            '<p style="text-indent:30px">用戶應將其軟體及資料自行作好完善的備份措施。本公司對於用戶之電腦及周邊設備、其軟體及資料以及其他相關設備所造成之所有損失及損壞不負任何責任。</p>',
            '<p>第四條 本服務費用標準</p>',
            '<p style="text-indent:20px">一、本服務之頻寬、各項收費標準、各項方案內容，均公告於本公司網站上(wwwens net.tw/enter/ ndex.php)費用均採預繳制。用戶應依本公司公佈之費率於首次裝機時或繳費通知單所定之期限內繳鈉全部費用，用戶</p>',
            '<p style="text-indent:30px">同意本公司就各項收費標準保留變更之權利，本服務之各項費用收暨標準應依本公司隨時公告於本公司網站或各合作之有線電親系統經營區之營業場所內之費率表為準。</p>',
            '<p style="text-indent:20px">二、本服務收費項目有1裝機暨 2設備保證金3連線服務費4設定費5移機費7異動暨8其他</p>',
            '<p style="text-indent:20px">三、費率如有調整時，本公司應於調整前以電子郵件或書面通知用戶，並於本公司合作之有線電税系統經營者櫃擡或網站公告之，用戶若不同意變更後之費率，則應於新費率生效後7日內以書面通知本公司停止其服務</p>',
            '<p style="text-indent:30px">，繳清相關費用並辦理終止手續，若未於期限內通知本公司，新費率生效日起之一切服務均依新費率計算之，且用戶有依新費率支付。</p>',
            '<p style="text-indent:20px">四、本服務各項服務費用於用戶因用戶積欠本公司或協力廠商費用，或因違反本契約之約定，或違反法令等被暫停使用者，其暫停使用期間，各項服務費用仍應照繳。</p>',
            '<p style="text-indent:20px">五、本公司所推出之各項優惠方案，不得與其他折扣或優惠併用。優惠方案期間享價格保障·優惠方案期間結束後恢復為當期一般價格。用戶選擇之優惠方案如同時提供贈品者，用戶不得要求抵扣或兌換現金，贈品遺</p>',
            '<p style="text-indent:30px">失或毀損時不另補發。因贈品數量有限，本公司保留更動贈品之權利。</p>',
            '<p style="text-indent:20px">六、用戶申請之本服務係本公司當時所推出之優惠方案，且約定使用本服務達一定期間者，不得任意中途終止之情形。倘該用戶於提前終止本服務或因用戶未依約續繳費用或違反本約任一規定經本公司終止本服務者，</p>',
            '<p style="text-indent:30px">用戶需返還因優惠方案所受有之利益及纜線數據機，並依約繳付違約金，且本公司並得追訴用戶賠償所受到之損害。</p>',
            '<p style="text-indent:20px">七、原合約期間已屆滿之用戶，欲參加年約優惠者，需以繳款單上所載期間優惠金額繳費，並同意遵守繳款單上年約特別約定事項及本合約之各項約定，如提前終止或違反本合約任一約定者，需補繳牌價金纇，及返回</p>',
            '<p style="text-indent:30px">贈品之等值現金。</p>',
            '<p>第五條 繳費方式及變更</p>',
            '<p style="text-indent:20px">一、用戶於收到帳單後，應於帳單所載之繳暨期限內，依雙方所約定或帳單上所記載之方式以現金繳納，雙方並同意月繳制者每月繳費一次；年繳制、半年繳制、季繳制及雙月繳，則為每週期始月份當月預繳該週期之</p>',
            '<p style="text-indent:30px">所有暨用。</p>',
            '<p style="text-indent:20px">二、付費方式之變更：用戶原約定之繳費方式如為（金融機構自動轉帳l或［信用卡自動代繳], 而欲變更為其他付款方式，或其他付款方式欲變更為［金融機構自動轉帳]或［信用卡自動代繳]者，應依帳單或申請</p>',
            '<p style="text-indent:30px">本服務所填具表單中所戳明之期限前通知本公司，並協同辦理相關程序·程序完備者該變更將於四十五個工作天之內生效，付款方式變更生效日前，用戶仍應依原約定之付款方式支付應付予本公司之所有款項。</p>',
            '<p style="text-indent:20px">三、資費方案之變更：如用戶有變更原資費方案之需，應依帳單或申請本服務所填具表單中所載明之方式向本公司提出申請。該項變更於本公司收到申請書之次月生效。</p>',
            '<p style="text-indent:20px">四、未能依帳單或申請本服務所填具表單中所載明之期限前為付暨方式及資費方案變更之通知者，其變更程序將順延至次月處理。</p>',
            '<p>第六條 本服務停用、欠暨、終止與退費</p>',
            '<p style="text-indent:20px">一、使用月轍制之用戶終止本服務時，應於預定終止日之三個工作天前以書面或傳真向本公司申請，並於申請書面到達本公司後三個工作日內I或用戶指定之日（仍須有三個以上之工作日）開始生效。</p>',
            '<p style="text-indent:20px">二、採預付雙月繳制／季徽制／半年繳制／年繳制之用戶及適用優惠方案之用戶若欲申請期前終止時，就已繳連線管及其他相關費用不得請求退款。</p>',
            '<p style="text-indent:20px">三、用戶如違反任一約款時，本公司有權暫停該用戶使用權，該違約用戶並應賠償本公司因此所受一切損害。</p>',
            '<p style="text-indent:20px">四、月繳制之用戶若欲期前終止時，本公司就未足月部分之連線費用將比例收取；用戶申請終止本服務時，須自行至本公司所指定之地點歸還相關用戶端設備；本契約之終止日以用戶至本公司提供之臨櫃歸還各項設備</p>',
            '<p style="text-indent:30px">之日為迄日，如有應退還之費用，本公司應於各項設備歸還日起四十五個工作天內通知用戶至櫃檯臨櫃以現金或匯款方式退還。</p>',
            '<p style="text-indent:20px">五、用戶申請終止使用或因欠費而被本公司暫停l終止提供服務，致用戶使用期間未滿所約定之最短使用期限者，用戶應補足未滿期間之各項費用。</p>',
            '<p style="text-indent:20px">六、用戶如未於繳款截止日前繳付各項應付暨用，如未繳交，本公司得以電話或語音或簡訊或書面等任一催繳方式通知用戶於期限內補繳；逾期仍未繳付時，本公司得暫停服務並繼續催繳，如仍未繳納時，本公司得逕</p>',
            '<p style="text-indent:30px">行終止本契約，並得依法追繳，且要求用戶返還各項設備，若客戶不自行至本公司合作之有線電視系統經營區櫃檯歸還各項設備，本契約終止後，本公司得以保證金充抵用戶積欠之各項費用，並就不足之金額逞繳</p>',
            '<p style="text-indent:30px">之。</p>',
            '<p style="text-indent:20px">七、用戶如因逾繳費期間而道本公司拆除外線後，申請恢復連線服務時，除繳清欠款並繳交連線費用外，應再繳納纜線數據之設定費及裝機費後，方可復訊。</p>',
            '<p>第七條 本服務中斷之處理</p>',
            '<p style="text-indent:20px">一、本公司系統基於維護或轉換需要，或因其他因素而停止或縮短運作時間時，本公司將於七日前以明顯之方式公告於網頁上並以電子郵件或其他適當方式通郟甲方。</p>',
            '<p style="text-indent:20px">二、用戶租用本服務，如因本公司之電信機線設備障礙、阻斷，以致發生錯誤、暹滯、中斷或不能傳遞致用戶連續十二小時以上無法遑線使用時，其停止通信期間，當月月租費應依下列規定予以扣減，並於次期帳單扣</p>',
            '<p style="text-indent:30px">抵，縱係不可抗力事由所致者，亦同。前項阻斷開始之時間，以本公司查覺或接到客戶通知之時間為準。但有事實足以證明實際開始阻斷之時間者，依實際開始阻斷之時間為準。</p>',
            '<p style="text-indent:20px">三、本公司如因情勢變更或服務需求，得暫停或終止全部或部分業務之經營，用戶不得異議或要求任何補償，但應於預定皙停或終止之日前三個月公告並通知用戶。用戶需至本公司辦理無息退還其溢繳暨用及終止租用</p>',
            '<p style="text-indent:30px">之手續。</p>',
        ];

        $p .='<div id="page5" style="line-height:2.8px">';

        foreach ($page5_1 as $t) {
            $p .= $t;
        }

        $p .='</div>';

        $p .= '
        <table border="1" style="text-align:center;" cellpadding="2">
            <tr>
                <td >連續阻斷時間</td>
                <td >12小時以上-未滿24小時</td>
                <td >24小時以上-未滿48小時</td>
                <td >48小時以上-未滿72小時</td>
                <td >72小時以上-未滿96小時</td>
                <td >96小時以上-未滿120小時</td>
                <td >120小時以上</td>
            </tr>
            <tr>
                <td >扣減下陷</td>
                <td >當月租費減收5%</td>
                <td >當月租費減收10%</td>
                <td >當月租費減收20%</td>
                <td >當月租費減收30%</td>
                <td >當月租費減收40%</td>
                <td >當月租費全免</td>
            </tr>
        </table>
        ';

        $page5_2 = [
            '<p >第八條 用戶責任</p>',
            '<p style="text-indent:20px">一、為維持系統正常運作，本公司有權過濾電腦病毒檔案及垃圾電子郵件，用戶不得異議。</p>',
            '<p style="text-indent:20px">二、用戶帳號註冊成功後，用戶不得變更或轉讓予第三人使用，並應自行防止連線帳號及密碼外洩，如用戶發生帳號密碼遭盜用、個人資料被竊取、竄改、毀損、滅失或洩漏等情事所生之一切損失，本公司不負賠償責</p>',
            '<p style="text-indent:30px">任。本項約定於用戶申請密碼重設時，亦適用之。</p>',
            '<p style="text-indent:20px">三、本公司提供用戶之e-mail電子郵遞服務及相關服務下之磁碟空間·僅供暫存資料，用戶應自行做好傭份．道免存放大量資料於主機上。</p>',
            '<p style="text-indent:20px">四、本公司對用戶所為之通知係以用戶於同意書上所填寫之聯絡資料為憑，用戶所提供之所有用戶資料如有變動時，應以電子郵件、郵件、傳真或電話通知本公司。用戶如以電話申請異動事項·本公司得詢問其個人資</p>',
            '<p style="text-indent:30px">料，確認無誤後即可辦理。如用戶怠於通知或所提供之資料有誤，因此對其權益造成任何影誓，應由用戶自行負責。</p>',
            '<p style="text-indent:20px">五、用戶明瞭於網路上可擷取之任何資料皆屢該資料提供者所有·非緤正式開放或合法授權，用戶不得I重自擷取或利用該等資料，否則應自負一切法律責任。且為保障著作權，本公司有權以契約、電子傳輸、自動偵測</p>',
            '<p style="text-indent:30px">系統或其他方式，告知用戶若有渉有侵權情事，本公司得依法終止全部或部分服務，並有權先行移除用戶違法或違約使用之部分，且不負任何賠償責任；用戶除需自負一切民／刑事法律責任及行政責任外，並應配合</p>',
            '<p style="text-indent:30px">本公司釐清相關事實及法律責任、如致本公司有和解、談判、仲裁及訴訟時，用戶應賠償本公司所受之損失。</p>',
            '<p style="text-indent:20px">六、本公司依用戶需求派員至用戶指定處所裝機完量時，將於裝機單上詳列用戶端電腦軟、硬體狀況，請求用戶簽名確認·用戶應予配合之。維修、退l拆機或移機時亦同。依前項約定方式確認後，用戶端電腦軟、硬體</p>',
            '<p style="text-indent:30px">如發生任何問題或有與本公司系統不相容之情形·概與本公司無涉。</p>',
            '<p style="text-indent:20px">七、為提升整體服務品質，用戶同意本公司得視管理之必要調整相關網路服務。</p>',
            '<p >第九條 用戶使用本服務規則</p>',
            '<p style="text-indent:20px">一、為維護整體網際網路資源之運用，用戶使用本服務時須遵守網路禮儀並遵守網際網路國際應用慣例，包括但不限於</p>',
            '<p style="text-indent:30px">（一）巖禁執行非服務主機所提供之任何程式。</p>',
            '<p style="text-indent:30px">（二）嚴禁干擾、入侵或破壞網際網路上任何系統或網路資源之企國或行為。</p>',
            '<p style="text-indent:30px">（三）用戶資料傳輸跨越其它網路時，仍須遵守其他網路之使用規範。</p>',
            '<p style="text-indent:20px">二、 用戶使用本公司所提供之服務不得有下列情形之一：</p>',
            '<p style="text-indent:30px">（一）利用網際網路散播電腦病毒或足以干擾電腦正常運作之程式。</p>',
            '<p style="text-indent:30px">（二）利用本公司所提供之個人免費使用網頁空間或電子郵件陳列、散佈或販賣具威脅恐嚇、誹謗、人身攻擊、侵犯他人隱私、猥褻、色情、破壞社會善良風俗或未經授權使用之文字、圖畫、聲音、影像或其他物</p>',
            '<p style="text-indent:45px">品。</p>',
            '<p style="text-indent:30px">（三）利用本服務造行法令規章所禁止之販售、推銷或仲介。</p>',
            '<p style="text-indent:30px">（四）蓄意破壞他人電子郵件信箱及有竊取、更改、破壞他人資訊、危害通信之情事。</p>',
            '<p style="text-indent:30px">（五）利用本服務於網路上收發、傳送未經收信人同意之電子郵件（不論是否經由本公司之郵件伺服器），因而造成浪費整體網路資源及加重本公司網路系統之負擅。用戶應定期收取電子郵件並閲後自行備份而後</p>',
            '<p style="text-indent:30px">刪除，本公司不負儲存電子郵件之責。用戶之郵件或個人免費使用網頁空間不得超過約定之容量。</p>',
            '<p style="text-indent:30px">（六）不遵守其他網際網路國際應用慣例、違反任何本契約或相關法令之規定事證明確、或其他有危害通信情事者。</p>',
            '<p >第十條 資訊保密義務</p>',
            '<p style="text-indent:20px">本公司依相關法令規定得將用戶在本公司登記之資料編印或建置用戶目錄，並於業務需要及法令規定之範圍內使用該等資料，且本公司因下列情形於符合個人資料保護法及相關法令規定，並以正式公文查詢，本公司得</p>',
            '<p style="text-indent:20px">對第三人揭露：1· 司法機關、監察機關或治安機關因偵查犯罪或調査證攄所需者。2· 其他政府機關因執行公權力並有正當理由所需者。3. 與公眾生命安全有關之機關（構）為緊急救助所需者。</p>',
            '<p >第十一條 本公司保留之權利</p>',
            '<p style="text-indent:20px">一、由於本公司所提供之服務專為網際網路接取服務，若用戶個人資料或電腦遭第三人不法之入侵、攻擊、破壞或擷取任何資料，致用戶所生之損害·本公司不負任何損害賠償責任。</p>',
            '<p style="text-indent:20px">二、本公司於各項廣告文案中所示速率皆為供裝後最高可達速率。網際網路連結為對外開放聯結之網路架構·其特殊之網路特性（即當客戶網路設備達接至網際網路時，會因客戶本身之電腦效能、網路分享器、無線傳輸</p>',
            '<p style="text-indent:30px">、分享軟體、防毒安全軟體及所遑接對方網站電腦之受限頻寬等因素），將影饗資訊接取速率。</p>',
            '<p style="text-indent:20px">三、本公司於合法範圍、主管機關許可或命令之前提下，保留對用戶停止提供服務之權利。</p>',
            '<p style="text-indent:20px">四、本公司保留對於有延暹付費或曾違反契約、法令等不良記錄之用戶，拒絕提供本服務之權利。</p>',
            '<p style="text-indent:20px">五、用戶同意本公司得發送相關之商業廑告及各種商品之促銷活動資料予用戶。</p>',
            '<p >第十二條 本契約修改</p>',
            '<p style="text-indent:20px">一、本約定書內容公告於本公司網站上(wwwens net.twlenter/mdex.php)• 本公司得隨時修改本約定之條款，修改後之約定條款將公布在網站·不另外個別通知。客戶如不能接受修改後之約定條款·應於公布之日起</p>',
            '<p style="text-indent:20px">三十日內通知本公司終止本業務·如未於該期限內終止，則視為客戶同意修改。</p>',
            '<p style="text-indent:20px">二、契約變更公告後之三十日內，用戶以書面向本公司申請終止者，應至本公司辦理終止手續·否則親為同意該變更或修正事項。惟符合下列情形之一者不得請求退款·如有期前終止違約金之約定者，並因依約定之數</p>',
            '<p style="text-indent:30px">額及方式支付違約金予本公司：</p>',
            '<p style="text-indent:30px">（一）關於為完成本約服務所租l借用之各項設備 （纜線數據機、網路卡及相關線材）。</p>',
            '<p style="text-indent:30px">（二）參加優惠方案之用戶期前終止者。</p>',
            '<p >第十三條 申訴服務</p>',
            '<p style="text-indent:30px">用戶不滿意本公司及其協力厰商提供之服務·除可撥叫本公司帳單與契約正面所示之服務專線外，亦得至本公司有合作關係之有線電親系統經營者之櫃檯、以電子郵件或書面申訴，本公司應即親實際情形依相關法令規</p>',
            '<p style="text-indent:30px">定處理。</p>',
            '<p >第十四條 準攄法與管轄法院</p>',
            '<p style="text-indent:20px">一、本契約條款如有疑義時·均依中華民國法律。</p>',
            '<p style="text-indent:20px">二、因本契約之解釋或執行所發生之訴訟，雙方同意以本公司主營業所所在地之法院為第一審管轄法院。</p>',
        ];

        $p .='<div id="page5" style="line-height:2.8px">';

        foreach ($page5_2 as $t) {
            $p .= $t;
        }

        $p .='</div>';


        return $p;
    }

    private function seventhPage()
    {
        $p = '';
        $page7 = [
            '<p style="">依據個人資料保護法（下稱「個資法」）第八條第一項規定，向台端告知下列事項，請台端詳閱：</p>',
            '<p style="">一、蒐集之目的：</p>',
            '<p style="text-indent:20px">為提供服務，我們將蒐集、保存及利用您所提供之「有線電視寬頻上網服務」用戶資料，包括您與我們聯絡所提供之用戶資料(例：帳務、資訊流服務調</p>',
            '<p style="">查等)。用戶資料之保存與使用主要是用於提昇產品服務品質、加強個人化服務及停止服務後之服務產品訊息告知(含以電郵、簡訊、語音及導網頁等方式</p>',
            '<p style="">方式提供適合您的服務及行銷訊息，並包括由本公司提供關係企業「數位天空有線電視服務」行銷資訊），未經您的同意，不會另外將您的用戶資料揭露</p>',
            '<p style="">於與本服務無關之第三人或非上述目的以外之用途。</p>',
            '<p style="text-indent:20px">蒐集目的及項目代號</p>',
            '<p style="text-indent:20px">履行契約義務及行使契約權利</p>',
            '<p style="text-indent:20px">履行法定義務</p>',
            '<p style="text-indent:20px">消費者/客戶/會員管理服務</p>',
            '<p style="text-indent:20px">電信業務/電信加值網路服務</p>',
            '<p style="text-indent:20px">行銷</p>',
            '<p style="text-indent:20px">調查統計與研究分析(含滿意度調查)</p>',
            '<p style="">二、蒐集之個人資料類別：</p>',
            '<p style="text-indent:20px">包括但不限於客戶本人（或其代表人）之姓名、出生年月日、身分證統一編號、統一編號、代表人之相關資料(包括依電信法蒐集之客戶雙證件影本)</p>',
            '<p style="">、通訊資料、付款相關資訊、其他足資辨識身分之證明文件、地址、電話及帳務相關等資料，詳如上述各相關業務之申請書、契約書、進電臨櫃之通知、</p>',
            '<p style="">電子發票中獎或授權書等。因符合低收入或身心障礙手冊而享本公司費用補助減免，本公司將依規定蒐集之執照或其他許可。</p>',
            '<p style="text-indent:20px">若使用本公司網頁或行動客服APP，本公司將依情形使用網路Cookie等，若客戶不願之網路Cookie之跟隨，請清理電腦、手機、平版等裝置之瀏覽</p>',
            '<p style="">紀錄或網站資料。</p>',
            '<p style="">三、個人資料蒐集、處理及利用之期間、地區、對象及方式：</p>',
            '<p style="text-indent:20px">(一) 期間：本公司會在您使用本服務（保有帳戶）的期間與地區內利用您的個人資料。當本契約終止或解除（您不再使用本服務）後，我們會在</p>',
            '<p style="text-indent:30px">法令要求或許可的範圍與期限內保留及利用您的個人資料，並在該期限後，以無法識別您的身分之形式保存您使用本服務期間所提供或產生</p>',
            '<p style="text-indent:30px">的資料 (申裝書或異動資料紙本保存5年)。</p>',
            '<p style="text-indent:20px">(二) 地區對象：本國傳輸之接收所在地；本公司、業務委外機構、本公司之協力廠商、依法之調查監理機關(包括檢調機關就特定IP使用之個人</p>',
            '<p style="text-indent:30px">資料查詢)等。若本公司將您的個人資料提供給受我們委託的第三人（例如行銷／分析／調查／廣告／公關業者、物流業者、金流業者、資</p>',
            '<p style="text-indent:30px">訊服務業者等），在受委託的範圍內協助我們達成蒐集目的。我們會對受委託的第三人執行必要的監督，以確保您的個人資料安全。</p>',
            '<p style="text-indent:20px">(三) 方式：以自動化機器，或其他非自動化方式所為之利用，或違約行為的預防、調查與權利行使等。</p>',
            '<p style="">四、依據個資法第三條規定，台端就本公司保有 台端之個人資料得行使下列權利：</p>',
            '<p style="text-indent:20px">（一）得向本公司查詢、請求閱覽或請求製給複製本，而本公司依法得酌收必要成本費用。</p>',
            '<p style="text-indent:20px">（二）得向本公司請求補充或更正，惟依法台端應為適當之釋明，並提供為申裝人核資資訊。</p>',
            '<p style="text-indent:20px">（三）得向本公司請求停止蒐集、處理或利用及請求刪除，惟依法本公司因執行業務所必須者，得不依台端請求為之(包括稅務、連線費，以及設備費用</p>',
            '<p style="text-indent:30px">紀錄等)。</p>',
            '<p style="text-indent:20px">（四）若您不想再收到我們的訊息或用戶資訊需要更新等，請與客服(市話撥4128811)或數位天空門市聯絡，我們將由專人為您服務。</p>',
            '<p style="text-indent:30px"></p>',
            '<p style="">五、蒐集目的以外的利用</p>',
            '<p style="text-indent:20px">本公司僅在蒐集目的之必要範圍內，依前述說明利用您的個人資料，惟以下情形除外 ：法律明文規定者，例如：受司法機關或主管機關依法要求</p>',
            '<p style="text-indent:20px">提供個人資料；為增進公共利益所必要或為防止他人權益之重大危害，例如為偵測／預防詐欺或網路犯罪等違法行為；為免除您的生命、身體、</p>',
            '<p style="text-indent:20px">自由或財產上之危險；受公務機關或學術研究機構請託，基於公共利益為統計或學術研究而有必要，以無法識別您的身分之形式，提供資料給該</p>',
            '<p style="text-indent:20px">公務機關或學術研究機構；或以可識別您的身分之形式提供資料，但該公務機關或學術研究機構保證所產出並對外揭露之結果無法識別您的身分；</p>',
            '<p style="text-indent:20px">依法得到您的同意，有利於您的權益)。</p>',
            '<p style="">六、流量使用之蒐集、處理及利用</p>',
            '<p style="text-indent:20px">為優化網際網路使用之良好體驗，本公司將就IP源之網路流量統計分析後(大數據方式)使用於網路互連等。</p>',
            '<p style="">七、若拒絕提供相關個人資料</p>',
            '<p style="text-indent:20px">申裝書或異動之個人資料若未提供，本公司將無法完成必要之審核處理作業，或將無法即時收到與本服務相關的必要資訊，將影響契約之成立及</p>',
            '<p style="text-indent:20px">服務之提供。</p>',
            '<p style="">八、本公司得修訂本告知書之內容</p>',
            '<p style="text-indent:20px">將於本公司網站上公告，或以簡訊或電郵等其他足以使台端知悉或可得知之方式告知台端修訂內容。</p>',
        ];

        $p .='<div id="page3" style="line-height:7.3px">';
        $p .='<p class="page2-style-head">106.06 初版；108.11.1 二版；109.02.1 三版；109.05.1 四版</p>';
        $p .= '<div style="text-align:center;">';
        $p .='<p style="font-weight: bold;">寬頻上網服務個人資料保護法第八條第一項之義務告知書(包括隱私權政策)</p>';
        $p .= '</div>';


        foreach ($page7 as $t) {
            $p .= $t;
        }

        $p .='</div>';

        return $p;
    }

    private function cmpdf($data,$checkCM,$InstAddrName)
    {
        $order_info = data_get($data,'order_info');
        $hometel = data_get($data,'hometel');
        $phonetel = data_get($data,'phonetel');
        $charges = data_get($data,'charges');
        $signUrl = data_get($data,'cmSignUrl');
        $PersonID = data_get($data,'PersonID');
        $showBill = data_get($data,'showBill');
        $MSContract2 = data_get($order_info,'MSContract2') ?? '';

        $worksheet = data_get($order_info,'WorkSheet');

        if (gettype($order_info->SubsCP) == 'string') {
            $IVR = (array)json_decode($order_info->SubsCP);
        } else {
            $IVR = (array)data_get($order_info,'SubsCP');
        }

        $cmIVR = '';
        if ($IVR && array_key_exists($worksheet, $IVR)) {

            $cmIVR = $IVR[$worksheet];
        }

        $tbl2 = '
        <table  border="1" style="width:100%;">
            <tr>
                <td style="width:44%;">客戶基本資料</td>
                <td style="width:15%;">派工類別</td>
                <td style="width:15%;">IVR簡碼</td>
                <td style="width:26%;">派工單序號</td>
            </tr>
            <tr>
                <td rowspan="2" style="width:44%;font-size:10px">
                    客戶編號：'.$order_info->CustID.' <br>
                    姓名：'.$order_info->CustName.' <br>
                    電話(家)：'.$hometel.' <br>
                    行動電話：'.$phonetel.' <br>
                    裝機地址：'.$InstAddrName.' <br>
                    收費地址：'.$InstAddrName.' <br>
                    大樓(社區)名稱： <br>
                    移機舊址：
                </td>
                <td style="height:40px;text-align:center;line-height:40px;">'.$order_info->WorkKind.'</td>
                <td style="height:40px;text-align:center;">'.$cmIVR.'</td>
                <td style="height:40px;text-align:center;line-height:40px;">'.$order_info->WorkSheet.'</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size:10px">
                    工程組別：<br>'.$order_info->WorkTeam.'('.$order_info->WorkerName.')  <br>
                    網路編號：'.$order_info->NetID.' <br>
                    下次收費日： <br>
                    方案別(合約起迄日)： <br>
                    <span style="font-size:6.2px">'.$order_info->SaleCampaign.'</span>
                </td>
                <td style="font-size:10px">
                    受理人:'.$order_info->CreateName.' <br>
                    受理日期時間:<br><span style="font-size:8px">'.$order_info->create_at.'</span><br>
                    預約日期時間:<br><span style="font-size:8px">'.$order_info->BookDate.'</span>
                </td>

            </tr>';
            // <tr>
            //     <td style="width:70%;border-top:1px solid black;">
            //         備註：
            //     </td>
            // </tr>
            $tbl2 .= '
        </table>
        <table border="1" cellpadding="0">
            <tr>
                <td style="width:10%">設備型號</td>
                <td style="width:18%">設備序號</td>
                <td style="width:21%">收費項目</td>
                <td style="width:23%">收費期間</td>
                <td style="width:9%">金額</td>
                <td colspan="2" style="width:19%">總應收金額</td>
            </tr>
            <tr>
                <td rowspan="8">
                </td>
                <td rowspan="8">
                </td>
                <td rowspan="8" style="font-size:7px;width:21%;line-height: 100%;">
                ';

                foreach ($charges as $key => $charge) {

                    if ($key != '2 CM' && $key != 'C HS') {
                        continue;
                    }

                    foreach ($charge as $value) {
                        $chargeName = data_get($value,'ChargeName');

                        $tbl2 .= '<p style="line-height:5px;">'.$chargeName.'</p>';
                    }
                }

                $tbl2 .= '
                    <br>
                </td>
                <td rowspan="8" style="font-size:7px;width:23%;line-height: 100%;">
                ';

                foreach ($charges as $key => $charge) {

                    if ($key != '2 CM' && $key != 'C HS') {
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
                        }

                        $tbl2 .= '<p style="line-height:6px;">'.$chargeDate.'</p>';
                    }

                }

                $tbl2 .= '
                    <br>
                </td>
                <td rowspan="8" style="font-size:10px;width:9%;text-align:right;line-height: 100%;">
                ';

                $cmAmt = 0;
                foreach ($charges as $key => $charge) {

                    if ($key != '2 CM' && $key != 'C HS') {
                        continue;
                    }

                    foreach ($charge as $value) {
                        $billAmt = data_get($value,'BillAmt');
                        if (empty($billAmt)) {
                            $billAmt=0;
                        }

                        $tbl2 .= '<p style="line-height:5px;">'.(int)$billAmt.' </p>';
                        $cmAmt +=(int)$billAmt;
                    }

                }

                $tbl2 .= '
                    <br>
                </td>
                <td colspan="2">
                    $'.$cmAmt.'
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    總實收金額
                </td>
            </tr>
            <tr>
                <td>本票</td>
                <td>金額</td>
            </tr>
            <tr>
                <td></td>
                <td>'.$cmAmt.'</td>
            </tr>
            <tr>
                <td colspan="2">
                    工程人員
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    '.$order_info->WorkerName.'
                </td>
            </tr>
            <tr>
                <td colspan="2">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                </td>
            </tr>
        </table>
        <table border="1" style="width:100%;font-size:7px">
            <tr>
                <td rowspan="2" style="width:38%">備註二：'.$order_info->MSComment1.'</td>
                <td style="width:20%">舊吊牌編碼</td>
                <td style="width:15%"></td>
                <td style="width:27%">設備/贈品/證件繳交確認</td>
            </tr>
            <tr>
                <td style="width:20%;line-height:100%;text-align:center">
                    <p style="">未完工填寫代碼</p>
                    <p style="">(完工貼吊牌)</p>
                    <br>
                </td>
                <td style="width:15%">
                </td>
                <td style="line-height: 100%;">';

                $checkId = empty(data_get($checkCM,'cm_check_id')) ? '口' : '[v]';
                $checkHealth = empty(data_get($checkCM,'cm_check_health')) ? '口' : '[v]';
                $checkDriver = empty(data_get($checkCM,'cm_check_driver')) ? '口' : '[v]';
                $checkDriverRem = data_get($checkCM,'cm_check_driver_desc');
                $checkCompany = empty(data_get($checkCM,'cm_check_company')) ? '口' : '[v]';
                $checkOther = empty(data_get($checkCM,'cm_check_other')) ? '口' : '[v]';
                $checkOtherRem = data_get($checkCM,'cm_check_other_desc');

                $tbl2 .= '
                    <p style="line-height:5px;">'.$checkId.'身分證正反面影本</p>
                    <p style="line-height:5px;">'.$checkHealth.'健保卡 </p>
                    <p style="line-height:5px;">'.$checkDriver.'駕照影本('.$checkDriverRem.')</p>
                    <p style="line-height:5px;">'.$checkCompany.'公司變更登記事項表</p>
                    <p style="line-height:5px;">'.$checkOther.'其他 '.$checkOtherRem.'</p>
                    <br>
                </td>
            </tr>
            <tr>
                <td style="width:100%;font-size:5px">
                    特約條款：
                    <span>'.$MSContract2.'
                    </span>
                </td>
            </tr>
        </table>
        <table border="1">';

        $checkInvoice = empty(data_get($checkCM,'cm_check_invoice')) ? '口' : '[v]';
        $checkLegal = data_get($checkCM,'cm_check_legal');
        $checkEmail = data_get($checkCM,'cm_check_title');
        $checkEquipment = empty(data_get($checkCM,'cm_check_equipment')) ? '口' : '[v]';
        $checkNotest = empty(data_get($checkCM,'cm_check_notest')) ? '口' : '[v]';
        $checkStandalone = empty(data_get($checkCM,'cm_check_standalone')) ? '口' : '[v]';
        $checkStandaloneRem = data_get($checkCM,'cm_check_standalone_desc');
        $checkNotewsStandaloe = empty(data_get($checkCM,'cm_check_notest_standalone')) ? '口' : '[v]';
        $checkNotewsStandaloeRem = data_get($checkCM,'cm_check_notest_standalone_desc');
        $checkTest = empty(data_get($checkCM,'cm_check_equipmentdiscord_test')) ? '口' : '[v]';
        $checkTestRem = data_get($checkCM,'cm_check_equipmentdiscord_test_desc');
        $checkDomicile = empty(data_get($checkCM,'cm_check_domicile')) ? '口' : '[v]';

        $signImage = '';
        if ($signUrl) {
           $signImage = '<img src="'.$signUrl.'" height="50px" tyle="padding: 0 20px 0 20px;">';
        }

        $str = $order_info->CompanyNo == '209'? '寶島聯網股份有限公司' : '中嘉寬頻股份有限公司';
        $tbl2 .= '
            <tr>
                <td style="font-size:6px;">
                    本人茲確認及同意 1.本申裝方案及抨約條款內容 2.申請書背面之寬頻連線服務契約 3.申裝設備用戶須附上雙證（身份證影本十駕照或健保卡）正反面影本 4.貴公司保護及使用用戶資料權益聲明:為提供服務我們將保存及使用您所提供之「寬頻上網及加值服務」用戶資料，包括您與我們聯絡所提供之用戶資料（例 :帳務、資訊流服務調查等）。用戶資料之保存與使用主要是用於提昇產品服務品質、加強個人化服務及停止服務後之服務產品訊息告知（包括以電郵、簡訊、語音及視訊等方式提供適合您的服務及行銷訊息，例:有線電視匯流相關服務產品、視訊服務後之產品銷售訊息通知、節目及服務數位資訊流匯整等），未經您的同意，不會另外將您的用戶資料揭露於與本服務無關之第三人或非上述目的以外之用途。若您不想再收到我們的訊息或用戶資訊需要更新等，請與客服聯絡，我們將由專人為您服務。5申裝光纖寬頻網路服務，本人了解終止服務時至營業櫃擡返還之義務及法律責任。
                    <br>
                    此致 '.$str.'
                    <br>
                    <div>
                        <span style="font-size:10px;">申裝人(簽名):'.$signImage.'，法定代理人/代表人:'.$checkLegal.'</span>
                        <span style="font-size:8px;">（個人用戶請簽名，代理人請另附身分證正反面影本，未滿二十歲者，須附法定代理人之簽章及身分證明文件） E-MAIL:'.$checkEmail.' </span>
                    </div>
                </td>
            </tr>
        </table>
        <table border="1" >
            <tr>
                <td rowspan="2" style="width:5%;text-align:center">個人資料</td>
                <td style="width:20%;"></td>
                <td rowspan="2" style="width:5%;text-align:center">配件確認</td>
                <td rowspan="2" style="width:35%;"><span style="font-size:9px">'.$checkEquipment.'CM一台、乙太網路線一條、USB連接線一條、電源線一條、說明書及驅動程式光碟</span>
                </td>
                <td rowspan="2" style="width:3%;text-align:center">裝置點</td>
                <td rowspan="2" style="width:7%;"></td>
            </tr>
            <tr>
                <td >電話:</td>
                <td>戶籍地址: '.$checkDomicile.'同裝機地址</td>
            </tr>

        </table>
        <div>
            <span>'.$checkNotest.'未備電腦、未為供裝速率實測 '.$checkStandalone.'單機實測為 ___'.$checkStandaloneRem.'___ ，在供裝速率範圍 '.$checkNotewsStandaloe.'無法單機測試 ___'.$checkNotewsStandaloeRem.'____，</span>
            <br>
            <span>'.$checkTest.'電腦設備不合標準，單機實測為 ___'.$checkTestRem.'___。</span>
        </div>
        ';

        return $tbl2;
    }

    private function twmbbpdf($data,$checkTWMBB,$InstAddrName)
    {
        $order_info = data_get($data,'order_info');
        $hometel = data_get($data,'hometel');
        $phonetel = data_get($data,'phonetel');
        $charges = data_get($data,'charges');
        $signUrl = data_get($data,'twmbbSignUrl');
        $PersonID = data_get($data,'PersonID');
        $showBill = data_get($data,'showBill');
        $MSContract2 = data_get($order_info,'MSContract2') ?? '';

        $worksheet = data_get($order_info,'WorkSheet');

        if (gettype($order_info->SubsCP) == 'string') {
            $IVR = (array)json_decode($order_info->SubsCP);
        } else {
            $IVR = (array)data_get($order_info,'SubsCP');
        }

        $twmbbIVR = '';
        if ($IVR && array_key_exists($worksheet, $IVR)) {
            $twmbbIVR = $IVR[$worksheet];
        }


        $tbl2 = '
        <table  border="1" style="width:100%;">
            <tr>
                <td style="width:44%;">客戶基本資料</td>
                <td style="width:15%;">派工類別</td>
                <td style="width:15%;">IVR簡碼</td>
                <td style="width:26%;">派工單序號</td>
            </tr>
            <tr>
                <td rowspan="2" style="width:44%;font-size:10px">
                    客戶編號：'.$order_info->CustID.' <br>
                    姓名：'.$order_info->CustName.' <br>
                    電話(家)：'.$hometel.' <br>
                    行動電話：'.$phonetel.' <br>
                    裝機地址：'.$InstAddrName.' <br>
                    收費地址：'.$InstAddrName.' <br>
                    大樓(社區)名稱： <br>
                    移機舊址：
                </td>
                <td style="height:40px;text-align:center;line-height:40px;">'.$order_info->WorkKind.'</td>
                <td style="height:40px;text-align:center;">'.$twmbbIVR.'</td>
                <td style="height:40px;text-align:center;line-height:40px;">'.$order_info->WorkSheet.'</td>
            </tr>
            <tr>
                <td colspan="2" style="font-size:10px">
                    工程組別：<br>'.$order_info->WorkTeam.'('.$order_info->WorkerName.')  <br>
                    網路編號：'.$order_info->NetID.' <br>
                    下次收費日： <br>
                    方案別(合約起迄日)： <br>
                    <span style="font-size:6.2px">'.$order_info->SaleCampaign.'</span>
                </td>
                <td style="font-size:10px">
                    受理人:'.$order_info->CreateName.' <br>
                    受理日期時間:<br><span style="font-size:8px">'.$order_info->create_at.'</span><br>
                    預約日期時間:<br><span style="font-size:8px">'.$order_info->BookDate.'</span>
                </td>

            </tr>';

            // <tr>
            //     <td style="width:70%;border-top:1px solid black;">
            //         備註：
            //     </td>
            // </tr>
            $tbl2 .= '
        </table>
        <table border="1" cellpadding="0">
            <tr>
                <td style="width:10%">設備型號</td>
                <td style="width:18%">設備序號</td>
                <td style="width:21%">收費項目</td>
                <td style="width:23%">收費期間</td>
                <td style="width:9%">金額</td>
                <td colspan="2" style="width:19%">總應收金額</td>
            </tr>
            <tr>
                <td rowspan="8">
                </td>
                <td rowspan="8">
                </td>
                <td rowspan="8" style="font-size:10px;width:21%;line-height: 100%;">
                ';


                foreach ($charges as $key => $charge) {

                    if ($key != 'D TWMBB') {
                        continue;
                    }

                    foreach ($charge as $value) {
                        $chargeName = data_get($value,'ChargeName');

                        $tbl2 .= '<p style="line-height:5px;">'.$chargeName.'</p>';
                    }
                }

                // foreach ($chargeTWMBBInfo as $item) {
                //     $strIndex = strpos($item->ChargeName, ' ');
                //     $chargeName = substr($item->ChargeName, $strIndex);

                //     if (strpos(strtoupper($chargeName), 'PVR')) {
                //         $slipData['pvrStatus'] = true;
                //     }

                //     $tbl2 .= '<p style="line-height:5px;">'.$chargeName.'</p>';
                // }

                $tbl2 .= '
                    <br>
                </td>
                <td rowspan="8" style="font-size:10px;width:23%;line-height: 100%;">
                ';

                    foreach ($charges as $key => $charge) {

                        if ($key != 'D TWMBB') {
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
                            }

                            $tbl2 .= '<p style="line-height:5px;">'.$chargeDate.'</p>';
                        }

                    }

                    // foreach ($chargeTWMBBInfo as $item) {
                    //     $chargeDate = '  ';
                    //     if (!empty($item->RecvStart)) {
                    //         $chargeDate = date("Y-m-d",strtotime($item->RecvStart)) .' ~ '.$item->EndDate;
                    //     }

                    //     $tbl2 .= '<p style="line-height:5px;">'.$chargeDate.'</p>';
                    // }

                $tbl2 .= '
                    <br>
                </td>
                <td rowspan="8" style="font-size:10px;width:9%;text-align:right;line-height: 100%;">
                ';

                $cmAmt = 0;
                foreach ($charges as $key => $charge) {

                    if ($key != 'D TWMBB') {
                        continue;
                    }

                    foreach ($charge as $value) {
                        $billAmt = data_get($value,'BillAmt');
                        if (empty($billAmt)) {
                            $billAmt=0;
                        }

                        $tbl2 .= '<p style="line-height:5px;">'.(int)$billAmt.' </p>';
                        $cmAmt +=(int)$billAmt;
                    }

                }


                // foreach ($chargeTWMBBInfo as $item) {

                //     if ($item->PrintBillYN == 'Y') {
                //         $tbl1 .= '<p style="line-height:5px;">0 </p>';
                //         continue;
                //     }

                //     $tbl2 .= '<p style="line-height:5px;">'.(int)$item->RecvAmt.' </p>';
                //     $cmAmt +=(int)$item->RecvAmt;
                // }

                $tbl2 .= '
                    <br>
                </td>
                <td colspan="2">
                    $'.$cmAmt.'
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    總實收金額
                </td>
            </tr>
            <tr>
                <td>本票</td>
                <td>金額</td>
            </tr>
            <tr>
                <td></td>
                <td>'.$cmAmt.'</td>
            </tr>
            <tr>
                <td colspan="2">
                    工程人員
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    '.$order_info->WorkerName.'
                </td>
            </tr>
            <tr>
                <td colspan="2">
                </td>
            </tr>
            <tr>
                <td colspan="2">
                </td>
            </tr>
        </table>
        <table border="1" style="width:100%;font-size:10px">
            <tr>
                <td rowspan="2" style="width:38%">備註二：'.$order_info->MSComment1.'</td>
                <td style="width:20%">舊吊牌編碼</td>
                <td style="width:42%"></td>

            </tr>
            <tr>
                <td style="width:20%;line-height:100%;text-align:center">
                    <p style="">未完工填寫代碼</p>
                    <p style="">(完工貼吊牌)</p>
                    <br>
                </td>
                <td style="width:42%">
                </td>
            </tr>
            <tr>
                <td style="width:100%;font-size:5px;">
                    特約條款：
                    <span>'.$MSContract2.'
                    </span>
                    <p style="text-align: right;">C 促銷中,I 促銷中,D 促銷中</p>
                </td>
            </tr>
        </table>';


        $checkDomicile = empty(data_get($checkTWMBB,'twmbb_check_domicile')) ? '口' : '[v]';
        $checkEquipment = empty(data_get($checkTWMBB,'twmbb_check_equipment')) ? '口' : '[v]';
        $checkNotest = empty(data_get($checkTWMBB,'twmbb_check_notest')) ? '口' : '[v]';
        $checkStandalone = empty(data_get($checkTWMBB,'twmbb_check_standalone')) ? '口' : '[v]';
        $checkStandaloneRem = data_get($checkTWMBB,'twmbb_check_standalone_desc');
        $checkNotewsStandaloe = empty(data_get($checkTWMBB,'twmbb_check_notest_standalone')) ? '口' : '[v]';
        $checkNotewsStandaloeRem = data_get($checkTWMBB,'twmbb_check_notest_standalone_desc');
        $checkTest = empty(data_get($checkTWMBB,'twmbb_check_equipmentdiscord_test')) ? '口' : '[v]';
        $checkTestRem = data_get($checkTWMBB,'twmbb_check_equipmentdiscord_test_desc');

        $signImage = '';
        if ($signUrl) {
            $signImage = '<img src="'.$signUrl.'" height="50px" style="padding: 0 20px 0 20px;">';
        }

        $tbl2 .= '<table border="1">
            <tr>
                <td style="font-size:6px;">
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div>
                        <span style="font-size:10px;">申裝人(簽名):'.$signImage.'，法定代理人/代表人:</span>
                        <span style="font-size:8px;">（個人用戶請簽名，代理人請另附身分證正反面影本，未滿二十歲者，須附法定代理人之簽章及身分證明文件） E-MAIL: </span>
                    </div>
                </td>
            </tr>
        </table>
        <table border="1">
            <tr>
                <td rowspan="2" style="width:5%;text-align:center">個人資料</td>
                <td style="width:20%;"></td>
                <td rowspan="2" style="width:5%;text-align:center">配件確認</td>
                <td rowspan="2" style="width:35%;"><span style="font-size:9px">'.$checkEquipment.'CM一台、乙太網路線一條、USB連接線一條、電源線一條、說明書及驅動程式光碟</span>
                </td>
                <td rowspan="2" style="width:3%;text-align:center">裝置點</td>
                <td rowspan="2" style="width:7%;"></td>
            </tr>
            <tr>
                <td >電話:</td>
                <td>戶籍地址: '.$checkDomicile.'同裝機地址</td>
            </tr>

        </table>
        <div>
            <span>'.$checkNotest.'未備電腦、未為供裝速率實測 '.$checkStandalone.'單機實測為 __'.$checkStandaloneRem.'__ ，</span>
            <br>
            <span>在供裝速率範圍 '.$checkNotewsStandaloe.'無法單機測試 __'.$checkNotewsStandaloeRem.'__，</span>
            <br>
            <span>'.$checkTest.'電腦設備不合標準，單機實測為 ___'.$checkTestRem.'__。</span>
        </div>
        ';

        return $tbl2;
    }

}
