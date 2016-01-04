@extends('front.template') 
@section('head') 
	{!! HTML::style('ckeditor/plugins/codesnippet/lib/highlight/styles/monokai.css')!!}
	<!-- Chang URLs to wherever Video.js files will be hosted -->
	<!-- Default URLs assume the examples folder is included alongside video.js -->
	{!! HTML::style('js/video/video-js.min.css') !!}
	<!-- Include ES5 shim, sham and html5 shiv for ie8 support  -->
	
	<!-- video.js must be in the <head> for older IEs to work. -->
	{!! HTML::script('js/video/video.min.js') !!} 
	<!-- Exclude this if you don't need ie8 support -->
	{!! HTML::script('js/video/ie8/videojs-ie8.min.js') !!}
	{!! HTML::script('js/video/videojs-resolution-switcher.js') !!} 
	{!! HTML::style('js/video/videojs-resolution-switcher.css') !!}
	<!-- Unless using the CDN hosted version, update the URL to the Flash SWF -->
	<script>
	    videojs.options.flash.swf = "js/video/video-js.swf";
	</script>
	
	{!! HTML::style('css/jquery.bxslider.css') !!}
@stop 
@section('main')
<div class="row">
	<div class="box">
		<div class="col-lg-12">
			<hr>
			<!-- <h2 class="text-center">
				{{ $post->title }} <br> <small>{{ $post->user->username }} {{
					trans('front/blog.on') }} {!! $post->created_at .
					($post->created_at != $post->updated_at ?
					trans('front/blog.updated') . $post->updated_at : '') !!}</small>
			</h2>
			<hr>
			{!! $post->summary !!}<br> {!! $post->content !!}
			<hr> -->
			<div align="center">
				<!-- Player -->
				<video id="example_video_1"
					class="video-js vjs-default-skin  vjs-big-play-centered" controls
					preload="auto" width="600" height="264" data-setup="{ }" autoplay  poster="{{$img_url.$post->poster_path}}?w=600&h=264">
					<source
						src="http://192.168.202.87/film/Mission.Impossible.Rogue.Nation.2015.m720p.HDTV.X264.ACC-TiN(1).mp4"
						type='video/mp4' label="SD" />
					<source
						src="http://192.168.202.87/film/Transformers.Age.of.Extinction.2014.1080p.BluRay.x264.YIFY.mp4"
						type='video/mp4' label="HD" />

					<track kind="captions"
						src="http://192.168.202.87/film/video-subtitles-en.vtt" srclang="en" label="English" default></track>
					<!-- Tracks need an ending tag thanks to IE9 -->
					<p class="vjs-no-js">
						To view this video please enable JavaScript, and consider
						upgrading to a web browser that <a
							href="http://videojs.com/html5-video-support/" target="_blank">supports
							HTML5 video</a>
					</p>
				</video>

			</div>
			<script type="text/javascript">
			videojs('example_video_1', {
			      controls: true,
			      muted: true,
			      width: 800,
			      height: 480,
			     
			      plugins: {
			        videoJsResolutionSwitcher: {
			          default: 'low'
			        }
			      }
			    }, function(){
			      // this is player
				      
			    })
			    
			    
			</script>
			<!-- end Player -->
			
		</div>
	</div>
