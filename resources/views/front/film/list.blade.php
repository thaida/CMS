@extends('front.template')
@section('head')

	{!! HTML::style('css/owl.carousel.css') !!}

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
    <div id="cj-slider01" class="show-cj show-cj-film">
        	<h3 class="show-h3">
            	<a>PHIM XEM NHIỀU NHẤT</a>
            </h3>
            <div class="wrap-show-cj">
            	<div class="show-cj-slider">
            	  @foreach ($films_most_view as $film)
                	<div class="show-cj-item">
                    	<a class="cj-item-lnk" href="{{$film_url.$film->slug}}">
                        	<img src="{{$img_url . $film->poster_path}}?w=250&amp;h=430&amp;crop-to-fit" title="{{ $film->title}}">
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a href="{{$film_url.$film->slug}}"> {{ $film->title}}</a>
                                <span class="sp-right">2001</span>
                            </h3>
                            <p class="cj-p-film">
                            	<span class="sp-left">Fire With Fire</span>
                                <span class="sp-right">{{ $film->running_time}}</span>                                
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
        
        

@if(isset($films))
	<!-- PHIM BY CAT -->
	<div id="cj-slider02" class="show-cj show-cj-film">
        	<h3 class="show-h3" >
            	<a title="{{ $films->get(0)->subCat}}"	href="{!! url('phim/'. $films->get(0)->catSlug) !!}">{{ $films->get(0)->subCat}}</a>
            </h3>
            <div class="wrap-show-cj">
            	<div class="show-cj-slider">
            	 @foreach ($films as $film)
                	<div class="show-cj-item">
                    	<a class="cj-item-lnk" href="{{$film_url.$film->slug}}">
                        	<img title="{{$film->title}}"  src="{{$img_url.$film->poster_path}}?w=250&h=350&crop-to-fit" />
                        </a>
                        <div class="cj-info">
                        	<h3 class="cj-h3">
                            	<a href="{{$film_url.$film->slug}}">{{$film->title}}</a>
                                <span class="sp-right">2001</span>
                            </h3>
                            <p class="cj-p-film">
                            	<span class="sp-left">Fire With Fire</span>
                                <span class="sp-right">{!! $film->running_time !!}</span>                                
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
@endif


@stop


@section('scripts')
<!-- SlidesJS Required: Link to jquery.slides.js -->
{!! HTML::script('js/jquery.bxslider.min.js')	!!}
{!! HTML::script('js/jquery.slides.min.js')	!!}
	{!!	HTML::script('js/owl.carousel.min.js') !!}
	{!! HTML::script('js/common.js')	!!}
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