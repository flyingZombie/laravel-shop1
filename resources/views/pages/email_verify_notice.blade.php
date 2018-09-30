@extends('layouts.app')
@section('title', 'Reminder')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">
  	Reminder
  </div>
  <div class="panel-body text-center">
	<h1>Please verify email</h1>
	<a href=" {{ route('root') }}" class="btn btn-primary">Return to home page</a>
  </div>
</div>

@endsection
