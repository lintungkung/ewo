<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\MyException;
use App\Repositories\Consumables\ConsumablesRepository;
use App\Repositories\Login\LoginBaseRepository;
use App\Repositories\Login\LoginRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;
use Exception;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;
use \Log;

use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderBaseRepository;

class ewoFuncAPIController
{
    private static $OrderRepository;
    private static $OrderBaseRepository;

    public function __construct()
    {
        self::$OrderBaseRepository = new OrderBaseRepository();
        self::$OrderRepository = new OrderRepository(self::$OrderBaseRepository);

    }

    public function __invoke(Request $req, $p1 = null)
    {
        $params = $req->json()->all();
        $func = data_get($params,'func');
        $ret = $this->$func($params);

        return $ret;

    }


    // 新增，[QA]點擊LOG
    static function addQAClickEvent($params)
    {
        $qaId = data_get($params,'qaId');
        $userCode = data_get($params,'userCode');
        $userName = data_get($params,'userName');
        $cacheKey = "addQAClickEvent#$qaId#$userCode";
        $p_time = date('Y-m-d H:i:s');
        $jsonData = $p_time;
        $sec = 60 * 1;
        if (Cache::store('redis')->has($cacheKey)) {
            $data = Cache::store('redis')->get($cacheKey);
            return $data;
        }

        // wm_qa_log
        $data = array(
            'qaId' => $qaId,
            'userCode' => $userCode,
            'userName' => $userName,
        );
        self::$OrderRepository->insertQALog($data);

        Cache::store('redis')->put($cacheKey, $jsonData, $sec);
        return $jsonData;
    }


