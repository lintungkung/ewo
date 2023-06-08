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


class PDF_ach extends Controller
{

    public function __construct(OrderRepository $OrderRepository)
    {
        $this->OrderRepository = $OrderRepository;
    }

    public function createPDF($source,$version,$params)
    {
        set_time_limit(300);
        $p_time_start = microtime(true);
        $dataAry = explode('_',$params);
        $orderId = $dataAry[0];
        $companyNo = $dataAry[1];
        $workSheet = $dataAry[2];
        $p_run_title = "PDF_$version"."_ID_$orderId";

        $page_list = array();
        $page_data = array();
        $ewo_url = config('order.EWO_URL');
        $logo_url = $ewo_url.'/img/logo_04.png';
        $logo_url_fet = $ewo_url.'/img/logo_fet.png';

if(0) {
    $ret = array(
        'code' => '0000',
        'msg' => 'test create',
        'p_version' => $version,
        'p_id' => $orderId,

    );
    return $ret;

}

        try {

            $qryData = array(
                'so' => $companyNo,
                'worksheet' => $workSheet,
            );
            $ms0300Info = $this->OrderRepository->getOrderWorkKind($qryData);
            $qryData = array(
                'companyNo' => $companyNo,
                'workSheet' => $workSheet,
                'formType' => 'ach',
            );
            $achInfo = $this->OrderRepository->getETF_FORMDATA($qryData);
            $achData = data_get($achInfo[0],'Data');
            $achDate = data_get($achInfo[0],'created_at');
            $achDataJson = json_decode($achData,1);
            $achDataMS0200 = data_get($achDataJson,'ms0200OldData');
            $payName = data_get($achDataMS0200,'PayName');
            $custId = data_get($achDataMS0200,'custid');
            $bookDate = data_get($ms0300Info,'BookDate');
            $ewo_url = config('order.EWO_URL');
            $signFile = 'upload/'.$custId."_".date("Ymd",strtotime($bookDate))."/sign_ach_".$workSheet.".jpg";
            $achSignUrl = $ewo_url.'/'.$signFile;

            $p_data = array();
            $p_data['PayName'] = $payName;
            $p_data['head_logo_img'] = $ewo_url.'/img/logo_04.png';
            $p_data['CustID'] = $custId;
            $p_data['CompanyNo'] = $companyNo;
            $p_data['WorkSheet'] = $workSheet;
            $p_data['custPhone'] = data_get($ms0300Info,'CellPhone01');
            $p_data['dateStr'] = intval(substr($achDate,0,4)) - 1911 . '年 '.date('m月 d日',strtotime($achDate));
            $p_data['signImage'] = $achSignUrl;

            $bankName = data_get($achDataMS0200,'BankName');
            $cardBank = data_get($achDataMS0200,'CardBank');
            $userBank = data_get($achDataMS0200,'UserBank');
            $bankAcctNo = data_get($achDataMS0200,'BankAcctNo');
            $vAccuN01 = data_get($achDataMS0200,'VAccuN01');
            $vAccuN02 = data_get($achDataMS0200,'VAccuN02');
            $cardKind = data_get($achDataMS0200,'CardKind');
            $cardValid = data_get($achDataMS0200,'CardValid');
            $bankRegID = data_get($achDataMS0200,'BankRegID');
            $bankRegName = data_get($achDataMS0200,'BankRegName');

            $p_data['custName'] = $bankRegName;
            switch ($payName) {
                case '3 信用卡':
                    $typeName = '信用卡';
                    $bankName = $cardBank;
                    $chkCode = $vAccuN01;
                    break;
                case '4 ACH':
                    $typeName = 'ACH';
                    $bankName = $userBank;
                    $chkCode = $vAccuN02;
                    break;
            }

            $bankNameAry = explode(' ',$bankName);
            $p_data['payType'] = $typeName;
            $p_data['bankName'] = data_get($bankNameAry,1);
            $p_data['chkCode'] = $chkCode;

            $domPDF = domPDF::loadView('pdf.table_ach', compact('p_data'));

            if(10) {
                $domPDF->setOptions(['adminPassword' => '','isRemoteEnabled'=>true])->setEncryption('0000');

                $fileName = 'ACH_'.$workSheet.'.pdf';
                $bookdateStr = date('Ymd',strtotime($bookDate));

                $directory = public_path("upload/$custId"."_$bookdateStr");
                if (!is_dir($directory)) {
                    mkdir($directory,0777,true);
                    chmod($directory,0777);
                }

                $domPDF->save("$directory/$fileName");

            } else {
                //預覽PDF
                return $domPDF->stream();

            }

            $ret = array(
                'code' => '0000',
                'status' => 'OK',
                'meg' => '',
                'data' => "$directory/$fileName",
//                'run' => json_encode($run),
                'date' => date('Y-m-d H:i:s')
            );


        } catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $ret = array(
                'code' => '0400',
                'status' => 'error',
                'meg' => '資料錯誤='.$e->getMessage(),
                'data' => '',
                'date' => date('Y-m-d H:i:s')
            );

        }


        return $ret;

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


}
