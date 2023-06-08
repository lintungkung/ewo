<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class queryFTTH
{

    /*
     *  FTTH 狀態信息
     *
    */
    static public function getResult($request)
    {
        $p_time = date('Y-m-d H:i:s');
        $companyNo = data_get($request,'companyNo');
        $workSheet = data_get($request,'workSheet');
        $custId = data_get($request,'custId');


        try {
            $sqlStr = <<<EOF
                    SELECT SingleSn
                    FROM COSSDB.dbo.MS0200
                    WHERE 1=1
                    AND CompanyNo IN ('$companyNo')
                    AND CustID IN ('$custId')
                    AND ServiceName IN ('B FTTH')
                    ORDER BY SingleSn DESC
EOF;

            $query = DB::connection('WMDB')->select($sqlStr);
            $list01 = data_get($query,'0');
            $singleSn = data_get($list01,'SingleSn');

//    $singleSn = '123';

            if(is_null($singleSn) || empty($singleSn)) {
                throw new Exception('尚未開通','0530');

            } else {
                $url = 'http://172.17.87.143:8083/api/FTTHAPI/FTTH/QueryFTTH';

//                if(10) {
//                    $companyNo = '230';
//                    $singleSn = 'ALCLB1491188'; // 開通
//                } else {
//                    $companyNo = '250';
//                    $singleSn = 'ALCLB2E9FFD0'; // 未開
//                }


                $params = array(
                    "SO" => $companyNo,
                    "SerNum" => $singleSn
                );
                $queryData = json_encode($params);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $queryData);

                $header = array('Content-Type: application/json');
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

                $result = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($result,1);

                if($result['RetCode'] == '0') {
                    $data = $result['RetData'];
                } else {
                    throw new Exception('api失敗;'.$result['RetMsg'],'0540');
                }

            }
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

}


