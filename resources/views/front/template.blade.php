<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js">
<!--<![endif]-->

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{ trans('front/site.title') }}</title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">

@yield('head') {!! HTML::style('css/main_front.css') !!}

<!--[if (lt IE 9) & (!IEMobile)]>
			{!! HTML::script('js/vendor/respond.min.js') !!}
		<![endif]-->
<!--[if lt IE 9]>
			{!! HTML::style('https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js') !!}
			{!! HTML::style('https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') !!}
		<![endif]-->

{!!
HTML::style('http://fonts.googleapis.com/css?family=Tangerine:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800')!!}
<!-- {!! HTML::style('http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800') !!}    -->
<!-- {!! HTML::style('http://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic') !!} -->

</head>

<body>

	<!--[if lt IE 8]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->

	<header role="banner">
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button aria-controls="navbar" aria-expanded="false"
						data-target="#navbar" data-toggle="collapse"
						class="navbar-toggle collapsed" type="button">
						<span class="sr-only">Toggle navigation</span> <span
							class="icon-bar"></span> <span class="icon-bar"></span> <span
							class="icon-bar"></span>
					</button>
					<a href="#" class="navbar-brand">{{ trans('front/site.title') }}</a>
				</div>
				<div class="navbar-collapse collapse" id="navbar">
					<div class="col-sm-4 col-lg-6">
						<ul class="nav navbar-nav">
							<li{!! classActivePath('/') !!}>{!! link_to('/',
								trans('front/site.home')) !!}</li> @if(session('statut') ==
							'visitor' || session('statut') == 'user')
							<li{!! classActivePath('contact/create') !!}>{!!
								link_to('contact/create', trans('front/site.contact')) !!}</li>
							@endif
							<li{!! classActiveSegment(1, ['articles', 'blog']) !!}>{!!
								link_to('articles', trans('front/site.blog')) !!}</li>
							@if(Request::is('auth/register'))
							<li class="active">{!! link_to('auth/register',
								trans('front/site.register')) !!}</li>
							@elseif(Request::is('password/email'))
							<li class="active">{!! link_to('password/email',
								trans('front/site.forget-password')) !!}</li> @else
							@if(session('statut') == 'visitor')
							<li{!! classActivePath('auth/login') !!}>{!!
								link_to('auth/login', trans('front/site.connection')) !!}</li>
							@else @if(session('statut') == 'admin')
							<li>{!! link_to_route('admin',
								trans('front/site.administration')) !!}</li>
							@elseif(session('statut') == 'redac')
							<li>{!! link_to('blog', trans('front/site.redaction')) !!}</li>
							@endif
							<li>{!! link_to('auth/logout', trans('front/site.logout')) !!}</li>
							@endif @endif
							<li class="dropdown"><a data-toggle="dropdown"
								class="dropdown-toggle" href="#"><img width="32" height="32"
									alt="{{ session('locale') }}"
									src="{!! asset('img/' . session('locale') . '-flag.png') !!}" />&nbsp;
									<b class="caret"></b></a>
								<ul class="dropdown-menu">
									@foreach ( config('app.languages') as $user) @if($user !==
									config('app.locale'))
									<li><a href="{!! url('language') !!}/{{ $user }}"
										title="Vietnamese"><img width="32" height="32"
											alt="{{ $user }}"
											src="{!! asset('img/' . $user . '-flag.png') !!}"></a></li>
									@endif @endforeach
								</ul></li>
						</ul>
					</div>
					<div class="col-sm-4 col-lg-2">

						<form method="get" action="tim-kiem" class="search" id="frmSearch">
							<input type="text" name="keywork" id="search_keyword"
								placeholder="Tìm Kiếm" class="form-control"> <a
								class="icon_search" href="#" id="btnSearch"></a>
						</form>

					</div>
					<div class="col-sm-2 col-lg-2">
						<div class="auth">
							<div class="">
								<a class="login" onclick="showLogin('/user/login')"
									href="javascript:void(0)">Đăng nhập</a>
							</div>
							<div class="">
								<a class="register" href="/signup/?next=http://fptplay.net">Đăng
									ký</a>
							</div>
						</div>
					</div>
				</div>
				<!--/.nav-collapse -->
			</div>
		</nav>

		@yield('header')

	</header>



	<main role="main" class="container"> @if(session()->has('ok'))
	@include('partials/error', ['type' => 'success', 'message' =>
	session('ok')]) @endif @if(isset($info))
	@include('partials/error',['type' => 'info', 'message' => $info])
	@endif @yield('slide') @yield('main') </main>

	<footer role="contentinfo">
		@yield('footer')
		<p class="text-center">
			<small>Copyright &copy; 2016 by VIETTEL CORP</small>
		</p>
	</footer>

	{!!
	HTML::script('//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js')
	!!}
	<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
	{!! HTML::script('js/plugins.js') !!} {!! HTML::script('js/main.js')
	!!}
	<script>
                                    $(document).ready(function(){
                                        $("#btnSearch").click(function(){
                                            var keyword_s = $("#search_keyword").val();
                                            if(keyword_s.length > 3){
                                                window.location.href = '/tim-kiem/' + keyword_s;
                                            }                                            
                                        });
                                    });

                                    function popupwindow(url, title, w, h) {
                                        var left = (screen.width/2)-(w/2);
                                        var top = (screen.height/2)-(h/2);
                                        return window.open(url, title, 'directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no,scrollbars=no,resizable=no, width='+w+', height='+h+', top='+top+', left='+left);
                                    } 
                                </script>

	<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
	<script>
		(function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
		function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
		e=o.createElement(i);r=o.getElementsByTagName(i)[0];
		e.src='//www.google-analytics.com/analytics.js';
		r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
		ga('create','UA-XXXXX-X');ga('send','pageview');
	</script>

	@yield('scripts')

</body>
</html>