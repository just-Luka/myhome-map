@extends('errors.layout')
@section('code', '419')
@section('title', 'Session Expired')
@section('message', 'Your session has expired. Please refresh the page and try again.')
@section('extra_action')
    <a href="{{ url()->previous() }}" class="btn btn-ghost">Go Back</a>
@endsection
