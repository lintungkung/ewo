<?php

namespace App\Api\Controllers;

use App\Model\MI0130;
use App\Repositories\Consumables\ConsumablesRepository;
use App\Repositories\ETSLog\ETSLogRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Response;
use Validator;
use \Log;
use Session;
use Exception;
use App\Http\Controllers\MyException;
use Illuminate\Http\Request;

class ConsumablesAPIController extends Controller
{

    static private $api_consumables_url = 'http://172.17.86.208:8000';

    public function __construct()
    {
        //
    }

    static public function index(Request $request,$p1)
    {
        $ret = self::$p1($request);

        return Response::json($ret);

    }


    // 調撥API
    static public function apiAllot($request)
    {
        $p_time = date('Y-m-d H:i:s');
        $url = self::$api_consumables_url . '/COSSMI/EGAllot';
        $singlesn = data_get($request,'singlesn');
        $companyno = data_get($request,'companyno');
        $username = data_get($request,'username');
        $usercode = data_get($request,'usercode');
        $deptnoname = data_get($request,'deptnoname');
        $placenoout = data_get($request,'placenoout');
        $placenoin = data_get($request,'placenoin');
        $keyinno = data_get($request,'keyinno');
        $mtno = data_get($request,'mtno');
        $csmodel = data_get($request,'csmodel');
        $createType = data_get($request,'sourcetype');
        $controller = data_get($request,'controller');
        $controller = empty($controller)? '' : "[$controller]";
        $resultDec = '';
        $result = '';

        $post_data = array (
            "CompanyNo" => $companyno,
            "UserName" => $username,
            "DeptNoName" => $deptnoname,
            "KeyInNo" => $keyinno,
            "PlaceNoOut" => $placenoout,
            "PlaceNoIn" => $placenoin,
            "SingleSN" => [$singlesn],
        );

        $curl_data = array (
            'url'       => $url,
            'method'    => 'post',
            'header'    => 'json',
            'post_data' => $post_data,
            'timeOut' => '20',
        );

        try {

            $validator = Validator::make($request->all(), [
                    'companyno' => 'required',
                    'username' => 'required',
                    'deptnoname' => 'required',
                    'placenoout' => 'required',
                    'placenoin' => 'required',
                    'singlesn' => 'required',
                ],
                [
                    'companyno.required' => '請輸入[公司別]',
                    'username.required' => '請輸入[經手人]',
                    'deptnoname.required' => '請輸入[調撥人部門]',
                    'placenoout.required' => '請輸入[撥出儲位]',
                    'placenoin.required' => '請輸入[撥入儲位]',
                    'singlesn.required' => '請輸入[單品序號]',

                ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0],'0530');
            };

            $result = self::curl($curl_data);
            $resultDec = json_decode($result,1);
            if (data_get($resultDec,'RetCode') != '0') {
                $result = 'API result is error#'.$result;
                $error = '調撥失敗；'.data_get($resultDec,'RetCode').'#'.data_get($resultDec,'RetMsg');
                throw new Exception($error,'5'.data_get($resultDec,'RetCode'));
            }

            $code = '0000';
            $msg = '成功';
            $data = array(
                'request' => json_encode($post_data),
                'reponse' => $result,
                'singlesn' => $singlesn,
            );

        } catch (Exception $e) {
            $code = $e->getCode();
            $code = ($e->getCode())? substr('000'.$code,-4) : '0500';
            $msg = 'error:'.$e->getMessage();
            $data = array(
                'request' => json_encode($post_data),
                'reponse' => $result,
                'singlesn' => $singlesn,
            );

        }

        // LOG紀錄
        $params = array();
        $params['userCode'] = $usercode;
        $params['userName'] = $username;
        $params['companyNo'] = $companyno;
        $params['dept'] = $deptnoname;
        $params['singleSN'] = $singlesn;
        $params['mtno'] = "$mtno";
        $params['csmodel'] = "$csmodel";
        $params['placeNoOut'] = "$placenoout";
        $params['placeNoIn'] = "$placenoin";
        $params['request'] = json_encode($post_data);
        $params['reponse'] = "$result";
        $params['retCode'] = data_get($resultDec,'RetCode');
        $params['retMsg'] = $controller.data_get($resultDec,'RetMsg');
        $params['apiType'] = "調撥";
        $params['createType'] = $createType;
        $obj = new ETSLogRepository();
        $obj->insertAllotLog($params);

