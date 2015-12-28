@extends('back.banner.template')

@section('form')
	{!! Form::open(['url' => 'banner', 'method' => 'post', 'class' => 'form-horizontal panel',  'files' => true]) !!}	
@stop
