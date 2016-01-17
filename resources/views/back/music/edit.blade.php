@extends('back.music.template')

@section('form')
	{!! Form::model($post, ['route' => ['music.update', $post->id], 'method' => 'put',  'files' => true, 'class' => 'form-horizontal panel']) !!}
@stop
