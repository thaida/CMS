@extends('front.template') 

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


<!-- PHIM HANH DONG -->
<div class="row rowbox">
	<div class="col-xs-6 col-sm-5 col-md-4 box_header Regular">
		<a class="category_title" title="Phim"	href="{!! url('phim') !!}">
			<span class="pull-left">Phim</span>
		</a>
		<a class="pull-left btn_arrow_right" title="Phim" href="{!! url('phim') !!}"></a>
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
					<!-- IMAGE -->
			  		<div class="img">
			  			<img title="{{$film->title}}"  src="{{$url.$film->poster_path}}?w=250&h=350&crop-to-fit" />
			  			@if($film->isFree)
			  			<span class="top right" >Miễn phí</span >
			  			@endif
			  		</div>
		  			<!-- end IMAGE -->
		  		</a>
		  		</div>
    
	  		</li>
		
	  	@endforeach
		</ul>
	</div>
</div>
<style>
.top{
    top: 0; 
}
 
.right{
    right: 0;
}
.img{
    
    /*background-color: #000;*/
}
.img span{
    position: absolute;
    color: red;
}
</style>
<!-- END PHIM  -->
<!-- NHAC -->
<div class="row rowbox">
	<div class="col-xs-6 col-sm-5 col-md-4 box_header Regular">
		<a class="category_title" title="nhạc"
			href="{!! url('music') !!}"><span
			class="pull-left">Nhạc</span></a> <a
			class="pull-left btn_arrow_right" title="Nhạc"
			href="{!! url('music') !!}"></a>
	</div>
	<div class="contentbox">
		<ul class="bxslider">
			<li><img title="Cảnh Sát Hình Sự: Câu Hỏi Số 5" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static1.fptplay.net.vn/static/img/share/video/13_08_2015/cau-hoi-so-5-len-song-tap-dau-tien-21h10-vtv313-08-2015_09g23-17.JPG?w=200&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static1.fptplay.net.vn/static/img/share/video/31_08_2015/wiaemqks31-08-2015_15g31-47.jpg?w=200&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static.fptplay.net.vn/static/img/share/video/10_02_2015/la10-02-2015_16g54-27.jpg?w=200&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static1.fptplay.net.vn/static/img/share/video/08_10_2015/rh18whn908-10-2015_16g43-05.jpg?w=200&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static.fptplay.net.vn/static/img/share/video/21_12_2015/hay-nham-mat-khi-anh-den21-12-2015_16g41-14.jpg?w=200&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static1.fptplay.net.vn/static/img/share/video/07_10_2015/a05eafe2e080146ee6cebc8a39689d30_144133558407-10-2015_09g59-53.jpg?w=200&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static1.fptplay.net.vn/static/img/share/video/12_10_2015/hdhs42yw12-10-2015_09g07-26.jpg?w=200&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static.fptplay.net.vn/static/img/share/video/06_04_2015/tgkcss06-04-2015_16g09-08.jpg?w=200&h=350&mode=scale" /></li>
		</ul>
	</div>
</div>
<!-- END NHAC -->

<div class="row rowbox">
	<div class="col-xs-6 col-sm-5 col-md-4 box_header Regular">
		<a class="category_title" title="Phim bộ Hoa ngu"
			href="{!! url('articles') !!}"><span
			class="pull-left">Phim nổi bật</span></a> <a
			class="pull-left btn_arrow_right" title="Phim bộ HOT"
			href="{!! url('articles') !!}"></a>
	</div>
	<div class="contentbox">
		<ul class="bxslider2"> 
			<li>
				
				<img title="Cảnh Sát Hình Sự: Câu Hỏi Số 5" data-tooltip="phim565c82b317dc130a16f4e7ec"
					src="http://static1.fptplay.net.vn/static/img/share/video/24_03_2015/thestandin24-03-2015_11g08-18.jpg?w=250&h=350&mode=scale" />
				
			</li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static1.fptplay.net.vn/static/img/share/video/31_08_2015/wiaemqks31-08-2015_15g31-47.jpg?w=250&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static.fptplay.net.vn/static/img/share/video/10_02_2015/la10-02-2015_16g54-27.jpg?w=250&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static1.fptplay.net.vn/static/img/share/video/08_10_2015/rh18whn908-10-2015_16g43-05.jpg?w=250&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static.fptplay.net.vn/static/img/share/video/21_12_2015/hay-nham-mat-khi-anh-den21-12-2015_16g41-14.jpg?w=250&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static1.fptplay.net.vn/static/img/share/video/07_10_2015/a05eafe2e080146ee6cebc8a39689d30_144133558407-10-2015_09g59-53.jpg?w=250&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static1.fptplay.net.vn/static/img/share/video/12_10_2015/hdhs42yw12-10-2015_09g07-26.jpg?w=250&h=350&mode=scale" /></li>
			<li><img title="Tình Yêu Quanh Ta - Love Around" data-tooltip="phim565c82b317dc130a16f4e7ec"
				src="http://static.fptplay.net.vn/static/img/share/video/06_04_2015/tgkcss06-04-2015_16g09-08.jpg?w=250&h=350&mode=scale" /></li>
		</ul>
	</div>
