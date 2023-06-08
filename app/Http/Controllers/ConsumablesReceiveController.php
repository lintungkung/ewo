<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Model\MI0130;
use App\Model\wm_usermang;
use App\Repositories\Consumables\ConsumablesRepository;
use App\Repositories\Login\LoginBaseRepository;
use App\Repositories\Login\LoginRepository;
use Illuminate\Http\Request;
use Jerry\JWT\JWT;
use Session;
use Exception;
use Validator;

class ConsumablesReceiveController extends Controller
{
	public function __construct()
	{
	}


	public function receive()
	{
	    $p_data = array();

        $userCode = Session::get('userId');
        $userName = Session::get('userName');

        $baseUser = new LoginBaseRepository();
        $dbUser = new LoginRepository($baseUser);
        $queryUser = $dbUser->getUserInfo($userCode);
        $placeno = data_get($queryUser,'PlaceNo');

        $obj = new ConsumablesRepository();
        $data = array('usercode' => $userCode);
        $query = $obj->getMS0090($data);
        $query = data_get($query,'0');
        $deptname = data_get($query,'DeptName');

        $solist = wm_usermang::where([
            ['Account', $userCode],
            ['IsEnable', 1],
        ])->first();

        $solist = data_get($solist,'CompanyNo');
        $solistAry = explode(',',$solist);
        $companyAry = config('company.database');
        foreach($companyAry as $k => $t) {
            if(!in_array($k,$solistAry)) {
                unset($companyAry[$k]);
            }
        }

        $p_data['companynoary'] = $companyAry;
        $p_data['usercode'] = $userCode;
        $p_data['username'] = $userName;
        $p_data['deptname'] = $deptname;

        $p_data['placeno'] = $placeno;


		return view('consumables.receive',compact('p_data'));
	}


}
