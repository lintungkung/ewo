<?php

namespace App\Repositories\Login;

use App\Repositories\Login\LoginBaseRepository;

use DB;
use Exception;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class LoginRepository
{
    public function __construct(LoginBaseRepository $LoginBaseRepository)
    {
        $this->LoginBaseRepository = $LoginBaseRepository;
        $this->db = $this->LoginBaseRepository;
    }

    public function getUserInfo($userId)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('WMDB')->table('wm_usermang')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->selectUserInfo();

            $this->db = $this->LoginBaseRepository->whereAccount($userId);

            $response = $this->db->first();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getUserToken($account)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('R1DB')->table('user_token')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->selectUserTokenInfo();

            $this->db = $this->LoginBaseRepository->whereUserId($account);

            $response = $this->db->first();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getFCMToken($data)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('WMDB')->table('wm_token')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->selectUserTokenInfo();

            $whereAry = array(
                ['name'=>'userCode', 'type'=>'=', 'value'=>$data['userId']]
                ,['name'=>'token', 'type'=>'=', 'value'=>$data['fcmToken']]
            );
            $this->db = $this->LoginBaseRepository->whereAry($whereAry);

            $response = $this->db->first();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function insertUserToken($data)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('R1DB')->table('user_token')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->insert($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateUserToken($data)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('R1DB')->table('user_token')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->whereUserId($data['userId']);

            $response = $this->LoginBaseRepository->updateToken($data);


            return ($response) ? 1 : 0;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateLoginTime($data)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('WMDB')->table('wm_usermang')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->whereAccount($data['userId']);

            $this->db = $this->LoginBaseRepository->updateLogin($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function insertFCMToken($data)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('WMDB')->table('wm_token')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->insertAry($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function dropFCMToken($data)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('WMDB')->table('wm_token')->lock('WITH(nolock)'));

            $whereAry = array(
                 ['name'=>'userCode', 'type'=>'=', 'value'=>$data['userCode']]
                ,['name'=>'create_at', 'type'=>'<', 'value'=>$data['time']]
            );
            $this->db = $this->LoginBaseRepository->whereAry($whereAry);

            $this->LoginBaseRepository->delete();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function changePWD($data)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('WMDB')->table('wm_usermang')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->whereAccount($data['userId']);

            $this->db = $this->LoginBaseRepository->updatePassword($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getUUIDList($account,$uuid)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('WMDB')->table('wm_uuid')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->selectUUIDInfo();

            $this->db = $this->LoginBaseRepository->whereAccount($account);

            //$this->LoginBaseRepository->whereUUID($uuid);

            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function insertUUID($data)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('WMDB')->table('wm_uuid')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->insertUUID($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateUUID($data)
    {
        try {
            $this->LoginBaseRepository->initDB(DB::connection('WMDB')->table('wm_uuid')->lock('WITH(nolock)'));

            $this->db = $this->LoginBaseRepository->whereUUID($data['uuid']);

            $this->db = $this->LoginBaseRepository->updateAccount($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

}
