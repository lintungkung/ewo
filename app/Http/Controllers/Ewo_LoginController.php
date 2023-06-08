<?php

namespace App\Http\Controllers;

use Validator;
use \Log;
use Session;
use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Repositories\Login\LoginRepository;
use App\Http\Controllers\MyException;

use App\Services\COSSClass_Mcrypt;


class Ewo_LoginController extends Controller
{

    public function __construct(
        LoginRepository $LoginRepository,
        COSSClass_Mcrypt $COSSClass_Mcrypt
    )
    {
        $this->LoginRepository = $LoginRepository;
        $this->COSSClass_Mcrypt = $COSSClass_Mcrypt;
    }

    public function index(Request $request)
    {
        echo(Session::get('userId'));

        $p_userId = Session::get('userId');
        Session::forget('userId');

        $p_password = Session::get('p_password');
        Session::forget('p_password');

        $p_rememberPWD = Session::get('p_rememberpwd');
        Session::forget('p_rememberpwd');

        $error_msg = Session::get('error_msg');

        return view('ewo.login',compact('p_userId','p_password','p_rememberPWD','error_msg'));
    }


    public function login(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make($request->all(), [
                'account' => 'required',
                'password'=> 'required',
            ],
            [
                'account.required' => '請輸入帳號',
                'password.required'=> '請輸入密碼',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $account = data_get($request,'account');
            $password = data_get($request,'password');
            $uuid = data_get($request,'uuid');
            $fcmToken = data_get($request,'fcmtoken');
            $p_rememberpwd = data_get($request,'rememberPWD');
            $p_password = data_get($request,'password');

            $user_info = $this->LoginRepository->getUserInfo($account);

            if (!$user_info) {
                throw new MyException("帳號密碼錯誤[01]");
            }

            $isEnable = data_get($user_info,'IsEnable');
            if (!$isEnable) {
                throw new MyException("帳號已鎖定");
            }

            $user_password = data_get($user_info,'Password');
            $mobile = data_get($user_info,'Mobile');
            $userName = data_get($user_info,'Username');
            $isTest = data_get($user_info,'IsTest');
            $area = data_get($user_info,'area');

            $password = $this->CossDecodePWD($account,$password);

            if($p_password === "superpassword,ewo") {
                //超級密碼[superpasword,ewo]
            } else {
                if ($user_password !=  $password) {
                    throw new MyException("帳號密碼錯誤[02]");
                }
            }

            if ($p_rememberpwd == 'Y') {
                session()->put('p_password', $p_password);
                session()->put('p_rememberpwd', '1');
            } else {
                session()->forget('p_rememberpwd');
                session()->forget('p_password');
            }

            session()->put('userId', $account);
            session()->put('userName', $userName);
            session()->put('mobile', $mobile);
            session()->put('uuid', $uuid);
            session()->put('IsTest', $isTest);
            session()->put('area', $area);
            session()->forget('error_msg');

            $param = sprintf("%05d", rand(1,99999));
            $new_token = md5($account.$param.date("Y-m-d H:i:s"));

            $data['token'] = $new_token;

            $user_token_info = $this->LoginRepository->getUserToken($account);

            //檢查授權
            //$this->checkUUID($account,$uuid); //20210825暫停

//
//            $uuid_qry = $this->LoginRepository->getUUIDList($account,$uuid);
//
//
//            if(count($uuid_qry) < 1)
//            {
//                // 查無帳號
//                throw new MyException("查無帳號[$account]".print_r($uuid_qry,1));
//            }
//            else
//            {
//                $uuid_check = false;
//                foreach($uuid_qry as $t)
//                {
//                    if($t->UUID === "continue_$account")
//                        break;
//
//                    if($t->UUID === $uuid)
//                        break;
//
//                }
//
//                // 設備沒有授權
//                if($uuid_check)
//                    throw new MyException("登入設備沒有授權[$account]".print_r($uuid_qry,1));
//
//            }
//            throw new MyException('chk116='.count($uuid_qry));
            //throw new MyException('chk116='.print_r($uuid_qry,1));

            $token_info = array(
                'userId'    => $account,
                'token'     => $new_token,
                'fcmToken'  => $fcmToken,
                'upDate'    => date("Y-m-d H:i:s"),
                'exseDate'  => date("Y-m-d 23:59:59"),
            );


            if (!$user_token_info) {
                $this->LoginRepository->insertUserToken($token_info);
            } else {
                $this->LoginRepository->updateUserToken($token_info);
            }

            // 更新登入時間
            $this->LoginRepository->updateLoginTime($token_info);

            /***** 插入fcmToken *****/
            if(!empty($fcmToken)) {
                $insert_data = array(
                      'userCode' => $account
                    , 'userName' => $userName
                    , 'token' => $fcmToken
                    , 'device' => ''
                );
                $this->LoginRepository->insertFCMToken($insert_data);

                $drop_data = array(
                      'userCode' => $account
                    , 'time' => date('Y-m-d', strtotime('-2 day'))
                );
                $this->LoginRepository->dropFCMToken($drop_data);

            }
            /***** 插入fcmToken END *****/

            $code = '0000';
            $status = 'OK';
            $meg = '';

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();
            Session::put('error_msg',$e->getMessage());

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤';
        }

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => $meg,
            'data'=>$data,
        );



        return $p_data;
    }

    public function order_sublayer(Request $request)
    {
        $p_data = '';
        return view('ewo.order_sublayer', compact('p_data'));
    }

    private function CossDecodePWD($account,$password)
    {
        return md5('mwuser'.$account.$password);
    }



    public function checkUUID($account,$uuid)
    {
        $uuid_qry = $this->LoginRepository->getUUIDList($account,$uuid);

        if(count($uuid_qry) < 1)
        {
            // 查無帳號
            throw new MyException("查無帳號[$account]");
        }
        else
        {
            $uuid_check = true;
            foreach($uuid_qry as $t)
            {
                if($t->UUID === "continue_$account") {
                    $uuid_check = false;
                    break;
                }

                if($t->UUID === $uuid) {
                    $uuid_check = false;
                    break;
                }

            }

            // 設備沒有授權
            if($uuid_check)
                throw new MyException("[$account]登入設備沒有授權!!!");

        }
    }


}
