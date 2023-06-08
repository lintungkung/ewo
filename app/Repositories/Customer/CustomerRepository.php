<?php

namespace App\Repositories\Customer;

use App\Repositories\Customer\CustomerBaseRepository;

use DB;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class CustomerRepository
{
    public function __construct(CustomerBaseRepository $CustomerBaseRepository)
    {
        $this->CustomerBaseRepository = $CustomerBaseRepository;
        $this->db = $this->CustomerBaseRepository;
    }

    public function getCustDevices($data)
    {
        try {
            $this->CustomerBaseRepository->initDB(DB::connection('WMDB')->table('COSSDB.dbo.MS0200 as ms200')->lock('WITH(nolock)'));

            $this->db = $this->CustomerBaseRepository->selectCustDevices();
            $this->db = $this->CustomerBaseRepository->selectDstbRemoteQr();

            $this->db = $this->CustomerBaseRepository->LeftJoinWmDstbRemotesFromMs200();

            $this->db = $this->CustomerBaseRepository->whereMS0200CompanyNo($data['companyNo']);

            $this->db = $this->CustomerBaseRepository->whereMS0200CustID($data['custId']);

            if(!empty($data['custStatusNotIn'])) {
                $this->db = $this->CustomerBaseRepository->whereMS0200CustStatusNotIn($data['custStatusNotIn']);
            }

            //$this->db = $this->CustomerBaseRepository->whereIn('ServiceName',['2 CM','3 DSTB'],'ms200.');

            $this->db = $this->CustomerBaseRepository->orderBy([['name'=>'ServiceName','type'=>'asc']]);
            $response = $this->db->get();
            return $response;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateCustDevice($data)
    {
        try {
            $this->CustomerBaseRepository->initDB(DB::connection('WMDB_COSSDB')->table('COSSDB.dbo.MS0301')->lock('WITH(nolock)'));


            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {
                        case 'companyNo':
                            $this->db = $this->CustomerBaseRepository->whereCompanyNo($value);
                            break;
                        case 'assignSheet':
                            $this->db = $this->CustomerBaseRepository->whereAssignSheet($value);
                            break;
                        case 'custid':
                            $this->db = $this->CustomerBaseRepository->whereCustId($value);
                            break;
                        case 'serviceName':
                            $this->db = $this->CustomerBaseRepository->whereServiceNameIn($value);
                            break;
                    }
                }
            }

            $this->db = $this->CustomerBaseRepository->updateDevice($data);

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function getCustDeviceMS0211($data)
    {
        try {
            $this->CustomerBaseRepository->initDB(DB::connection('WMDBNAME_IO')->table('MS0211 as ms211')->lock('WITH(nolock)'));

            $this->db = $this->CustomerBaseRepository->selectCustInfo();

            foreach ($data as $key => $value) {
                if (!empty($value)) {
                    switch ($key) {

                    case 'companyNoIn':
                        $this->db = $this->CustomerBaseRepository->whereIn('CompanyNo',$value,'ms211.');
                        break;

                    case 'subsIDIn':
                        $this->db = $this->CustomerBaseRepository->whereIn('SubsID',$value,'ms211.');
                        break;

                    case 'chargeNameIn':
                        $this->db = $this->CustomerBaseRepository->whereIn('ChargeName',$value,'ms211.');
                        break;

                    }
                }
            }

            $ret = $this->db->get();

            return $ret;

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


    public function getEnableCustomerByCustomerId($customer_id,$company_number)
    {
        try {
            $this->CustomerBaseRepository->initDB(DB::connection('WMDB')->table('COSSDB.dbo.MS0200 as ms200')->lock('WITH(nolock)'));

            /** @var Builder  $query */
            $query = $this->CustomerBaseRepository->selectCustPhones();

            // Filter 條件
            $query->whereNotIn('CustStatus',['2 停機','3 已拆','4 註銷']);
            $query->where('CustID','=',$customer_id);
            $query->where('CompanyNo','=',$company_number);

            // groupBy
            $query->groupBy("CellPhone01","CellPhone02","TeleNum01","TeleNum02","TeleNum03","MailCity");

            return $query->get();

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

    }

}
