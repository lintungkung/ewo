<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Log\LogRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderBaseRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class setDeviceSingleSN
{

    /*
     *  設定設備序號
     *  限定設備
     *
    */
    static public function getResult($request)
    {
        $p_time = date('Y-m-d H:i:s');
        $data = array();
        $p_start = microtime(true);
        $chkChargeName = config('order.SEtDeviceChargeName');

        $companyNo = data_get($request,'companyNo');
        $custId = data_get($request,'custId');
        $workSheet = data_get($request,'workSheet');
        $singleSnAry = data_get($request,'singleSnAry');

        $companyNo = data_get($request,'companyNo');
        $workSheet = data_get($request,'workSheet');
        $custID = data_get($request,'custId');
        $userCode = data_get($request,'userCode');
        $userName = data_get($request,'userName');
        $updateName = "$userCode $userName";

        $subsIdAry = [];

        try {
            $base = new OrderBaseRepository();
            $obj = new OrderRepository($base);

            foreach($singleSnAry as $k => $t) {
                $subsId = data_get($t,'subsId');
                $chargeName = data_get($t,'chargeName');
                $singleSn = data_get($t,'singleSn');
                $model = data_get($t,'model');

                if(empty($companyNo)) throw new Exception('缺少資料(公司別)','0410');
                if(empty($workSheet)) throw new Exception('缺少資料(工單號)','0410');
                if(empty($subsId)) throw new Exception('缺少資料(訂編)','0410');
                if(empty($chargeName)) throw new Exception('缺少資料(方案內容)','0410');
                if(empty($singleSn)) throw new Exception('缺少資料(序號)','0410');

                // 新增MS03Z1
                $Data301 = array(
                    'so'=>$companyNo,
                    'worksheet'=>$workSheet,
                    'custid'=>$custID,
                    'chargename'=>$chargeName,
                );
                $query_ms0301 = $obj->getOrderCharge($Data301,true);
                foreach($query_ms0301 as $k => $insMS03z1) {
                    $insMS03z1 = (array)$insMS03z1;
                    $insMS03z1['UpdateName'] = "$userCode $userName";
                    $insMS03z1['UpdateTime'] = $p_time;
                    $obj->insertMS03Z1($insMS03z1);
                }

                $whereAry = array(
                    'CompanyNo' => $companyNo,
                    'WorkSheet' => $workSheet,
                    'SubsID' => $subsId,
                    'CustID' => $custID,
                    'ChargeName' => $chargeName,
                    'SingleSN' => $singleSn,
                    'SWVersion' => $model,
                    'UpdateName' => $updateName,
                    'UpdateTime' => $p_time,
                );
                $upMS0301 = $obj->updateMS0301SingleSN($whereAry);
                $responses = $upMS0301? $upMS0301 : 'OK';
                $whereAry['UserNum'] = $userCode;
                $whereAry['UserName'] = $userName;
                $whereAry['EventType'] = 'updDevicSingleSn';
                $whereAry['Request'] = '更新設備序號；'.json_encode($whereAry);
                $whereAry['Responses'] = $responses;
                self::insertLog($whereAry);

                $subsIdAry[$chargeName] = array(
                    'chargeName' => $chargeName,
                    'subsId' => $subsId,
                    'singleSn' => $singleSn,
                    'result' => $responses,
                );
            }

            $data = $subsIdAry;
            $code = '0000';

        } catch (Exception $e) {
            $code = $e->getCode();
            $code = ($e->getCode())? substr('000'.$code,-4) : '0500';
            $data = 'error:'.$e->getMessage();
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => $p_time,
        );

        return $ret;

    }


    static public function insertLog($data)
    {
        // add log
        $params['CompanyNo'] = data_get($data,'CompanyNo');
        $params['WorkSheet'] = data_get($data,'WorkSheet');
        $params['CustID'] = data_get($data,'CustID');
        $params['UserNum'] = data_get($data,'UserNum');
        $params['UserName'] = data_get($data,'UserName');
        $params['EventType'] = data_get($data,'EventType');
        $params['Request'] = data_get($data,'Request');
        $params['Responses'] = data_get($data,'Responses');
        $obj = new LogRepository();
        $obj->insertLog($params);

    }

}


