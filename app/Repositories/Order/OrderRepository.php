<?php

namespace App\Repositories\Order;

use App\Repositories\Order\OrderBaseRepository;
use http\QueryString;
use \Log;
use DB;
use Exception;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    private $COSSDB;

    public function __construct(OrderBaseRepository $OrderBaseRepository)
    {
        $this->OrderBaseRepository = $OrderBaseRepository;
        $this->db = $this->OrderBaseRepository;
        $this->COSSDB = config('order.COSSDBTYPE').'.dbo';
    }


    public function getOrderList($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0301 AS MS301")->lock('WITH(nolock)'));

            $this->OrderBaseRepository->leftJoinMS0300_Orderlist();

            $this->OrderBaseRepository->leftJoinWM_OrderList();

            $this->OrderBaseRepository->leftJoinMS0100();

            $selectAry = array(
                'MS300.CustName','MS300.TeleNum01','MS300.CellPhone01','MS300.TeleNum2','MS300.CellPhone2','MS300.CallinTele','MS300.FinishDate','MS300.BookDate','MS300.MSCityA','MS300.InstAddrName','MS300.WorkKind','MS300.MSComment1','MS300.FinishDate','MS300.PrintBillYN','MS300.CallCause','MS300.BillItem2','MS300.SaleCampaign'
                ,'MS301.WorkSheet','MS301.CompanyNo','MS301.CustID','MS301.AssignSheet','MS301.WorkTeam','MS301.Worker1','MS301.Worker2','MS301.SheetStatus','MS301.ChargeName','MS301.BillAmt','MS301.SubsID','MS301.ServiceName','MS301.ChargeKind','MS301.MSRemark','MS301.BrokerKind','MS301.PackageName','MS301.BookingNo'
                ,'MS010.MailTitle'
                ,'wmOrderList.expected','wmOrderList.Id','wmOrderList.receiveMoney','wmOrderList.receiveType','wmOrderList.checkin','wmOrderList.pdf_v','wmOrderList.WorkSheet as wmWorkSheet'
                ,'wmOrderList.CustID as wmCustID'
            );
            $this->db = $this->OrderBaseRepository->selectOrderInfo($selectAry);

            $this->OrderBaseRepository->addSelectRaw("
                CASE WHEN MS300.WorkKind = '5 維修' THEN (
                    SELECT COUNT(bb.CustID) FROM $this->COSSDB.MS0300 aa WITH(nolock) LEFT JOIN $this->COSSDB.MS0301 bb WITH(nolock) ON aa.CompanyNo=bb.CompanyNo AND aa.WorkSheet = bb.AssignSheet
                    WHERE 1=1 AND aa.BookDate BETWEEN CONVERT(varchar(10) ,DATEADD(DAY, -8, getDate() ),120 ) AND CONVERT(varchar(10) ,DATEADD(DAY, -1, getDate() ),120 )
                    AND aa.CompanyNo = MS301.CompanyNO AND aa.CustId = MS301.CustId AND bb.SheetStatus NOT IN ('A.取消') AND aa.WorkKind IN ('5 維修')
                    --GROUP BY bb.CustID
                ) ELSE '0' END AS COUNT07
            ");

            $this->OrderBaseRepository->addSelectRaw("
                CASE WHEN MS300.WorkKind = '5 維修' THEN (
                   SELECT COUNT(bb.CustID) FROM $this->COSSDB.MS0300 aa WITH(nolock) LEFT JOIN $this->COSSDB.MS0301 bb WITH(nolock) ON aa.CompanyNo=bb.CompanyNo AND aa.WorkSheet = bb.AssignSheet
                   WHERE 1=1 AND aa.BookDate BETWEEN CONVERT(varchar(10) ,DATEADD(MONTH, -1, getDate() ),120 ) AND CONVERT(varchar(10) ,DATEADD(DAY, -1, getDate() ),120 )
                   AND aa.CompanyNo = MS301.CompanyNO AND aa.CustId = MS301.CustId AND bb.SheetStatus NOT IN ('A.取消') AND aa.WorkKind IN ('5 維修')
                   --GROUP BY bb.CustID
                ) ELSE '0' END AS COUNT30
            ");

            $this->OrderBaseRepository->whereInObj([['name'=>'CompanyNo','ary'=>config('order.CompanyNoAry')]],'MS301.');

            foreach ($data as $k => $t) {
                if (!empty($t)) {
                    switch ($k) {
                        case 'userId':
                            $this->OrderBaseRepository->whereWorker1($t.'%','like','MS301.');
                            break;

                        case 'bookdate_s':
                            $this->OrderBaseRepository->whereObj([['name'=>'BookDate','type'=>'>=','value'=>$t]],'MS301.');
                            break;

                        case 'bookdate_e':
                            $this->OrderBaseRepository->whereObj([['name'=>'BookDate','type'=>'<=','value'=>$t]],'MS301.');
                            break;

                        case 'assignsheet':
                            $this->OrderBaseRepository->whereObj([['name'=>'AssignSheet','type'=>'=','value'=>$t]],'MS301.');
                            break;

                        case 'workkind':
                            $this->OrderBaseRepository->whereInObj([['name'=>'WorkKind','ary'=>$t]],'MS300.');
                            break;

                        case 'bookingNo':
                            $this->OrderBaseRepository->whereObj([['name'=>'BookingNo','type'=>'=','vaule'=>$t]],'MS301.');
                            break;

                        case 'notBookingNo':
                            $this->OrderBaseRepository->whereObj([['name'=>'BookingNo','type'=>'<>','value'=>$t]],'MS301.');
                            break;

                        case 'sheetStatusIn':
                            $this->OrderBaseRepository->whereInObj([['name'=>'SheetStatus','ary'=>$t]],'MS301.');
                            break;
                    }
                }
            }

            $orderByAry = array(
                ['name' => 'MS301.Bookdate', 'type' => 'ASC'],
                ['name' => 'MS301.AssignSheet', 'type' => 'ASC'],
            );
            $this->OrderBaseRepository->orderByOrderAry($orderByAry);

            if(config('order.SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sqlStr = str_replace('?', "'".'%s'."'", $this->db->toSql());
                $sqlStr = sprintf($sqlStr, ...$bindings);
                Log::channel('ewoLog')->info('chk getOrderList sql=='.$sqlStr);
            }

            $ret = $this->db->get();

        } catch (Exception $e) {
            $ret = $e->getMessage();
        }

        return $ret;

    }


    public function getUserOrders0527($data,$limit,$page)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0300 AS MS300")->lock('WITH(nolock)'));
            $this->db = $this->OrderBaseRepository->leftJoinMS0301();
            $this->db = $this->OrderBaseRepository->leftJoinWM_OrderList();
            $this->db = $this->OrderBaseRepository->leftJoinMS0040();
            $this->db = $this->OrderBaseRepository->leftJoinMS020_OrderList();

            $this->db = $this->OrderBaseRepository->selectUserOrderInfo0524();

            $this->db = $this->OrderBaseRepository->whereBookDate0524();

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'userId':
                            $this->db = $this->OrderBaseRepository->whereWorker1($value.'%','like','MS301.');
                            break;

                        case 'workKind':
                            $this->db = $this->OrderBaseRepository->whereWorkKind0524($value);
                            break;

                        case 'workSheetIn':
                            $this->db = $this->OrderBaseRepository->whereInWorkSheet($value,'MS301.');
                            break;

                        case 'companyNoIn':
                            $this->db = $this->OrderBaseRepository->whereInObj([['name'=>'ComPanyNo','ary'=>$value]],'MS301.');
                            break;
                    }
                }
            }

            $this->OrderBaseRepository->groupByOrderList();

            $this->OrderBaseRepository->orderByOrderList();

            $total = $this->db->get()->count();

            if(config('order.SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sqlStr = str_replace('?', "'".'%s'."'", $this->db->toSql());
                $sqlStr = sprintf($sqlStr, ...$bindings);
                Log::channel('ewoLog')->info('chk getUserOrders0527 count sql=='.$sqlStr);
            }

            $offset = $limit * ($page-1);
            //$this->db->offset($offset);
            //$this->db->limit($limit);

            $response = $this->db->get();

            if(config('order.SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sqlStr = str_replace('?', "'".'%s'."'", $this->db->toSql());
                $sqlStr = sprintf($sqlStr, ...$bindings);
                Log::channel('ewoLog')->info('chk getUserOrders0527 get sql=='.$sqlStr);
            }

            return array($response,$total);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getStatistics($data)
    {

        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_orderlist')->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectOrderInfo(array('Id','CompanyNo','WorkSheet','BookDate','receiveMoney','receiveType','laborsafetyCheckList','CustId','cmqualityforkg','ServiceName','WorkKind','dataList','WifiTestValue'));

            foreach($data as $k => $t)
            {
                switch ($k)
                {
                    case 'usercode':
                        $this->OrderBaseRepository->whereObj([['name'=>'WorkerNum','type'=>'=','value'=>$t]]);
                        break;
                    case 'timestart':
                        $this->OrderBaseRepository->whereObj([['name'=>'BookDate','type'=>'>','value'=>$t]]);
                        break;
                    case 'timeend':
                        $this->OrderBaseRepository->whereObj([['name'=>'BookDate','type'=>'<','value'=>$t]]);
                        break;
                    case 'worksheet':
                        $this->OrderBaseRepository->whereObj([['name'=>'WorkSheet','type'=>'=','value'=>$t]]);
                        break;
                    case 'companyno':
                        $this->OrderBaseRepository->whereObj([['name'=>'CompanyNo','type'=>'=','value'=>$t]]);
                        break;
                    case 'sign_CM':
                        $this->OrderBaseRepository->whereNotNullObj(array('sign_cm'));
                        break;
                    case 'sign_mengineer':
                        $this->OrderBaseRepository->whereNotNullObj(array('sign_mengineer'));
                        break;
                    case 'sign_mcust':
                        $this->OrderBaseRepository->whereNotNullObj(array('sign_mcust'));
                        break;
                    case 'sign_TWMBB':
                        $this->OrderBaseRepository->whereNotNullObj(array('sign_twmbb'));
                        break;
                    case 'sign_DSTB':
                        $this->OrderBaseRepository->whereNotNullObj(array('sign_dstb'));
                        break;
                    case 'receiveType':
                        $this->OrderBaseRepository->whereNotNullObj(array('receiveType'));
                        break;
                }
            }

            $this->OrderBaseRepository->orderByOrderAry([['name'=>'receiveType','type'=>'asc']]);

            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getLaborsafetyDangerplace($data)
    {
        $this->OrderBaseRepository->initDB(
            DB::connection('WMDB')->table('wm_laborsafety')->lock('WITH(nolock)')
        );

        $this->db = $this->OrderBaseRepository->selectOrderAmt();

        foreach ($data as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'so':
                        $this->OrderBaseRepository->whereSo($value);
                        break;
                    case 'type':
                        $this->OrderBaseRepository->whereObj(
                            [array('name' => 'Type', 'type' => '=', 'value' => $value)]
                        );
                        break;
                    case 'isenable':
                        $this->OrderBaseRepository->whereObj(
                            [array('name' => 'IsEnable', 'type' => '=', 'value' => $value)]
                        );
                        break;

                }
            }
        }

        $ret = $this->db->get();

        return $ret;
    }


    public function getLaborsafetyCheckList($data)
    {
        $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_laborsafety AS laborsafety')->lock('WITH(nolock)'));

        if(array_key_exists('workernum',$data) > 0) {
            $this->OrderBaseRepository->leftJoinWM_laborsafetylog();
            $this->db = $this->OrderBaseRepository->selectFromAry(array('Reply'),'laborsafetylog.');
        }

        $this->db = $this->OrderBaseRepository->selectOrderAmt('laborsafety.');

        foreach ($data as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'companyno':
                        $this->OrderBaseRepository->whereSo($value,'laborsafety.');
                        break;
                    case 'worksheet':
                        $this->OrderBaseRepository->whereObj([array('name' => 'WorkSheet', 'type' => '=', 'value' => $value)],'laborsafetylog.');
                        break;
                    case 'workernum':
                        $this->OrderBaseRepository->whereObj([array('name' => 'UserCode', 'type' => '=', 'value' => $value)],'laborsafetylog.');
                        break;
                    case 'type':
                        $this->OrderBaseRepository->whereObj([array('name' => 'Type', 'type' => '=', 'value' => $value)],'laborsafety.');
                        break;
                    case 'bookdatestart':
                        $this->OrderBaseRepository->whereObj([array('name' => 'BookDate', 'type' => '>=', 'value' => $value)],'laborsafetylog.');
                        break;
                    case 'bookdateend':
                        $this->OrderBaseRepository->whereObj([array('name' => 'BookDate', 'type' => '<=', 'value' => $value)],'laborsafetylog.');
                        break;
                    case 'bookdate':
                        $this->OrderBaseRepository->whereObj([array('name' => 'BookDate', 'type' => '=', 'value' => $value)],'laborsafetylog.');
                        break;
                    case 'isenable':
                        $this->OrderBaseRepository->whereObj([array('name' => 'IsEnable', 'type' => '=', 'value' => $value)],'laborsafety.');
                        break;
                }
            }
        }

        $ret = $this->db->get();

        return $ret;
    }


    public function getLaborsafetylog($data)
    {
        $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_laborsafetylog AS laborsafety')->lock('WITH(nolock)'));

        $this->db = $this->OrderBaseRepository->selectOrderAmt();

        foreach ($data as $key => $value) {
            if (!empty($value)) {
                switch ($key) {
                    case 'companyno':
                        $this->OrderBaseRepository->whereSo($value,'laborsafety.');
                        break;
                    case 'worksheet':
                        $this->OrderBaseRepository->whereObj([array('name' => 'WorkSheet', 'type' => '=', 'value' => $value)],'laborsafety.');
                        break;
                    case 'userCode':
                        $this->OrderBaseRepository->whereObj([array('name' => 'UserCode', 'type' => '=', 'value' => $value)],'laborsafety.');
                        break;
                    case 'userName':
                        $this->OrderBaseRepository->whereObj([array('name' => 'UserName', 'type' => '=', 'value' => $value)],'laborsafety.');
                        break;
                    case 'desc1':
                        $this->OrderBaseRepository->whereObj([array('name' => 'Desc1', 'type' => 'like', 'value' => $value)],'laborsafety.');
                        break;
                    case 'type':
                        $this->OrderBaseRepository->whereObj([array('name' => 'Type', 'type' => '=', 'value' => $value)],'laborsafety.');
                        break;
                }
            }
        }

        $ret = $this->db->get()->first();

        return $ret;
    }


    public function getOrderInfo($data,$getAll='',$selectAry=array())
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_orderlist')->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectOrderInfo($selectAry);

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'id':
                            $this->OrderBaseRepository->whereId($value);
                            break;
                        case 'so':
                            $this->OrderBaseRepository->whereSo($value);
                            break;
                        case 'worksheet':
                            $this->OrderBaseRepository->whereWorkSheet($value);
                            break;
                        case 'worknum':
                            $this->OrderBaseRepository->whereObj([array('name'=>'WorkerNum','type'=>'=','value'=>$value)]);
                            break;
                        case 'signauthorization':
                            $this->OrderBaseRepository->whereObj([array('name'=>'SignAuthorization','type'=>'>=','value'=>date('Y-m-d 00:00:00'))]);
                            $this->OrderBaseRepository->whereObj([array('name'=>'SignAuthorization','type'=>'<=','value'=>date('Y-m-d 23:59:59'))]);
                            break;

                    }
                }
            }

            if($getAll === 'all') {
                $ret = $this->db->get();
            } else {
                $ret = $this->db->first();
            }

            return $ret;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getOrderInfoById($id)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_orderlist')->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectOrderInfo();

            $this->db = $this->OrderBaseRepository->whereId($id);

            $response = $this->db->first();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getOrderCharge($data,$totalField=false,$orderBy=array())
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0301")->lock('WITH(nolock)'));


            if(is_array($totalField))
                $this->db = $this->OrderBaseRepository->selectFromAry($totalField);
            else
                $this->db = $this->OrderBaseRepository->selectChargeInfo($totalField);

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'worksheetin':
                            $p_data = array(
                                ['name'=>'WorkSheet','ary'=>$value]
                            );
                            $this->db = $this->OrderBaseRepository->whereInObj($p_data);
                            break;
                        case 'worker1':
                            $this->db = $this->OrderBaseRepository->whereWorker1($value,'=');
                            break;
                        case 'worker1like':
                            $this->db = $this->OrderBaseRepository->whereWorker1($value,'like');
                            break;
                        case 'so':
                            $this->db = $this->OrderBaseRepository->whereSo($value);
                            break;
                        case 'worksheet':
                            $this->db = $this->OrderBaseRepository->whereAssignSheet($value);
                            break;
                        case 'worksheet2':
                            $this->db = $this->OrderBaseRepository->whereWorkSheet($value);
                            break;
                        case 'subsid':
                            $this->db = $this->OrderBaseRepository->whereSubsId($value);
                            break;
                        case 'custid':
                            $this->db = $this->OrderBaseRepository->whereCustId($value);
                            break;
                        case 'chargename':
                            $this->db = $this->OrderBaseRepository->whereChargeName($value);
                            break;
                        case 'chargenamein':
                            $this->db = $this->OrderBaseRepository->whereInChargeName($value);
                            break;
                        case 'chargekind':
                            $this->db = $this->OrderBaseRepository->whereChargeKind($value);
                            break;
                        case 'servicename':
                            $p_data = array(['name'=>'ServiceName','ary'=>$value]);
                            $this->db = $this->OrderBaseRepository->whereInObj($p_data);
                            break;
                        case 'statusNotIn':
                            $this->OrderBaseRepository->whereNotInObj([['name'=>'SheetStatus','ary'=>$value]]);
                            break;
                    }
                }
            }


            if(config('order.SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sqlStr = str_replace('?', "'".'%s'."'", $this->db->toSql());
                $sqlStr = sprintf($sqlStr, ...$bindings);
                Log::channel('ewoLog')->info('chk getOrderCharge sql=='.$sqlStr);
            }

            if(sizeof($orderBy) > 0) {
                $this->OrderBaseRepository->orderByOrderAry($orderBy);
            }

            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getMs0301BillamtSum($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0301 AS MS301")->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectOrderInfoRaw('sum(MS301.BillAmt) as BillAmtSum');

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'so':
                            $this->db = $this->OrderBaseRepository->whereSo($value,'MS301.');
                            break;
                        case 'worksheet':
                            // $this->db = $this->OrderBaseRepository->whereWorkSheet($value);
                            $this->db = $this->OrderBaseRepository->whereWorkSheet($value);
                            break;
                        case 'inworksheet':
                            $this->db = $this->OrderBaseRepository->whereInWorkSheet($value);
                            break;
                    }
                }
            }

            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getMS0400($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0040 AS MS40")->lock('WITH(nolock)'));

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                    case 'so':
                        $this->db = $this->OrderBaseRepository->whereSo($value);
                        break;
                    case 'chargename':
                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>'ChargeName','type'=>'=','value'=>$value]]);
                        break;
                    case 'servicename':
                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>'ServiceName','type'=>'=','value'=>$value]]);
                        break;
                    }
                }
            }

            $selectAry = array('CMValue','DnStream');

            $this->db = $this->OrderBaseRepository->selectOrderInfo($selectAry);

            $ret = $this->db->get();

        } catch (Exception $e) {
            $ret = $e->getMessage();

        }
        return $ret;
    }




    public function getMS0392($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0392")->lock('WITH(nolock)'));

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                    case 'so':
                        $this->db = $this->OrderBaseRepository->whereSo($value);
                        break;
                    case 'custid':
                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>'CustID','type'=>'=','value'=>$value]]);
                        break;
                    case 'announcer':
                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>'Announcer','type'=>'=','value'=>$value]]);
                        break;
                    case 'mssubject':
                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>'MSSubject','type'=>'=','value'=>$value]]);
                        break;
                    case 'announcet01Start':
                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>'AnnounceT01','type'=>'>=','value'=>$value]]);
                        break;
                    case 'announcet01End':
                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>'AnnounceT01','type'=>'<=','value'=>$value]]);
                        break;
