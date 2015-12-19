@extends('back.cat.template')

@section('form')
	{!! Form::model($post, ['route' => ['cat.update', $post->id], 'method' => 'put', 'class' => 'form-horizontal panel']) !!}
@stop
