<?php

namespace App\Repositories\Customer;

use DB;
use Exception;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class CustomerBaseRepository
{

    private $db;

    public function __construct()
    {

    }

    public function initDB($Repository_db)
    {
        $this->db = $Repository_db;
    }

    public function LeftJoinMS0200()
    {
        $this->db->leftjoin(DB::RAW("COSSDB.dbo.MS0200 as ms200 with(nolock)"),function ($join) {
        //$this->db->leftjoin("ms0200 as ms200",function ($join) {
            $join->on("ms200.CustID", "=", "ms102.CustID");
        });
    }

    public function LeftJoinWmDstbRemotesFromMs200()
    {
        // 使用注意事項:需先 Join 或 from COSS 的 MS0200 並取別名 ms200
        // 因為 "wm_dstb_remotes" 相同的 (CompanyNo,CustID,SubsID) 下只會有一筆資料，所以 join 此表資料不會變多

        $this->db->leftjoin(DB::RAW("WMDB_APP.dbo.wm_dstb_remotes AS wm_dstb_remotes WITH(NOLOCK)"),function ($join) {
            $join->on("ms200.CompanyNo", "=", "wm_dstb_remotes.CompanyNo");
            $join->on("ms200.CustID", "=", "wm_dstb_remotes.CustID");
            $join->on("ms200.SubsID", "=", "wm_dstb_remotes.SubsID");
        });

        return $this->db;

    }

    public function selectCustInfo()
    {
        $this->db->addselect(
            "*"

        );

        return $this->db;
    }


    public function whereMS0102CompanyNo($companyNo)
    {
        $this->db->where('ms102.CompanyNo', '=', $companyNo);

        return $this->db;
    }

    public function whereMS0102CustID($custID)
    {
        $this->db->where('ms102.CustID', '=', $custID);

        return $this->db;
    }

    public function whereMS0102SubsID($subsID)
    {
        $this->db->where('ms200.SubsID', '=', $subsID);

        return $this->db;
    }

    public function selectCustDevices()
    {
        $this->db->addselect(
            "ms200.SingleSN","ms200.SmartCard","ms200.SubsID","ms200.SWVersion","ms200.ServiceName","ms200.ChargeName2","ms200.SWVersion2","ms200.SingleSN2"
        );

        return $this->db;
    }

    public function selectCustPhones()
    {
        $this->db->addselect(
            "CellPhone01","CellPhone02","TeleNum01","TeleNum02","TeleNum03","MailCity"
        );

        return $this->db;
    }

    public function selectDstbRemoteQr()
    {
        $this->db->addselect(
            "wm_dstb_remotes.remoteQrCode"
        );

        return $this->db;
    }


    public function whereMS0200CompanyNo($companyNo)
    {
        $this->db->where('ms200.CompanyNo', '=', $companyNo);

        return $this->db;
    }

    public function whereMS0200CustID($custID)
    {
        $this->db->where('ms200.CustID', '=', $custID);

        return $this->db;
    }

    public function whereMS0200CustStatusNotIn($CustStatusAry)
    {
        $this->db->whereNotIn('ms200.CustStatus', $CustStatusAry);

        return $this->db;
    }

    public function whereCompanyNo($companyNo)
    {
        $this->db->where('CompanyNo', '=', $companyNo);

        return $this->db;
    }

    public function whereWorkSheet($workSheet)
    {
        $this->db->where('WorkSheet', '=', $workSheet);

        return $this->db;
    }

    public function whereAssignSheet($assignSheet)
    {
        $this->db->where('AssignSheet', '=', $assignSheet);

        return $this->db;
    }

    public function whereCustId($custID)
    {
        $this->db->where('CustID', '=', $custID);

        return $this->db;
    }

    public function whereServiceNameIn($serviceName)
    {
        $this->db->whereIn('ServiceName', $serviceName);

        return $this->db;
    }

    public function whereChargeNameLike($data)
    {
        $this->db->where('ChargeName', 'like', $data);

        return $this->db;
    }

    public function updateDevice($data)
    {
        $this->db->update([
            'SingleSN'  => data_get($data,'singleSN'),
            'SmartCard'  => data_get($data,'smartCard'),
            'SubsID'  => data_get($data,'subsid'),
        ]);

        return $this->db;
    }

    public function whereOnlyDSTB()
    {
        $this->db->where('ms200.ServiceName', '=', '3 DSTB');

        return $this->db;
    }

    public function whereIn($column,$data,$asName)
    {
        $this->db->whereIn($asName.$column,$data);

        return $this->db;
    }

    public function orderBy($data)
    {
        foreach($data as $t) {
            $this->db->orderBy($t['name'],$t['type']);
        }

        return $this->db;
    }

}
