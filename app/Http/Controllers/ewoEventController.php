<?php
namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;

use App\Repositories\Log\LogRepository;
use App\Repositories\Log\LogBaseRepository;

class ewoEventController extends Controller
{

    public function __construct(
        LogRepository $LogRepository
    )
    {
        $this->LogRepository = $LogRepository;
    }

    public function eventUpdataTime(Request $req)
    {
        error_log('eventUpdataTime[ewoEventController]');
        //return csrf_token();
        //return $req->json()->all();
        $params = $req->json()->all();
        $params['p_value'] = $params['p_value']?? date('Y-m-d H:i:s');
        error_log(print_r($params));
        $ret = $this->LogRepository->updateEventTime($params);

        return Response()->json(array("code"=>"000","data"=>$ret));
    }

    public function insertLog($params)
    {

    }

}
