<?php
namespace App\Http\Controllers;

use App\Repositories\Login\LoginBaseRepository;
use App\Repositories\Login\LoginRepository;
use App\Repositories\Order\OrderBaseRepository;
use App\Repositories\Order\OrderRepository;
use App\Services\COSSClass_Mcrypt;
use App\Services\User;
use GuzzleHttp\Client;
use http\Params;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Session;
use Validator;
use App\Http\Controllers\MyException;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use \Log;
use Mail;
use Exception;
use App\Repositories\Log\LogRepository;
use App\Repositories\Log\LogBaseRepository;

use ZipArchive;

class Ewo_EventController extends Controller
{

    private $LogRepository;
    private $OrderRepository;
    private $nowTime;

    public function __construct()
    {
        $this->LogRepository = new LogRepository;

        $baseor = new OrderBaseRepository();
        $this->OrderRepository = new OrderRepository($baseor);
        $this->nowTime = date('Y-m-d H:i:s');
    }

    public function delateApi(Request $request)
    {
        $params = array();
        $params['p_columnName'] = data_get($request,'p_columnName');
        $params['p_value'] = data_get($request,'p_value');
        $params['p_companyNo'] = data_get($request,'p_companyNo');
        $params['p_workSheet'] = data_get($request,'p_workSheet');
        return $this->reqUpdataTime($params);
    }