</div>
<div class="row">
	<!--Tập phim-->
	<div class="video_detail fix_padding box">
		<div class="video_top_title col-xs-12 col-sm-12 col-md-12">
			<h1 style="color: #fd9604; font-size: 24px;"
				class="col-xs-8 col-sm-8 col-md-8">
				{{ $post->title }} - Tập <span next="2" class="_chap">1</span>

			</h1>
		</div>
		<!--<p class="title_details_count pull-right"> <span class="num_view">694,050</span> lượt xem </p>    -->

		<div class="wrap_desc_content">
			<p style="display: inline;">
				<span class="desc_header">Thể loại</span>:
			</p>
			<h2 style="font-size: 14px; margin: 0px; display: inline;">Anime Nổi
				Bật, Tổng Hợp</h2>
			<p></p>
			<p>
				<span class="desc_header">Ngày phát hành</span>: 1986
			</p>
			<p>
				<span class="desc_header">Đạo diễn</span>: Daisuke Nishio
			</p>
			<p>
				<span class="desc_header">Diễn viên</span>: Masako Nozawa, Jôji
				Yanami, Doug Parker
			</p>
			<p>
				<span class="desc_header">Số tập</span>: 349 tập
			</p>
			<p>
				<span class="desc_header">Upload bởi</span>: kimchisuck
			</p>
			<p>
				<span class="desc_header">Mô tả: </span>
			</p>
			<pre>
			{!! $post->summary !!}<!-- <br> {!! $post->content !!} -->
			Phim mô tả cuộc hành trình của Sôn Gôku từ lúc bé đến trưởng thành, qua các lần tầm sư học võ và khám phá thế giới để truy tìm các viên ngọc rồng với điều ước từ rồng thiên. Xuyên suốt hành trình của Songuku, cậu đã gặp được nhiều bạn bè và chống lại những kẻ hung ác có ý định dùng điều ước từ rồng thiên để làm bá chủ thế giới.</pre>
		</div>

		<!-- Giá  -->
		
		
		<!-- PHIM DE XUAT -->
		<div class="row rowbox">
			<div class="col-xs-6 col-sm-5 col-md-4 box_header Regular">
				<a class="category_title" title="Phim đề xuất"	href="{!! url('free') !!}">
					<span class="pull-left">Phim đề xuất</span>
				</a>
				<a class="pull-left btn_arrow_right" title="Phim đề xuất" href="{!! url('free') !!}"></a>
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
		<!-- END PHIM DE XUAT -->
		
		<!-- PHIM MIEN PHI -->
		<div class="row rowbox">
			<div class="col-xs-6 col-sm-5 col-md-4 box_header Regular">
				<a class="category_title" title="Phim đề xuất"	href="{!! url('articles') !!}">
					<span class="pull-left">Phim miễn phí</span>
				</a>
				<a class="pull-left btn_arrow_right" title="Phim đề xuất" href="{!! url('articles') !!}"></a>
			</div>
			<div class="contentbox">
				<ul class="bxslider">
				 @foreach ($films_free as $film)
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
		<!-- END PHIM MIEN PHI -->
	
	</div>

	<div class="row">
		<div class="box">
			<div class="col-lg-12">
				<div class="col-lg-12">
					<hr>
					<h3 class="text-center">{{ trans('front/blog.comments') }}</h3>
					<hr>

					<div class="row" id="formcreate">
						@if(session()->has('warning')) 
							@include('partials/error', ['type'=> 'warning', 'message' => session('warning')]) 
						@endif
						@if(session('statut') != 'visitor') 
							{!! Form::open(['url' =>'comment']) !!}
							{!! Form::hidden('post_id', $post->id) !!}
							{!!	Form::control('textarea', 12, 'comments', $errors, trans('front/blog.comment')) !!} 
							{!!	Form::submit(trans('front/form.send'), ['col-lg-12']) !!} 
							{!!	Form::close() !!} 
						@else
							<div class="text-center">
								<i class="text-center">{{ trans('front/blog.info-comment') }}</i>
							</div>
						@endif
					</div>

				</div>
			</div>
		</div>
	</div>

</div>

@stop 
@section('scripts') 
{!! HTML::script('ckeditor/plugins/codesnippet/lib/highlight/highlight.pack.js') !!} 
@if(session('statut') != 'visitor') 
{!! HTML::script('ckeditor/ckeditor.js') !!} 
@endif

{!!	HTML::script('js/jquery.bxslider.min.js') !!}
<script>

/* SLIDER */
$('.bxslider').bxSlider({
  	 autoControls: true,
  	 captions: true,
  	  minSlides: 2,
  	  maxSlides: 4,
  	  slideWidth: 250,
  	  
  	  slideMargin: 20
  	});

$(document).ready(function() {
	$('.boxTitle').hover(function(){
			$(this).find('.caption').slideDown();
		},
		function(){
			$(this).find('.caption').slideUp();
		}
	);

	//kiem tra user chua login thi dua ra trang thong bao
	 var player = videojs("example_video_1");
	 player.on("play", function(){
		 /* @if(!Auth::check())
			 player.pause();
			 if(confirm("Bạn cần đăng ký gói cước để xem phim thỏa thích")){
				 console.log("Được cho phép");
				 //player.play();
			 }
		 @endif */
		}); 
	
	});

