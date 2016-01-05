@extends('back.template') @section('head') {!!
HTML::style('ckeditor/plugins/codesnippet/lib/highlight/styles/default.css')
!!} @stop @section('main')

<!-- Entête de page -->
@include('back.partials.entete', ['title' =>
trans('back/film.dashboard'), 'icone' => 'pencil', 'fil' =>
link_to('film', trans('back/film.location')) . ' / ' .
trans('common.add')])
<style>
.nopadding {
   padding: 0 !important;
   margin: 0 !important;
}
.wrap{
	width: 100%;
}
input[type="file"]{
	position:absolute;
	z-index:2;
	top:0;
	left:0;
	filter: alpha(opacity=0);
	-ms-filter:"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)";
	opacity:0;
	background-color:transparent;
	color:transparent;	
}

</style>
<div class="col-sm-9 nopadding">
	@yield('form')
	<div class="panel panel-default">
	  <div class="panel-heading"><b>Thông tin phim</b></div>
	  <div class="panel-body">
	
		{!! Form::control('text', 0, 'title', $errors, trans('common.title'))	!!}
		<div class="form-group {!! $errors->has('slug') ? 'has-error' : '' !!}">
			{!! Form::label('slug', trans('back/blog.permalink'), ['class' => 'control-label']) !!} 
			{!! url('/') . '/film/' . Form::text('slug', null, ['id' => 'permalien']) !!} 
			<small class="text-danger">	{!!	$errors->first('slug') !!}</small>
		</div>
		{!! Form::selection('sub_cat_id', $select, null, trans('back/cat.subcat')) !!} 
		{!! Form::control('textarea', 0, 'summary', $errors, trans('common.summary')) !!} 
		
	  </div>
	</div>

	<div class="panel panel-default">
	  <div class="panel-heading"><b>Nội dung media</b></div>
	  <div class="panel-body">
	  <div class="form-group">
	  <!-- choose poster -->
		<div style="position:relative;">
		<div class=" col-sm-2">
	        <a class='btn btn-primary wrap'  href="javascript:BrowseServer('btnImage', 'info-poster');">
	            poster...
	            <!-- <input type="file" name="file_poster" size="40"  onchange='$("#btnImage1").html($(this).val());'> -->
	       </a>
	       </div>
	        <div class=" col-sm-10">
	        <span class='label label-info' id="info-poster"></span> 
	        <input type="hidden" name="btnImage" id="btnImage" />
	        </div>
		</div>
	<!-- end choose poster -->
	</div>
	<!-- 	<input name="multi_url" type="text" id="multi_url" maxlength="255" value="" /> -->
		
	<!-- <a id="browse_server" href="{!! url($url) !!}?langCode=vi&field_name=multi_url"><span>Browse server</span></a> -->
	
		<div class="form-group">						
			{!! isset($post) ? "<img src='$img_host_url$post->poster_path?w=100&h=100' width='100' height='100' />" : "" !!}
		</div>
	
		<div class="form-group">
		<!-- choose subtitle -->
		<div style="position:relative;">
		<div class=" col-sm-2">
	        <a class='btn btn-primary wrap' href="javascript:BrowseServer('btnSubTitle', 'info-subtitle');">
	            subtitle...
	            <!-- <input type="file" name="file_subtile" size="40"  onchange='$("#btnSubTitle").html($(this).val());'> -->
	        </a>
	        </div>
	        
	        <div class=" col-sm-10">
		        <span class='label label-info' id="info-subtitle"></span>
		        <input type="hidden" name="btnSubTitle" id="btnSubTitle" />
	        </div>
		</div>
	<!-- end choose subtitle -->
			
			
		</div>
	
		<div class="form-group">
		<!-- choose film -->
		<div style="position:relative;">
			<div class="col-sm-2">
		        <a class='btn btn-primary wrap' href="javascript:BrowseServer('btnfilm', 'info-film');">
		            film...
		           <!--  <input type="file" name="file_film" size="40"  onchange='$("#btnfilm").html($(this).val());'> -->
		        </a>
	        </div>
	        
	        <div class=" col-sm-10">
		        <span class='label label-info' id="info-film"></span>		        
		         <input type="hidden" name="btnfilm" id="btnfilm" />
	         </div>
		</div>
		<!-- end choose film -->

		</div> 
			<div class="form-group">
			<label class="control-label" for="sub_cat_id">Phim</label> 
			<input type="text" id="btnfilm1" name="btnfilm1" />
			<button type="button" onclick="BrowseServer('btnfilm1');">Pick film</button>
			</div>	
		</div>
	</div>

	
</div>

<!-- LEFT COLUMN -->
<div class="col-sm-3 nopadding">
	<div class="panel panel-default">
	  <div class="panel-heading"><b>Thông tin khác</b></div>
	  <div class="panel-body">
	  	<div class="form-group checkbox pull-left">
	  
			<label> {!! Form::checkbox('publish') !!} {{ trans('common.published')	}} </label> <br/>
			<label> {!! Form::checkbox('isHot') !!} {{ trans('common.isFront')	}} </label>
		</div>
		<div class="form-group">
			{!! Form::date('name22', \Carbon\Carbon::now()); !!}
		</div>
	  </div>	  
	 </div>
</div>
<div class="col-sm-12">
	{!!	Form::submit(trans('common.save')) !!}
</div> 
	{!! Form::close() !!}
@stop 
@section('scripts')
<script type="text/javascript">
     var urlobj;
     var urlDisplay;

     function BrowseServer(obj1, obj2)
     {
          urlobj = obj1;
          urlDisplay = obj2;
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
          $("#" + urlDisplay).html(url);
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

	$(document).ready(function(){
		
		  //$("#btnImage1").html("abcn");
		 
		}); 
				
  </script>

@stop
