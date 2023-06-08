<?php

namespace App\Repositories\EMS;

use FontLib\Table\Type\name;
use \Log;
use DB;
use Exception;
use \Session;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class emsRepository
{

    private $db, $emsBaseRepository;

    public function __construct()
    {
        $emsBaseRepository = new emsBaseRepository();
        $this->db = $emsBaseRepository;
    }


    // 倉位 >> (設備 型號 料號) 清單
    public function getPlaceDetail($data)
    {
        $companyno = data_get($data,'companyno');
        $placeno = data_get($data,'placeno');

        $this->db->initDB(DB::connection('WMDB')->table('COSSERP.dbo.MI0120 as a')->lock('WITH(nolock)'));

        $joinAry = array(
            'table' => 'COSSERP.dbo.MI0100',
            'asname' => 'b',
            'onary' => [
                ['a.CompanyNo','b.CompanyNo'],
                ['a.MTNo','b.MTNo'],
            ],
        );
        $this->db->lefJoinObj($joinAry);

        $this->db->selectObj(['PlaceNo','MTNo','StoreQty'],'a.');

        $this->db->selectObj(['MTCHName','MTSpec'],'b.');

        $soAry = array('209','210','220','230','240','250','270','310','610','620','720','730');
        $this->db->whereInObj('CompanyNo',$soAry,'a.');

        foreach ($data as $k => $val) {
            if(!empty($val)) {
                switch ($k) {
                case 'placeno':
                    $this->db->whereObj('PlaceNo', '=', $placeno, 'a.');
                    break;
                case 'companyno':
                    $this->db->whereObj('CompanyNo', '=', $companyno, 'a.');
                    break;
                }
            }
        }

        if(!empty($placeno)) {
            $this->db->whereObj('PlaceNo', '=', $placeno, 'a.');
        }
        if(!empty($companyno)) {
            $this->db->whereObj('CompanyNo', '=', $companyno, 'a.');
        }

        $ordByAry = array(
            ['name' => 'a.CompanyNo', 'type' => 'ASC'],
            ['name' => 'a.MTNo', 'type' => 'ASC'],
        );
        $this->db->orderByObj($ordByAry);

        $sql = $this->db->toSqlObj();

        $data = $this->db->getObj();

        $ret = $data;

        return $ret;

    }


    // 工令合約
    public function getContact($data)
    {
        $this->db->initDB(DB::connection('COSSDB')->table('COSSERP.dbo.MI9920')->lock('WITH(nolock)'));
        $this->db->selectObj();

        foreach ($data as $k => $val) {
            if(!empty($val)) {
                switch ($k) {
                case 'companyNo':
                    $this->db->whereObj('CompanyNo', '=', $val);
                    break;
                case 'useKind':
                    $this->db->whereObj('UseKind', '=', $val);
                    break;
                case 'costCenter':
                    $this->db->whereObj('CostCenter', '=', $val);
                    break;
                }
            }
        }

        $sql = $this->db->toSqlObj();

        $data = $this->db->getObj();

        $ret = $data;

        return $ret;

    }


    public function updateHardwareMaterialsPicking($params)
    {
        $id = data_get($params,'id');
        $reponse = data_get($params,'reponse');
        $retCode = data_get($params,'retCode');
        $retMsg = data_get($params,'retMsg');

        $code = '0000';
        $data = 'OK';
        try {
            $this->db->initDB(DB::connection('WMDB')
                ->table('WMDB_APP.dbo.wm_hardwareMaterialsPicking')
                ->lock('WITH(nolock)'));

            $this->db->whereObj('id','=',$id);

            $this->db->updateObj('reponse',$reponse);
            $this->db->updateObj('retCode',$retCode);
            $this->db->updateObj('retMsg',$retMsg);
            $this->db->updateObj('update_at',date('Y-m-d H:i:s'));

        } catch (Exceptioin $e) {
            $code = $e->getCode();
            $data = $e->getMessage();

        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => date('Y-m-d H:i:s'),
        );

        return $ret;
    }



    public function addHardwareMaterialsPicking($data)
    {
        $this->db->initDB(DB::connection('WMDB')
            ->table('WMDB_APP.dbo.wm_hardwareMaterialsPicking')
            ->lock('WITH(nolock)'));

        $ret = $this->db->insertGetId($data);

        return $ret;

    }





}
