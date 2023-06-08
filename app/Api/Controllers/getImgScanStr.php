<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Exception;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class getImgScanStr
{

    // API，圖片掃描結果
    static public function getResult($request)
    {
        $p_time = date('Y-m-d H:i:s');
        $data = array();
        $orc = '';
        $p_start = microtime(true);
        $error = '';

        try {
            $validator = Validator::make($request, [
                'p_id' => 'required',
                'companyno' => 'required',
                'custid' => 'required',
                'worksheet' => 'required',
                'bookdate' => 'required',
            ],[
                'p_id.required' => '請輸入[圖片ID]',
                'companyno.required' => '請輸入[公司別]',
                'custid.required' => '請輸入[住編]',
                'worksheet.required' => '請輸入[工單號]',
                'bookdate.required' => '請輸入[預約時間]',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0],'0530');
            };


            $companyno = data_get($request,'companyno');
            $worksheet = data_get($request,'worksheet');
            $p_id = data_get($request,'p_id');
            $custid = data_get($request,'custid');
            $bookdate = data_get($request,'bookdate');
            $bookdate2 = date('Ymd',strtotime($bookdate));

            $fileAry['id01'] = 'identity_01.jpg';
            $fileAry['id02'] = 'identity_02.jpg';
            $fileAry['id030'] = $worksheet.'_id03_1'.'.jpg';
            $fileAry['id031'] = $worksheet.'_id03_2'.'.jpg';
            $fileAry['id032'] = $worksheet.'_id03_3'.'.jpg';

            $path = 'upload/'.$custid.'_'.$bookdate2.'/'.$fileAry[$p_id];
            $file_url = public_path(''.$path);

            $chkFile = is_file($file_url);
            if($chkFile === false) {
                throw new Exception('找不到檔案','0540');
            }

            $src = config('order.EWO_URL').'/'.$path.'?'.date('Hsi');
            $orc = (new TesseractOCR($file_url))->run();

            $p_run = (microtime(true) - $p_start);

            $code = '0000';
            $data = array(
                'run' => $p_run,
                'str' => $orc,
                'src' => $src,
                'file_url' => $file_url,
            );

        } catch (Exception $e) {
            $code = $e->getCode();
            $code = ($e->getCode())? substr('000'.$code,-4) : '0500';
            $error = "code=$code;error=".$e->getMessage();
            $orc = 'null無法辨識';
        }

        $p_run = (microtime(true) - $p_start);

        $code = '0000';
        $data = array(
            'run' => $p_run,
            'str' => $orc,
            'src' => $src,
            'file_url' => $file_url,
            'error' => $error,
        );

        $ret = array(
            'code' => $code,
            'date' => $p_time,
            'data' => $data,
        );

        return $ret;

    }

}
?>
