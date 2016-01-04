@extends('front.template')
@section('head')
{!! HTML::style('css/jquery.bxslider.css') !!} 
@stop
@section('slide')

<!-- SlidesJS Required: These styles are required if you'd like a responsive slideshow -->
{!! HTML::style('css/slide.css') !!}
<!-- SlidesJS Required: -->
<!-- SlidesJS Required: Start Slides -->
<!-- The container is used to define the width of the slideshow -->
{!! HTML::style('css/jquery.bxslider.css') !!}
<div id="slides">
	<img
		src="http://img2.wikia.nocookie.net/__cb20120229020811/marveldatabase/images/1/1a/The_Avengers_(film)_poster_011.jpg"
		alt="Photo by: Missy S Link: http://www.flickr.com/photos/listenmissy/5087404401/">
	<img src="https://www.cinemasterpieces.com/62014/cineam15.jpg"
		alt="Photo by: Daniel Parks Link: http://www.flickr.com/photos/parksdh/5227623068/">
	<img
		src="http://www.btchflcks.com/wp-content/uploads/2013/10/chloe_moretz_in_carrie_movie-HD.jpg"
		alt="Photo by: Mike Ranweiler Link: http://www.flickr.com/photos/27874907@N04/4833059991/">
</div>

@stop 

@section('main')
    <!-- PHIM XEM NHIEU NHAT -->
	<div class="row rowbox">
		<div class="col-xs-6 col-sm-5 col-md-4 box_header Regular">
			<a class="category_title" title="Phim xem nhiều nhất"	href="{!! url('film/phim-bo') !!}">
				<span class="pull-left">{{ 'Phim bộ xem nhiều nhất' }}</span>
			</a>
			<a class="pull-left btn_arrow_right" title="Phim xem nhiều nhất" href="{!! url('film/phim-bo') !!}"></a>
		</div>
		
		<div class="contentbox">
			<ul class="bxslider">
			
			 @foreach ($films_most_view as $film)
		  		<li>
		  			<div class="boxTitle">
		  			<a href="{{$film_url.$film->slug}}">
						<div class="caption">
							<h4>{{$film->title}}</h4>
							<p>Đạo diễn: <br/>
							Diễn viên: <br/>
							Thể loại: <br />
							<p>Thời lượng: </p>
							<p class="">{!! $film->summary !!}</p>
						</div>
			  		
			  			<img title="{{$film->title}}"  src="{{$img_url.$film->poster_path}}?w=250&h=350&crop-to-fit" />
			  		</a>
			  		</div>
		  		</li>
			
		  	@endforeach
			</ul>
		</div>
	</div>
	<!-- END XEM NHIEU NHAT -->
@if(isset($films))
	<!-- PHIM TAM LY -->
	<div class="row rowbox">
		<div class="col-xs-6 col-sm-5 col-md-4 box_header Regular">
			<a class="category_title" title="{{ $films->get(0)->subCat}}"	href="{!! url('phim/'. $films->get(0)->catSlug) !!}">
				<span class="pull-left">{{ $films->get(0)->subCat}}</span>
			</a>
			<a class="pull-left btn_arrow_right" title="{{ $films->get(0)->subCat}}" href="{!! url('film/'.$films->get(0)->catSlug) !!}"></a>
		</div>
		<div class="contentbox">
			<ul class="bxslider">
			 @foreach ($films as $film)
		  		<li>
		  			<div class="boxTitle">
		  			<a href="{{$film_url.$film->slug}}">
						<div class="caption">
							<h4>{{$film->title}}</h4>
							<p>Đạo diễn: <br/>
							Diễn viên: <br/>
							Thể loại: <br />
							<p>Thời lượng: </p>
							<p class="">{!! $film->summary !!}</p>
						</div>
			  		
			  			<img title="{{$film->title}}"  src="{{$img_url.$film->poster_path}}?w=250&h=350&crop-to-fit" />
			  		</a>
			  		</div>
		  		</li>
			
		  	@endforeach
			</ul>
		</div>
	</div>
	<!-- END TAM LY -->
@endif
@stop


@section('scripts')
<!-- SlidesJS Required: Link to jquery.slides.js -->
<script src="{{ url('js/jquery.bxslider.min.js')}}"></script>
<script src="{{ url('js/jquery.slides.min.js') }}"></script>
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
    	 autoControls: true,
    	 captions: true,
    	  minSlides: 2,
    	  maxSlides: 4,
    	  slideWidth: 250,
    	  slideMargin: 10
    	});
  </script>
<!-- End SlidesJS Required -->
@stop