        $ret = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'date' => $p_time,
        );

        return $ret;

    }


    // 設備序號[Detail]
    static public function getDeviceDetail($request)
    {

        if(is_string($request)) {
            $request = json_decode($request,true);
        } else {
            $request = $request->json()->all();
        }
        $p_time = date('Y-m-d H:i:s');
        $r_start = microtime(true);

        $code = '';
        $msg = '';
        $data = [];
        $date = $p_time;

        try {
            $companyNoAry = array('209','210','220','230','240','250','270','310','610','620','720','730');
            $companyno = data_get($request,'companyno');
            $singlesn = data_get($request,'singlesn');
            $singlesn = str_replace("\t",'',$singlesn);
            $qryTyp = data_get($request,'type');
            $devInfo = data_get($request,'devInfo');

            if(empty($singlesn)) {
                throw new Exception('請輸入[設備序號]','0401');
            }

            $obj = new ConsumablesRepository();

            $defaultAmt = '';
            if($qryTyp === 'default') {
                // 違約金
                $defaultAmt = self::getDefaultAmt($singlesn);
            }

            // 倉位
            $data = array(
                'singlesn' => $singlesn,
                'companyno' => $companyno,
            );
            $devicInfo = $obj->getDevLisFroPla($data);
            if(count($devicInfo['list']) < 1) {
                throw new Exception('查無設備');
            }
            $devicInfo = $devicInfo['list'][0];

            // 狀態
            $devInfo = $devInfo == 'Y'? self::getDeviceStatus($devicInfo) : $devInfo;

            $placeno = data_get($devicInfo,'PlaceNo');
            $companyno = data_get($devicInfo,'CompanyNo');
            $csmodel = data_get($devicInfo,'CSModel');
            $mtno = data_get($devicInfo,'MTNo');
            $instore = data_get($devicInfo,'InStore');
            $code = '0000';
            $msg = 'OK';
            $data = array(
                'placeno' => $placeno,
                'companyno' => $companyno,
                'singlesn' => $singlesn,
                'csmodel' => $csmodel,
                'mtno' => $mtno,
                'amt' => $defaultAmt,
                'instore' => $instore,
                'devInfo' => $devInfo,
            );

        } catch (Exception $e) {
            $code = $e->getCode();
            $code = ($e->getCode() > 0)? substr('0000'.$code,-4) : '0500';
            $msg = empty($e->getMessage())? 'error' : $e->getMessage();

        }

        $r_run = $r_start - microtime(true);
        $data['run'] = $r_run;

        $ret = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'date' => $date,
        );

        return $ret;

    }


    // 檢查設備 狀態
    static public function getDeviceStatus($params)
    {
        $companyno = data_get($params,'CompanyNo');
        $singlesn = data_get($params,'SingleSN');
        $url = 'http://172.17.87.143/api/CMAPI/CM/QueryRMADevice';
        $post_data = array (
            "so" => "$companyno",
            "singlesn" => "$singlesn"
        );

        $curl_data = array (
            'url'       => $url,
            'method'    => 'post',
            'header'    => 'json',
            'post_data' => $post_data,
            'timeOut' => '20',
        );
        $result = self::curl($curl_data);
        $resultAry = json_decode($result,true);
        $v_RetCode = data_get($resultAry,'RetCode');
        $v_RetMsg = data_get($resultAry,'RetMsg');

        switch ($v_RetCode) {
            case '0':
            case '127':
                $ret = $v_RetMsg;
                break;
            default:
                $ret = '設備資訊查詢失敗['.$v_RetCode.']'.$v_RetMsg;
                break;
        }

        return $ret;
    }


    // 查詢設備違約金
    static public function getDefaultAmt($singlesn)
    {
        try {

            $db = new ConsumablesRepository();
            $qryParam = array(
                'companynoIN' => ['209','210','220','230','240','250','270','310','610','620','720','730'],
                'singlesn' => $singlesn,
                'orderby' => ['name'=>'TriggerTime','type'=>'desc'],
                'start' => '0',
                'limit' => '1',
            );
            $qrySubsid = $db->getMS0211($qryParam);
            $qrySubsid = data_get($qrySubsid,'0');
            $subsid = data_get($qrySubsid,'SubsID');
            $companyno = data_get($qrySubsid,'CompanyNo');

            if(empty($subsid)) {
                throw new Exception('0');
            }

            $qryParam['subsid'] = $subsid;
            $qryParam['companyno'] = $companyno;
            $qryCustid = $db->getMS0200($qryParam);
            $qryCustid = data_get($qryCustid,'0');
            $custid = data_get($qryCustid,'CustID');

            if(empty($custid)) {
                throw new Exception('0');
            }

            $qryParam = array(
                'companynoIN' => array('209','210','220','230','240','250','270','310','610','620','720','730'),
                'custid' => $custid,
                'chargekindIN' => array('71','75','76'),
                'sheetstatus' => '4',
                'recvyn' => 'N',
                'passyn' => 'N',

            );

            $ms0301Amt = $db->getDefaultAmtMS0301($qryParam);
            $ms0301Amt = data_get($ms0301Amt[0],'amt');

            $ms3200Amt = $db->getDefaultAmtMS3200($qryParam);
            $ms3200Amt = data_get($ms3200Amt[0],'amt');

            $sumAmt = intval($ms0301Amt) + intval($ms3200Amt);

            $ret = $sumAmt;

        } catch (Exception $e) {
            $ret = $e->getMessage();

        }

        return $ret;

    }


    static public function curl($data)
    {

        try {
            Log::channel('curl')->info('******Curl start******');
            Log::channel('curl')->info('Curl data: '.print_r($data,true));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $data['url']);

            if ($data['method'] == 'post') {
                curl_setopt($ch, CURLOPT_POST, true);
                $data['post_data'] = json_encode($data['post_data'],JSON_UNESCAPED_UNICODE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post_data']);
            }

            if ($data['header'] == 'json') {
                $header = array('Content-Type: application/json');
                //$header = array('Content-Type: text/plain');

                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            }

            $timeOut = isset($data['timeOut'])? $data['timeOut'] : 5;

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeOut);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $result = curl_exec($ch);

            curl_close($ch);


            Log::channel('curl')->info('curl result: '.print_r($result,true));
            Log::channel('curl')->info('******Curl END******');

            return $result;


        } catch (Exception $e) {
            Log::channel('curl')->info('******Curl error******');
            Log::channel('curl')->info('error msg: '.print_r($e->getMessage(),true));
        }
    }

}
