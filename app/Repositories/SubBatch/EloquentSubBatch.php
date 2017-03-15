<?php

namespace Vanguard\Repositories\SubBatch;

use Vanguard\SubBatch;
use Carbon\Carbon;

class EloquentSubBatch implements SubBatchRepository
{
 
	public function __construct()
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return SubBatch::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        return SubBatch::where('name', $name)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return SubBatch::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage, $search = null)
    {
        $query = SubBatch::query();

        if ($search) {
            $query->where(function ($q) use($search) {
                $q->where('subBatches.name', "like", "%{$search}%")
                ->orderBy('created_at', 'desc');
            });
        }


        $result = $query
        ->leftjoin('batches', 'batches.id', '=', 'sub_batches.batch_id')
        ->leftjoin('users', 'users.id', '=', 'sub_batches.user_id')
        ->select('sub_batches.*' ,'batches.name as batch_name', 'users.username', 'sub_batches.seq_no as sub_batch_name')
        ->paginate($perPage);
        
        
        $result = $query->orderBy('created_at', 'desc')->paginate($perPage);

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
        $subBatch = $this->find($id);

        return $subBatch->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return SubBatch::count();
    }
    
    /**
     * {@inheritdoc}
     */
    public function getMaxSeqNo($batchId)
    {
    	return SubBatch::where('batch_id', $batchId)->max('seq_no');
    }

    /**
     * {@inheritdoc}
     */
    public function newSubBatchesCount()
    {
        return SubBatch::whereBetween('created_at', [Carbon::now()->firstOfMonth(), Carbon::now()])
            ->count();
    }

    /**
     * {@inheritdoc}
     */
    public function latest($count = 20)
    {
        return SubBatch::orderBy('created_at', 'DESC')
            ->limit($count)
            ->get();
    }

}