    public function eventApi(Request $request)
    {
        $params = array();
        $params['p_columnName'] = (isset($request['p_columnName']))? data_get($request,'p_columnName') : '';
        $params['p_value'] = (isset($request['p_value']))? data_get($request,'p_value') : '';
        $params['p_companyNo'] = (isset($request['p_companyNo']))? data_get($request,'p_companyNo') : '';
        $params['p_workSheet'] = (isset($request['p_workSheet']))? data_get($request,'p_workSheet') : '';
        $params['p_id'] = (isset($request['p_id']))? $request['p_id'] : '';
        $params['p_so'] = (isset($request['p_so']))? $request['p_so'] : '';
        $params['p_userCode'] = (isset($request['p_userCode']))? $request['p_userCode'] : '';
        $params['p_userName'] = (isset($request['p_userName']))? $request['p_userName'] : '';
        $params['p_serviceResonFirst'] = (isset($request['p_serviceResonFirst']))? $request['p_serviceResonFirst'] : '';
        $params['p_serviceResonLast'] = (isset($request['p_serviceResonLast']))? $request['p_serviceResonLast'] : '';

        //Log::channel('ewoLog')->info('eventApi $params=='.print_r($params,1));

        switch ($params['p_columnName'])
        {
        case 'hardConsSave': // 五金耗料 存檔
            $params['p_custId'] = data_get($request,'p_custId');
            return $this->hardConsSave($params);

        case 'laborsafety_dangerplace': // 勞安-危險地點
            $data = array();
            $data['CompanyNo'] = $params['p_companyNo'];
            $data['WorkSheet'] = $params['p_workSheet'];
            $data['Type'] = data_get($request,'p_type');
            $this->LogRepository->laborsafetyLogDel($data);

            $desc1 = data_get($request,'p_desc1');
            $desc2 = data_get($request,'p_desc2');

            $data['BookDate'] = data_get($request,'p_bookdate');
            $data['desc1'] = $desc1;
            $data['desc2'] = $desc2;
            $data['reply'] = data_get($request,'p_reply');
            $data['laborsafetyid'] = data_get($request,'p_id');
            $data['UserCode'] = $params['p_userCode'];
            $data['UserName'] = $params['p_userName'];
            $this->LogRepository->laborsafetyLogAdd($data);

            $p_instAddr = data_get($request,'p_instAddr');

            $data = array();
            $data['p_userCode'] = $params['p_userCode'];
            $data['p_userName'] = $params['p_userName'];
            $data['CompanyNo'] = $params['p_companyNo'];
            $data['WorkSheet'] = $params['p_workSheet'];
            $data['CustID'] = data_get($request, 'p_custId');
            $data['Responses'] = '安裝地址:'.$p_instAddr.'#危險地點:'.$desc1.'#注意:'.$desc2;
            $data['EventType'] = $params['p_columnName'];
            $this->insertLog($data);

            $ret = array(
                'code' => '0000',
                'data' => '成功(危險地點確認)',
                'date' => date('Y-m-d H:i:s'),
            );

            return Response()->json($ret);

        case 'laborsafetyCheckList': // 勞安-檢點表

            $data = array();
            $data['CompanyNo'] = $params['p_companyNo'];
            $data['WorkSheet'] = $params['p_workSheet'];
            $data['Type'] = data_get($request,'p_type');
            $data['CustID'] = data_get($request, 'p_custId');
            $data['p_userCode'] = $params['p_userCode'];
            $data['p_userName'] = $params['p_userName'];
            $data['EventType'] = 'laborsafetylog';
            $data['p_value'] = $params['p_value'];

            $this->LogRepository->laborsafetyLogDel($data);

            $list = data_get($request,'checklist');
            $list = json_decode($list,1);
            foreach($list as $k => $t) {
                $p_desc1 = data_get($t,'desc1');
                $p_desc2 = data_get($t,'desc2');
                $p_id = data_get($t,'id');
                $p_bookdate = data_get($t,'bookdate');
                $p_reply = data_get($t,'value');
                $p_reply = $p_reply > 0? 'true' : 'false';

                $addData = array(
                    'CompanyNo' => $data['CompanyNo'],
                    'WorkSheet' => $data['WorkSheet'],
                    'UserCode' => $data['p_userCode'],
                    'UserName' => $data['p_userName'],
                    'Type' => $data['Type'],
                    'Desc1' => $p_desc1,
                    'Desc2' => $p_desc2,
                    'reply' => $p_reply,
                    'BookDate' => $p_bookdate,
                    'CustID' => $data['CustID'],
                    'laborsafetyid' => $p_id,
                );
                $this->LogRepository->laborsafetyLogAdd($addData);
            }
            $this->insertLog($data);
            $params['p_value'] = date('Y-m-d H:i:s');

            return $this->reqUpdataTime($params);

        case 'finishCheckList': // 完工檢核表
            $whereAry = array(
                'id' => $params['p_id'],
            );
            $query = $this->OrderRepository->getOrderInfo($whereAry);
            $value = data_get($query,'dataList');
            $valueAry = json_decode($value,true);
            $valueAry['finishCheckList'] = $params['p_value'];
            $valueJson = json_encode($valueAry);
            $params['p_value'] = $valueJson;
            return $this->reqUpdataTime($params);

        case 'checkOutImgPost': //出班檢查，主管檢查
            $data = array();
            $data['Type'] = 'D.出班檢查';
            $data['p_userCode'] = $params['p_userCode'];
            $data['CompanyNo'] = data_get($request,'p_area');
            $data['WorkSheet'] = date('Y-m-d');
            // 刪除重複的
            $this->LogRepository->laborsafetyLogDel($data);
            $data['p_userName'] = $params['p_userName'];

            $p_mangUser = data_get($request,'p_mangUser');
            $fnameList = config('order.checkOutImg');
            $fnameAry = array_values($fnameList);
            $strAry = array(
                'userMang' => $p_mangUser,
                'list' => implode(',',$fnameAry),
            );
            $vResponses = json_encode($strAry);

            // 新增
            $addData = array(
                'UserCode' => $data['p_userCode'],
                'UserName' => $data['p_userName'],
                'Type' => $data['Type'],
                'CompanyNo' => $data['CompanyNo'],
                'WorkSheet' => $data['WorkSheet'],
//                'Desc1' => date('Y-m-d'),
                'Desc2' => $vResponses,
            );
            $insertLog = $this->LogRepository->laborsafetyLogAdd($addData);

            $data['EventType'] = $params['p_columnName'];
            $data['vResponses'] = $vResponses;
            $this->insertLog($data);
            // 插入，cache
            $rKey = 'coImg_'.date('Ymd').'_'.$data['p_userCode'];
            $redis = app('redis.connection');
            $redis->set($rKey,'Y');
            $redis->expire($rKey, 86400);

            $ret = array(
                'code' => '0000',
                'data' => 'OK',
                'date' => date('Y-m-d H:i:s'),
            );
            return Response()->json($ret);

        case 'checkin': // 打卡
            $params['p_value_gps'] = 'LAT:'.data_get($request,'lat').',LNG:'.data_get($request,'lng');
            $params['p_value'] = date('Y-m-d H:i:s');
            return $this->reqUpdataTime($params);

        case 'serviceReasonRemarks': // 工程人員備註
            $validator = Validator::make($request->all(), [
                'p_companyNo' => 'required',
                'p_workSheet'=> 'required',
            ], [
                'p_companyNo.required' => '請輸入公司別',
                'p_workSheet.required'=> '請輸入工單號碼',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };
            // 新增MS03Z1
            $Data301 = array(
                'so'=>$params['p_companyNo'],
                'worksheet'=>$params['p_workSheet'],
                'custid'=>$request['p_so'],
            );
            $query_ms0301 = $this->OrderRepository->getOrderCharge($Data301,true);
            foreach($query_ms0301 as $k => $insMS03z1) {
                $insMS03z1 = (array)$insMS03z1;
                $insMS03z1['UpdateName'] = $params['p_userCode'].' '.$params['p_userName'];
                $insMS03z1['UpdateTime'] = date('Y-m-d H:i:s');
                $this->OrderRepository->insertMS03Z1($insMS03z1);
            }
            // 更新
            $query = $this->LogRepository->serviceReasonRemarks($params);

            $data = array();
            $data['CompanyNo'] = $params['p_companyNo'];
            $data['WorkSheet'] = $params['p_workSheet'];
            $data['CustID'] = data_get($request,'p_custId');
            $data['p_userCode'] = data_get($request,'p_userCode');
            $data['p_userName'] = data_get($request,'p_userName');
            $data['EventType'] = 'serviceReasonRemarks';
            $data['p_value'] = $params['p_value'] == ''? '!!工程沒有輸入任何內容['.date('YmdHis').']!!' : $params['p_value'];
            $this->insertLog($data);

            Log::channel('ewoLog')->info('chk eventApi serviceReasonRemarks '.$params['p_companyNo'].'-'.$params['p_workSheet'].';p_value=='.$params['p_value'].';query=='.$query);
            $status = ($params['p_value'] === $query)? 'OK' : '更新失敗';
            if(strlen($query) < 1 && strlen($params['p_value']) < 1)
                $status = 'OK..';

            return Response()->json(array("code"=>"0000", "status"=>$status,'data'=>$data['p_value'], "date"=>date('Y-m-d H:i:s')));

        case 'constructionPhotoDel': // 施工圖片，刪除
        case 'id03PhotoDel': // 第二證件，刪除
            //$data['p_id'] = data_get($request,'p_id');
            $params['fname'] = data_get($request,'fname');
            $params['names'] = data_get($request,'names');
            $params['CustID'] = data_get($request,'CustID');
            $params['BookDate'] = data_get($request,'BookDate');

            if(is_array($params['names']) === true) {
                $params['names'] = '["'.implode('","',$params['names']).'"]';
            }
            $p_value = str_replace('"'.$params['fname'].'"','',$params['names']);
            $p_value = str_replace(',,',',',$p_value);
            $p_value = str_replace(',]',']',$p_value);
            $p_value = str_replace('[,','[',$p_value);

            $params['p_value'] = $p_value;
            $ret = $this->reqUpdataTime($params);
            $this->delImage($params);
            return $ret;

        case 'demolitionflow': // 拆機流向
            $ret = $this->LogRepository->setDemolitionFlow_MS0300($params);
            $ret_MS0301 = $this->LogRepository->setDemolitionFlow_MS0301($params);
            $ret_MS0301 = json_encode($ret_MS0301);

            $data = array();
            $data['CompanyNo'] = $params['p_companyNo'];
            $data['WorkSheet'] = $params['p_workSheet'];
            $data['CustID'] = data_get($request,'p_custId');
            $data['p_userCode'] = $params['p_userCode'];
            $data['p_userName'] = $params['p_userName'];
            $data['EventType'] = 'demolitionflow';
            $data['p_value'] = $params['p_value'];
            $data['Responses'] = 'MS0300.WorkTeam2='.$ret.';MS0301.GiftList2='.$ret_MS0301;
            $this->insertLog($data);

            return Response()->json(array("code"=>"0000", "msg"=>$ret, "date"=>date('Y-m-d H:i:s')));

        case 'sentmail': // 寄送mail給客戶
            if($params['p_userCode'] === '001265') {
                $mailContent = array(
//                'soDeptcode'=>$soDeptcode,
//                "soDept"    =>$soDept,
//                "rpnType"   =>$rpnType,
//                "rpnName"   =>$rpnName,
//                "rpnDate"   =>$rpnDate,
//                "applydate21"=>$rpnDate,
//                "applydate22"=>$rpnDate,
//                "fileNameTmp"   =>$fileNameTmp,
//                "fileNameReal"  =>$fileNameReal,
//                "Uploadpath"    =>$Uploadpath,
//                "seqno"         =>$seqno
                );
                $fromEmail = "wm_server@homeplus.net.tw";
                $fromName = "中嘉資訊電子工單";

                $toMail = data_get($request, 'p_value');
                $subject = "中嘉服務合約";

                $bccList = array();

                Mail::send(
                    'ewo.sendMailPDF'
                    , $mailContent
                    , function ($mail) use ($toMail, $bccList, $subject, $fromEmail, $fromName) {
                        $mail->to($toMail)
                            //->bcc($bccList)
                            ->subject($subject)
                            ->from($fromEmail, $fromName);
                    }
                );
            }
            break;

        case 'certificatejz': // 憑證上傳[震江]

            $data = array();
            $data['CompanyNo'] = $params['p_companyNo'];
            $data['WorkSheet'] = $params['p_workSheet'];
            $data['CustID'] = data_get($request,'p_custId');
            $data['p_userCode'] = $params['p_userCode'];
            $data['p_userName'] = $params['p_userName'];
            $data['EventType'] = 'certificatejz';
            $data['p_value'] = $params['p_value'];
            $data['Responses'] = '';
            $this->insertLog($data);
            break;

        case 'PaperPDF': // 紙本工單
            // 新增申告
            $data = array();
            $data['p_value'] = $params['p_value'];
            $data = array(
                'companyNo' => data_get($params,'p_companyNo'),
                'workSheet' => data_get($params,'p_workSheet'),
            );
            $addMS0310 = $this->addMS0310PaperPDF($data);
            return $this->reqUpdataTime($params);

        case 'RetrieveList': // 設備取回單
        case 'BorrowmingList': // 設備借用單
            $select = data_get($request,'p_value');
            $selectJson = json_decode($select,true);
            $selectAry = array();
            foreach($selectJson as $t) {
                $vAry = explode('#',$t);
                $selectAry[$vAry[0]] = array(
                    'id' => $vAry[0],
                    'qty' => $vAry[1],
                );
            }
            $query = $this->OrderRepository->getOrderInfo(['id' => $params['p_id']]);

            if($params['p_columnName'] == 'BorrowmingList') {
                $vBorrowmingList = data_get($query,'BorrowmingList' );
                $vBorrowmingListAry = json_decode($vBorrowmingList,true);
                $vBorrowmingListAry['select'] = $selectAry;
                $vBorrowmingListJson = json_encode($vBorrowmingListAry);
                $params['p_value'] = $vBorrowmingListJson;
            } else {
                $vRetrieveList = data_get($query,'RetrieveList' );
                $vRetrieveListAry = json_decode($vRetrieveList,true);
                $vRetrieveListAry['select'] = $selectAry;
                $vRetrieveListJson = json_encode($vRetrieveListAry);
                $params['p_value'] = $vRetrieveListJson;
            }
            $this->reqUpdataTime($params);

            $ret = array(
                "code"=>'0000',
                "data"=>'OK',
                "date"=>date('Y-m-d H:i:s')
            );
            return $ret;

        case 'termsi': // 條款讀取
        case 'termsd': // 條款讀取
//            $params['p_columnName2'] = $params['p_columnName'];
            $params['p_columnName'] = str_replace('termsPDFRead_','',$params['p_columnName']);
            $params['p_value'] = date('Y-m-d H:i:s');
            $this->reqUpdataTime($params);
            $ret = array(
                "code"=>'0000',
                "data"=> array('column'=>$params['p_columnName']),
                "date"=>date('Y-m-d H:i:s')
            );
            return $ret;

        case 'sign_mcust_select': // 用戶簽名，選擇對象
            $params['p_value'] = json_encode(['sign_mcust_select'=>$params['p_value']]);
            return $this->reqUpdataTime($params);

        case 'cmnsQuery' : // CMNS測試，查詢
            $whereAry = array(
                'companyno' => data_get($params,'p_companyNo'),
                'custid' => data_get($params,'p_custId'),
                'custstatusNotIn' => ['name'=>'CustStatus','ary'=>['3 已拆']],
                'servicenameIn' => ['2 CM', 'C HS', 'D TWMBB', 'F CML'],
            );
            $query = $this->OrderRepository->getMS0200($whereAry);
            $query01 = data_get($query,'0');
            $vSingleSN = data_get($query01,'SingleSN');
            if(empty($vSingleSN)) {
                $ret = array(
                    'code' => '0500',
                    'data' => '尚未開通[找不到設備序號]',
                    'date' => date('Y-m-d H:i:s'),
                );
                return response()->json($ret, 200);
            }
            $vCrmId = config('order.SO_CrmID.'.$params['p_companyNo']);
            $dataAry = array('CrmId' => $vCrmId, 'CmMac' => $vSingleSN);
            $client = new Client();
            $result = $client->request('POST',
                "http://172.17.87.143/api/CMAPI/CM/CheckSpeedTest",
                [
                    'body' => json_encode($dataAry),
                    'headers' => [ 'Content-Type' => 'application/json', ]
                ]);
            $resultJson = $result->getBody();
            $logAry = array(
                'p_userCode' => $params['p_userCode'],
                'p_userName' => $params['p_userName'],
                'CompanyNo' => $params['p_companyNo'],
                'WorkSheet' => $params['p_workSheet'],
                'CustID' => data_get($request,'p_custId'),
                'Request' => 'CMNS測速查詢；request>>'.json_encode($dataAry),
                'Responses' => $resultJson,
                'EventType' => $params['p_columnName'],
            );
            $this->insertLog($logAry);

            $resultJson = json_decode($resultJson,true);
            $vCode = data_get($resultJson,'RetCode') == '0'? '0000' : '0500';
            $vData = $vCode == '0000'? (data_get($resultJson,'RetData')) : data_get($resultJson,'RetMsg');
            if($vCode == '0000') {
                $vData = data_get($resultJson,'RetData');
            } else {
                $vData = data_get($resultJson,'RetMsg');
            }
            $ret = array(
                'code' => $vCode,
                'data' => $vData,
                'date' => $this->nowTime,
            );
            return $ret;

        default:
            return $this->reqUpdataTime($params);

        } // end switch

    }


    // 紙本工單；新增[申告]
    public function addMS0310PaperPDF($params)
    {
        $orderInfo = $this->LogRepository->getOrderInfo($params);

        $orderInfo = $orderInfo[0];

        $addMS0310Data = [
            'CompanyNo' => data_get($orderInfo,'CompanyNo'),
            'InOutBound' => 'I',
            'WorkSheet' => data_get($orderInfo,'AssignSheet').'P',
            'ServiceName' => data_get($orderInfo,'ServiceName'),
            'CustID' => data_get($orderInfo,'CustID'),
            'CustName' => data_get($orderInfo,'CustName'),
            'SubsID' => data_get($orderInfo,'SubsID'),
            'SubsName' => data_get($orderInfo,'CustBroker'),
            'CallRequest' => '40100 裝機服務',
            'WorkCause' => '2170 我要紙本工單',
            'TeleNum01' => data_get($orderInfo,'CellPhone01'),
            'BookDate' => data_get($orderInfo,'BookDate'),
            'WorkTeam' => data_get($orderInfo, 'WorkTeam'),
            'WorkKind' => data_get($orderInfo, 'WorkKind'),
            'MSComment' => data_get($orderInfo, 'MSComment1'),
            'SpendTime' => 0,
            'AssignDate' => date("Y-m-d H:i:s"),
            'AssignName' => data_get($orderInfo,'Worker1'),
            'MSResult' => '',
            'Executor' => data_get($orderInfo,'Worker1'),
            'MSRemark' => '',
            'CaseClose' => 'N',
            'CreateTime' => date("Y-m-d H:i:s"),
            'CreateName' => data_get($orderInfo,'CustBroker'),
            'BrokerKind' => data_get($orderInfo,'BrokerKind'),
            'CustBroker' => data_get($orderInfo,'CustBroker'),
        ];

        $chkExistParams = array(
            'companyNo' => data_get($addMS0310Data,'CompanyNo'),
            'workSheet' => data_get($addMS0310Data,'WorkSheet'),
            'count' => 'Y',
        );
        $chkExist = $this->LogRepository->getMS0310($chkExistParams);
        $chkCount = data_get($chkExist[0],'count');

        if($chkCount < 1) {
            $addMS0310 = $this->LogRepository->insertMS0310($addMS0310Data);
        }

        $ret = '';

        return $ret;
    }


    public function checkData(Request $request)
    {
        $data = $request->all();
        $ret = $this->LogRepository->updateCheckData($data);
        $p_date = date('Y-m-d H:i:s');
        return Response()->json(array("code"=>"0000" , "data"=>$ret, "date"=>$p_date));
    }

    // 五金耗料，存檔
    public function hardConsSave($params)
    {
        $p_params = array();
        $p_params['id'] = $params['p_id'];

        //Log::channel('ewoLog')->info('hardConsSave params=='.print_r($params,1));
        // 刪除舊的紀錄
        $this->LogRepository->hardConsDeleteById($p_params);

        $listAry = json_decode($params['p_value'],1);
        foreach ($listAry as $code => $count) {
            $p_params['p_orderlistId'] = $params['p_id'];
            $p_params['p_companyNo'] = $params['p_companyNo'];
            $p_params['p_userCode'] = $params['p_userCode'];
            $p_params['p_materialsCode'] = $code;
            $p_params['p_count'] = $count;
            $this->LogRepository->hardConsAddSave($p_params);
        }

        $ret['type'] = 'hardConsSave';
        $ret['value'] = $params['p_value'];

        $p_date = date('Y-m-d H:i:s');
        $data = array();
        $data['CompanyNo'] = $params['p_companyNo'];
        $data['WorkSheet'] = $params['p_workSheet'];
        $data['CustID'] = $params['p_custId'];
        $data['EventType'] = 'hardConsSave';
        $data['p_request'] = json_encode($params);
        $data['Responses'] = $params['p_value'];
        $this->insertLog($data);
        return Response()->json(array("code"=>"0000" , "data"=>$ret, "date"=>$p_date));
    }

    public function reqUpdataTime($p_params=null)
    {
        $msg = '';
        $code = '0000';
        $params = $p_params;
        //Log::channel('ewoLog')->info('reqUpdataTime p_params=='.print_r($p_params,1));
        $params['p_value'] = data_get($params,'p_value')?? 'no value;';

        switch ($params['p_columnName']) {
        case 'constructionPhotoDel': // 刪除，施工照片
            $params['p_columnName'] = 'constructionPhoto';
            break;
        case 'id03PhotoDel': // 刪除，第二證件
            $params['p_columnName'] = 'id03Photo';
            break;
        case 'survey': // 家戶側寫
            $params['p_value'] = date('Y-m-d H:i:s');
            break;
        case 'PaperPDF': // 紙本工單
            $params['p_value'] = data_get($p_params,'p_value');
            break;
        case 'checkin': // 打卡
            $params['checkInData'] = data_get($p_params,'checkInData');
            break;
        case 'sign_mcust_select': // pdfv3，用戶簽名，選擇對象
            $params['p_columnName2'] = $params['p_columnName'];
            $params['p_columnName'] = 'twmbbcheck';
            break;
        case 'cmnsQuerySave': // cmns 測速，存檔
            $whereAry = array('id' => data_get($p_params,'p_id'),);
            $query = $this->OrderRepository->getOrderInfo($whereAry);
            $dataList = data_get($query,'dataList');
            $dataListAry = json_decode($dataList,true);
            $dataListAry['cmnsQuery'] = $p_params['p_value'];
            $params['p_value'] = json_encode($dataListAry);
            break;
        case 'cmmacinfo': // CM MAC連線資訊
            $params['p_columnName2'] = $params['p_columnName'];
            $whereAry = array('id' => data_get($p_params,'p_id'));
            $query = $this->OrderRepository->getOrderInfo($whereAry);
            $dataList = data_get($query,'dataList');
            $dataListaAry = json_decode($dataList,true);
            $dataListaAry[$params['p_columnName']] = $p_params['p_value'];
            $params['p_value'] = json_encode($dataListaAry);
            $params['p_columnName'] = 'dataList';
            break;
        case 'finishCheckList': // 完工檢核表
            $params['p_columnName2'] = $p_params['p_columnName'];
            $params['p_columnName'] = 'dataList';
            break;
        } // end switch

        //Log::channel('ewoLog')->info('chk reqUpdataTime $p_params=='.print_r($p_params,1));
        $data = $this->LogRepository->updateEventTime($params);
        $data = json_decode(json_encode($data),1);
        $data['EventType'] = data_get($params, 'p_columnName');
        $data['p_value'] = (isset($data[$params['p_columnName']]))? $data[$params['p_columnName']] : '';
        $data['p_userCode'] = data_get($params,'p_userCode');
        $data['p_userName'] = data_get($params,'p_userName');
        $data['p_sign_chs'] = data_get($params,'p_sign_chs');

        switch ($params['p_columnName']) {
        case 'checkin':
            $data['p_value_gps'] = data_get($params,'p_value_gps');
            break;
        case 'openApi': // 開通API、完工API，LOG紀錄
        case 'finsh':
            $data['p_request'] = data_get($params,'p_request');
            $data['Responses'] = data_get($params,'Responses');
            break;
        case 'survey': //家戶側寫
            $data['p_request'] = '家戶側寫，'.date('Y-m-d H:i:s');
            break;
        case 'id03PhotoDel': // 刪除 第二證件
            $data['EventType'] = 'id03PhotoDel';
            break;
        case 'constructionPhotoDel': // 刪除 施工圖片
            $data['EventType'] = $p_params['p_columnName'];
            break;
        case 'certified': // 已核個資
            $data['EventType'] = $p_params['p_columnName'];
            break;
        case 'saleAP': // 順推-加購wifiAP
            $data['EventType'] = $p_params['p_columnName'];
            break;
        case 'BorrowmingList': // 設備借用單
        case 'RetrieveList': // 設備取回單
            $data['EventType'] = $p_params['p_columnName'];
            $data['Responses'] = $p_params['p_value'];
            break;
        case 'laborsafety_checklist': // 勞安-檢點表
            $data['EventType'] = $p_params['p_columnName'];
            break;
        case 'cmnsQuerySave': // cmns 測速，存檔
            $data['EventType'] = $p_params['p_columnName'];
            $data['Request'] = 'CMNS測速_存檔，OK';
            $data['Responses'] = $params['p_value'];;
            break;
        case 'cmqualityforkg': // CM查詢，網路品質查詢_存檔
            // CM查詢，網路品質查詢_存檔
            if (strpos($params['p_value'], ':false')) {
                $vReq = '網路品質查詢_存檔，不合格[' . $params['p_value'] . ']';
                $vRes = '存檔失敗';
                $code = '0210';
            } elseif (empty($params['p_value'])) {
                $vReq = '網路品質查詢_存檔，不合格[無參數]';
                $vRes = '無參數';
            } else {
                $vReq = '網路品質查詢_存檔，OK';
                $vRes = $params['p_value'];
            }
            $data['EventType'] = $p_params['p_columnName'];
            $data['Request'] = $vReq;
            $data['Responses'] = $vRes;
            break;
        case 'deviceChk': // 維修、換機，設備確認
            $data['EventType'] = $p_params['p_columnName'];
            $data['Responses'] = $p_params['p_value'];
            break;

        default:
            switch(data_get($params,'p_columnName2')) {
            case 'sign_mcust_select': // pdfv3，用戶簽名，選擇對象
                $data['EventType'] = $params['p_columnName2'];
                break;
            case 'finishCheckList': // 完工，檢核表
                $data['EventType'] = $params['p_columnName2'];
                $valueAry = json_decode($p_params['p_value'],true);
                $data['Responses'] = data_get($valueAry,'finishCheckList');
                break;
            case 'cmmacinfo': // CM MAC連線資訊
                $data['EventType'] = $params['p_columnName2'];
                $valueAry = json_decode($params['p_value'],true);
                $data['Responses'] = data_get($valueAry,'cmmacinfo');
                break;

            }
            break;
        } // end Switch

        //Log::channel('ewoLog')->info('reqUpdataTime insertLog data=='.print_r($data,1));
        $this->insertLog($data);

        $ret = data_get($data, data_get($params,'p_columnName'));
        switch ($params['p_columnName']) {
        case 'delate': // 遲到
            $ret = $data['delatedesc'];
            break;
        case 'constructionPhoto': // 施工照片
            $ret = $data['constructionPhoto'];
            break;
        case 'chargeback': // 退單
            $ret = $data['chargebackdesc'];
            break;
        case 'PaperPDF': // 紙本工單
            if(strlen($p_params['p_value']) < 1)
                $ret = '取消紙本工單；時間'.date('Y-m-d H:i:s');
            else
                $ret = '申請紙本工單；時間:'.$p_params['p_value'];
            break;
        case 'cmqualityforkg': // 網路品質查詢_存檔
            $ret = $code == '0210'? '網路品質查詢_不合格' : '';
            break;
        case 'expected': // 約定到府時間
            $baseor = new OrderBaseRepository();
            $objor = new OrderRepository($baseor);
            $baseLog = new LoginBaseRepository();
            $objlog = new LoginRepository($baseLog);
            $baseLogin = new LoginBaseRepository();
            $objLogin = new LoginRepository($baseLogin);
            $objuser = new User($objLogin);
            $objCoss = new COSSClass_Mcrypt();
            $conlogin = new Ewo_LoginController($objLogin,$objCoss);
            $obj = new Ewo_OrderController($objor,$objlog,$objuser,$conlogin);
            $expected = data_get($data,'expected');
            $expected = date('Y-m-d H:i',strtotime($expected));
            $alert = $obj->getAlertTime($data);

            $ret = array(
                'expected' => $expected,
                'alert' => $alert,
            );
            break;
        default:
            $code = '0000';
            $msg = 'OK';
            $ret = '';
            break;
        }

        $return = array(
            "code"=>$code,
            "msg"=>$msg,
            "data"=>$ret,
            "date"=>date('Y-m-d H:i:s')
        );

        return Response()->json($return);
    }


    public function insertLog($data)
    {
        $p_UserNum = data_get($data,'p_userCode');
        $p_UserName = data_get($data,'p_userName');
        //Log::channel('ewoLog')->info('chk insertLog data=='.print_r($data,1));

        switch($data['EventType'])
        {
            case "expected":
                $p_request = '預計到達:'.$data['p_value'];
                break;
            case "serviceReasonRemarks":
                $p_request = '工程人員備註：'.$data['p_value'];
                break;
            case "checkin":
                $p_request = '打卡；' . $data['p_value_gps'];
                break;
            case "delate":
                $p_request = '遲到原因:'.$data['delatedesc'];
                break;
            case "id01":
            case "id02":
            case "id03":
                $p_request = '證件圖片上傳['.$data['EventType'].'];';
                break;
            case "cert01":
            case "cert02":
                $p_request = '憑證圖片上傳['.$data['EventType'].'];';
                break;
            case "sign":
            case "sign_dstb":
            case "sign_cm":
            case "sign_twmbb":
                $p_request = $data['EventType'].'簽名圖片上傳;';
                if(data_get($data,'p_sign_chs') === 'Y')
                    $p_request .= ';Sigh_C_HS';
                break;
            case "sign_mcust":
                $p_request = '用戶_簽名圖片上傳;';
                break;
            case "sign_mengineer":
                $p_request = '工程_簽名圖片上傳;';
                break;
            case "constructionPhoto":
                $p_request = '施工照片圖片上傳;';
                break;
            case "constructionPhotoDel":
                $p_request = '施工照片圖片刪除;';
                break;
            case "serviceReson":
                $p_request = '維修原因['.$data['serviceResonFirst'].','.$data['serviceResonLast'].'];';
                break;
            case "pdf":
                $p_request = '產生PDF';
                break;
            case "finsh": // 完工，API
                $p_request = data_get($data,'p_request');
                break;
            case "openApi": // 開通，API
                $p_request = data_get($data,'p_request');
                break;
            case "hardConsSave": // 五金耗料存檔
                $p_request = '五金耗料存檔，'.data_get($data,'p_request');
                break;
            case "PaperPDF": // 紙本工單
                $p_request = (data_get($data,'p_value') === '')? '取消' : '申請';
                $p_request .= '，紙本工單。';
                break;
            case "survey": // 入戶側寫
                $p_request = '入戶側寫';
                break;
            case "id03Photo": // 第二證件
                $p_request = '新增，第二證件';
                break;
            case "id03PhotoDel": // 第二證件
                $p_request = '刪除，第二證件';
                break;
            case "demolitionflow": // 拆機樓向
                $p_request = '拆機流向:'.$data['p_value'];
                break;
            case "certificatejz": // 憑證上傳[震江]
                $p_request = '憑證上傳，上傳憑證 (1G & 低收)[使用紀錄]';
                break;
            case "WifiTestValue": // WifiTest環境測試
                $p_request = 'WifiTest環境測試:'.$data['p_value'];
                break;
            case "chargeback": // 退單
                $p_request = '退單:'.$data['p_value'];
                break;
            case "sentmail": // 寄送mail
                $p_request = '寄送郵件'.$data['p_value'];
                break;
            case "saleAP": // 順推-加購wifiAP
                $p_request = '順推-加購wifiAP='.$data['p_value'];
                break;
            case "cmqualityforkg": // 網路品質查詢_存檔
            case "cmnsQuerySave": // CMNS測速查詢
                $p_request = data_get($data,'Request');
                break;
            case "laborsafety_dangerplace": // 勞安-危險地點
                $p_request = '危險地點';
                break;
            case "laborsafetyCheckList": // 勞安-檢點表
                $p_request = '檢點表';
                break;
            case "laborsafetyImg": // 勞安-設備照片
                $p_request = '勞安設備照片';
                break;
            case "laborsafetylog": // 勞安-檢點表
                $p_request = '勞安-檢點表';
                break;
            case "checkOutImg": // 出班檢查
                $p_request = '出班檢查，照片上傳';
                break;
            case "checkOutImgPost": // 出班檢查，主管檢查
                $vResponses = data_get($data,'vResponses');
                $data['Responses'] = $vResponses;
                $p_request = '出班檢查，主管檢查';
                break;
            case "certified": // 已核個資
                $p_request = '已核個資['.$data['p_value'].']';
                break;
            case "lineWill": // Line 同意/不同意
                $vAry['Y'] = '同意';
                $vAry['N'] = '不同意';
                $p_request = '詢問用戶 同意/不同意 加入 Line;'.$vAry[$data['p_value']].'['.$data['p_value'].']';
                break;
            case "sign_mcust_select": // pdfv3，用戶簽名，選擇對象
                $p_request = '用戶簽名，選擇對象>>'.data_get(json_decode($data['p_value'],true),'sign_mcust_select');
                break;
            case "BorrowmingList": // 設備借用單
            case "RetrieveList": // 設備取回單
                $p_request = $data['EventType'] == 'BorrowmingList'? '設備借用單' : '設備取回單';
                break;
            case "finishCheckList": // 完工，檢核表
                $p_request = '完工，檢核表';
                break;
            case 'cmmacinfo': // CM MAC連線資訊
                $p_request = '存檔，CM MAC連線資訊';
                break;
            case 'deviceChk': // 維修、換機，設備確認
                $p_request = '維修、換機，設備確認';
                break;
            case 'termsi': // 條款讀取
            case 'termsd': // 條款讀取
                $val = substr($data['EventType'],-1);
                $valAry['i'] = '寬頻條款';
                $valAry['d'] = '有線電視條款';
                $p_request = '讀取條款，'.$valAry[$val];
                break;
            case 'imgSizeAbnormal': // 檢查上傳圖片大小
                $p_request = '圖片大小小於2.5kb;';
                break;
            default:
                $p_request = 'No Msg;EventType=='.$data['EventType'].';CompanyNo=='.data_get($data,'CompanyNo').';WorkSheet=='.data_get($data,'WorkSheet').';CustID=='.data_get($data,'CustID');
                break;
        }
        unset($params);
        $params['CompanyNo'] = data_get($data,'CompanyNo');
        $params['WorkSheet'] = data_get($data,'WorkSheet');
        $params['CustID'] = data_get($data,'CustID');
        $params['EventType'] = data_get($data,'EventType');
        $params['UserNum'] = $p_UserNum;
        $params['UserName'] = $p_UserName;
        $params['Request'] = $p_request;
        $params['Responses'] = data_get($data,'Responses');

        $ret = $this->LogRepository->insertLog($params);

        return $ret;
    }

    public function delImage($request)
    {
//        error_log('chk191=='.print_r($request,1));
//        return;
        try {

//            $validator = Validator::make($request->all(), [
//                'id' => 'required',
//                'imageName' => 'required',
//            ],
//                [
//                    'id.required' => '請確認Id',
//                    'imageName.required' => '請確認檔名',
//                ]);
//
//            if ($validator->fails()) {
//                $error = $validator->errors()->all();
//                throw new Exception($error[0]);
//            };

            $so = data_get($request,'p_companyNo');
            $worksheet = data_get($request,'p_workSheet');

            $sessionKey = $so.'_'.$worksheet.'_';

            $order_info = Session::get($sessionKey.'orderInfo');

            if (empty($order_info)) {
                $id = data_get($request,'p_id');

                $order_info = $this->OrderRepository->getOrderInfoById($id);
                $chargeCMInfo = json_decode($order_info->ChargeDetailCM);
                $chargeCATVInfo = json_decode($order_info->ChargeDetailCATV);
                $chargeTWMBBInfo = json_decode($order_info->ChargeDetailTWMBB);
                $checkCATV = json_decode($order_info->catvcheck);
                $checkCM = json_decode($order_info->cmcheck);
                $checkTWMBB = json_decode($order_info->twmbbcheck);
                $borrowmingList = json_decode($order_info->BorrowmingList);

            }

            $imageName = data_get($request,'fname');

            $custID = data_get($order_info,'CustID');
            $bookDate = data_get($order_info,'BookDate');
            $directory = config('filesystems.disks.upload.root')."/".$custID."_".date("Ymd",strtotime($bookDate))."/".$imageName;

            if(!preg_match('/\.(jpg|png|jpeg)$/', $imageName)) {
                throw new Exception("資料錯誤", 1);
            }

            if (file_exists($directory)) {
                unlink($directory);
            }

            $code = '0000';
            $status = 'OK';
            $meg = '';
            $o_data = $imageName;

        } catch (Exception $e) {
            error_log(print_r($e->getMessage(),true));
            $code = '0400';
            $status = 'error';
            $meg = '資料錯誤';
            $o_data = '';
        }

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'meg' => $meg,
            'data' => $o_data,
            'date' => date('Y-m-d H:i:s')
        );

        return $p_data;


    }


