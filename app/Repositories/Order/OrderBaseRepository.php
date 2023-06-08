<?php

namespace App\Repositories\Order;

use DB;
use Exception;
use PDOException;
use Illuminate\Database\Eloquent\Collection;
use function Sodium\add;

class OrderBaseRepository
{
    private $COSSDB;
    private $db;

    public function __construct()
    {
        $this->COSSDB = config('order.COSSDBTYPE').'.dbo';
    }

    public function initDB($Repository_db)
    {
        $this->db = $Repository_db;
    }

    public function selectUserOrderInfo()
    {
        $this->db->addselect(
            "*"

        );

        return $this->db;
    }

    public function selectUserOrderInfo0524()
    {
        $this->db->addselect(
            'MS300.CustName','MS300.TeleNum01','MS300.CellPhone01','MS300.FinishDate','MS300.BookDate','MS300.InstAddrName','MS300.WorkKind','MS300.MSComment1','MS300.FinishDate','MS300.PrintBillYN','MS300.CallCause'
            ,'MS301.WorkSheet','MS301.CompanyNo','MS301.CustID','MS301.AssignSheet','MS301.WorkTeam','MS301.Worker1','MS301.Worker2','MS301.SheetStatus','MS301.ChargeName'
            ,'MS40.CMValue','MS200.TeleCod01','wmOrderList.expected','wmOrderList.Id','wmOrderList.receiveMoney','wmOrderList.receiveType'
        );

        $this->db->addselect(
            DB::RAW("STUFF(( SELECT CONCAT(',',servicename) FROM $this->COSSDB.MS0301 WITH(nolock) WHERE 1=1 AND companyno = MS301.companyno AND AssignSheet = MS300.WorkSheet GROUP BY servicename FOR XML PATH('')),1,1,'') AS ServiceNameJson")
        );
        $this->db->addselect(
            DB::RAW("STUFF(( SELECT CONCAT(',',subsid) FROM $this->COSSDB.MS0301 WITH(nolock) WHERE 1=1 AND companyno = MS301.companyno AND AssignSheet = MS300.WorkSheet GROUP BY subsid FOR XML PATH('')),1,1,'') AS SubsIDJson")
        );
        $this->db->addselect(
            DB::RAW("STUFF(( SELECT CONCAT(',',WorkSheet) FROM $this->COSSDB.MS0301 WITH(nolock) WHERE 1=1 AND companyno = MS301.companyno AND AssignSheet = MS300.WorkSheet AND Worker1 = MS301.Worker1  GROUP BY WorkSheet FOR XML PATH('')),1,1,'') AS WorkSheetJson")
        );
        $this->db->addselect(
            DB::RAW("(SELECT SUM(billamt) FROM $this->COSSDB.MS0301 WITH(nolock) WHERE 1=1 AND companyno = MS301.companyno AND AssignSheet = MS300.WorkSheet AND SheetStatus != 'A.取消') AS SUM301")
        );
        $this->db->addselect(
            DB::RAW("CASE WHEN MS300.PrintBillYN = 'N' THEN 0 ELSE (SELECT SUM(RecvAmt) FROM $this->COSSDB.MS3200 WITH(nolock) WHERE 1=1 AND companyno = MS301.companyno AND WorkSheet = MS300.WorkSheet AND RecvYN = 'N') END AS SUM3200")
        );
        $this->db->addselect(
            DB::RAW("CASE WHEN SUBSTRING(MS301.SheetStatus,1,1) IN ('4') THEN (SELECT SUM(RecvAmt) FROM $this->COSSDB.MS3200 WITH(nolock) WHERE 1=1 AND companyno = MS301.companyno AND WorkSheet = MS301.WorkSheet AND CONVERT(VARCHAR,RecvDate,112) = CONVERT(VARCHAR,MS301.BookDate,112) ) ELSE 0 END AS SUM3200ALL")
        );
        return $this->db;
    }


