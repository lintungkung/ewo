<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class getDistanceGPS
{

    /*
     *  獲得兩點GPS，距離
     *
     *
    */


    static public function getResult($request)
    {

        $lat1 = data_get($request,'lat1');
        $lng1 = data_get($request,'lng1');
        $lat2 = data_get($request,'lat2');
        $lng2 = data_get($request,'lng2');

        $radLat1 = deg2rad($lat1); //deg2rad()函數將角度轉換爲弧度
        $radLng1 = deg2rad($lng1);
        $radLat2 = deg2rad($lat2);
        $radLng2 = deg2rad($lng2);
        $a = $radLat1 - $radLat2;
        $b = $radLng1 - $radLng2;
        $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2))) * 6378.137 * 1000;

        $s = sprintf('%.2f', (float)$s);

        $ret = array(
            'code' => '0000',
            'date' => date('Y-m-d H:i:s'),
            'data' => $s,
        );

        return $ret;

    }
    static public function getResult2($request)
    {
        $p_time = date('Y-m-d H:i:s');

        $lat1 = data_get($request,'lat1');
        $lng1 = data_get($request,'lng1');
        $lat2 = data_get($request,'lat2');
        $lng2 = data_get($request,'lng2');
        $earthRadius = 6367000; //approximate radius of earth in meters


        /*
        Convert these degrees to radians
        to work with the formula
        */
        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;
        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;

        /*
        Using the
        Haversine formula
        http://en.wikipedia.org/wiki/Haversine_formula
        calculate the distance
        */
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        $data = round($calculatedDistance);

        $code = '0000';

        $ret = array(
            'code' => $code,
            'date' => $p_time,
            'data' => $data,
        );

        return $ret;

    }

}


