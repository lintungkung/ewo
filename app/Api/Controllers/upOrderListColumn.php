<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ewoToolsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class upOrderListColumn
{

    /**
     *
     * 更新，工單列表，欄位
     *
    */
    static public function getResult($request)
    {
        try {
            $validator = Validator::make($request, [
                'id' => 'required',
                'userCode' => 'required',
                'userName' => 'required',
                'eventType' => 'required',
                'request' => 'required',
                'responses' => 'required',
                'columnName' => 'required',
                'value' => 'required',
            ], [
                'id.required' => '請輸入[工單ID]',
                'userCode.required' => '請輸入[工程代號]',
                'userName.required' => '請輸入[工程名稱]',
                'eventType.required'=> '請輸入[更新代號]',
                'request.required'=> '請輸入[LOGRequery]',
                'responses.required'=> '請輸入[LOGReponse]',
                'columnName.required'=> '請輸入[更新欄位]',
                'value.required'=> '請輸入[更新內容]',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0]);
            }

            $p_id = data_get($request,'id');
            $p_companyNo = data_get($request,'companyNo');
            $p_workSheet = data_get($request,'workSheet');
            $p_custId = data_get($request,'custId');
            $p_userCode = data_get($request,'userCode');
            $p_userName = data_get($request,'userName');

            $p_eventType = data_get($request,'eventType');
            $p_request = data_get($request,'request');
            $p_responses = data_get($request,'responses');
            $p_columnName = data_get($request,'columnName');
            $p_value = data_get($request,'value');

            $obj = new ewoToolsController();
            $dataAry = array(
                'id' => $p_id,
                'companyNo' => $p_companyNo,
                'workSheet' => $p_workSheet,
                'custId' => $p_custId,
                'userCode' => $p_userCode,
                'userName' => $p_userName,
                'eventType' => $p_eventType,
                'request' => $p_request,
                'responses' => $p_responses,
                'columnName' => $p_columnName,
                'value' => $p_value,
            );
            $result = $obj->upOrderlistColumn($dataAry);

            if($result['code'] != '0000')
                throw new Exception($result['data']);

            $code = '0000';
            $data = 'OK';

        } catch (Exception $exception) {
            $code = '0500';
            $code = empty($exception->getCode())? $code : substr('000'.$exception->getCode(),-4);
            $data = $exception->getMessage();

        }

        $ret = array(
            'data' => $data,
            'code' => $code,
            'date' => date('Y-m-d H:i:s'),
        );

        return $ret;
    }

}