    // 取得，QA，List
    static function getQAList($params)
    {
        try {
            $query_data = array(
                'tableType' => 'QA',
            );
            $query = self::$OrderRepository->getQAList($query_data);

            $list = array();
            foreach ($query as $k => $t) {
                $vId = $t->Id;
                $vInherit = $t->inherit;
                if(empty($vInherit))
                    $list['0'][] = $t;
                else
                    $list[$vInherit][] = $t;
            }

            $code = '0000';
            $data = $list;

        } catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
            $code = $e->getCode()? substr('000'.$e->getCode(),'-4') : '0500';
            $data = '資料錯誤'.$e->getMessage();
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => date('Y-m-d H:i:s'),
        );
        return $ret;

    }


    // 取得，出班檢查，資訊
    static function getCOImg($params)
    {
        $vUserCode = data_get($params,'userCode');
        $vUserName = data_get($params,'userName');
        $vTime = date('Y-m-d');

        try {
            if(empty($vUserCode) || empty($vUserName)) {
                throw new Exception('請登入','0540');
            }
            $query_data = array(
                'userCode' => $vUserCode,
                'desc1' => $vTime,
                'type' => 'D.出班檢查',
            );
            $query = self::$OrderRepository->getLaborsafetylog($query_data);
            $vDesc2 = data_get($query,'Desc2');
            $vDesc2Ary = empty($vDesc2)? '' : json_decode($vDesc2,true);
            data_set($query,'userMang',data_get($vDesc2Ary,'userMang'));

            $code = '0000';
            $data = $query;

        } catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
            $code = $e->getCode()? substr('000'.$e->getCode(),'-4') : '0500';
            $data = '資料錯誤'.$e->getMessage();
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => date('Y-m-d H:i:s'),
        );
        return $ret;

    }


    // 取得工程，訊息清單[當天]
    static function getUserMsg($params)
    {
        $data = array();

        try {

            $p_userCode = data_get($params,'userCode');
            $p_userName = data_get($params,'userName');
            $p_timeNow = date('Y-m-d H:i:s');
            $p_timeStart = date('Y-m-d');
            $p_timeEnd = date('Y-m-d',strtotime('+1 Day'));

            $query_data = array(
                'companynoin' => config('company.companyNoAry'),
                'usercode' => $p_userCode,
                'timestart' => $p_timeStart,
                'timeend' => $p_timeEnd,
            );

            $query = self::$OrderRepository->getPushMsg($query_data);

            $query_data['usercode'] = $p_userCode;
            $query_data['read_at'] = $p_timeNow;

            $code = '0000';
            $status = 'OK';
            $msg = '成功';
            $data = $query;

        }  catch (MyException $e) {
            $code = '0501';
            $status = 'error';
            $msg = '資料錯誤'.$e->getMessage();

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0502';
            $status = 'error';
            $msg = '資料錯誤'.$e->getMessage();

        }

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'msg' => $msg,
            'data'=>$data,
        );

        return $p_data;
    }


    // 取得，建議設備清單
    static function getPlanDeviceList($params)
    {
        $data = array();

        $deviceAry = config('device.ChargeName');

        try {

            $p_userCode = data_get($params,'userCode');
            $p_userName = data_get($params,'userName');
            $p_timeNow = date('Y-m-d H:i:s');
            $p_day = date('Ymd');
            $p_timeStart = date('Y-m-d');
            $p_timeEnd = date('Y-m-d',strtotime('+1 Day'));
            $orderNew = array();
            $cacheKey = "getPlanDeviceList#$p_userCode#$p_day";
            $sec = 60 * 60;

            if (Cache::store('redis')->has($cacheKey)) {
                $data = Cache::store('redis')->get($cacheKey);
                $ret = json_decode($data,1);
                return $ret;
            }

            $data = array(
                'userId' => $p_userCode.'',
                'bookdate_s' => date('Y-m-d 00:00:00'),
                'bookdate_e' => date('Y-m-d 00:00:00',strtotime('+2 day')),
//                'assignsheet' => 'A2022030014548',
            );

            $orderList = self::$OrderRepository->getOrderList($data);
            foreach($orderList as $k => $t) {
                $assignsheet = data_get($t,'AssignSheet');
                $workkind = data_get($t,'WorkKind');
                $chargename = data_get($t,'ChargeName');
                $sheetstatus = data_get($t,'SheetStatus');

                // 設備清單
                $workkindType = config('order.CahrgeBackType.'.$workkind);
                if($sheetstatus < '4' && $workkindType !== '11') {
                    $deviceStr = data_get($deviceAry,$chargename);
//                    if(!empty($deviceStr))
                        $orderNew[$assignsheet]['planDevice'][$chargename] = $deviceStr;
                }
            }


            $code = '0000';
            $status = 'OK';
            $msg = '成功';
            $data = $orderNew;

        }  catch (MyException $e) {
            $code = '0501';
            $status = 'error';
            $msg = '資料錯誤'.$e->getMessage();

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0502';
            $status = 'error';
            $msg = '資料錯誤'.$e->getMessage();

        }

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'msg' => $msg,
            'data'=>$data,
        );

        $jsonData = json_encode($p_data);
        Cache::store('redis')->put($cacheKey, $jsonData, $sec);


        return $p_data;
    }


    // 取得統計
    static function getStatistics($params)
    {

        try {
            $userCode = data_get($params,'userCode');
            $timeS = date('Y-m-d 00:00:00');
            $timeE = date('Y-m-d 23:59:59');


            $query_data = array(
                'usercode' => $userCode,
                'timestart' => $timeS,
                'timeend' => $timeE,
                'receiveType' => '',
            );
            $query = self::$OrderRepository->getStatistics($query_data);

            $cash = 0;
            $swipe = 0;
            $cashList = $swipeList = array();
            foreach ($query as $k => $t) {
                if ($t->receiveType === '1') {
                    $swipeList[] = $t;
                    $swipe += intval($t->receiveMoney);
                } else if ($t->receiveType === '2') {
                    $cashList[] = $t;
                    $cash += intval($t->receiveMoney);
                }
            }

            $code = '0000';
            $data = array(
                'cash' => $cash,
                'swipe' => $swipe,
                'cashList' => $cashList,
                'swipeList' => $swipeList,
            );

        }  catch (Exception $e) {
             $code = $e->getCode() ?
                 '05000' : substr('000' . $e->getCode(), -4);
             $data = $e->getMessage();

        }

        $ret = array(
            'code' => $code,
            'data'=>$data,
            'date' => date("Y-m-d H:i:s"),
        );

        return $ret;

    }


    // 取得設備清單
    static function getDeviceList($params)
    {
        $userCode = data_get($params,'userCode');
        $userName = data_get($params,'userName');

        $code = '0000';
        try {

            if(empty($userCode) || empty($userName)) {
                throw new Exception('參數不完整','0540');
            }

            $baseUser = new LoginBaseRepository();
            $dbUser = new LoginRepository($baseUser);
            $queryUser = $dbUser->getUserInfo($userCode);
            $placeno = data_get($queryUser,'PlaceNo');

            if(empty($placeno)) {
                throw new Exception('(倉位)參數不完整','0550');
            }

            $db = new ConsumablesRepository();

            $data = array(
                'placeno' => $placeno,
                'instore' => 'Y',
            );
            $query = $db->getDevLisFroPla($data);

            $list = array();
            $companyNoList = array();
            foreach($query['list'] as $k => $t) {
                $companyno = data_get($t,'CompanyNo');
                $backTime = data_get($t,'BackTime');

                if(!in_array($companyno,$companyNoList)) {
                    array_push($companyNoList, $companyno);
                }

                if(empty($backTime)) {
                    $list[$companyno]['product'][] = $t;
                } else {
                    $list[$companyno]['recycle'][] = $t;
                }
            }

            $data = array(
                'list' => $list,
                'companyNoList' => $companyNoList,
                'companyNoStrAry' => config('company.database'),
                'type' => array(
                    'recycle' => '回收',
                    'product' => '良品',
                ),
            );

        } catch (Exception $e) {
            $code = $e->getCode()? substr('000'.$e->getCode(),-4) : '0500';
            $data = 'Error:'.$e->getMessage();
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => date('Y-m-d H:i:s'),
        );

        return $ret;

    }


    // 補簽名清單
    static function getAddSignList($params)
    {
        $userCode = data_get($params,'userCode');
        $userName = data_get($params,'userName');

        $code = '0000';
        try {

            if(empty($userCode) || empty($userName)) {
                throw new Exception('參數不完整','0540');
            }

            $baseUser = new LoginBaseRepository();
            $dbUser = new LoginRepository($baseUser);
            $queryUser = $dbUser->getUserInfo($userCode);
            $vIsEnable = data_get($queryUser,'IsEnable');

            if($vIsEnable != '1') {
                throw new Exception('帳號沒有啟用','0550');
            }

            $query_data = array(
                'worknum'=>$userCode,
                'signauthorization'=>'signauthorization',
            );
            $data = self::$OrderRepository->getOrderInfo($query_data,'all');

            foreach ($data as $t) {
                $servicename = data_get($t,'ServiceName');
                $servicename = str_replace('"','',$servicename);
                $servicename = str_replace('[','',$servicename);
                $servicename = str_replace(']','',$servicename);
                $servicenameAry = explode(',',$servicename);
                data_set($t,'servicenamelist',$servicename);
                data_set($t,'servicenameary',$servicenameAry);

                $custid = data_get($t,'CustID');
                $bookdate = data_get($t,'BookDate');
                $bookdateStr = str_replace('-','',$bookdate);
                $bookdateStr = substr($bookdateStr,0,8);
                $forder = $custid.'_'.$bookdateStr;
                data_set($t,'forder',$forder);

                $telenum01_200 = data_get($t,'TeleNum01_200');
                $telenum01 = data_get($t,'TeleNum01');
                $telenum02_200 = data_get($t,'TeleNum02_200');
                $phoneAry = array($telenum01_200,$telenum01,$telenum02_200);
                $phoneAry = array_unique($phoneAry);
                $phoneAry = array_values($phoneAry);
                $phonelist = implode(',',$phoneAry);
                data_set($t,'phonelist',$phonelist);
            }

        } catch (Exception $e) {
            $code = $e->getCode()? substr('000'.$e->getCode(),-4) : '0500';
            $data = 'Error:'.$e->getMessage();
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => date('Y-m-d H:i:s'),
        );

        return $ret;

    }


}
?>
