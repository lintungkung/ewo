<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Order\OrderBaseRepository;
use App\Repositories\Order\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class getPDFSelDevList
{

    /**
     *  取得 PDF 設備選取 清單
     *
    */
    static public function getResult($request)
    {
        $p_time = date('Y-m-d H:i:s');

        try {

            $pdf_v = data_get($request,'pdf_v');
            $pdf_v = empty($pdf_v)? 'v3' : $pdf_v;

            $objBase = new OrderBaseRepository();
            $obj = new OrderRepository($objBase);

            $qryAry = array(
                'enable' => '1',
                'selectType' => '1',
            );
            $query = $obj->get_wm_equipment($qryAry);

            $list = array();
            foreach($query as $k => $t) {
                $type = data_get($t,'type');
                $id = data_get($t,'Id');
                $typeDesc = data_get($t,'typeDesc');
                $devName = data_get($t,'deviceName');
                $list["$type"][] = array(
                    'id' => $id,
                    'typeDesc' => $typeDesc,
                    'devName' => $devName,
                );
            }

            $data = $list;
            $code = '0000';

        } catch (Exception $e) {
            $code = empty($e->getCode())? '0500' : substr('000'.$e->getCode(),-4);
            $data = $e->getMessage();

        }

        $ret = array(
            'code' => $code,
            'date' => $p_time,
            'data' => $data,
        );

        return $ret;

    }

}