    public function whereUserId($userId)
    {
        $this->db->where('WorkerNum', '=', $userId);

        return $this->db;
    }

    public function whereWorker1($accout,$type,$asName='')
    {
        $this->db->where($asName.'worker1', $type, $accout);

        return $this->db;
    }


    public function whereCMValue()
    {
        $this->db->where('ms40.CMValue', '!=', '');

        return $this->db;
    }

    public function whereFinish($finish)
    {
        if ($finish) { // 已完工
            $this->db->where(function($query){
                $query->whereNotNull('finsh')
                    ->orWhereNotNull('chargeback');
            });
        } else { // 未完工
            $this->db->where(function($query){
                $query->whereNull('finsh')
                    ->whereNull('chargeback');
            });
        }

        return $this->db;
    }

    public function whereFinish0524($data)
    {
        if($data['finish'])
            $this->db->where(function($query){
                $query->whereNotNull('MS300.finishdate')
                    ->orWhere('MS301.sheetstatus','=','A 取消');
            });
        else
            $this->db->where(function($query){
                $query->whereNull('MS300.finishdate')
                    ->orWhere('MS301.sheetstatus','=','A 取消');
            });

        return $this->db;
    }

    public function whereExpected0524($data)
    {
        if($data['expected'] === 1)
            $this->db->where(function($query){
                $query->whereNotNull('wmOrderList.expected');
            });
        elseif($data['expected'] === 2)
            $this->db->where(function($query){
                $query->whereNull('wmOrderList.expected');
            });

        return $this->db;
    }

    public function whereExpected($finish)
    {
        if ($finish) { // 已預約
            $this->db->whereNotNull('expected');
        } else { // 未預約
            $this->db->whereNull('expected');
        }

        return $this->db;
    }

    public function whereWorkKind($workKind)
    {
        $this->db->wherein('WorkKind', $workKind);

        return $this->db;
    }

    public function whereWorkKind0524($workKind)
    {
        $this->db->wherein('MS300.WorkKind', $workKind);

        return $this->db;
    }

    public function whereBookDate($Bookdate,$type,$asName='')
    {
        $this->db->where($asName.'BookDate', $type, $Bookdate);

        return $this->db;
    }

    public function whereBookDate0524()
    {
        $this->db->where('MS301.BookDate', '>=', date("Y-m-d 00:00:00"));
        $this->db->where('MS301.BookDate', '<', date("Y-m-d 00:00:00",strtotime('+3 day')));

        return $this->db;
    }

    public function whereSo($companyNo,$asName='')
    {
        $this->db->where($asName.'CompanyNo', '=', $companyNo);

        return $this->db;
    }

    public function whereUserCode($userCode,$asName='')
    {
        $this->db->where($asName.'userCode', '=', $userCode);

        return $this->db;
    }

    public function whereCreateDateS($timeStart,$asName='')
    {
        $this->db->where($asName.'create_at', '>=', $timeStart);

        return $this->db;
    }

    public function whereCreateDateE($timeEnd,$asName='')
    {
        $this->db->where($asName.'create_at', '<=', $timeEnd);

        return $this->db;
    }

    public function whereId($id)
    {
        $this->db->where('Id', '=', $id);

        return $this->db;
    }


    public function whereWorkSheet($worksheet,$asName='')
    {
        $this->db->where($asName.'WorkSheet', '=', $worksheet);

        return $this->db;
    }


    public function whereSubsId($SubsId,$asName='')
    {
        $this->db->where($asName.'SubsId', '=', $SubsId);

        return $this->db;
    }


    public function whereInWorkSheet($worksheetAry,$asName='')
    {
        $this->db->whereIn($asName.'WorkSheet', $worksheetAry);

        return $this->db;
    }

    public function whereInObj($data,$asName='')
    {
        foreach($data as $k => $t)
        {
            $this->db->whereIn($asName.$t['name'], $t['ary']);
        }

        return $this->db;
    }

