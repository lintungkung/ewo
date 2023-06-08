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

class ewoAPIController
{

    public function __invoke(Request $req, $p1 = null)
    {
        $params = $req->json()->all();
        if(empty($p1)) {
            $ret = 'EWO_API';
        } else {
            switch ($p1) {
                case 'getFault':
                case 'getImgScanStr':
                case 'upWMOrderList':
                    $ret = $this->$p1($params);
                    break;
                case 'apiOpinion':
                case 'queryFTTH':
                case 'updSignMengineer':
                    $path = 'App\Api\Controllers\\'.$p1;
                    $ret = $path::getResult($req);
                    break;
                default:
                    $path = 'App\Api\Controllers\\'.$p1;
                    $ret = $path::getResult($params);
                    break;
            }
        }


        return Response::json($ret);
    }


    /**
     * 區故
     * 一個月區故 join 一個月的建單日
     */
    static public function getFault($params)
    {
        $vKey = 'ewo_getFault_'.date('Ymd');
        $sec = (60*60) * 2;
        $redis = app('redis.connection');
        $p_companyNo = data_get($params,'companyNo');
        $p_custId = data_get($params,'custId');
        $p_date = data_get($params,'date');
        $p_date = date('Y-m-d',strtotime($p_date));
        $p_time = date('Y-m-d H:i:s');
        $code = '0000';

        if($redis->exists($vKey)) {
            $data = $redis->get($vKey);

            if(empty($data)) {
                $code = '0548';
                $data = '查無DB';
            } else {
                $data = json_decode($data,true);
            }

            $ret = array(
                'code' => $code,
                'data' => $data,
                'date' => $p_time,
            );

            if(empty($p_custId) || empty($p_date) || empty($p_companyNo)) {
                return $ret;
            }
        }

        // 建單日，一個月前；住編、LinkID；查詢
        $sql = <<<EOF
            SELECT aa.CompanyNo,aa.CustID,aa.LinkID
            FROM COSSDB.dbo.MS0300 aa
            LEFT JOIN COSSDB.dbo.MS0301 bb ON aa.companyno = bb.CompanyNo AND aa.WorkSheet = bb.AssignSheet
            WHERE 1=1
            AND aa.CreateTime >= DATEADD(MONTH, -1,GETDATE())
            GROUP BY aa.CompanyNo,aa.CustID,aa.LinkID
            ;
EOF;
        $query = DB::connection('WMDB')->select($sql);
        $custIDList = array();
        foreach ($query as $k => $t) {
            $vCompanyNo = $t->CompanyNo;
            $vCustID = $t->CustID;
            $vLinkID = $t->LinkID;
            $custIDList[$vCompanyNo][$vLinkID][] = $vCustID;
        }

        // 區故清單
        $sql = <<<EOF
        SELECT
            aa.CompanyNo,aa.LinkID,aa.EventReason,aa.EventKind,aa.EventTime,aa.WishTime,aa.RecoverTime
        FROM cossdb.dbo.CNS000 aa
        WHERE 1=1
            AND aa.EventNo_Remark != 'D'
            AND aa.EventTime > DATEADD(MONTH, -1,GETDATE())
        ORDER BY aa.EventTime DESC
    ;
EOF;
        $query = DB::connection('WMDB')->select($sql);
        $list = array();
        foreach($query as $k => $t) {
            $vCompanyNo = $t->CompanyNo;

            $vEventTime = $t->EventTime;
            $vEventTimeDay = substr($vEventTime,0,10);

            $vLinkID = $t->LinkID;
            $vLinkIDAry = explode(',',$vLinkID);

            $vEventReason = $t->EventReason;
            $vEventKind = $t->EventKind;
            $vWishTime = $t->WishTime;
            $vRecoverTime = $t->RecoverTime;
            foreach ($vLinkIDAry as $t2) {
                if(empty($t2)) continue;
                if(!isset($custIDList[$vCompanyNo][$t2]))
                    continue;

                // 一個月內建單的區故清單
                foreach ($custIDList[$vCompanyNo][$t2] as $k3 => $vCustID) {
                    $list[$vCompanyNo][$vEventTimeDay][$vCustID] = array(
                        'LinkID' => $t2,
                        'EventReason' => $vEventReason,
                        'EventKind' => $vEventKind,
                        'EventTime' => $vEventTime,
                        'WishTime' => $vWishTime,
                        'RecoverTime' => $vRecoverTime,
                        'time' => $p_time,
                    );
                }
            }
            $a = '';
        }
        $listJson = json_encode($list);
        $redis->set($vKey,$listJson);
        $redis->expire($vKey,$sec);

        $data = $list;

        // 即時查詢
        if(!empty($p_custId.$p_date.$p_companyNo)) {
            // 確認資料完整
            if(empty($p_companyNo) || empty($p_custId) || empty($p_date)) {
                $code = '0535';
                $data = '請確認資料完整['."$p_companyNo#$p_custId#$p_date".']';
            } else {
                if(isset($list[$p_companyNo][$p_date][$p_custId])) {
                    $data = $list[$p_companyNo][$p_date][$p_custId];
                } else {
                    $data = '查無資料';
                }
            }
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => $p_time,
        );

        return $ret;
    }


    // API，圖片掃描結果
    static public function getImgScanStr($request)
    {
        $p_time = date('Y-m-d H:i:s');
        $data = array();

        try {

            $validator = Validator::make($request, [
//                'path' => 'required',
            ],[
//                'path.required' => '請輸入[路徑2]',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0],'0530');
            };

            $path = data_get($request,'path');

            $file_url = public_path('upload/'.$path);
            $timeout_ms = 20;

            $orc = (new TesseractOCR(public_path($file_url),$timeout_ms))->lang('chi_tra')->run();

            $data = $orc;
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


    /**
     * 更新 wm_orderList()
     */
    static public function upWMOrderList($request)
    {
        $a = '';
        $code = '0000';
        $data = 'OK';
        try {
            $base = new OrderBaseRepository();
            $obj = new OrderRepository($base);

            $validator = Validator::make($request
                ,[
                    'companyNo' => 'required',
                    'workSheet'=> 'required',
                    'userCode'=> 'required',
                ]
                ,[
                    'companyNo.required' => '請輸入公司別',
                    'workSheet.required'=> '請輸入工單號碼',
                    'worker.required'=> '請輸入工程人員代號',
                ]
            );

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0]);
            }

            $companyNo = data_get($request,'companyNo');
            $workSheet = data_get($request,'workSheet');
            $userCode = data_get($request,'userCode');

            $paidData = array(
                'so' => $companyNo,
                'worksheet' => $workSheet,
                'worker1like' => "$userCode%",
            );
            $chargeInfo = $obj->getOrderCharge($paidData,true);
            $jsonChargeInfo = json_encode($chargeInfo);

            $paidData['orderInfoList'] = $jsonChargeInfo;
            $obj->updateOrderInfoList($paidData);
        } catch (Exception $exception) {
            $code = $exception->getCode();
            $code = empty($code)? '0500' : substr('000'.$code,-4);
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
