<?php

namespace App\Repositories\EMS;

use \Log;
use DB;
use Exception;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class emsBaseRepository
{

    private $db;

    public function initDB($Repository_db)
    {
        $this->db = $Repository_db;
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

    public function toSqlObj() {
        $bindings = $this->db->getBindings();
        $sqlStr = str_replace('?', "'".'%s'."'", $this->db->toSql());
        $sqlStr = sprintf($sqlStr, ...$bindings);

        return $sqlStr;
    }

    public function selRawObj($data) {
        $this->db->addselect(
            DB::RAW($data)
        );

        return $this->db;
    }

    public function getObj() {
        return $this->db->get();
    }

    public function limitObj($data) {
        $star = data_get($data,'start');
        $limit = data_get($data,'limit');

        return $this->db->offset($star)->limit($limit);
    }

    public function offsetObj($val) {
        return $this->db->offset($val);
    }

    public function delete()
    {
        $this->db->delete();
        return $this->db;
    }

    public function updateObj($column,$value)
    {
        $this->db->update([
            $column => $value
        ]);
    }

    public function orderByObj($data)
    {
        foreach($data as $t) {
            $this->db->orderBy($t['name'],$t['type']);
        }

        return $this->db;
    }


    public function groupByObj($groupByAry)
    {
        foreach ($groupByAry as $t) {
            $this->db->groupBy($t);
        }

        return $this->db;
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

    public function whereInObj($colorm,$array,$asName='')
    {
        $this->db->whereIn($asName.$colorm, $array);

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

    public function insertGetId($data)
    {
        $id = $this->db->insertGetId($data);

        return $id;
    }


}
