<?php

namespace App\Http\Controllers;

use App\Model\MS0200;
use App\Model\MS0301;
use App\Model\MS0216;
use App\Repositories\Log\LogRepository;
use App\Repositories\Order\OrderBaseRepository;
use App\Repositories\Order\OrderRepository;
use Illuminate\Support\Facades\DB;
use Validator;
use \Log;
use Session;
use Exception;

class ewoToolsController extends Controller
{
    protected $time;
    protected $objOrder;
    protected $objLog;

    public function __construct()
    {
        $this->time = date('Y-m-d H:i:s');
        $this->objOrder = new OrderRepository(new OrderBaseRepository());
        $this->objLog = new LogRepository();
    }



    /**
     * 更新，工單列表，欄位
     */
    public function upOrderlistColumn($params)
    {

        try {
            $objUpd = new LogRepository();

            // 更新欄位
            $dataAry = array(
                'p_columnName' => data_get($params,'columnName'),
                'p_value' => data_get($params,'value'),
                'p_id' => data_get($params,'id'),
            );
            $upd = $objUpd->updateEventTime($dataAry);

            $p_responses = data_get($params, 'responses');

            // 新增紀錄
            $dataAry2 = array(
                'CompanyNo' => data_get($params, 'companyNo'),
                'WorkSheet' => data_get($params, 'workSheet'),
                'CustID' => data_get($params, 'custId'),
                'UserNum' => data_get($params, 'userCode'),
                'UserName' => data_get($params, 'userName'),
                'EventType' => data_get($params, 'eventType'),
                'Request' => data_get($params, 'request'),
                'Responses' => $p_responses,
            );
            $logId = $objUpd->insertLog($dataAry2);

            $code = '0000';
            $data = $logId;

        } catch (Exception $exception) {
            $code = '0500';
            $code = empty($exception->getCode())? $code : substr('000'.$exception->getCode(),-4);
            $data = $exception->getMessage();

        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => $this->time,
        );

        return $ret;
    }


    /**
     * 插入，LOG
     */
    public function insertLog($params)
    {
        $code = '0000';
        try {
            $logdb = new LogRepository();
    //        $params = array(
    //            'CompanyNo' => '',
    //            'WorkSheet' => '',
    //            'CustID' => '',
    //            'UserNum' => '',
    //            'UserName' => '',
    //            'EventType' => '',
    //            'Request' => '',
    //            'Responses' => '',
    //        );
            $data = $logdb->insertLog($params);

        } catch (Exception $exception) {
            $code = '0500';
            $code = empty($exception->getCode())? $code : substr('000'.$exception->getCode(),-4);
            $data = $exception->getMessage();

        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => $this->time,
        );

        return $ret;
    }

