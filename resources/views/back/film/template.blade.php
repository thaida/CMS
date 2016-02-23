@extends('back.template') 
@section('head') 
	{!! HTML::style('ckeditor/plugins/codesnippet/lib/highlight/styles/default.css') !!}
	{!! HTML::style('css/jquery-ui.css') !!} 
@stop 
@section('main')

<!-- enter  page -->
@include('back.partials.entete', ['title' => trans('back/film.dashboard'), 'icone' => 'pencil', 
								'fil' =>link_to('film', trans('back/film.location')) . ' / ' .trans('common.add')])
<style>

  .ui-autocomplete-loading {
    background: white url("../img/bx_loader.gif") right center no-repeat;
  }
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

/* Hiding the checkbox, but allowing it to be focused */
.badgebox
{
    opacity: 0;
}

.badgebox + .badge
{
    /* Move the check mark away when unchecked */
    text-indent: -999999px;
    /* Makes the badge's width stay the same checked and unchecked */
	width: 27px;
}

.badgebox:focus + .badge
{
    /* Set something to make the badge looks focused */
    /* This really depends on the application, in my case it was: */
    
    /* Adding a light border */
    box-shadow: inset 0px 0px 5px;
    /* Taking the difference out of the padding */
}

.badgebox:checked + .badge
{
    /* Move the check mark back when checked */
	text-indent: 0;
}
/* autocomplete */
.ui-autocomplete 
{ 
	cursor:pointer; 
	height:120px; 
	overflow-y:scroll;	
}    
</style>
<!-- LEFT COLUM -->
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
		<!-- the loai phim -->
		{!! Form::selection('sub_cat_id', $select, null, trans('back/category.subcat')) !!} 
		<!-- Tập phim liên quan(tới tập 1 của phim nếu là phim bộ) -->
		{!! Form::control('text', 0, 'first_episode', $errors, trans('back/film.firstfilm'))	!!}
		<input type="hidden" id="first_episode_id" name="first_episode_id" value="{{ isset($post) ? $post->first_episode_id : ''}}"/>
		<!-- ghi chu -->
		{!! Form::control('textarea', 0, 'summary', $errors, trans('common.summary')) !!} 
	  </div>
	</div>

	<div class="panel panel-default">

	  <div class="panel-heading"><b>Nội dung media</b>
	  </div>
	  <div class="panel-body">
	  	<div class="form-group">
  		<!-- choose poster -->
			<div style="position:relative;">
				<div class=" col-sm-2">
			        <a class='btn btn-primary wrap'  href="javascript:BrowseServer('poster_path', 'info-poster');">
			            Poster...
			            <!-- <input type="file" name="file_poster" size="40"  onchange='$("#btnImage1").html($(this).val());'> -->
			       </a>
	       		</div>
	        	<div class=" col-sm-7">
			        <span class='label label-info' id="info-poster"></span> 
			        <input type="hidden" name="poster_path" id="poster_path"  />
		        </div>
	        	<div class="col-sm-3">
	        		{!! isset($post) ? "<img src='$img_host_url$post->poster_path?w=100&h=100' width='100' height='100' />" : "" !!}
	        	</div>
			</div>
		<!-- end choose poster -->
		</div>	
		<div class="form-group">
		<!-- choose subtitle -->
			<div style="position:relative;">
				<div class=" col-sm-2">
			        <a class='btn btn-primary wrap' href="javascript:BrowseServer('subtitle_path', 'info-subtitle');">
			            Subtitle...
			            <!-- <input type="file" name="file_subtile" size="40"  onchange='$("#btnSubTitle").html($(this).val());'> -->
			        </a>
	        	</div>
	        
		        <div class=" col-sm-10">
			        <span class='label label-info' id="info-subtitle">{{isset($post) ? basename($post->subtitle_path) : ""}}</span>
			        @if(isset($post))
			        	<span class="glyphicon glyphicon-download-alt"><a href="">Download</a></span>
			        @endif
			        <input type="hidden" name="subtitle_path" id="subtitle_path" />
		        </div>
		</div>
		<!-- end choose subtitle -->
		</div>
	
		<div class="form-group">
		<!-- choose film -->
			<div style="position:relative;">
				<div class="col-sm-2">
			        <a class='btn btn-primary wrap' href="javascript:BrowseServer('film_path', 'info-film');">
			            Phim...
			           <!--  <input type="file" name="file_film" size="40"  onchange='$("#btnfilm").html($(this).val());'> -->
			        </a>
		        </div>
	        
		        <div class=" col-sm-10">
			        <span class='label label-info' id="info-film">{{isset($post) ? basename($post->film_path) : ""}}</span>		        
			         <input type="hidden" name="film_path" id="film_path" />
		         </div>
			</div>
		<!-- end choose film -->

	</div>
	
	</div>
</div>

</div>
<!-- RIGHT COLUMN -->
	<div class="col-sm-3 nopadding">
		<div class="panel panel-default">
		  <div class="panel-heading"><b>Thông tin khác</b></div>
		  <div class="panel-body">
		  	<div class="form-group checkbox pull-left">
			  	<div class="container">
				</div>
