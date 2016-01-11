@extends('front.template') 
@section('main')
<div class="row">
	<div class="box">
		<div class="col-lg-12">
			@if(session()->has('error')) 
				@include('partials/error', ['type' => 'danger', 'message' => session('error')]) 
			@endif
			<hr>
			<h2 class="intro-text text-center">{{ trans('front/login.connection') }}</h2>
			<hr>
			<!-- <p>{{ trans('front/login.text') }}</p> -->
			<!-- <div class="container">
				<form class="form-signin">
					<h2 class="form-signin-heading">Please sign in</h2>
					<label class="sr-only" for="inputEmail">Email address</label> <input
						type="email" autofocus="" required="" placeholder="Email address"
						class="form-control" id="inputEmail"> <label class="sr-only"
						for="inputPassword">Password</label> <input type="password"
						required="" placeholder="Password" class="form-control"
						id="inputPassword">
					<div class="checkbox">
						<label> <input type="checkbox" value="remember-me"> Remember me
						</label>
					</div>
					<button type="submit" class="btn btn-lg btn-primary btn-block">Sign
						in</button>

				</form>
			</div> -->
<style type="text/css">

.login-box{
  position:relative;
  margin: 10px auto;
  width: 500px;
  height: 380px;
  background-color: #fff;
  padding: 10px;
  border-radius: 3px;
  -webkit-box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.33);
