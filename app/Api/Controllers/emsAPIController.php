<?php

namespace App\Api\Controllers;

use App\Model\ems_api_log;
use App\Model\MI0130;
use App\Repositories\Consumables\ConsumablesRepository;
use App\Repositories\EMS\emsRepository;
use App\Repositories\ETSLog\ETSLogRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Response;
use thiagoalessio\TesseractOCR\Tests\Common\SkipException;
use Validator;
use \Log;
use Session;
use Exception;
use App\Http\Controllers\MyException;
use Illuminate\Http\Request;

class emsAPIController extends Controller
{

    static private $api_consumables_url = 'http://172.17.86.208:8000';

    public function __construct()
    {
        //
    }

    static public function index(Request $request,$p1)
    {
        if(empty($p1)) {
            $ret = 'Consumables API';
        }
        $id = md5($p1.time().rand());

        $params = $request->json()->all();

        self::insertApiLog('request',$p1,$id,$params);

        if (method_exists(New emsAPIController, $p1)) {
            $ret = self::$p1($params);
        } else {
            $ret = array(
                'code' => '0500',
                'msg' => 'Error',
                'date' => date('Y-m-d H:i:s'),
                'retData' => "Api[$p1] is not exists;",
            );
        }

        self::insertApiLog('reponse',$p1,$id,$ret);

        return Response::json($ret);
    }

    // insert api log
    static function insertApiLog($type,$p1,$id,$params)
    {
        ems_api_log::insertGetId([
            'api' => $p1,
            'requestId' => $id,
            'type' => $type,
            'text' => json_encode($params),
        ]);
    }

