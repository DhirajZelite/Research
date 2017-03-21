<?php

namespace Vanguard\Repositories\Batch;

use Vanguard\Batch;
use Carbon\Carbon;

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
    public function paginate($perPage, $search = null, $vendorId = null)
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
        $result = $query->select('batches.*', 'projects.code as project_code', 'vendors.name as vendor_name','projects.No_Companies as No_Companies')
        ->paginate($perPage);

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
}