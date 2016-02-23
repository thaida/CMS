@extends('back.template') 
@section('main')
	@include('back.partials.entete', ['title' =>trans('back/film.dashboard'). link_to_route('film.create',trans('common.add'), [], ['class' =>'btn btn-info pull-right']), 'icone'
					=> 'pencil', 'fil' => trans('back/film.location')])
@if(session()->has('ok')) 
	@include('partials/error', ['type' =>'success', 'message' => session('ok')]) 
@endif

<div class="container">
	<div class="block-header">
		<h2>Danh sach film</h2>
	</div>

	<div class="card">
		<div class="listview lv-bordered lv-lg">
			<div class="lv-header-alt">
				<h2 class="lvh-label hidden-xs">Tat ca film</h2>

				<ul class="lv-actions actions">
					<li><a href=""> <i class="md md-access-time"></i>
					</a></li>
					<li class="dropdown"><a href="" data-toggle="dropdown"
						aria-expanded="true"> <i class="md md-sort"></i>
					</a>

						<ul class="dropdown-menu dropdown-menu-right">
							<li><a href="">Last Modified</a></li>
							<li><a href="">Last Edited</a></li>
							<li><a href="">Name</a></li>
							<li><a href="">Date</a></li>
						</ul></li>
					<li><a href=""> <i class="md md-info"></i>
					</a></li>
					<li class="dropdown"><a href="" data-toggle="dropdown"
						aria-expanded="true"> <i class="md md-more-vert"></i>
					</a>

						<ul class="dropdown-menu dropdown-menu-right">
							<li><a href="">Refresh</a></li>
							<li><a href="">Listview Settings</a></li>
						</ul></li>
				</ul>
			</div>

			<div class="lv-body">
				@include('back.film.table')				
			
			</div>
		</div>
		<div >{!! $links !!}</div>
		
		<!-- <ul class="pagination lv-pagination">
			<li><a href="" aria-label="Previous"> <i class="md md-chevron-left"></i>
			</a></li>
			<li class="active"><a href="">1</a></li>
			<li><a href="">2</a></li>
			<li><a href="">3</a></li>
			<li><a href="">4</a></li>
			<li><a href="">5</a></li>
			<li><a href="" aria-label="Next"> <i class="md md-chevron-right"></i>
			</a></li>
		</ul> -->
	</div>
</div>




@stop
