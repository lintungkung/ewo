<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ewoToolsController;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class updSignMengineer
{

    /**
     *  上傳 工程 簽名
     *
    */
    static public function getResult($request)
    {
        $p_time = date('Y-m-d H:i:s');

        try {
            $userCode = data_get($request,'userCode');
            $userName = data_get($request,'userName');
            $fileName = "SignMengineer_$userCode.jpg";
            $urlLog = config('order.EWO_URL')."/api/EWO/addEventLog";
            $directory = 'upload/SignMengineer/';
            $urlImg = config('order.EWO_URL')."/$directory$fileName?$p_time";
            if (!is_dir($directory)) {
                mkdir($directory,0777,true);
                chmod($directory,0777);
            }

            $file = $request->file("file");
            $file->move($directory,$fileName);

            // 新增log
            $logAry = array(
                'CompanyNo' => '0',
                'WorkSheet' => '0',
                'CustID' => '0',
                'UserNum' => $userCode,
                'UserName' => $userName,
                'EventType' => 'signMengineer',
                'Request' => '工程上傳APP簽名',
                'Responses' => $urlImg,
            );
            $obj = new ewoToolsController();
            $obj->insertLog($logAry);

            $code = '0000';
            $data = $urlImg;

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

}


