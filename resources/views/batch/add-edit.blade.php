@extends('layouts.app')

@section('page-title', trans('app.batches'))

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            {{ $edit ? $batch->name : trans('app.create_new_batch') }}
            <small>{{ $edit ? trans('app.edit_batch_details') : trans('app.batch_details') }}</small>
            <div class="pull-right">
                <ol class="breadcrumb">
                    <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                    <li><a href="{{ route('batch.list') }}">@lang('app.batches')</a></li>
                    <li class="active">{{ $edit ? trans('app.edit') : trans('app.create') }}</li>
                </ol>
            </div>
        </h1>
    </div>
</div>

@include('partials.messages')

@if ($edit)
    {!! Form::open(['route' => ['batch.update', $batch->id], 'method' => 'PUT', 'id' => 'batch-form']) !!}
@else
    {!! Form::open(['route' => 'batch.store', 'id' => 'batch-form']) !!}
@endif

<div class="row">
    <div class="col-lg-6 col-md-12 col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">@lang('app.batch_details_big')</div>
            <div class="panel-body">
            	<div class="form-group">
                    <label for="project_id">@lang('app.project_name')</label>
                    {!! Form::select('project_id', $projects, $edit ? $batch->project_id : '',
                        ['class' => 'form-control', 'id' => 'project_id']) !!}
                </div>
                <div class="form-group">
                    <label for="vendor_id">@lang('app.vendor_name')</label>
                    {!! Form::select('vendor_id', $vendors, $edit ? $batch->vendor_id : '',
                        ['class' => 'form-control', 'id' => 'vendor_id']) !!}
                </div>
                <div class="form-group">
                    <label for="name">@lang('app.name')</label>
                    <input type="text" class="form-control" id="name"
                           name="name" placeholder="@lang('app.batch_name')" value="{{ $edit ? $batch->name : old('name') }}">
                </div>
		      <div class="form-group">
                    <label for="startdate">@lang('app.target_date')</label>
                    <div class="form-group">
							<div class='input-group date'>
								<input type='text' name="Target_Date" id='Target_Date' value="{{ $edit ? $batch->Target_Date : '' }}" class="form-control" />
								<span class="input-group-addon" style="cursor: default;">
                                <span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
					</div>
                </div>
                 <div class="form-group">
                    <label for="description">@lang('app.description')</label>
                    <input type="text" class="form-control" id="description"
                           name="description" placeholder="@lang('app.description')" value="{{ $edit ? $batch->description : old('description') }}">
                </div>
                 <div class="form-group">
				  <label class="control-label" for="upload file">@lang('app.upload')</label>
 					<div class="input-group">
				    	<input type='text' name="upload" id='upload' placeholder="@lang('app.select')" class="form-control" />
				    	<span class="input-group-btn">
				    	<button class="btn btn-success" type="button">@lang('app.upload')</button>
    					</span>
  					</div>
				</div>
                <!-- <div class="form-group">
                    <label for="description">@lang('app.description')</label>
                    <textarea name="description" id="description" class="form-control">{{ $edit ? $batch->description : old('description') }}</textarea>
                </div>
                 -->
                </div>
            </div>
        </div>
    </div>

<div class="row">
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary btn-block">
            <i class="fa fa-save"></i>
            {{ $edit ? trans('app.update_batch') : trans('app.create_batch') }}
        </button>
    </div>
</div>

@stop
@section('styles')
    {!! HTML::style('assets/css/bootstrap-datetimepicker.min.css') !!}
@stop
@section('scripts')
    @if ($edit)
        {!! JsValidator::formRequest('Vanguard\Http\Requests\Batch\UpdateBatchRequest', '#batch-form') !!}
    @else
        {!! JsValidator::formRequest('Vanguard\Http\Requests\Batch\CreateBatchRequest', '#batch-form') !!}
    @endif
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! HTML::script('assets/js/as/profile.js') !!}
@stop