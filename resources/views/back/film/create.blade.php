@extends('back.film.template')

@section('form')
	{!! Form::open(['url' => 'film', 'method' => 'post', 'class' => 'form-horizontal panel',  'files' => true]) !!}	
@stop
