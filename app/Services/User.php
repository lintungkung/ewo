<?php

namespace App\Services;

use Session;

use App\Repositories\Login\LoginRepository;

class User {

    public function __construct(
        LoginRepository $LoginRepository
    )
    {
        $this->LoginRepository = $LoginRepository;
    }


    public function checkPermissions($cns)
    {
        $userId = Session::get('userId');

        $user_token_info = $this->LoginRepository->getUserToken($userId);

        $token = data_get($user_token_info, "token");
        $exseDate = data_get($user_token_info, "exseDate");

        if (!$cns || !$userId || $token != $cns || $exseDate < date("Y-m-d H:i:s")) {
            return false;
        }

        return true;
    }

}