    /**
     * 複製工程簽名
    */
    public function cpSignMengineer($params)
    {
        try {
            $validator = Validator::make($params, [
                'companyNo' => 'required',
                'custId' => 'required',
                'workSheet'=> 'required',
                'userCode'=> 'required',
                'userName'=> 'required',
                'bookDate'=> 'required',
                'id'=> 'required',
            ], [
                'companyNo.required' => '請輸入[公司別]',
                'custId.required'=> '請輸入[住編]',
                'workSheet.required'=> '請輸入[單號]',
                'userCode.required'=> '請輸入[工程代號]',
                'userName.required'=> '請輸入[工程名稱]',
                'bookDate.required'=> '請輸入[約工時間]',
                'id.required'=> '請輸入[id]',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0]);
            }

            $companyNo = $params['companyNo'];
            $custId = $params['custId'];
            $workSheet = $params['workSheet'];
            $userCode = $params['userCode'];
            $userName = $params['userName'];
            $id = $params['id'];
            $bookDate = $params['bookDate'];
            $bookDate02 = date('Ymd',strtotime($bookDate));
            $directory02 = $custId.'_'.$bookDate02;

            $img01 = public_path("upload/SignMengineer/SignMengineer_$userCode.jpg");
            $directory01 = public_path("upload/$directory02");
            // 建立目錄
            if (!is_dir($directory01)) {
                mkdir($directory01,0777,true);
                chmod($directory01,0777);
            }
            $img02 = public_path("upload/$directory02/sign_mengineer_$workSheet.jpg");
            $dbBase02 = new OrderBaseRepository();
            $dbObj02 = new OrderRepository($dbBase02);
//            $dbLogBase = new LogBaseRepository();
            $dbLog = new LogRepository();

            $qryParams = array(
                'id' => $id,
            );
            $query = $dbObj02->getOrderInfo($qryParams);
            $sign_mengineer = data_get($query,'sign_mengineer');

            // app,工程簽名
            if(file_exists($img01)) {
                // 尚未上傳簽名
                if(empty($sign_mengineer)) {
                    // 複製簽名
                    if (copy($img01, $img02)) {
                        // 更新簽名欄位
                        $updAry = array(
                            'p_id' => $id,
                            'p_columnName' => 'sign_mengineer',
                            'p_value' => $this->time,
                        );
                        $resultUpd = $dbLog->updateEventTime($updAry);

                        // 新增log
                        $dataAry132 = array(
                            'CompanyNo' => $companyNo,
                            'WorkSheet' => $workSheet,
                            'CustID' => $custId,
                            'UserNum' => $userCode,
                            'UserName' => $userName,
                            'EventType' => 'sign_mengineer',
                            'Request' => '工程_簽名圖片，複製APP簽名',
                            'Responses' => '',
                        );
                        $logId = $this->insertLog($dataAry132);
                        $a = '';
                    }
                }
            }

            $code = '0000';
            $data = 'ok';

        } catch (Exception $exception) {
            $code = '0500';
            $code = empty($exception->getCode())? $code : substr('000'.$exception->getCode(),-4);
            $data = $exception->getMessage();

        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => $this->time,
        );

