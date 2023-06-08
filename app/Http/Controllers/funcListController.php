<?php

namespace App\Http\Controllers;

use App\Model\wm_funclist;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Log;
use Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
//use App\Http\Controllers\appSet;

class funcListController extends Controller
{

    public function __invoke(Request $req, $p1 = null)
    {
        $userCode = session()->get('userId');
        $userName = session()->get('userName');
        $area = session()->get('area');

        $p_data = array();
        $p_data['userCode'] = $userCode;
        $p_data['userName'] = $userName;
        $p_data['area'] = $area;

        if(empty($p1)) {
            return $this->funcList();

        } else {
            switch ($p1) {
                case 'ewoOrderList':
                    return redirect()->route('ewoOrderList');
                default:
                    $path = __NAMESPACE__.'\\' . $p1;
                    return view("func.$p1", compact('p_data'));
            }
        }

    }


    public function funcList()
    {
        $userCode = session()->get('userId');
        $userName = session()->get('userName');
        $userCode06 = substr($userCode,1,6);

        $query = wm_funclist::select('*')->where('enable','1')->orderBy('sort','asc')->get()->toArray();
        $list = $query;

        $p_data = array();
        $p_data['userCode'] = $userCode;
        $p_data['userName'] = $userName;
//        $p_data['coImg'] = $this->getCoImgRedis($userCode);
        $p_data['timeYMD'] = date('Ymd');
        $p_data['qaNewItem'] = $this->chkQAListNewItem();

        return view('func.funclist', compact('list','p_data'));
    }

    // 檢查QA，有沒有新的
    public function chkQAListNewItem()
    {
        $p_params = array('func' => 'getQAList');
        $client = new Client();
        $url = config('order.EWO_URL').'/api/EWOFUNC';
        $postAry = array(
            'body' => json_encode($p_params),
            'headers' => ['Content-Type' => 'application/json',],
        );
        $result = $client->request('POST', $url, $postAry);

        if($result->getStatusCode() !== 200) return false;

        $resultAry = json_decode($result->getBody(), true);

        if($resultAry['code'] !== '0000') return false;

        $chkNew = '';
        foreach($resultAry['data'] as $k => $t) {
            foreach($t as $k2 => $t2) {
                if(empty($chkNew) && $t2['newItem'] > 0) $chkNew = 'Y';
            }
        }

        return $chkNew;
    }

//    // 判斷，出班檢查
//    public function getCoImgRedis($userCode)
//    {
//        $rKey = 'coImg_'.date('Ymd').'_'.$userCode;
//        $redis = app('redis.connection');
//        $vRedis = $redis->get($rKey);
//        $vCache = empty($vRedis)? 'N' : 'Y';
//        $ret = $vCache;
//
//        return $ret;
//    }

}
