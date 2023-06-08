<?php

namespace App\Console\Commands;

use App\Console\BaseCommand;
use App\Model\MSCNS09;
use App\Repositories\Order\OrderBaseRepository;
use App\Repositories\Order\OrderRepository;

class ewo_createCache extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'EWO:creatCache01';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '生成cache資料 維修 alertInfo';

    private $OrderRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        ini_set('memory_limit', '2G');

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $p_time = date('Y-m-d H:i:s');

        $redis = app('redis.connection');
        $heash = 'cache-220-15003254';
        $key = 'alertInfo';
        $sec = 86400*3;

        // D+3 CustId List
        $objOrderBase = new OrderBaseRepository();
        $objOrder = new OrderRepository($objOrderBase);
        $data = array(
            'statusNotIn' => array('A.取消'),
            'workKindIn' => array('5 維修'),
            'bookDateS' => date('Y-m-d 00:00:00'),
            'bookDateE' => date('Y-m-d 00:00:00',strtotime('+3 day')),
            'select' => array(
                ['column' => 'CompanyNo','asName' => 'MS301.'],
                ['column' => 'CustID','asName' => 'MS301.'],
            ),
            'orderBy' => array(
                ['name'=>'CompanyNo','type'=>'desc','asName'=>'MS301.'],
            ),
            'groupBy' => array('MS301.CompanyNo','MS301.CustID'),
        );
        $orderList = $objOrder->getWorksheetList($data);

        // 新增 空的
        foreach($orderList as $t) {
            $companyNo = data_get($t,'CompanyNo');
            $custId = data_get($t,'CustID');
            $heash = "cache-$companyNo-$custId";
            $redis->hSet($heash,$key,'');
            $redis->expire($heash,$sec);

            $this->info("新增空的 $heash $key");
        }

        // 查詢 資料
        $qryMSCNS09 = MSCNS09::query()
            ->select('Content','CustID','CompanyNo')
            ->where('KindNo', 'A06 工程APP使用')
            ->whereIn('TypeNo', ['A060101','A060102','A060103'])
            ->where('StartDate','<=',$p_time)
            ->where('EndDate','>=',$p_time)
            ->orderBy('CompanyNo')
            ->orderBy('CustID')
            ->groupBy('CompanyNo','CustID','Content')
            ->get();

        // 整理資料
        $listAry = array();
        foreach($qryMSCNS09 as $k => $t) {
            $companyNo = $t->CompanyNo;
            $custId = $t->CustID;
            $conTent = $t->Content;
            $listAry[$companyNo][$custId][] = $conTent;
        }

        // 寫入 redis
        foreach ($listAry as $companyNo => $t) {
            foreach($t as $custId => $value) {
                $heash = "cache-$companyNo-$custId";
                $valueStr = json_encode($value);
                $redis->hSet($heash,$key,$valueStr);
                $redis->expire($heash,$sec);

                $this->info("$heash $key");
                $this->info($valueStr);
                $a = '';
            }
        }

    }


}


