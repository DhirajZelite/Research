<?php

namespace Vanguard;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class SubBatch extends Model
{
	use Sortable;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sub_batches';

    protected $casts = [
        'removable' => 'boolean'
    ];

    protected $fillable = ['name', 'batch_id','project_id', 'user_id','vendor_id', 'status', 'company_count', 'seq_no'];
    
    protected $sortable=['name', 'batch_id','project_id', 'user_id','vendor_id', 'status', 'company_count', 'seq_no'];
    
}