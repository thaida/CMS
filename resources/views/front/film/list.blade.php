@extends('front.template')
@section('head')
	{!! HTML::style('css/jquery.bxslider.css') !!} 
@stop
@section('slide')
<!-- The container is used to define the width of the slideshow -->
<ul class="bxslider">
	  <li><img
		src="https://static.fptplay.net/static/img/share/video/06_01_2016/imgpsh_fullsize06-01-2016_15g48-43.jpg?w=1000&h=400&mode=scale"
		alt="Photo by: Missy S Link: http://www.flickr.com/photos/listenmissy/5087404401/">
		</li>
	  <li><img src="https://static.fptplay.net/static/img/share/video/30_12_2015/x19qj3tu30-12-2015_22g27-52.jpg?w=1000&h=400&mode=scale"></li>
		<li>
		<img src="https://static.fptplay.net/static/img/share/video/30_12_2015/hxq787qa30-12-2015_22g21-52.jpg?w=1000&h=400&mode=scale">
		</li>
		<li>
		<img src="https://static.fptplay.net/static/img/share/video/30_12_2015/0i0x4kly30-12-2015_22g17-56.jpg?w=1000&h=400&mode=scale" >
		</li>
</ul>
	

@stop 

@section('main')
    <!-- PHIM XEM NHIEU NHAT -->
<div id="cj-slider01" class="show-cj show-cj-film">
	<h3 class="show-h3">
		<a>PHIM XEM NHIỀU NHẤT</a>
	</h3>
	<ul class="bxslider-01">
	  @foreach ($films_most_view as $film)
		<li>		
			<a href="{{$film_url.$film->slug}}" title="{{$film->title}}">
				<img title="{{$film->title}}"  src="{{$img_url.$film->poster_path}}?w=300&h=430&crop-to-fit" />
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

@if(isset($films))
	<!-- PHIM BY CAT -->
	<div id="cj-slider02" class="show-cj show-cj-film">
        	<h3 class="show-h3" >
            	<a title="{{ $films->get(0)->subCat}}"	href="{!! url('phim/'. $films->get(0)->catSlug) !!}">{{ $films->get(0)->subCat}}</a>
            </h3>
           <ul class="bxslider-02">
           	@foreach ($films as $film)
				<li>
					<a href="{{$film_url.$film->slug}}" title="{{$film->title}}">
						<img title="{{$film->title}}"  src="{{$img_url.$film->poster_path}}?w=300&h=430&crop-to-fit" />
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
@endif


@stop


@section('scripts')
<!-- SlidesJS Required: Link to jquery.slides.js -->
{!! HTML::script('js/jquery.bxslider.min.js')	!!}
<!-- End SlidesJS Required -->
<!-- SlidesJS Required: Initialize SlidesJS with a jQuery doc ready -->
<script>
    $(function() {
      $('#slides').slidesjs({
        width: 940,
        height: 400,
        play: {
          active: true,
          auto: true,
          interval: 4000,
          swap: true
        }
      });
    });

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
    $('.bxslider-02').bxSlider({
	  	minSlides: 2,
	  	maxSlides: 4,
	  	slideWidth: 240,
	    slideMargin: 10,
	    touchEnabled: true
  	});
  </script>
<!-- End SlidesJS Required -->


@stop