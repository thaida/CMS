@extends('back.subcat.template')

@section('form')
	{!! Form::model($post, ['route' => ['subcat.update', $post->id], 'method' => 'put', 'class' => 'form-horizontal panel']) !!}
@stop
