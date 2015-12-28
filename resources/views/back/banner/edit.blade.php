@extends('back.banner.template')

@section('form')
	{!! Form::model($post, ['route' => ['banner.update', $post->id], 'method' => 'put',  'files' => true, 'class' => 'form-horizontal panel']) !!}
@stop
