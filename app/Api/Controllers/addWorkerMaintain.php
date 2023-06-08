<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Ewo_EventController;
use App\Http\Controllers\MyException;
use App\Repositories\Log\LogRepository;
use App\Repositories\Order\OrderBaseRepository;
use App\Repositories\Order\OrderRepository;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Exception;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class addWorkerMaintain
{
    // 新增維修工單

    static public function getResult($request)
    {
        $p_time = date('Y-m-d H:i:s');
        $data = array();
        $orc = '';
        $p_start = microtime(true);
        $error = '';

        try {
            $validator = Validator::make($request, [
                'id' => 'required',
                'companyNo' => 'required',
                'workSheet' => 'required',
                'custId' => 'required',
                'subsId' => 'required',
                'userCode' => 'required',
                'userName' => 'required',
                'contactName' => 'required',
                'workTeam' => 'required',
                'workCause' => 'required',
            ],[
                'id.required' => '請輸入[ID]',
                'companyNo.required' => '請輸入[公司別]',
                'workSheet.required' => '請輸入[工單號]',
                'custId.required' => '請輸入[住編]',
                'subsId.required' => '請輸入[訂編]',
                'userCode.required' => '請輸入[工程代號]',
                'userName.required' => '請輸入[工程名稱]',
                'contactName.required' => '請輸入[用戶名稱]',
                'workTeam.required' => '請輸入[工程組別]',
                'workCause.required' => '請輸入[維修原因]',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0],'0530');
            }

            $p_formal = ''; // COSSTREAN
            $p_formal = 'official'; // COSSDB
            $p_time = date('Y-m-d H:i:s');
            $p_companyNo = data_get($request,'companyNo');
            $p_workSheet = data_get($request,'workSheet');
            $p_subsId = data_get($request,'subsId');
            $p_custId = data_get($request,'custId');
            $p_WorkCause = data_get($request,'workCause');
            $p_remark = data_get($request,'remark');
            $p_userCode = data_get($request,'userCode');
            $p_userName = data_get($request,'userName');
            $p_Broker = "$p_userCode $p_userName";
            $p_contactName = data_get($request,'contactName');
            $p_contactCell = data_get($request,'contactCell');
            $p_workkind = data_get($request,'workKind');
            $p_workTeam = data_get($request,'workTeam');
            $p_bookdate = substr($p_time,0,10);
            $p_timing = substr($p_time,11,10);

            // 檢查是否有沒完工=>維修
            $whereAry = array(
                'companyNo' => $p_companyNo,
                'workKind' => '5 維修',
                'subsId' => $p_subsId,
                'statusNotIn' => array('A.取消'),
                'first' => 'first',
                'select' => array(
                    ['column' => 'SheetStatus','asName' => 'MS301.'],
                    ['column' => 'WorkTeam','asName' => 'MS301.'],
                    ['column' => 'AssignSheet','asName' => 'MS301.'],
                ),
                'orderBy' => array(
                    ['name'=>'BookDate','type'=>'desc','asName'=>'MS301.']
                )
            );
            $objBase = new OrderBaseRepository();
            $obj = new OrderRepository($objBase);
            $queryWorkStatus = $obj->getWorksheetList($whereAry);
            $v_sheetStatus = data_get($queryWorkStatus,'SheetStatus');
            $v_assignSheet = data_get($queryWorkStatus,'AssignSheet');
            $v_workTeam = data_get($queryWorkStatus,'WorkTeam');
            if(!empty($v_sheetStatus) && !in_array($v_sheetStatus,['A.取消','4.結款','4 結案'])) {
                throw new Exception('還有[未完工]的[維修]單;'.$p_companyNo.'#'.$v_assignSheet.'['.$v_workTeam.']','0598');
            }

            $post_data = array (
                "formal" => $p_formal,
                "orderNo" => "$p_companyNo-$p_subsId",
                "WorkCause" => $p_WorkCause, // 維修原因
                "remark" => "訊號異常轉幹線；[$p_remark]", // 備註
                "Broker" => $p_Broker, // 建單[工程代號+名稱]
                "contactName" => $p_contactName,
                "contactCell" => $p_contactCell,
                "bookdate" => $p_bookdate,
                "timing" => $p_timing,
                "WorkTeam" => $p_workTeam,
            );
            $url = config('order.R1_URL').'/api/cossAPI/v1/repair';
            $curl_data = array (
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
                'timeOut' => '20',
            );
            $obj = New ConsumablesAPIController();
            $result = $obj->curl($curl_data);
            $resultAry = json_decode($result, true);
            $r_code = data_get($resultAry,'code');
            $r_msg = data_get($resultAry,'msg');

            $params['CompanyNo'] = $p_companyNo;
            $params['WorkSheet'] = $p_workSheet;
            $params['CustID'] = $p_custId;
            $params['UserNum'] = $p_userCode;
            $params['UserName'] = $p_userName;
            $params['EventType'] = 'addWorkMain';
            $params['Request'] = '新增[轉幹線]工單;'.json_encode($post_data);
            $params['Responses'] = $result;
            self::addLog($params);

            if(!in_array($r_code,['0000','0099'])) {
                throw new Exception('建立新維修工單異常;result='.$result,'0400');
            }

            $params['p_companyNo'] = $p_companyNo;
            $params['p_workSheet'] = $p_workSheet;
            $params['p_userCode'] = data_get($request,'p_userCode');
            $params['p_userName'] = data_get($request,'p_userName');
            $params['Responses'] = $result;
            $params['p_columnName'] = 'cmqualityforkg';
            $params['p_value'] = "新增[幹線]維修工單，維修原因:$p_remark";
            $b = new Ewo_EventController();
            $b->reqUpdataTime($params);

            $code = '0000';
            $data = $r_msg;

        } catch (Exception $e) {
            $code = $e->getCode();
            $msg = $e->getMessage();
            if($code != '0530') {
                // 插入LOG
                $params['CompanyNo'] = $p_companyNo;
                $params['WorkSheet'] = $p_workSheet;
                $params['CustID'] = $p_custId;
                $params['UserNum'] = $p_userCode;
                $params['UserName'] = $p_userName;
                $params['EventType'] = 'addWorkMain';
                $params['Request'] = '新增[轉幹線]工單;';
                $params['Responses'] = '失敗；'.strval($code).'#'.$msg;
                self::addLog($params);
            }

            $code = ($code)? substr('000'.$code,-4) : '0500';
            $data = $msg;
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => $p_time,
        );

        return $ret;
    }


    static function addLog($params)
    {
        $addLog = new LogRepository();
        $addLog->insertLog($params);
    }
}
?>
