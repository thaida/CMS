@extends('front.template')
@section('head')
	{!! HTML::style('css/jquery.bxslider.css') !!} 
@stop
@section('slide')
<!-- The container is used to define the width of the slideshow -->
@if(isset($banners))
		<ul class="bxslider">
		@foreach($banners as $banner)
		  <li><img src="{{ $img_url.$banner->poster_path }}" title="{{$banner->title}}" /></li>
		@endforeach
		</ul>
	@endif
	

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

@if(isset($films) && count($films) > 0)

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
   /*  $(function() {
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
    }); */

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