    public function whereNotInObj($data,$asName='')
    {
        foreach($data as $k => $t)
        {
            $this->db->whereNotIn($asName.$t['name'], $t['ary']);
        }

        return $this->db;
    }

    public function whereAssignSheet($worksheet,$asName='')
    {
        $this->db->where($asName.'AssignSheet', '=', $worksheet);

        return $this->db;
    }

    public function selectOrderInfo($addSelect=array(),$asName='')
    {
        if(sizeof($addSelect) > 0) {
            foreach($addSelect as $t) {
                $this->db->addselect($asName.$t);
            }
        } else {
            $this->db->addselect("*");
        }

        return $this->db;
    }

    public function selectOrderInfoRaw($str='')
    {
        $this->db->addselect(DB::RAW($str));

        return $this->db;
    }

    public function selectOrderDevicesInfo()
    {
        $this->db->addselect(
            "ms301.ChargeName"
        );

        return $this->db;
    }


    public function leftJoinMS0300_Orderlist()
    {
        $this->db->leftJoin(DB::RAW("$this->COSSDB.MS0300 AS MS300 WITH(NOLOCK)"),function ($join) {
            $join->on("MS301.CompanyNo", "=", "MS300.CompanyNo");
            $join->on("MS301.WorkSheet", "=", "MS300.WorkSheet");
        });
        return $this->db;
    }


    public function leftJoinMS0100()
    {
        $this->db->leftJoin(DB::RAW("$this->COSSDB.MS0100 AS MS010 WITH(NOLOCK)"),function ($join) {
            $join->on("MS301.CompanyNo", "=", "MS010.CompanyNo");
            $join->on("MS301.CustID", "=", "MS010.CustID");
        });
        return $this->db;
    }


    public function leftJoinMS0301()
    {
        $this->db->leftJoin(DB::RAW("$this->COSSDB.MS0301 AS MS301 WITH(NOLOCK)"),function ($join) {
            $join->on("MS301.CompanyNo", "=", "MS300.CompanyNo");
            $join->on("MS301.WorkSheet", "=", "MS300.WorkSheet");
        });
        return $this->db;
    }


    public function leftJoinWM_OrderList()
    {
        $this->db->leftJoin(DB::RAW("wmdb_app.dbo.wm_orderlist AS wmOrderList WITH(NOLOCK)"),function ($join) {
            $join->on("wmOrderList.CompanyNo", "=", "MS301.CompanyNo");
            $join->on("wmOrderList.WorkSheet", "=", "MS301.AssignSheet");
        });
        return $this->db;
    }



    public function leftJoinMS0040()
    {
        $this->db->leftJoin(DB::RAW("$this->COSSDB.MS0040 AS MS40 WITH(NOLOCK)"),function ($join) {
            $join->on("MS40.CompanyNo", "=", "MS300.CompanyNo");
            $join->on("MS40.ChargeName", "=", "MS300.BillItem2");
        });
        return $this->db;
    }

    public function leftJoinMS3200()
    {
        $this->db->leftjoin("MS3200 as ms3200",function ($join) {
            $join->on("ms3200.CompanyNo", "=", "ms300.CompanyNo");
            $join->on("ms3200.CustID", "=", "ms300.CustID");
        });

        return $this->db;
    }

    public function selectChargeInfo($totalField)
    {
        if ($totalField) {
            $this->db->addselect(
                "*"
            );
        } else {
            $this->db->addselect(
                "CompanyNo","CustID","SubsID","WorkSheet","ServiceName","ChargeName","BillAmt","FromDate","TillDate","NextDate","SheetStatus","Worker1","AssignSheet","BrokerKind","BookingNo","ServiceName","ChargeKind","SingleSn","OrgSingleSn","SheetSNo"
            );
        }


        return $this->db;
    }

    public function selectFromAry($selectAry,$asName='')
    {
        foreach($selectAry as $t) {
            $this->db->addselect($asName.$t);
        }

        return $this->db;
    }

