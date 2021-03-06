<?php

namespace Vanguard\Repositories\Company;

use Vanguard\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EloquentCompany implements CompanyRepository
{
 
	public function __construct()
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Company::find($id);
    }
	/*public function findByBatch($batch_id)
	{
		return Company::find($batch_id);
	}
    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return Company::where('name', $name)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return Company::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage, $search = null, $parentId = null)
    {
        $query = Company::query();

        if ($search) {
            $query->where(function ($q) use($search) {
                $q->where('company_name', "like", "%{$search}%");
            });
        }

        $result = $query->where("parent_id", "=", $parentId)->orderBy('created_at', 'DESC')->get();

        if ($search) {
            $result->appends(['search' => $search]);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        return $this->find($id)->update($data);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $company = $this->find($id);

        return $company->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return Company::count();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getUnAssignedCount($batchId)
    {
    	$query = Company::query();
    	if ($batchId != 0) {
    		$query->where(function ($q) use($batchId) {
    			$q->where('companies.batch_id', "=", "{$batchId}")
    				->where('companies.status',"=","UnAssigned");
 //   				->whereNull('sub_batch_id');
    		});
    	} else {
    		return 0;
    	}
    	$result = $query->count();
    	Log::debug("getUnAssignedCount Sql:". $query->toSql());
		Log::debug("GetUnAssigned Count:".$result);
    	return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTotalCompanyCount($batchId)
    {
    	$query = Company::query();
    	if ($batchId != 0) {
    		$query->where(function ($q) use($batchId) {
    			$q->where('companies.batch_id', "=", "{$batchId}");
    			$q->where('companies.parent_id',"=",'0');
    		});
    	} else {
    		return 0;
    	}
    	$result = $query->count();
    	Log::debug("getTotalCompanyCount Sql:". $query->toSql());
		Log::debug("GetUnAssigned Count:".$result);
    	return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function lists($column = 'name', $key = 'id')
    {
    	return Company::lists($column, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function newCompanysCount()
    {
        return Company::whereBetween('created_at', [Carbon::now()->firstOfMonth(), Carbon::now()])
            ->count();
    }

    /**
     * {@inheritdoc}
     */
    public function latest($count = 20)
    {
        return Company::orderBy('created_at', 'DESC')
            ->limit($count)
            ->get();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCompanyRecord($subBatchId, $userId)
    {
    	$query = Company::query();
    	if ($subBatchId != 0) {
    		$query = $query->where(function ($q) use($subBatchId, $userId) {
    			$q->where('companies.sub_batch_id', "=", "{$subBatchId}")
    			->where('companies.user_id', "=", "{$userId}")
    			->where('companies.status', "=", "Assigned");    			
    		});
    	} else {
    		return 0;
    	}
    	$result = $query->orderBy('companies.updated_at', 'desc')->limit(1)->get();
    	//$result = $query->orderBy('companies.parent_id', 'desc')->limit(1)->get();
    	Log::debug("getCompanyRecord Sql:". $query->toSql());
		Log::debug("getCompanyRecord Output:".$result);
    	return $result;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCompaniesForBatch($batchId, $limit)
    {
    	$query = Company::query();
    	if ($batchId != 0) {
    		$query->where(function ($q) use($batchId) {
    			$q->where('companies.batch_id', "=", "{$batchId}")
    			->where('companies.status', "=", "UnAssigned")
    			->orderBy('id', 'ASC');
    		});
    	} else {
    		return 0;
    	}
    	$result = $query->limit($limit)->get();
    	Log::debug("getCompaniesForBatch Sql:". $query->toSql());
		Log::debug("getCompaniesForBatch Output:".$result);
    	return $result;
    }
    
    
    /**
     * {@inheritdoc}
     * /
     */
    public function getCompaniesForSubBatchDelete($batchId)
    {
    	$query = Company::query();
    	if ($batchId != 0) {
    		$query->where(function ($q) use($batchId) {
    			$q->where('companies.sub_batch_id', "=", "{$batchId}")
    			->orderBy('id', 'ASC');
    		});
    	} else {
    		return 0;
    	}
    	$result = $query->get();
    	Log::debug("getCompaniesForSubBatchDelete Sql:". $query->toSql());
		Log::debug("getCompaniesForSubBatchDelete Output:".$result);
    	return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubmittedCompanyCount($batchId)
    {
    	$query = Company::query();
    	if ($batchId != 0) {
    		$query->where(function ($q) use($batchId) {
    			$q->where('companies.batch_id', "=", "{$batchId}");
    			$q->where('companies.status',"=","Submitted");
    			$q->where('companies.parent_id',"=","0");
    		});
    	} else {
    		return 0;
    	}
    	$result = $query->count();
    	Log::debug("getSubmittedCompanyCount Sql:". $query->toSql());
		Log::debug("getSubmittedCompanyCount Count:".$result);
    	return $result;
    }
    
    /**
     * {@inheritDoc}
     * @see \Vanguard\Repositories\Company\CompanyRepository::getTotalCompany()
     */
    public function getTotalCompany($batchId)
    {
    	$query = Company::query();
    	$result = $query
    	->leftjoin('contacts', 'companies.id', '=', 'contacts.company_id')
    	->leftjoin('batches','batches.id','=', 'companies.batch_id')
    	->leftjoin('users','users.id',"=","companies.user_id")
    	->leftjoin('countries','countries.id',"=","companies.country")
    	->leftjoin('projects','projects.id',"=","batches.project_id")
    	->select('companies.*','contacts.*','batches.name as batch_name','projects.code as project_code','projects.name as project_name','companies.id as com_id','contacts.id as contact_id','users.first_name as ufname','countries.name as country_name','users.last_name as ulname','companies.additional_info1 as com_info1','companies.additional_info2 as com_info2','companies.additional_info3 as com_info3','companies.additional_info4 as com_info4','companies.additional_info5 as com_info5','companies.additional_info6 as com_info6','companies.additional_info7 as com_info7','companies.additional_info8 as com_info8','contacts.additional_info1 as info1','contacts.additional_info2 as info2','contacts.additional_info3 as info3','contacts.additional_info4 as info4','contacts.additional_info5 as info5','contacts.additional_info6 as info6','contacts.additional_info7 as info7','contacts.additional_info8 as info8','companies.created_at as company_created_at','companies.updated_at as company_updated_at','contacts.created_at as contacts_created_at','contacts.updated_at as contact_updated_at')
    	->orderBy('companies.company_name', 'ASC');
    	if ($batchId != 0) {
    		$query->where(function ($q) use($batchId) {
    			$q->where('companies.batch_id', "=", "{$batchId}");
    			$q->where('companies.status',"=","Submitted");
    		});
    	} else {
    		return 0;
    	}
    
    	$result = $query->get();
    	Log::debug("getTotalCompany Sql:". $query->toSql());
		Log::debug("getTotalCompany Output:".$result);
    	return $result;
    }
    
    
    /**
     * 
     * {@inheritDoc}
     * @see \Vanguard\Repositories\Company\CompanyRepository::getChildCompanies()
     */
    public function getChildCompanies($parentId)
    {
    	return Company::where('parent_id', $parentId)->get();//lists('company_name','id');
    }
   
    public function getcompanies($batchId)
    {
    	$query = Company::query();
    	if ($batchId != 0) {
    		$query->where(function ($q) use($batchId) {
    			$q->where('companies.batch_id', "=", "{$batchId}");
    		});
    	} else {
    		return 0;
    	}
    	$result = $query->select('id')->get();
    	Log::debug("getcompanies Sql:". $query->toSql());
		Log::debug("getcompanies Output:".$result);
    	return $result;
    }
    
    public function getCompaniesForProductivityReport($vendorId = null,$userId = null,$start_date=null,$end_date=null)
    {
    	$query = Company::query();
    	if($vendorId)
    	{
    		$query->where('batches.vendor_id',"=","{$vendorId}");
    	}
    	if($userId)
    	{
    		$query->where('companies.user_id',"=","{$userId}");
    	}
    	if($start_date)
    	{
    		$query->where('companies.updated_at',">=","{$start_date}");
    	}
    	if($end_date == null )
    	{
    		$end_date=Carbon::now();//->format('Y-m-d h:M:s');//Carbon::today()
    		$query->where('companies.updated_at',"<=","{$end_date}");
    	}
    	else
    	{
    		$end_date =$end_date . " 23:59:59";
    		$query->where('companies.updated_at',"<=","{$end_date}");
    	}
    	if($vendorId || $userId)
    	{
    		$query->where('companies.parent_id',"=","0");
    		$result=$query
    			->leftjoin('batches', 'batches.id', '=', 'companies.batch_id')
    			->where('companies.status',"=","Submitted")
    			->count();
    			Log::debug("getCompaniesForProductivityReport Sql:". $query->toSql());
				Log::debug("getCompaniesForProductivityReport Count:".$result);
    			return $result;
    	}
    	else 
    		return 0;
    }
    
    public function getSubsidiaryCompaniesForProductivityReport($vendorId = null,$userId = null,$start_date=null,$end_date=null)
    {
    	$query = Company::query();
    	if($vendorId)
    	{
    		$query->where('batches.vendor_id',"=","{$vendorId}");
    	}
    	if($userId)
    	{
    		$query->where('companies.user_id',"=","{$userId}");
    	}
    	if($start_date)
    	{
    		$query->where('companies.updated_at',">=","{$start_date}");
    	}
    	if($end_date == null )
    	{
    		$end_date=Carbon::now();//->format('Y-m-d h:M:s');//Carbon::today()
    		$query->where('companies.updated_at',"<=","{$end_date}");
    	}
    	else
    	{
    		$end_date =$end_date . " 23:59:59";
    		$query->where('companies.updated_at',"<=","{$end_date}");
    	}
    	if($vendorId || $userId)
    	{
    		$query->where('companies.parent_id',"!=","0");
    		$result=$query
    		->leftjoin('batches', 'batches.id', '=', 'companies.batch_id')
    		->where('companies.status',"=","Submitted")
    		->count();
    		Log::debug("getSubsidiaryCompaniesForProductivityReport Sql:". $query->toSql());
			Log::debug("getSubsidiaryCompaniesForProductivityReport Count:".$result);
    		return $result;
    	}
    	else
    		return 0;
    }
    
    public function getcompaniesforReport($vendorId = null,$userId = null)
    {
    	$query = Company::query();
    	if ($vendorId) 
    	{
    		$query->where('companies.vendor_id', "=", "{$vendorId}");
    	}
    	if($userId)
    	{
    		$query->where('$companies.user_id',"=","{$userId}");
    	}
    	$result = $query->select('id')->get();
    	Log::debug("getcompaniesforReport Sql:". $query->toSql());
		Log::debug("getcompaniesforReport output:".$result);
    	return $result;
    }
    
    public function getAssignedCompanyCountForSubBatch($subBatchId = null)
    {
    	$query = Company::query();
    	if($subBatchId)
    	{
    		$query->where('companies.sub_batch_id',"=","{$subBatchId}");
    	}
    	$result = $query->where('status',"=","Assigned")->count();
    	Log::debug("getAssignedCompanyCountForSubBatch Sql:". $query->toSql());
		Log::debug("getAssignedCompanyCountForSubBatch Count:".$result);
    	return $result;
    }
    
    public function getCompaniesForBatchForReallocation($batch_id)
    {
    	$query = Company::query();
    	$result = $query->where('batch_id',"=","{$batch_id}")
    					->where('parent_id',"=","0")
    					->get();
    	return $result;
    }
    
    public function getSubmittedCompanyCountForReport($batchId,$userId)
    {
    	$query = Company::query();
    	if($batchId)
    	{
    		$query->where('companies.batch_id',"=","{$batchId}");
    	}
    	if($userId)
    	{
    		$query->where('companies.user_id',"=","{$userId}");
    	}
    	$query->where('companies.parent_id',"=","0");
    	$result=$query->where('companies.status',"=","Submitted")
    				->count();
    		Log::debug("getSubmittedCompanyCountForReport Sql:". $query->toSql());
    		Log::debug("getSubmittedCompanyCountForReport Count:".$result);
    	return $result;
    }
    
    public function getSubmittedSubsidiaryCompanyCount($batchId=null,$userId=null)
    {
    	$query = Company::query();
    	if($batchId)
    	{
    		$query->where('companies.batch_id',"=","{$batchId}");
    	}
    	if($userId)
    	{
    		$query->where('companies.user_id',"=","{$userId}");
    	}
    	$query->where('companies.parent_id',"!=","0");
    	$result=$query->where('companies.status',"=","Submitted")
    	->count();
    	Log::debug("getSubmittedCompanyCountForReport Sql:". $query->toSql());
    	Log::debug("getSubmittedCompanyCountForReport Count:".$result);
    	return $result;
    }
    
    public function getCompaniesForBatchForReallocatedToVendor($batchId)
    {
    	$query = Company::query();
    	$result = $query->leftjoin('users','users.id',"=",'companies.user_id')
    			->where('companies.batch_id',"=","{$batchId}")
    			->where('companies.notify',"=","Reassign")
    			->select('companies.*','users.first_name','users.last_name','users.username')
    			->get();
    	return $result;
    }
}