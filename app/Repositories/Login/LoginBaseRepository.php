<?php

namespace App\Repositories\Login;

use DB;
use Exception;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class LoginBaseRepository
{

    private $db;

    public function __construct()
    {

    }

    public function initDB($Repository_db)
    {
        $this->db = $Repository_db;
    }

    public function selectUserInfo()
    {
        $this->db->select(
            "Account","CompanyNo","Password","Username","IsEnable","IsTest","Mobile","Dept","PlaceNo","ContractorCode","area","mail"
        );

        return $this->db;
    }

    public function whereAccount($account)
    {
        $this->db->where('Account', '=', $account);

        return $this->db;
    }

    public function selectPackageInfo()
    {
        $this->db->select(
            "a.CompanyNo","c.ChargeName","a.PackageCode","b.ServiceName",
                "a.TotalAmt","a.PackageName","a.PackBookChk","c.CMValue"
        );

        return $this->db;
    }

    public function wherePackageCode($packageCode)
    {
        $this->db->where('a.PackageCode', '=', $packageCode);

        return $this->db;
    }

    public function wherePackageName($packageName)
    {
        $this->db->where('a.PackageName', 'like', '%'.$packageName.'%');

        return $this->db;
    }

    public function whereTotalAmt($moneyMin,$moneyMax)
    {
        $this->db->whereBetween('a.TotalAmt', [$moneyMin, $moneyMax]);

        return $this->db;
    }

    public function whereCMValue($cmMin,$cmMax)
    {
        $this->db->whereBetween('c.CMValue', [$cmMin, $cmMax]);

        return $this->db;
    }


    public function selectUserTokenInfo()
    {
        $this->db->addselect(
            "*"
        );

        return $this->db;
    }

    public function whereUserId($userId)
    {
        $this->db->where('userId', '=', $userId);

        return $this->db;
    }

    public function whereAry($data, $asName='')
    {
        foreach($data as $t) {
            $this->db->where($asName.$t['name'], $t['type'], $t['value']);
        }

        return $this->db;
    }


    public function insert($data)
    {
        $this->db->insert([
            'userId'    => data_get($data,'userId'),
            'token'     => data_get($data,'token'),
            'upDate'    => data_get($data,'upDate'),
            'exseDate'  => data_get($data,'exseDate'),
        ]);

        return $this->db;
    }

    public function updateToken($data)
    {
        $this->db->update([
            'token'     => data_get($data,'token'),
            'upDate'    => data_get($data,'upDate'),
            'exseDate'  => data_get($data,'exseDate'),
        ]);

        return $this->db;
    }

    public function updateLogin($data)
    {
        $this->db->update([
            'LastLoginTime' => data_get($data,'upDate'),
        ]);

        return $this->db;
    }

    public function updatePassword($data)
    {
        $this->db->update([
            'password'  => data_get($data,'password'),
            'Update_at' => data_get($data,'upDate'),
        ]);

        return $this->db;
    }

    public function selectUUIDInfo()
    {
        $this->db->addselect(
            "*"
        );

        return $this->db;
    }

    public function whereUUID($uuid)
    {
        $this->db->where('UUID', '=', $uuid);

        return $this->db;
    }

    public function insertUUID($data)
    {
        $this->db->insert([
            'Account'  => data_get($data,'Account'),
            'UUID'     => data_get($data,'uuid'),
            'Create_at'=> data_get($data,'Create_at')
        ]);

        return $this->db;
    }

    public function insertAry($data)
    {
        $this->db->insert($data);

        return $this->db;
    }

    public function updateAccount($data)
    {
        $this->db->update([
            'Account'  => data_get($data,'Account'),
        ]);

        return $this->db;
    }

    public function delete()
    {
        $this->db->delete();

        return $this->db;
    }


}