    public function addSelectRaw($str='')
    {
        $this->db->selectRaw($str);

        return $this->db;
    }

    public function whereMS3200So($companyNo)
    {
        $this->db->where('ms3200.CompanyNo', '=', $companyNo);

        return $this->db;
    }


    public function whereMS3200WorkSheet($worksheet)
    {
        $this->db->where('ms3200.WorkSheet', '=', $worksheet);

        return $this->db;
    }

    public function whereMS3200ServiceName($ServiceName)
    {

        switch ($ServiceName) {
            case 'CM':
                $this->db->where('ms3200.ServiceName', '=', '2 CM');
                break;
            case 'TWMBB':
                $this->db->where('ms3200.ServiceName', '=', 'D TWMBB');
                break;
            default:
                $this->db->where('ms3200.ServiceName', '<>', '2 CM');
                break;
        }


        return $this->db;
    }

    public function whereMS3200MediumYMIsNull()
    {
        $this->db->where('ms3200.MediumYM', '=', '');

        return $this->db;
    }

    public function updateOrderList($data)
    {
        $this->db->update([
            // 'finsh'         => data_get($data,'finsh'),
            'AssignSheet'    => data_get($data,'AssignSheet'),
            'ServiceName'  => data_get($data,'ServiceName'),
            'CustID'  => data_get($data,'CustID'),
            'SubsID'  => data_get($data,'SubsID'),
            'BookDate'  => data_get($data,'BookDate'),
            'WorkKind'  => data_get($data,'WorkKind'),
            'NetID'  => data_get($data,'NetID'),
            'SaleCampaign'  => data_get($data,'SaleCampaign'),
            'WorkerNum'  => data_get($data,'WorkerNum'),
            'WorkerName'  => data_get($data,'WorkerName'),
            'WorkTeam'  => data_get($data,'WorkTeam'),
            'SubsCP'  => data_get($data,'SubsCP'),
            'MSComment1'  => data_get($data,'MSComment1'),
            'deviceCount'  => data_get($data,'deviceCount'),
            'maintainHistory'  => data_get($data,'maintainHistory'),
            'deviceSWVersion'  => data_get($data,'deviceSWVersion'),
            'WorkCause'  => data_get($data,'WorkCause'),
            'CustName'  => data_get($data,'CustName'),
            'InstAddrName'  => data_get($data,'InstAddrName'),
            'TeleNum01'  => data_get($data,'TeleNum'),
            'TeleCod01_200'  => data_get($data,'TeleCod01'),
            'TeleNum01_200'  => data_get($data,'TeleNum01'),
            'TeleCod02_200'  => data_get($data,'TeleCod02'),
            'TeleNum02_200'  => data_get($data,'TeleNum02'),
            'MSContract'  => data_get($data,'MSContract'),
            'MSContract2'  => data_get($data,'MSContract2'),
            'CreateName'  => data_get($data,'CreateName'),
            'CustBroker'  => data_get($data,'CustBroker'),
            'AcceptDate'  => data_get($data,'AcceptDate'),
            'BrokerKind'  => data_get($data,'BrokerKind'),
            'pdfTerms'  => data_get($data,'pdfTerms'),
            'pdf_v'  => data_get($data,'pdf_v'),
        ]);


        return $this->db;
    }

    public function updateOrderListPaidList($data)
    {
        $this->db->update([
            'PaidList'  => data_get($data,'paidList'),
        ]);


        return $this->db;
    }

    public function updateOrderInfoList($data)
    {
        $this->db->update([
            'orderInfoList'  => data_get($data,'orderInfoList'),
        ]);


        return $this->db;
    }

    public function updateFinshMS3200($data)
    {
        $this->db->update([
            'finshMS3200'  => data_get($data,'finshMS3200'),
        ]);

        return $this->db;
    }

    public function updateFinshMS0300($data)
    {
        $this->db->update([
            'finshMS0300'  => data_get($data,'finshMS0300'),
        ]);

        return $this->db;
    }