        return $ret;
    }

    /**
     * PDF v3 套餐info
     * string companyNo
     * string assignSheet
     *
     * 查詢[I]的套餐參數
    */
    public function getProgramInfo($params)
    {
        $data = array();
        $data['backUpTable'] = [];
        $companyNo = data_get($params,'companyNo');
        $assignSheet = data_get($params,'assignSheet');

        $selAry = array(
            ['column' => 'WorkCause', 'asName' => 'MS300.'],
            ['column' => 'WorkKind', 'asName' => 'MS300.'],
            ['column' => 'CustID', 'asName' => 'MS301.'],
            ['column' => 'ServiceName', 'asName' => 'MS301.'],
            ['column' => 'PackageName', 'asName' => 'MS301.'],
            ['column' => 'ListPrice', 'asName' => 'MS301.'],
            ['column' => 'ListPrd', 'asName' => 'MS301.'],
            ['column' => 'SaleKind', 'asName' => 'MS301.'],
            ['column' => 'ChargeName', 'asName' => 'MS301.'],
            ['column' => 'BookDate', 'asName' => 'MS301.'],
            ['column' => 'ChargeKind', 'asName' => 'MS301.'],
            ['column' => 'WorkSheet', 'asName' => 'MS301.'],
        );
        $dataMS0300 = array(
            'select' => $selAry,
            'companyNo' => $companyNo,
            'assignSheet' => $assignSheet,
            'statusNotIn' => array('A.取消'),
        );
        $qryMS0300 = $this->objOrder->getWorksheetList($dataMS0300);
        if(empty($qryMS0300))
            return 'MS0300查無功單。';

        $serviceNameAry = $custIdAry = $workSheetAry = array();
        $packageName = $workCause = $aveAmt = $listPrice = $listPrd = $chargeName = $bookDate = $workKind = '';
        foreach($qryMS0300 as $k => $t) {
            $vServiceName = data_get($t,'ServiceName');
            $vSaleKind = data_get($t,'SaleKind');
            $vChargeKind = data_get($t,'ChargeKind');
            $vWorkSheet = data_get($t,'WorkSheet');
            if(in_array($vServiceName,['1 CATV','3 DSTB'])) continue;
            if(in_array($vSaleKind,['Z 退項'])) continue;
            if(!in_array($vWorkSheet,$workSheetAry) && $vWorkSheet != $assignSheet) {
                $workSheetAry[] = $vWorkSheet;
            }
            if(!in_array($vServiceName,$serviceNameAry)) {
                $serviceNameAry[] = $vServiceName;
            }
            $vCustId = data_get($t,'CustID');
            if(!in_array($vCustId,$custIdAry)) {
                $custIdAry[] = $vCustId;
            }
            if(empty($packageName)) $packageName = data_get($t,'PackageName');
            if(empty($workCause) && $vChargeKind == '50') $workCause = data_get($t,'WorkCause'); // 促變 判斷欄位
            if(empty($listPrice) && $vChargeKind == '20') $listPrice = data_get($t,'ListPrice');
            if(empty($listPrd) && $vChargeKind == '20') $listPrd = data_get($t,'ListPrd');
            if(empty($chargeName) && $vChargeKind == '20') $chargeName = data_get($t,'ChargeName');
            if(empty($bookDate)) $bookDate = data_get($t,'BookDate');
            if(empty($workKind)) $workKind = data_get($t,'WorkKind');
        }
        if(empty($serviceNameAry))
            return '工單無[I]服務';

        $custId = data_get($custIdAry,0);
        // 雙住編
        $custId02 = data_get($custIdAry,1);
        $serviceName = $serviceNameAry[0];
        // 雙[I]
        $serviceName2 = data_get($serviceNameAry,1);

        $data['custId'] = $custId;
        $data['custId02'] = $custId02;
        $data['serviceName'] = $serviceName;
        $data['serviceName2'] = $serviceName2;

        if($workCause == '促案變更') {
            $qryMS0216 = MS0216::query()
                ->where(['CompanyNo'=>$companyNo,'WorkSheet'=>$assignSheet])
                ->first();
            if(empty($qryMS0216) && isset($workSheetAry[0])) {
                $qryMS0216 = MS0216::query()
                    ->where(['CompanyNo'=>$companyNo,'WorkSheet'=>$workSheetAry[0]])
                    ->first();
            }
            $data['backUpTable'][] = array('ms0216'=>$qryMS0216);
            $nextAmt = data_get($qryMS0216,'NextAmt');
            $nextPrd = data_get($qryMS0216,'NextPrd');
            $chargeName = data_get($qryMS0216,'NewCharge');
            $packageName = data_get($qryMS0216,'NewPackage');
            $aveAmt = intval($nextAmt) / intval($nextPrd);

        } else {
            if($workKind == 'C 換機') {
                $qryMS0200 = MS0200::query()
                    ->where([
                        'CompanyNo' => $companyNo,
                        'CustID' => $custId,
                        'ServiceName' => $serviceName,
                    ])
                    ->orderBy('CustStatus')
                    ->first();
                $data['backUpTable'][] = array('ms0200'=>$qryMS0200);
                $tieStart = data_get($qryMS0200,'TieStart');
                $bookDate = $tieStart;
            }
            if(empty($listPrice) || empty($listPrd)) {
                $aveAmt = 0;
                $data['emptyListPriceORListPrd'] = 'Y';
            } else {
                $aveAmt = intval($listPrice) / intval($listPrd);
            }
        }
        $packageNameAry = explode(' ',$packageName);
        $packageNameAry01 = data_get($packageNameAry,0);
        $chargeNameAry = explode(' ',$chargeName);
        // 速率
        $data['BillItem'] = data_get($chargeNameAry,1);
        // 合約優惠價(每月)
        $data['Aveamt'] = $aveAmt;
        // 合約期始日
        $data['BookDate'] = $bookDate;

        // 連續(月)
        $aryMS0042 = array(
            'companyNo' => $companyNo,
            'packageCode' => $packageNameAry01,
        );
        $qryMS0042 = $this->objOrder->getMS0042($aryMS0042);
        $qryMS004201 = data_get($qryMS0042,'0');
        $data['backUpTable'][] = array('ms0042'=>$qryMS004201);
        $packDuration = data_get($qryMS004201,'PackDuration');
        $data['PackDuration'] = $packDuration;

        // 違約金
        $aryMS0043 = array(
            'companyNo' => $companyNo,
            'packageCode' => $packageNameAry01,
            'serviceName' => $serviceName,
            'chargeName' => $chargeName,
        );
        $qryMS0043 = $this->objOrder->getMS0043($aryMS0043);
        $qryMS0043_01 = data_get($qryMS0043,'0');
        $data['backUpTable'][] = array('ms0043'=>$qryMS0043_01);
        $penalAmt01 = intval(data_get($qryMS0043_01,'PenalAmt01'));
        $data['PenalAmt01'] = $penalAmt01;

        $ret = $data;

        return $ret;
    }

    /**
     * 將手機以及一般市話電話過濾掉不符規則電話以及添加市話區碼
     * @param string $city
     * @param string $phone
     * @return string
     */
    public static function transferPhoneNumberToVerifyFormat (string $city,string $phone)
    {
        // 如果傳入空字串，直接回傳 空字串
        if ($phone == '') {
            return '';
        }

        // 過濾特殊字元只保留 0~9
        $phone = (string)preg_replace("/[^0-9]/", "", $phone);


        // 判斷是不是手機 09開頭 + 後面為8碼符合回傳
        if (preg_match( "/^09[0-9]{8}/",$phone)) {
            return $phone;
        }
        // 總長度在7-8碼之間，添加區碼
        if (in_array(strlen($phone),[7,8])) {
            switch(true) {
                case (str_contains($city, '基隆')):
                case (str_contains($city, '台北')):
                case (str_contains($city, '臺北')):
                case (str_contains($city, '新北')):
                    $phone = '02'.$phone;
                    break;
                case (str_contains($city, '桃園')):
                    // 可能是迴龍地區
                    if (strlen($phone) == 8) {
                        $phone = '02'.$phone;
                    } else {
                        $phone = '03'.$phone;
                    }
                    break;
                case (str_contains($city, '新竹')):
                case (str_contains($city, '花蓮')):
                case (str_contains($city, '宜蘭')):
                case (str_contains($city, '苗栗')):
                    $phone = '03'.$phone;
                    break;
                case (str_contains($city, '臺中')):
                case (str_contains($city, '台中')):
                case (str_contains($city, '彰化')):
                case (str_contains($city, '南投')):
                    $phone = '04'.$phone;
                    break;
                case (str_contains($city, '嘉義')):
                case (str_contains($city, '雲林')):
                    $phone = '05'.$phone;
                    break;
                case (str_contains($city, '臺南')):
                case (str_contains($city, '台南')):
                case (str_contains($city, '澎湖')):
                    $phone = '06'.$phone;
                    break;
                case (str_contains($city, '高雄')):
                    $phone = '07'.$phone;
                    break;
                case (str_contains($city, '屏東')):
                case (str_contains($city, '臺東')):
                case (str_contains($city, '台東')):
                case (str_contains($city, '金門')):
                case (str_contains($city, '馬祖')):
                    $phone = '08'.$phone;
                    break;
            }
        }
        // 區碼以添加完畢，確認開頭為"0"且不包含"0"的長度為 8 或 9 ， 回傳 $phone 符合市話規則
        if (preg_match( "/^0[0-9]{8,9}$/",$phone)) {
            return $phone;
        }

        // 如果因為沒有對應到地區，而無法加到區碼也回傳讓現場工程人員可判讀
        if (preg_match( "/^[1-9][0-9]{6,7}$/",$phone)) {
            return $phone;
        }

        // 如果不符驗證規則的話，回傳空字串
        return '';
    }

    /**
     * companyNo 系統台
     * custId 住編
     * infoType 資料ID
     * 取 cache 資料
    */
    public function getCacheData($params)
    {
        $p_time = date('Y-m-d H:i:s');
        $companyNo = data_get($params,'companyNo');
        $custId = data_get($params,'custId');
        $infoType = data_get($params,'infoType');

        try {

            if(empty($companyNo) || empty($custId))
                throw new Exception('請確認','0566');

            $redis = app('redis.connection');
            $heash = "cache-$companyNo-$custId";
            $key = $infoType;

            // 檢查 主heash
            $chkHeash = $redis->exists($heash);
            if(!$chkHeash)
                throw new Exception("查詢[$heash]不存在",'0576');

            // 檢查 主heash 內的資料key
            $chkHeashKey = $redis->hExists($heash,$key);
            if(!$chkHeashKey && !empty($key))
                throw new Exception("查詢[$heash]內，查無[$key]",'0582');

            if(empty($infoType)) {
                // 取出 全部
                $getValue = $redis->hGetAll($heash);
                $listAry02 = array();
                foreach($getValue as $k => $t) {
                    $listAry02[$k]=json_decode($t,true);
                }
                $result = ($listAry02);

            }
            else {
                $getValue = $redis->hGet($heash, $key);
                $result = json_decode($getValue,true);
            }

            $data = $result;
            $code = '0000';

        } catch (Exception $e) {
            $code = empty($e->getCode())? '0500' : substr('000'.$e->getCode(),-4);
            $data = $e->getMessage();
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => $p_time,
        );

        return $ret;
    }

    /**
     * 取得 PDF 參數 pdf_v terms
     * @String bookDate
     *
    */
    public function getPDFParams($params){
        $bookDate = data_get($params, 'bookDate');
        $bookDate02 = date('Ymd',strtotime($bookDate));
        $workKind = data_get($params,'workKind');

        if(!in_array($workKind,config('order.workKindIns'))) {
            $pdf_v = 'v2';
            $terms = '';

        } else
            if($bookDate02 < '20221101') {
                $pdf_v = 'v2';
                $terms = '';

            } else if ($bookDate02 >= '20221101' && $bookDate02 < '20230208') {
                $pdf_v = 'v3';
                $terms = '20221101';

            } else if ($bookDate02 >= '20230208' && $bookDate02 < '20230503') {
                $pdf_v = 'v3';
                $terms = '20230208';

            } else if ($bookDate02 >= '20230503') {
                $pdf_v = 'v3';
                $terms = '20230503';
            } else {
                $pdf_v = 'v2';
                $terms = '';

            }

        $ret = array($pdf_v,$terms);

        return $ret;
    }


    /**
     * 查詢 oets 清單
     * String CustId
     *
    */
    public function qryOETSLabel01($params) {
        $companyNo = data_get($params,'companyNo');
        $custId = data_get($params,'custId');

        if(empty($companyNo) || empty($custId)) {
            return '請確認參數';
        }

        $sql = <<<SQL
SELECT DISTINCT TOP 99
a.custid,b.label,a.created_at
FROM oets_case_item a,oets_options b
WHERE 1=1
and a.companyNo = '$companyNo'
and a.custId = '$custId'
AND a.level01 IN ('51','306')
AND a.status NOT IN ('60')
AND a.custId IS NOT NULL
AND a.created_at >= CONVERT(varchar, DATEADD(MONTH, -6, GETDATE()),120)
AND a.level01 = b.id
AND b.id IN ('51','306')
ORDER BY a.created_at DESC
;
SQL;
        $query = DB::connection('R1DB')->select($sql);

        if(empty($query))
            return [];

        $ret = (array)$query;

        return $ret;

    }


    /**
     * 查詢 滿意度 6個月 內 不滿意統計
     * String companyNo
     * String custId
    */
    public function qryDissatisfied($params) {
        $companyNo = data_get($params, 'companyNo');
        $custId = data_get($params, 'custId');

        if(empty($companyNo) || empty($custId))
            return '請確認參數';

        $sql = <<<SQL
SELECT DISTINCT
custid,q2,q3,q4,a.createtime
FROM qs_dissatisfied_result a
WHERE 1=1
AND createtime >= CONVERT(varchar, DATEADD(MONTH, -6, GETDATE()),120)
AND so = '$companyNo'
AND custid = '$custId'
;
SQL;
        $query = DB::connection('R1DB')->select($sql);

        $countAry = array();
        if(empty($query))
            return $countAry;

        $countAry['統計筆數'] = 0;
        $countAry['施工準時'] = 0;
        $countAry['服裝儀容'] = 0;
        $countAry['專業度'] = 0;
        foreach($query as $k => $t) {
            $q2 = data_get($t,'q2');
            $q3 = data_get($t,'q3');
            $q4 = data_get($t,'q4');

            $countAry['統計筆數'] += 1;

            if($q2 != '1')
                $countAry['施工準時'] += 1;
            if($q3 != '1')
                $countAry['服裝儀容'] += 1;
            if($q4 != '1')
                $countAry['專業度'] += 1;
        }

        $ret = $countAry;

        return $ret;
    }


}