-moz-box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.33);
box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.33);
}
.lb-header{
  position:relative;
  color: #00415d;
  margin: 5px 5px 10px 5px;
  padding-bottom:10px;
  border-bottom: 1px solid #eee;
  text-align:center;
  height:28px;
}
.lb-header a{
  margin: 0 25px;
  padding: 0 20px;
  text-decoration: none;
  color: #666;
  font-weight: bold;
  font-size: 15px;
  -webkit-transition: all 0.1s linear;
  -moz-transition: all 0.1s linear;
  transition: all 0.1s linear;
}
.lb-header .active{
  color: #029f5b;
  font-size: 18px;
}
.social-login{
  position:relative;
  float: left;
  width: 100%;
  height:auto;
  padding: 10px 0 15px 0;
  border-bottom: 1px solid #eee;
}
.social-login a{
  position:relative;
  float: left;
  width:calc(40% - 8px);
  text-decoration: none;
  color: #fff;
  border: 1px solid rgba(0,0,0,0.05);
  padding: 12px;
  border-radius: 2px;
  font-size: 12px;
  text-transform: uppercase;
  margin: 0 3%;
  text-align:center;
}
.social-login a i{
  position: relative;
  float: left;
  width: 20px;
  top: 2px;
}
.social-login a:first-child{
  background-color: #49639F;
}
.social-login a:last-child{
  background-color: #DF4A32;
}
.email-login,.email-signup{
  position:relative;
  float: left;
  width: 100%;
  height:auto;
  margin-top: 20px;
  text-align:center;
}
.u-form-group{
  width:100%;
  margin-bottom: 10px;
}
.u-form-group input[type="email"],
.u-form-group input[type="password"]{
  width: calc(50% - 22px);
  height:45px;
  outline: none;
  border: 1px solid #ddd;
  padding: 0 10px;
  border-radius: 2px;
  color: #333;
  font-size:0.8rem;
  -webkit-transition:all 0.1s linear;
  -moz-transition:all 0.1s linear;
  transition:all 0.1s linear;
}
.u-form-group input:focus{
  border-color: #358efb;
}
.u-form-group button{
  width:50%;
  background-color: #1CB94E;
  border: none;
  outline: none;
  color: #fff;
  font-size: 14px;
  font-weight: normal;
  padding: 14px 0;
  border-radius: 2px;
  text-transform: uppercase;
}
.forgot-password{
  width:50%;
  text-align: left;
  text-decoration: underline;
  color: #888;
  font-size: 0.75rem;
}
</style>
<div class="login-box">
    <div class="lb-header">
      <a href="#" class="active" id="login-box-link">Đăng nhập</a>
      <a href="#" id="signup-box-link">Đăng ký</a>
    </div>
   
    {!! Form::open(['url' => 'auth/login', 'method' => 'post', 'role' => 'form', 'class' => 'email-login']) !!}
      <div class="u-form-group">
        <input type="email" placeholder="Mobile" id="log" name="log"/>
      </div>
      <div class="u-form-group">
        <input type="password" placeholder="Password" name="password" id="password"/>
      </div>
      <div class="u-form-group">
        <button>Log in</button>
      </div>
      <div class="u-form-group">
        <a href="#" class="forgot-password">Forgot password?</a>
      </div>
   	{!! Form::close() !!}
   	
   	{!! Form::open(['url' => 'auth/register', 'method' => 'post', 'role' => 'form', 'class' => 'email-signup']) !!}
  
      <div class="u-form-group">
        <input type="email" placeholder="Mobile" name="mobile" id="mobile"/>
      </div>
      <div class="u-form-group">
        <input type="password" placeholder="Captcha" id="captcha" name="captcha" />
      </div>
      <div class="u-form-group">
      {!! captcha_img()!!}
        <!-- <input type="password" placeholder="Confirm Password"/> -->
        <!-- {!! Form::control('text', 6, 'captcha', $errors, trans('front/register.captcha'), null, [trans('front/register.warning'), trans('front/register.warning-password')]) !!} --> 
      </div>
      <div class="u-form-group">
        <button>Sign Up</button>
      </div>
    {!! Form::close() !!}
  </div>
			<!-- {!! Form::open(['url' => 'auth/login', 'method' => 'post', 'role' => 'form']) !!}

			<div class="row">

				{!! Form::control('text', 6, 'log', $errors, trans('front/login.log')) !!} 
				{!! Form::control('password', 6, 'password', $errors, trans('front/login.password')) !!} 
				{!!	Form::submit(trans('front/form.send'), ['col-lg-12']) !!} 
				{!!	Form::check('memory', trans('front/login.remind')) !!}
				{!! Form::text('address', '', ['class' => 'hpet']) !!}
				<div class="col-lg-12">{!! link_to('password/email', trans('front/login.forget')) !!}</div>

			</div>

			{!! Form::close() !!}
 -->
			<div class="text-center">
				<!-- <hr>
				<h2 class="intro-text text-center">{{ trans('front/login.register')
					}}</h2>
				<hr> -->
			<!-- 	<p>{{ trans('front/login.register-info') }}</p> -->
				{!! link_to('auth/register', trans('front/login.registering'),
				['class' => 'btn btn-default']) !!}
			</div>

		</div>
	</div>
</div>
{!!	HTML::script('js/jquery.min.js')	!!}
<script>
$(".email-signup").hide();
$("#signup-box-link").click(function(){
  $(".email-login").fadeOut(100);
  $(".email-signup").delay(100).fadeIn(100);
  $("#login-box-link").removeClass("active");
  $("#signup-box-link").addClass("active");
});
$("#login-box-link").click(function(){
  $(".email-login").delay(100).fadeIn(100);;
  $(".email-signup").fadeOut(100);
  $("#login-box-link").addClass("active");
  $("#signup-box-link").removeClass("active");
});
</script>
<style>
/* .form-signin {
	margin: 0 auto;
	max-width: 330px;
	padding: 15px;
}

.form-signin .form-signin-heading, .form-signin .checkbox {
	margin-bottom: 10px;
}

.form-signin input[type="password"] {
	border-top-left-radius: 0;
	border-top-right-radius: 0;
	margin-bottom: 10px;
}

.form-signin .form-control {
	box-sizing: border-box;
	font-size: 16px;
	height: auto;
	padding: 10px;
	position: relative;
} */
</style>
@stop
