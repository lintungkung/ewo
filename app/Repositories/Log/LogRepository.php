<?php

namespace App\Repositories\Log;

use App\Repositories\Log\LogBaseRepository;

use App\Repositories\Order\OrderRepository;
use \Log;
use DB;
use Exception;
use \Session;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class LogRepository
{
    const  COSSDB = 'COSSDB.dbo.';

    public function updateCheckData($params)
    {
        $p_id = data_get($params, 'id');
        $p_type = data_get($params,'type');
        unset($data,$jsonStr);
        $json = array();
        if($p_type === "dstbcheck")
        {
            $json['dstb_check_id'] = data_get($params, 'dstb_check_id');
            $json['dstb_check_health'] = data_get($params, 'dstb_check_health');
            $json['dstb_check_driver'] = data_get($params, 'dstb_check_driver');
            $json['dstb_check_driver_desc'] = data_get($params, 'dstb_check_driver_desc');
            $json['dstb_check_company'] = data_get($params, 'dstb_check_company');
            $json['dstb_check_other'] = data_get($params, 'dstb_check_other');
            $json['dstb_check_other_desc'] = data_get($params, 'dstb_check_other_desc');
            $json['dstb_check_invoice'] = data_get($params, 'dstb_check_invoice');

            $dstb_check_personal = data_get($params, 'dstb_check_personal');
            $dstb_check_personal = (is_array($dstb_check_personal))? $dstb_check_personal[0] : null;
            $json['dstb_check_personal'] = $dstb_check_personal;
            $json['dstb_check_legal'] = data_get($params, 'dstb_check_legal');
            $json['dstb_check_title'] = data_get($params, 'dstb_check_title');

        }
        elseif($p_type === "cmcheck")
        {
            $json['cm_check_id'] = data_get($params, 'cm_check_id');
            $json['cm_check_health'] = data_get($params, 'cm_check_health');
            $json['cm_check_driver'] = data_get($params, 'cm_check_driver');
            $json['cm_check_driver_desc'] = data_get($params, 'cm_check_driver_desc');
            $json['cm_check_company'] = data_get($params, 'cm_check_company');
            $json['cm_check_other'] = data_get($params, 'cm_check_other');
            $json['cm_check_other_desc'] = data_get($params, 'cm_check_other_desc');
            $json['cm_check_invoice'] = data_get($params, 'cm_check_invoice');
            $json['cm_check_domicile'] = data_get($params, 'cm_check_domicile');
            $json['cm_check_legal'] = data_get($params, 'cm_check_legal');
            $json['cm_check_title'] = data_get($params, 'cm_check_title');
            $json['cm_check_equipment'] = data_get($params, 'cm_check_equipment');
            $json['cm_check_notest'] = data_get($params, 'cm_check_notest');
            $json['cm_check_standalone'] = data_get($params, 'cm_check_standalone');
            $json['cm_check_standalone_desc'] = data_get($params, 'cm_check_standalone_desc');
            $json['cm_check_notest_standalone'] = data_get($params, 'cm_check_notest_standalone');
            $json['cm_check_notest_standalone_desc'] = data_get($params, 'cm_check_notest_standalone_desc');
            $json['cm_check_equipmentdiscord_test'] = data_get($params, 'cm_check_equipmentdiscord_test');
            $json['cm_check_equipmentdiscord_test_desc'] = data_get($params, 'cm_check_equipmentdiscord_test_desc');

        }
        elseif($p_type === "twmbbcheck")
        {
            $json['twmbb_check_domicile'] = data_get($params, 'twmbb_check_domicile');
            $json['twmbb_check_equipment'] = data_get($params, 'twmbb_check_equipment');
            $json['twmbb_check_notest'] = data_get($params, 'twmbb_check_notest');
            $json['twmbb_check_standalone'] = data_get($params, 'twmbb_check_standalone');
            $json['twmbb_check_standalone_desc'] = data_get($params, 'twmbb_check_standalone_desc');
            $json['twmbb_check_notest_standalone'] = data_get($params, 'twmbb_check_notest_standalone');
            $json['twmbb_check_notest_standalone_desc'] = data_get($params, 'twmbb_check_notest_standalone_desc');
            $json['twmbb_check_equipmentdiscord_test'] = data_get($params, 'twmbb_check_equipmentdiscord_test');
            $json['twmbb_check_equipmentdiscord_test_desc'] = data_get($params, 'twmbb_check_equipmentdiscord_test_desc');
            $json['twmbb_check_legal'] = data_get($params, 'twmbb_check_legal');
            $json['twmbb_check_title'] = data_get($params, 'twmbb_check_title');

        }
        elseif($p_type === "BorrowmingList")
        {
            // 設備借用單
            $json['Cable_modem_port'] = data_get($params, 'Cable_modem_port');
            $json['Cable_modem_two_way'] = data_get($params, 'Cable_modem_two_way');
            $json['Basic_digital_set_top_box'] = data_get($params, 'Basic_digital_set_top_box');
            $json['Digital_set_top_box_two_way_type'] = data_get($params, 'Digital_set_top_box_two_way_type');
            $json['camera'] = data_get($params, 'camera');
            $json['Door_and_window_sensor'] = data_get($params, 'Door_and_window_sensor');
            $json['Smoke_detector'] = data_get($params, 'Smoke_detector');
            $json['Cable_accessories_wireless_anti_frequency_sharing_device'] = data_get($params, 'Cable_accessories_wireless_anti_frequency_sharing_device');
            $json['Cable_accessories_transformer_power_cord'] = data_get($params, 'Cable_accessories_transformer_power_cord');
            $json['Cable_accessories_Ethernet_cable'] = data_get($params, 'Cable_accessories_Ethernet_cable');
            $json['Cable_accessories_USB_wireless_anti_frequency_network_card'] = data_get($params, 'Cable_accessories_USB_wireless_anti_frequency_network_card');
            $json['Set_top_box_accessories_remote_control'] = data_get($params, 'Set_top_box_accessories_remote_control');
            $json['Set_top_box_accessories_HDI'] = data_get($params, 'Set_top_box_accessories_HDI');
            $json['Set_top_box_accessories_AV_cable'] = data_get($params, 'Set_top_box_accessories_AV_cable');
            $json['Set_top_box_accessories_Chromatic_aberration_line'] = data_get($params, 'Set_top_box_accessories_Chromatic_aberration_line');
            $json['Set_top_box_accessories_transformer_power_cord'] = data_get($params, 'Set_top_box_accessories_transformer_power_cord');
            $json['Set_top_box_accessories_smart_card'] = data_get($params, 'Set_top_box_accessories_smart_card');
            $json['Set_top_box_accessories_external_hard_disk'] = data_get($params, 'Set_top_box_accessories_external_hard_disk');
            $json['Set_top_box_accessories_USB_wireless_anti_frequency_network_card'] = data_get($params, 'Set_top_box_accessories_USB_wireless_anti_frequency_network_card');
            $json['Set_top_box_accessories_ATV_set_top_box'] = data_get($params, 'Set_top_box_accessories_ATV_set_top_box');
            $json['Set_top_box_accessories_Bluetooth_remote_control'] = data_get($params, 'Set_top_box_accessories_Bluetooth_remote_control');
            $json['Smart_home_accessories_transformer_power_cord'] = data_get($params, 'Smart_home_accessories_transformer_power_cord');
            $json['Fiber_Modem_HomeOnt'] = data_get($params, 'Fiber_Modem_HomeOnt');

        }
        elseif($p_type === "RetrieveList")
        {
            // 設備取回單
            $json['get_Cable_modem_port'] = data_get($params, 'get_Cable_modem_port');
            $json['get_Cable_modem_two_way'] = data_get($params, 'get_Cable_modem_two_way');
            $json['get_Basic_digital_set_top_box'] = data_get($params, 'get_Basic_digital_set_top_box');
            $json['get_Digital_set_top_box_two_way_type'] = data_get($params, 'get_Digital_set_top_box_two_way_type');
            $json['get_camera'] = data_get($params, 'get_camera');
            $json['get_Door_and_window_sensor'] = data_get($params, 'get_Door_and_window_sensor');
            $json['get_Smoke_detector'] = data_get($params, 'get_Smoke_detector');
            $json['get_Cable_accessories_wireless_anti_frequency_sharing_device'] = data_get($params, 'get_Cable_accessories_wireless_anti_frequency_sharing_device');
            $json['get_Cable_accessories_transformer_power_cord'] = data_get($params, 'get_Cable_accessories_transformer_power_cord');
            $json['get_Cable_accessories_Ethernet_cable'] = data_get($params, 'get_Cable_accessories_Ethernet_cable');
            $json['get_Cable_accessories_USB_wireless_anti_frequency_network_card'] = data_get($params, 'get_Cable_accessories_USB_wireless_anti_frequency_network_card');
            $json['get_Set_top_box_accessories_remote_control'] = data_get($params, 'get_Set_top_box_accessories_remote_control');
            $json['get_Set_top_box_accessories_HDI'] = data_get($params, 'get_Set_top_box_accessories_HDI');
            $json['get_Set_top_box_accessories_AV_cable'] = data_get($params, 'get_Set_top_box_accessories_AV_cable');
            $json['get_Set_top_box_accessories_Chromatic_aberration_line'] = data_get($params, 'get_Set_top_box_accessories_Chromatic_aberration_line');
            $json['get_Set_top_box_accessories_transformer_power_cord'] = data_get($params, 'get_Set_top_box_accessories_transformer_power_cord');
            $json['get_Set_top_box_accessories_smart_card'] = data_get($params, 'get_Set_top_box_accessories_smart_card');
            $json['get_Set_top_box_accessories_external_hard_disk'] = data_get($params, 'get_Set_top_box_accessories_external_hard_disk');
            $json['get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card'] = data_get($params, 'get_Set_top_box_accessories_USB_wireless_anti_frequency_network_card');
            $json['get_Set_top_box_accessories_ATV_set_top_box'] = data_get($params, 'get_Set_top_box_accessories_ATV_set_top_box');
            $json['get_Set_top_box_accessories_Bluetooth_remote_control'] = data_get($params, 'get_Set_top_box_accessories_Bluetooth_remote_control');
            $json['get_Smart_home_accessories_transformer_power_cord'] = data_get($params, 'get_Smart_home_accessories_transformer_power_cord');
            $json['get_Fiber_Modem_HomeOnt'] = data_get($params, 'get_Fiber_Modem_HomeOnt');

        }

        //error_log(print_r($p_type,1));

        $jsonStr = json_encode($json);
        $data['p_columnName'] = $p_type;
        $data['p_value'] = $jsonStr;
//        Session::put($p_type,$jsonStr);
        $whereAry = array(["Id","=",$p_id]);

        try {

            $obj = New LogBaseRepository();
            $obj->initDB(DB::connection('WMDB_IO')->table('wm_orderlist')->lock('WITH(nolock)'));
            $obj->where($whereAry);
            $ret = $obj->updateCheckData($data);

            // 新增LOG
            $logAry = array(
                'CompanyNo' => data_get($params, 'companyNo'),
                'WorkSheet' => data_get($params, 'workSheet'),
                'CustID' => data_get($params, 'custId'),
                'UserNum' => data_get($params, 'userCode'),
                'UserName' => data_get($params, 'userName'),
                'EventType' => $p_type,
            );
            $logAry['Request'] = "ID:$p_id;Type:$p_type;";
            $logAry['Responses'] = 'Data:'.json_encode($data);
            $this->insertLog($logAry);

            return $ret;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // 五金耗料，依[wm_orderlist.Id]刪除
    public function hardConsDeleteById($params)
    {
        $p_id = data_get($params,'id');
        $obj = New LogBaseRepository();
        $obj->initDB(DB::connection('WMDB')->table('wm_hardcons')->lock('WITH(nolock)'));
        $whereAry = array(
            ['orderlistId','=',$p_id]
        );
        $obj->where($whereAry);
        $ret = $obj->delete();
        return $ret;
    }

    // 五金耗料，新增
    public function hardConsAddSave($params)
    {
        $p_params = array();
        $p_params['p_orderlistId'] = data_get($params,'p_orderlistId');
        $p_params['p_companyNo'] = data_get($params,'p_companyNo');
        $p_params['p_userCode'] = data_get($params,'p_userCode');
        $p_params['p_materialsCode'] = data_get($params,'p_materialsCode');
        $p_params['p_count'] = data_get($params,'p_count');

        $obj = New LogBaseRepository();
        $obj->initDB(DB::connection('WMDB_IO')->table('wm_hardcons')->lock('WITH(nolock)'));
        $ret = $obj->insertHardCons($p_params);
        return $ret;
    }


    // 勞安LOG，新增
    public function laborsafetyLogAdd($data)
    {
        $obj = New LogBaseRepository();

        $obj->initDB(DB::connection('WMDB_IO')->table('wm_laborsafetylog')->lock('WITH(nolock)'));

        $ret = $obj->addLaborsafetylog($data);

        return $ret;
    }

    // 勞安LOG，刪除
    public function laborsafetyLogDel($params)
    {
        $companyNo = data_get($params,'CompanyNo');
        $workSheet = data_get($params,'WorkSheet');
        $type = data_get($params,'Type');
        if(empty($companyNo) || empty($workSheet) || empty($type))
            return 'check column CompanyNo,WorkSheet,Type is null?';

        $obj = New LogBaseRepository();

        $obj->initDB(DB::connection('WMDB_IO')->table('wm_laborsafetylog')->lock('WITH(nolock)'));

        foreach($params as $k => $t) {

            switch ($k) {
                case 'CompanyNo':
                    $obj->whereObj('CompanyNo','=',$t);
                    break;

                case 'WorkSheet':
                    $obj->whereObj('WorkSheet','=',$t);
                    break;

                case 'Type':
                    $obj->whereObj('Type','=',$t);
                    break;

                case 'Desc1':
                    $obj->whereObj('Desc1','=',$t);
                    break;

                case 'userCode':
                case 'p_userCode':
                    $obj->whereObj('UserCode','=',$t);
                    break;

                case 'timeStart':
                    $obj->whereObj('create_at','>=',$t);
                    break;

                case 'timeEend':
                    $obj->whereObj('create_at','<=',$t);
                    break;
            }
        }

//        $a = $obj->objToSql();
//        return $a;
        $obj->delete();
    }

    // 更新 時間的欄位
    public function updateEventTime($params)
    {
        $p_id = intval(data_get($params,'p_id'));
        $data['p_columnName'] = data_get($params,'p_columnName');
        $data['p_value'] = data_get($params,'p_value');
        $whereAry = array(
            ['CompanyNo' ,'=', data_get($params,'p_companyNo')],
            ['WorkSheet' ,'=', data_get($params,'p_workSheet')]
        );
        if(empty($p_id) === false) {
            $whereAry = array(
                ['Id', '=', data_get($params,'p_id')]
            );
        }
        //Log::channel('ewoLog')->info('chk updateEventTime params=='.print_r($params,1));

        try {
            $obj = New LogBaseRepository();
            $obj->initDB(DB::connection('WMDB_IO')->table('wm_orderlist')->lock('WITH(nolock)'));
            $obj->where($whereAry);

            if($data['p_columnName'] === "checkin") { // 打卡 GPS
                $data['p_value_gps'] = data_get($params, 'p_value_gps');

                $checkInData = data_get($params,'checkInData');
                $data['gpsRefAddres'] = data_get($checkInData, 'checkInAddres');
                $data['custGps'] = data_get($checkInData, 'custGps');
                $data['gpsDistance'] = data_get($checkInData, 'gpsDistance');
                $obj->updateCheckIn($data);

            } elseif($data['p_columnName'] === "serviceReson") { // 維修原因
                $data['p_serviceResonFirst'] = data_get($params, 'p_serviceResonFirst');
                $data['p_serviceResonLast'] = data_get($params, 'p_serviceResonLast');
                $obj->updataServiceReson($data);

            } elseif($data['p_columnName'] === "chargeback") { // 退單
                $data['p_value'] = data_get($params, 'p_value');
                $obj->updataChargeBackDesc($data);

            } elseif($data['p_columnName'] === "delate") { // 遲到
                $data['p_value'] = data_get($params, 'p_value');
                $obj->updataDeltaeDesc($data);

            } elseif($data['p_columnName'] === "id03Photo") { // 第二證件照
                $data['p_value'] = data_get($params, 'p_value');
                $obj->updataId03Photo($data);

            } elseif(in_array($data['p_columnName'],array('sign_dstb','sign_cm','sign_twmbb')) > 0) { // 簽名檔
                $data['p_value'] = data_get($params, 'p_value');
                $obj->updataSign($data);

            } elseif(in_array($data['p_columnName'],array('finsh')) > 0) { // 完工
                $data['p_receiveType'] = data_get($params, 'p_receiveType');
                $data['p_receiveMoney'] = data_get($params, 'p_receiveMoney');
                $data['p_BackCause'] = data_get($params, 'p_BackCause');
                $data['p_CleanCause'] = data_get($params, 'p_CleanCause');
                $data['p_BackCause'] = data_get($params, 'p_BackCause');
                $data['p_CleanCause'] = data_get($params, 'p_CleanCause');
                $obj->updataFinsh($data);

            } elseif(in_array($data['p_columnName'],array('openApi')) > 0) { // 開通
                $data['p_receiveType'] = data_get($params, 'p_receiveType');
                $data['p_receiveMoney'] = data_get($params, 'p_receiveMoney');
                $obj->updataOpenApi($data);

//            } elseif(in_array($data['p_columnName'],array('cmqualityforkg')) > 0) { // CM查詢，網路品質查詢_存檔
//                $vValue = data_get($data,'p_value');
//                $vValueAry = json_decode($vValue,true);
//                $vCmqualityforkg = data_get($vValueAry,'cmqualityforkg');
//                $vCmqualityforkgStr = json_encode($vCmqualityforkg);
//                $obj->updateEventTime($data);

            } elseif(in_array($data['p_columnName'],array('cmnsQuerySave'))) { // cmns 測速，存檔
                $data['p_columnName'] = 'dataList';
                $obj->updateEventTime($data);

            } else {
                // constructionPhoto[施工照片]
                // // sentmail[PDF 寄送 mail]
                $obj->where($whereAry);
                $obj->updateEventTime($data);
            }


            $obj->selectAll();
            $obj->where($whereAry);
            $response = $obj->first();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function insertLog($data=null)
    {
        try {
            $obj = New LogBaseRepository();
            $obj->initDB(DB::connection('WMDB_IO')->table('wm_log')->lock('WITH(nolock)'));
            $response = $obj->insertLog($data);

            return $response;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getMS0310($data)
    {
        $COSSDB = self::COSSDB;
        $COSSDB = substr($COSSDB,0,strlen($COSSDB)-1);
        $obj = New LogBaseRepository();
        $obj->initDB(DB::connection('WMDB_COSSDB')->table("$COSSDB.MS0310")->lock('WITH(nolock)'));

        foreach ($data as $k => $t) {
            if(empty($t)) continue;

            switch ($k) {
                case 'companyNo':
                case 'workSheet':
                case 'callRequest':
                case 'workCause':
                case 'caseClose':
                    $obj->whereObj($k,'=',$t);
                    break;
            }
        }

        if(data_get($data,'count') == 'Y') {
            $obj->selectCount();
        } else {
            $obj->selectAll();
        }

        $query = $obj->objGET();

        return $query;
    }


    public function insertMS0310($data)
    {
        $COSSDB = self::COSSDB;
        $COSSDB = substr($COSSDB,0,strlen($COSSDB)-1);
        try {
            $obj = New LogBaseRepository();
            $obj->initDB(DB::connection('WMDB_COSSDB')->table("$COSSDB.MS0310")->lock('WITH(nolock)'));
            $response = $obj->insertMS0310($data);

            return $response;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function insertPDFData($userId)
    {
        try {
            $this->LogBaseRepository->initDB(DB::connection('WMDB')->table('wm_pdfdata')->lock('WITH(nolock)'));
            $this->db = $this->LogBaseRepository->selectUserTokenInfo();
            $response = $this->LogBaseRepository->whereUserId($userId);


            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    // 維修原因備註
    public function serviceReasonRemarks($params) {
        try {
            $COSSDB = self::COSSDB;
            $COSSDB = substr($COSSDB,0,strlen($COSSDB)-1);

            $obj = New LogBaseRepository();
            $obj->initDB(DB::connection('WMDB_COSSDB')->table("$COSSDB.MS0301")->lock('WITH(nolock)'));

            $obj->whereSo($params['p_companyNo']);
            $obj->whereAssignSheet($params['p_workSheet']);
            $obj->updateMSRemark($params['p_value']);

            $obj->objAddSelect(['MSRemark']);
            $query = $obj->objGET();
            $ret = $query[0]->MSRemark;

            Log::channel('ewoLog')->info(
                'chk serviceReasonRemarks  p_companyNo=='
                . $params['p_companyNo'] . ';p_workSheet=='
                . $params['p_workSheet'] . ';p_value=='
                . $params['p_value'] . ';ret=='
                . $ret
            );

            return $ret;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // 拆機流向_MS0300
    public function setDemolitionFlow_MS0300($params) {
        $COSSDB = self::COSSDB;
        $COSSDB = substr($COSSDB,0,strlen($COSSDB)-1);
        try {
            $obj = New LogBaseRepository();
            $obj->initDB(DB::connection('WMDB_COSSDB')->table("$COSSDB.MS0300")->lock('WITH(nolock)'));

            $obj->whereSo($params['p_companyNo']);

            $obj->whereWorkSheet($params['p_workSheet']);

            $obj->updateWorkTeam2($params['p_value']);

            $obj->objAddSelect(['WorkTeam2']);
            $query = $obj->objGET();
            $ret = $query[0]->WorkTeam2;

            return $ret;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    // 拆機流向_MS0301
    public function setDemolitionFlow_MS0301($params) {
        $COSSDB = self::COSSDB;
        $COSSDB = substr($COSSDB,0,strlen($COSSDB)-1);
        try {
            $obj = New LogBaseRepository();
            $obj->initDB(DB::connection('WMDB_COSSDB')->table("$COSSDB.MS0301")->lock('WITH(nolock)'));

            $obj->whereSo($params['p_companyNo']);

            $obj->whereWorkSheet($params['p_workSheet']);

            $obj->whereChargeKind();

            $obj->updateGiftList2($params['p_value']);

            $obj->objAddSelect(['GiftList2']);
            $ret = $obj->objGET();

            return $ret;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function t_sentMainSQL($date)
    {
        $obj = New LogBaseRepository();
        $obj->initDB(DB::connection('WMDB')->table('wm_orderlist as a')->lock('WITH(nolock)'));

        $obj->t_leftjoinSentMailLOG();

        $obj->objAddSelect(['companyno','worksheet','bookdate','sentmail','custid','personid'],'a.');

        $params = array(
            ['a.BookDate' ,'<', $date]
        );
        $obj->where($params);

        $obj->objIsNotNull(['a.sentmail']);

        $sql = $obj->objToSql();

        $data = $obj->objGET();

        $ret = array('data'=>$data,'sql'=>$sql);

        return $ret;

    }


    // 查詢工單訊息
    public function getOrderInfo($data)
    {
        $COSSDB = self::COSSDB;
        $COSSDB = substr($COSSDB,0,strlen($COSSDB)-1);

        $obj = New LogBaseRepository();
        $obj->initDB(DB::connection('WMDB')->table("$COSSDB.MS0300 AS a")->lock('WITH(nolock)'));

        $obj->selectAll();

        $joinAry = array(
            'table' => "$COSSDB.MS0301",
            'asname' => 'b',
            'onary' => [
                ['a.CompanyNo','b.CompanyNo'],
                ['a.WorkSheet','b.AssignSheet'],
            ],
        );
        $obj->lefJoinObj($joinAry);

        foreach ($data as $k => $t) {
            if(empty($t)) continue;

            switch ($k) {
                case 'companyNo':
                    $obj->whereObj('CompanyNo', '=', $t,'a.');
                    break;

                case 'workSheet':
                    $obj->whereObj('WorkSheet', '=', $t,'a.');
                    break;

                case 'worker1Like':
                    $obj->whereObj('Worker1', 'like', $t,'b.');
                    break;

                case 'sheetStatusNotIn':
                    $obj->objNotIn(['column' => 'b.SheetStatus','value' => $t]);
                    break;

                case 'bookDateStart':
                    $obj->whereObj('BookDate', '>=', $t,'a.');
                    break;

                case 'bookDateEnd':
                    $obj->whereObj('BookDate', '<', $t,'a.');
                    break;
            }
        }

        $orderByAry = array(
            ['name' => 'a.Bookdate', 'type' => 'ASC'],
        );
        $obj->orderByOrderAry($orderByAry);

        $sql = $obj->objToSql();
        $query = $obj->objGET();

       $ret = $query;

        return $ret;

    }


}
