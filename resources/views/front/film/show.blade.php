@extends('front.template') 
@section('head') 
	{!! HTML::style('ckeditor/plugins/codesnippet/lib/highlight/styles/monokai.css')!!}
	<!-- Chang URLs to wherever Video.js files will be hosted -->
	<!-- Default URLs assume the examples folder is included alongside video.js -->
	{!! HTML::style('js/video/video-js.min.css') !!}
	<!-- Include ES5 shim, sham and html5 shiv for ie8 support  -->
	<!-- Exclude this if you don't need ie8 support -->
	{!! HTML::script('js/video/ie8/videojs-ie8.min.js') !!}
	<!-- video.js must be in the <head> for older IEs to work. -->
	{!! HTML::script('js/video/video.min.js') !!} 
	{!! HTML::script('js/video/videojs-resolution-switcher.js') !!} 
	{!! HTML::style('js/video/videojs-resolution-switcher.css') !!}
	<!-- Unless using the CDN hosted version, update the URL to the Flash SWF -->
	<script>
	    videojs.options.flash.swf = "js/video/video-js.swf";
	</script>
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
					preload="none" width="600" height="264" data-setup="{}">
					<source
						src="http://192.168.202.87/film/Transformers.Age.of.Extinction.2014.1080p.BluRay.x264.YIFY.mp4"
						type='video/mp4' label="SD" />
					<source
						src="http://192.168.202.87/film/Transformers.Age.of.Extinction.2014.1080p.BluRay.x264.YIFY.mp4"
						type='video/mp4' label="HD" />

					<track kind="captions"
						src="http://192.168.202.87/film/video-subtitles-en.vtt" srclang="en"
						label="English"></track>
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
		
		<!--Tong hop-->
		<div class="row">
			<div
				class="col-xs-6 col-sm-5 col-md-4 box_header title-detail-header Regular">
				<span class="pull-left title-detail">Video liên quan</span>
				<hr class="title-detail">
			</div>
			<div class="row fix_slider sliderTH"
				id="box_530d59c0c969281a9600bd8b_new">
				<div class="wrap_item_tonghop">
					<a class="col-xs-0 col-sm-1 bx-prev"></a> <a
						class="col-xs-12 bx-prev_mobile"></a>
					<div class="col-xs-12 col-sm-11">
						<div class="bx-wrapper"
							style="max-width: 1350px; margin: 0px auto;">
							<div class="bx-viewport"
								style="width: 100%; overflow: hidden; position: relative; height: 160px;">
								<ul class="slider_tonghop"
									style="width: 715%; position: relative; transition-duration: 0s; transform: translate3d(0px, 0px, 0px);">

									<li class="col-xs-12 col-sm-3 col-md-4 col-lg-4"
										style="float: left; list-style: outside none none; position: relative; width: 382px;">
										<div class="col-xs-12">
											<div data-tooltip="phim55c80db417dc1344d30130f7"
												class="col-xs-6 small_tonghop_item">
												<a class="item_image" title=""
													href="http://fptplay.net/xem-video/bay-vien-ngoc-rong-sieu-cap-dragon-ball-super-2015-55c80db417dc1344d30130f7.html"><img
													alt="Bảy Viên Ngọc Rồng Siêu Cấp - Dragon Ball Super 2015"
													src="http://static1.fptplay.net.vn/static/img/share/video/21_12_2015/ivf18a9221-12-2015_17g36-14.jpg?w=200&amp;h=120&amp;mode=scale"
													data-original="http://static1.fptplay.net.vn/static/img/share/video/21_12_2015/ivf18a9221-12-2015_17g36-14.jpg?w=200&amp;h=120&amp;mode=scale"
													class="lazy" style="height: auto; display: block;"
													isloaded="true"></a>
												<div class="title Regular">
													<a
														href="http://fptplay.net/xem-video/bay-vien-ngoc-rong-sieu-cap-dragon-ball-super-2015-55c80db417dc1344d30130f7.html">Bảy
														Viên Ngọc Rồng Siêu Cấp - Dragon Ball Super 2015</a>
												</div>
											</div>
											<div data-tooltip="phim55e7f83317dc1312a1a53b14"
												class="col-xs-6 small_tonghop_item">
												<a class="item_image" title=""
													href="http://fptplay.net/xem-video/moi-tinh-ky-la-iss-pyaar-ko-kya-naam-doon-55e7f83317dc1312a1a53b14.html"><img
													alt="Mối Tình Kỳ Lạ - Iss Pyaar Ko Kya Naam Doon"
													src="http://static1.fptplay.net.vn/static/img/share/video/03_09_2015/zozjglw03-09-2015_14g29-53.jpg?w=200&amp;h=120&amp;mode=scale"
													data-original="http://static1.fptplay.net.vn/static/img/share/video/03_09_2015/zozjglw03-09-2015_14g29-53.jpg?w=200&amp;h=120&amp;mode=scale"
													class="lazy" style="height: auto; display: block;"
													isloaded="true"></a>
												<div class="title Regular">
													<a
														href="http://fptplay.net/xem-video/moi-tinh-ky-la-iss-pyaar-ko-kya-naam-doon-55e7f83317dc1312a1a53b14.html">Mối
														Tình Kỳ Lạ - Iss Pyaar Ko Kya Naam Doon</a>
												</div>
											</div>
										</div>
									</li>

									<li class="col-xs-12 col-sm-3 col-md-4 col-lg-4"
										style="float: left; list-style: outside none none; position: relative; width: 382px;">
										<div class="col-xs-12">
											<div data-tooltip="phim566a4fcc17dc134fe148f711"
												class="col-xs-6 small_tonghop_item">
												<a class="item_image" title=""
													href="http://fptplay.net/xem-video/sinh-tu-chien-the-king-of-fighters-566a4fcc17dc134fe148f711.html"><img
													alt="Sinh Tử Chiến - The King Of Fighters"
													src="http://static.fptplay.net.vn/static/img/share/video/download/11-12-2015/6xtjjfcz3a7jzb4wwx2e178v2c8_11-12-2015_11g23-35.jpg?w=200&amp;h=120&amp;mode=scale"
													data-original="http://static.fptplay.net.vn/static/img/share/video/download/11-12-2015/6xtjjfcz3a7jzb4wwx2e178v2c8_11-12-2015_11g23-35.jpg?w=200&amp;h=120&amp;mode=scale"
													class="lazy" style="height: auto; display: block;"
													isloaded="true"></a>
												<div class="title Regular">
													<a
														href="http://fptplay.net/xem-video/sinh-tu-chien-the-king-of-fighters-566a4fcc17dc134fe148f711.html">Sinh
														Tử Chiến - The King Of Fighters</a>
												</div>
											</div>
											<div data-tooltip="phim544e257b17dc13291c541b42"
												class="col-xs-6 small_tonghop_item">
												<a class="item_image" title=""
													href="http://fptplay.net/xem-video/vua-hai-tac-one-piece-544e257b17dc13291c541b42.html"><img
													alt="Vua Hải Tặc - One Piece"
													src="http://static.fptplay.net.vn/static/img/share/video/10_09_2015/one-piece-backdrop10-09-2015_09g15-02.jpg?w=200&amp;h=120&amp;mode=scale"
													data-original="http://static.fptplay.net.vn/static/img/share/video/10_09_2015/one-piece-backdrop10-09-2015_09g15-02.jpg?w=200&amp;h=120&amp;mode=scale"
													class="lazy" style="height: auto; display: block;"
													isloaded="true"></a>
												<div class="title Regular">
													<a
														href="http://fptplay.net/xem-video/vua-hai-tac-one-piece-544e257b17dc13291c541b42.html">Vua
														Hải Tặc - One Piece</a>
												</div>
											</div>
										</div>
									</li>

									<li class="col-xs-12 col-sm-3 col-md-4 col-lg-4"
										style="float: left; list-style: outside none none; position: relative; width: 382px;">
										<div class="col-xs-12">
											<div data-tooltip="phim565369a117dc13731260f703"
												class="col-xs-6 small_tonghop_item">
												<a class="item_image" title=""
													href="http://fptplay.net/xem-video/tan-thoi-minh-nguyet-thuyet-minh-the-legend-of-qin-565369a117dc13731260f703.html"><img
													alt="Tần Thời Minh Nguyệt (Thuyết minh) - The Legend Of Qin"
													src="http://static.fptplay.net.vn/static/img/share/video/21_12_2015/tan-thoi21-12-2015_16g41-59.jpg?w=200&amp;h=120&amp;mode=scale"
													data-original="http://static.fptplay.net.vn/static/img/share/video/21_12_2015/tan-thoi21-12-2015_16g41-59.jpg?w=200&amp;h=120&amp;mode=scale"
													class="lazy" style="height: auto; display: block;"
													isloaded="true"></a>
												<div class="title Regular">
													<a
														href="http://fptplay.net/xem-video/tan-thoi-minh-nguyet-thuyet-minh-the-legend-of-qin-565369a117dc13731260f703.html">Tần
														Thời Minh Nguyệt (Thuyết minh) - The Legend Of Qin</a>
												</div>
											</div>
											<div data-tooltip="phim5159602bc96928703a603b31"
												class="col-xs-6 small_tonghop_item">
												<a class="item_image" title=""
													href="http://fptplay.net/xem-video/sinh-nhat-thay-5159602bc96928703a603b31.html"><img
													alt="Sinh nhật thầy"
													src="http://static.fptplay.net.vn/static/img/share/video/sinh_nhat_thay_hai_huoc.jpg?w=200&amp;h=120&amp;mode=scale"
													data-original="http://static.fptplay.net.vn/static/img/share/video/sinh_nhat_thay_hai_huoc.jpg?w=200&amp;h=120&amp;mode=scale"
													class="lazy" style="height: auto; display: block;"
													isloaded="true"></a>
												<div class="title Regular">
													<a
														href="http://fptplay.net/xem-video/sinh-nhat-thay-5159602bc96928703a603b31.html">Sinh
														nhật thầy</a>
												</div>
											</div>
										</div>
									</li>

									<li class="col-xs-12 col-sm-3 col-md-4 col-lg-4"
										style="float: left; list-style: outside none none; position: relative; width: 382px;">
										<div class="col-xs-12">
											<div data-tooltip="phim51b5480bc9692876d9fd5c0c"
												class="col-xs-6 small_tonghop_item">
												<a class="item_image" title=""
													href="http://fptplay.net/xem-video/masterchef-us-season-03-51b5480bc9692876d9fd5c0c.html"><img
													alt="MasterChef US (Season 03)"
													src="http://static.fptplay.net.vn/static/img/share/video/masterchef_us_03_tap_14.jpg?w=200&amp;h=120&amp;mode=scale"
													data-original="http://static.fptplay.net.vn/static/img/share/video/masterchef_us_03_tap_14.jpg?w=200&amp;h=120&amp;mode=scale"
													class="lazy" style="height: auto; display: block;"
													isloaded="true"></a>
												<div class="title Regular">
													<a
														href="http://fptplay.net/xem-video/masterchef-us-season-03-51b5480bc9692876d9fd5c0c.html">MasterChef
														US (Season 03)</a>
												</div>
											</div>
											<div data-tooltip="phim55f7cd3517dc130c376403b0"
												class="col-xs-6 small_tonghop_item">
												<a class="item_image" title=""
													href="http://fptplay.net/xem-video/dual-parallel-trouble-adventure-55f7cd3517dc130c376403b0.html"><img
													alt="Dual Parallel Trouble Adventure"
													src="http://static1.fptplay.net.vn/static/img/share/video/15_09_2015/137673270299215-09-2015_14g46-28.jpg?w=200&amp;h=120&amp;mode=scale"
													data-original="http://static1.fptplay.net.vn/static/img/share/video/15_09_2015/137673270299215-09-2015_14g46-28.jpg?w=200&amp;h=120&amp;mode=scale"
													class="lazy" style="height: auto; display: block;"
													isloaded="true"></a>
												<div class="title Regular">
													<a
														href="http://fptplay.net/xem-video/dual-parallel-trouble-adventure-55f7cd3517dc130c376403b0.html">Dual
														Parallel Trouble Adventure</a>
												</div>
											</div>
										</div>
									</li>

									<li class="col-xs-12 col-sm-3 col-md-4 col-lg-4"
										style="float: left; list-style: outside none none; position: relative; width: 382px;">
										<div class="col-xs-12">
											<div data-tooltip="phim53b4ae68c9692847db80d6ee"
												class="col-xs-6 small_tonghop_item">
												<a class="item_image" title=""
													href="http://fptplay.net/xem-video/nguoi-nhen-sieu-dang-2-53b4ae68c9692847db80d6ee.html"><img
													alt="Người Nhện Siêu Đẳng 2"
													src="http://static1.fptplay.net.vn/static/img/share/video/03_07_2014/nguoi-nhen-sieu-dang-203-07-2014_08g14-12.jpg?w=200&amp;h=120&amp;mode=scale"
													data-original="http://static1.fptplay.net.vn/static/img/share/video/03_07_2014/nguoi-nhen-sieu-dang-203-07-2014_08g14-12.jpg?w=200&amp;h=120&amp;mode=scale"
													class="lazy" style="height: auto; display: block;"
													isloaded="true"></a>
												<div class="title Regular">
													<a
														href="http://fptplay.net/xem-video/nguoi-nhen-sieu-dang-2-53b4ae68c9692847db80d6ee.html">Người
														Nhện Siêu Đẳng 2</a>
												</div>
											</div>
											<div data-tooltip="phim5577d0ed17dc1328679cf25e"
												class="col-xs-6 small_tonghop_item">
												<a class="item_image" title=""
													href="http://fptplay.net/xem-video/7-vien-ngoc-rong-dragon-ball-5577d0ed17dc1328679cf25e.html"><img
													alt="7 Viên Ngọc Rồng - Dragon Ball"
													src="http://static1.fptplay.net.vn/static/img/share/video/download/10-06-2015/maxresdefault_10-06-2015_13g54-48.jpg?w=200&amp;h=120&amp;mode=scale"
													data-original="http://static1.fptplay.net.vn/static/img/share/video/download/10-06-2015/maxresdefault_10-06-2015_13g54-48.jpg?w=200&amp;h=120&amp;mode=scale"
													class="lazy" style="height: auto; display: block;"
													isloaded="true"></a>
												<div class="title Regular">
													<a
														href="http://fptplay.net/xem-video/7-vien-ngoc-rong-dragon-ball-5577d0ed17dc1328679cf25e.html">7
														Viên Ngọc Rồng - Dragon Ball</a>
												</div>
											</div>
										</div>
									</li>
								</ul>
							</div>
							<div class="bx-controls bx-has-controls-direction">
								<div class="bx-controls-direction">
									<a href="" class="bx-prev disabled"><img alt=""
										src="http://fptplay.net/img/icon_left_c.png"
										class="img-reponsive"></a><a href="" class="bx-next"><img
										alt="" src="http://fptplay.net/img/icon_right_c.png"
										class="img-reponsive"></a>
								</div>
							</div>
						</div>
					</div>
					<a class="col-xs-0 col-sm-1 bx-next"></a> <a
						class="col-xs-12 bx-next_mobile"></a>
				</div>
			</div>
		</div>

	</div>

	<div class="row">
		<div class="box">
			<div class="col-lg-12">
				<div class="col-lg-12">
					<hr>
					<h3 class="text-center">{{ trans('front/blog.comments') }}</h3>
					<hr>

					<div class="row" id="formcreate">
						@if(session()->has('warning')) @include('partials/error', ['type'
						=> 'warning', 'message' => session('warning')]) @endif
						@if(session('statut') != 'visitor') {!! Form::open(['url' =>
						'comment']) !!} {!! Form::hidden('post_id', $post->id) !!} {!!
						Form::control('textarea', 12, 'comments', $errors,
						trans('front/blog.comment')) !!} {!!
						Form::submit(trans('front/form.send'), ['col-lg-12']) !!} {!!
						Form::close() !!} @else
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

@stop @section('scripts') {!!
HTML::script('ckeditor/plugins/codesnippet/lib/highlight/highlight.pack.js')
!!} @if(session('statut') != 'visitor') {!!
HTML::script('ckeditor/ckeditor.js') !!} @endif

<script>	  

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
