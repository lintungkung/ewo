<?php

namespace App\Console\Commands;

use App\Admin\coss\Database\ms00431;
use App\Console\BaseCommand;
use App\Http\Controllers\ewoToolsController;
use App\Model\MSCNS09;
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

class jobtest extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'JOB:jobtest {so=all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'test job';

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

        $obj = new ewoToolsController();
        $params = array(
            'companyNo' => '209',
            'custId' => '65004827',
        );
        $result = $obj->qryDissatisfied($params);

        $p_time = date('Y-m-d H:i:s');
        $qryMSCNS09 = MSCNS09::query()
            ->select('Content','CustID','CompanyNo')
            ->where([
                'KindNo' => 'A06 工程APP使用',
                'TypeNo' => 'A060101',
            ])
            ->where('StartDate','<=',$p_time)
            ->where('EndDate','>=',$p_time)
            ->orderBy('CompanyNo')
            ->orderBy('CustID')
            ->groupBy('CompanyNo','CustID','Content')
            ->get();
        $listAry = array();
        if(0)
        foreach($qryMSCNS09 as $k => $t) {
            $companyNo = $t->CompanyNo;
            $custId = $t->CustID;
            $conTent = $t->Content;
            $listAry[$companyNo][$custId] = $conTent;
        }

        $redis = app('redis.connection');
        $heash = 'Cache_220_15003254';
        $key = 'alertInfo';
        $sec = 86400;

        $a = array('a1','b2');
        $listJson = json_encode($a);
        $redis->Hset($heash,$key,$listJson);
        $redis->expire($heash,$sec);

        $ttl = $redis->ttl($heash);
        $result = $redis->hGet($heash,$key);

        $heash = 'cache-730-60782177';

//        $heash .= 'x';
        $key .= 'x';

        $result = $redis->hGetAll($heash);
        $listAry02 = array();
        foreach($result as $k => $t) {
            $listAry02[$k]=json_decode($t,true);
        }
        $listStr = json_encode($listAry02);

        $chkHeash = $redis->exists($heash);
        $chkHeashKey = $redis->hExists($heash,$key);
        $a = '';

        $a = '';

    }

}



