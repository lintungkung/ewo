<?php

namespace App\Api\Controllers;

use App\Model\MS0200;
use App\Model\wm_testResultLog;
use App\Repositories\Order\OrderBaseRepository;
use App\Repositories\Order\OrderRepository;
use Exception;
use GuzzleHttp\Client;
use http\Params;
use Validator;

class addCheckValeuLog
{

    /*
     *  查測數據新增紀錄
    */
    static public function getResult($request)
    {
        $p_time = date('Y-m-d H:i:s');
        $code = '0000';
        $data = '成功';
        $caseId = $resultCode = '';
        $urlAry = array();
        $urlAry['r1'] = config('order.R1_URL');
        $urlAry['orderlist'] = $urlAry['r1'].'/wm/orderlist';
        $urlAry['testvaluelog'] = $urlAry['r1'].'/wm/testValueLog';

        try {

            $validator = Validator::make($request, [
                'companyNo' => 'required',
                'custId' => 'required',
                'userCode' => 'required',
                'userName' => 'required',
                'type' => 'required',
                'info' => 'required',
                'source' => 'required',
            ],[
                'companyNo.required' => '請輸入[公司別]',
                'custId.required' => '請輸入[住編]',
                'userCode.required' => '請輸入[工程代號]',
                'userName.required' => '請輸入[工程名稱]',
                'type.required' => '請輸入[查測類型]',
                'info.required' => '請輸入[查測結果]',
                'source.required' => '請輸入[來源]',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0],'0504');
            }

            $companyNo = data_get($request,'companyNo');
            $workSheet = data_get($request,'workSheet');
            $custId = data_get($request,'custId');
            $subsId = data_get($request,'subsId');
            $userCode = data_get($request,'userCode');
            $userName = data_get($request,'userName');
            $type = data_get($request,'type');
            $info = data_get($request,'info');
            $singleSn = data_get($request,'singleSn');
            $caseId = data_get($request,'caseId');
            $serviceName = data_get($request,'serviceName');
            $source = data_get($request,'source');
            $datalist = data_get($request,'datalist');
            $path = 'App\Api\Controllers\addCheckValeuLog';

            // 參數檢查
            if(method_exists($path,$type)) {
                $func = $type;
                $result = $path::$func($request);

                // 紀錄 工單列表 dstbTestValue
                if($type == 'stb_atvqrcode')
                    self::saveDstbTestValue($result,$request);

                $chkResultCode = data_get($result,'code');
                $resultCode = $chkResultCode;
                $chkResultData = data_get($result,'data');
                $chkResultMsg = $chkResultCode == '0000'? 'true' : 'false';//data_get($chkResultData,'msg');
                if(empty($subsId))
                    $subsId = data_get($result,'subsId');
                if(empty($singleSn))
                    $singleSn = data_get($result,'singleSn');
                if($type == 'stb_atvqrcode') {
                    $v_ret_datalist = data_get($result,'dataList');
                    if($chkResultCode != '0000')
                        $v_ret_datalist = array_merge($v_ret_datalist,['備註01'=>$chkResultCode.';'.data_get($chkResultData,'msg')]);
//                    $info .= ',資料解析：' . data_get($result, 'dataList');
                    $info = json_encode(array(
                        'qrCode'=>$info,
                        'result'=>$v_ret_datalist,
//                        'result'=>data_get($result, 'dataList'),
                        ));
                }
            } else {
                $chkResultMsg = "類別[$type]無對應檢查工具";
            }

            // 查詢loadData新增Log
            $queryLogAry = array(
                'companyNo' => $companyNo,
                'workSheet' => $workSheet,
                'custId' => intval($custId),
                'subsId' => intval($subsId),
                'userCode' => $userCode,
                'userName' => $userName,
                'type' => $type,
                'info' => $info,
                'result' => $chkResultMsg,
                'singleSn' => $singleSn,
                'serviceName' => $serviceName,
                'source' => $source,
                'datalist' => $datalist,
                'created_at' => $p_time,
            );
            $resltId = wm_testResultLog::insertGetId($queryLogAry);

            // 新增操作紀錄
            $logParams = array(
                'companyNo' => $companyNo,
                'workSheet' => $workSheet,
                'custId' => $custId,
                'type' => $type,
                'userCode' => $userCode,
                'userName' => $userName,
                'request' => "參數查詢紀錄[$type][apiLog];".$info,
                'responses' =>$chkResultMsg,
            );
            $logResult = self::addLog($logParams);
            $msg = $chkResultMsg;

            $urlPath = "?&id=$resltId&68b74952b25fdee7dd585d52afc0d745=&3222c5c072c701595c3d78e7be5c13d0=&58e817e5767dfb1a47d981bda1274e8e=&8a07709892d99584978a216ad075d4b6=&51c9c92c41819e79a5e54df70a7f9e9e=&0bffeb5a27dc3eef879595c71d3ed26a=&2b6481aeb8459911756a7fc572379624=";
            $urlAry['testvaluelog'] = $urlAry['testvaluelog'] . $urlPath;

        } catch (Exception $e) {
            $code = $e->getCode();
            $code = ($e->getCode())? substr('000'.$code,-4) : '0500';
            $msg = 'error:'.$e->getMessage();
        }

