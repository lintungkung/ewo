<?php

namespace App\Repositories\ETSLog;

use \Log;
use Exception;

class ETSLogBaseRepository
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

    public function delete()
    {
        $this->db->delete();

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


    public function insertLog($data)
    {
        $id = $this->db->insertGetId($data);

        return $id;
    }

}
