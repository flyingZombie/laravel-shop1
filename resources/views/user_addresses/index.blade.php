
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
							<a class="btn btn-primary" href="{{ route('user_addresses.edit', ['user_address' => $address->id ]) }}">Edit</a>
							
							<button type="button" class="btn btn-danger btn-del-address" data-id="{{ $address->id}}">Delete</button>

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
@section('scriptsAfterJs')
<script>
$(document).ready(function () {
	$('.btn-del-address').click(function () {
		var id = $(this).data('id');
		swal({
			title: 'Are you sure to delete this address?',
			icon: 'warning',
			buttons: ['Cancel', 'Confirm'],
			dangerMode: true,
		})
		.then(function (willDelete) {
			if (!willDelete) {
				return;
			}
			axios.delete('/user_addresses/'+id)
			.then(function () {
				location.reload();
			})
		});
	});
});
</script>
@endsection