//                    case 'servicename':
//                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>'ServiceName','type'=>'=','value'=>$value]]);
//                        break;
                    }
                }
            }

            $selectAry = array();

            $this->db = $this->OrderBaseRepository->selectOrderInfo();

            $count = $this->db->get()->count();

            $ret = array(
                'count' => $count
            );

        } catch (Exception $e) {
            $ret = $e->getMessage();

        }
        return $ret;
    }





    public function getMS3200RecvAmttSum($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS3200 AS MS320")->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectOrderInfoRaw('sum(MS320.RecvAmt) as RecvAmtSum');

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'so':
                            $this->db = $this->OrderBaseRepository->whereSo($value);
                            break;
                        case 'worksheet':
                            $this->db = $this->OrderBaseRepository->whereWorkSheet($value);
                            break;
                        case 'inworksheet':
                            $this->db = $this->OrderBaseRepository->whereInWorkSheet($value);
                            break;
                        case 'recvyn':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'RecvYN','type'=>'=','value'=>$value]]);
                            break;
                        case 'workrecvyn':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'WorkRecvYN','type'=>'=','value'=>$value]]);
                            break;
                    }
                }
            }

            $this->db = $this->OrderBaseRepository->whereRecvYN('MS320.');;
            $this->db = $this->OrderBaseRepository->wherePassYN('MS320.');;

            $response = $this->db->get();

            if(env('SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sql = str_replace('?', '%s', $this->db->toSql());
                $sql = sprintf($sql, ...$bindings);
                Log::channel('ewoLog')->info('chk sql getMS3200RecvAmttSum=='.$sql);
            }

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

//    public function getworkerPlaceInfo($data)
//    {
//        try {
//            $this->OrderBaseRepository->initDB(DB::connection('COSSERPDB')->table('MI0120 as mi120')->lock('WITH(nolock)'));
//            $this->db = $this->OrderBaseRepository->leftJoinMI0100();
//
//            $this->db = $this->OrderBaseRepository->selectPlaceInfo();
//
//            foreach ($data as $key => $value) {
//                if (!empty($value)) {
//                    switch ($key) {
//                        case 'so':
//                            $this->db = $this->OrderBaseRepository->whereMI0120So($value);
//                            break;
//                        case 'placeNo':
//                            $this->db = $this->OrderBaseRepository->whereMI0120PlaceNo($value);
//                            break;
//                    }
//                }
//            }
//
//            $response = $this->db->get();
//
//            return $response;
//
//        } catch (Exception $e) {
//            throw new Exception($e->getMessage());
//        }
//    }

    public function getOrderHardcons($id)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_hardcons as hardcons')->lock('WITH(nolock)'));
            $this->db = $this->OrderBaseRepository->leftJoinHardcons_Prodlist();

            $this->db = $this->OrderBaseRepository->selectOrderInfo();

            $this->db = $this->OrderBaseRepository->whereHardconsId($id);

            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getHardconsList($companyNo)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_hardcons_prodlist')->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectOrderInfo();
            $this->db = $this->OrderBaseRepository->whereSo($companyNo);

            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getWorksheetList($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0300 AS MS300")->lock('WITH(nolock)'));
            $this->OrderBaseRepository->leftJoinMS0301();

            if(isset($data['select'])) {
                foreach($data['select'] as $k => $t) {
                    $this->OrderBaseRepository->selectOrderInfo([$t['column']],$t['asName']);
                }
            } else {
                $this->OrderBaseRepository->selectOrderInfo(['MS300.WorkSheet','MS300.SubsCP2','MS300.PrintBillYN','MS301.SubsId','MS301.ServiceName']);
            }

            foreach ($data as $k => $val) {
                if (!empty($val)) {
                    switch ($k) {
                        case 'companyNo':
                            $this->OrderBaseRepository->whereSo($val,'MS300.');
                            break;
                        case 'workKind':
                            $this->OrderBaseRepository->whereObj([['name'=>'WorkKind','type'=>'=','value'=>$val]],'MS300.');
                            break;
                        case 'workKindIn':
                            $this->OrderBaseRepository->whereInObj([['name'=>'WorkKind','ary'=>$val]],'MS300.');
                            break;
                        case 'subsId':
                            $this->OrderBaseRepository->whereObj([['name'=>'SubsID','type'=>'=','value'=>$val]],'MS301.');
                            break;
                        case 'workSheet':
                            $this->OrderBaseRepository->whereAssignSheet($val,'MS301.');
                            break;
                        case 'assignSheet':
                            $this->OrderBaseRepository->whereObj([['name'=>'AssignSheet','type'=>'=','value'=>$val]],'MS301.');
                            break;
                        case 'statusNotIn':
                            $this->OrderBaseRepository->whereNotInObj([['name'=>'SheetStatus','ary'=>$val]],'MS301.');
                            break;
                        case 'bookDateS':
                            $this->OrderBaseRepository->whereObj([['name'=>'BookDate','type'=>'>=','value'=>$val]],'MS301.');
                            break;
                        case 'bookDateE':
                            $this->OrderBaseRepository->whereObj([['name'=>'BookDate','type'=>'<=','value'=>$val]],'MS301.');
                            break;
                    }
                }
            }

            if(isset($data['groupBy'])) {
                $this->OrderBaseRepository->groupByOrderAry($data['groupBy']);
            }

            if(isset($data['orderBy'])) {
                foreach($data['orderBy'] as $k => $t) {
                    $this->OrderBaseRepository->orderByOrderAry([['name'=>$t['name'],'type'=>$t['type']]],$t['asName']);
                }
            }

            if(isset($data['first'])){
                $ret = $this->OrderBaseRepository->objFirst();
            } else {
                $ret = $this->OrderBaseRepository->objGET();
            }

            return $ret;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getMS0200($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0200 as ms200")->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectObj(['PackageName','PayName','ServiceName','BillItem','Aveamt','SWVersion','CustStatus','CustID','SubsID','SingleSN']);

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                    case 'companyno':
                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>'CompanyNo', 'type'=>'=', 'value'=>$value]]);
                        break;
                    case 'custid':
                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>'CustID', 'type'=>'=', 'value'=>$value]]);
                        break;
                    case 'subsid':
                        $this->db = $this->OrderBaseRepository->whereInObj([['name'=>'SubsID', 'ary'=>[$value]]]);
                        break;
                    case 'custstatusNotIn':
                        $whereAry = array(
                            [
                                'name' => 'CustStatus',
                                'ary' => $value,
                            ]
                        );
                        $this->db = $this->OrderBaseRepository->whereNotInObj($whereAry);
                        break;
                    case 'paynameNotIn':
                        $whereAry = array(
                            [
                                'name' => 'PayName',
                                'ary' => $value,
                            ]
                        );
                        $this->db = $this->OrderBaseRepository->whereNotInObj($whereAry);
                        break;
                    case 'servicenameIn':
                        $whereAry = array(
                            [
                                'name' => 'ServiceName',
                                'ary' => $value,
                            ]
                        );
                        $this->db = $this->OrderBaseRepository->whereInObj($whereAry);
                        break;
                    case 'packageNameNotIn':
                        $value = $value['ary'][0] == 'isEmpty'? [''] : $value;
                        $whereAry = array(
                            'name' => 'PackageName',
                            'list' => $value,
                        );
                        $this->db = $this->OrderBaseRepository->notInObj($whereAry);
                        break;
                    }
                }
            }
            if(isset($data['order'])) {
                foreach ($data['order'] as $k => $t) {
                    $this->db->orderBy($t['column'],$t['type']);
                }
            }

            $ret = $this->db->get();

            return $ret;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getMS0042($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0042 as ms42")->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectObj();

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'companyNo':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'CompanyNo', 'type'=>'=', 'value'=>$value]]);
                            break;
                        case 'packageCode':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'PackageCode', 'type'=>'=', 'value'=>$value]]);
                            break;
                    }
                }
            }

            $ret = $this->db->get();

            return $ret;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getMS0043($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0043 as ms43")->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectObj();

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'companyNo':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'CompanyNo', 'type'=>'=', 'value'=>$value]]);
                            break;
                        case 'packageCode':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'PackageCode', 'type'=>'=', 'value'=>$value]]);
                            break;
                        case 'serviceName':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'ServiceName', 'type'=>'=', 'value'=>$value]]);
                            break;
                        case 'chargeName':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'ChargeName', 'type'=>'=', 'value'=>$value]]);
                            break;
                    }
                }
            }

            $ret = $this->db->get();

            return $ret;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function get_wm_equipment($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_equipment')->lock('WITH(nolock)'));
            $this->db = $this->OrderBaseRepository->selectObj();

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'enable':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'enable', 'type'=>'=', 'value'=>$value]]);
                            break;
                        case 'selectType':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'selectType', 'type'=>'=', 'value'=>$value]]);
                            break;
                        case 'mtnoLike':
                            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'mtnoList', 'type'=>'like', 'value'=>'%'.$value.'%']]);
                            break;
                    }
                }
            }

            $orderByAry = array(
                ['name' => 'type', 'type' => 'ASC'],
                ['name' => 'selectType', 'type' => 'ASC'],
                ['name' => 'enable', 'type' => 'ASC'],
                ['name' => 'sort', 'type' => 'ASC'],
            );
            $this->OrderBaseRepository->orderByOrderAry($orderByAry);

            $ret =  $this->db->get()->toArray();

            return $ret;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getOrderSingleSN($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0201 as ms201")->lock('WITH(nolock)'));
            $this->db = $this->OrderBaseRepository->leftJoinMS0200();

            // $this->db = $this->OrderBaseRepository->selectOrderInfo();
            $this->db = $this->OrderBaseRepository->addSelectSingleSNRaw();

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'so':
                            $this->db = $this->OrderBaseRepository->whereMS0201So($value);
                            break;
                        case 'subsid':
                            $this->db = $this->OrderBaseRepository->whereMS0201Subsid($value);
                            break;
                    }
                }
            }


            $response = $this->db->first();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getMediabillno($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS3200'")->lock('WITH(nolock)'));


            $this->db = $this->OrderBaseRepository->selectOrderInfo();



            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'so':
                            $this->db = $this->OrderBaseRepository->whereSo($value);
                            break;
                        case 'subsId':
                            $this->db = $this->OrderBaseRepository->whereSubsId($value);
                            break;
                        case 'custId':
                            $this->db = $this->OrderBaseRepository->whereCustId($value);
                            break;
                        case 'chargeName':
                            $this->db = $this->OrderBaseRepository->whereChargeName($value);
                            break;
                    }
                }
            }

            // $this->db = $this->OrderBaseRepository->whereMS3200ServiceName($data['serviceName']);

            $this->db = $this->OrderBaseRepository->whereRecvYN();
            $this->db = $this->OrderBaseRepository->wherePassYN();

            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getOrderBill($data,$creditCard=false)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS3200")->lock('WITH(nolock)'));


            $this->db = $this->OrderBaseRepository->selectOrderInfo();
            // $this->db = $this->OrderBaseRepository->addSelectRecvExpire();

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'worksheetin':
                            $p_data = array(
                                ['name'=>'WorkSheet','ary'=>$value]
                            );
                            $this->db = $this->OrderBaseRepository->whereInObj($p_data);
                            break;
                        case 'so':
                            $this->db = $this->OrderBaseRepository->whereSo($value);
                            break;
                        case 'recvyn':
                            $this->db = $this->OrderBaseRepository->whereRecvYN('');
                            break;
                        case 'worksheet':
                            $this->db = $this->OrderBaseRepository->whereWorkSheet($value);
                            break;
                        case 'recvNo':
                            $this->db = $this->OrderBaseRepository->whereRecvNo($value);
                            break;
                        case 'inworksheet':
                            $this->db = $this->OrderBaseRepository->whereInWorkSheet($value);
                            break;
                    }
                }
            }

            // $this->db = $this->OrderBaseRepository->whereMS3200ServiceName($data['serviceName']);

            if ($creditCard) {
                $this->db = $this->OrderBaseRepository->whereRecvYN();
                $this->db = $this->OrderBaseRepository->wherePassYN();
            }

            if(env('SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sql = str_replace('?', '%s', $this->db->toSql());
                $sql = sprintf($sql, ...$bindings);
                Log::channel('ewoLog')->info('chk sql getOrderBill=='.$sql);
            }

            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getMS0300($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0300")->lock('WITH(nolock)'));
            $this->db = $this->OrderBaseRepository->selectOrderInfo();
            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'worksheetin':
                            $p_data = array(
                                ['name'=>'WorkSheet','ary'=>$value]
                            );
                            $this->db = $this->OrderBaseRepository->whereInObj($p_data);
                            break;
                        case 'so':
                            $this->db = $this->OrderBaseRepository->whereSo($value);
                            break;

                        case 'worksheet':
                            $this->db = $this->OrderBaseRepository->whereWorkSheet($value);
                            break;
                        case 'inworksheet':
                            $this->db = $this->OrderBaseRepository->whereInWorkSheet($value);
                            break;
                    }
                }
            }

            if(env('SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sql = str_replace('?', '%s', $this->db->toSql());
                $sql = sprintf($sql, ...$bindings);
                Log::channel('ewoLog')->info('chk sql getOrderBill=='.$sql);
            }

            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getOrderInforNEW($data)
    {
        try {


            $sql = <<<EOF
            SELECT
                aa.CompanyNo
                ,aa.WorkSheet
                ,bb.BillPrd
                ,bb.ServiceName
                ,aa.PrintBillYN
                ,bb.ChargeName
                ,bb.BillAmt
                ,bb.ChargeKind
                ,bb.SheetStatus
                ,bb.AcceptDate
                ,bb.BrokerKind
                ,bb.MSRemark
                ,aa.WorkKind
                ,SUBSTRING(bb.WorkTeam,CHARINDEX(' ',bb.WorkTeam) + 1,LEN(bb.WorkTeam)) AS WorkTeam
                ,bb.Worker1
                ,aa.CustBroker
                ,aa.SaleCampaign
                ,cc.NetID
                ,bb.FinishTime
                ,aa.SubsCP2
                ,bb.CustID
                ,bb.SubsID
                ,aa.BookDate
                ,aa.CreateTime
                ,cc.MailTitle
                ,aa.CustName
                ,aa.MSComment1
                ,SUBSTRING(aa.WorkCause,CHARINDEX(' ',aa.WorkCause) + 1,LEN(aa.WorkCause)) AS WorkCause
                ,aa.InstAddrName
                ,aa.TeleNum01 as TeleNum
                ,aa.CellPhone01
                ,dd.TeleCod01
                ,dd.TeleNum01
                ,dd.TeleCod02
                ,dd.TeleNum02
                ,dd.PayName
                ,bb.SaleKind
                ,ee.MSContract
                ,ee.MSContract2
                ,aa.CreateName
                ,ff.LinkID
                ,ff.ADDRSORT
                ,(ff.Latitude+','+ff.Longitude) as custGps
                ,(ff.MSCITY+ff.MSDISTRICT+ff.ADDRNAME) as custAddress
                ,bb.SmartCard
                ,bb.CleanCause
                ,bb.OrgSingleSN
                ,bb.SingleSN
                ,bb.SWVersion
                ,cc.InvUnifyNo
                ,ff.NodeNo
                ,cc.InvUnifyNo

                --*

            FROM $this->COSSDB.MS0300 AS aa WITH (nolock)
            LEFT JOIN $this->COSSDB.MS0301 AS bb WITH(nolock) ON aa.CompanyNo = bb.CompanyNo AND aa.WorkSheet = bb.WorkSheet
            INNER JOIN $this->COSSDB.MS0100 AS cc WITH(nolock) ON bb.CompanyNo = cc.CompanyNo AND bb.CustID = cc.CustID
            INNER JOIN $this->COSSDB.MS0200 AS dd WITH(nolock) ON aa.CompanyNo = dd.CompanyNo AND bb.SubsID = dd.SubsID
            LEFT JOIN $this->COSSDB.MS0042 AS ee WITH(nolock) ON bb.CompanyNo = ee.CompanyNo AND bb.PackageName = ee.DataName
            INNER JOIN $this->COSSDB.MS0102 AS ff WITH(nolock) ON bb.CompanyNo = ff.CompanyNo AND bb.CustID = ff.CustID
            WHERE 1=1
                AND bb.CompanyNo = '{$data['so']}'
                AND bb.AssignSheet = '{$data['worksheet']}'
                AND bb.SheetStatus NOT IN ('A.取消')
                AND ff.AddrNo = '0'
            ;

EOF;

            $response = DB::connection('WMDB')->select($sql);

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    // 同址設備
    public function getDeviceListOnAddr($params) {
        $COSSDB = $this->COSSDB;
        try {
            $p_ADDRSORT = $params['ADDRSORT'];
            $p_companyNo = $params['companyNo'];
            $p_custId = $params['custId'];

            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$COSSDB.ms0102")->lock('WITH(nolock)'));

            $joinAry = array(
                'table' => "$COSSDB.MS0200",
                'asname' => 'b',
                'onary' => [
                    ['ms0102.CompanyNo','b.CompanyNo'],
                    ['ms0102.CustID','b.CustID'],
                ],
            );
            $this->db = $this->OrderBaseRepository->joinObj($joinAry);

            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'CustID','type'=>'!=','value'=>$p_custId]],'ms0102.');
            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'CompanyNo','type'=>'=','value'=>$p_companyNo]],'ms0102.');
            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'ADDRSORT','type'=>'=','value'=>$p_ADDRSORT]],'ms0102.');
            $selAry = array(
                'SubsID','CustID',
                'ServiceName',
                'CustStatus',
                'SWVersion',
                'SingleSN',
            );
            $this->db = $this->OrderBaseRepository->selectOrderInfo($selAry,'b.');

            $orderByAry = array(
                ['name'=>'CustID','type'=>'asc'],
                ['name'=>'ServiceName','type'=>'asc'],
                ['name'=>'CustStatus','type'=>'asc'],
            );
            $this->db = $this->OrderBaseRepository->orderByOrderAry($orderByAry,'b.');
            $ret = $this->db->get();
