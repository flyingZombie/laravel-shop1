
@extends('layouts.app'); 
@section('title', 'list of User addresses'); 

@section('content')

<div class="row">
	<div class="col-lg-10 col-lg-offset-1">
		<div class="panel panel-default">
			<div class="panel-heading">
				List of user addresses
				<a href="{{ route('user_addresses.create') }}" class="pull-right">New Shipping Address</a>
			</div>
			<div class="panel-body">
				
			<table class="table table-bordered table-striped">
				<thead>
				<tr>

					<th>Receiver</th>
					<th>Address</th>
					<th>Postcode</th>
					<th>Phone</th>
					<th>Action</th>

				</tr>
				</thead>
				<tbody>
				@foreach($addresses as $address)
					<tr>
						<td>{{ $address->contact_name }}</td>
						<td>{{ $address->full_address }}</td>
						<td>{{ $address->postcode }}</td>
						<td>{{ $address->contact_phone }}</td>
						<td>
							<button class="btn btn-primary">Edit</button>
							<button class="btn btn-danger">Delete</button>
						</td>
					</tr>
				@endforeach
				</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection
