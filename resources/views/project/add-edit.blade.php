@extends('layouts.app')

@section('page-title', trans('app.projects'))

@section('content')

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <h1 class="page-header">
            {{ $edit ? $project->name : trans('app.create_new_project') }}
            <small>{{ $edit ? trans('app.edit_project_details') : trans('app.project_details') }}</small>
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                    <li><a href="{{ route('project.list') }}">@lang('app.projects')</a></li>
                    <li class="active">{{ $edit ? trans('app.edit') : trans('app.create') }}</li>
                </ol>
            </div>
        </h1>
    </div>
</div>

@include('partials.messages')

@if ($edit)
    {!! Form::open(['route' => ['project.update', $project->id], 'method' => 'PUT','files' => true, 'id' => 'project-form']) !!}
@else
    {!! Form::open(['route' => 'project.store', 'id' => 'project-form','files'=>true ]) !!}
@endif

<div class="row"  id="date">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="panel panel-default">
            <div class="panel-heading">@lang('app.project_details_big')</div>
            <div class="panel-body">
                 <div class="form-group">
                    <label for="code">@lang('app.code')<i style="color:red;">*</i></label>
                    <input type="text" class="form-control" id="code" maxlength="8"
                           name="code" placeholder="@lang('app.code')" value="{{ $edit ? $project->code : old('code') }}" @if($edit) readonly="readonly" @endif>
                </div>
                <div class="form-group">
                    <label for="name">@lang('app.name')</label>
                    <input type="text" class="form-control" id="name" maxlength="60"
                           name="name" placeholder="@lang('app.project_name')" value="{{ $edit ? $project->name : old('name') }}">
                </div>
                <div class="form-group" id="no_companies">
                    <label for="No_Companies">@lang('app.number_of_companies')</label>
                    <input type="text" class="form-control" id="No_Companies" maxlength="5" onkeypress="return isNumberKey(event)"
                           name="No_Companies" placeholder="@lang('Number of Companies')" disabled value="{{ $edit ? $project->No_Companies : old('No_Companies') }}">
                </div>
                <div class="form-group">
                    <label for="staff">@lang('app.expected_staff')<i style="color:red;">*</i></label>
                    <input type="text" class="form-control" id="Expected_Staff" maxlength="5" onkeypress="return isNumberKey(event)"
                           name="Expected_Staff" placeholder="@lang('Expected Staff')" value="{{ $edit ? $project->Expected_Staff : old('Expected_Staff') }}">
                </div>
                @if($edit)
                <div class="form-group">
                    <label for="startdate">@lang('app.start_date')<i style="color:red;">*</i></label>
                    <div class="form-group">
							<div class='input-group date' id='start_date1'>
								<input type='text' name="Start_Date" id='Start_Date' value="{{ $edit ? $project->Start_Date : '' }}" class="form-control" />
								<span class="input-group-addon" style="cursor: default;">
                                <span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
					</div>
                </div>
               	<div class="form-group">
                    <label for="expecteddate">@lang('app.expected_date')<i style="color:red;">*</i></label>
                    <div class="form-group">
							<div class='input-group date' id='expected_date1'>
								<input type='text' name="Expected_date" id='Expected_date' value="{{ $edit ? $project->Expected_date : '' }}" class="form-control" />
								<span class="input-group-addon" style="cursor: default;">
                                <span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
                </div>
                @else
                <div class="form-group">
                    <label for="startdate">@lang('app.start_date')<i style="color:red;">*</i></label>
                    <div class="form-group">
							<div class='input-group date' id='start_date'>
								<input type='text' name="Start_Date" id='Start_Date' value="" class="form-control" />
								<span class="input-group-addon" style="cursor: default;">
                                <span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
					</div>
                </div>
               	<div class="form-group">
                    <label for="expecteddate">@lang('app.expected_date')<i style="color:red;">*</i></label>
                    <div class="form-group">
							<div class='input-group date' id='expected_date'>
								<input type='text' name="Expected_date" id='Expected_date' value="" class="form-control" />
								<span class="input-group-addon" style="cursor: default;">
                                <span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
                </div>
                @endif
                <div class="form-group">
				  <label class="control-label" for="upload file">@lang('app.task_brief') @if(!$edit)<i style="color:red;">*</i>@endif</label>
 					<div class="input-group">
				    	<input type='text' name="upload" id='upload' placeholder="choose file" value="{{ $edit ? $project->brief_file : old('brief_file')}}" class="form-control" />
				    	<span class="input-group-btn">
				    	<input type="file" accept=".pdf, .doc, .docx,.xlsx" class="file" id="attachement" name="attachement" style="display: none;" onchange="fileSelected(this)"/>
				    	<button class="btn btn-success" type="button" id="btnAttachment" onclick="openAttachment()">@lang('app.upload_task_brief')</button>
    					</span>
  					</div>
				</div>
                </div>
            </div>
        </div>
    </div>

<div class="row">
	<div class="col-xs-4 col-sm-4 col-md-2 col-lg-2"></div>
    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
        <button type="submit" class="btn btn-primary btn-block" id="btnSubmit">
            <i class="fa fa-save"></i>
            {{ $edit ? trans('app.update_project') : trans('app.create_project') }}
        </button>
    </div>
    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
        <a href="{{ route('project.list') }}" class="btn btn-primary btn-block" id="cancel">
            @lang('app.cancel')
        </a>
    </div>
</div>

@stop
@section('styles')
    {!! HTML::style('assets/css/bootstrap-datetimepicker.min.css') !!}
@stop
@section('scripts')
<script>
var date=new Date();
date.setDate(date.getDate()-1);
$(function () {
    $('#start_date').datetimepicker({
					format: 'YYYY-MM-DD',
					minDate:date
			});
    $('#expected_date').datetimepicker({
		format: 'YYYY-MM-DD',
		minDate:date
			})
	$('#start_date1').datetimepicker({
					format: 'YYYY-MM-DD'
			});
    $('#expected_date1').datetimepicker({
		format: 'YYYY-MM-DD'
			})
});

$(document).ready(function() {
	$("#btnSubmit").click(function(event)
			{
			var startdate=$("#Start_Date").val();
			var enddate=$("#Expected_date").val();
			if(startdate>enddate){
				$('#Expected_date').css('border-color', 'red');
				return false;
	     	}
			else{
					$('#Expected_date').css('border-color', 'green');
					return true;
				}	
	});
});
	function openAttachment() {
	  document.getElementById('attachement').click();
	}

	function fileSelected(input){
	  document.getElementById('upload').value =input.files[0].name
	}
    function isNumberKey(evt)
    {
       var charCode = (evt.which) ? evt.which : event.keyCode
       if (charCode > 31 && (charCode < 48 || charCode > 57))
          return false;
       return true;
    }
    </script>
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! HTML::script('assets/js/as/profile.js') !!}
    @if ($edit)
        {!! JsValidator::formRequest('Vanguard\Http\Requests\Project\UpdateProjectRequest', '#project-form') !!}
    @else
        {!! JsValidator::formRequest('Vanguard\Http\Requests\Project\CreateProjectRequest', '#project-form') !!}
    @endif
@stop