    // 補送信件[2021-08-02 20:39:16 以前]
    public function t_sentmail(Request $request)
    {
        echo '測試_寄送mail';
        echo '<hr>';

        $date = data_get($request,'t');
        $sent = data_get($request,'sent');

        //2021-08-02 20:39:16
        if(empty($request['t'])) {
            exit('請輸入時間');
        }

        $query = $this->LogRepository->t_sentMainSQL($date);

        echo 'sql='.$query['sql'].'<hr>';


        $sentAry = array();
        $errorAry = array();
        $noPDFAry = array();
        foreach($query['data'] as $k => $t) {
            $file = config('order.DOCUMENT_ROOT')."/public/upload/".$t->custid."_".date("Ymd",strtotime($t->bookdate))."/".$t->worksheet.".pdf";;

            data_set($t,'pdf',$file);

            if($this->chkMail($t->sentmail) && $this->checkFile($file)) {
                array_push($sentAry, $t);
            } elseif($this->checkFile($file)) {
                array_push($noPDFAry,$t);
            } else {
                array_push($errorAry,$t);
            }
        }

        if($sent === 'yes') {
            echo '<hr>';
            echo '寄送信件';
            echo '<hr>';
            $i = 0;
            foreach($sentAry as $k => $t) {

                $p_companyno = $t->companyno;
                $p_worksheet = $t->worksheet;
                $p_custid = $t->custid;
                $p_bookdate = $t->bookdate;
                $p_usercode = '12345678';
                $p_username = 'EWO系統';
                $p_mail = $t->sentmail;
                $p_pdf = $t->pdf;
                $p_personid = $t->personid;


                // ZIP
                $zipAry = array(
                    'worksheet' => $p_worksheet,
                    'custid' => $p_custid,
                    'bookdate' => date('Ymd',strtotime($p_bookdate)),
                    'password' => $p_personid,
                );
                $this->fileToZip($zipAry);

                // sent MAIL
                if(0) {
                    $mailContent = array();
                    $fromEmail = "wm_service@homeplus.net.tw";
                    $fromName = "中嘉資訊電子工單";
                    $toMail = $p_mail;
                    $subject = "中嘉服務合約";
                    $file = $p_pdf;

                    //Mail::queue(
                    Mail::send(
                        'ewo.sendMailPDF'
                        , $mailContent
                        , function ($mail) use ($toMail, $subject, $fromEmail, $fromName, $file) {
                            $mail->to($toMail)
                                ->subject($subject)
                                ->attach($file)
                                ->from($fromEmail, $fromName);
                        }
                    );
                }

                echo $p_mail.'<br>';
                if(fmod($i++,50) < 1)
                    sleep(3);

                // add LOG
                if(0) {
                    $data = array();
                    $data['CompanyNo'] = $p_companyno;
                    $data['WorkSheet'] = $p_worksheet;
                    $data['CustID'] = $p_custid;
                    $data['p_userCode'] = $p_usercode;
                    $data['p_userName'] = $p_username;
                    $data['EventType'] = 'sent';
                    $data['p_value'] = $p_mail;
                    $data['Responses'] = '成功[系統補送]';

                    $this->insertLog($data);
                }
            }
        }

        echo '<h1>MAIL & PDF =>OK</h1>';
        $html = '<table >';
        $i = 1;
        foreach($sentAry as $k => $t)
        {
            $html .= '<tr>';
            $html .= '<td style="border: solid darkseagreen;">'.$i++.'</td>';
            $html .= '<td style="border: solid darkseagreen;">'.$t->companyno.'</td>';
            $html .= '<td style="border: solid darkseagreen;">'.$t->worksheet.'</td>';
            $html .= '<td style="border: solid darkseagreen;">'.$t->bookdate.'</td>';
            $html .= '<td style="border: solid darkseagreen;">'.$t->sentmail.'</td>';
            $html .= '<td style="border: solid darkseagreen;">'.$t->pdf.'</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        echo $html;

        echo '<hr>';
        echo '<h1>缺少PDF</h1>';
        $html = '<table >';
        $i = 1;
        foreach($noPDFAry as $k => $t)
        {
            $html .= '<tr>';
            $html .= '<td style="border: solid #795548;">'.$i++.'</td>';
            $html .= '<td style="border: solid #795548;">'.$t->companyno.'</td>';
            $html .= '<td style="border: solid #795548;">'.$t->worksheet.'</td>';
            $html .= '<td style="border: solid #795548;">'.$t->bookdate.'</td>';
            $html .= '<td style="border: solid #795548;">'.$t->sentmail.'</td>';
            $html .= '<td style="border: solid #795548;">'.$t->pdf.'</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        echo $html;

        echo '<h1>Error[other]</h1>';
        echo '<hr>';
        $html = '<table >';
        $i = 1;
        foreach($errorAry as $k => $t)
        {
            $html .= '<tr>';
            $html .= '<td style="border: solid #9e9e9e;">'.$i++.'</td>';
            $html .= '<td style="border: solid #9e9e9e;">'.$t->companyno.'</td>';
            $html .= '<td style="border: solid #9e9e9e;">'.$t->worksheet.'</td>';
            $html .= '<td style="border: solid #9e9e9e;">'.$t->bookdate.'</td>';
            $html .= '<td style="border: solid #9e9e9e;">'.$t->sentmail.'</td>';
            $html .= '<td style="border: solid #9e9e9e;">'.$t->pdf.'</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        echo $html;

        echo '<hr>';
        echo 'end';

    }

    public function fileToZip($data)
    {
        $worksheet = $data['worksheet'];
        $custid = $data['custid'];
        $bookdate = $data['bookdate'];
        $password = $data['password'];

        $forderURL = public_path("upload/$custid"."_$bookdate/");

        $zip_file = "$worksheet.zip"; // 要下载的压缩包的名称

        $zip = new ZipArchive();

        $zip->open($forderURL.$zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        //$zip->setPassword("test222");

        $zip->addFile($forderURL."$worksheet.pdf", "$worksheet.pdf");

        //$zip->setEncryptionName('aa.pdf', ZipArchive::EM_AES_256, 'test');

        $zip->setEncryptionName("$worksheet.pdf", ZipArchive::EM_AES_256,$password);

        $zip->close();
    }


    public function chkMail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkFile($url)
    {
        if(file_exists($url))
            return true;
        else
            return false;
    }



}
