<?php

namespace App\Repositories\ETSLog;

use \Log;
use Exception;
use DB;

class ETSLogRepository
{
    private $base;

    public function __construct()
    {
        $this->base = new ETSLogBaseRepository();
    }

    public function insertAllotLog($data=null)
    {
        try {
            $this->base->initDB(DB::connection('WMDB_IO')->table('ets_apilog')->lock('WITH(nolock)'));
//            $this->base->initDB(DB::connection('WMDB_IO')->table('ets_allotlog')->lock('WITH(nolock)'));

            $response = $this->base->insertLog($data);

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());

        }
    }


}
