<?php

namespace App\Repositories\Reason;

use App\Repositories\Reason\ReasonBaseRepository;

use DB;
use Exception;
use \Session;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class ReasonRepository
{
    const  COSSDB = 'COSSDB.dbo.';

    public function __construct(ReasonBaseRepository $ReasonBaseRepository)
    {
        $this->ReasonBaseRepository = $ReasonBaseRepository;
        $this->db = $this->ReasonBaseRepository;
    }

    public function getServiceReasonFirst($data)
    {
        $COSSDB = self::COSSDB;
        $COSSDB = substr($COSSDB,0,strlen($COSSDB)-1);

        try {
            $this->ReasonBaseRepository->initDB(DB::connection('WMDB')->table("$COSSDB.V_CNSREPFT_VIEW")->lock('WITH(nolock)'));

            $this->db = $this->ReasonBaseRepository->selectServiceReasonInfo();

            $this->db = $this->ReasonBaseRepository->whereSreviceReasonCode($data['services']);


            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getServiceReasonSecond($data)
    {
        try {
            $this->ReasonBaseRepository->initDB(DB::connection('COSSDBNAME')->table('V_CNSREPST_VIEW')->lock('WITH(nolock)'));

            $dataWhere = array(
                [
                    'asname' => '',
                    'name' => 'STOPYN',
                    'type' => '=',
                    'value' => 'N',
                ],
            );
            $this->db = $this->ReasonBaseRepository->whereObj($dataWhere);

            $this->db = $this->ReasonBaseRepository->whereSreviceCode($data['services']);

            $this->db = $this->ReasonBaseRepository->whereSreviceReasonFirstCode($data['firstCode']);

            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getBackReason()
    {
        $COSSDB = self::COSSDB;
        $COSSDB = substr($COSSDB,0,strlen($COSSDB)-1);

        try {
            $this->ReasonBaseRepository->initDB(DB::connection('WMDB')->table("$COSSDB.V_CNSBACK_VIEW")->lock('WITH(nolock)'));

            $this->db = $this->ReasonBaseRepository->selectServiceReasonInfo();


            $response = $this->db->get();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getDemolitionFlow()
    {
        $COSSDB = self::COSSDB;
        $COSSDB = substr($COSSDB,0,strlen($COSSDB)-1);

        try {
            $this->ReasonBaseRepository->initDB(DB::connection('WMDB')->table("$COSSDB.MSCD0031 AS MSCD31")->lock('WITH(nolock)'));

            $this->db = $this->ReasonBaseRepository->joinMSCD9990();

            $this->db = $this->ReasonBaseRepository->selectDemolitionFlow();

            $this->db = $this->ReasonBaseRepository->whereDemolitionFlow();


            $response = $this->db->get()->toArray();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    // 同戶服務狀態
    public function getSameAccountService($p_custId,$p_companyNo)
    {
        $COSSDB = self::COSSDB;
        $COSSDB = substr($COSSDB,0,strlen($COSSDB)-1);

        try {
            $this->ReasonBaseRepository->initDB(DB::connection('WMDB')->table("$COSSDB.V_CNSSTOPEQP_VIEW")->lock('WITH(nolock)'));

            $this->db = $this->ReasonBaseRepository->selectServiceReasonInfo();

            $this->db = $this->ReasonBaseRepository->whereCustId($p_custId);

            $this->db = $this->ReasonBaseRepository->likeCompanyNo($p_companyNo);

            $response = $this->db->get()->toArray();

            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }




}
