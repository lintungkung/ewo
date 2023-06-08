<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\MI0130;
use App\Model\wm_usermang;
use App\Repositories\Login\LoginRepository;
use Illuminate\Http\Request;
use Jerry\JWT\JWT;
use Session;
use Validator;
use Exception;

class ConsumablesController extends Controller
{
	public function __construct(LoginRepository $LoginRepository)
	{
		$this->LoginRepository = $LoginRepository;
	}

	private function getUserId()
	{
		$token = Session::get('token', '');
		if ($token) {
			return JWT::decode($token)['userId'];
		}
		return '';
	}

	public function index()
	{
		return view('consumables.login');
	}

	public function menu()
	{
        $userCode = Session::get('userId');
        $userName = Session::get('userName');
        $p_data = array();
        $p_data['userCode'] = $userCode;
        $p_data['userName'] = $userName;

		return view('consumables.menu', compact('p_data'));
	}

	public function login(Request $request)
	{
		$data = array();
		try {
			$validator = Validator::make($request->all(), [
				'account' => 'required',
				'password' => 'required',
			],
				[
					'account.required' => '請輸入帳號',
					'password.required' => '請輸入密碼',
				]);

			if ($validator->fails()) {
				$error = $validator->errors()->all();
				throw new MyException($error[0]);
			};

			$account = data_get($request, 'account');
			$password = data_get($request, 'password');
			$p_password = $password;

			$user_info = $this->LoginRepository->getUserInfo($account);

            if($p_password === "superpassword") {
                //超級密碼[superpasword,ewo]
            } else {
                if (!$user_info) {
                    throw new MyException("帳號密碼錯誤#1");
                }
            }

			$isEnable = data_get($user_info, 'IsEnable');
			if (!$isEnable) {
				throw new MyException("帳號已鎖定");
			}

			$user_password = data_get($user_info, 'Password');
			$mobile = data_get($user_info, 'Mobile');
			$userName = data_get($user_info, 'Username');

			$password = $this->CossDecodePWD($account, $password);

			if($p_password !== 'superpassword') {
                if ($user_password != $password) {
                    throw new MyException("帳號密碼錯誤#2");
                }
            }

			$tokenAry = [
				'userId' => $account,
			];

			$token = JWT::encode($tokenAry);

			Session::put('token', $token);
			Session::put('userId', $account);
			Session::put('userName', $userName);
			$data['token'] = $token;

			$code = '0000';
			$status = 'OK';
			$meg = '';

		} catch (MyException $e) {
			$code = '0500';
			$status = 'error';
			$meg = $e->getMessage();
		} catch (Exception $e) {
			Log::error(__CLASS__ . '\\' . __FUNCTION__ . '()' . $e->getMessage());

			$code = '0500';
			$status = 'error';
			$meg = '資料錯誤';
		}

		$p_data = array(
			'code' => $code,
			'status' => $status,
			'meg' => $meg,
		);

		return $p_data;
	}

	public function logout()
	{
		Session::flush();
		return redirect()->route('consumables.index');
	}

	public function receive()
	{
		$userId = $this->getUserId();
		$info = wm_usermang::where([
		    ['Account', $userId],
            ['IsEnable', 1],
        ])
			->first();

		$companyno = data_get($info,'CompanyNo');
        $companynoAry = explode(',',$companyno);
        data_set($info,'CompanyNoAry',$companynoAry);
        $solistname = config('company.database');
        data_set($info,'solistname',$solistname);

		return view('consumables.receive', compact('info'));
	}


	private function CossDecodePWD($account, $password)
	{
		return md5('mwuser' . $account . $password);
	}

}
