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

class ConsumablesListController extends Controller
{
	public function __construct()
	{
	}


	public function list()
	{
	    $p_data = array();
        $p_data['header'] = '';
	    $p_data['companynoary'] = config('company.database');

	    $userCode = Session::get('userId');
	    $userName = Session::get('userName');

        $p_data['userCode'] = $userCode;
        $p_data['userName'] = $userName;
        $baseUser = new LoginBaseRepository();
        $dbUser = new LoginRepository($baseUser);
        $queryUser = $dbUser->getUserInfo($userCode);
        $placeno = data_get($queryUser,'PlaceNo');

        $p_data['placeno'] = $placeno;
        $p_data['product'] = '良品';
        $p_data['recycle'] = '回收';

        $db = new ConsumablesRepository();

        $data = array(
            'placeno' => $placeno,
            'instore' => 'Y',
        );
        $query = $db->getDevLisFroPla($data);
        $list = $query['list'];

        $companynoList = array();
        $list2 = array();
        foreach($query['list'] as $k => $t) {
            $companyno = data_get($t,'CompanyNo');
            $backTime = data_get($t,'BackTime');

            if(!in_array($companyno,$companynoList)) {
                array_push($companynoList, $companyno);
            }

            if(empty($backTime)) {
                $list2[$companyno]['product'][] = $t;
            } else {
                $list2[$companyno]['recycle'][] = $t;
            }
        }


		return view('consumables.list',compact('p_data','list','list2','companynoList'));
	}


}