    public function updateDataList($data)
    {
        $this->db->update([
            'dataList'  => data_get($data,'dataList'),
        ]);

        return $this->db;
    }

    public function leftJoinMI0100()
    {
        $this->db->leftjoin("MI0100 as mi100",function ($join) {
            $join->on("mi100.CompanyNo", "=", "mi120.CompanyNo");
            $join->on("mi100.MTNo", "=", "mi120.MTNo");
        });

        return $this->db;
    }

    public function leftJoinWMMSG()
    {
        $this->db->leftjoin("wmdb_app.dbo.wm_msg as MSG",function ($join) {
            $join->on("MS301.CompanyNo", "=", "MSG.CompanyNo");
            $join->on("MS301.AssignSheet", "=", "MSG.WorkSheet");
        });

        return $this->db;
    }

    public function selectPlaceInfo()
    {
        $this->db->addselect(
            "mi120.PlaceNo","mi120.MTNo","mi100.MTCHName"
        );

        return $this->db;
    }

    public function whereMI0120So($companyNo)
    {
        $this->db->where('mi120.CompanyNo', '=', $companyNo);

        return $this->db;
    }

    public function whereMI0120PlaceNo($placeNo)
    {
        $this->db->where('mi120.PlaceNo', 'like', '%'.$placeNo);

        return $this->db;
    }


    public function selectOrderHardconsInfo()
    {
        $this->db->addselect(
            "hardcons.count","hardcons_prodlist.*"
        );

        return $this->db;
    }

    public function leftJoinHardcons_Prodlist()
    {
        $this->db->leftjoin("wm_hardcons_prodlist as hardcons_prodlist",function ($join) {
            $join->on("hardcons_prodlist.CompanyNo", "=", "hardcons.CompanyNo");
            $join->on("hardcons_prodlist.materialsCode", "=", "hardcons.materialsCode");
        });

        return $this->db;
    }

    public function whereHardconsId($id)
    {
        $this->db->where('hardcons.orderlistId', '=', $id);

        return $this->db;
    }

    public function selectOrderAmt($asName='')
    {
        $this->db->addselect($asName.'*');

        return $this->db;
    }

    public function leftJoinMS0200()
    {
        $this->db->leftjoin("$this->COSSDB.MS0200 as ms200",function ($join) {
            $join->on("ms200.CompanyNo", "=", "ms201.CompanyNo");
            $join->on("ms200.SubsID", "=", "ms201.SubsID");
        });

        return $this->db;
    }


    public function lefJoinObj($data)
    {
        $table = data_get($data,'table');
        $asname = data_get($data,'asname');
        if(!empty($asname)) {
            $asname = "as $asname";
        }
        $onAry = data_get($data,'onary');

        $this->db->leftJoin("$table $asname", function ($join) use ($onAry) {
            foreach($onAry as $k => $t) {
                $join->on($t[0],'=',$t[1]);
            }
        });

        return $this->db;
    }


    public function joinObj($data)
    {
        $table = data_get($data,'table');
        $asname = data_get($data,'asname');
        if(!empty($asname)) {
            $asname = "as $asname";
        }
        $onAry = data_get($data,'onary');

        $this->db->leftJoin("$table $asname", function ($join) use ($onAry) {
            foreach($onAry as $k => $t) {
                $join->on($t[0],'=',$t[1]);
            }
        });

        return $this->db;
    }

    public function leftJoinWM_laborsafetylog()
    {
        $this->db->leftjoin("wm_laborsafetylog as laborsafetylog",function ($join) {
            $join->on("laborsafetylog.CompanyNo", "=", "laborsafety.CompanyNo");
            $join->on("laborsafetylog.laborsafetyid", "=", "laborsafety.Id");
        });

        return $this->db;
    }

