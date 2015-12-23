@extends('front.template') 
@section('head') 
	{!!	HTML::style('ckeditor/plugins/codesnippet/lib/highlight/styles/monokai.css')!!}
	<!-- Chang URLs to wherever Video.js files will be hosted -->
	<!-- Default URLs assume the examples folder is included alongside video.js -->
	{!! HTML::style('js/video/video-js.min.css') !!}
	<!-- Include ES5 shim, sham and html5 shiv for ie8 support  -->
	<!-- Exclude this if you don't need ie8 support -->
	{!! HTML::script('js/video/ie8/videojs-ie8.min.js') !!}
	<!-- video.js must be in the <head> for older IEs to work. -->
	{!! HTML::script('js/video/video.min.js') !!} {!!
	HTML::script('js/video/videojs-resolution-switcher.js') !!} {!!
	HTML::style('js/video/videojs-resolution-switcher.css') !!}
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
			<h2 class="text-center">
				{{ $post->title }} <br> <small>{{ $post->user->username }} {{
					trans('front/blog.on') }} {!! $post->created_at .
					($post->created_at != $post->updated_at ?
					trans('front/blog.updated') . $post->updated_at : '') !!}</small>
			</h2>
			<hr>
			{!! $post->summary !!}<br> {!! $post->content !!}
			<hr>
			<div align="center">
			<!-- Player -->
			<video id="example_video_1" class="video-js vjs-default-skin  vjs-big-play-centered"
				controls preload="none" width="600" height="264" data-setup="{}">
				<source
					src="http://localhost/film/Transformers.Age.of.Extinction.2014.1080p.BluRay.x264.YIFY.mp4"
					type='video/mp4' label="SD" />
				<source
					src="http://localhost/film/Transformers.Age.of.Extinction.2014.1080p.BluRay.x264.YIFY.mp4"
					type='video/mp4' label="HD" />

				<track kind="captions" src="http://localhost/film/video-subtitles-en.vtt" srclang="en"
					label="English" ></track>
				<!-- Tracks need an ending tag thanks to IE9 -->
				<p class="vjs-no-js">
					To view this video please enable JavaScript, and consider upgrading
					to a web browser that <a
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
