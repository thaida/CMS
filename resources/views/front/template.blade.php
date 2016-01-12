<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="">
<!--<![endif]-->

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>{{ trans('front/site.title') }}</title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">

@yield('head') 
{!! HTML::style('css/common.css') !!}

<!--[if (lt IE 9) & (!IEMobile)]>
			{!! HTML::script('js/vendor/respond.min.js') !!}
		<![endif]-->
<!--[if lt IE 9]>
			{!! HTML::style('https://oss.maxcdn.com/libs/html5shiv/3.7.2/html5shiv.js') !!}
			{!! HTML::style('https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js') !!}
		<![endif]-->

{!! HTML::style('http://fonts.googleapis.com/css?family=Tangerine:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800') !!}
<!-- {!! HTML::style('http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800') !!}    -->
<!-- {!! HTML::style('http://fonts.googleapis.com/css?family=Josefin+Slab:100,300,400,600,700,100italic,300italic,400italic,600italic,700italic') !!} -->

</head>

<body>
<!--[if lt IE 8]>
		<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
	<![endif]-->
<div id="wrapper"> 
  <div id="header">
  		<div class="header-top">   
        	  <div class="wrap-header-top">
                <a class="logo-cj" href="{{url('/') }}">
                	<img src="{{ url('img/cj_logo.png')}}" />
                </a>
                <p class="p-login">
                <!-- 	<a class="lnk-login" href="{{url('auth/login') }}">Đăng nhập</a>
                	&nbsp;&nbsp;|&nbsp;&nbsp;<a class="lnk-login">Đăng ký</a> -->
					@if(session('statut') == 'visitor')
						{!!	link_to('auth/login', trans('front/site.connection'), ['class' => 'lnk-login']) !!}
					@else 
						@if(session('statut') == 'admin')
						
							{!! link_to_route('admin',	trans('front/site.administration'), [], ['class' => 'lnk-login']) !!}
						
						@elseif(session('statut') == 'redac')
						
							{!! link_to('blog', trans('front/site.redaction'), ['class' => 'lnk-login']) !!}
						
						@endif
						&nbsp;&nbsp;|&nbsp;&nbsp;
							{!! link_to('auth/logout', trans('front/site.logout'), ['class' => 'lnk-login']) !!}
						
					@endif  
                </p> 
                <div class="clear"></div>
              </div>  
        </div>
      
        <div class="header-bottom">
        	<div class="wrap-header-bottom">
        	<div class="header-menu" id="top-nav">
            	<ul class="header-ul-parent">
                	<li class="header-li-parent active">
                    	<a class="header-lnk-parent" href="{{url('/') }}">HOME</a>
                    </li>
                    <li class="header-li-parent">
                    	<a class="header-lnk-parent" href="{{url('phim') }}">PHIM</a>
                    	<ul>
                    		<li><a class="header-lnk-parent" href="{{url('film/phim-bo') }}">PHIM BỘ</a></li>
                    		<li><a class="header-lnk-parent" href="{{url('film/phim-le') }}">PHIM LẺ</a></li>
                    	</ul>
                    </li>
                     <li class="header-li-parent">
                    	<a class="header-lnk-parent" href="{{url('music') }}">NHẠC</a>
                    </li>
                     <li class="header-li-parent">
                    	<a class="header-lnk-parent" href="{{url('tv-show') }}">TV SHOW</a>
                    </li>
                     <li class="header-li-parent">
                    	<a class="header-lnk-parent" href="{{url('news') }}">TIN GIẢI TRÍ</a>
                    </li>
                </ul>
            </div>
            
                  <form method="get" action="tim-kiem" class="header-search" id="frmSearch">
	                  <input type="text" id="search_keyword" class="search-input" placeholder="Tìm kiếm...">
	                  <input type="submit" value="" id="btnSearch" class="search-btn">
                  </form>
            
                <div class="clear"></div>
           </div>
        </div>
  </div>
  <!--end-header-->
  <div id="body">
@if(session()->has('ok'))
	@include('partials/error', ['type' => 'success', 'message' => session('ok')]) 
@endif 
@if(isset($info))
	@include('partials/error',['type' => 'info', 'message' => $info])
@endif

	 @yield('slide') 
	 @yield('main') 
	 @yield('tooltip')
       
        
 </div>
  <div id="footer">
  <div class="wrap-footer">
  	@yield('footer')
        <a class="logo-footer">
            <img src="{{ url('img/cj_logo_footer.png')}}" />
        </a>
        <p class="content-footer">
            Tầng 8, Tòa  nhà ngân hàng công đoàn, ngõ 72 Trần Thái Tông, Cầu Giấy, Hầ Nội<br />
            <a href="www.emotv.vn">www.emotv.vn</a> Hotline 19008198
        </p>
   </div>     
  </div>
  <!--end-footer--> 
</div>
<!--end-wrapper-->

	{!!	HTML::script('js/jquery.min.js')	!!}
	<script>window.jQuery || document.write('<script src="js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
	{!! HTML::script('js/plugins.js') !!} 
	{!! HTML::script('js/main.js')	!!}
	
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

	<!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
	
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