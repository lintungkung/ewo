<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Ewo_EventController;
use App\Http\Controllers\Ewo_LoginController;
use App\Http\Controllers\ewoToolsController;
use App\Repositories\Log\LogRepository;
use App\Repositories\Login\LoginBaseRepository;
use App\Repositories\Login\LoginRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Validator;
use \Log;
use Session;
use Exception;
use Mail;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Api\Controllers\Controller;
use App\Http\Controllers\MyException;

use App\Repositories\Customer\CustomerRepository;
use App\Repositories\Reason\ReasonRepository;
use App\Repositories\Order\OrderRepository;


class ApiController extends Controller
{

    protected $api_url;
    protected $compCode;


     public function __construct(
        LoginRepository $LoginRepository,
        CustomerRepository $CustomerRepository,
        ReasonRepository $ReasonRepository,
        OrderRepository $OrderRepository
    )
    {
        $this->api_url = config('api.url');
        $this->api_cm_url = config('api.cm_url');
        $this->api_cm_lg_url = config('api.cm_lg_url');
        $this->api_dstb_url = config('api.dstb_url');
        $this->compCode = config('company.compCode');
        $this->CustomerRepository = $CustomerRepository;
        $this->ReasonRepository = $ReasonRepository;
        $this->OrderRepository = $OrderRepository;
        $this->LoginRepository = $LoginRepository;
    }

    static function aes_encrypt($p_payload, $p_key)
    {
        $p_payload .= (strlen($p_payload) % 16 === 0) ? '' : (str_repeat("\x00", 16 - (strlen($p_payload) % 16)));
        $p_encrypted = openssl_encrypt($p_payload, 'AES-192-ECB', $p_key, OPENSSL_ZERO_PADDING);
        return ($p_encrypted);
    }

    //開通
    public function AuthorSTB(Request $request,$aaa = '')
    {
        //Log::channel('ewoLog')->info('chk AuthorSTB;');
        $data = array();
        try {

            $validator = Validator::make($request->all(), [
                'companyNo' => 'required',
                'workSheet'=> 'required',
                'deviceNo'=> 'required',
                'mobile'=> 'required',
                'ivrNo'=> 'required',
                'worker'=> 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'workSheet.required'=> '請輸入工單號碼',
                'deviceNo.required'=> '請輸入設備序號',
                'mobile.required'=> '請輸入手機',
                'ivrNo.required'=> '請輸入IVR簡碼',
                'worker.required'=> '請輸入工程人員',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $companyNo = data_get($request,'companyNo');
            $workSheet = data_get($request,'workSheet');
            $deviceNo = data_get($request,'deviceNo');
            $mobile = data_get($request,'mobile');
            $ivrNo = data_get($request,'ivrNo');
            $worker = data_get($request,'worker');
            $HDSerialNo = data_get($request,'HDSerialNo');

            $crmId = $this->compCode[$companyNo];

            $post_data = array(
                'CrmId'             => $crmId,  //公司別
                'CrmWorkshortsno'   => $ivrNo,      //工單簡碼 6碼
                'DeviceSNo3'        => $deviceNo,   //設備序號
                'CrmWorkOrder'      => $workSheet,  //工單全碼
                'CrmWorker1'        => $worker,     //工程人員
                'MobilePhone'       => $mobile,     //手機號碼
                'ReturnMode'        => '2',         //開通方式  0: 控制台 | 1：STB | 2：手機APP
                'IncludeHD'         => '0',         //固定0 2021/6/17
                'HDSerialNo'        => $HDSerialNo, //硬碟序號
            );

            $url = $this->api_url.'/STB/AuthorSTB/';

            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );

            $result = $this->curl($curl_data);

            $result_msg = json_decode($result['d']);

            $p_qrCode = data_get($request,'p_qrCode');

            $p_Request = '開通API：'.json_encode($curl_data).';QRCode:'.$p_qrCode;
            //Log::channel('ewoLog')->info('chk AuthorSTB curl_data=='.print_r($curl_data,1).';reponse=='.print_r($result_msg,1));

            $params = array();
            if ($result_msg->RetCode != 0) {
                $params['CompanyNo'] = $companyNo;
                $params['WorkSheet'] = $workSheet;
                $params['CustID'] = data_get($request,'custId');
                $params['UserNum'] = data_get($request,'p_userCode');
                $params['UserName'] = data_get($request,'p_userName');
                $params['EventType'] = 'openApi';
                $params['Request'] = $p_Request;
                $params['Responses'] = $result['d'];
                $a = new LogRepository();
                $a->insertLog($params);

                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);
            } else {
                $params['p_companyNo'] = $companyNo;
                $params['p_workSheet'] = $workSheet;
                $params['p_receiveType'] = data_get($request,'p_receiveType');
                $params['p_receiveMoney'] = data_get($request,'p_receiveMoney');
                $params['p_request'] = $p_Request;
                $params['Responses'] = $result['d'];
                $params['p_userCode'] = data_get($request,'p_userCode');
                $params['p_userName'] = data_get($request,'p_userName');
                $params['Responses'] = $result['d'];
                $params['p_columnName'] = 'openApi';
                $params['p_value'] = date('Y-m-d H:i:s');
                $b = new Ewo_EventController();
                $b->reqUpdataTime($params);
            }


            $code = '0000';
            $status = 'OK';
            $meg = '';

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();
        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤01';
        }

        $p_companyno = data_get($request,'companyNo');
        $p_worksheet = data_get($request,'workSheet');

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => "$p_companyno-$p_worksheet;".$meg,
            'data'=>$data,
        );

