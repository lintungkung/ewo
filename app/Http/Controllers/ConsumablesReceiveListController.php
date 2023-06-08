<?php
namespace App\Http\Controllers;

use App\Api\Controllers\ConsumablesAPIController;
use App\Http\Controllers\Controller;
use App\Model\MI0130;
use App\Model\wm_usermang;
use App\Repositories\Consumables\ConsumablesRepository;
use App\Repositories\Log\LogRepository;
use App\Repositories\Login\LoginBaseRepository;
use App\Repositories\Login\LoginRepository;
use Illuminate\Http\Request;
use Jerry\JWT\JWT;
use Session;
use Exception;
use Validator;

class ConsumablesReceiveListController extends Controller
{
	public function __construct()
	{
	}


	public function index()
	{

	    $p_time = date('Y-m-d H:i:s');
        $p_data = array();
        $companyNoAry = config('order.CompanyNoStrAry');
        $p_data['companyNoAry'] = $companyNoAry;

        $userCode = Session::get('userId');
        $userName = Session::get('userName');

        // 查詢 清單
        $funAry = array(
            'userCode' => $userCode,
            'p_time' => $p_time,
        );
        list($runTime,$recList,$notInstore) = $this->getRecList($funAry);
        $p_data['runTime'] = $runTime;
        $p_data['redList'] = $recList;
        $p_data['notInstore'] = $notInstore;

        $colAry =   array(
            'assignSheet' => '單號',
            'subsId' => '訂編',
            'bookDate' => '預約日期',
            'worker1' => '工程人員',
            'instore' => '是否在庫',
            'orgSingleSn' => '設備序號',
            'csmodel' => '設備型號',
        );
        $p_data['colAry'] = $colAry;

        return view('consumables.receiveList', compact('p_data'));
	}

	public function getRecList($params)
    {
        $userCode = $params['userCode'];
        $p_time = $params['p_time'];
        $vKey = "consumables_RecList_$userCode".'_'.date('Ymd');
        $sec = 60 * 10;
        $redis = app('redis.connection');

        if($redis->exists($vKey)) {
            $data = $redis->get($vKey);
            $dataAry = json_decode($data,true);
            $runTime = data_get($dataAry,'runTime');
            $list = data_get($dataAry,'list');
            $notInstore = data_get($dataAry,'notInstore');

            $ret = array($runTime,$list,$notInstore);

        } else {
            $dbLog = new LogRepository();
            $detailObj = new ConsumablesAPIController();

            $whereAry = array(
                'worker1Like' => "$userCode %",
                'sheetStatusNotIn' => ['A.取消'],
                'bookDateStart' => date('Y-m-d 00:00:00',strtotime('-3 day')),
                'bookDateEnd' => date('Y-m-d 23:59:59'),
            );
            $qryList = $dbLog->getOrderInfo($whereAry);
            $list = array();
            $notInstore = 0;
            foreach ($qryList as $k => $t) {
                $companyNo = data_get($t,'CompanyNo');
                $assignSheet = data_get($t,'AssignSheet');
                $bookDate = data_get($t,'BookDate');
                $bookDate2 = substr($bookDate,0,10);
                $subsId = data_get($t,'SubsID');
                $orgSingleSn = data_get($t,'OrgSingleSN');
                $worker1 = data_get($t,'Worker1');
                if(empty($orgSingleSn)) continue;

                $ary = array(
                    'singlesn' => $orgSingleSn,
                    'companyno' => $companyNo,
                );
                $detail = $detailObj::getDeviceDetail(json_encode($ary));
                $instore = $detail['code'] == '0000'? data_get($detail['data'],'instore') : 'null';
                $csmodel = $detail['code'] == '0000'? data_get($detail['data'],'csmodel') : 'null';

                // 統計 未回庫
                $notInstore += $instore == 'N'? 1 : 0;
                $list[$companyNo][$bookDate2][$assignSheet][] =
                    array(
                        'bookDate' => date('Y-m-d H:i:s',strtotime($bookDate)),
                        'subsId' => $subsId,
                        'instore' => $instore,
                        'csmodel' => $csmodel,
                        'orgSingleSn' => $orgSingleSn,
                    );

            }

            $jsonAry = array(
                'list' => $list,
                'runTime' => $p_time,
                'notInstore' => $notInstore,
            );
            $listJson = json_encode($jsonAry);
            $redis->set($vKey,$listJson);
            $redis->expire($vKey,$sec);

            $ret = array($p_time,$list,$notInstore);

        }

        return $ret;
    }

}
