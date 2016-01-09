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
{!! HTML::style('css/jquery.bxslider.css') !!}

<div id="slides">
	<img
		src="http://c1.staticflickr.com/5/4147/5087404401_d24513119a_n.jpg"
		alt="Photo by: Missy S Link: http://www.flickr.com/photos/listenmissy/5087404401/">
	<img
		src="http://c1.staticflickr.com/5/4132/5087985262_77003d3f70_n.jpg"
		alt="Photo by: Daniel Parks Link: http://www.flickr.com/photos/parksdh/5227623068/">
	<img
		src="http://vtv1.vcmedia.vn/Uploaded/lanchi/2013_06_07/my%20khe%206.jpg"
		alt="Photo by: Mike Ranweiler Link: http://www.flickr.com/photos/27874907@N04/4833059991/">
	<img
		src="https://vtv1.vcmedia.vn/thumb_w/650/Uploaded/lanchi/2013_06_07/my%20khe%205_635062082144361668.png"
		alt="Photo by: Stuart SeegerLink: http://www.flickr.com/photos/stuseeger/97577796/">
</div>

@stop

@section('main')
	@if(isset($films) && count($films) >0)
	
	
	<div class="row rowbox">
		<div class="col-xs-6 col-sm-5 col-md-4 box_header Regular">
			<a class="category_title" title="{{ $films->get(0)->subCat}}" href="{!! url('phim/'. $films->get(0)->catSlug) !!}">
				<span class="pull-left">{{ $films->get(0)->subCat}}</span>
			</a> 
			<a class="pull-left btn_arrow_right" title="{{ $films->get(0)->subCat}}" href="{!! url('phim/'.$films->get(0)->catSlug) !!}"></a>
		</div>
		<div style="padding-top: 30px;">
		   @foreach ($films as $film)
			 <div class="col-xs-6 col-sm-3">
			 	<div class="boxTitle">
		  			<a href="{{$film_url . $film->slug }}">
						<div class="caption" style="display: none;">
							<h4> {{ $film->title}}</h4>
							<p>Đạo diễn:  {{ $film->director}}<br>
							Diễn viên: {{ $film->actor}}<br>
							Thể loại: <br>
							</p><p>Thời lượng: {{ $film->running_time}}</p>
							<p class=""></p>
							<p>dá</p>
						</div>
						<!-- IMAGE -->
				  		<div class="img">
				  			<img src="{{$img_url . $film->poster_path}}?w=250&amp;h=350&amp;crop-to-fit" title="{{ $film->title}}">
						</div>
			  			<!-- end IMAGE -->
			  		</a>
		  		</div>
		  		<div>
		  		<p>
		  		 <span class=""><h5>{{ $film->title}}</h5></span>
		  		 <span>{{ date('Y', strtotime($film->release_date)) }}</span>
		  		</p>
		  		<p>
		  		 <span>Ghi chú</span>
		  		 <span>{{ $film-> running_time}}</span>
		  		</p>
		  		</div>
				  		
		       <br/>
		        {{ $film->counter}}
		      </div>
		      
	     
		@endforeach
		</div>
	 
	<div class="row col-lg-12">
		<div class="pull-right link">{!! $links !!}</div>
	</div>
		
	</div>
	@endif
@stop


	@section('scripts')
	<!-- SlidesJS Required: Link to jquery.slides.js -->
	{!! HTML::script('js/jquery.slides.min.js') !!} 
	{!!	HTML::script('js/jquery.bxslider.min.js') !!}
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
    </script>
	@stop