    public function leftJoinMS020_OrderList()
    {
        $this->db->leftjoin(DB::RAW("$this->COSSDB.MS0200 as MS200 WITH(NOLOCK)"),function ($join) {
            $join->on("MS200.CompanyNo", "=", "MS301.CompanyNo");
            $join->on("MS200.SubsID", "=", "MS301.SubsID");
        });

        return $this->db;
    }

    public function addSelectSingleSNRaw()
    {
        $str = "ms201.CompanyNo, ms201.SubsID, case when isnull(ms200.SingleSN,'')>'' then ms200.SingleSN else ms201.SingleSN end as SingleSN ";

        $this->db->selectRaw($str);

        return $this->db;
    }

    public function whereMS0201So($companyNo)
    {
        $this->db->where('ms201.CompanyNo', '=', $companyNo);

        return $this->db;
    }

    public function whereMS0201Subsid($subsID)
    {
        $this->db->where('ms201.SubsID', '=', $subsID);

        return $this->db;
    }

    public function whereRecvNo($recvNo)
    {
        $this->db->where('RecvNo', '=', $recvNo);

        return $this->db;
    }

    public function whereObj($data, $asName='')
    {
        foreach($data as $k => $t) {
            $this->db->where($asName.$t['name'], $t['type'], $t['value']);
        }

        return $this->db;
    }

    public function notInObj($data, $asName='')
    {
        $this->db->whereNotIn($asName.$data['name'], $data['list']);

        return $this->db;
    }

    public function whereNotNullObj($data, $asName='')
    {
        foreach($data as $k => $t) {
            $this->db->whereNotNull($asName.$t);
        }

        return $this->db;
    }

    public function whereNullObj($data, $asName='')
    {
        foreach($data as $k => $t) {
            $this->db->whereNull($asName.$t['name']);
        }

        return $this->db;
    }

    public function whereCustId($custID, $asName='')
    {
        $this->db->where($asName.'CustID', '=', $custID);

        return $this->db;
    }

    public function whereChargeName($chargeName, $asName='')
    {
        $this->db->where($asName.'ChargeName', '=', $chargeName);

        return $this->db;
    }

    public function whereChargeKind($chargeKind, $asName='')
    {
        $this->db->where($asName.'ChargeKind', '=', $chargeKind);

        return $this->db;
    }

    public function whereInSheetStatus($sheetStatusAry, $asName='')
    {
        $this->db->whereIn($asName.'SheetStatus', $sheetStatusAry);

        return $this->db;
    }

    public function whereInChargeName($chargeNameAry, $asName='')
    {
        $this->db->whereIn($asName.'ChargeName', $chargeNameAry);

        return $this->db;
    }

    public function whereRecvYN($asName='')
    {
        $this->db->where($asName.'RecvYN', '=', 'N');

        return $this->db;
    }

    public function whereWorkRecvYN($asName='')
    {
        $this->db->where($asName.'WorkRecvYN', '=', 'Y');

        return $this->db;
    }

    public function wherePassYN($asName='')
    {
        $this->db->where($asName.'PassYN', '=', 'N');

        return $this->db;
    }

    public function whereMS3200NotRecvNo()
    {
        $this->db->whereColumn('RecvNo', '<>', 'WorkSheet');

        return $this->db;
    }

    public function whereAssignSheetType()
    {
        $this->db->where('AssignSheetType', '<>', 'Y');

        return $this->db;
    }

    public function addSelectRecvExpire()
    {
        $str = "DATEADD(DAY,-1,RecvExpire) as TillDate ";

        $this->db->selectRaw($str);


        return $this->db;
    }

    public function groupByOrderAry($groupByAry=array())
    {
        foreach ($groupByAry as $t) {
            $this->db->groupBy($t);
        }

        return $this->db;
    }

