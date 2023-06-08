<?php

namespace App\Repositories\Log;

use \Log;
use DB;
use Exception;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class LogBaseRepository
{

    private $db;
//
//    public function __construct()
//    {
//
//    }

    public function initDB($Repository_db)
    {
        $this->db = $Repository_db;
    }

//    public function selectUserInfo()
//    {
//        $this->db->select(
//            "CompanyNo","UserPwd","UserName","DeptNo","Email","ProTitle","CompanyList", "MSLevel","StopYN"
//        );
//
//        return $this->db;
//    }
//    public function whereStopYN()
//    {
//        $this->db->where('StopYN', '=', 'N');
//
//        return $this->db;
//    }
//
//
//    public function selectPackageInfo()
//    {
//        $this->db->select(
//            "a.CompanyNo","c.ChargeName","a.PackageCode","b.ServiceName",
//                "a.TotalAmt","a.PackageName","a.PackBookChk","c.CMValue"
//        );
//
//        return $this->db;
//    }
//
//    public function wherePackageCode($packageCode)
//    {
//        $this->db->where('a.PackageCode', '=', $packageCode);
//
//        return $this->db;
//    }
//
//    public function wherePackageName($packageName)
//    {
//        $this->db->where('a.PackageName', 'like', '%'.$packageName.'%');
//
//        return $this->db;
//    }
//
//    public function whereTotalAmt($moneyMin,$moneyMax)
//    {
//        $this->db->whereBetween('a.TotalAmt', [$moneyMin, $moneyMax]);
//
//        return $this->db;
//    }
//
//    public function whereCMValue($cmMin,$cmMax)
//    {
//        $this->db->whereBetween('c.CMValue', [$cmMin, $cmMax]);
//
//        return $this->db;
//    }
//
//
//    public function selectUserTokenInfo()
//    {
//        $this->db->addselect(
//            "*",
//        );
//
//        return $this->db;
//    }
//
//
//    public function insert($data)
//    {
//        $this->db->insert([
//            'userId'    => data_get($data,'userId'),
//            'token'     => data_get($data,'token'),
//            'upDate'    => data_get($data,'upDate'),
//            'exseDate'  => data_get($data,'exseDate'),
//        ]);
//
//        return $this->db;
//    }
//
//    public function updateToken($data)
//    {
//        $this->db->update([
//            'token'     => data_get($data,'token'),
//            'upDate'    => data_get($data,'upDate'),
//            'exseDate'  => data_get($data,'exseDate'),
//        ]);
//
//        return $this->db;
//    }
//

    public function whereId($id)
    {
        $this->db->where('Id', '=', $id);
        return $this->db;
    }

    public function where($data)
    {
        $this->db->where($data);

        return $this->db;
    }

    public function whereObj($column,$type,$value,$asName = '')
    {
        $this->db->where($asName.$column,$type,$value);

        return $this->db;
    }

    public function delete()
    {
        $this->db->delete();
        return $this->db;
    }

    public function updateCheckData($data)
    {
        $this->db->update([
            'update_at' => date('Y-m-d H:i:s'),
            $data['p_columnName'] => data_get($data,'p_value')
        ]);
    }

    public function updateEventTime($data)
    {
        $this->db->update([
            'update_at' => date('Y-m-d H:i:s'),
            $data['p_columnName'] => data_get($data,'p_value')
        ]);
    }

    // 打卡GPS
    public function updateCheckIn($data)
    {
        $this->db->update([
            'update_at' => date('Y-m-d H:i:s'),
            $data['p_columnName'] => data_get($data,'p_value'),
            'gps' => data_get($data,'p_value_gps'),
            'gpsRefAddres' => data_get($data,'gpsRefAddres'),
            'custGps' => data_get($data,'custGps'),
            'gpsDistance' => data_get($data,'gpsDistance'),
        ]);
        return $this->db;
    }

    // 遲到
    public function updataDeltaeDesc($data)
    {
        $p_date = ($data['p_value'] == "no value;")? null : date('Y-m-d H:i:s');
        $p_val = ($data['p_value'] == "no value;")? null : $data['p_value'];
        $this->db->update([
            'update_at' => date('Y-m-d H:i:s')
            ,$data['p_columnName'] => $p_date
            ,'delatedesc' => $p_val
        ]);
        return $this->db;
    }

    // 第二證件照
    public function updataId03Photo($data)
    {
        $p_val = ($data['p_value'] == "no value;")? null : $data['p_value'];
        $this->db->update([
            'update_at' => date('Y-m-d H:i:s')
            ,$data['p_columnName'] => $p_val
            ,'id03' => date('Y-m-d H:i:s')
        ]);
        return $this->db;
    }

    // 簽名檔
    public function updataSign($data)
    {
        $p_val = ($data['p_value'] == "no value;")? null : $data['p_value'];
        $this->db->update([
            'update_at' => date('Y-m-d H:i:s')
            ,$data['p_columnName'] => $p_val
            ,'pdf_v' => config('order.PDF_CODE_V')
        ]);
        return $this->db;
    }

    // 開通
    public function updataOpenApi($data)
    {
        $this->db->update([
            'update_at' => date('Y-m-d H:i:s')
            ,$data['p_columnName'] => date('Y-m-d H:i:s')
            ,'receiveType' => data_get($data,'p_receiveType')
            ,'receiveMoney' => data_get($data,'p_receiveMoney')
        ]);
        return $this->db;
    }

    // 完工
    public function updataFinsh($data)
    {
        $this->db->update([
            'update_at' => date('Y-m-d H:i:s')
            ,$data['p_columnName'] => date('Y-m-d H:i:s')
            ,'receiveType' => data_get($data,'p_receiveType')
            ,'receiveMoney' => data_get($data,'p_receiveMoney')
            ,'BackCause' => data_get($data,'p_BackCause')
            ,'CleanCause' => data_get($data,'p_CleanCause')
        ]);
        return $this->db;
    }

    public function updataChargeBackDesc($data)
    {
        $p_date = ($data['p_value'] == "no value;")? null : date('Y-m-d H:i:s');
        $p_val = ($data['p_value'] == "no value;")? null : $data['p_value'];
        $this->db->update([
            'update_at' => date('Y-m-d H:i:s')
            ,$data['p_columnName'] => $p_date
            ,'chargebackdesc' => $p_val
        ]);
        Log::channel('ewoLog')->info('chk updataChargeBackDesc sql=='.$this->db->toSql());
        return $this->db;
    }

    public function updataServiceReson($data)
    {
        $this->db->update([
            'update_at' => date('Y-m-d H:i:s')
            ,'serviceResonTime' => date('Y-m-d H:i:s')
            ,'serviceResonFirst' => $data['p_serviceResonFirst']
            ,'serviceResonLast' => $data['p_serviceResonLast']
        ]);
        error_log('chk190=='.$this->db->tosql());
        return $this->db;
    }

    public function selectColumn($data)
    {
        $this->db->select("$data");
        return $this->db;
    }

    public function selectAll()
    {
        $this->db->select("*");
        return $this->db;
    }

    public function selectCount()
    {
        $this->db->Select(DB::RAW('count(*) as count'));
        return $this->db;
    }


    // 新增，申告
    public function insertMS0310($data)
    {
        $this->db->insertGetId($data);

        return $this->db;
    }

    public function insertLog($data)
    {
        $id = $this->db->insertGetId([
            'CompanyNo' => data_get($data,'CompanyNo'),
            'WorkSheet' => data_get($data,'WorkSheet'),
            'CustID'    => data_get($data,'CustID'),
            'UserNum' => data_get($data,'UserNum'),
            'UserName' => data_get($data,'UserName'),
            'EventType' => data_get($data,'EventType'),
            'Request'   => data_get($data,'Request'),
            'Responses' => data_get($data,'Responses'),
        ]);
        //Log::channel('ewoLog')->info('insertLog logbase sql=='.$this->db->toSql());

        return $id;
    }


    public function orderByOrderAry($orderBy,$asName='')
    {
        foreach($orderBy as $k => $t) {
            $this->db->orderBy($asName.$t['name'],$t['type']);
        }
        return $this->db;

    }

    // 五金耗料 新增
    public function insertHardCons($data)
    {
        $this->db->insertGetId([
            'orderlistId'   => data_get($data,'p_orderlistId'),
            'CompanyNo'   => data_get($data,'p_companyNo'),
            'userCode'   => data_get($data,'p_userCode'),
            'materialsCode'      => data_get($data,'p_materialsCode'),
            'count'         => data_get($data,'p_count'),
        ]);
        return $this->db;
    }

    // 勞安LOG 新增
    public function addLaborsafetylog($data)
    {
        $id = $this->db->insertGetId($data);

        return $id;
    }

    public function first()
    {
        return $this->db->first();
    }

    public function whereAssignSheet($worksheet,$asName='')
    {
        $this->db->where($asName.'AssignSheet', '=', $worksheet);

        return $this->db;
    }


    public function whereSo($companyNo,$asName='')
    {
        $this->db->where($asName.'CompanyNo', '=', $companyNo);

        return $this->db;
    }

    public function whereWorkSheet($workSheet,$asName='')
    {
        $this->db->where($asName.'WorkSheet', '=', $workSheet);

        return $this->db;
    }

    // 拆機流向_MS0301
    public function whereChargeKind($asName='')
    {
        $this->db->where($asName.'ChargeKind', '=', '00');

        return $this->db;
    }

    // 維修原因，備註
    public function updateMSRemark($data) {
        $this->db->update([
            'MSRemark' => "$data",
        ]);

        return $this->db;
    }

    // 拆機流向_MS0300
    public function updateWorkTeam2($data) {
        $this->db->update([
            'WorkTeam2' => "$data",
        ]);

        return $this->db;
    }

    // 拆機流向_MS0301
    public function updateGiftList2($data) {
        $this->db->update([
            'GiftList2' => "$data",
        ]);

        return $this->db;
    }

    public function objGET() {
        return $this->db->get();
    }

    public function objToSql() {
        return $this->db->ToSql();
    }

    public function objAddSelect($data,$asName='') {
        foreach ($data as $k => $t) {
            $this->db->addSelect($asName.$t);
        }
        return $this->db;
    }

    public function objIsNotNull($data) {
        foreach ($data as $k => $t) {
            $this->db->whereNotNull($t);
        }
        return $this->db;
    }

    public function objIn($data) {
        $this->db->whereIn($data['column'],$data['value']);
        return $this->db;
    }

    public function objNotIn($data) {
        $this->db->whereNotIn($data['column'],$data['value']);
        return $this->db;
    }

    public function t_leftjoinSentMailLOG()
    {
        $this->db->leftJoin(DB::RAW("WMDB_APP.dbo.WM_LOG AS b WITH(NOLOCK)"),function ($join) {
            $join->on("a.CompanyNo", "=", "b.CompanyNo");
            $join->on("a.WorkSheet", "=", "b.WorkSheet");
            $join->where("b.eventtype", "=", "sentmail");
            $join->where("b.responses", "like", "成功[%");
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


}
