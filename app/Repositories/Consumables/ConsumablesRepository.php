<?php

namespace App\Repositories\Consumables;

use App\Repositories\Log\LogBaseRepository;

use FontLib\Table\Type\name;
use \Log;
use DB;
use Exception;
use \Session;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class ConsumablesRepository
{
    private $COSSDB;
    private $db;

    public function __construct()
    {
        $this->COSSDB = config('order.COSSDBTYPE').'.dbo';
    }

    // 倉位 >> (設備 型號 料號) 清單
    public function getDevLisFroPla($data)
    {
        $placeno = data_get($data,'placeno');
        $singlesn = data_get($data,'singlesn');
        $companyno = data_get($data,'companyno');
        $instore = data_get($data,'instore');

        $obj = New ConsumablesBaseRepository();

        $obj->initDB(DB::connection('WMDB')->table('COSSERP.dbo.MI0130 as a')->lock('WITH(nolock)'));

        $joinAry = array(
            'table' => 'COSSERP.dbo.MI0100',
            'asname' => 'b',
            'onary' => [
                ['a.CompanyNo','b.CompanyNo'],
                ['a.MTNo','b.MTNo'],
            ],
        );
        $obj->lefJoinObj($joinAry);

        $obj->selectObj(['CompanyNo','SingleSN','MTNo','PlaceNo','InStore'],'a.');

        $obj->selectObj(['CSModel','MTCHName','MTSpec'],'b.');

        $obj->selRawObj('(SELECT TOP 1 CreateTime FROM COSSERP.dbo.MI3020 WHERE 1=1
                                    AND CompanyNo=a.CompanyNo AND SingleSN = a.SingleSN
                                    ORDER BY CreateTime DESC) AS CreateTime');

        $obj->selRawObj("(SELECT TOP 1 CreateTime FROM COSSERP.dbo.MI3020 WHERE 1=1
                                AND CompanyNo=a.CompanyNo AND SingleSN = a.SingleSN
                                AND tranno LIKE 'EB%' AND tranno LIKE '%AI%'
                                and CreateTime >= '".date('Y-m-d',strtotime('-20 day'))."'
                                ORDER BY CreateTime DESC) AS BackTime");

        $soAry = array('209','210','220','230','240','250','270','310','610','620','720','730');
        $obj->whereInObj('CompanyNo',$soAry,'a.');

        if(!empty($instore)) {
            $obj->whereObj('InStore','=','Y','a.');
        }
        if(!empty($placeno)) {
            $obj->whereObj('PlaceNo', '=', $placeno, 'a.');
        }
        if(!empty($singlesn)) {
            $obj->whereObj('SingleSN', '=', $singlesn, 'a.');
        }
        if(!empty($companyno)) {
            $obj->whereObj('CompanyNo', '=', $companyno, 'a.');
        }

        $ordByAry = array(
            ['name' => 'BackTime', 'type' => 'DESC'],
            ['name' => 'a.CreateTime', 'type' => 'DESC'],
            ['name' => 'a.CompanyNo', 'type' => 'ASC'],
            ['name' => 'b.CSModel ', 'type' => 'ASC'],
            ['name' => 'a.MTNo', 'type' => 'ASC'],
        );
        $obj->orderByObj($ordByAry);

//        $sql = $obj->toSqlObj();

        $data = $obj->getObj();

        $ret = array(
            'list'=>$data,
//            'sql'=>$sql,
        );

        return $ret;

    }




    // 公司別+序號 >> SubsID
    public function selDevSubsID($data)
    {
        $obj = New ConsumablesBaseRepository();
        $obj->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0211")->lock('WITH(nolock)'));

        foreach($data as $k => $val) {
            switch ($k) {
                case 'companyno':
                    $obj->whereObj('CompanyNo','=',$val);
                    break;

                case 'singlesn':
                    $obj->whereObj('singlesn','=',$val);
                    break;

                case 'stopyn':
                    $obj->whereObj('StopYN','=',$val);
                    break;

            }
        }

        $obj->selectObj(['SubsID']);

        $ret = $obj->getObj();

        return $ret;
/*
select top 1 @Companyno=a.CompanyNo,@worksheet=a.WorkSheet2,@Subsid=a.SubsID,@StopYN=isnull(StopYN,'N')
From MS0211 a with(nolock)
where a.CompanyNo='610'
AND a.singlesn = 'F81D0F0B4060'
order by a.CreateTime desc

  select CompanyNo,SubsID,WorkSheet,ChargeName,BillAmt
  From MS0301 a with(nolock)
  where a.CompanyNo=@Companyno
  and a.SubsID=@Subsid
  and a.ChargeKind in ('71','75','76')
  and a.SheetStatus<'4'

  union all

  select CompanyNo,SubsID,RecvNo,ChargeName,BillAmt
  From MS3200 a with(nolock)
  where a.CompanyNo=@Companyno
   and a.SubsID=@Subsid
  and a.ChargeKind in  ('71','75','76')
  and a.recvyn ='N'

 * */

    }


    // MS0211
    public function getMS0211($data)
    {
        $obj = New ConsumablesBaseRepository();
        $obj->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0211")->lock('WITH(nolock)'));
        $obj->selectObj();

        foreach($data as $k => $val) {
            if(!empty($val))
            switch ($k) {
            case 'companyno':
                $obj->whereObj('CompanyNo','=',$val);
                break;

            case 'companynoIN':
                $obj->whereInObj('CompanyNo',$val);
                break;

            case 'subsid':
                $obj->whereObj('SubsID','=',$val);
                break;

            case 'singlesn':
                $obj->whereObj('SingleSN','=',$val);
                break;

            }
        }

        $orderby = data_get($data,'orderby');
        if(!empty($orderby)) {
            $obj->orderByObj([$orderby]);
        }

        $p_start = data_get($data,'start');
        $p_limit = data_get($data,'limit');
        if(!empty($p_start) || !empty($p_limit)) {
            $obj->limitObj(['start' => $p_start, 'limit' => $p_limit]);
        }

        $ret = $obj->getObj();


        if(config('order.SQL_DEBUG')) {
            $sqlStr = $obj->toSqlObj();
            Log::channel('ewoLog')->info('chk getMS0211 sql=='.$sqlStr);
        }

        return $ret;

    }



    // MS0200
    public function getMS0200($data)
    {
        $obj = new ConsumablesBaseRepository();

        $obj->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0200")->lock('WITH(nolock)'));

        if(count($data['select'])){
            foreach($data['select'] as $k => $t){
                $obj->selectObj([$t['column']],$t['asName'].'.');
            }
        } else{
            $obj->selectObj();
        }

        foreach($data as $k => $val) {
            if(!empty($val))
            switch ($k) {
            case 'companyno':
                $obj->whereObj('CompanyNo','=',$val,'MS0200.');
                break;

            case 'subsid':
                $obj->whereObj('SubsID','=',$val,'MS0200.');
                break;

            case 'custid':
                $obj->whereObj('CustID','=',$val,'MS0200.');
                break;

            case 'custstatusNotIn':
                $obj->whereNotInObj('CustStatus',$val,'MS0200.');
                break;
            }
        }

        if($data['leftMS0211']) {
            $joinAry = array(
                'table' => "$this->COSSDB.MS0211",
                'asname' => 'ms0211',
                'onary' => [
                    ['MS0200.CompanyNo','ms0211.CompanyNo'],
                    ['MS0200.SubsID','ms0211.SubsID'],
                ],
            );
            $obj->lefJoinObj($joinAry);
        }

        $p_start = data_get($data,'p_start');
        $p_limit = data_get($data,'p_limit');
        if(!empty($p_start) && !empty($p_limit)) {
            $obj->limitObj(['start'=>0,'limit'=>1]);
        }

        $sqlStr = $obj->toSqlObj();

        $ret = $obj->getObj();

        return $ret;

    }


    // 違約金 MS3200
    public function getDefaultAmtMS3200($data)
    {
        $obj = New ConsumablesBaseRepository();
        $obj->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS3200")->lock('WITH(nolock)'));

        foreach($data as $k => $val) {
            if(!empty($val))
            switch ($k) {

                case 'companynoIN':
                    $obj->whereInObj('CompanyNo',$val);
                    break;

                case 'custid':
                    $obj->whereObj('CustID','=',$val);
                    break;

                case 'recvyn':
                    $obj->whereObj('RecvYN','=',$val);
                    break;

                case 'passyn':
                    $obj->whereObj('PassYN','=',$val);
                    break;

                case 'chargekindIN':
                    $obj->whereInObj('ChargeKind',$val);
                    break;

            }
        }

        $obj->selRawObj('sum(BillAmt) as amt');

        $strSql = $obj->toSqlObj();

        $ret = $obj->getObj();

        return $ret;

    }


    // 違約金 MS0301
    public function getDefaultAmtMS0301($data)
    {
        $obj = New ConsumablesBaseRepository();
        $obj->initDB(DB::connection('WMDB')->table("$this->COSSDB.MS0301")->lock('WITH(nolock)'));

        foreach($data as $k => $val) {
            if(!empty($val))
            switch ($k) {

                case 'custid':
                    $obj->whereObj('CustID','=',$val);
                    break;

                case 'sheetstatus':
                    $obj->whereObj('SheetStatus','<',$val);
                    break;

                case 'companynoIN':
                    $obj->whereInObj('CompanyNo',$val);
                    break;

                case 'chargekindIN':
                    $obj->whereInObj('ChargeKind',$val);
                    break;

            }
        }

        $obj->selRawObj('sum(BillAmt) as amt');

        $strSql = $obj->toSqlObj();

        $ret = $obj->getObj();

        return $ret;

    }


    // MS0090
    public function getMS0090($data)
    {
        $obj = New ConsumablesBaseRepository();
        $obj->initDB(DB::connection('COSSDB')->table("$this->COSSDB.MS0090")->lock('WITH(nolock)'));

        foreach ($data as $k => $val) {

            switch ($k) {
                case 'usercode' :
                    $obj->whereObj('MSCODE','=',$val);
                    break;
            }
        }
        $obj->selectObj();

        $ret = $obj->getObj();

        return $ret;

    }

}