/* END SLIDER */
		@if(session('statut') != 'visitor')

			CKEDITOR.replace('comments', {
				language: '{{ config('app.locale') }}',
				height: 200,
				toolbarGroups: [
					{ name: 'basicstyles', groups: [ 'basicstyles'] }, 
					{ name: 'links' },
					{ name: 'insert' }
				],
				removeButtons: 'Table,SpecialChar,HorizontalRule,Anchor'
			});

			function buttons(i) {
				return "<input id='val" + i +"' class='btn btn-default' type='submit' value='{{ trans('front/blog.valid') }}'><input id='btn" + i + "' class='btn btn-default btnannuler' type='button' value='{{ trans('front/blog.undo') }}'></div>";
			}

			$(function() {

				$('a.editcomment span').tooltip();
				$('a.deletecomment span').tooltip();

				// Set comment edition
				$('a.editcomment').click(function(e) {   
					e.preventDefault();
					$(this).hide();
					var i = $(this).attr('id').substring(7);
					var existing = $('#contenu' + i).html();
					var url = $('#formcreate').find('form').attr('action');
					jQuery.data(document.body, 'comment' + i, existing);
					var html = "<div class='row'><form id='form" + i + "' method='POST' action='" + url + '/' + i + "' accept-charset='UTF-8' class='formajax'><input name='_token' type='hidden' value='" + $('input[name="_token"]').val() + "'><div class='form-group col-lg-12 '><label for='comments' class='control-label'>{{ trans('front/blog.change') }}</label><textarea id='cont" + i +"' class='form-control' name='comments" + i + "' cols='50' rows='10' id='comments" + i + "'>" + existing + "</textarea><small class='help-block'></small></div><div class='form-group col-lg-12'>" + buttons(i) + "</div>";
					$('#contenu' + i).html(html);
					CKEDITOR.replace('comments' + i, {
						language: '{{ config('app.locale') }}',
						height: 200,
						toolbarGroups: [
							{ name: 'basicstyles', groups: [ 'basicstyles'] }, 
							{ name: 'links' },
							{ name: 'insert' }
						],
						removeButtons: 'Table,SpecialChar,HorizontalRule,Anchor'
					});
				});

				// Escape edition
				$(document).on('click', '.btnannuler', function() {    
					var i = $(this).attr('id').substring(3);
					$('#comment' + i).show();
					$('#contenu' + i).html(jQuery.data(document.body, 'comment' + i));
				});

				// Validation 
				$(document).on('submit', '.formajax', function(e) {  
					e.preventDefault();
					var i = $(this).attr('id').substring(4);
					$('#val' + i).parent().html('<i class="fa fa-refresh fa-spin fa-2x"></i>').addClass('text-center');
					$.ajax({
						method: 'put',
						url: $(this).attr('action'),
						data: $(this).serialize()
					})
					.done(function(data) {
						$('#comment' + data.id).show();
						$('#contenu' + data.id).html(data.content);	
					})
					.fail(function(data) {
						var errors = data.responseJSON;
						$.each(errors, function(index, value) {
							$('textarea[name="' + index + '"]' + ' ~ small').text(value);
							$('textarea[name="' + index + '"]').parent().addClass('has-error');
							$('.fa-spin').parent().html(buttons(index.slice(-1))).removeClass('text-center');
						});
					});
				});

				// Delete comment
				$('a.deletecomment').click(function(e) {   
					e.preventDefault();		
					if (!confirm('{{ trans('front/blog.confirm') }}')) return;	
					var i = $(this).attr('id').substring(13);
					var token = $('input[name="_token"]').val();
					$(this).replaceWith('<i class="fa fa-refresh fa-spin pull-right"></i>');
					$.ajax({
						method: 'delete',
						url: '{!! url('comment') !!}' + '/' + i,
						data: '_token=' + token
					})
					.done(function(data) {
						$('#comment' + data.id).parents('.commentitem').remove();
					})
					.fail(function() {
						alert('{{ trans('front/blog.fail-delete') }}');
					});					
				});

			});

		@endif

		hljs.initHighlightingOnLoad();

	</script>

@stop
