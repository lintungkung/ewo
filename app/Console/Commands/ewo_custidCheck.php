<?php

namespace App\Console\Commands;

use App\Console\BaseCommand;
use App\Repositories\Log\LogRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderBaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

use App\Api\Controllers\PDF_v1;
use App\Api\Controllers\PDF_v2;
use App\Api\Controllers\PDF_v3;

class ewo_custidCheck extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'EWO:checkCusetID';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '產生PDF';

    private $OrderRepository;
    private $LogRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        ini_set('memory_limit', '2G');

        $base = new OrderBaseRepository();
        $this->OrderRepository = new OrderRepository($base);
        $this->LogRepository = new LogRepository();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $p_startTime = date('Y-m-d 00:00:00',strtotime('-1 day'));
        $p_endTime = date('Y-m-d 23:59:59',strtotime('-1 day'));

        $start = Carbon::now();
        $this->info('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
        $this->info('檢查[住編] Start '.$start);
        $this->info('區間'.$p_startTime.'~'.$p_endTime);
        $this->info('');

        $data = array(
            'bookdate_s' => $p_startTime,
            'bookdate_e' => $p_endTime,
            'workkind' => ['6 移機','C 換機','2 復機'],
            'sheetStatusIn' => ['4 結案','4.結款'],

//            'assignsheet' => 'A2022110002933',
        );
        $query = $this->OrderRepository->getOrderList($data);
        $workerExistAry = array();
        foreach ($query as $key => $item) {
            $v_id = data_get($item,'Id');
            $v_pdfv = data_get($item,'pdf_v');
            $v_companyNo = data_get($item,'CompanyNo');
            $v_worKkind = data_get($item,'WorkKind');
            $v_assignSheet = data_get($item,'AssignSheet');
            $v_custID = data_get($item,'CustID');
            $v_wmCustID = data_get($item,'wmCustID');
            $v_bookDate = data_get($item,'BookDate');
            $v_bookDateDay = date('Ymd',strtotime($v_bookDate));
            $v_directoryName = $v_custID.'_'.$v_bookDateDay;
            $v_directoryUrl = public_path("upload/$v_directoryName");
            $v_directoryNameWM = $v_wmCustID.'_'.$v_bookDateDay;
            $v_directoryUrlWM = public_path("upload/$v_directoryNameWM");

            // 沒透過APP操作，跳過
            if(empty($v_id)) continue;

            // 住編沒變動，跳過
            if($v_custID == $v_wmCustID) continue;

            // 重複，跳過
            if(isset($workerExistAry[$v_companyNo])
                && array_search($v_assignSheet,$workerExistAry[$v_companyNo]) > -1)
                continue;
            else
                $workerExistAry[$v_companyNo][] = $v_assignSheet;
                //array_push($workerExistAry,"$v_companyNo-$v_assignSheet");

            $info01 = "type:$v_worKkind;id=$v_id;pdf=$v_pdfv;COSSDB CustID=$v_custID; WMDB CustID=$v_wmCustID;";
            $this->info("$info01 調整中..");

            if(!file_exists($v_directoryUrl)) {
                mkdir($v_directoryUrl,0777,true);
                chmod($v_directoryUrl,0777);
                $this->info("建立目錄：$v_directoryUrl");
            }

            // 搬移檔案
            $command = "cp $v_directoryUrlWM/* $v_directoryUrl";
            $cmd = exec($command);
            $this->info("搬移檔案 command=$command; \n result=".print_r($cmd,1).';');

            // 更新[PDF]CustId;
            $pdf_query = $this->OrderRepository->getOrderPdfInfo($v_id);
            $pdf_data = data_get($pdf_query,'Data');
            $pdf_dataAry = json_decode($pdf_data,1);
            $pdf_dataAry['custid'] = $v_custID;
            $pdf_dataJson = json_encode($pdf_dataAry);
            $pdfIns = array(
                'CompanyNo' => $v_companyNo,
                'WorkSheet' => $v_assignSheet,
                'Version' => $v_pdfv,
                'AssegnUser' => 'system 排程',
                'Data' => $pdf_dataJson,
                'orderListId' => $v_id,
            );
            $this->OrderRepository->insertOrderPdfInfo($pdfIns);

            // 更新[wm_orderlist]CustId
            $updData = array(
                'p_id' => $v_id,
                'p_columnName' => 'CustID',
                'p_value' => $v_custID,
            );
            $this->LogRepository->updateEventTime($updData);

            // 新增Log
            $params['CompanyNo'] = $v_companyNo;
            $params['WorkSheet'] = $v_assignSheet;
            $params['CustID'] = $v_custID;
            $params['UserNum'] = 'system';
            $params['UserName'] = '排程';
            $params['EventType'] = 'sysUpdCustId';
            $params['Request'] = '更新[CustID]';
            $params['Responses'] = $info01;
            $this->LogRepository->insertLog($params);

            $this->info("\n\n\n");
        }

        $end = Carbon::now();
        $this->info('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
        $this->info('結束 end='.$end);
    }

}



