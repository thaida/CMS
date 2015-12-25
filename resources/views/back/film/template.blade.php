@extends('back.template') @section('head') {!!
HTML::style('ckeditor/plugins/codesnippet/lib/highlight/styles/default.css')
!!} @stop @section('main')

<!-- Entête de page -->
@include('back.partials.entete', ['title' =>
trans('back/film.dashboard'), 'icone' => 'pencil', 'fil' =>
link_to('film', trans('back/film.location')) . ' / ' .
trans('common.add')])

<div class="col-sm-12">
	@yield('form')

	<div class="form-group checkbox pull-right">
		<label> {!! Form::checkbox('active') !!} {{ trans('common.published')
			}} </label>
	</div>

	{!! Form::control('text', 0, 'title', $errors, trans('common.title'))	!!}
	<div class="form-group {!! $errors->has('slug') ? 'has-error' : '' !!}">
		{!! Form::label('slug', trans('back/blog.permalink'), ['class' => 'control-label']) !!} 
		{!! url('/') . '/film/' . Form::text('slug', null, ['id' => 'permalien']) !!} 
		<small class="text-danger">	{!!	$errors->first('slug') !!}</small>
	</div>
	<div class="form-group">
		<label class="control-label" for="sub_cat_id">Poster</label>
		<input type="text" id="btnImage"  name="btnImage"/>
		<button type="button" onclick="BrowseServer('btnImage');">Pick Image</button>
		
		{!! isset($post) ? "<img src='$img_host_url$post->poster_path' width='100' height='100' />" : "" !!}
	</div>

	<div class="form-group">
		<label class="control-label" for="sub_cat_id">Phụ đề</label> 
		<input type="text" id="btnSubTitle" name="btnSubTitle" />
		<button type="button" onclick="BrowseServer('btnSubTitle');">Pick sub file</button>
		
	</div>

	<div class="form-group">
		<label class="control-label" for="sub_cat_id">Phim</label> 
		<input type="text" id="btnfilm" name="btnfilm" />
		<button type="button" onclick="BrowseServer('btnfilm');">Pick film</button>
	</div> 	
	
	
	{!! Form::selection('sub_cat_id', $select, null, trans('back/cat.subcat')) !!} 
	{!! Form::control('textarea', 0, 'summary', $errors, trans('common.summary')) !!} 
	{!!	Form::submit(trans('common.save')) !!} 
	{!! Form::close() !!}
</div>

@stop 
@section('scripts')
<script type="text/javascript">
     var urlobj;

     function BrowseServer(obj)
     {
          urlobj = obj;
          OpenServerBrowser(
          "{!! url($url) !!}?type=images",
          screen.width * 0.7,
          screen.height * 0.7 ) ;
     }

     function OpenServerBrowser( url, width, height )
     {
          var iLeft = (screen.width - width) / 2 ;
          var iTop = (screen.height - height) / 2 ;
          var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes" ;
          sOptions += ",width=" + width ;
          sOptions += ",height=" + height ;
          sOptions += ",left=" + iLeft ;
          sOptions += ",top=" + iTop ;
          var oWindow = window.open( url, "BrowseWindow", sOptions ) ;
     }

     function SetUrl( url, width, height, alt )
     {
          document.getElementById(urlobj).value = url ;
          oWindow = null;
     }

</script>

{!! HTML::script('ckeditor/ckeditor.js') !!}

<script>

	var config = {
		codeSnippet_theme: 'Monokai',
		language: '{{ config('app.locale') }}',
		height: 100,
		toolbarGroups: [
			{ name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
			{ name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
			{ name: 'links' },
			{ name: 'insert' },
			{ name: 'forms' },
			{ name: 'tools' },
			{ name: 'document',	   groups: [ 'mode', 'document', 'doctools' ] },
			{ name: 'others' },
			//'/',
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
			{ name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
			{ name: 'styles' },
			{ name: 'colors' }
		]
	};

	CKEDITOR.replace( 'summary', config);

	config['height'] = 400;		

	$("#title").keyup(function(){
			var str = sansAccent($(this).val());
			str = str.replace(/[^a-zA-Z0-9\s]/g,"");
			str = str.toLowerCase();
			str = str.replace(/\s/g,'-');
			$("#permalien").val(str);        
		});

  </script>

@stop
