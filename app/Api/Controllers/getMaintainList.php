<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class getMaintainList
{

    const SEC1D = 86400;

    /**
     * 維修紀錄清單
     *
     * @param $request
     *
     * @return array
     *
     */
    static public function getResult($request)
    {
        $companyNo = data_get($request,'companyNo');
        $custId = data_get($request,'custId');
        $countType = data_get($request,'countType');

        $cacheKey = "maintainList#$companyNo#$custId#$countType";
        $sec = self::SEC1D;

        $sql = <<<EOF
            SELECT
            bb.ServiceName,bb.CompanyNo,bb.CustId,bb.SubsId,bb.Worker1
            ,bb.BackCause,bb.CleanCause,bb.WorkCause,CONVERT(VARCHAR(19) ,bb.BookDate,120 ) AS BookDate
            FROM COSSDB.dbo.MS0300 aa WITH(nolock)
            LEFT JOIN COSSDB.dbo.MS0301 bb ON aa.companyno = bb.companyno AND aa.worksheet = bb.assignsheet
            WHERE 1=1
            AND aa.workkind = '5 維修'
            AND
                 bb.bookdate BETWEEN
                    (CASE WHEN '$countType' = '7'
                        THEN CONVERT(varchar(10) ,DATEADD(DAY, -8, getDate() ),120 )
                        ELSE CONVERT(varchar(10) ,DATEADD(MONTH, -1, getDate() ),120 )
                        END
                        )
                    AND CONVERT(varchar(10) ,DATEADD(DAY, -1, getDate() ),120 )
            AND bb.CompanyNo IN ('$companyNo')
            AND bb.CustID = '$custId'
            AND bb.SheetStatus NOT IN ('A.取消')
            ;
EOF;

        try {

            if (Cache::store('redis')->has($cacheKey)) {
                $cache = Cache::store('redis')->get($cacheKey);
                $data = json_decode($cache,1);

            } else {
                $query = DB::connection('WMDB')->select($sql);
                $data = $query;

                $dataJson = json_encode($data);
                Cache::store('redis')->put($cacheKey, $dataJson, $sec);

            }

            $code = '0000';

        } catch (Exception $e) {
            $code = empty($e->getCode()) ? '0500' : substr('000'.$e->getCode(),'-4');
            $data = 'error:'.$e->getMessage();

        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => date('Y-m-d H:i:s'),
        );

        return $ret;

    }

}
