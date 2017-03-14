<?php

namespace Vanguard\Http\Requests\Project;

use Vanguard\Http\Requests\Request;

class UpdateProjectRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
    	$project = $this->route('project');
    	return [
    			//'name' => 'required|unique:projects,name,' . $project->id,
    			'code' => 'required|unique:projects,code,' . $project->id,
    			'Start_Date' => 'required|date',
        		'Expected_date' => 'required|date',
        		'No_Companies'=>'required|digits_between:1,4',
        		'Expected_Staff'=>'required|digits_between:1,4',
    			'attachement'=>'required|file',
    	];
    }
}
