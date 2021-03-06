<?php

namespace Vanguard\Http\Requests\Batch;

use Vanguard\Http\Requests\Request;

class UpdateBatchRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
    	$batch = $this->route('batch');
    	return [
    			'name' => 'unique:batches,name,' . $batch->id,
    			'Target_Date'=>'required|date',
    			'attachement'=>'required|file',
    			'project_id'=>'required|exists:projects,id',
    			'vendor_id'=>'required|exists:vendors,id',
    	];
    }
}
