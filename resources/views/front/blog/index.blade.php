@extends('front.template') @section('slide')

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

<div class="row rowbox">
	<div class="col-xs-6 col-sm-5 col-md-4 box_header Regular">
		<a class="category_title" title="Phim bộ Hoa ngu"
			href="{!! url('articles') !!}"><span
			class="pull-left">Phim</span></a> <a
			class="pull-left btn_arrow_right" title="Phim bộ HOT"
			href="{!! url('articles') !!}"></a>
	</div>
	<div class="contentbox">
		<ul class="bxslider">
			<li><a class="item_image" title="Bài thi ngắn nam - Trophee Eric Bompard 2015" href="{!! url('film/viet-nam-que-huong-toi') !!}">
			<img title="Cảnh Sát Hình Sự: Câu Hỏi Số 5"
				src="http://static1.fptplay.net.vn/static/img/share/video/13_08_2015/cau-hoi-so-5-len-song-tap-dau-tien-21h10-vtv313-08-2015_09g23-17.JPG?w=200&h=120&mode=scale" />
				</a>
			</li>
			<li>
			<a class="item_image" title="Bài thi ngắn nam - Trophee Eric Bompard 2015" href="{!! url('film/viet-nam-que-huong-toi') !!}">
			<img title="Tình Yêu Quanh Ta - Love Around"
				src="http://static1.fptplay.net.vn/static/img/share/video/31_08_2015/wiaemqks31-08-2015_15g31-47.jpg?w=200&h=120&mode=scale" />
				</a>
			</li>
			<li>
			<a class="item_image" title="Bài thi ngắn nam - Trophee Eric Bompard 2015" href="{!! url('film/viet-nam-que-huong-toi') !!}">
			<img title="Tình Yêu Quanh Ta - Love Around"
				src="http://static.fptplay.net.vn/static/img/share/video/10_02_2015/la10-02-2015_16g54-27.jpg?w=200&h=120&mode=scale" />
				</a></li>
			<li><a class="item_image" title="Bài thi ngắn nam - Trophee Eric Bompard 2015" href="{!! url('film/viet-nam-que-huong-toi') !!}">
			<img title="Tình Yêu Quanh Ta - Love Around"
				src="http://static1.fptplay.net.vn/static/img/share/video/08_10_2015/rh18whn908-10-2015_16g43-05.jpg?w=200&h=120&mode=scale" />
				</a></li>
			<li><a class="item_image" title="Bài thi ngắn nam - Trophee Eric Bompard 2015" href="{!! url('film/viet-nam-que-huong-toi') !!}"><img title="Tình Yêu Quanh Ta - Love Around"
				src="http://static.fptplay.net.vn/static/img/share/video/21_12_2015/hay-nham-mat-khi-anh-den21-12-2015_16g41-14.jpg?w=200&h=120&mode=scale" />
				</a>
				</li>
			<li><a class="item_image" title="Bài thi ngắn nam - Trophee Eric Bompard 2015" href="{!! url('film/viet-nam-que-huong-toi') !!}"><img title="Tình Yêu Quanh Ta - Love Around"
				src="http://static1.fptplay.net.vn/static/img/share/video/07_10_2015/a05eafe2e080146ee6cebc8a39689d30_144133558407-10-2015_09g59-53.jpg?w=200&h=120&mode=scale" />
				</a>
				</li>
			<li><a class="item_image" title="Bài thi ngắn nam - Trophee Eric Bompard 2015" href="{!! url('film/viet-nam-que-huong-toi') !!}"><img title="Tình Yêu Quanh Ta - Love Around"
				src="http://static1.fptplay.net.vn/static/img/share/video/12_10_2015/hdhs42yw12-10-2015_09g07-26.jpg?w=200&h=120&mode=scale" />
				</a>
				</li>
			<li><a class="item_image" title="Bài thi ngắn nam - Trophee Eric Bompard 2015" href="{!! url('film/viet-nam-que-huong-toi') !!}"><img title="Tình Yêu Quanh Ta - Love Around"
				src="http://static.fptplay.net.vn/static/img/share/video/06_04_2015/tgkcss06-04-2015_16g09-08.jpg?w=200&h=120&mode=scale" /></a></li>
		</ul>
	</div>
</div>


<div class="row">

	@foreach($posts as $post)
	<div class="box">
		<div class="col-lg-12 text-center">
			<h2>
				{{ $post->title }} <br> <small>{{ $post->user->username }} {{
					trans('front/blog.on') }} {!! $post->created_at .
					($post->created_at != $post->updated_at ?
					trans('front/blog.updated') . $post->updated_at : '') !!}</small>
			</h2>
		</div>
		<div class="col-lg-12">
			<p>{!! $post->summary !!}</p>
		</div>
		<div class="col-lg-12 text-center">
			{!! link_to('blog/' . $post->slug, trans('front/blog.button'),
			['class' => 'btn btn-default btn-lg']) !!}
			<hr>
		</div>
	</div>
	@endforeach

	<div class="col-lg-12 text-center">{!! $links !!}</div>

</div>

@stop @section('scripts')
<!-- SlidesJS Required: Link to jquery.slides.js -->
<script src="js/jquery.bxslider.min.js"></script>
<script src="js/jquery.slides.min.js"></script>

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
    	  maxSlides: 5,
    	  slideWidth: 200,
    	  slideMargin: 10
    	});
  </script>
<!-- End SlidesJS Required -->
@stop
