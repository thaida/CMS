@extends('back.film.template')

@section('form')
	{!! Form::model($post, ['route' => ['film.update', $post->id], 'method' => 'put',  'files' => true, 'class' => 'form-horizontal panel']) !!}
@stop
