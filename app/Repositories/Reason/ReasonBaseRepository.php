<?php

namespace App\Repositories\Reason;

use DB;
use Exception;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class ReasonBaseRepository
{

    private $db;

    public function __construct()
    {

    }

    public function initDB($Repository_db)
    {
        $this->db = $Repository_db;
    }

    public function selectServiceReasonInfo()
    {
        $this->db->addselect(
            "*"
        );

        return $this->db;
    }

    public function whereSreviceReasonCode($code)
    {
        $this->db->wherein('servicecode', $code);

        return $this->db;
    }

    public function whereObj($data)
    {
        foreach($data as $k => $t) {
            $this->db->where($t['asname'].$t['name'],$t['type'],$t['value']);

        }

        return $this->db;
    }

    public function whereSreviceCode($code)
    {
        $this->db->where('servicecode', '=', $code);

        return $this->db;
    }

    public function likeCompanyNo($data) {
        $this->db->where('CompanyNo', 'like', "$data%");

        return $this->db;
    }

    public function whereCustId($data,$asName = '')
    {
        $this->db->where($asName.'CustId', '=', $data);

        return $this->db;
    }

    public function whereSreviceReasonFirstCode($firstCode)
    {
        $this->db->where('msctrl', '=', $firstCode);

        return $this->db;
    }


    // 拆機流向
    public function selectDemolitionFlow() {
        $this->db->addSelect('MSCD31.dataName');

        return $this->db;
    }

    public function joinMSCD9990() {
        $this->db->Join(DB::RAW("COSSDB.dbo.MSCD9990 AS MSCD9990 WITH(NOLOCK)"),function ($join) {
            $join->on("MSCD9990.SERVICECODE", "=", "MSCD31.SERVICECODE");
        });

        return $this->db;
    }

    // 拆機流向
    public function whereDemolitionFlow() {
        $this->db->where('MSCD31.MSTABLE','=','A014');
        $this->db->where('MSCD31.SERVICECODE','=','1');

        return $this->db;
    }
}
