<?php

namespace App\Console\Commands;

use App\Admin\coss\Database\ms00431;
use App\Console\BaseCommand;
use App\Model\wm_orderlist;
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

class ewo_cretePDF extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'EWO:cretePDF {limit=all} {startDate=0} {endDate=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '產生PDF';

    private $OrderRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        ini_set('memory_limit', '2G');

        $base = new OrderBaseRepository();
        $db = new OrderRepository($base);
        $this->OrderRepository = $db;

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $k_StartDate = $this->argument('startDate');
        $k_EndDate = $this->argument('endDate');
        $k_limit = $this->argument('limit');
        $p_time = date('Y-m-d H:i:s');

        if($k_StartDate < 1 || $k_EndDate < 1) {
            $p_startTime = date('Y-m-d 00:00:00',strtotime('-1 day'));
            $p_endTime = date('Y-m-d 23:59:59',strtotime('-1 day'));

//            $p_startTime = '2022-01-13 10:00';
//            $p_endTime = '2022-01-13 18:00';

        } else {
            $k_StartDate = str_replace('_',' ',$k_StartDate);
            $k_EndDate = str_replace('_',' ',$k_EndDate);

            $p_startTime = Carbon::parse($k_StartDate);
            $p_endTime = Carbon::parse($k_EndDate);

        }

        $start = Carbon::now();
        $this->info('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
        $this->info('cretePDF Start '.$start);
        $this->info($p_startTime.'~'.$p_endTime);


        $query = wm_orderlist::query()
            ->select('*')
            ->whereBetween('BookDate',["$p_startTime","$p_endTime"])
            ->whereNotNull('finsh')
            ->whereNotNull('sign_mcust')
            ->whereNotNull('sign_mengineer')
            ->where(function ($where) {
                $where->OrWhereNull('pdf');
                $where->OrWhereRaw('CONVERT(VARCHAR,pdf) < CONVERT(VARCHAR,finsh) ');
                $where->OrWhereRaw('CONVERT(VARCHAR,pdf) < CONVERT(VARCHAR,sign_mcust) ');
                $where->OrWhereRaw('CONVERT(VARCHAR,pdf) < CONVERT(VARCHAR,sign_mengineer) ');
            });
        if($k_limit != 'all')
            $query = $query->limit(intval($k_limit));
//            ->toSql()
        $query = $query->get();

        $today = Carbon::now();
        $this->info('today='.$today);

        $pdf_v3 = new PDF_v3($this->OrderRepository);
        $pdf_v2 = new PDF_v2($this->OrderRepository);
        foreach ($query as $key => $item) {
            $this->info('~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~');
            $v_id = data_get($item,'Id');
            $v_pdfv = data_get($item,'pdf_v');
            $v_workKind = data_get($item,'WorkKind');
            $this->info($v_id.'#'.$v_pdfv);
            $insWorkKindAry = array_merge(['99'],config('order.workKindIns'));

            // 裝機類 && PDF版本[v3]
            if(in_array($v_workKind,$insWorkKindAry) && in_array($v_pdfv,['v3','v3fet'])) {
                $params = array('cmd'=>'Y');
                $result = $pdf_v3->createPDF('',$v_pdfv,$v_id,$params);
            } else {
                $result = $pdf_v2->createPDF('',$v_pdfv,$v_id);
            }

            $this->info(print_r($result,1));

            $code = data_get($result,'code');
            if($code === '0000') {
                wm_orderlist::where('Id', $v_id)
                    ->update(['pdf' => $p_time]);

//                $sql = <<<EOF
//                    UPDATE wm_orderlist
//                        SET pdf = '{$p_time}'
//                    FROM wm_orderlist AS aa WITH(nolock)
//                    WHERE 1=1
//                        AND aa.Id = '{$v_id}'
//                    ;
//EOF;
//                $this->info('upd sql='.$sql);
//                $update = DB::connection('WMDB')->update($sql);

            }

        }

        $end = Carbon::now();
        $this->info('end='.$end);

    }

}




// error_log(print_r($response,true));

/*
$url = 'http://ewo.test/api/createpdf/v1/';

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => $url.'79006',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
*/
