<?php

namespace App\Api\Controllers;

use App\Repositories\Log\LogRepository;
use Validator;
use \Log;
use Exception;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Api\Controllers\Controller;
use App\Http\Controllers\MyException;

use App\Repositories\Order\OrderRepository;



class pdfController extends Controller
{

    public function __construct(OrderRepository $OrderRepository)
    {
        $this->OrderRepository = $OrderRepository;
    }


    public function index(Request $request, $source, $version, $id)
    {
        try {


            if (empty($version) || empty($id)) {
                throw new Exception("資料錯誤", 1);
            }

            $className = __NAMESPACE__."\\".'PDF_'.$version;

            switch ($version) {
                case 'v1':
                case 'v2':
                    $pdf = new $className($this->OrderRepository);
                    $p_data = $pdf->createPDF($source,$version,$id);
                    break;
                case 'v3':
                    $pdf = new $className($this->OrderRepository);
                    $params = $request->all();
                    $p_data = $pdf->createPDF($source,$version,$id,$params);
                    break;
                case 'v3fet':
                    $pdf = new $className($this->OrderRepository);
                    $params = $request->all();
                    $p_data = $pdf->createPDF($source,$version,$id,$params);
                    break;
                case 'ach':
                    $pdf = new $className($this->OrderRepository);
                    $p_data = $pdf->createPDF($source,$version,$id);
                    break;
                default:
                    throw new Exception("資料錯誤", 1);
                    break;
            }



        } catch (Exception $e) {
            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤';
            $data = '';

            $p_data = array(
                'code' => $code,
                'status' => $status,
                'date' => date("Y-m-d H:i:s"),
                'meg' => $meg,
                'data'=>$data,
            );
        }

        return $p_data;
    }

    public function UpdatePdfInfo(Request $request, $version, $orderId)
    {
        $pdf_info = $this->OrderRepository->getOrderPdfInfo($orderId);

        try {

            if ($pdf_info) {
                $this->OrderRepository->deletePdfInfo($orderId);
            }

            $order_info = $this->OrderRepository->getOrderInfoById($orderId);

            $so = data_get($order_info,'CompanyNo');
            $worksheet = data_get($order_info,'WorkSheet');
            $finish = data_get($order_info,'finsh');
            $zj_finsh = data_get($order_info, "zj_finsh");
            $assignSheet = data_get($order_info,'AssignSheet');
            $serviceName = json_decode($order_info->ServiceName);
            $IVR = json_decode($order_info->SubsCP);

            $TeleNum01_200 = data_get($order_info,'TeleNum01_200');
            $CustID = data_get($order_info,'CustID');
            $SubsID = data_get($order_info,'SubsID');
            $BookDate = data_get($order_info,'BookDate');
            $WorkKind = data_get($order_info,'WorkKind');
            $CustName = data_get($order_info,'CustName');
            $NetID = data_get($order_info,'NetID');
            $SaleCampaign = data_get($order_info,'SaleCampaign');
            $CreateName = data_get($order_info,'CreateName');
            $CreateTime = data_get($order_info,'CreateTime');
            $WorkerName = data_get($order_info,'WorkerName');
            $WorkTeam = data_get($order_info,'WorkTeam');
            $assignSheet = data_get($order_info,'AssignSheet');
            $create_at = data_get($order_info,'create_at');
            $MSComment1 = data_get($order_info,'MSComment1');

            $checkDSTB = json_decode($order_info->dstbcheck);
            $checkCM = json_decode($order_info->cmcheck);
            $checkTWMBB = json_decode($order_info->twmbbcheck);
            $borrowmingList = json_decode($order_info->BorrowmingList);
            $retrieveList = json_decode($order_info->RetrieveList);

            $deviceCount = data_get($order_info,'deviceCount');
            $deviceSWVersion = data_get($order_info,'deviceSWVersion');
            $maintainHistory = data_get($order_info,'maintainHistory');

            $InstAddrName = data_get($order_info,'InstAddrName');
            $WorkCause = data_get($order_info,'WorkCause');
            $MSContract = data_get($order_info,'MSContract');
            $MSContract2 = data_get($order_info,'MSContract2');
            $saleAP = data_get($order_info,'saleAP');


            $pdfData = array(
                'CompanyNo' => $so,
                'WorkSheet' => $worksheet,
                'finsh' => $finish,
                'ServiceName' => $serviceName,
                'TeleNum01_200' => $TeleNum01_200,
                'CustID' => $CustID,
                'SubsID' => $SubsID,
                'BookDate' => $BookDate,
                'WorkKind' => $WorkKind,
                'CustName' => $CustName,
                'NetID' => $NetID,
                'SaleCampaign' => $SaleCampaign,
                'CreateName' => $CreateName,
                'CreateTime' => $CreateTime,
                'WorkerName' => $WorkerName,
                'WorkTeam' => $WorkTeam,
                'AssignSheet' => $assignSheet,
                'dstbcheck' => $checkDSTB,
                'cmcheck' => $checkCM,
                'twmbbcheck' => $checkTWMBB,
                'BorrowmingList' => $borrowmingList,
                'SubsCP' => $IVR,
                'create_at' => $create_at,
                'RetrieveList' => $retrieveList,
                'MSComment1' => $MSComment1,
                'deviceCount' => $deviceCount,
                'deviceSWVersion' => $deviceSWVersion,
                'maintainHistory' => $maintainHistory,
                'InstAddrName' => $InstAddrName,
                'WorkCause' => $WorkCause,
                'MSContract' => $MSContract,
                'MSContract2' => $MSContract2,
                'saleAP' => $saleAP,
            );

            $insertData = array(
                'CompanyNo' => $so,
                'WorkSheet' => $worksheet,
                'Version' => $version,
                'AssegnUser' => 'app',
                'Data' => json_encode($pdfData),
                'orderListId' => $orderId
            );


            $pdfId = $this->OrderRepository->insertOrderPdfInfo($insertData);

            $code = '0000';
            $status = 'OK';
            $meg = '';
            $data = array(
                'pdfId' => $pdfId,
            );

        } catch (Exception $e) {

            Log::error(__CLASS__.'\\'.__FUNCTION__.'()'.$e->getMessage());

            $code = '0500';
            $status = 'error';
            $meg = '資料錯誤';
            $data = '';

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

}
