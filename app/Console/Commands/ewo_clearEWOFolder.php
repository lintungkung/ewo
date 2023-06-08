<?php

namespace App\Console\Commands;

use App\Console\BaseCommand;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Order\OrderBaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;

use App\Api\Controllers\PDF_v1;
use App\Api\Controllers\PDF_v2;

class ewo_clearEWOFolder extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'EWO:clearEWOFolder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '清除[ewo Folder]';

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
        $folder = 'EWO_Folder';
        $path = storage_path('app/'.$folder);

        if(is_dir($path)) {
            $this->deleteDirectory($path);
        }

        mkdir($path);

        if(is_dir($path)) {
            $this->info($path.';目錄重整完成');
        }

    }


    // 清除目錄[內含檔案]
    public function deleteDirectory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }

        }

        return rmdir($dir);
    }


}