        $dataAry = array(
            'code' => $resultCode,
            'msg' => $msg,
            'itemId' => $resltId,
            'caseId' => $caseId,
            'url' => $urlAry,
            'datalist' => $datalist,
        );
        $ret = array(
            'code' => $code,
            'data' => $dataAry,
            'date' => $p_time,
        );

        return $ret;
    }


    // addLog
    static function addLog($params)
    {
        $client = new Client();
        $url = config('order.EWO_URL').'/api/EWO/addEventLog';
        $postAry = array(
            'body' => json_encode($params),
            'headers' => ['Content-Type' => 'application/json',],
        );
        $logResult = $client->request('POST', $url, $postAry);

        return $logResult;
    }

    // 紀錄 dstb test value wm_orderlist
    static function saveDstbTestValue($params,$request)
    {
        $objOrderBase = new OrderBaseRepository();
        $objOrder = new OrderRepository($objOrderBase);

        $companyNo = $request['companyNo'];
        $workSheet = $request['workSheet'];
        $subsId = $params['subsId'];
        if(empty($companyNo) || empty($workSheet))
            return;

        $data = array(
            'worksheet' => $workSheet,
            'companyno' => $companyNo,
        );
        // 從 dataList 取出 dstbTestValue
        $query = $objOrder->getStatistics($data);
        $queryItem = data_get($query,0);
        if($queryItem) {
            // 合併資料
            $dataList = data_get($queryItem,'dataList');
            $dataListAry = empty($dataList)? array() : json_decode($dataList,true);
            $dstbTestValue = data_get($dataListAry,'dstbTestValue');

            $singleSn = $params['singleSn'];
            $key = empty($subsId)? $singleSn : $subsId;
            $value = $params['data'];

            $dstbTestValue[$key] = $value;
            $dataListAry['dstbTestValue'] = $dstbTestValue;
            $dataListJson = json_encode($dataListAry);

            // 寫回 dataList
            $upAry = array('so'=>$companyNo,'worksheet'=>$workSheet,'dataList'=>$dataListJson);
            $objOrder->updateDataList($upAry);
        }

    }

    // 檢查 Stb & ATV QrCode解析
    static function stb_atvqrcode($params)
    {
        $subsId = $serviceName = $singleSn = ''; // 預設參數
        $v_version = $v_ber = $v_snr = $v_level = $valStr = '';
        $valStr = data_get($params,'info');
        $companyNo = data_get($params,'companyNo');
        $custId = data_get($params,'custId');

        // loadData[SingleSn]
        $s1 = strpos($valStr,'Facisno:');
        $s2 = strpos($valStr,',',$s1);
        $s_l1 = $s2 - $s1;
        $single = trim(substr($valStr,$s1,$s_l1));
        $singleAry = explode(':',$single);
        $singleSn = trim(data_get($singleAry,1));

        // 參數標準
        $p_berChkMax = 0.00001;
        $p_snrChkMin = 37;
        $p_levelChkMin = 51;
        $p_levelChkMax = 72;

        try {
            if(strlen($singleSn) < 8)
                throw new Exception('序號不合格');

            // 檢查序號開通狀態
            $queryCustInfo = MS0200::query()
                ->select('*')
                ->where([
                    'CompanyNo' => $companyNo,
                    'CustID' => $custId,
                    'SingleSN' => $singleSn,
                ])
                ->orderBy('CustStatus','asc')
                ->first();

            $subsId = data_get($queryCustInfo,'SubsID');
            $serviceName = data_get($queryCustInfo,'ServiceName');

            // loadData[Data]
            $a1 = strpos($valStr,'Data:');
            $a2 = strpos($valStr,'(',$a1) + 1;
            $a3 = strpos($valStr,')',$a2);
            $l1 = $a3 - $a2;
            $str1 = substr($valStr,$a2,$l1);
            $ary1 = explode(',',$str1);
            if(!is_array($ary1)) {
                throw new Exception("參數解析錯誤[$str1],來源");
            }
            $v_version = data_get($ary1,0);
            $v_ber = trim(data_get($ary1,1));
            $v_ber2 =  floatval($v_ber);
            $v_snr = trim(data_get($ary1,2));
            $v_snr2 = intval(str_replace('dB','',$v_snr));
            $v_level = trim(data_get($ary1,3));
            $v_level2 = intval(str_replace('dBuV','',$v_level));

            // 條件判斷
            if($v_ber2 > $p_berChkMax) {
                throw new Exception("不合格,錯誤率[$v_ber]大於$p_berChkMax");
            }
            if($v_snr2 < $p_snrChkMin) {
                throw new Exception("不合格,雜訊比[$v_snr]dB小於$p_snrChkMin dB");
            }
            if($v_level2 > $p_levelChkMax || $v_level2 < $p_levelChkMin) {
                throw new Exception("不合格,訊號強度[$v_level],小於$p_levelChkMin dBuV且大於$p_levelChkMax dBuV");
            }

            $code = '0000';
            $data = array(
                'msg' => 'ok',
                'VERSION' => $v_version,
                'BER' => $v_ber,
                'SNR' => $v_snr,
                'LEVEL' => $v_level,
                'SOURCE' => $valStr,
            );

        } catch (Exception $exception) {
            $valChkResult = $exception->getMessage();
            $code = empty($exception->getCode())?
                '0500'
                : substr('000'.$exception->getCode(),-4);
            $data = array(
                'msg' => $valChkResult,
                'VERSION' => $v_version,
                'BER' => $v_ber,
                'SNR' => $v_snr,
                'LEVEL' => $v_level,
                'SOURCE' => $valStr,
            );
        }

        $v_berChkStr = ($v_ber2 > $p_berChkMax)? "不合格,最高$p_berChkMax" : '合格';
        $v_snrChkStr = ($v_snr2 < $p_snrChkMin)? "不合格,最小$p_snrChkMin" : '合格';
        $v_levelChkStr = ($v_level2 > $p_levelChkMax || $v_level2 < $p_levelChkMin)? "不合格,最低$p_levelChkMin,最高$p_levelChkMax" : '合格';
        $datalist = (array(
            '設備版本' => $v_version,
            '錯誤率' => $v_ber."($v_berChkStr)",
            '雜訊比' => $v_snr."($v_snrChkStr)",
            '訊號強度' => $v_level."($v_levelChkStr)",
        ));

        $ret = array(
            'code' => $code,
            'data' => $data,
            'subsId' => $subsId,
            'serviceName' => $serviceName,
            'singleSn' => $singleSn,
            'dataList' => $datalist,
        );

        return $ret;
    }


    // cm 品質查測
    static function querycminfo($params)
    {
        $subsId = $singleSn = ''; // 預設參數
        $valStr = data_get($params,'info');
        $companyNo = data_get($params,'companyNo');
        $custId = data_get($params,'custId');
        $subsId = data_get($params,'subsId');

        try {
            // 檢查序號開通狀態
            $queryCustInfo = MS0200::query()
                ->select('*')
                ->where([
                    'CompanyNo' => $companyNo,
                    'CustID' => $custId,
                    'SubsID' => $subsId,
                ])
                ->orderBy('CustStatus','asc')
                ->first();

            $singleSn = data_get($queryCustInfo,'SingleSN');

            if(strlen($valStr) < 10)
                throw new Exception('數據異常','0410');

            $valStrJson = json_decode($valStr,true);
            $v_code = data_get($valStrJson,'RetCode');

            if($v_code != '0') {
                $v_msg = data_get($valStrJson,'RetMsg');
                throw new Exception("code:$v_code;$v_msg", '0415');
            }

            if(strpos($valStr,':false'))
                throw new Exception('數據不合格','0420');

            $data = 'true';
            $code = '0000';

        } catch (Exception $exception) {
            $valChkResult = $exception->getMessage();
            $code = empty($exception->getCode())?
                '0500'
                : substr('000'.$exception->getCode(),-4);
            $data = array(
                'msg' => $valChkResult,
                'SOURCE' => $valStr,
            );
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'singleSn' => $singleSn,
        );

        return $ret;
    }
}