<!-- <label for="publish" class="btn btn-info">{{ trans('common.published')	}} <input type="checkbox" id="publish" name="publish" class="badgebox"><span class="badge">&check;</span></label>
<br />
<label for="isFree" class="btn btn-warning">{{ trans('common.isFree')	}} <input type="checkbox" id="isFree" name="isFree" class="badgebox"><span class="badge">&check;</span></label>
<br />
<label for="isHot" class="btn btn-primary"> {{ trans('common.isFront')	}}<input type="checkbox" id="isHot" name="isHot" class="badgebox"><span class="badge">&check;</span></label> -->
			<i class="glyphicon glyphicon-ok-circle"></i><label> {!! Form::checkbox('publish') !!} {{ trans('common.published')	}} </label> <br/> 
			<i class="glyphicon glyphicon-usd"></i>  <label> {!! Form::checkbox('isFree') !!} {{ trans('common.isFree')	}} </label> <br/>
			<i class="glyphicon glyphicon-home"></i> <label> {!! Form::checkbox('isHot') !!} {{ trans('common.isFront')	}} </label>
			</div>
					
		<!-- ngay phat hanh -->
		{!! Form::control('text', 0, 'release_date', $errors, trans('back/film.release_date'))	!!}
		<!-- thoi luong phim -->
		{!! Form::control('number', 0, 'running_time', $errors, trans('back/film.running'))	!!}
		<!-- dao dien -->
		{!! Form::control('text', 0, 'director', $errors, trans('back/film.director'))	!!}
		<!-- Dien vien -->
		{!! Form::control('text', 0, 'actor', $errors, trans('back/film.actor'))	!!}
		<!-- Ngon ngu -->
		{!! Form::control('text', 0, 'language', $errors, trans('back/film.language'))	!!}
	  </div>	  
	 </div>
	 
	 <div class="panel panel-default">
	  <div class="panel-heading"><b>Tập phim</b></div>
	  <div class="panel-body">
	  <!-- So tap phim -->
	  {!! Form::control('number', 0, 'num', $errors, trans('back/film.number'))	!!}
	  <!-- la tap may -->
	  {!! Form::control('number', 0, 'episode', $errors, trans('back/film.episode'))	!!}
	  <!-- so diem cho phim -->
	  {!! Form::control('number', 0, 'star', $errors, trans('back/film.star'))	!!}
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
{!! HTML::script('js/jquery-ui.min.js') !!}
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

	

 $(function() {
		
		function split( val ) {
			return val.split( /,\s*/ );
		}
		function extractLast( term ) {
			return split( term ).pop();
		}

		
		$( "#language" )
			// don't navigate away from the field on tab when selecting an item
			.bind( "keydown", function( event ) {
				if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).autocomplete( "instance" ).menu.active ) {
					event.preventDefault();
				}
			})
			.autocomplete({
				minLength: 0,
				scroll: true,
				source: function( request, response ) {
					// delegate back to autocomplete, but extract the last term
					$.getJSON( "{!! url('ajax/nation') !!}", {
             			term: extractLast( request.term )
         				}, response);},
				/* focus: function() {
					// prevent value inserted on focus
					return false;					
				}, */
				select: function( event, ui ) {
					var terms = split( this.value );
					// remove the current input
					terms.pop();
					// add the selected item
					terms.push( ui.item.value );
					// add placeholder to get the comma-and-space at the end
					terms.push( "" );
					this.value = terms.join( ", " );
					return false;
				}
			})
			.focus(function() {
                $(this).autocomplete("search", "");
            })
			;

	
		 
		$( "#release_date" ).datepicker({ 
			dateFormat: 'dd/mm/yy',
			startDate: '01/01/1970',
			//showOn: 'button', 
		})
		//.val(new Date('01/01/1970').toLocaleDateString("en-US"))
		/* .next('button').button({
		    icons: {
		        primary: 'ui-icon-calendar'
		    }, text:false
		}) */
		;
	});
 /* $('#language').autocomplete({
     type: "get",
     source: function( request, response ) {
         $.getJSON( "{!! url('ajax/nation') !!}", {
             term: extractLast( request.term )
         }, response );},//"{!! url('ajax/nation') !!}",
     dataType: "json",
     minLength: 1,
     select: function( event, ui ) {
			var terms = split( this.value );
			// remove the current input
			terms.pop();
			// add the selected item
			terms.push( ui.item.value );
			// add placeholder to get the comma-and-space at the end
			terms.push( "" );
			this.value = terms.join( ", " );
			return false;
		}
	}); */
	 $(document).ready(function(){
	        $("#first_episode").autocomplete({
            source:"{!! url('ajax/films') !!}", // The source of the AJAX results
            minLength: 2, // The minimum amount of characters that must be typed before the autocomplete is triggered
            focus: function( event, ui ) { // What happens when an autocomplete result is focused on
                $("#first_episode").val( ui.item.label );
                return false;
          },
          select: function ( event, ui ) { // What happens when an autocomplete result is selected
              $("#first_episode").val( ui.item.label );
              $('#first_episode_id').val( ui.item.id );
          }
      });
	        
	       /*  $("#first_episode").on("change", function(){
		        alert(1);
	        	$('#first_episode_id').val("");
	        }); */
		  //$("#btnImage1").html("abcn");
		 
		});  
				
  </script>

@stop