    // API，工令合約
    static function getContact($request)
    {
        $code = '0000';
        $msg = 'OK';
        $p_time = date('Y-m-d H:i:s');
        $data = '';

        try {

            $validator = Validator::make($request
                , [
                    'companyNo' => 'required',
                    'useKind' => 'required',
                    'costCenter' => 'required',
                ]
                ,[
                    'companyNo.required' => '請輸入[公司別]',
                    'useKind.required' => '請輸入[用途別]',
                    'costCenter.required' => '請輸入[成本中心]',
                ]
            );

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0], '0530');
            }

            $companyNo = data_get($request,'companyNo');
            $useKind = data_get($request,'useKind');
            $costCenter = data_get($request,'costCenter');

            $obj = new emsRepository();
            $whereAry = array(
                'companyNo' => $companyNo,
                'useKind' => $useKind,
                'costCenter' => $costCenter,
            );
            $query = $obj->getContact($whereAry);
            $contractAry = array();
            foreach($query as $k => $t) {
                $contractAry[] = data_get($t,'Contract');
            }
            $data = array('ContactList' => $contractAry);

        } catch (Exception $e) {
            $code = $e->getCode();
            $code = ($e->getCode())? substr('000'.$code,-4) : '0500';
            $msg = 'error:'.$e->getMessage();
        }

        $ret = array(
            'code' => $code,
            'msg' => $msg,
            'date' => $p_time,
            'retData' => $data,
        );

        return $ret;
    }


    // API，查詢倉位內容
    static public function QueryPlaceDetail($request)
    {
        $p_time = date('Y-m-d H:i:s');
        $data = array();

        try {

            $validator = Validator::make($request, [
                    'companyNo' => 'required',
                    'placeNo' => 'required',
                ],
                [
                    'companyNo.required' => '請輸入[公司別]',
                    'placeNo.required' => '請輸入[倉位]',
                ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0],'0530');
            }

            $companyNo = data_get($request,'companyNo');
            $placeNo = data_get($request,'placeNo');

            $obj = new emsRepository();
            $whereAry = array(
                'companyno' => $companyNo,
                'placeno' => $placeNo,
            );
            $qryLst = $obj->getPlaceDetail($whereAry);

            $data = $qryLst;
            $code = '0000';
            $msg = 'OK';

        } catch (Exception $e) {
            $code = $e->getCode();
            $code = ($e->getCode())? substr('000'.$code,-4) : '0500';
            $msg = 'error:'.$e->getMessage();

        }

        $ret = array(
            'code' => $code,
            'msg' => $msg,
            'date' => $p_time,
            'retData' => $data,
        );

        return $ret;

    }


    // EMS匯入>>耗料清單
    static public function EATranNo($request)
    {
        $p_time = date('Y-m-d H:i:s');

        $code = '';
        $msg = '';
        $retData = array();

        try {

            $validator = Validator::make($request, [
                    'companyNo' => 'required',
                    'deptName' => 'required',
                    'userCode' => 'required',
                    'placeNo' => 'required',
                    'keyInNo' => 'required',
                    'contractorCode' => 'required',

            ], [
                    'companyNo.required' => '請輸入[公司別]',
                    'deptName.required' => '請輸入[工程單位]',
                    'userCode.required' => '請輸入[工程代號]',
                    'placeNo.required' => '請輸入[工程倉位]',
                    'keyInNo.required' => '請輸入[文號]',
                    'contractorCode.required' => '請輸入[承商代號]',
                ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0],'0530');
            }

            $companyNo = data_get($request,'companyNo');
            $deptName = data_get($request,'deptName');
            $userCode = data_get($request,'userCode');
            $placeNo = data_get($request,'placeNo');
            $keyInNo = data_get($request,'keyInNo');
            $hardwareList = data_get($request,'hardwareList');
            $contractorCode = data_get($request,'contractorCode');

            $count = 0;
            $listAry = array();
            foreach ($hardwareList as $k => $t) {
                $usekind = data_get($t,'useKind');
                $costcenter = data_get($t,'costCenter');
                $contract = data_get($t,'contract');
                $mtno = data_get($t,'mtno');
                $tranqty = data_get($t,'tranQty');

                $listAry[] = array(
                    'UseKind' => $usekind,
                    'CostCenter' => $costcenter,
                    'Contract' => $contract,
                    'MTNo' => $mtno,
                    'TranQty' => intval($tranqty),
                );
                $count += 1;

            }

            $jsonAry = array(
                'CompanyNo' => $companyNo,
                'DeptNoName' => $deptName,
                'UserName' => $userCode,
                'PlaceNo' => $placeNo,
                'KeyInNo' => $keyInNo,
                'SingleSNList' => [],
                'HardwareList' => $listAry
            );

            $jsonStr = json_encode($jsonAry);

            $obj = new emsRepository();
            $addAry = array(
                'CompanyNo' => $companyNo,
                'WorkSheet' => $keyInNo,
//                'SubsID' => '',
//                'WorkKind' => '',
//                'ServiceName' => '',
//                'SheetStatus' => '',
//                'BookDate' => '',
//                'FinishTime' => '',
                'sourceType' => 'API',
                'api' => '領料',
                'MTNo' => 'null',
                'Qty' => 'null',
                'Worker1' => $contractorCode,
                'importTime' => $p_time,
//                'implement' => '',
                'request' => $jsonStr,
//                'reponse' => '',
//                'retCode' => '',
//                'retMsg' => '',
                'update_at' => $p_time,
                'create_at' => $p_time,

            );
            $id = $obj->addHardwareMaterialsPicking($addAry);

            if(empty($id)) {
                throw new MyException('新增失敗','0410');

            } else {
                // 領料
                $url = 'http://172.17.86.208:8000'.'/COSSMI/EATranNo';
                $result = self::curl($url,$jsonStr);
                $resultJson = empty($result)? '' : json_decode($result,true);
                $retCode = strlen(strval(data_get($resultJson,'RetCode')))? strval(data_get($resultJson,'RetCode')) : '0540';
                $retMsg = empty(data_get($resultJson,'RetMsg'))? 'Error' : data_get($resultJson,'RetMsg');

                if($retCode == '0') {
                    $reponse = $result;
                    $msg .= "領料:{$retMsg}";
                    $retCode = '0000';
                } else {
                    $reponse = $result;
                    $msg .= "領料:失敗;code:{$retCode};MSG:{$retMsg};reponse:{$reponse}";
                }
                $updateAry = array(
                    'id' => $id,
                    'reponse' => $reponse,
                    'retCode' => $retCode,
                    'retMsg' => $retMsg,
                );
                $obj->updateHardwareMaterialsPicking($updateAry);

            }

            $retData = "匯入 $count 筆";
            $code = $retCode;

        } catch (Exception $e) {
            $code = ($e->getCode() > 0)? substr('0000'.$code,-4) : '0500';
            $msg = empty($e->getMessage())? 'error' : $e->getMessage();

        }

        $ret = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $retData,
            'date' => $p_time,
        );

        return $ret;

    }


    static public function curl($url,$data)
    {
        $header = array('Content-Type: application/json');

        try {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $ret = curl_exec($ch);
            curl_close($ch);

        } catch (Exception $e) {
            $ret = array(
                'code' => $e->getCode(),
                'data' => 'CURL[Error]'.$e->getMessage(),
                'date' => date('Y-m-d H:i:s'),
            );
        }

        return $ret;
    }

}
