@extends('back.music.template')

@section('form')
	{!! Form::model($music, ['route' => ['music.update', $music->id], 'method' => 'put',  'files' => true, 'class' => 'form-horizontal panel']) !!}
@stop
