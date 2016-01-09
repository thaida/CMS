@extends('front.template') 
@section('head')
{!! HTML::style('css/owl.carousel.css') !!}
@stop

@section('slide')

<!-- SlidesJS Required: These styles are required if you'd like a responsive slideshow -->
{!! HTML::style('css/slide.css') !!}
<!-- SlidesJS Required: -->
<!-- SlidesJS Required: Start Slides -->
<!-- The container is used to define the width of the slideshow -->

<div class="banner-top">
	<div class="slider-banner">
		<a class="slider-lnk">
			<img src="http://c1.staticflickr.com/5/4147/5087404401_d24513119a_n.jpg" />
		</a>
		<a class="slider-lnk">
			<img src="http://c1.staticflickr.com/5/4132/5087985262_77003d3f70_n.jpg" />
		</a>
		<a class="slider-lnk">
			<img src="http://vtv1.vcmedia.vn/Uploaded/lanchi/2013_06_07/my%20khe%206.jpg" />
		</a>
		<a class="slider-lnk">
			<img src="https://vtv1.vcmedia.vn/thumb_w/650/Uploaded/lanchi/2013_06_07/my%20khe%205_635062082144361668.png" />
		</a>
	</div>
</div>

@stop

@section('main')

<!-- PHIM -->
<div id="cj-slider01" class="show-cj show-cj-film">
	<h3 class="show-h3">
		<a href="{{ url('phim')}}">PHIM</a>
	</h3>
	<div class="wrap-show-cj">
		<div class="show-cj-slider">			    	
	  	@foreach ($films as $film)
			<div class="show-cj-item">
				<a class="cj-item-lnk" href="{{$film_url.$film->slug}}">
					<img title="{{$film->title}}"  src="{{$url.$film->poster_path}}?w=300&h=430&crop-to-fit" />
				</a>
				<div class="cj-info">
					<h3 class="cj-h3">
						<a href="{{$film_url.$film->slug}}">{{$film->title}}</a>
						<span class="sp-right">2001</span>
					</h3>
					<p class="cj-p-film">
						<span class="sp-left">Fire With Fire</span>
						<span class="sp-right">{{$film->running_time}}</span>                                
					</p>
					<p class="cj-p-type">
						<span class="sp-left">Bản đẹp&nbsp;|&nbsp;Thuyết minh</span>
					</p>
				</div>
			</div>
		@endforeach
		</div>
	</div>
</div>
<!-- END PHIM -->
<!-- NHAC -->
<div id="cj-slider02" class="show-cj show-cj-music">
        	<h3 class="show-h3">
            	<a>NHẠC</a>
            </h3>
            <div class="wrap-show-cj">
            	<div class="show-cj-slider">
                	<div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_08.jpg" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1</a>
                                </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                    <div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_09.jpg" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1</a>
                            </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                    <div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_10.jpg" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1</a>
                            </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                    <div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_12.png" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1 Ở Nhà một mình 1</a>
                            </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
     <!-- TVSHOW   --> 
<div id="cj-slider03" class="show-cj show-cj-tv">
        	<h3 class="show-h3">
            	<a>TV SHOWS</a>
            </h3>
            <div class="wrap-show-cj">
            	<div class="show-cj-slider">
                	<div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_08.jpg" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1</a>
                                </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                    <div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_09.jpg" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1</a>
                            </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                    <div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_10.jpg" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1</a>
                            </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                    <div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_12.png" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1 Ở Nhà một mình 1</a>
                            </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<!-- GIAI TRI -->
<div id="cj-slider04" class="show-cj show-cj-giaitri">
        	<h3 class="show-h3">
            	<a>GIẢI TRÍ</a>
            </h3>
            <div class="wrap-show-cj">
            	<div class="show-cj-slider">
                	<div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_08.jpg" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1</a>
                                </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                    <div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_09.jpg" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1</a>
                            </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                    <div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_10.jpg" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1</a>
                            </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                    <div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_12.png" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a>Ở Nhà một mình 1 Ở Nhà một mình 1</a>
                            </h3>
                            <p class="cj-p-type">
                            	<span class="sp-left">Nam cường ft. Sơn ca</span>
                                <span class="sp-right">123</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
	@stop
	
	
	
	@section('scripts')
	<!-- SlidesJS Required: Link to jquery.slides.js -->
	
	
	{!!	HTML::script('js/owl.carousel.min.js') !!}
	<!-- End SlidesJS Required -->
	
	<script>
   
    $(document).ready(function(){
    	$('.slider-banner').owlCarousel({
    	loop:true,
    	margin:10,
    	nav:true,
    	items:1
    	})
    	$('#cj-slider01 .show-cj-slider').owlCarousel({
    	loop:true,
    	nav:true,
    	margin:26,
    	items:4
    	})
    	
    	$('#cj-slider02 .show-cj-slider').owlCarousel({
    	loop:true,
    	nav:true,
    	margin:26,
    	items:4
    	})
    	
    	$('#cj-slider03 .show-cj-slider').owlCarousel({
    	loop:true,
    	nav:true,
    	margin:26,
    	items:4
    	})
    	
    	$('#cj-slider04 .show-cj-slider').owlCarousel({
    	loop:true,
    	nav:true,
    	margin:26,
    	items:4
    	})
    });
  </script>
	<!-- End SlidesJS Required -->
	@stop