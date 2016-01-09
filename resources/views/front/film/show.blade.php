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
	
	{!! HTML::style('css/owl.carousel.css') !!}
@stop 
@section('main')
 <div class="player-video">
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
			      width: 1000,
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
<!-- Cac tap phim khác -->
@if($post->num > 1)
<div id="cj-slider02" class="show-cj show-cj-music">
            <div class="wrap-show-cj">
            	<div class="show-cj-slider">
                	<div class="show-cj-item">
                    	<a class="cj-item-lnk">
                        	<img src="../images/template/temp_08.jpg" />
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
                        	<img src="../images/template/temp_09.jpg" />
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
                        	<img src="../images/template/temp_10.jpg" />
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
                        	<img src="../images/template/temp_12.png" />
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
  @endif
<!-- end cac tap phim khac -->
<!-- Thong tin phim -->
 <div class="info-music">
	<p class="p-info-top">
		<strong>{{ $post->title }}
		<!-- Neu la phim dai tap thi hien thi so tap -->
		@if($post->num > 1)
			- Tập {{$post->episode }}
		@endif
		</strong><br />
		Thể loại: {{ $post->subCat}} <br />
		Ngôn ngữ: {{ $post->language}}<br />
		Đã phát hành: {{ date('Y',strtotime($post->release_date ))}}
				
	</p>
	<p class="p-info-bottom">
		<strong>
			Mô tả phim
		</strong><br />
		{!! $post->summary !!}
	</p>
</div>
<!-- end thong tin phim -->

		<!-- Giá  -->
		
		
		<!-- PHIM DE XUAT -->
<div id="cj-slider01" class="show-cj show-cj-film">
	<h3 class="show-h3">
		<a>PHIM ĐỀ XUẤT</a>
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
						<a  href="{{$film_url.$film->slug}}" title="{{$film->title}}">{{$film->title}}</a>
						<span class="sp-right"> {{ date('Y',strtotime($film->release_date ))}}</span>
					</h3>
					<p class="cj-p-film">
						<span class="sp-left">Fire With Fire</span>
						<span class="sp-right">{{$film->running_time}}</span>                                
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
		<!-- END PHIM DE XUAT -->
		
		<!-- PHIM MIEN PHI -->
<div id="cj-slider01" class="show-cj show-cj-film">
	<h3 class="show-h3">
		<a>PHIM MIỄN PHÍ</a>
	</h3>
	<div class="wrap-show-cj">
		<div class="show-cj-slider">
		 @foreach ($films_free as $film)
			<div class="show-cj-item">
				<a class="cj-item-lnk" href="{{$film_url.$film->slug}}">
					<img title="{{$film->title}}"  src="{{$img_url.$film->poster_path}}?w=250&h=350&crop-to-fit" />
				</a>
				<div class="cj-info">
					<h3 class="cj-h3">
						<a  href="{{$film_url.$film->slug}}" title="{{$film->title}}">{{$film->title}}</a>
						<span class="sp-right"> {{ date('Y',strtotime($film->release_date ))}}</span>
					</h3>
					<p class="cj-p-film">
						<span class="sp-left">Fire With Fire</span>
						<span class="sp-right">{{$film->running_time}}</span>                                
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

	{!!	HTML::script('js/owl.carousel.min.js') !!}
	{!! HTML::script('js/common.js')	!!}
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