    public function groupByOrderList()
    {
        $this->db->groupBy('MS300.WorkSheet'
            ,'MS300.CustName','MS300.TeleNum01','MS300.CellPhone01','MS300.FinishDate','MS300.BookDate','MS300.InstAddrName','MS300.PrintBillYN','MS300.WorkKind','MS300.MSComment1','MS300.FinishDate','MS300.CallCause'
            ,'MS301.WorkSheet','MS301.CompanyNo','MS301.CustID','MS301.AssignSheet','MS301.WorkTeam','MS301.Worker1','MS301.Worker2','MS301.BookDate','MS301.SheetStatus','MS301.ChargeName'
            ,'MS40.CMValue','wmOrderList.expected','wmOrderList.Id','MS200.TeleCod01','wmOrderList.receiveMoney','wmOrderList.receiveType');

        return $this->db;
    }

    public function orderByOrderList()
    {
        $this->db->orderBy('MS300.BookDate','asc');
        $this->db->orderBy('MS300.WorkKind','desc');
        $this->db->orderBy('SUM3200ALL','desc');
        $this->db->orderBy('MS301.WorkSheet','asc');

        return $this->db;
    }

    public function orderByOrderAry($orderBy,$asName='')
    {
        foreach($orderBy as $k => $t) {
            $this->db->orderBy($asName.$t['name'],$t['type']);
        }
        return $this->db;

    }

    public function OrderListCount()
    {
        $a = DB::RAW('select count(*) from');

        return $a;
    }

    public function wherePdfOrderListId($id)
    {
        $this->db->where('orderListId', '=', $id);

        return $this->db;
    }

    public function insertPDF($data)
    {
        $id = $this->db->insertGetId([
            'CompanyNo'     => data_get($data,'CompanyNo'),
            'WorkSheet'     => data_get($data,'WorkSheet'),
            'Version'       => data_get($data,'Version'),
            'AssegnUser'    => data_get($data,'AssegnUser'),
            'Data'          => data_get($data,'Data'),
            'orderListId'   => data_get($data,'orderListId'),
        ]);

        return $id;
    }

    public function insertWMOrderlist($data)
    {
        $id = $this->db->insertGetId($data);

        return $id;
    }

    public function updateMS0300TotalAmt($data)
    {
        $this->db->update([
            'TotalAmt'  => data_get($data,'TotalAmt'),
        ]);

        return $this->db;
    }

    public function updateSentMail($sentmail)
    {
        $this->db->update([
            'sentmail'  => $sentmail,
        ]);

        return $this->db;
    }

    public function updateRead($read)
    {
        $this->db->update([
            'read_at' => $read,
            'update_at' => date('Y-m-d H:i:s')
        ]);

        return $this->db;
    }

    public function updateMS0301BillAmt($data)
    {
        $this->db->update([
            'BillAmt'     => data_get($data,'BillAmt'),
            'UpdateName'  => data_get($data,'UpdateName'),
            'UpdateTime'  => data_get($data,'UpdateTime'),
        ]);

        return $this->db;
    }

    public function updateMS0301SingleSN($data)
    {
        $this->db->update([
            'SingleSN'     => data_get($data,'SingleSN'),
            'SWVersion'     => data_get($data,'SWVersion'),
            'UpdateName'  => data_get($data,'UpdateName'),
            'UpdateTime'  => data_get($data,'UpdateTime'),
        ]);

        return $this->db;
    }

    public function insertMS03Z0($data)
    {
        $id = $this->db->insertGetId($data);

        return $id;
    }

    public function insertMS03Z1($data)
    {
        $id = $this->db->insertGetId($data);

        return $id;
    }

    public function insertMS0301($data)
    {
        $id = $this->db->insertGetId($data);

        return $id;
    }


    public function selectObj($data=array(),$asName='') {

        if(sizeof($data)) {
            foreach ($data as $k => $t) {
                $this->db->addSelect($asName.$t);
            }
        } else {
            $this->db->addSelect($asName.'*');
        }

        return $this->db;
    }


    public function objGET() {
        return $this->db->get();
    }


    public function objFirst() {
        return $this->db->first();
    }
}