//            $ret = $this->db->toSql();

            return $ret;

        } catch (Exception $e) {
            return $e->getCode().','.$e->getMessage();
        }
    }



    public function getOrderPdfInfo($id)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_pdf')->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectOrderInfo();

            $this->db = $this->OrderBaseRepository->wherePdfOrderListId($id);

            $response = $this->db->first();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function insertOrderPdfInfo($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_pdf')->lock('WITH(nolock)'));

            $pdfId = $this->OrderBaseRepository->insertPDF($data);

            return $pdfId;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function insertQALog($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_qa_log')->lock('WITH(nolock)'));
            $pdfId = $this->OrderBaseRepository->insertWMOrderlist($data);
            return $pdfId;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function deletePdfInfo($id)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_pdf')->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->wherePdfOrderListId($id);

            $this->db->delete();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateOrderListData($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_orderlist')->lock('WITH(nolock)'));

            if (empty($data['so']) || empty($data['worksheet'])) {
                return;
            }

            $this->OrderBaseRepository->whereSo($data['so']);

            $this->OrderBaseRepository->whereWorkSheet($data['worksheet']);

            $this->db = $this->OrderBaseRepository->updateOrderList($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateMs0300TotalAmt($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_COSSDB')->table("$this->COSSDB.MS0300")->lock('WITH(nolock)'));

            $this->OrderBaseRepository->whereSo($data['CompanyNo']);

            $this->OrderBaseRepository->whereWorkSheet($data['WorkSheet']);

            $this->OrderBaseRepository->whereCustId($data['CustID']);

            $this->db = $this->OrderBaseRepository->updateMS0300TotalAmt($data);
//            $this->db = $this->OrderBaseRepository->selectOrderInfo();

//            $bindings = $this->db->getBindings();
//            $sql = str_replace('?', '%s', $this->db->toSql());
//            $sql = sprintf($sql, ...$bindings);
//            Log::channel('ewoLog')->info('chk sql updateMs0300TotalAmt=='.$sql);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    // 更新設備序號
    public function updateMS0301SingleSN($data)
    {

        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_COSSDB')->table("$this->COSSDB.MS0301")->lock('WITH(nolock)'));

            $this->OrderBaseRepository->whereSo($data['CompanyNo']);

            $this->OrderBaseRepository->whereWorkSheet($data['WorkSheet']);

            $this->OrderBaseRepository->whereCustId($data['CustID']);

            $this->OrderBaseRepository->whereChargeName($data['ChargeName']);

            $this->db = $this->OrderBaseRepository->updateMS0301SingleSN($data);

            if(env('SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sql = str_replace('?', '%s', $this->db->toSql());
                $sql = sprintf($sql, ...$bindings);
                Log::channel('ewoLog')->info('chk sql updateMS0301SingleSN=='.$sql);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function updateMs0301BillAmt($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_COSSDB')->table("$this->COSSDB.MS0301")->lock('WITH(nolock)'));

            $this->OrderBaseRepository->whereSo($data['CompanyNo']);

            $this->OrderBaseRepository->whereWorkSheet($data['WorkSheet']);

            $this->OrderBaseRepository->whereCustId($data['CustID']);

            $this->OrderBaseRepository->whereChargeName($data['ChargeName']);

            $this->db = $this->OrderBaseRepository->updateMS0301BillAmt($data);

            if(env('SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sql = str_replace('?', '%s', $this->db->toSql());
                $sql = sprintf($sql, ...$bindings);
                Log::channel('ewoLog')->info('chk sql updateMs0300TotalAmt=='.$sql);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function dbBeginTransaction()
    {
        $this->OrderBaseRepository->initDB(DB::connection('WMDB')->beginTransaction());
    }

    public function dbRolback()
    {
        $this->OrderBaseRepository->initDB(DB::connection('WMDB')->rollback());
    }


    public function dbCommit()
    {
        $this->OrderBaseRepository->initDB(DB::connection('WMDB')->commit());
    }

    public function insertMS03Z0($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_COSSDB')->table("$this->COSSDB.MS03Z0")->lock('WITH(nolock)'));

            $pdfId = $this->OrderBaseRepository->insertMS03Z0($data);

            return $pdfId;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function insertMS03Z1($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_COSSDB')->table("$this->COSSDB.MS03Z1")->lock('WITH(nolock)'));

            $pdfId = $this->OrderBaseRepository->insertMS03Z1($data);

            return $pdfId;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function insertMS0301($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_COSSDB')->table("$this->COSSDB.MS0301")->lock('WITH(nolock)'));

            $pdfId = $this->OrderBaseRepository->insertMS03Z1($data);

            return $pdfId;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateOrderListPaidList($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_orderlist')->lock('WITH(nolock)'));


            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'so':
                            $this->db = $this->OrderBaseRepository->whereSo($value);
                            break;
                        case 'worksheet':
                            $this->db = $this->OrderBaseRepository->whereWorkSheet($value);
                            break;
                    }
                }
            }

            if (empty($data['so']) && empty($data['worksheet'])) {
                return;
            }

            $this->db = $this->OrderBaseRepository->updateOrderListPaidList($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateOrderInfoList($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_orderlist')->lock('WITH(nolock)'));


            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'so':
                            $this->db = $this->OrderBaseRepository->whereSo($value);
                            break;
                        case 'worksheet':
                            $this->db = $this->OrderBaseRepository->whereWorkSheet($value);
                            break;
                    }
                }
            }

            if (empty($data['so']) && empty($data['worksheet'])) {
                return;
            }

            $this->db = $this->OrderBaseRepository->updateOrderInfoList($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateFinshMS3200($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_orderlist')->lock('WITH(nolock)'));

            if (empty($data['so']) || empty($data['worksheet'])) {
                return;
            }

            $this->db = $this->OrderBaseRepository->whereSo(data_get($data,'so'));

            $this->db = $this->OrderBaseRepository->whereWorkSheet(data_get($data,'worksheet'));

            $this->db = $this->OrderBaseRepository->updateFinshMS3200($data);

            if(config('order.SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sqlStr = str_replace('?', "'".'%s'."'", $this->db->toSql());
                $sqlStr = sprintf($sqlStr, ...$bindings);
                Log::channel('ewoLog')->info('chk updateFinshMS3200 sql=='.$sqlStr);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());

        }
    }


    public function updateFinshMS0300($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_orderlist')->lock('WITH(nolock)'));

            if (empty($data['so']) || empty($data['worksheet'])) {
                return;
            }

            $this->db = $this->OrderBaseRepository->whereSo(data_get($data,'so'));

            $this->db = $this->OrderBaseRepository->whereWorkSheet(data_get($data,'worksheet'));

            $this->db = $this->OrderBaseRepository->updateFinshMS0300($data);

            if(config('order.SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sqlStr = str_replace('?', "'".'%s'."'", $this->db->toSql());
                $sqlStr = sprintf($sqlStr, ...$bindings);
                Log::channel('ewoLog')->info('chk updateFinshMS3200 sql=='.$sqlStr);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());

        }
    }

    public function updateDataList($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_orderlist')->lock('WITH(nolock)'));

            if (empty($data['so']) || empty($data['worksheet'])) {
                return;
            }

            $this->db = $this->OrderBaseRepository->whereSo(data_get($data,'so'));

            $this->db = $this->OrderBaseRepository->whereWorkSheet(data_get($data,'worksheet'));

            $this->db = $this->OrderBaseRepository->updateDataList($data);

            if(config('order.SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sqlStr = str_replace('?', "'".'%s'."'", $this->db->toSql());
                $sqlStr = sprintf($sqlStr, ...$bindings);
                Log::channel('ewoLog')->info('chk updateFinshMS3200 sql=='.$sqlStr);
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());

        }
    }

    public function addOrderlist($data){
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_orderlist')->lock('WITH(nolock)'));

            $params = array();
            $params['WorkerNum'] = data_get($data,'WorkerNum');
            $params['WorkerName'] = data_get($data,'WorkerName');
            $params['CompanyNo'] = data_get($data,'so');
            $params['WorkSheet'] = data_get($data,'worksheet');
            $params['CustID'] = data_get($data,'custid');
            $params['BookDate'] = data_get($data,'bookdate');
            $params['saleAP'] = data_get($data,'saleAP');
            $params['pdf_v'] = data_get($data,'pdf_v');
            $params['pdfTerms'] = data_get($data,'pdfTerms');
            $newId = $this->OrderBaseRepository->insertWMOrderlist($params);
            return $newId;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //設備數量
    public function getMaintainDeviceCount($data)
    {
        try {
            $companyNo = data_get($data,'companyNo');
            $custId = data_get($data,'custId');

            $sql = <<<EOF

            SELECT
            --    AA.CompanyNo AS COMPCODE
            --    , AA.CustID AS CUSTID
            --    , AA.SubsID AS SUBSID
            --    , AA.ServiceName AS SERVICETYPE
            --    , AA.CustStatus
            --    , BB.SingleSN AS FACISNO
            --    , CASE WHEN NOT(ISNULL(CC.CMValue,'') = '') THEN LEFT(CC.CMValue,CHARINDEX(',',CC.CMValue) - 1) END AS CMBAUDRATE
            --    , SUBSTRING(BB.SWVersion,CHARINDEX(' ',BB.SWVersion) + 1,LEN(BB.SWVersion)) AS MODELNAME
            --    , DD.Bothway
            --    , BB.ChargeName
            --    , EE.ChargeKind
            --    , CASE WHEN AA.CustStatus = '3 已拆' AND BB.StopYN = 'N' THEN 1 END AS NOTGET
            --    , ROW_NUMBER() OVER(PARTITION BY AA.CustID, AA.ServiceName ORDER BY BB.InstDate) AS FACI_CNT
            AA.CompanyNo
            ,AA.CustID
            , CASE WHEN NOT(ISNULL(CC.CMValue,'') = '') THEN LEFT(CC.CMValue,CHARINDEX(',',CC.CMValue) - 1) END AS CMBAUDRATE
            , COUNT(DISTINCT CASE WHEN AA.ServiceName = '2 CM' THEN BB.SingleSN END) AS I_CNT
                  , COUNT(DISTINCT CASE WHEN AA.ServiceName = '3 DSTB' AND EE.ChargeKind = '50' AND DD.Bothway = '1' THEN BB.SingleSN END) AS D_DUBLECNT
                  , COUNT(DISTINCT CASE WHEN AA.ServiceName = '3 DSTB' AND EE.ChargeKind = '50' AND NOT(ISNULL(DD.Bothway,'') = '1') THEN BB.SingleSN END) AS D_SINGLECNT
                  , COUNT(DISTINCT CASE WHEN AA.ServiceName = '3 DSTB' AND BB.ChargeName LIKE '%外接硬碟%' THEN BB.SingleSN END) AS PVR_CNT
            --    , CEILING(ROW_NUMBER() OVER(PARTITION BY AA.CustID, AA.ServiceName ORDER BY BB.InstDate) / @MaxFaciNum) AS FACI_THISPAGE
            --    INTO #FACIDATA
              FROM
                $this->COSSDB.MS0200 AA WITH(NOLOCK)
                INNER JOIN $this->COSSDB.MS0211 BB WITH(NOLOCK)
                  ON BB.CompanyNo = AA.CompanyNo
                  AND BB.SubsID = AA.SubsID
                LEFT JOIN $this->COSSDB.MS0040 CC WITH(NOLOCK)
                  ON CC.CompanyNo = AA.CompanyNo
                  AND CC.ChargeName = AA.BillItem
                  AND CC.ServiceName = AA.ServiceName
                LEFT JOIN COSSERP.dbo.MICD0101 DD WITH(NOLOCK)
                  ON DD.CSModel = BB.SWVersion
                LEFT JOIN $this->COSSDB.MS0040 EE WITH(NOLOCK)
                  ON EE.CompanyNo = BB.CompanyNo
                  AND EE.ChargeName = BB.ChargeName
                  AND EE.ServiceName = BB.ServiceName
              WHERE
                1 = 1
                AND BB.StopYN = 'N'
                AND NOT(BB.SingleSN = '')
                AND (
                  BB.SingleSN = AA.SingleSN
                  OR BB.ChargeName LIKE '%DTV雙向模組%'
                  OR BB.ChargeName LIKE '%外接硬碟%'
                )
                AND AA.CompanyNo = '{$companyNo}'
                AND AA.CustID = '{$custId}'
            GROUP BY
                  AA.CompanyNo
                  , AA.CustID
                  , CC.CMValue


EOF;

            $response = DB::connection('WMDB')->select($sql);


            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    //歷史紀錄
    public function getMaintainHistory($data)
    {
        try {
            $companyNo = data_get($data,'companyNo');
            $custId = data_get($data,'custId');

            $sql = <<<EOF

            SELECT
                  A.CompanyNo AS COMPCODE
                  , A.CustID AS CUSTID
                  , CONVERT(VARCHAR,A.FinishDate,111) AS FINTIME
                  , SUBSTRING(B.CleanName,CHARINDEX(' ',B.CleanName) + 1,LEN(B.CleanName)) AS SIGNNAME
                  , SUBSTRING(B.WorkTeam,CHARINDEX(' ',B.WorkTeam) + 1,LEN(B.WorkTeam)) AS GROUPNAME
                  , SUBSTRING(A.WorkCause,CHARINDEX(' ',A.WorkCause) + 1,LEN(A.WorkCause)) AS SERVICENAME
                  , SUBSTRING(B.BackCause,CHARINDEX(' ',B.BackCause) + 1,LEN(B.BackCause)) AS MFNAME1
                  , SUBSTRING(B.CleanCause,CHARINDEX(' ',B.CleanCause) + 1,LEN(B.CleanCause)) AS MFNAME2
                  , ROW_NUMBER() OVER(PARTITION BY A.CompanyNo, A.CustID ORDER BY A.FinishDate DESC) AS SEQNO
                FROM
                  $this->COSSDB.MS0300 A WITH(NOLOCK)
                  INNER JOIN $this->COSSDB.MS0301 B WITH(NOLOCK)
                    ON B.CompanyNo = A.CompanyNo
                    AND B.WorkSheet = A.WorkSheet
                  --INNER JOIN #SNODATA C
                    --ON C.COMPCODE = A.CompanyNo
                    --AND C.CUSTID = A.CustID
                WHERE
                  1 = 1
                  AND A.WorkKind IN ('5 維修')
                  AND A.FinishDate < CONVERT(DATE,GETDATE())
                  AND B.SheetStatus < 'A'
                  AND B.ChargeKind < '20'
                  AND CHARINDEX(' ',B.BackCause) > 0
                  AND CHARINDEX(' ',B.CleanCause) > 0
                  AND A.CompanyNo = '{$companyNo}'
                  AND A.CustID = '{$custId}'

EOF;

            $response = DB::connection('WMDB')->select($sql);

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    //設備序號
    public function getMaintainDeviceSWVersion($data)
    {
        try {
            $companyNo = data_get($data,'companyNo');
            $custId = data_get($data,'custId');

            $sql = <<<EOF

            SELECT
                AA.CompanyNo
                ,AA.CustID
                , AA.ServiceName
                , BB.SingleSN AS FACISNO
                ,CASE WHEN AA.CustStatus = '3 已拆' AND BB.StopYN = 'N' THEN 1 END AS NOTGET
                ,SUBSTRING(BB.SWVersion,CHARINDEX(' ',BB.SWVersion) + 1,LEN(BB.SWVersion)) AS MODELNAME
              FROM
                $this->COSSDB.MS0200 AA WITH(NOLOCK)
                INNER JOIN $this->COSSDB.MS0211 BB WITH(NOLOCK)
                  ON BB.CompanyNo = AA.CompanyNo
                  AND BB.SubsID = AA.SubsID
                LEFT JOIN $this->COSSDB.MS0040 CC WITH(NOLOCK)
                  ON CC.CompanyNo = AA.CompanyNo
                  AND CC.ChargeName = AA.BillItem
                  AND CC.ServiceName = AA.ServiceName
                LEFT JOIN COSSERP.dbo.MICD0101 DD WITH(NOLOCK)
                  ON DD.CSModel = BB.SWVersion
                LEFT JOIN $this->COSSDB.MS0040 EE WITH(NOLOCK)
                  ON EE.CompanyNo = BB.CompanyNo
                  AND EE.ChargeName = BB.ChargeName
                  AND EE.ServiceName = BB.ServiceName
              WHERE
                1 = 1
                AND BB.StopYN = 'N'
                AND NOT(BB.SingleSN = '')
                AND (
                  BB.SingleSN = AA.SingleSN
                  OR BB.ChargeName LIKE '%DTV雙向模組%'
                  OR BB.ChargeName LIKE '%外接硬碟%'
                )
                AND AA.CompanyNo = '{$companyNo}'
                AND AA.CustID = '{$custId}'

EOF;

            $response = DB::connection('WMDB')->select($sql);

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    //來電紀錄
    public function getCallRecord($data)
    {
        try {
            $companyNo = data_get($data,'companyNo');
            $custId = data_get($data,'custId');

            $sql = <<<EOF

            SELECT
                TOP 10
                aa.CallRequest
                ,aa.WorkCause
                ,aa.WorkCause2
                ,aa.ServiceName
                ,aa.MSResult
                ,aa.MSComment
                ,aa.MSRemark
                ,aa.CreateTime
                --,*
            FROM $this->COSSDB.MS0310 AS aa WITH(nolock)
            WHERE 1=1
            AND aa.CompanyNo = '{$companyNo}'
            AND aa.CustID = '{$custId}'
            AND aa.WorkCause LIKE '%來電%'
            AND aa.WorkCause NOT IN ('1071 來電銷售未成功(CM)','1070 來電銷售未成功(CATV)','1070 來電銷售未成功')
            ORDER BY aa.CreateTime DESC
            ;

EOF;

            $response = DB::connection('WMDB')->select($sql);

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getOrderWorkKind($data,$addSelect=array())
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0300")->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectOrderInfo($addSelect);

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'so':
                            $this->db = $this->OrderBaseRepository->whereSo($value);
                            break;
                        case 'worksheet':
                            $this->db = $this->OrderBaseRepository->whereWorkSheet($value);
                            break;
                        case 'custid':
                            $this->db = $this->OrderBaseRepository->whereCustId($value);
                            break;
                    }
                }
            }

            $response = $this->db->first();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getQAList($data)
    {

        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table('wm_qa_mang')->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->selectOrderInfo();
            $this->db = $this->OrderBaseRepository->selectOrderInfoRaw('DATEDIFF(day, updated_at, GETDATE()) AS updateItem');
            $this->db = $this->OrderBaseRepository->selectOrderInfoRaw("case WHEN DATEDIFF(day, created_at, GETDATE()) <= 3 THEN '1' ELSE '0' END newItem");

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                    case 'enable':
                    case 'tableType':
                        $this->db = $this->OrderBaseRepository->whereObj([['name'=>$key,'type'=>'=','value'=>$value]]);
                        break;
                    }
                }
            }

            $orderByAry = array(
                ['name'=>'inherit','type'=>'asc'],
                ['name'=>'sort','type'=>'asc'],
                ['name'=>'code','type'=>'asc'],
                ['name'=>'title','type'=>'asc'],
            );
            $this->OrderBaseRepository->orderByOrderAry($orderByAry);

            $query = $this->db->get();

            $ret = $query;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $ret;
    }


    public function getPushMsg($data)
    {

        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0301 AS MS301")->lock('WITH(nolock)'));

            $this->OrderBaseRepository->leftJoinWMMSG();

            $selAry = array('title','companyNo','custId','workSheet','msg','create_at','Id');
            $this->db = $this->OrderBaseRepository->selectOrderInfo($selAry,'MSG.');

            //Log::channel('ewoLog')->info('chk1333 data=='.print_r($data,1));

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'companyno':
                            $this->db = $this->OrderBaseRepository->whereSo($value,'MS301.');
                            break;
                        case 'worksheet':
                            $this->db = $this->OrderBaseRepository->whereWorkSheet($value,'MSG.');
                            break;
                        case 'assignsheet':
                        $this->db = $this->OrderBaseRepository->whereAssignSheet($value,'MS301.');
                            break;
                        case 'usercode':
                            $this->db = $this->OrderBaseRepository->whereUserCode($value);
                            break;
                        case 'likeworker1':
                            $params = array(
                                ['name'=>'worker1','type'=>'like','value'=>$value.'%'],
                            );
                            $this->db = $this->OrderBaseRepository->whereObj($params,'MS301.');
                            break;
                        case 'timestart':
                            $this->db = $this->OrderBaseRepository->whereCreateDateS($value,'MSG.');
                            break;
                        case 'timeend':
                            $this->db = $this->OrderBaseRepository->whereCreateDateE($value,'MSG.');
                            break;
                        case 'read_null':
                            $this->db = $this->OrderBaseRepository->whereNullObj(array(['name'=>'read_at']),'MSG.');
                            break;
                        case 'worksheet_not_null':
                            $this->db = $this->OrderBaseRepository->whereNotNullObj(array('WorkSheet'),'MSG.');
                            break;
                    }
                }
            }

            $this->OrderBaseRepository->orderByOrderAry(array(['name'=>'create_at','type'=>'desc']),'MSG.');

            $groupAry = array('MSG.create_at','MSG.msg','MSG.title','MSG.companyNo','MSG.custId','MSG.workSheet','MSG.Id');
            $this->OrderBaseRepository->groupByOrderAry($groupAry);

            if(config('order.SQL_DEBUG')) {
                $bindings = $this->db->getBindings();
                $sqlStr = str_replace('?', "'".'%s'."'", $this->db->toSql());
                $sqlStr = sprintf($sqlStr, ...$bindings);
                Log::channel('ewoLog')->info('getPushMsg sql=='.$sqlStr);
            }

            $query = $this->db->get();

            $ret = array(
                'query' => $query,
            );

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return $ret;
    }

    // setPushMsg讀取紀錄
    public function setPushMsgRead($data)
    {

        try {
            $this->db = $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_msg')->lock('WITH(nolock)'));

            $whereAry = array(
                ['name'=>'read_at']
            );
            $this->OrderBaseRepository->whereNullObj($whereAry);

            foreach($data as $k => $t) {
                switch($k) {
                    case 'usercode':
                        $this->OrderBaseRepository->whereUserCode($t);
                        break;
                    case 'msg_id':
                        $this->OrderBaseRepository->whereInObj([array('name'=>'Id', 'ary'=>$t)]);
                        break;
                }
            }

            $this->OrderBaseRepository->updateRead($data['read_at']);

            return $this->db;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());

        }
    }


    // 更新寄送mail
    public function updateSentMail($data)
    {
        try {
            $this->OrderBaseRepository->initDB(DB::connection('WMDB_IO')->table('wm_orderlist')->lock('WITH(nolock)'));

            $this->OrderBaseRepository->whereSo($data['companyno']);

            $this->OrderBaseRepository->whereWorkSheet($data['worksheet']);

            $this->db = $this->OrderBaseRepository->updateSentMail($data['sentmail']);

            return $this->db;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

    public function getCdCompany($data)
    {
        try {
            $this->db = $this->OrderBaseRepository->initDB(DB::connection('COSSDBNAME')->table('CDCompany')->lock('WITH(nolock)'));

            $this->db = $this->OrderBaseRepository->whereObj([['name'=>'ComPanyno','type'=>'=','value'=>$data['companyno']]]);

            $this->db = $this->OrderBaseRepository->selectFromAry(['CompCode']);

            $result = $this->db->first();

            return $result;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());

        }
    }

    public function getCaList($data)
    {
        try {
            $p_companyno = data_get($data,'companyno');
            $p_subsid = data_get($data,'subsid');

            $p_SQL =
                "
                select
                    b.CAList as CAList
                from
                    ms0210 a with (nolock),
                    $this->COSSDB.ms0040 b with (nolock)
                where
                        a.CompanyNo='".$p_companyno."'
                    and a.SubsID=".$p_subsid."
                    and a.ChargeName=b.ChargeName
                    and a.CompanyNo=b.CompanyNo
            ";

            $query = DB::connection('COSSDBNAME')->select(DB::raw($p_SQL));

            $result = $query;

            return $result;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());

        }
    }


    // 取得R1，etf_formdata
    public function getETF_FORMDATA($data)
    {

        try {
            $companyNo = data_get($data,'companyNo');
            $workSheet = data_get($data,'workSheet');
            $formType = data_get($data,'formType');

            $sqlStr =
                "
                SELECT * FROM
                r1db.dbo.etf_formdata
                WHERE 1=1
                AND CompanyNo  = '$companyNo'
                AND WorkSheet = '$workSheet'
                AND FormType = '$formType'
            ";
            $query = DB::connection('COSSDBNAME')->select(DB::raw($sqlStr));

            $ret = $query;
        } catch (Exception $e) {
            $ret = $e->getMessage();
        }
        return $ret;

    }



}