        return $p_data;
    }

    //裝機工單完工
    public function InstalledFinished(Request $request)
    {

        $data = array();
        try {

            $validator = Validator::make($request->all(), [
                'companyNo' => 'required',
                'workSheet'=> 'required',
                'worker'=> 'required',
                'dataMatch'=> 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'workSheet.required'=> '請輸入工單號碼',
                'worker.required'=> '請輸入工程人員',
                'dataMatch.required'=> '請輸入付款類別',
            ]);


            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };


            $companyNo = data_get($request,'companyNo');
            $workSheet = data_get($request,'workSheet');
            $worker = data_get($request,'worker');
            $dataMatch = data_get($request,'dataMatch');
            $p_receiveType = data_get($request,'p_receiveType');
            $p_receiveMoney = data_get($request,'p_receiveMoney');
            $custid = data_get($request,'custId');
            $p_userCode = data_get($request,'p_userCode');
            $p_userName = data_get($request,'p_userName');
            $phoneNum = data_get($request,'phoneNum');
            $serviceName = data_get($request,'serviceName');
            $subsidStr = data_get($request,'p_subsidStr');
            $p_worksheet2 = data_get($request,'p_worksheet2');
            $crmId = $this->compCode[$companyNo];

            $subsidAry = explode(',',$subsidStr);

            //檢查簽名
            $query_data = array(
                'usercode' => $p_userCode,
                'companyno' => $companyNo,
                'worksheet' => $workSheet,
                'servicenamestr' => '0 mcust', // 用戶簽名
            );
            $querySign = $this->checkSignFromFinsh($query_data);
            if(count($querySign) < 1)
            {
                throw new MyException('完工失敗，請檢查用戶/工程簽名。');
            }

            // 檢查檢點表
            // 檢查(品質參數查詢)是否存檔
            $chk252 = self::chkCMQualityforkg($companyNo,$workSheet,'CMQuery');

            if(!empty($chk252)) {
                throw new MyException($chk252);
            }

            $paidData = array(
                'so' => $companyNo,
                'worksheet' => $workSheet,
                'worker1like' => "$p_userCode%",
            );
            $chargeInfo = $this->OrderRepository->getOrderCharge($paidData,true);
            $jsonChargeInfo = json_encode($chargeInfo);

            $workSheetAry = array();
            foreach($chargeInfo as $k => $t) {
                if(in_array(data_get($t,'WorkSheet'),$workSheetAry) === false)
                    $workSheetAry[] = data_get($t,'WorkSheet');
            }

            $paidData['orderInfoList'] = $jsonChargeInfo;

            $backUpMS3200 = $this->getMS3200($companyNo,$workSheetAry);

            $backUpMS0300 = $this->getMS0300($companyNo,$workSheetAry);


            $post_data = array(
                'ProcessType'   => '2',         //固定為2
                'CrmId'         => $crmId,  //公司別
                'SNO'           => $workSheet,  //工單單號
                'WorkerEn'      => $worker,     //工程人員代碼
                'FINTIME'       => date("Y/m/d H:i:s"),     //工單完工時間, 格式 : yyyy/MM/dd HH:mm:ss
                'SignEn'        => $worker,     //簽收人員代碼
                'SignDate'      => date("Y/m/d H:i:s"),     //簽收日期，Date格式: yyyy/MM/dd HH:mm:ss
                'DataMatch'     => $dataMatch,         //IVR資料符合[2:符合 | 1:不符合 MS0300.CPCellNum2]，當為2 符合入已收 ；1 不符合保留未收
                'OPID'          => 'WMAPP',
            );

            $url = $this->api_url.'/IVRCALLBACK/API_01_2/';

            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );

            $result = $this->curl($curl_data);
            $result_msg = json_decode($result['d']);
            //Log::channel('ewoLog')->info('chk InstalledFinished API Reponse=='.print_r($result_msg,1));

            if($result_msg->RetCode == '133' && $workSheet != $p_worksheet2) {
                // add log
                $params['CompanyNo'] = $companyNo;
                $params['WorkSheet'] = $workSheet;
                $params['CustID'] = $custid;
                $params['UserNum'] = $p_userCode;
                $params['UserName'] = $p_userName;
                $params['EventType'] = 'InstalledFinished';
                $params['Request'] = '裝機工單完工API[error]02：'.json_encode($curl_data);
                $params['Responses'] = $result['d'];
                $a = new LogRepository();
                $a->insertLog($params);

                // ms0301.worksheet != assignsheet，送完工[SNO]參數改成第二個worksheet;
                $post_data['SNO'] = $p_worksheet2;
                $curl_data = array(
                    'url'       => $url,
                    'method'    => 'post',
                    'header'    => 'json',
                    'post_data' => $post_data,
                );

                $result = $this->curl($curl_data);
                $result_msg = json_decode($result['d']);
            }

            $params = array();
            if ($result_msg->RetCode != 0) {
                // add log
                $params['CompanyNo'] = $companyNo;
                $params['WorkSheet'] = $workSheet;
                $params['CustID'] = $custid;
                $params['UserNum'] = $p_userCode;
                $params['UserName'] = $p_userName;
                $params['EventType'] = 'InstalledFinished';
                $params['Request'] = '裝機工單完工API[error]01：'.json_encode($curl_data);
                $params['Responses'] = $result['d'];
                $a = new LogRepository();
                $a->insertLog($params);

                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);


            } else {
                // 備份MS3200
                $backUpMS3200['worksheet'] = $workSheet;
                $this->BackUpFinshMS3200($backUpMS3200);

                // 備份MS0300
                $backUpMS0300['worksheet'] = $workSheet;
                $this->BackUpFinshMS0300($backUpMS0300);

                // 備份 pdf 參數
                $parAry = array('companyNo'=>$companyNo,'workSheet'=>$workSheet);
                $this->backupFinshPDF($parAry);

                if($p_receiveType === '2' && $p_receiveMoney > 0) {
                    // 完工簡訊
                    $data = array(
                        'phoneNum' => $phoneNum,
                        'custid' => $custid,
                        'subsid' => data_get($subsidAry,'0'),
                        'worksheet' => $workSheet,
                        'companyno' => $companyNo,
                        'p_usercode' => "$p_userCode",
                        'p_username' => "$p_userName",
                        'recvtype' => $p_receiveType,
                        'recvamt' => $p_receiveMoney,
                    );
                    $this->sentFinshSMS($data);
                }


                $params['p_companyNo'] = $companyNo;
                $params['p_workSheet'] = $workSheet;
                $params['p_receiveType'] = $p_receiveType;
                $params['p_receiveMoney'] = $p_receiveMoney;
                $params['p_request'] = '裝機工單完工API[ok]：'.json_encode($curl_data);
                $params['p_columnName'] = 'finsh';
                $params['Responses'] = $result['d'];
                $params['p_userCode'] = $p_userCode;
                $params['p_userName'] = $p_userName;
                $params['p_value'] = date('Y-m-d H:i:s');
                $b = new Ewo_EventController();
                $b->reqUpdataTime($params);

                // 生成PDF
                $data = array(
                    'p_id' => data_get($request,'p_id'),
                    'p_pdf_v' => data_get($request,'p_pdf_v'),
                );
                $this->create_pdf($data);

            }

            $this->OrderRepository->updateOrderInfoList($paidData);

            $code = '0000';
            $status = '成功';
            $meg = $result_msg->RetMsg;

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();
            $params = array();
            $params['CompanyNo'] = $companyNo;
            $params['WorkSheet'] = $workSheet;
            $params['CustID'] = $custid;
            $params['UserNum'] = $p_userCode;
            $params['UserName'] = $p_userName;
            $params['EventType'] = 'InstalledFinished';
            $params['Request'] = '裝機工單完工API[error]：'.$meg;
            $params['Responses'] = 'null';
            $a = new LogRepository();
            $a->insertLog($params);

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤';
        }

        $p_companyno = data_get($request,'companyNo');
        $p_worksheet = data_get($request,'workSheet');

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => "$p_companyno-$p_worksheet;".$meg,
            'data'=>$data,
        );

        return $p_data;
    }

    //維修工單完工
    public function MaintainFinished(Request $request)
    {

        $data = array();
        try {

            $validator = Validator::make($request->all(), [
                'companyNo' => 'required',
                'workSheet'=> 'required',
                'worker'=> 'required',
                'mfCode1'=> 'required',
                'mfCode2'=> 'required',
                'dataMatch'=> 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'workSheet.required'=> '請輸入工單號碼',
                'worker.required'=> '請輸入工程人員',
                'mfCode1.required'=> '請輸入故障原因一',
                'mfCode2.required'=> '請輸入故障原因二',
                'dataMatch.required'=> '請輸入付款類別',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $companyNo = data_get($request,'companyNo');
            $workSheet = data_get($request,'workSheet');
            $worker = data_get($request,'worker');
            $MFCode1 = data_get($request,'mfCode1');
            $MFCode2 = data_get($request,'mfCode2');
            $dataMatch = data_get($request,'dataMatch');
            $p_receiveType = data_get($request,'p_receiveType');
            $p_receiveMoney = data_get($request,'p_receiveMoney');
            $custid = data_get($request,'custId');
            $p_userCode = data_get($request,'p_userCode');
            $p_userName = data_get($request,'p_userName');
            $phoneNum = data_get($request,'phoneNum');
            $subsidStr = data_get($request,'p_subsidStr');
            $p_worksheet2 = data_get($request,'p_worksheet2');
            $crmId = $this->compCode[$companyNo];

            $subsidAry = explode(',',$subsidStr);

            $paidData = array(
                'so' => $companyNo,
                'worksheet' => $workSheet,
                'worker1like' => "$p_userCode%",
            );

            //檢查簽名
            $query_data = array(
                'usercode' => $p_userCode,
                'companyno' => $companyNo,
                'worksheet' => $workSheet,
                'servicenamestr' => '0 mengineer',
            );
            $querySign = $this->checkSignFromFinsh($query_data);
            if(count($querySign) < 1 && $MFCode1 != 'K03 客戶因素退單')
            {
                throw new MyException('完工失敗，請檢查用戶/工程簽名。');
            }

            // 檢查檢點表
            // 檢查(品質參數查詢)是否存檔
            $chk252 = self::chkCMQualityforkg($companyNo,$workSheet,'CMQuery');
            if(!empty($chk252) && $MFCode1 != 'K03 客戶因素退單') {
                throw new MyException($chk252);
            }

            $chargeInfo = $this->OrderRepository->getOrderCharge($paidData,true);
            $jsonChargeInfo = json_encode($chargeInfo);

            $workSheetAry = array();
            foreach($chargeInfo as $k => $t) {
                if(in_array(data_get($t,'WorkSheet'),$workSheetAry) === false)
                    $workSheetAry[] = data_get($t,'WorkSheet');
            }

            $paidData['orderInfoList'] = $jsonChargeInfo;

            $backUpMS3200 = $this->getMS3200($companyNo,$workSheetAry);

            $backUpMS0300 = $this->getMS0300($companyNo,$workSheetAry);

            $post_data = array(
                'ProcessType'   => '6',         //固定為6
                'CrmId'         => $crmId,  //公司別
                'SNO'           => $workSheet,  //工單單號
                'WorkerEn'      => $worker,     //工程人員代碼
                'FINTIME'       => date("Y/m/d H:i:s"),     //工單完工時間, 格式 : yyyy/MM/dd HH:mm:ss
                'SignEn'        => $worker,     //簽收人員代碼
                'SignDate'      => date("Y/m/d H:i:s"),     //簽收日期，Date格式: yyyy/MM/dd HH:mm:ss
                'MFCode1'       => $MFCode1, //故障原因代碼1-[完工說明]
                'MFCode2'       => $MFCode2, //故障原因代碼2-[細項說明]
                'DataMatch'     => $dataMatch,         //IVR資料符合[2:符合 | 1:不符合 MS0300.CPCellNum2]，當為2 符合入已收 ；1 不符合保留未收
            );

            $url = $this->api_url.'/IVRCALLBACK/API_02_6/';

            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );

            $result = $this->curl($curl_data);
            $result_msg = json_decode($result['d']);
            //Log::channel('ewoLog')->info('chk MaintainFinished API Reponse=='.print_r($result_msg,1));

//            // 未測試
//            if($result_msg->RetCode == '133' && $workSheet != $p_worksheet2) {
//                // add log
//                $params['CompanyNo'] = $companyNo;
//                $params['WorkSheet'] = $workSheet;
//                $params['CustID'] = $custid;
//                $params['UserNum'] = $p_userCode;
//                $params['UserName'] = $p_userName;
//                $params['EventType'] = 'MaintainFinished';
//                $params['Request'] = '維修工單完工API[error]02：'.json_encode($curl_data).data_get($request,'p_userCode').";p_BackCause=$MFCode1;p_CleanCause=$MFCode2";
//                $params['Responses'] = $result['d'];
//                $a = new LogRepository();
//                $a->insertLog($params);
//
//                // ms0301.worksheet != assignsheet，送完工[SNO]參數改成第二個worksheet;
//                $post_data['SNO'] = $p_worksheet2;
//                $curl_data = array(
//                    'url'       => $url,
//                    'method'    => 'post',
//                    'header'    => 'json',
//                    'post_data' => $post_data,
//                );
//
//                $result = $this->curl($curl_data);
//                $result_msg = json_decode($result['d']);
//            }

            $params = array();
            if ($result_msg->RetCode != 0) {
                // add log
                $params['CompanyNo'] = $companyNo;
                $params['WorkSheet'] = $workSheet;
                $params['CustID'] = $custid;
                $params['UserNum'] = $p_userCode;
                $params['UserName'] = $p_userName;
                $params['EventType'] = 'MaintainFinished';
                $params['Request'] = '維修工單完工API[error]01：'.json_encode($curl_data).data_get($request,'p_userCode').";p_BackCause=$MFCode1;p_CleanCause=$MFCode2";
                $params['Responses'] = $result['d'];
                $a = new LogRepository();
                $a->insertLog($params);

                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);

            } else {
                // 備份MS3200
                $backUpMS3200['worksheet'] = $workSheet;
                $this->BackUpFinshMS3200($backUpMS3200);

                // 備份MS0300
                $backUpMS0300['worksheet'] = $workSheet;
                $this->BackUpFinshMS0300($backUpMS0300);

                if($p_receiveType === '2' && $p_receiveMoney > 0) {
                    // 完工簡訊
                    $data = array(
                        'phoneNum' => $phoneNum,
                        'custid' => $custid,
                        'subsid' => data_get($subsidAry, '0'),
                        'worksheet' => $workSheet,
                        'companyno' => $companyNo,
                        'p_usercode' => "$p_userCode",
                        'p_username' => "$p_userName",
                        'recvtype' => $p_receiveType,
                        'recvamt' => $p_receiveMoney,
                    );
                    $this->sentFinshSMS($data);
                }

                $params['p_companyNo'] = $companyNo;
                $params['p_workSheet'] = $workSheet;
                $params['p_receiveType'] = $p_receiveType;
                $params['p_receiveMoney'] = $p_receiveMoney;
                $params['p_request'] = '維修工單完工API[ok]：'.json_encode($curl_data).";p_BackCause=$MFCode1;p_CleanCause=$MFCode2";
                $params['p_columnName'] = 'finsh';
                $params['p_value'] = date('Y-m-d H:i:s');
                $params['Responses'] = $result['d'];
                $params['p_BackCause'] = $MFCode1;
                $params['p_CleanCause'] = $MFCode2;
                $params['p_userCode'] = $p_userCode;
                $params['p_userName'] = $p_userName;
                $b = new Ewo_EventController();
                $b->reqUpdataTime($params);

                // 生成PDF
                $data = array(
                    'p_id' => data_get($request,'p_id'),
                    'p_pdf_v' => data_get($request,'p_pdf_v'),
                );
                $this->create_pdf($data);

            }

            $this->OrderRepository->updateOrderInfoList($paidData);

            $code = '0000';
            $status = '成功';
            $meg = $result_msg->RetMsg;

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();

        }  catch (Exception $e) {
            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤';
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

        }

        $p_companyno = data_get($request,'companyNo');
        $p_worksheet = data_get($request,'workSheet');

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => "$p_companyno-$p_worksheet;".$meg,
            'data'=>$data,
        );

        return $p_data;
    }

    //拆機工單完工
    public function RemoveFinished(Request $request)
    {

        $data = array();
        try {

            $validator = Validator::make($request->all(), [
                'companyNo' => 'required',
                'workSheet'=> 'required',
                'worker'=> 'required',
                'devices'=> 'required',
                'dataMatch'=> 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'workSheet.required'=> '請輸入工單號碼',
                'worker.required'=> '請輸入工程人員',
                'devices.required'=> '請輸入設備序號',
                'dataMatch.required'=> '請輸入付款類別',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $companyNo = data_get($request,'companyNo');
            $workSheet = data_get($request,'workSheet');
            $worker = data_get($request,'worker');
            $devices = data_get($request,'devices');
            $dataMatch = data_get($request,'dataMatch');
            $p_receiveType = data_get($request,'p_receiveType');
            $p_receiveMoney = data_get($request,'p_receiveMoney');
            $custid = data_get($request,'custId');
            $p_userCode = data_get($request,'p_userCode');
            $p_userName = data_get($request,'p_userName');
            $phoneNum = data_get($request,'phoneNum');
            $subsidStr = data_get($request,'p_subsidStr');
            $p_worksheet2 = data_get($request,'p_worksheet2');
            $crmId = $this->compCode[$companyNo];

            $subsidAry = explode(',',$subsidStr);

            //檢查簽名
            $query_data = array(
                'usercode' => $p_userCode,
                'companyno' => $companyNo,
                'worksheet' => $workSheet,
                'servicenamestr' => '0 mengineer',
            );
            $querySign = $this->checkSignFromFinsh($query_data);
            if(count($querySign) < 1)
            {
                throw new MyException('完工失敗，請檢查用戶/工程簽名。');
            }

            // 檢查檢點表
            $data = array(
                'worksheet' => $workSheet,
                'companyno' => $companyNo,
            );
            $query = $this->OrderRepository->getStatistics($data);
            if($query) {
                $query = $query[0];
            } else {
                throw new MyException('完工失敗，APP找不到訂單');
            }
            if(data_get($query,'laborsafetyCheckList') < 1) {
                throw new MyException('完工失敗，請檢查(檢點表)是否存檔');
            }

            if(substr($workSheet,-1) == 'U') {
                $workSheet = substr($workSheet,0,strlen($workSheet)-1);
            }


            $paidData = array(
                'so' => $companyNo,
                'worksheet' => $workSheet,
                'worker1like' => "$p_userCode%",
            );
            $chargeInfo = $this->OrderRepository->getOrderCharge($paidData,true);
            $jsonChargeInfo = json_encode($chargeInfo);

            $workSheetAry = array();
            foreach($chargeInfo as $k => $t) {
                if(in_array(data_get($t,'WorkSheet'),$workSheetAry) === false)
                    $workSheetAry[] = data_get($t,'WorkSheet');
            }

            $paidData['orderInfoList'] = $jsonChargeInfo;

            $backUpMS3200 = $this->getMS3200($companyNo,$workSheetAry);

            $backUpMS0300 = $this->getMS0300($companyNo,$workSheetAry);

            $post_data = array(
                'ProcessType'   => '9',         //固定為9
                'CrmId'         => $crmId,  //公司別
                'SNO'           => $workSheet,  //工單單號
                'WorkerEn'      => $worker,     //工程人員代碼
                'FINTIME'       => date("Y/m/d H:i:s"),     //工單完工時間, 格式 : yyyy/MM/dd HH:mm:ss
                'SignEn'        => $worker,     //簽收人員代碼
                'SignDate'      => date("Y/m/d H:i:s"),     //簽收日期，Date格式: yyyy/MM/dd HH:mm:ss
                'ReturnDevices' => $devices, //取回設備，多設備時以逗號分隔
                'DataMatch'     => $dataMatch,         //IVR資料符合[2:符合 | 1:不符合 MS0300.CPCellNum2]，當為2 符合入已收 ；1 不符合保留未收
            );

            $url = $this->api_url.'/IVRCALLBACK/API_03_9/';

            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );

            $time = date('YmdHis');
            $result = $this->curl($curl_data);
            Log::channel('ewoLog')->info("chk RemoveFinished API $companyNo#$workSheet#$time request==".print_r($curl_data,1));
            Log::channel('ewoLog')->info("chk RemoveFinished API $companyNo#$workSheet#$time result==".json_encode($result));

            $result_msg = json_decode(data_get($result,'d'));

//            // 未測試
//            if($result_msg->RetCode == '133' && $workSheet != $p_worksheet2) {
//                // add log
//                $params['CompanyNo'] = $companyNo;
//                $params['WorkSheet'] = $workSheet;
//                $params['CustID'] = $custid;
//                $params['UserNum'] = $p_userCode;
//                $params['UserName'] = $p_userName;
//                $params['EventType'] = 'RemoveFinished';
//                $params['Request'] = '拆機工單完工API[error]02：'.json_encode($curl_data);
//                $params['Responses'] = $result['d'];
//                $a = new LogRepository();
//                $a->insertLog($params);
//
//                // ms0301.worksheet != assignsheet，送完工[SNO]參數改成第二個worksheet;
//                $post_data['SNO'] = $p_worksheet2;
//                $curl_data = array(
//                    'url'       => $url,
//                    'method'    => 'post',
//                    'header'    => 'json',
//                    'post_data' => $post_data,
//                );
//
//                $result = $this->curl($curl_data);
//                $result_msg = json_decode($result['d']);
//            }

            $params = array();
            if ($result_msg->RetCode != 0) {
                $params['CompanyNo'] = $companyNo;
                $params['WorkSheet'] = $workSheet;
                $params['CustID'] = $custid;
                $params['UserNum'] = $p_userCode;
                $params['UserName'] = $p_userName;
                $params['EventType'] = 'RemoveFinished';
                $params['Request'] = '拆機工單完工API[error]01：'.json_encode($curl_data);
                $params['Responses'] = $result['d'];
                $a = new LogRepository();
                $a->insertLog($params);

                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);

            } else {
                // 備份MS3200
                $backUpMS3200['worksheet'] = $workSheet;
                $this->BackUpFinshMS3200($backUpMS3200);

                // 備份MS0300
                $backUpMS0300['worksheet'] = $workSheet;
                $this->BackUpFinshMS0300($backUpMS0300);

                if($p_receiveType === '2' && $p_receiveMoney > 0) {
                    // 完工簡訊
                    $data = array(
                        'phoneNum' => $phoneNum,
                        'custid' => $custid,
                        'subsid' => data_get($subsidAry, '0'),
                        'worksheet' => $workSheet,
                        'companyno' => $companyNo,
                        'p_usercode' => "$p_userCode",
                        'p_username' => "$p_userName",
                        'recvtype' => $p_receiveType,
                        'recvamt' => $p_receiveMoney,
                    );
                    $this->sentFinshSMS($data);
                }

                $params['p_companyNo'] = $companyNo;
                $params['p_workSheet'] = $workSheet;
                $params['p_receiveType'] = $p_receiveType;
                $params['p_receiveMoney'] = $p_receiveMoney;
                $params['p_request'] = '拆機工單完工API[ok]：'.json_encode($curl_data);
                $params['p_columnName'] = 'finsh';
                $params['p_value'] = date('Y-m-d H:i:s');
                $params['Responses'] = $result['d'];
                $params['p_userCode'] = $p_userCode;
                $params['p_userName'] = $p_userName;
                $b = new Ewo_EventController();
                $b->reqUpdataTime($params);

                // 生成PDF
                $data = array(
                    'p_id' => data_get($request,'p_id'),
                    'p_pdf_v' => data_get($request,'p_pdf_v'),
                );
                $this->create_pdf($data);

            }

            $this->OrderRepository->updateOrderInfoList($paidData);

            $code = '0000';
            $status = '成功';
            $meg = $result_msg->RetMsg;

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();

        }  catch (Exception $e) {
            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤';
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

        }
        $p_companyno = data_get($request,'companyNo');
        $p_worksheet = data_get($request,'workSheet');

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => "$p_companyno-$p_worksheet;".$meg,
            'data'=>$data,
        );

        return $p_data;
    }

    //退單
    public function Chargeback(Request $request)
    {

        $data = array();
        try {


            $validator = Validator::make($request->all(), [
                'companyNo' => 'required',
                'workSheet'=> 'required',
                'worker'=> 'required',
                'type'=> 'required',
                'returnCode'=> 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'workSheet.required'=> '請輸入工單號碼',
                'worker.required'=> '請輸入工程人員',
                'type.required'=> '請輸入類別',
                'returnCode.required'=> '請輸入退單原因',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $companyNo = data_get($request,'companyNo');
            $workSheet = data_get($request,'workSheet');
            $worker = data_get($request,'worker');
            $type = data_get($request,'type');
            $returnCode =  data_get($request,'returnCode');
            $crmId = $this->compCode[$companyNo];

            $post_data = array(
                'ProcessType'   => $type,       //固定為 4(裝機退單) 8(維修退單) 11(拆機退單)
                'CrmId'         => $crmId,  //公司別
                'SNO'           => $workSheet,  //工單單號
                'ReturnCode'    => $returnCode,
                'WorkerEn'      => $worker,     //工程人員代碼
                'SignEn'        => $worker,     //簽收人員代碼
                'SignDate'      => date("Y/m/d H:i:s"),     //簽收日期，Date格式: yyyy/MM/dd HH:mm:ss
            );

            $url = $this->api_url.'/IVRCALLBACK/';

            switch ($type) {
                case '4': // 裝機退單
                    $url .= 'API_01_4/';
                    break;
                case '8': // 維修退單
                    $url .= 'API_02_8/';
                    break;
                case '11': // 拆機退單
                    $url .= 'API_03_11/';
                    break;

            }

            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );

            $result = $this->curl($curl_data);
            $result_msg = json_decode($result['d']);
            //Log::channel('ewoLog')->info('chk Chargeback curl_data=='.print_r($curl_data,1).';reponse=='.print_r($result_msg,1));

            $params = array();
            if ($result_msg->RetCode != 0) {
                $params['CompanyNo'] = $companyNo;
                $params['WorkSheet'] = $workSheet;
                $params['CustID'] = data_get($request,'custId');
                $params['UserNum'] = data_get($request,'p_userCode');
                $params['UserName'] = data_get($request,'p_userName');
                $params['EventType'] = 'chargeback';
                $params['Request'] = '退單API：'.json_encode($curl_data);
                $params['Responses'] = $result['d'];
                $a = new LogRepository();
                $a->insertLog($params);

                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);
            } else {
                $params['p_companyNo'] = $companyNo;
                $params['p_workSheet'] = $workSheet;
                $params['p_request'] = '退單API：'.json_encode($curl_data);
                $params['p_columnName'] = 'chargeback';
                $params['Responses'] = $result['d'];
                $params['p_value'] = data_get($request,'returnCode');
                $params['p_userCode'] = data_get($request,'p_userCode');
                $params['p_userName'] = data_get($request,'p_userName');
                $b = new Ewo_EventController();
                $b->reqUpdataTime($params);
            }


            $code = '0000';
            $status = 'OK';
            $meg = '';

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();
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

    //更換設備序號
    public function ChangeDevice(Request $request)
    {

        $data = array();
        try {

            $validator = Validator::make($request->all(), [
                'companyNo' => 'required',
                'workSheet'=> 'required',
                'custid'=> 'required',
                'subsid'=> 'required',
                'singleSN'=> 'required',
                'smartCard'=> 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'workSheet.required'=> '請輸入工單號碼',
                'custid.required'=> '請輸入住編',
                'subsid.required'=> '請輸入訂編',
                'singleSN.required'=> '請輸入設備序號',//更換的設備序號
                'smartCard.required'=> '請輸入卡號',//更換的卡號
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            }
            $p_time = date('Y-m-d H:i:s');
            $p_companyNo = data_get($request,'companyNo');
            $p_workSheet = data_get($request,'workSheet');
            $p_custId = data_get($request,'custid');
            $p_subsId = data_get($request,'subsid');
            $p_userCode = data_get($request,'p_userCode');
            $p_userName = data_get($request,'p_userName');
            $Data301 = array(
                'so'=>$p_companyNo,
                'worksheet'=>$p_workSheet,
                'custid'=>$p_custId,
                'servicename'=>['3 DSTB'],
            );
            $query_ms0301 = $this->OrderRepository->getOrderCharge($Data301,true);
            if(sizeof($query_ms0301) <= 0) {
                $error = '沒有找到符合條件';
                throw new Exception($error);
            }

            // 新增MS03Z1
            foreach($query_ms0301 as $k => $insMS03z1) {
                $insMS03z1 = (array)$insMS03z1;
                $insMS03z1['UpdateName'] = "$p_userCode $p_userName";
                $insMS03z1['UpdateTime'] = $p_time;
                $this->OrderRepository->insertMS03Z1($insMS03z1);
            }

            $data = array(
                'companyNo' => $p_companyNo,
                'custid'    => $p_custId,
                'subsid'    => $p_subsId,
                'serviceName' => ['3 DSTB'],
                'assignSheet' => $p_workSheet,
                'singleSN'  => data_get($request,'singleSN'),
                'smartCard' => data_get($request,'smartCard'),
                'p_userCode' => $p_userCode,
                'p_userName' => $p_userName,
            );
            $updateCustDevice = $this->CustomerRepository->updateCustDevice($data);

            $p_value = array('subsid'=>$p_subsId,'time'=>date('Y-m-d H:i:s'));
            $p_updataTime = array(
                'p_companyNo' => $p_companyNo,
                'p_workSheet' => $p_workSheet,
                'p_columnName' => 'deviceChk',
                'p_userCode' => $p_userCode,
                'p_userName' => $p_userName,
                'p_value' => json_encode($p_value),
            );
            $obj = New Ewo_EventController();
            $obj->reqUpdataTime($p_updataTime);

            $params = array();
            $params['CompanyNo'] = $p_companyNo;
            $params['WorkSheet'] = $p_workSheet;
            $params['CustID'] = $p_custId;
            $params['EventType'] = 'ChangeDevice';
            $params['UserNum'] = $p_userCode;
            $params['UserName'] = $p_userName;
            $params['Request'] = '更換設備：'.json_encode($data);
            $params['Responses'] = json_encode($p_value);
            $a = new LogRepository();
            $a->insertLog($params);

            $code = '0000';
            $status = 'OK';
            $meg = '';

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();
        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤';
        }

        $ret = array(
            'code' => $code,
            'status' => $status,
            'date' => $p_time,
            'meg' => $meg,
            'data'=>$data,
        );

        return $ret;
    }


    //第一層維修原因
    public function ServiceReasonFirst(Request $request)
    {

        $data = array();
        try {

            $reasonData = array(
                'services'=>array(),
            );
            $data = $this->ReasonRepository->getServiceReasonFirst($reasonData);

            $code = '0000';
            $status = 'OK';
            $meg = '';

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();
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

    //第一層維修原因
    public function ServiceReasonSecond(Request $request)
    {

        $data = array();
        try {

            $validator = Validator::make($request->all(), [
                'services' => 'required',
                'firstCode'=> 'required',
            ],
            [
                'services.required' => '請輸入類別',
                'firstCode.required'=> '請輸入代碼',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $services = data_get($request,'services');
            $firstCode = data_get($request,'firstCode');

            $reasonData = array(
                'services'=>$services,
                'firstCode'=>$firstCode,
            );

            $data = $this->ReasonRepository->getServiceReasonSecond($reasonData);

            $code = '0000';
            $status = 'OK';
            $meg = '';

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0501';
            $status = 'error';
            $meg = '資料錯誤,'.$e->getMessage();

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

    //信用卡刷卡
    public function CreditCard(Request $request)
    {

        $data = array();
        try {

            $validator = Validator::make($request->all(), [
                'creditNumber' => 'required',
                'validDate' => 'required',
                'companyNo' => 'required',
                'custId' => 'required',
                'workSheet'=> 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'creditNumber.required' => '請輸入卡號',
                'validDate.required' => '請輸入有效日期',
                'custId.required'=> '請輸入住編',
                'workSheet.required'=> '請輸入工單號碼',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $creditNumber = data_get($request,'creditNumber');
            $creditNumber = str_replace('-', '', $creditNumber);
            $validDate = data_get($request,'validDate');
            $companyNo = data_get($request,'companyNo');
            $custid = data_get($request,'custId');
            $workSheet = data_get($request,'workSheet');
            $amount = data_get($request,'amount');
            $assignSheet = data_get($request,'assignSheet');
            $p_userCode = data_get($request,'p_userCode');
            $p_userName = data_get($request,'p_userName');
            $subsidStr = data_get($request,'p_subsidStr');
            $phoneNum = data_get($request,'phoneNum');
            $subsidAry = explode(',',$subsidStr);

            $crmId = $this->compCode[$companyNo];

            // $chargeData = array(
            //     'so' => $companyNo,
            //     'worksheet' => $workSheet,
            // );

            // $chargeInfo = $this->OrderRepository->getOrderCharge($chargeData);

            $assignSheet = explode(",", $assignSheet);
            $assignSheet[] = $workSheet;
            $assignSheet = array_filter($assignSheet);
            $assignSheet = array_values($assignSheet);

            $mediabillnoStr = '';
            $itemMediabillno = array();
            $amt = 0;

            $bill = array();
            $jsonOrderBill = array();
            foreach ($assignSheet as $key => $value) {
                $whereAry = array(
                    'so' => $companyNo,
                    'worksheet' => $value,
                    'worker1' => "$p_userCode $p_userName",
                );

                // MS0301工單合計
                $chargeBill = $this->OrderRepository->getOrderCharge($whereAry);
                $jsonOrderBill['MS0301'] = $chargeBill;
                foreach ($chargeBill as $key => $itemBill) {
                    $billAmt = data_get($itemBill,'BillAmt');
                    $amt += (int)$billAmt;
                }

                // MS3200順收合計
                $orderBill = $this->OrderRepository->getOrderBill($whereAry,true);
                $jsonOrderBill['MS3200'] = $orderBill;
                foreach ($orderBill as $key => $itemBill) {
                    $crmMediabillno = data_get($itemBill,'MediumCode');
                    $itemMediabillno[] = $crmMediabillno;

                    $workRecvYN = data_get($itemBill,'WorkRecvYN');
                    if($workRecvYN === 'Y') {
                        $recvAmt = data_get($itemBill, 'RecvAmt');
                        $amt += (int)$recvAmt;
                    }
                }

            }

            $itemMediabillno = array_unique($itemMediabillno);
            $itemMediabillno = array_values($itemMediabillno);
            $mediabillnoStr = implode(",",$itemMediabillno);

            $jsonOrderBill = json_encode($jsonOrderBill);

            if ($amount != $amt) {
                $params = array();
                $params['CompanyNo'] = $companyNo;
                $params['WorkSheet'] = $workSheet;
                $params['CustID'] = $custid;
                $params['EventType'] = 'creditcard';
                $params['UserNum'] = $p_userCode;
                $params['UserName'] = $p_userName;
                $params['Request'] = "刷卡API[controller check amount=$amount <> amt=$amt == fail  OrderBill=$jsonOrderBill ]";
                $params['Responses'] = '金額錯誤';
                $a = new LogRepository();
                $a->insertLog($params);

                throw new MyException('金額錯誤');

            }


            $post_data = array(
                "CrmId" => $crmId,
                "CreditNumber" => $creditNumber,
                "ValidDate" => $validDate,
                "Authorize" => "",
                "CrmPrePay" => $amt,
                "CrmMediabillno" => $mediabillnoStr, //
                "DeviceSNo3" => str_pad($custid, 18, '0', STR_PAD_LEFT),
                "OPID" => "WMAPP"
            );
            $p_key = $post_data['DeviceSNo3'] . str_replace('/', '', $post_data['ValidDate']) . str_pad($post_data['CrmId'], 2, '0', STR_PAD_LEFT);
            $post_data['CreditNumber'] = self::aes_encrypt($post_data['CreditNumber'], $p_key);
            $post_data['Authorize'] = "";
            $post_data["PKEY"] = $p_key;

            $url = $this->api_url.'/STB/CrmPayBill/';

            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );

            $result = $this->curl($curl_data);
            $result_msg = json_decode($result['d']);
            //Log::channel('ewoLog')->info('chk CreditCard curl_data=='.print_r($curl_data,1).';reponse=='.print_r($result_msg,1));

            $params = array();
            $params['CompanyNo'] = $companyNo;
            $params['WorkSheet'] = $workSheet;
            $params['CustID'] = $custid;
            $params['EventType'] = 'creditcard';
            $params['UserNum'] = data_get($request,'p_userCode');
            $params['UserName'] = data_get($request,'p_userName');
            $params['Request'] = '刷卡API：'.json_encode($curl_data);
            $params['Responses'] = $result['d'];
            $a = new LogRepository();
            $a->insertLog($params);

            if ($result_msg->RetCode != 0) {
                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);
            }

            // 完工簡訊
            $data = array(
                'phoneNum' => $phoneNum,
                'custid' => $custid,
                'subsid' => substr($subsidStr,0,8),
                'worksheet' => $workSheet,
                'companyno' => $companyNo,
                'p_usercode' => "$p_userCode",
                'p_username' => "$p_userName",
                'recvtype' => '1',
                'recvamt' => $amt,
            );
            $this->sentFinshSMS($data);



            $code = '0000';
            $status = 'OK';
            $meg = $result_msg->RetMsg;

        }  catch (MyException $e) {
            $code = '0501';
            $status = 'error';
            $meg = $e->getMessage();
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

    //即時網路品質參數
    public function InternetQuality(Request $request)
    {

        $data = array();
        $source = '';
        try {

            $validator = Validator::make($request->all(), [
                'companyNo' => 'required',
                'subsId' => 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'subsId.required'=> '請輸入訂編',

            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $companyNo = data_get($request,'companyNo');
            $subsid = data_get($request,'subsId');

            $crmId = $this->compCode[$companyNo];


            $singleSN = array(
                'so' => $companyNo,
                'subsid' => $subsid,
            );

            $singleSNInfo = $this->OrderRepository->getOrderSingleSN($singleSN);

            $cmMac = data_get($singleSNInfo,'SingleSN');

            if(empty($cmMac) === true) {
                $p_data = array(
                    'code' => '0401',
                    'status' => 'error',
                    'date' => date("Y-m-d H:i:s"),
                    'meg' => '查無[cmMac]請確認開通完成',
                    'data'=>[],
                );
                return $p_data;
            }

            $post_data = array
            (
                "CrmId" => $crmId,
                "CrmCustId" => $subsid,
                "CmMac" => $cmMac,

            );

//            if (in_array($crmId,array('5','6','7','8','9','11','13','15')) === true) {
                $url = $this->api_cm_lg_url.'/QueryCMInfo';
//            } else {
//                $url = $this->api_cm_url;
//            }


            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );

            $result = $this->curl($curl_data);
            $result_msg = json_decode($result['d']);
            //Log::channel('ewoLog')->info('chk InternetQuality curl_data=='.print_r($curl_data,1).';reponse=='.print_r($result_msg,1)); //ddd

            // LOG
            $params = array();
            $params['CompanyNo'] = $companyNo;
            $params['WorkSheet'] = data_get($request,'workSheet');
            $params['CustID'] = data_get($request,'custId');
            $params['EventType'] = 'InternetQuality';
            $params['UserNum'] = data_get($request,'p_userCode');
            $params['UserName'] = data_get($request,'p_userName');
            $params['Request'] = '網路品質參數API：'.json_encode($curl_data);
            $params['Responses'] = '"'.$result['d'].'"';
            $a = new LogRepository();
            $a->insertLog($params);


            if (data_get($result_msg,'RetCode') != 0) {
                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);
            }

            $retData = data_get($result_msg,'RetData')??'';
            $docsIfSigQSignalNoise = data_get($retData,'DocsIfSigQSignalNoise')??'';
            $docsIfCmtsCmStatusSignalNoise = data_get($retData,'DocsIfCmtsCmStatusSignalNoise')??'';
            $docsIfDownChannelPower = data_get($retData,'DocsIfDownChannelPower')??'';
            $docsIfCmStatusTxPower = data_get($retData,'DocsIfCmStatusTxPower')??'';


            $source = $result['d'];
            $data['DocsIfSigQSignalNoise'] = $docsIfSigQSignalNoise;
            $data['DocsIfCmtsCmStatusSignalNoise'] = $docsIfCmtsCmStatusSignalNoise;
            $data['DocsIfDownChannelPower'] = $docsIfDownChannelPower;
            $data['DocsIfCmStatusTxPower'] = $docsIfCmStatusTxPower;




            $code = '0000';
            $status = 'OK';
            $meg = '';

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();
        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤';
            $source = '{"RetCode":0500,"RetMsg":"API錯誤"}';
        }

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => $meg,
            'data'=>$data,
            'source'=>$source,
        );

        return $p_data;
    }


    //RestCM 重置CM
    public function ResetCM(Request $request)
    {
        $data = array();
        try {

            $validator = Validator::make($request->all(), [
                'companyNo' => 'required',
                'subsId' => 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'subsId.required'=> '請輸入訂編',

            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $companyNo = data_get($request,'companyNo');
            $subsid = data_get($request,'subsId');

            $url = $this->api_cm_lg_url.'/ResetCM';

            $post_data = array
            (
                "CompanyNo" => $companyNo,
                "SubsID" => $subsid,
            );

            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );

            $result = $this->curl($curl_data);

            $result_msg = json_decode($result['d']);

            // LOG
            $params = array();
            $params['CompanyNo'] = $companyNo;
            $params['WorkSheet'] = data_get($request,'workSheet');
            $params['CustID'] = data_get($request,'custId');
            $params['EventType'] = 'RestCM';
            $params['UserNum'] = data_get($request,'p_userCode');
            $params['UserName'] = data_get($request,'p_userName');
            $params['Request'] = '重置CM：'.json_encode($curl_data);
            $params['Responses'] = $result['d'];
            $a = new LogRepository();
            $a->insertLog($params);

            if ($result_msg->RetCode != 0) {
                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);
            }

            $code = '0000';
            $status = '成功';
            $meg = $result_msg->RetMsg;

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();
        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤'.$e->getMessage();
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


    // RestCM 重開CM 2022-10-14停用
//    public function ReStartCM(Request $request)
//    {
//        $data = array();
//        try {
//            $validator = Validator::make($request->all(), [
//                'companyNo' => 'required',
//                'subsId' => 'required',
//            ],
//            [
//                'companyNo.required' => '請輸入公司別',
//                'subsId.required'=> '請輸入訂編',
//            ]);
//
//            if ($validator->fails()) {
//                $error = $validator->errors()->all();
//                throw new MyException($error[0]);
//            };
//
//            $companyNo = data_get($request,'companyNo');
//            $crmId = config("company.compCode.$companyNo");
//            $subsid = data_get($request,'subsId');
//
//            $whereAry = array(
//                'companyno' => $companyNo,
//                'subsid' => $subsid,
//            );
//            $query = $this->OrderRepository->getMS0200($whereAry);
//            if(count($query) < 1) {
//                throw new MyException('查無服務別');
//            }
//            $deviceSNo1 = $query[0]->SingleSN;
//
//            $url = $this->api_cm_lg_url.'/ResetDevice';
//
//            $post_data = array
//            (
//                "CrmId" => $crmId,
//                "DeviceSNo1" => $deviceSNo1,
//            );
//
//            $curl_data = array(
//                'url'       => $url,
//                'method'    => 'post',
//                'header'    => 'json',
//                'post_data' => $post_data,
//            );
//            $result = $this->curl($curl_data);
//
//            $result_msg = json_decode($result['d']);
//
//            // LOG
//            $params = array();
//            $params['CompanyNo'] = $companyNo;
//            $params['WorkSheet'] = data_get($request,'workSheet');
//            $params['CustID'] = data_get($request,'custId');
//            $params['EventType'] = 'ReStartCM';
//            $params['UserNum'] = data_get($request,'p_userCode');
//            $params['UserName'] = data_get($request,'p_userName');
//            $params['Request'] = '重新打開CM：'.json_encode($curl_data);
//            $params['Responses'] = $result['d'];
//            $a = new LogRepository();
//            $a->insertLog($params);
//
//            if ($result_msg->RetCode != 0) {
//                $error_msg = $result_msg->RetMsg;
//                throw new MyException($error_msg);
//            }
//
//            $code = '0000';
//            $status = '成功';
//            $meg = $result_msg->RetMsg;
//
//        }  catch (MyException $e) {
//            $code = '0500';
//            $status = 'error';
//            $meg = $e->getMessage();
//        }  catch (Exception $e) {
//            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
//
//            $code = '0500';
//            $status = 'error';
//            $meg = '資料錯誤'.$e->getMessage();
//        }
//
//        $p_data = array(
//            'code' => $code,
//            'status' => $status,
//            'date' => date("Y-m-d H:i:s"),
//            'meg' => $meg,
//            'data'=>$data,
//        );
//
//        return $p_data;
//    }


    // CM-Wifi重開
    public function CMWifiRestart(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make($request->all(), [
                'companyNo' => 'required',
                'subsId' => 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'subsId.required'=> '請輸入訂編',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            }

            $companyNo = data_get($request,'companyNo');
            $subsid = data_get($request,'subsId');
            $wifiType = data_get($request,'wifiType');

            $workSheet = data_get($request,'workSheet');
            $custId = data_get($request,'custId');
            $p_userCode = data_get($request,'p_userCode');
            $p_userName = data_get($request,'p_userName');
            $eventType = 'CMWifiRestart';

            $whereAry = array(
                'companyno' => $companyNo,
                'subsid' => $subsid,
            );
            $query = $this->OrderRepository->getMS0200($whereAry);
            if(count($query) < 1) {
                throw new MyException('查無服務別');
            }

            $url = $this->api_cm_lg_url.'/WifiDisable';
            $post_data = array(
                "so" => $companyNo,
                "subsid" => $subsid,
                "wifiType" => $wifiType,
                "CrmOperAtorId" => $p_userCode,
            );
            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );
//throw new MyException('test'.json_encode($curl_data));
            $result = $this->curl($curl_data);
            $result_msg = json_decode($result['d']);

            // LOG
            $params = array();
            $params['CompanyNo'] = $companyNo;
            $params['WorkSheet'] = $workSheet;
            $params['CustID'] = $custId;
            $params['EventType'] = $eventType;
            $params['UserNum'] = $p_userCode;
            $params['UserName'] = $p_userName;
            $params['Request'] = 'CM-WifiRest>>Disable：'.json_encode($curl_data);
            $params['Responses'] = $result['d'];
            $a = new LogRepository();
            $a->insertLog($params);

            $msgLog = '';
            if($result_msg->RetCode < 1) {
                $msgLog .= 'Disable=>成功；';
                $url = $this->api_cm_lg_url.'/WifiEnable';
                $curl_data = array(
                    'url'       => $url,
                    'method'    => 'post',
                    'header'    => 'json',
                    'post_data' => $post_data,
                );
                $result = $this->curl($curl_data);
                $result_msg = json_decode($result['d']);

                // LOG02
                $params = array();
                $params['CompanyNo'] = $companyNo;
                $params['WorkSheet'] = $workSheet;
                $params['CustID'] = $custId;
                $params['EventType'] = $eventType;
                $params['UserNum'] = $p_userCode;
                $params['UserName'] = $p_userName;
                $params['Request'] = 'CM-WifiRest>>Enable：'.json_encode($curl_data);
                $params['Responses'] = $result['d'];
                $a = new LogRepository();
                $a->insertLog($params);

                $msgLog .= ($result_msg->RetCode < 1)? 'Enable=>成功；' : 'Enable=>失敗；';
            } else {
                $msgLog .= 'Disable=>失敗；';
            }

            if ($result_msg->RetCode != 0) {
                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);
            }

            $code = '0000';
            $status = '成功';
            $meg = $msgLog;

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();
        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤'.$e->getMessage();
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


    // CM MAC連線資訊
    public function CMMACInfo(Request $request)
    {
        $data = array();
        try {
            $validator = Validator::make($request->all(), [
                'companyNo' => 'required',
                'subsId' => 'required',
            ],
            [
                'companyNo.required' => '請輸入公司別',
                'subsId.required'=> '請輸入訂編',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $companyNo = data_get($request,'companyNo');
            $crmId = config("company.compCode.$companyNo");
            $subsid = data_get($request,'subsId');
            $custId = data_get($request,'custId');

            $whereAry = array(
                'companyno' => $companyNo,
                'subsid' => $subsid,
            );
            $query = $this->OrderRepository->getMS0200($whereAry);
            if(count($query) < 1) {
                throw new MyException('查無服務別');
            }
            $deviceSNo1 = $query[0]->SingleSN;

            $url = $this->api_cm_lg_url.'/CheckSpeedTest';

            $post_data = array
            (
                "CrmId" => $crmId,
                "CmMac" => $deviceSNo1,
            );

            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );
            $result = $this->curl($curl_data);

            $result_msg = json_decode($result['d']);

            // LOG
            $params = array();
            $params['CompanyNo'] = $companyNo;
            $params['WorkSheet'] = data_get($request,'workSheet');
            $params['CustID'] = data_get($request,'custId');
            $params['EventType'] = 'CMMACInfo';
            $params['UserNum'] = data_get($request,'p_userCode');
            $params['UserName'] = data_get($request,'p_userName');
            $params['Request'] = 'CM MAC 連線資訊：'.json_encode($curl_data);
            $params['Responses'] = $result['d'];
            $a = new LogRepository();
            $a->insertLog($params);

            if ($result_msg->RetCode != 0) {
                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);
            }

            $data = $result_msg->RetData;
            $code = '0000';
            $status = '成功';
            $meg = $result_msg->RetMsg;

        }  catch (MyException $e) {
            $code = '0500';
            $status = 'error';
            $meg = $e->getMessage();
        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤'.$e->getMessage();
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


    // 寄送Mail[PDF]
    public function SentMailPDF(Request $request)
    {
        $data = array();

        $validator = Validator::make($request->all(), [
                'mail' => 'required',
                'companyNo' => 'required',
                'custId' => 'required',
                'workSheet' => 'required',
                'bookdate' => 'required',
                'p_userCode' => 'required',
                'p_userName' => 'required',
            ], [
                'mail.required'=> '請輸入寄送的Mail',
                'companyNo.request'=> '請輸入客編',
                'custId.request'=> '請輸入客編',
                'workSheet.request'=> '請輸入工單號碼',
                'bookdate.request'=> '請輸入預約時間',
                'p_userCode.request'=> '請輸入工程代號',
                'p_userName.request'=> '請輸入工程姓名',
            ]);

        if ($validator->fails()) {
            $error = $validator->errors()->all();
            throw new MyException($error[0]);
        };

        $mail = data_get($request, 'mail');
        $companyno = data_get($request,'companyNo');
        $worksheet = data_get($request,'workSheet');
        $custid = data_get($request,'custId');
        $bookdate = data_get($request,'bookdate');
        $p_usercode = data_get($request,'p_userCode');
        $p_username = data_get($request,'p_userName');

        // LOG
        $log_params = array();
        $log_params['CompanyNo'] = $companyno;
        $log_params['WorkSheet'] = $worksheet;
        $log_params['CustID'] = $custid;
        $log_params['EventType'] = 'sentmail';
        $log_params['UserNum'] = $p_usercode;
        $log_params['UserName'] = $p_username;
        $log_params['Request'] = '寄送郵件['.$mail.']';

        try {
            $mailContent = array();
            $fromEmail = "wm_service@homeplus.net.tw";
            $fromName = "中嘉資訊電子工單";
            $toMail = $mail;
            $subject = "中嘉服務合約";
            $file = config('order.DOCUMENT_ROOT')."/public/upload/".$custid."_".date("Ymd",strtotime($bookdate))."/".$worksheet.".pdf";;
            if(!file_exists($file))
            {
                throw new MyException('PDF附件檔不存在.','510');
            }

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

            // UPDATE SetnMail
            $wm_params = array();
            $wm_params['companyno'] = $companyno;
            $wm_params['worksheet'] = $worksheet;
            $wm_params['sentmail'] = $mail;
            $this->OrderRepository->updateSentMail($wm_params);

            // LOG
            $log_params['Responses'] = '成功';
            $a = new LogRepository();
            $a->insertLog($log_params);

            $code = '0000';
            $status = '成功';
            $meg = '信件已經寄出';

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            //LOG
            $log_params['Responses'] = '失敗；'.$e->getMessage();
            $a = new LogRepository();
            $a->insertLog($log_params);

            $code = substr('00'.$e->getCode(),-4);
            $status = 'error';
            $meg = $e->getMessage();

        }

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => $meg,
            'data' => $data,
        );

        return $p_data;
    }

    // 計算應收金額
    public function sumReceivableAMT(Request $request)
    {
        $p_time = date('Y-m-d H:i:s');
        $data = array();
        try {
                $validator = Validator::make($request->all(), [
                    'companyno' => 'required',
                    'worksheet' => 'required',
                    'usercode' => 'required',
                ], [
                    'companyNo.required' => '請輸入公司別',
                    'smartcard.required'=> '請輸入工單標號',
                    'usercode.required'=> '請輸入工程代號',
                ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $companyno = data_get($request,'companyno');
            $worksheet = data_get($request,'worksheet');
            $usercode = data_get($request,'usercode');

            $selAry = array('WorkSheet','BillAmt');
            $data = array(
                'worker1like' => $usercode.'%',
                'so' => $companyno,
                'worksheet' => $worksheet,
            );
            $qryWorksheetAry = $this->OrderRepository->getOrderCharge($data,$selAry);

            $worksheetAry = array();
            $sumamt = 0;
            foreach ($qryWorksheetAry as $k => $t) {
                $var = data_get($t,'WorkSheet');
                $amt = data_get($t,'BillAmt');
                if(in_array($var,$worksheetAry) < 1) {
                    array_push($worksheetAry,$var);
                }
                $sumamt += intval($amt);
            }

            if(sizeof($worksheetAry) < 1) {
                throw new MyException('查無訂單資料','0530');
            } else {
                $data = array(
                    'so' => $companyno,
                    'inworksheet' => $worksheetAry,
                    'workrecvyn' => 'Y',
                );
                $qry3200SUM = $this->OrderRepository->getMS3200RecvAmttSum($data);
                $RecvAmtSum = data_get($qry3200SUM[0],'RecvAmtSum');
                $data = array(
                    'sumamt' => intval($RecvAmtSum + $sumamt)
                );
            }

            $code = '0000';
            $status = 'ok';

        }  catch (MyException $e) {
            $code = '0501';
            $status = 'error';

        }

        $ret = array(
            'code' => $code,
            'status' => $status,
            'data' => $data,
            'date' => $p_time,
        );

        return $ret;
    }

    //RestDSTB 重置DSTB
    public function RebootDSTB(Request $request)
    {

        $data = array();

        $validator = Validator::make($request->all(), [
            'companyNo' => 'required',
            'smartcard' => 'required',
        ],
            [
                'companyNo.required' => '請輸入公司別',
                'smartcard.required'=> '請輸入SmartCard',

            ]);

        if ($validator->fails()) {
            $error = $validator->errors()->all();
            throw new MyException($error[0]);
        };

        $smartcard = data_get($request,'smartcard');
        $p_companyNo = data_get($request,'companyNo');
        $p_workSheet = data_get($request,'workSheet');
        $p_custId = data_get($request,'custId');
        $p_userCode = data_get($request,'p_userCode');
        $p_userName = data_get($request,'p_userName');

        // LOG
        $params = array();
        $params['CompanyNo'] = $p_companyNo;
        $params['WorkSheet'] = $p_workSheet;
        $params['CustID'] = $p_custId;
        $params['UserNum'] = $p_userCode;
        $params['UserName'] = $p_userName;

        try {


            $url = $this->api_dstb_url.'/CAS/IRDCommand';

            $post_data = array
            (
                "CrmId" => '2',
                "SmartCard" => $smartcard,
                "Command" => '12,0',
                "OPID" => 'IVR002',
            );

            $curl_data = array(
                'url'       => $url,
                'method'    => 'post',
                'header'    => 'json',
                'post_data' => $post_data,
            );

            $result = $this->curl($curl_data);

            //Log::channel('ewoLog')->info('chk rebootdstb $curl_data=='.print_r($curl_data,1));
            //Log::channel('ewoLog')->info('chk rebootdstb $result=='.print_r($result,1));

            $result_msg = json_decode($result['d']);

            // LOG
            $params['CustID'] = $p_custId;
            $params['EventType'] = 'RebootDSTB';
            $params['Request'] = '重開DSTB：'.json_encode($curl_data);
            $params['Responses'] = json_encode($result);
            $a = new LogRepository();
            $a->insertLog($params);

            if ($result_msg->RetCode != 0) {
                $error_msg = $result_msg->RetMsg;
                throw new MyException($error_msg);
            }

            $code = '0000';
            $status = '成功';
            $meg = $result_msg->RetMsg;

        }  catch (MyException $e) {
            $code = '0501';
            $status = 'error';
            $meg = $e->getMessage();


            // LOG
            $params['CustID'] = $p_custId;
            $params['EventType'] = 'RestDSTB';
            $params['Request'] = '重開DSTB：'.json_encode($curl_data);
            $params['Responses'] = '失敗；'.$e->getMessage();
            $a = new LogRepository();
            $a->insertLog($params);

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0502';
            $status = 'error';
            $meg = '資料錯誤'.$e->getMessage();

            // LOG
            $params['CustID'] = $p_custId;
            $params['EventType'] = 'RestDSTB';
            $params['Request'] = '重開DSTB：'.json_encode($curl_data);
            $params['Responses'] = '失敗；'.$e->getMessage();
            $a = new LogRepository();
            $a->insertLog($params);

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


    // 取得 Push MSG List
    public function GetPushMsg(Request $request)
    {
        $data = array();

        try {
            $validator = Validator::make($request->all(), [
                'fromtype' => 'required',
                'p_userCode' => 'required',
            ], [
                'fromtype.required' => '請輸入查詢代號',
                'p_userCode.required' => '請輸入工程代號',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $fromtype = data_get($request,'fromtype');
            $p_userCode = data_get($request,'p_userCode');
            $p_timeNow = date('Y-m-d H:i:s');
            $p_timeStart = date('Y-m-d');
            $p_timeEnd = date('Y-m-d',strtotime('+1 Day'));
            $p_companyno = data_get($request,'companyno');
            $p_worksheet = data_get($request,'worksheet');
            $p_id_str = data_get($request,'id_str');

            $query_data = array(
                'companynoin' => array('209','210','220','230','240','250','270','310','610','620','720','730'),
                'usercode' => $p_userCode,
                'timestart' => $p_timeStart,
                'timeend' => $p_timeEnd,
            );

            if($p_userCode === 'null') {
                $query_data['companyno'] = $p_companyno;
                $query_data['assignsheet'] = $p_worksheet;
            }

            // detail 1分鐘/次
            if($fromtype === 'app02') {
                $query_data['read_null'] = 'null';
                unset($query_data['timestart']);
                unset($query_data['timeend']);
            }
            $query = $this->OrderRepository->getPushMsg($query_data);

            if($fromtype === 'app') {
                $query_data['usercode'] = $p_userCode;
                $query_data['read_at'] = $p_timeNow;
                $update = $this->OrderRepository->setPushMsgRead($query_data);
            }

            if($fromtype === 'app_read') {
                $query_data['usercode'] = $p_userCode;
                $query_data['msg_id'] = explode(',',$p_id_str);
                $query_data['read_at'] = $p_timeNow;
                $update = $this->OrderRepository->setPushMsgRead($query_data);
            }


            $code = '0000';
            $status = 'OK';
            $msg = '成功';
            $data = $query;

        }  catch (MyException $e) {
            $code = '0501';
            $status = 'error';
            $msg = '資料錯誤'.$e->getMessage();

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0502';
            $status = 'error';
            $msg = '資料錯誤'.$e->getMessage();

        }

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'msg' => $msg,
            'data'=>$data,
        );

        return $p_data;
    }


    // 取得補簽名
//    public function getAddSign(Request $request)
//    {
//        $data = array();
//
//        try {
//            $validator = Validator::make($request->all(), [
//                'fromtype' => 'required',
//                'p_userCode' => 'required',
//            ], [
//                'fromtype.required' => '請輸入查詢代號',
//                'p_userCode.required' => '請輸入工程代號',
//            ]);
//
//            if ($validator->fails()) {
//                $error = $validator->errors()->all();
//                throw new MyException($error[0]);
//            };
//
//            $p_userCode = data_get($request,'p_userCode');
//
//            // 當天補簽名
//            $query_data = array(
//                'worknum'=>$p_userCode,
//                'signauthorization'=>'signauthorization',
//            );
//            $query = $this->OrderRepository->getOrderInfo($query_data,'all');
//
//            $code = '0000';
//            $status = 'OK';
//            $msg = '成功';
//            $data = $query;
//
//        }  catch (MyException $e) {
//            $code = '0501';
//            $status = 'error';
//            $msg = '資料錯誤'.$e->getMessage();
//
//        }  catch (Exception $e) {
//            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
//
//            $code = '0502';
//            $status = 'error';
//            $data = data_get($request,'p_userCode');
//            $msg = '資料錯誤'.$e->getMessage();
//
//        }
//
//        $p_data = array(
//            'code' => $code,
//            'status' => $status,
//            'date' => date("Y-m-d H:i:s"),
//            'msg' => $msg,
//            'data'=>$data,
//        );
//
//        return $p_data;
//    }

    //ChangePWD 修改密碼
    public function ChangePWD(Request $request)
    {

        $account = $userName = '';
        $data = array();
        try {

            $validator = Validator::make($request->all(), [
                'account' => 'required',
                'password_old' => 'required',
                'password_new' => 'required',
            ],
            [
                'account.required' => '請輸入App帳號',
                'password_old.required' => '請輸入原來密碼',
                'password_new.required'=> '請輸入新密碼',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $account = data_get($request,'account');
            $password_old = data_get($request,'password_old');
            $password_new = data_get($request,'password_new');

            $user_info = $this->LoginRepository->getUserInfo($account);

            if (!$user_info) {
                throw new MyException("帳號密碼錯誤",'511');
            }

            $isEnable = data_get($user_info,'IsEnable');
            if (!$isEnable) {
                throw new MyException("帳號已鎖定",'531');
            }

            // 密碼檢查
            $userName = data_get($user_info,'Username');
            $user_password = data_get($user_info,'Password');
            $password = $this->CossDecodePWD($account,$password_old);
            if ($user_password !=  $password) {
                throw new MyException("帳號密碼錯誤",'512');
            }
            $password_new = $this->CossDecodePWD($account,$password_new);


            // 更新密碼
            $data_info = array(
                'userId'    => $account,
                'password'  => $password_new,
                'upDate'    => date("Y-m-d H:i:s"),
            );

            $this->LoginRepository->changePWD($data_info);

            $code = '0000';
            $status = '成功';
            $meg = '修改成功';

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = substr('00'.$e->getCode(),-4);//;
            $status = 'error';
            $meg = '資料錯誤,'.$e->getMessage();
        }

        // LOG
        $params = array();
        $params['CompanyNo'] = '999';
        $params['WorkSheet'] = '999';
        $params['CustID'] = '999';
        $params['EventType'] = 'ChangePWD';
        $params['UserNum'] = $account;
        $params['UserName'] = $userName;
        $params['Request'] = '修改密碼';
        $params['Responses'] = $meg.';code='.$code;
        $a = new LogRepository();
        $a->insertLog($params);


        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => $meg,
            'data'=>$data,
        );

        return $p_data;
    }


    // 取得統計數據
    public function GetStatistics(Request $request)
    {
        $data = array();

        try {
            $validator = Validator::make($request->all(), [
                'p_userCode' => 'required',
            ], [
                'p_userCode.required' => '請輸入工程代號',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            }

            $p_userCode = data_get($request,'p_userCode');
            $p_timeStart = date('Y-m-d 00:00:00');
            $p_timeEnd = date('Y-m-d 23:59:59');

            $query_data = array(
                'usercode' => $p_userCode,
                'timestart' => $p_timeStart,
                'timeend' => $p_timeEnd,
                'receiveType' => '',
            );
            $query = $this->OrderRepository->getStatistics($query_data);
            $cash = 0;
            $swipe = 0;
            $cashList = $swipeList = array();
            foreach ($query as $k => $t) {
                if ($t->receiveType === '1') {
                    $swipeList[] = $t;
                    $swipe += intval($t->receiveMoney);
                } else if ($t->receiveType === '2') {
                    $cashList[] = $t;
                    $cash += intval($t->receiveMoney);
                }
            }

            $ret = [
                'cash' => $cash,
                'swipe' => $swipe,
                'cashList' => $cashList,
                'swipeList' => $swipeList,
                'timestart' => $p_timeStart,
                'timeend' => $p_timeEnd,
            ];

            $code = '0000';
            $status = 'OK';
            $meg = '成功';
            $data = $ret;

        }  catch (MyException $e) {
            $code = '0502';
            $status = 'error';
            $meg = '資料錯誤；'.$e->getMessage();

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤；'.$e->getMessage();

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



    // 更改維修金額
    public function changMaintainCost(Request $request) {
        try {

            $validator = Validator::make(
                $request->all(), [
                    'companyNo' => 'required',
                    'workSheet'=> 'required',
                    'custId'=> 'required',
                    'subsId'=> 'required',
                ],[
                    'companyNo.required' => '請輸入公司別',
                    'workSheet.required' => '請輸入工單編號',
                    'custId.required' => '請輸入客戶編號',
                    'subsId.required' => '請輸入訂單編號',
                ]
            );

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $p_companyNo = data_get($request,'companyNo');
            $p_workSheet = data_get($request,'workSheet');
            $p_subsId = data_get($request,'subsId');
            $p_custId = data_get($request,'custId');
            $p_cost = data_get($request,'cost');
            $p_userCode = data_get($request,'p_userCode');
            $p_userName = data_get($request,'p_userName');
            $p_updateTime = date('Y-m-d H:i:s');
            $p_updateName = "$p_userCode $p_userName";

            $Data300 = array(
                'so'=>$p_companyNo,
                'worksheet'=>$p_workSheet,
                'custid'=>$p_custId,
            );
            $query_ms0300 = $this->OrderRepository->getOrderWorkKind($Data300);
            $query_ms0300 = (array)$query_ms0300;
            $p_300_cost_old = $query_ms0300['TotalAmt'];

            //Log::channel('ewoLog')->info('chk1322 $query_ms0300=='.print_r($query_ms0300,1));

            $Data301 = array(
                'so'=>$p_companyNo,
                'worksheet2'=>$p_workSheet,
                'custid'=>$p_custId,
                'chargekind'=>'19',
                'chargenamein'=>['019 維修費','19991 維修服務費',           '19997 維修服務費(100)'],
                'sheetStatusIn'=>['0.預約','1.分派','1.控制','1.報竣','1.開通','2.改約'],
            );
            $query_ms0301 = $this->OrderRepository->getOrderCharge($Data301,true);
            //Log::channel('ewoLog')->info('chk 1349==sizeof($query_ms0301)=='.sizeof($query_ms0301));
            if(sizeof($query_ms0301) < 0) {
                $error = '沒有找到符合條件';
                throw new Exception($error);
            } else {
                $query_ms0301 = (array)$query_ms0301[0];
                $p_301_cost_old = $query_ms0301['BillAmt'];
            }


            $this->OrderRepository->dbBeginTransaction();

            // 新增MS03Z0
            $query_ms0300['UpdateName'] = $p_updateName;
            $query_ms0300['UpdateTime'] = $p_updateTime;
            $this->OrderRepository->insertMS03Z0($query_ms0300);

            // 新增MS03Z1
            $query_ms0301['UpdateName'] = $p_updateName;
            $query_ms0301['UpdateTime'] = $p_updateTime;
            $this->OrderRepository->insertMS03Z1($query_ms0301);

            // 更新MS0300.ToatalAmt
            $p_editCost = (intval($p_cost) - intval($query_ms0301['BillAmt']));
            $p_ms0300Cost = intval($query_ms0300['TotalAmt']) + intval($p_editCost);
            $query_ms0300['TotalAmt'] = $p_ms0300Cost;
            $query_ms0300['UpdateName'] = $p_updateName;
            $query_ms0300['UpdateTime'] = $p_updateTime;
            $this->OrderRepository->updateMs0300TotalAmt($query_ms0300);

            //更新MS0301.BillAmt
            $query_ms0301['BillAmt'] = intval($p_cost);
            $query_ms0301['UpdateName'] = $p_updateName;
            $query_ms0301['UpdateTime'] = $p_updateTime;
            $this->OrderRepository->updateMs0301BillAmt($query_ms0301);

            // 更新後查詢 MS0300
            $query_ms0300_new = $this->OrderRepository->getOrderWorkKind($Data300,['TotalAmt']);
            $query_ms0300_new = (array)$query_ms0300_new;
            $p_300_cost_new = $query_ms0300_new['TotalAmt'];

            // 更新後查詢 MS0301
            $query_ms0301_new = $this->OrderRepository->getOrderCharge($Data301,false);
            $query_ms0301_new = (array)$query_ms0301_new[0];
            $p_301_cost_new = $query_ms0301_new['BillAmt'];

            // LOG
            $params = array();
            $params['CompanyNo'] = $p_companyNo;
            $params['WorkSheet'] = $p_workSheet;
            $params['CustID'] = $p_custId;
            $params['EventType'] = 'CostModify';
            $params['UserNum'] = $p_userCode;
            $params['UserName'] = $p_userName;
            $params['Request'] = "修改費用 $p_cost, 修改前 MS0300.TotalAmt=$p_300_cost_old, MS0301.BillAmt=$p_301_cost_old;";
            $params['Responses'] = "修改後 MS0300.TotalAmt=($p_300_cost_old + ($p_editCost))=$p_300_cost_new, MS0301.BillAmt=$p_301_cost_new;";
            $a = new LogRepository();
            $a->insertLog($params);

            $this->OrderRepository->dbCommit();

            $code = '0000';
            $status = '成功';
            $meg = "修改成功".intval($p_300_cost_old).'+('.$p_editCost.')='.intval($p_300_cost_new);

        }  catch (MyException $e) {
            $this->OrderRepository->dbRolback();
            $code = '0501';
            $status = 'error';
            $meg = $e->getMessage();

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
            $this->OrderRepository->dbRolback();
            $code = '0502';
            $status = '失敗';
            $meg = '資料錯誤:'.$e->getMessage();

        }

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => $meg,
            'data'=>'',
        );

        return $p_data;
    }


    // 加購產品
    public function chargeProduct(Request $request) {
        try {

            $validator = Validator::make(
                $request->all(), [
                    'companyNo' => 'required',
                    'workSheet'=> 'required',
                    'custId'=> 'required',
                    'product'=> 'required',
                ],[
                    'companyNo.required' => '請輸入公司別',
                    'workSheet.required' => '請輸入工單編號',
                    'custId.required' => '請輸入客戶編號',
                    'product.required' => '請輸入產品',
                ]
            );

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $p_companyNo = data_get($request,'companyNo');
            $p_workSheet = data_get($request,'workSheet');
            $p_productSource = data_get($request,'product');
            $p_custId = data_get($request,'custId');
            $p_userCode = data_get($request,'p_userCode');
            $p_userName = data_get($request,'p_userName');
            $p_updateTime = date('Y-m-d H:i:s');
            $p_updateName = "$p_userCode $p_userName";

            $p_productAry = explode(',',$p_productSource);
            $p_product = $p_productAry[0];
            $p_productCost = intval($p_productAry[1]);
            $p_chargeKind = $p_productAry[2];

            $Data300 = array(
                'so'=>$p_companyNo,
                'worksheet'=>$p_workSheet,
                'custid'=>$p_custId,
            );
            $query_ms0300 = $this->OrderRepository->getOrderWorkKind($Data300);
            $query_ms0300 = (array)$query_ms0300;
            //$p_300_cost_old = $query_ms0300['TotalAmt'];

            //Log::channel('ewoLog')->info('chk1322 $query_ms0300=='.print_r($query_ms0300,1));

            $Data301 = array(
                'so'=>$p_companyNo,
                'worksheet2'=>$p_workSheet,
                'custid'=>$p_custId,
            );
            $orderByAry = [
                ['name'=>'SheetSNo','type'=>'desc']
            ];
            $query_ms0301 = $this->OrderRepository->getOrderCharge($Data301,true,$orderByAry);
            $query_ms0301 = (array)$query_ms0301[0];
            $query_ms0301_sheetsno = $query_ms0301['SheetSNo'] + 1;

            // MS0301.SheetStatus 已經完工
            if(in_array(substr($query_ms0301['SheetStatus'],0,1),['4','A'])) {
                $error = '工單已經完工';
                throw new Exception($error);
            }

            $this->OrderRepository->dbBeginTransaction();

            // 新增MS0301
            $query_ms0301['SingleSN'] = '';
            $query_ms0301['SmartCard'] = '';
            $query_ms0301['OrgChargeName'] = '';
            $query_ms0301['OrgSingleSN'] = '';
            $query_ms0301['ChargeName'] = $p_product;
            $query_ms0301['ChargeKind'] = $p_chargeKind;
            $query_ms0301['BillAmt'] = $p_productCost;
            $query_ms0301['SheetSNo'] = $query_ms0301_sheetsno;
            $query_ms0301['AssignName'] = $p_updateName;
            $query_ms0301['AssignDate'] = $p_updateTime;
            $query_ms0301['UpdateName'] = $p_updateName;
            $query_ms0301['UpdateTime'] = $p_updateTime;
            $this->OrderRepository->insertMS0301($query_ms0301);

            // 新增MS03Z0
            $query_ms0300['UpdateName'] = $p_updateName;
            $query_ms0300['UpdateTime'] = $p_updateTime;
            $this->OrderRepository->insertMS03Z0($query_ms0300);

            // 更新MS0300.ToatalAmt
            $p_ms0300Cost = intval($query_ms0300['TotalAmt']) + intval($p_productCost);
            $query_ms0300['TotalAmt'] = $p_ms0300Cost;
            $query_ms0300['UpdateName'] = $p_updateName;
            $query_ms0300['UpdateTime'] = $p_updateTime;
            $this->OrderRepository->updateMs0300TotalAmt($query_ms0300);

            // 更新後查詢 MS0300
            $query_ms0300_new = $this->OrderRepository->getOrderWorkKind($Data300,['TotalAmt']);
            $query_ms0300_new = (array)$query_ms0300_new;
            $p_300_cost_new = $query_ms0300_new['TotalAmt'];

            // 新增後查詢 MS0301
            $Data301 = array(
                'so'=>$p_companyNo,
                'worksheet2'=>$p_workSheet,
                'custid'=>$p_custId,
            );
            $query_ms0301_new = $this->OrderRepository->getOrderCharge($Data301,false);
            $query_ms0301_new = (array)$query_ms0301_new[0];
            $p_301_cost_new = $query_ms0301_new['BillAmt'];

            // LOG
            $params = array();
            $params['CompanyNo'] = $p_companyNo;
            $params['WorkSheet'] = $p_workSheet;
            $params['CustID'] = $p_custId;
            $params['EventType'] = 'ChargeProduct';
            $params['UserNum'] = $p_userCode;
            $params['UserName'] = $p_userName;
            $params['Request'] = "加購產品:".$p_product.";$$p_productCost.000";
            $params['Responses'] = "";
            $a = new LogRepository();
            $a->insertLog($params);

            $this->OrderRepository->dbCommit();

            $code = '0000';
            $status = '成功';
            $meg = "加購成功";

        }  catch (MyException $e) {
            $this->OrderRepository->dbRolback();
            $code = '0501';
            $status = 'error';
            $meg = $e->getMessage();

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
            $this->OrderRepository->dbRolback();
            $code = '0502';
            $status = '失敗';
            $meg = '資料錯誤:'.$e->getMessage();

        }

        $p_data = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => $meg,
            'data'=>'',
        );

        return $p_data;
    }


    public function curl($data)
    {
        try {
            Log::channel('curl')->info('******Curl start******');
            Log::channel('curl')->info('Curl data: '.print_r($data,true));

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $data['url']);

            if ($data['method'] == 'post') {
                curl_setopt($ch, CURLOPT_POST, true);
                $data['post_data'] = json_encode($data['post_data'],JSON_UNESCAPED_UNICODE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data['post_data']);
            }

            if ($data['header'] == 'json') {
                $header = array('Content-Type: application/json');

                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 35);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

            $result['d'] = curl_exec($ch);
            $httpCode = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            $result['verify'] = $this->checkHttpCode($httpCode);
            $result['httpCode'] = $httpCode;

            curl_close($ch);


            Log::channel('curl')->info('curl result: '.print_r($result,true));
            Log::channel('curl')->info('******Curl END******');

            return $result;


        } catch (Exception $e) {
            Log::channel('curl')->info('******Curl error******');
            Log::channel('curl')->info('error msg: '.print_r($e->getMessage(),true));
        }
    }

    private function checkHttpCode($code)
    {
        if (strpos((string)$code, '2') === false ) {
            return false;
        }

        return true;
    }

    public function getChargeRecvAmt($chargeCM,$chargeCATV,$chargeTWMBB)
    {
        $amt = 0;

        foreach ($chargeCM as $item) {
            $amount = data_get($item,'RecvAmt');
            $amt+=(int)$amount;
        }


        foreach ($chargeCATV as $item) {
            $amount = data_get($item,'RecvAmt');
            $amt+=(int)$amount;
        }

        foreach ($chargeTWMBB as $item) {
            $amount = data_get($item,'RecvAmt');
            $amt+=(int)$amount;
        }

        return $amt;

    }

    private function CossDecodePWD($account,$password)
    {
        return md5('mwuser'.$account.$password);
    }


    public function sentFinshSMS($data)
    {
        $url = config('api.CNSC_URL').'/setSMS_SendList'; //  /setSMS_SendList // setSMS_SendTest
        $phoneNum = data_get($data,'phoneNum');
        $worksheet = data_get($data,'worksheet');
        $custid = data_get($data,'custid');
        $subsid = data_get($data,'subsid');
        $companyno = data_get($data,'companyno');
        $p_usercode = data_get($data,'p_usercode');
        $p_username = data_get($data,'p_username');
        $recvtype = data_get($data,'recvtype');
        $recvamt = data_get($data,'recvamt');
        $createname = "$p_usercode $p_username";

        $recvtypeStr = ($recvtype === '2')? '現金' : '刷卡';
        $smsSTR = '中嘉寬頻|'.config("company.database.$companyno")." 提醒您，今日施工收費金額為".$recvamt."元";
        if(intval($recvamt) > 0) {
            $smsSTR .= "，付費方式為[$recvtypeStr]";
        }
        $smsSTR .= '，已於電子工單完成審閱及簽名。';

        //return 1;

        $post_data = array(
            "CellPhone" => $phoneNum,
            "SMSContent" => $smsSTR,
            "CustID" => $custid,
            "SubsID" => $subsid,
            "CreateName" => $createname, // [1234 name]
            "CompanyNo" => $companyno,
            "AttribCode" => "2003 各SO工程", //"AttribCode" => "0003 中嘉工程","0009 中嘉資訊","2003 各SO工程"
            "CallMS1030" => "Y",
            "SMSKind" => "",
            "SendType" => "",
        );
        $curl_data = array(
            'url'       => $url,
            'method'    => 'post',
            'header'    => 'json',
            'post_data' => $post_data,
        );
        $result = $this->curl($curl_data);
        $result_msg = json_decode($result['d']);

        if($result_msg->ReturnCode === 0)
            $reponses = '成功';
        elseif(empty($phoneNum))
            $reponses = '沒有手機號碼';
        else
            $reponses = '失敗;'.$result['d'];

        $params = array(
            'CompanyNo' => $companyno,
            'WorkSheet' => $worksheet,
            'CustID' => $custid,
            'UserNum' => $p_usercode,
            'UserName' => $p_username,
            'EventType' => 'finshesms',
            'Request' => "完工發送簡訊[$phoneNum]；".$smsSTR.';'.json_encode($curl_data),
            'Responses' => $reponses,
        );
        $a = new LogRepository();
        $a->insertLog($params);

    }

    public function getMS3200($companyno,$workSheetAry)
    {

        $queryAry01 = array(
            'so' => $companyno,
            'inworksheet' => $workSheetAry
        );

        $query = $this->OrderRepository->getOrderBill($queryAry01);

        $queryAry01['finshMS3200'] = json_encode($query);

        return $queryAry01;

    }

    public function getMS0300($companyno,$workSheetAry)
    {
        $queryAry01 = array(
            'so' => $companyno,
            'inworksheet' => $workSheetAry
        );

        $query = $this->OrderRepository->getMS0300($queryAry01);

        $queryAry01['finshMS0300'] = json_encode($query);

        return $queryAry01;

    }

    public function BackUpFinshMS3200($data)
    {
        $this->OrderRepository->updateFinshMS3200($data);

    }
    public function BackUpFinshMS0300($data)
    {
        $this->OrderRepository->updateFinshMS0300($data);

    }

    // 備份 PDF 參數
    public function backupFinshPDF($params)
    {
        $companyNo = data_get($params,'companyNo');
        $workSheet = data_get($params,'workSheet');

        // 查詢 pdf 參數
        $objTools = new ewoToolsController();
        $parAry = array('companyNo'=>$companyNo,'assignSheet'=>$workSheet);
        $qryPDF = $objTools->getProgramInfo($parAry);

        // 查詢 order
        $parAry02 = array('worksheet' => $workSheet, 'companyno' => $companyNo,);
        $qryOrder = $this->OrderRepository->getStatistics($parAry02);
        if($qryOrder) {
            $qryOrder = $qryOrder[0];
        } else {
            return '完工失敗，APP找不到訂單';
        }
        $dataList = data_get($qryOrder,'dataList');
        $dataListAry = json_decode($dataList,true);
        $dataListAry['backUpPDF'] = $qryPDF;
        $dataListJson = json_encode($dataListAry);

        // 備份 pdf 參數
        $parAry03 = array('so'=>$companyNo,'worksheet'=>$workSheet,'dataList'=>$dataListJson);
        $this->OrderRepository->updateDataList($parAry03);

    }

    // 先檢查 checkAuth 在檢查 EWO
    public function checkAuth20(Request $request)
    {

        $code = '0000';
        $status = 'OK';
        $msg = $data = $data2 = '';

        try {

            $validator = Validator::make(
                $request->all(), [
                        'account' => 'required',
                        'password' => 'required',
                    ], [
                        'account.required' => '請輸入公司別',
                        'password.required' => '請輸入工單號碼',
                    ]
            );

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $account = data_get($request,'account');
            $password = data_get($request,'password');

            $credentials = array("account"=>$account,"password"=>$password);
            $client = new Client();
            $result = $client->request('POST',
                "https://cnsapi.hmps.cc/api/CNS/v1/checkAuth",
                [
                    'body' => json_encode($credentials),
                    'headers' => [
                        'Content-Type'     => 'application/json',
                    ]
                ]);
            $result = json_decode($result->getBody(), true);

            if($result['code'] !== '0000') {
                $params = array(
                    'account'=>$account,
                    'password'=>$password
                );
                $data2 = $this->chkLooking($params);

            } else {
                $code = $result['code'];
                $data = $result['data'];
                $msg = $result['msg'];

            }



        }  catch (MyException $e) {
            $this->OrderRepository->dbRolback();
            $code = $e->getCode() < 1? '0501' : substr('00'.$e->getCode(),-4);
            $status = 'error';
            $msg = $e->getMessage();

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
            $this->OrderRepository->dbRolback();
            $code = '0502';
            $status = '失敗';
            $msg = '資料錯誤:'.$e->getMessage();

        }

        $result = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'msg' => $msg,
            'data'=> $data,
            'data2'=> $data2,
        );

        return Response::json($result);

    }

    public function chkLooking($params)
    {
        $account = data_get($params,'account');
        $password = data_get($params,'password');

        $obj2 = new LoginBaseRepository();
        $obj = new LoginRepository($obj2);
        $user_info = $obj->getUserInfo($account);

        if (!$user_info) {
            throw new MyException("帳號密碼錯誤",'0500');
        }

        $isEnable = data_get($user_info,'IsEnable');
        if (!$isEnable) {
            throw new MyException("帳號已鎖定",'0530');
        }

        $user_password = data_get($user_info,'Password');
        $password = $this->CossDecodePWD($account,$password);

        if ($user_password !=  $password) {
            throw new MyException("帳號密碼錯誤",'0540');
        }

        $solist = data_get($user_info,'CompanyNo');
        $solist = str_replace('"','',$solist);

        $result = array(
            'userid' => $account,
            'name' => data_get($user_info,'Username'),
            'dept' => data_get($user_info,'Dept'),
            'mobile' => data_get($user_info,'Mobile'),
            'mail' => data_get($user_info,'mail'),
	        'solist' => $solist,
	        'contractorCode' => data_get($user_info,'ContractorCode'),
	        'placeNo' => data_get($user_info,'PlaceNo'),
        );

        return $result;

    }


//     //區故查詢 2022-09-21 ewoAPIController 重建功能
//    public function getFault(Request $request) {
//
//        $code = '0000';
//        $status = 'OK';
//        $msg = $data = '';
//        try {
//            $validator = Validator::make(
//                $request->all(), [
//                'companyno' => 'required',
//                'custid' => 'required',
//            ],[
//                    'companyno.required' => '請輸入公司別',
//                    'custid.required' => '請輸入客編',
//                ]
//            );
//
//            if ($validator->fails()) {
//                $error = $validator->errors()->all();
//                throw new MyException($error[0]);
//            };
//
//            $companyno = data_get($request,'companyno');
//            $custid = data_get($request,'custid');
//            $serviceNameAry = config('order.ServiceNameList');
//            $serviceNameList = implode("','",$serviceNameAry);
//            $serviceNameList = "'$serviceNameList'";
//
//            $sql = <<<EOF
//				SELECT TOP 10 AA.CompanyNo,AA.CustID,BB.linkid ,CC.EventNo,CC.EventTime,CC.WishTime,CC.RecoverTime,CC.EventReason,CC.EventKind
//                FROM MS0200 AA WITH(NoLock)
//                INNER JOIN MS0102 BB WITH(NoLock) ON AA.CompanyNo=BB.CompanyNo AND AA.Custid=BB.Custid AND BB.Addrno='0'
//                INNER JOIN (
//                    --震江區斷 TABLE
//                    SELECT A.CompanyNo,A.EventNo ,A.EventTime ,A.EventReason ,A.EventKind ,A.WishTime ,A.RecoverTime,B.LinkID ,B.AffectList,B.AffectQty ,A.FIXYN
//                    FROM MS0031 A with(NoLock)
//                    LEFT JOIN MS0032 B with(NoLock) ON A.EventNo=B.EventNo AND A.CompanyNo=B.CompanyNo
//                    WHERE 1=1
//--                     AND A.EventTime >= Convert(varchar(10),Getdate(),111)
//--                     AND A.WishTime >= Convert(varchar(10),Getdate(),111)
//                    AND A.FIXYN='N'
//                    AND (A.RecoverTime IS NULL OR A.RecoverTime >= Convert(varchar(10),Getdate(),111))
//                ) CC ON AA.CompanyNo=CC.CompanyNo AND CHARINDEX(',' + BB.linkid + ',',',' + CC.AffectList + ',') > 0
//			WHERE 1=1
//			AND LEFT(AA.CustStatus,1) in('0','1','7','8','9','A','B')
//			AND AA.ServiceName IN ($serviceNameList)
//			AND AA.CompanyNo IN ('209','210','220','230','240','610','620','720','730','310')
//			AND AA.CompanyNo = '$companyno'
//			AND AA.CustId = '$custid'
//			GROUP BY AA.CompanyNo,AA.CustID,BB.linkid,CC.EventNo,CC.EventTime,CC.WishTime,CC.RecoverTime,CC.EventReason,CC.EventKind
//			ORDER BY CC.EventTime DESC
//            ;
//EOF;
//
////SELECT top 10 AA.CompanyNo,AA.CustID,BB.linkid 網點,CC.事件編號,CC.發生時間,CC.預訂完工時間,CC.實際完工時間,CC.原因,CC.分類
////SELECT A.CompanyNo,A.EventNo 事件編號,A.EventTime 發生時間,A.EventReason 原因,A.EventKind 分類,A.WishTime 預訂完工時間,A.RecoverTime 實際完工時間
////,B.LinkID 網點設備,B.AffectList,B.AffectQty 影響戶數,A.FIXYN
//
//
//            //$query = DB::connection('WMDB')->select($sql);
//            $query = DB::connection('COSSDBNAME')->select($sql);
//
//            $data = $query;
//
//
//
//        }  catch (MyException $e) {
//            $this->OrderRepository->dbRolback();
//            $code = $e->getCode() < 1? '0500' : substr('00'.$e->getCode(),-4);
//            $status = 'error';
//            $msg = $e->getMessage();
//
//        }  catch (Exception $e) {
//            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
//            $this->OrderRepository->dbRolback();
//            $code = '0502';
//            $status = '失敗';
//            $msg = '資料錯誤:'.$e->getMessage();
//
//        }
//
//        $ret = array(
//            'code' => $code,
//            'status' => $status,
//            'msg' => $msg,
//            'data'=> $data,
//            'date' => date("Y-m-d H:i:s"),
//        );
//
//
//        return $ret;
//    }


    public function proceedE015(Request $request)
    {

        $code = '0000';
        $status = 'OK';
        $msg = $data = '';
        try {
            $validator = Validator::make(
                $request->all(), [
                    'companyNo' => 'required',
                    'smartcard' => 'required',
                    'subsid'=> 'required',
                ],[
                    'companyNo.required' => '請輸入公司別',
                    'smartcard.required' => '請輸入smartCard',
                    'subsid.required' => '請輸入訂編',
                ]
            );

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new MyException($error[0]);
            };

            $p_companyno = data_get($request,'companyNo');
            $p_smartcard = data_get($request,'smartcard');
            $p_subsid = data_get($request,'subsid');

            $companyNo = data_get($request,'companyNo');
            $workSheet = data_get($request,'workSheet');
            $custId = data_get($request,'custId');
            $p_userCode = data_get($request,'p_userCode');
            $p_userName = data_get($request,'p_userName');


            if(empty($p_smartcard) || empty($p_subsid))
            {
                throw new MyException('SmartCart OR SubsID is Empty!','0501');
            }

            $params = array(
                'companyno' => $p_companyno
            );
            $p_temp = $this->OrderRepository->getCdCompany($params);
            $p_crmid = data_get($p_temp,'CompCode');

            $params = array(
                'companyno' => $p_companyno,
                'subsid' => $p_subsid,
            );
            $p_CA_rec = $this->OrderRepository->getCaList($params);

            $p_loop = 0; $p_CAList = '';
            foreach($p_CA_rec as $p_ca_row)
            {
                if($p_loop>0)
                    $p_CAList .= ',';
                $p_CAList .= isset($p_ca_row->CAList) ? $p_ca_row->CAList : "";
                $p_loop++;
            }

            if(empty($p_CAList))
            {
                throw new MyException('此STB沒有頻道授權組資訊');
            }

            $p_CAList = str_replace(",", ";", $p_CAList);
            $p_calist_tmp = explode(';', $p_CAList);
            $p_calist_array = array_filter(array_unique($p_calist_tmp, SORT_NUMERIC));
            $p_CAList_str = "";

            foreach($p_calist_array as $p_ca)
            {
                if(intval($p_ca)>=1000)
                    continue;
                if(empty($p_CAList_str))
                    $p_CAList_str .= $p_ca;
                else
                    $p_CAList_str .= ','.$p_ca;
            }
            $p_params = array
            (
                'CrmId' => $p_crmid,
                'SmartCard' => $p_smartcard,
                'EntiType' => '1',
                'ProdCode' => $p_CAList_str,
                'OPID' => 'checkCA.E015'
            );
            $url = "http://172.17.1.83/CNSApi/api/CAS/Entitle";
            $method = "POST";

            $client = new Client();
            $result = $client->request($method, $url,
                [
                    'body' => json_encode($p_params),
                    'headers' => [
                        'Content-Type'     => 'application/json',
                    ]
                ]);

            $p_result = json_decode($result->getBody(), true);

            $p_params['method'] = $method;
            $p_params['url'] = $url;

            $log_data = array();
            $log_data['CompanyNo'] = $companyNo;
            $log_data['WorkSheet'] = $workSheet;
            $log_data['CustID'] = $custId;
            $log_data['UserNum'] = $p_userCode;
            $log_data['UserName'] = $p_userName;
            $log_data['EventType'] = 'proceedE015';
            $log_data['Request'] = json_encode($p_params);
            $log_data['Responses'] = json_encode($p_result);
            $a = new LogRepository();
            $a->insertLog($log_data);


        }  catch (MyException $e) {
            $this->OrderRepository->dbRolback();
            $code = $e->getCode() < 1? '0500' : substr('00'.$e->getCode(),-4);
            $status = 'error';
            $msg = $e->getMessage();

        }  catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());
            $this->OrderRepository->dbRolback();
            $code = '0502';
            $status = '失敗';
            $msg = '資料錯誤:'.$e->getMessage();

        }



        $ret = array(
            'code' => $code,
            'status' => $status,
            'date' => date("Y-m-d H:i:s"),
            'meg' => $msg,
            'data'=> $data,
        );


        return $ret;

    }


    // 檢查簽名
    public function checkSignFromFinsh($data)
    {
        $serviceName = data_get($data,'servicenamestr');
        $serviceNameAry = explode(',',$serviceName);
        foreach($serviceNameAry as $t)
        {
            $a = explode(' ',$t);
            if(data_get($a,'1') === 'CATV') continue;

            $data['sign_'.data_get($a,'1')] = '';
        }

        $query = $this->OrderRepository->getStatistics($data);

        return $query;
    }


    // create pdf
    public function create_pdf($data)
    {
        $p_version = $data['p_pdf_v'];
        $p_id = $data['p_id'];
        $p_time = time();
        $url = $_SERVER['HTTP_HOST']."/api/createpdf/app/$p_version/$p_id?_=$p_time";

        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_exec($ch);
        curl_close($ch);

    }


    public function imgUpload(Request $request,$p1=null)
    {
        $code = '';
        $msg = '';
        $data = '';
        $p_time = date('Y-m-d  H:i:s');

        try {

            $validator = Validator::make($request->all(), [
                    'p_userCode' => 'required',
                    'p_userName' => 'required',
                    'p_CompanyNo' => 'required',
                    'p_CustID' => 'required',
                    'p_WorkSheet' => 'required',
                    'p_BookDate' => 'required',
                    'p_columnName' => 'required',
                    'fileName' => 'required',
                ], [
                    'p_userCode.required' => '請輸入操作人代號',
                    'p_userName.required' => '請輸入操作人名稱',
                    'p_CompanyNo.required' => '請輸入公司別',
                    'p_CustID.required' => '請輸入住編',
                    'p_WorkSheet.required' => '請輸入工單編號',
                    'p_BookDate.required' => '請輸入預約時間',
                    'p_columnName.required' => '請輸入欄位名稱',
                    'fileName.required' => '請輸入檔案名稱',
                ]
            );

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0]);
            }

            $id = data_get($request,'id');
            $usercode = data_get($request,'p_userCode');
            $username = data_get($request,'p_userName');
            $companyno = data_get($request,'p_CompanyNo');
            $custid = data_get($request,'p_CustID');
            $worksheet = data_get($request,'p_WorkSheet');
            $bookdate = data_get($request,'p_BookDate');
            $columnname = data_get($request,'p_columnName');
            $filename = data_get($request,'fileName');
            $names = data_get($request,'names');
            $source = $p1;

            // 自動建立wm_orderlist
            if(empty($id)) {
                $data = [
                    'so' => $companyno,
                    'worksheet' => $worksheet,
                    'custid' => $custid,
                ];
                $id = $this->OrderRepository->addOrderlist($data);
            }

            $directory = '';
            $url = '';

            $obj = new LogRepository();

            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {
                    $p_bookdate = date('Ymd',strtotime($bookdate));
                    $directory = public_path('upload')."/".$custid."_".$p_bookdate;
                    $url = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/upload/'.$custid.'_'.$p_bookdate.'/'.$filename.'?='.date('is');

                    if (!is_dir($directory)) {
                        mkdir($directory,0777,true);
                        chmod($directory,0777);
                    }

                    if($columnname === 'id03Photo') {
                        // 第二證件

                        // 檔名json
                        $chkMove = 'N';
                        if(empty($names)) {
                            $namesAry = array($filename);
                            $chkMove = 'Y';
                        } else {
                            $namesAry = json_decode($names,1);
                            if(!is_array($namesAry)) {
                                $namesAry = explode(',',$names);
                            }
                            if(count($namesAry) < 3) {
                                $namesAry[] = $worksheet.'_id03_'.(sizeof($namesAry)+1).'.jpg';
                                $chkMove = 'Y';
                            }
                        }

                        // 檔案移動
                        if(sizeof($namesAry) < 3 || $chkMove === 'Y') {
                            $request->file('image')->move($directory, $filename);
                        } else {
                            rename($directory.'/'.$namesAry[1],$directory.'/'.$namesAry[2]);
                            rename($directory.'/'.$namesAry[0],$directory.'/'.$namesAry[1]);
                            $request->file('image')->move($directory, $filename);
                        }

                        $p_value = json_encode($namesAry);

                    } else {
                        $request->file('image')->move($directory, $filename);
                        $p_value = $p_time;

                    }

                }
            }

			// 更新欄位時間
            $params = array();
            $params['p_id'] = $id;
            $params['p_companyNo'] = $companyno;
            $params['p_workSheet'] = $worksheet;
            $params['p_columnName'] = $columnname;
            $params['p_value'] = $p_value;
            if(in_array($columnname,array('etf_ach')) === false) {
                $obj->updateEventTime($params);
            }

            $logRequest = '圖片上傳';
            switch ($columnname) {
            case 'id03Photo':
                $logRequest = 'R1,更新第二證件';
                break;
            case 'etf_ach':
                $logRequest = 'ETF,ACH用戶簽名';
                break;
            case 'sign_ccadaf':
                $logRequest = 'ETF,CCADA用戶簽名';
                break;
            }

            // add log
            $params = array();
            $params['CompanyNo'] = $companyno;
            $params['WorkSheet'] = $worksheet;
            $params['CustID'] = $custid;
            $params['UserNum'] = $usercode;
            $params['UserName'] = $username;
            $params['EventType'] = $columnname;
            $params['Request'] = $logRequest;
            $params['Responses'] = 'OK';
            $obj->insertLog($params);

            $file_url = $directory.'/'.$filename;

            $code = '0000';
            $msg = '上傳完成;';
            $data = array(
                'url' => $url,
                'directory' => $file_url,
                'id' => $id,
            );

        } catch (Exception $e) {
            $code = (empty($e->getCode()))? '0500' : substr('000'.$e->getCode(),0,4);
            $msg = $e->getMessage();

        }

        $ret = array(
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
            'date' => $p_time,
        );

        return Response()->json($ret);

    }


    // 檢查WM，品質查測，欄位是否[空值]
    public function chkCMQualityforkg($companyNo, $workSheet, $chkType='')
    {
        // 檢查檢點表
        $data = array(
            'worksheet' => $workSheet,
            'companyno' => $companyNo,
        );
        $query = $this->OrderRepository->getStatistics($data);
        if($query) {
            $query = $query[0];
        } else {
            return '完工失敗，APP找不到訂單';
        }
        if(data_get($query,'laborsafetyCheckList') < 1) {
            return '完工失敗，請檢查(檢點表)是否存檔';
        }
        $workKind = data_get($query, 'WorkKind');
        $dataList = data_get($query, 'dataList');
        $serviceName = data_get($query, 'ServiceName');
        $serviceNameAry = json_decode($serviceName,true);

        if($chkType == 'CMQuery') {
            // 檢查品質查測
            $serviceName = data_get($query, 'ServiceName');
            $serviceNameAry = json_decode($serviceName,true);
            $serviceNameAry2 = $serviceNameAry;

            // [I]
            if(in_array('1 CATV',$serviceNameAry2))
            unset($serviceNameAry2[array_search('1 CATV',$serviceNameAry2)]);
            if(in_array('3 DSTB',$serviceNameAry2))
            unset($serviceNameAry2[array_search('3 DSTB',$serviceNameAry2)]);
            if(in_array('C HS',$serviceNameAry2))
            unset($serviceNameAry2[array_search('C HS',$serviceNameAry2)]);
            if(in_array('F CML',$serviceNameAry2))
            unset($serviceNameAry2[array_search('F CML',$serviceNameAry2)]);
            if(in_array('B FTTH',$serviceNameAry2))
            unset($serviceNameAry2[array_search('B FTTH',$serviceNameAry2)]);

            $vcmqualityforkg = data_get($query, 'cmqualityforkg');
            $vWifiTestValue = data_get($query,'WifiTestValue');
            $vdataList = data_get($query, 'dataList');

            $vdataListAry = json_decode($vdataList,true);
            $vCMNSQuery = data_get($vdataListAry,'cmnsQuery');

            if(in_array($workKind,['1 裝機','5 維修']) && count($serviceNameAry2)) {
                $chkTowI = true;
                // 同時兩個I，忽略檢查
                $serviceNameAry03 = $serviceNameAry;
                array_unshift($serviceNameAry03, 'default');;
                if(in_array('2 CM',$serviceNameAry03) && in_array('B FTTH',$serviceNameAry03))
                    $chkTowI = false;

                if(empty($vcmqualityforkg) && $chkTowI)
                    return '完工失敗，請檢查(CM品質參數)是否存檔';
//                if(empty($vCMNSQuery) && in_array($workKind,['1 裝機']))
//                    return '完工失敗，請檢查(CMNS測速)是否存檔';
                if(empty($vWifiTestValue))
                    return '完工失敗，請檢查(wifi環境檢測)是否存檔';
            }

//            if(in_array($workKind,['5 維修']) && strlen($vcmqualityforkg) < 1) {
//                return '完工失敗，請檢查(品質參數查詢)是否存檔';
//            } else if(in_array($workKind,['1 裝機']) && strlen($vcmqualityforkg) < 1 && strpos($serviceName, 'CM') > 0) {
//                return '完工失敗，請檢查(品質參數查詢)是否存檔';
//            }

//            if(date('Y-m-d') >= '2022-09-19')
//            if(in_array($workKind,['1 裝機']) && strpos($serviceName,'CM') && empty($vCMNSQueryStr))
//                return '完工失敗，請檢查(CMNS測速)是否存檔';
        }

        // 檢查 DSTB 參數紀錄
        if(in_array('3 DSTB',$serviceNameAry)) {
            $dataListAry = json_decode($dataList,true);
            $dstbTestValue = data_get($dataListAry,'dstbTestValue');

            if(0) {
                // 查詢 合併工單 subsid
                $parAry = array(
                    'companyNo' => $companyNo,
                    'assignSheet' => $workSheet,
                    'statusNotIn' => ['A.取消'],
                );
                $qryMSOrder = $this->OrderRepository->getWorksheetList($parAry);
                if($qryMSOrder) {
                    // 收集 DSTB的SubsId
                    foreach ($qryMSOrder as $k => $t) {
                        $subsId = data_get($t,'SubsId');
                        $serviceName = data_get($t,'ServiceName');
                        if($serviceName == '3 DSTB') {
                            if(!isset($dstbTestValue[$subsId])) {
                                return "完工失敗；檢查缺少[訂編:$subsId STB&ATV參數紀錄]";
                            }
                        }
                    }
                }
            }

            if(empty($dstbTestValue)) {
                return '完工失敗；檢查缺少[STB & ATV 參數紀錄]，請掃描 STB or ATV 電視QrCode，完成紀錄。';
            }
        }

        return '';
    }

}
