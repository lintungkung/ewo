<?php

namespace App\Api\Controllers;

use App\Repositories\Log\LogRepository;
use Exception;
use Validator;

class addEventLog
{

    /*
     *  新增LOG
     *
    */
    static public function getResult($request)
    {
        try {

            $validator = Validator::make($request, [
                'companyNo' => 'required',
                'custId' => 'required',
                'workSheet' => 'required',
                'userCode' => 'required',
                'userName' => 'required',
                'type' => 'required',
            ],[
                'companyNo.required' => '請輸入[companyNo]',
                'custId.required' => '請輸入[workSheet]',
                'workSheet.required' => '請輸入[workSheet]',
                'userCode.required' => '請輸入[userCode]',
                'userName.required' => '請輸入[userName]',
                'type.required' => '請輸入[type]',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0],'0530');
            };

            $companyNo = data_get($request,'companyNo');
            $custId = data_get($request,'custId');
            $workSheet = data_get($request,'workSheet');
            $userCode = data_get($request,'userCode');
            $userName = data_get($request,'userName');
            $type = data_get($request,'type');
            $vRequest = data_get($request,'request');
            $responses = data_get($request,'responses');
            $value = data_get($request,'value');

            // LOG紀錄
            $logData = array();
            $logData['CompanyNo'] = $companyNo;
            $logData['WorkSheet'] = $workSheet;
            $logData['CustID'] = $custId;
            $logData['EventType'] = $type;
            $logData['UserNum'] = $userCode;
            $logData['UserName'] = $userName;
            $logData['Request'] = $vRequest;
            $logData['Responses'] = $responses;
            $db = new LogRepository();
            $db->insertLog($logData);

            $data = 'OK';
            $code = '0000';

        } catch (Exception $e) {
            $code = $e->getCode();
            $code = ($e->getCode())? substr('000'.$code,-4) : '0500';
            $data = 'error:'.$e->getMessage();

        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => date('Y-m-d H:i:s'),
        );

        return $ret;
    }

}


