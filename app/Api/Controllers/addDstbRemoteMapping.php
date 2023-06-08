<?php

namespace App\Api\Controllers;

use App\Model\wm_dstb_remotes;
use App\Repositories\Log\LogRepository;
use Exception;
use GuzzleHttp\Client;
use function MongoDB\BSON\toJSON;

class addDstbRemoteMapping
{

    /*
     *  紀錄
    */
    static public function getResult($request)
    {
        /** @var array $request */
        // 取得時間
        $p_time = date('Y-m-d H:i:s');
        $code = '0000';

        try {
            // 檢查傳送的欄位
            // SingleSN 欄位不是必填，裝機工單可能沒有
            // remoteVendor 為預留欄位
            $validator = \Illuminate\Support\Facades\Validator::make($request, [
                'CompanyNo' => 'required',
                'CustID' => 'required',
                'SubsID' => 'required',
                'AssignSheet' => 'required', //  WMDBAPP worksheet 為  MS0301.AssignSheet 比較新的表欄位較 worksheet
                'remoteQrCode' => 'required',
                'userCode' => 'required',
                'userName' => 'required',
            ],[
                'CompanyNo.required' => '請輸入[公司別]',
                'CustID.required' => '請輸入[住編]',
                'SubsID' => '請輸入[訂編]',
                'AssignSheet' => '請輸入[工單號碼]', //  WMDBAPP worksheet 為  MS0301.AssignSheet 比較新的表欄位較 worksheet
                'remoteQrCode' => '請輸入[遙控器 QR Code]',
                'userCode.required' => '請輸入[工程代號]',
                'userName.required' => '請輸入[工程名稱]',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0],'0504');
            }

            $CompanyNo = data_get($request,'CompanyNo');
            $CustID = data_get($request,'CustID');
            $SubsID = data_get($request,'SubsID');
            $AssignSheet = data_get($request,'AssignSheet');
            $remoteQrCode = substr(data_get($request,'remoteQrCode'),0,20) ;
            $SingleSN = data_get($request,'SingleSN');
            $userCode = data_get($request,'userCode');
            $userName = data_get($request,'userName');
            $type = 'addDstbRemoteMapping';

            $data = [
                'AssignSheet' => $AssignSheet,
                'remoteQrCode' => $remoteQrCode,
                'SingleSN' => $SingleSN,
                'userCode' => $userCode,
                'userName' => $userName,
            ];

            //更新或者是建立 wm_dstb_remotes
            wm_dstb_remotes::updateOrCreate(
                [
                    'CompanyNo' => $CompanyNo,
                    'CustID' => $CustID,
                    'SubsID' => $SubsID,
                ],
                $data
            );


            // 新增操作紀錄 event log
            $logParams = array(
                'CompanyNo' => $CompanyNo,
                'WorkSheet' => $AssignSheet,
                'CustID' => $CustID,
                'UserNum' => $userCode,
                'UserName' => $userName,
                'EventType' => $type,
                'Request' => "API[addDstbRemoteMapping]:".json_encode($request),
                'Responses' =>'',
            );
            //
            $LogRepository = new LogRepository();
            $LogRepository->insertLog($logParams);


        } catch (Exception $e) {
            $code = $e->getCode();
            $code = ($e->getCode())? substr('000'.$code,-4) : '0500';
            $msg = 'error:'.$e->getMessage();
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => $p_time,
        );


        return $ret;
    }
}


