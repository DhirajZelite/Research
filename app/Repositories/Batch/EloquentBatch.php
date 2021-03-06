<?php

namespace Vanguard\Repositories\Batch;

use Vanguard\Batch;
use Carbon\Carbon;
use Kyslik\ColumnSortable\Sortable;
use Illuminate\Support\Facades\Log;

class EloquentBatch implements BatchRepository
{
 
	public function __construct()
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Batch::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return Batch::where('name', $name)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return Batch::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage, $search = null, $vendorId = null, $status = null,$vendorCode =null,$projectcode = null)
    {
        $query = Batch::query();
        
        if ($search) {
            $query->where(function ($q) use($search) {
                $q->where('batches.name', "like", "%{$search}%");
                $q->orwhere('projects.code',"like","%{$search}%");
                $q->orwhere('vendors.name',"like","%{$search}%");
                $q->orwhere('projects.No_Companies',"like","%{$search}%");
            });
        }

        $query = $query
        ->leftjoin('projects', 'projects.id', '=', 'batches.project_id')
        ->leftjoin('vendors', 'vendors.id', '=', 'batches.vendor_id');
        
        if ($vendorId) {
        	$query = $query->where('vendors.id', '=', $vendorId);
        }
        if($vendorCode)
        {
        	$query= $query->where('vendors.vendor_code','=',$vendorCode);
        }
        if($projectcode)
        {
        	$query=$query->where('projects.code','=',$projectcode);
        }
        if ($status) {
        	$query = $query->where('batches.status', '=', $status);
        }
        $result = $query->select('batches.*', 'projects.code as project_code', 'vendors.vendor_code as vendor_code','projects.No_Companies as No_Companies')
        ->sortable()->orderBy('created_at', 'DESC')->paginate($perPage);

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
        $batch = $this->find($id);

        return $batch->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return Batch::count();
    }

    /**
     * {@inheritdoc}
     */
    public function newBatchesCount()
    {
        return Batch::whereBetween('created_at', [Carbon::now()->firstOfMonth(), Carbon::now()])
            ->count();
    }

    /**
     * {@inheritdoc}
     */
    public function latest($count = 20)
    {
        return Batch::orderBy('created_at', 'DESC')
            ->limit($count)
            ->get();
    }
    
    /**
     * {@inheritdoc}
     */
    public function lists($column = 'name', $key = 'id')
    {
    	return Batch::lists($column, $key);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVendorBatches($vendorId)
    {
    	return Batch::where('vendor_id', $vendorId)->lists('name', 'id');
    	
    }
    
    public function getDataForProjectReport($vendor_code = null, $project_code = null,$project_name=null)
    {
    	$query = Batch::query();
    	if($project_code)
    	{
    		$query->where('projects.code',"=","{$project_code}");
    	}
    	if($vendor_code)
    	{
    		$query->where('vendors.vendor_code',"=","{$vendor_code}");
    	}
    	if($project_name)
    	{
    		$query->where('projects.name',"=","{$project_name}");
    	}
    	$result=$query
    	->leftjoin('projects', 'projects.id', '=', 'batches.project_id')
    	->leftjoin('vendors', 'vendors.id', '=', 'batches.vendor_id')
    	->select('vendors.vendor_code','projects.code','projects.name as project_name','batches.company_count as companies','batches.id','batches.name','batches.status');
    	$result= $query->get();
    	Log::debug("getDataForProjectReport Sql:". $query->toSql());
    	return $result;
    }
    
    /**
     * Batch Name Logic
     */
    public function getBatchNameCount($batch = null)
    {
    	$query = Batch::query();
    	if($batch)
    	{
    		$query->where('name',"like","%{$batch}%");
    	}
    	else{
    		return 0;
    	}
    	$result = $query->count();
    	return $result;
    }
    
    public function getCompanyCountBasedOnProject($projectId=null)
    {
    	$query = Batch::query();
    	if($projectId)
    	{
    		$query->where('project_id',"=","{$projectId}");
    	}
    	else{
    		return 0;
    	}
    	$result = $query->sum('company_count');
    	return $result;
    }
    
    public function getProjectBatches($project_id)
    {
//     	$query = Batch::query();
//     	$query->where('project_id',"=",'{$project_id}');
//     	$result = $query->where('status',"=",'Complete')->lists('name', 'id')->toArray();
//     	return $result;
    	return Batch::where('project_id', $project_id)->lists('name', 'id')->toArray();
    }
    
    public function getBatchesForVendor($vendorId)
    {
    	$query = Batch::query();
    	if($vendorId)
    	{
    		$query->where('vendor_id',"=","{$vendorId}");
    	}
    	$result = $query->get();
    	return $result;
    }
    
    public function getBatchesForReallocation($vendorId)
    {
    	return Batch::where('vendor_id', $vendorId)->where('notify',"Reassign")->lists('name', 'id')->toArray();
    }
}