<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Repositories\Log\LogRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;
use mysql_xdevapi\SqlStatementResult;
use Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;

class getSameHouseholdArrears
{

    /*
     *
     *  同戶欠費
     *
    */


    static public function getResult($request)
    {
        $companyNo = data_get($request,'companyNo');
        $custId = data_get($request,'custId');
        $workSheet = data_get($request,'workSheet');
        $userCode = data_get($request,'userCode');
        $userName = data_get($request,'userName');

        $sqlStr = <<<EOF
            SELECT
            ROW_NUMBER() OVER (ORDER BY (SELECT 1)) AS No,billamt,CompanyNo,SubsID,AssignSheet,ServiceName,ChargeName
            FROM cossdb.dbo.ms0301 WITH(nolock) WHERE 1=1
            AND companyno ='$companyNo'
            AND custid = '$custId'
            AND SUBSTRING(SheetStatus,1,1) < '4'
            AND chargekind IN ('71','75','76')
EOF;

        $query = DB::connection('WMDB')->select($sqlStr);

        $params['CompanyNo'] = $companyNo;
        $params['WorkSheet'] = $workSheet;
        $params['CustID'] = $custId;
        $params['UserNum'] = $userCode;
        $params['UserName'] = $userName;
        $params['EventType'] = 'Arrears';
        $params['Request'] = "查詢同戶欠費";
        $params['Responses'] = json_encode($query);
        $a = new LogRepository();
        $a->insertLog($params);

        $ret = array(
            'code' => '0000',
            'date' => date('Y-m-d H:i:s'),
            'data' => $query,
        );

        return $ret;

    }


}


