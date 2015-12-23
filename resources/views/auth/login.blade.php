@extends('front.template') 
@section('main')
<div class="row">
	<div class="box">
		<div class="col-lg-6">

			@if(session()->has('error')) @include('partials/error', ['type' =>
			'danger', 'message' => session('error')]) @endif
			<hr>
			<h2 class="intro-text text-center">{{ trans('front/login.connection')
				}}</h2>
			<hr>
			<p>{{ trans('front/login.text') }}</p>

			{!! Form::open(['url' => 'auth/login', 'method' => 'post', 'role' => 'form']) !!}

			<div class="row">

				{!! Form::control('text', 0, 'log', $errors, trans('front/login.log')) !!} 
				{!! Form::control('password', 0, 'password', $errors, trans('front/login.password')) !!} 
				{!! Form::submit(trans('front/form.send'), ['col-lg-12']) !!} 
				{!!	Form::check('memory', trans('front/login.remind')) !!} 
				{!!	Form::text('address', '', ['class' => 'hpet']) !!}
				<div class="col-lg-12">{!! link_to('password/email', trans('front/login.forget')) !!}</div>
						
			</div>
			{!! Form::close() !!}
			

			{!! Form::open(['url' => 'auth/captchaImage', 'method' => 'post', 'role' => 'form']) !!}
		  
			<div class="text-center">
				<hr>
				<h2 class="intro-text text-center">{{ trans('front/login.register')
					}}</h2>
				<hr>
				<div>	
					{!! Form::control('text', 0, 'mobile', $errors, trans('front/login.mobilephone')) !!} 				
					{!! Form::control('text', 0, 'captcha', $errors, trans('front/login.recaptcha')) !!}
					{!! captcha_img() !!}
				 </div>
				 {!! Form::submit(trans('front/login.getpassword'), ['col-lg-12']) !!} 
				 <!-- <div class="col-lg-12">{!! link_to('auth/getpassword', trans('front/login.getpassword')) !!}</div>
				<p>{{ trans('front/login.register-info') }}</p>
				{!! link_to('auth/register', trans('front/login.registering'),
				['class' => 'btn btn-default']) !!} -->
			</div>
	{!! Form::close() !!}

		</div>
	</div>
</div>
@stop

