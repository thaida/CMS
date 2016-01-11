@extends('front.template') 
@section('head')
{!! HTML::style('css/owl.carousel.css') !!}
@stop

@section('slide')

<!-- SlidesJS Required: These styles are required if you'd like a responsive slideshow -->
{!! HTML::style('css/slide.css') !!}
<!-- SlidesJS Required: -->
{!! HTML::style('css/jquery.bxslider.css') !!}
<!-- SlidesJS Required: Start Slides -->
<!-- The container is used to define the width of the slideshow -->
<div class="banner-top">
	<ul class="bxslider">
	  <li><img src="{{ url('img/temp_01.jpg') }}" title="Funky roots" /></li>
	  <li><img src="{{ url('img/temp_02.jpg') }}" title="The long and winding road" /></li>
	</ul>
</div>

@stop

@section('main')

<!-- PHIM -->
<div id="cj-slider01" class="show-cj show-cj-film">
	<h3 class="show-h3">
		<a href="{{ url('phim')}}">PHIM</a>
	</h3>
	<ul class="bxslider-01">
	@foreach ($films as $film)
		<li>		
			<a href="{{$film_url.$film->slug}}" title="{{$film->title}}">
				<img title="{{$film->title}}"  src="{{$url.$film->poster_path}}?w=300&h=430&crop-to-fit" />
			</a>
			<div class="cj-info">
				<h3 class="cj-h3">
					<a href="{{$film_url.$film->slug}}" title="{{$film->title}}">{{$film->title}}</a>
					<span class="sp-right">{{ date('Y', strtotime($film->release_date)) }}</span>
				</h3>
				<p class="cj-p-film">
					<span class="sp-left">Fire With Fire</span>
					<span class="sp-right">{{$film->running_time}}</span>                                
				</p>
				<p class="cj-p-type">
					<span class="sp-left">Bản đẹp&nbsp;|&nbsp;Thuyết minh</span>
				</p>
			</div>
		</li>
	@endforeach
	</ul> 	
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
					<img src="http://192.168.202.87/img/image/userfiles/temp_08.jpg?w=230&crop-to-fit" />
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
					<img src="http://192.168.202.87/img/image/userfiles/temp_08.jpg?w=230&crop-to-fit" />
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
					<img src="http://192.168.202.87/img/image/userfiles/temp_08.jpg?w=230&crop-to-fit" />
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
					<img src="http://192.168.202.87/img/image/userfiles/temp_08.jpg?w=230&crop-to-fit" />
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
                        	<img src="http://192.168.202.87/img/image/userfiles/temp_08.jpg?w=230&crop-to-fit" />
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
                        	<img src="http://192.168.202.87/img/image/userfiles/temp_08.jpg?w=230&crop-to-fit" />
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
                        	<img src="http://192.168.202.87/img/image/userfiles/temp_08.jpg?w=230&crop-to-fit" />
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
                        	<img src="http://192.168.202.87/img/image/userfiles/temp_08.jpg?w=230&crop-to-fit" />
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
                        	<img src="http://192.168.202.87/img/image/userfiles/temp_09.jpg?w=230&crop-to-fit" />
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
                        	<img src="http://192.168.202.87/img/image/userfiles/temp_09.jpg?w=230&crop-to-fit" />
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
                        	<img src="http://192.168.202.87/img/image/userfiles/temp_09.jpg?w=230&crop-to-fit" />
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
                        	<img src="http://192.168.202.87/img/image/userfiles/temp_09.jpg?w=230&crop-to-fit" />
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
	{!!	HTML::script('js/jquery.bxslider.min.js') !!}

	<!-- End SlidesJS Required -->
	
	<script>
  

    $('.bxslider').bxSlider({
    	  mode: 'fade',
    	  width: 1000,
    	  height: 400,
    	  autoControls: true,
    	  auto: true,
    	});
    $('.bxslider-01').bxSlider({
	  	minSlides: 2,
	  	maxSlides: 4,
	  	slideWidth: 240,
	    slideMargin: 10,
	    touchEnabled: true
  	});
  </script>
	<!-- End SlidesJS Required -->
	@stop