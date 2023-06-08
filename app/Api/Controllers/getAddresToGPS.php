<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class getAddresToGPS
{

    /*
     *  查詢地址的GPS
     *  地址 to GPS
     *
    */
    static public function getResult($request)
    {
        $p_time = date('Y-m-d H:i:s');
        $data = array();
        $orc = '';
        $p_start = microtime(true);

        $companyNo = data_get($request,'companyNo');
        $custId = data_get($request,'custId');
        $address = data_get($request,'address');
        $lat = data_get($request,'lat');
        $lng = data_get($request,'lng');

        try {

            if(!empty($custId) && !empty($companyNo)) {
                $sqlStr = <<<EOF
                    SELECT TOP 1
                    (Latitude+','+Longitude) as gps
                    ,(MSCITY+MSDISTRICT+ADDRNAME) as address
                    FROM COSSDB.dbo.MS0102
                    WHERE 1=1
                    AND CompanyNo IN ('$companyNo')
                    AND CustID IN ('$custId')
                    AND Latitude IS NOT NULL
                    ORDER BY TriggerTime DESC
EOF;
                $query = DB::connection('WMDB')->select($sqlStr);
                $qryList = data_get($query,'0');
                $gps = data_get($qryList,'gps');
                $address = data_get($qryList,'address');

                $data = empty($gps)? 'DB查無資料' : array('gps'=>$gps,'address'=>$address);


            } elseif(!empty($lat) && !empty($lng)) {
                $key = 'AIzaSyAYK37QipZGgQx_ry1p0b6FpOmoaQq7DtE';
                $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&language=zh-TW&key=".$key;
                $result = file_get_contents($url);
//$result = '{"plus_code":{"compound_code":"3FGV+WHV 台灣新北市三重區","global_code":"7QQ33FGV+WHV"},"results":[{"address_components":[{"long_name":"227","short_name":"227","types":["street_number"]},{"long_name":"仁愛街","short_name":"仁愛街","types":["route"]},{"long_name":"五常里","short_name":"五常里","types":["administrative_area_level_4","political"]},{"long_name":"三重區","short_name":"三重區","types":["administrative_area_level_3","political"]},{"long_name":"新北市","short_name":"新北市","types":["administrative_area_level_1","political"]},{"long_name":"台灣","short_name":"TW","types":["country","political"]},{"long_name":"241","short_name":"241","types":["postal_code"]}],"formatted_address":"241台灣新北市三重區仁愛街227號","geometry":{"location":{"lat":25.077385,"lng":121.4940084},"location_type":"ROOFTOP","viewport":{"northeast":{"lat":25.0787339802915,"lng":121.4953573802915},"southwest":{"lat":25.0760360197085,"lng":121.4926594197085}}},"place_id":"ChIJsZ7fVCipQjQRa6XqcDEl-6Q","plus_code":{"compound_code":"3FGV+XJ 台灣新北市三重區","global_code":"7QQ33FGV+XJ"},"types":["street_address"]}],"status":"OK"}';
                $result = json_decode($result,1);

                $resultAry = data_get($result,'results');
                $status = data_get($result,'status');

                if($status === 'OK') {
                    $resultAry0 = data_get($resultAry,0);
                    $retAddress = data_get($resultAry0,'formatted_address');

                    $data = $retAddress;

                } else {
                    $data = 'googleAPI錯誤';

                }

            } elseif(!empty($address)) {
                $key = 'AIzaSyAYK37QipZGgQx_ry1p0b6FpOmoaQq7DtE';
                $url = "https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=".$key;
                $result = file_get_contents($url);
                $result = json_decode($result,1);

                $resultAry = data_get($result,'results');
                $status = $result['status'];
                if($status === 'OK') {
                    $geometryData = data_get($resultAry,0);
                    $geometryAry = data_get($geometryData,'geometry');

                    $location = data_get($geometryAry,'location');
                    $lat = data_get($location,'lat');
                    $lng = data_get($location,'lng');
                    $data = "$lat,$lng";

                } else {
                    $data = 'googleAPI錯誤';

                }
            } else {
                $data = '參數不完整';
            }

            $code = '0000';

        } catch (Exception $e) {
            $code = $e->getCode();
            $code = ($e->getCode())? substr('000'.$code,-4) : '0500';
            $data = 'error:'.$e->getMessage();

        }

        $ret = array(
            'code' => $code,
            'date' => $p_time,
            'data' => $data,
        );

        return $ret;

    }

}


