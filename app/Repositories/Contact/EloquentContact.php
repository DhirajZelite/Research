<?php

namespace Vanguard\Repositories\Contact;

use Vanguard\Contact;
use Carbon\Carbon;
use League\Flysystem\Adapter\NullAdapter;
use DB;
use Illuminate\Support\Facades\Log;

class EloquentContact implements ContactRepository
{
 
	public function __construct()
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Contact::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return Contact::where('name', $name)->first();
    }

    /**
     * find by company
     * @param unknown $company_id
     * @return unknown
     */
    public function findByCompany($company_id)
    {
    	return Contact::where('company_id', $company_id)->get();
    }
    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return Contact::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage, $search = null, $companyId = null,$first=null)
    {
        $query = Contact::query();

        if ($search) {
            $query->where(function ($q) use($search) {
                $q->where('first_name', "like", "%{$search}%");
                $q->orWhere('last_name',"like","%{$search}%");
                $q->orWhere('job_title',"like","%{$search}%");
                $q->orWhere('staff_email',"like","%{$search}%");
            });
        }
        if($first)
        {
        	$query->where('first_name',"=","{$first}");
        }
        if ($companyId) {
        	$query->where('company_id', "=", "{$companyId}");
        }
        
        $result = $query->orderBy('created_at', 'DESC')->paginate($perPage);

        if ($search) {
            $result->appends(['search' => $search]);
        }
        if ($companyId) {
        	$result->appends(['company_Id' => $companyId]);
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
        $project = $this->find($id);

        return $project->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return Contact::count();
    }
    
    /**
     * {@inheritdoc}
     */
    public function lists($column = 'code', $key = 'id')
    {
    	return Contact::lists($column, $key);
    }

    /**
     * {@inheritdoc}
     */
    public function newContactsCount()
    {
        return Contact::whereBetween('created_at', [Carbon::now()->firstOfMonth(), Carbon::now()])
            ->count();
    }

    /**
     * {@inheritdoc}
     */
    public function latest($count = 20)
    {
        return Contact::orderBy('created_at', 'DESC')
            ->limit($count)
            ->get();
    }
    
    /** 
     * {@inheritDoc}
     * @see \Vanguard\Repositories\Contact\ContactRepository::duplicat()
     */
    public function duplicate($first = null,$last = null,$jobTitle = null,$email = null,$companyName = null,$website = null,$address =null,$city = null,$state = null,$zipcode =null,$specility = null,$phone = null,$prm = null)
    {	
    	$query = Contact::query();
    	
    	if($companyName)
    	{
    		$query->where('companies.updated_company_name',"like","{$companyName}%");
    	}
    	if($website)
    	{
    		$query->where('companies.website', "=", "{$website}");
    	}
    	if($address)
    	{
    		$query->where('companies.address1', "like", "{$address}%"); 
    	}
    	if($city)
    	{
    		$query->where('companies.city',"like", "{$city}%");
    	}
    	if($state)
    	{
    		$query->where('companies.state',"like","{$state}%");
    	}
    	if($zipcode)
    	{
    		$query->where('companies.zipcode',"=","{$zipcode}");
    	}
    	if($phone)
    	{
    		$query->where('companies.branchNumber',"like","{$phone}%");
    	}
    	if ($first) {
    		$query->where('contacts.first_name', "=", "{$first}");
    	}
    	if($last)
    	{
    		$query->where('contacts.last_name', "=", "{$last}");
    	}
    	if($jobTitle)
    	{
    		$query->where('contacts.job_title',"like","{$jobTitle}%");
    	}
    	if($email)
    	{
    		$query->where('contacts.staff_email', "=" ,"{$email}");
    	}
    	if($specility)
    	{
    		$query->where('contacts.specialization',"like","{$specility}%");
    	}
    	if($prm)
    	{
    		$query->where('companies.prm','like',"{$prm}");
    	}
    	$result = $query
    	->leftjoin('companies', 'companies.id', '=', 'contacts.company_id')
    	->select('companies.*','contacts.*');
    	$result= $query->get();
    	Log::debug("duplicate Sql:". $query->toSql());
    	return $result;
    }

    
    /**
     * {@inheritdoc}
     */
    public function getTotalContactCount($companyId)
    {
    	$query = Contact::query();
    	if ($companyId != 0) {
    		$query->where(function ($q) use($companyId) {
    			$q->where('contacts.company_id', "=", "{$companyId}");
    		});
    	} else {
    		return 0;
    	}
    	$result = $query->leftjoin('companies','companies.id',"=",'contacts.company_id')
    				->where('companies.status',"=","Submitted")				
    				->count();
    	Log::debug("getTotalContactCount Sql:". $query->toSql());
    	return $result;
    }
    
    public function getDataForReport($vendorId = null,$userId = null,$fromDate = null, $toDate = null)
    {
    	$query = Contact::query();
    	if($vendorId)
    	{
    		$query->where('vendors.id',"=","{$vendorId}");
    	}
    	if($userId)
    	{
    		$query->where('users.id',"=","{$userId}");
    	}
    	/*if($fromDate)
    	 {
    	 $query->where('contacts.updated_at',">=", "{$fromDate}");
    	 }*/
    	 if($toDate == null )
    	 {
    	 	$toDate=Carbon::now();//->format('Y-m-d h:M:s');//Carbon::today()
    	 }
    	 else 
    	 {
    	 	$toDate =$toDate . " 23:59:59";
    	 }
    	 
    	if($vendorId== 0 && $userId == 0)
    	{
    		$result=$query
    				->from(DB::raw('(select count(*) no_rows,CONCAT(TIMESTAMPDIFF(HOUR, min(s.updated_at), max(s.updated_at)), ":",MOD(TIMESTAMPDIFF(MINUTE, min(s.updated_at), max(s.updated_at)),60)) as hrs, count(distinct(c.id)) as comp_count,b.vendor_id, c.user_id 
								from contacts s inner join companies c on s.company_id = c.id  inner join batches b on c.batch_id = b.id where s.updated_at >="'.$fromDate.'" and s.updated_at <= "'.$toDate.'" group by b.vendor_id) as rows'))
    							->select('vendors.vendor_code', 'rows.no_rows', 'rows.hrs', 'rows.comp_count','rows.vendor_id')
    							->join('users','users.id',"=","rows.user_id")
    							->rightJoin('vendors','vendors.id',"=","rows.vendor_id")
    							->get();
    	}
    	else {
    		$result=$query
    				->from(DB::raw('(select count(*) no_rows,CONCAT(TIMESTAMPDIFF(HOUR, min(s.updated_at), max(s.updated_at)), ":",MOD(TIMESTAMPDIFF(MINUTE, min(s.updated_at), max(s.updated_at)),60)) as hrs, count(distinct(c.id)) as comp_count,b.vendor_id, c.user_id from
								contacts s inner join companies c on s.company_id = c.id  inner join batches b on c.batch_id = b.id where s.updated_at >="'.$fromDate.'" and s.updated_at <= "'.$toDate.'" group by b.vendor_id, c.user_id) as rows'))
    							->select('vendors.vendor_code', 'users.first_name', 'users.last_name', 'rows.no_rows', 'rows.hrs', 'rows.comp_count','rows.vendor_id','rows.user_id')
    							->join('users','users.id',"=","rows.user_id")
    							->rightJoin('vendors','vendors.id',"=","rows.vendor_id")
    							->get();
    	}
    	return $result;
    }
    
    public function getProcessRecordFromDate($start,$end)
    {
    	$query = Contact::query();
    	
    	$query->where(function ($q) use($start,$end) {
    			$q->where('contacts.updated_at', ">=", "{$start}");
    			$q->where('contacts.updated_at', "<=", "{$end}");
    	});
    	$result = $query->count();
    	Log::debug("getProcessRecordFromDate Sql:". $query->toSql());
    	return $result;
    }
    
    public function getProcessRecordCount($vendorId = null,$userId = null,$fromDate = null, $toDate = null)
    {
    	$query = Contact::query();
    	if($vendorId)
    	{
    		$query->where('users.vendor_id',"=","{$vendorId}");
    	}
    	if($userId)
    	{
    		$query->where('contacts.user_id',"=","{$userId}");
    	}
    	if($fromDate)
    	{
    	 	$query->where('contacts.updated_at',">=", "{$fromDate}");
    	}
    	
    	if($toDate)
    	{
    		$toDate =$toDate . " 23:59:59";
    		$query->where('contacts.updated_at',"<=","{$toDate}");
    	}
    	
    	$result=$query
    			->leftjoin('users', 'users.id', '=', 'contacts.user_id')
    			->leftjoin('companies','companies.id',"=",'contacts.company_id')
    			->where('companies.status',"=","Submitted")
    			->count();
    	Log::debug("getProcessRecordCount Sql:". $query->toSql());
    	Log::debug("getProcessRecordCount Count:".$result);
    	return $result;
    }
    
    public function getEmailRecordCount($vendorId = null,$userId = null,$fromDate = null, $toDate = null)
    {
    	$query = Contact::query();
    	if($vendorId)
    	{
    		$query->where('users.vendor_id',"=","{$vendorId}");
    	}
    	if($userId)
    	{
    		$query->where('contacts.user_id',"=","{$userId}");
    	}
    	if($fromDate)
    	{
    		$query->where('contacts.updated_at',">=", "{$fromDate}");
    	}
    	 
    	if($toDate)
    	{
    		$toDate =$toDate . " 23:59:59";
    		$query->where('contacts.updated_at',"<=","{$toDate}");
    	}
    	 
    	$result=$query
    	->leftjoin('users', 'users.id', '=', 'contacts.user_id')
    	->leftjoin('companies','companies.id',"=",'contacts.company_id')
    	->where('companies.status',"=","Submitted")
    	->where('contacts.staff_email', "!=", " ")
    	->count();
    	Log::debug("getEmailRecordCount Sql:". $query->toSql());
    	Log::debug("getEmailRecordCount Count:".$result);
    	return $result;
    }
    
    /**
     * get total email for reports
     * @param unknown $companyId
     * @return number|unknown
     */
    public function getTotalEmailCount($companyId)
    {
    	$query = Contact::query();
    	if ($companyId != 0) {
    		$query->where(function ($q) use($companyId) {
    			$q->where('contacts.company_id', "=", "{$companyId}");
    			$q->where('contacts.staff_email',"!=", " ");
    		});
    	} else {
    		return 0;
    	}
    	$result = $query
    			->leftjoin('companies','companies.id',"=",'contacts.company_id')
    			->where('companies.status',"=","Submitted")
    			->count();
    	Log::debug("getTotalContactCount Sql:". $query->toSql());
    	return $result;
    }
    
    /**
     * get total no. of staff for perticular batch
     * {@inheritDoc}
     * @see \Vanguard\Repositories\Contact\ContactRepository::getProcessRecordCountForBatch()
     */
    public function getProcessRecordCountForBatch($batch = null,$userId = null,$fromDate = null, $toDate = null)
    {
    	$query = Contact::query();
    	if($batch)
    	{
    		$query->where('companies.batch_id',"=","{$batch}");
    	}
    	if($userId)
    	{
    		$query->where('contacts.user_id',"=","{$userId}");
    	}
    	if($fromDate)
    	{
    		$query->where('contacts.updated_at',">=", "{$fromDate}");
    	}
    	 
    	if($toDate)
    	{
    		$toDate =$toDate . " 23:59:59";
    		$query->where('contacts.updated_at',"<=","{$toDate}");
    	}
    	 
    	$result=$query
    			->leftjoin('users', 'users.id', '=', 'contacts.user_id')
    			->leftjoin('companies','companies.id',"=",'contacts.company_id')
    			->where('companies.status',"=","Submitted")
    			->count();
    	Log::debug("getProcessRecordCountForBatch Sql:". $query->toSql());
    	Log::debug("getProcessRecordCountForBatch Count:".$result);
    	return $result;
    }
    
    /**
     * get total no of email processed for perticular batch
     * @param unknown $batch
     * @param unknown $userId
     * @param unknown $fromDate
     * @param unknown $toDate
     * @return unknown
     */
    public function getEmailRecordCountForBatch($batch = null,$userId = null,$fromDate = null, $toDate = null)
    {
    	$query = Contact::query();
    	if($batch)
    	{
    		$query->where('companies.batch_id',"=","{$batch}");
    	}
    	if($userId)
    	{
    		$query->where('contacts.user_id',"=","{$userId}");
    	}
    	if($fromDate)
    	{
    		$query->where('contacts.updated_at',">=", "{$fromDate}");
    	}
    
    	if($toDate)
    	{
    		$toDate =$toDate . " 23:59:59";
    		$query->where('contacts.updated_at',"<=","{$toDate}");
    	}
    
    	$result=$query
    			->leftjoin('users', 'users.id', '=', 'contacts.user_id')
    			->leftjoin('companies','companies.id',"=",'contacts.company_id')
    			->where('contacts.staff_email', "!=", " ")
    			->where('companies.status',"=","Submitted")
    			->count();
    	Log::debug("getEmailRecordCountForBatch Sql:". $query->toSql());
    	Log::debug("getEmailRecordCountForBatch Count:".$result);
    	return $result;
    }
}