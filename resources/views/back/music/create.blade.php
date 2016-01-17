@extends('back.music.template')

@section('form')
	{!! Form::open(['url' => 'music', 'method' => 'post', 'class' => 'form-horizontal panel',  'files' => true]) !!}	
@stop
