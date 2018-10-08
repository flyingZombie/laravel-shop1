@extends('layouts.app')
@section('title', 'New Shipping Address')

@section('content')

<div class="row">
<div class="col-lg-10 col-lg-offset-1">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h2 class="text-center">New Shipping Address</h2>
		</div>
	</div>
	<div class="panel-body">

		@if (count($errors) > 0) 
		  <div class="alert alert-danger">
		  	<h4>Error happened: </h4>
		  	<ul>
		  		@foreach ($errors->all() as $error)
                <li><i class="glyphicon glyphicon-remove"></i>{{ $error }}</li>
		  		@endforeach
		  	</ul>
		  </div>
		@endif
		

	<form class="form-horizontal" role="form" action="{{ route('user_addresses.store') }}" method="post">
        <!-- 引入 csrf token 字段 -->
        {{ csrf_field() }}

        <div class="form-group">
    	<label class="control-label col-sm-2">Address</label>
        	<div class="col-sm-9">
          	<user-addresses-create-and-edit/>
        	</div>
		</div>

        <div class="form-group">
          <label class="control-label col-sm-2">Contact name</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="contact_name" value="{{ old('contact_name', $address->contact_name) }}">
          </div>
        </div>
        <div class="form-group">
          <label class="control-label col-sm-2">Contact phone</label>
          <div class="col-sm-9">
            <input type="text" class="form-control" name="contact_phone" value="{{ old('contact_phone', $address->contact_phone) }}">
          </div>
        </div>
		
		

        <div class="form-group text-center">
          <button type="submit" class="btn btn-primary">提交</button>
        </div>
		


        
      </form>
	</div>
</div>
</div>
</div>
@endsection