</div>
	@stop
	
	@section('tooltip')
	<!-- begin tooltip-->
	<div id="vtstickytooltip" class="stickytooltip">
	<div id="phim565c82b317dc130a16f4e7ec" class="atip">
            <div class="content_tooltip" >
                <h3 class="text_h3_tootip">Mị Nguyệt Truyện (Thuyết minh) - The Legend Of Miyue</h3>
                <span class="text_content_tootip">Bộ phim Mị Nguyệt Truyện kể về cuộc đời của n&agrave;ng Mị Nguyệt từ l&uacute;c c&ograve;n l&agrave; c&ocirc;ng ch&uacute;a đến khi đăng cơ th&agrave;nh th&aacute;i hậu. Bộ phim được chuyển thể từ tiểu thuyết c&ugrave;ng t&ecirc;n (t&ecirc;n gọi trước l&agrave; &ldquo;Đại Tần Tuy&ecirc;n th&aacute;i hậu&rdquo;) của nh&agrave; văn Tưởng Thắng Nam. Tiểu thuyết kể về cuộc đời đầy ly kỳ của Mị B&aacute;t Tử &ndash; mẫu th&acirc;n của Tần Chi&ecirc;u Vương. Từ c&aacute;i t&ecirc;n &ldquo;Mị Nguyệt&rdquo; được t&igrave;m thấy trong những t&agrave;n t&iacute;ch ở Binh M&atilde; Dũng v&agrave; cả danh hiệu phi tử của Tần Huệ Vương Mị Nguyệt được thấy khắc tr&ecirc;n miếng ng&oacute;i của cung A Ph&ograve;ng người ta phỏng đo&aacute;n Mị B&aacute;t Tử ch&iacute;nh l&agrave; Mị Nguyệt. Mị B&aacute;t Tử l&agrave; con g&aacute;i của Sở vương, do một vị thiếp của Sở vương sinh ra. Mị B&aacute;t Tử cũng được gọi l&agrave; Tuy&ecirc;n th&aacute;i hậu. Tương truyền Mị B&aacute;t Tử th&ocirc;ng minh kh&eacute;o l&eacute;o, mưu lược hơn người, ở Đại Tần nắm hết triều ch&iacute;nh, h&ocirc; phong ho&aacute;n vũ gần 40 năm. Trong lịch sử Trung Quốc, tước hiệu th&aacute;i hậu bắt đầu từ xuất hiện từ b&agrave;, th&aacute;i hậu chuy&ecirc;n quyền cũng bắt đầu từ thời b&agrave; mới c&oacute;.</span>
            </div>
    </div>
    </div>
	<!-- end tooltip -->
	@stop
	
	@section('scripts')
	<!-- SlidesJS Required: Link to jquery.slides.js -->
	{!! HTML::script('js/jquery.slides.min.js') !!} 
	{!!	HTML::script('js/jquery.bxslider.min.js') !!}
	<!-- End SlidesJS Required -->
	<!-- SlidesJS Required: Initialize SlidesJS with a jQuery doc ready -->
	{!! HTML::style('css/stickytooltip.css') !!}
	{!! HTML::script('js/stickytooltip.js') !!}
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
   	  slideMargin: 20
   	});
    $('.bxslider2').bxSlider({
      	 autoControls: true,
      	 captions: true,
      	  minSlides: 2,
      	  maxSlides: 2,
      	  slideWidth: 250,
      	  slideMargin: 250,
      	});
    $('.bxslider3').bxSlider({
     	 autoControls: true,
     	 captions: true,
     	  minSlides: 2,
     	  maxSlides: 4,
     	  slideWidth: 200,
     	  slideMargin: 25,
     	});

    $(document).ready(function() {
    	$('.boxTitle').hover(function(){
    			$(this).find('.caption').delay(200).slideDown();
    		},
    		function(){
    			$(this).find('.caption').delay(200).slideUp();
    		}
    	);	
    	});
      	
  </script>
	<!-- End SlidesJS Required -->
	@stop