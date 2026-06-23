@extends('errors.layout')
@section('code', '410')
@section('title', 'Link Expired')
@section('message'){{ $exception->getMessage() ?: "This link has expired or already been used." }}@endsection
