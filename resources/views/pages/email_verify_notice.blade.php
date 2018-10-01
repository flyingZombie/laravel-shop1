@extends('layouts.app')
@section('title', 'Reminder')

@section('content')

<div class="panel panel-default">
  <div class="panel-heading">
  	Reminder
  </div>
  <div class="panel-body text-center">
	<h1>Please verify email</h1>
	<a class="btn btn-primary" href="{{ route('email_verification.send') }}">Please send verification email again!</a>
  </div>
</div>

@endsection
