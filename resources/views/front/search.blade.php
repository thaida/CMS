@extends('front.template')

@section('main')
	<div class="row">
		<div class="box">
			<div class="col-lg-12">
				<ul class="bxslider-02">


				@if(!empty($results) && $results['hits']['total'] > 0)
				
	           		@foreach($results['hits']['hits'] as $result)
	           		<li style="float: left; with: 24%; padding: 5px;">
						<a href="{{$film_url}}" title="{{ $result["_source"]["title"] }}">
							<img title="{{$result["_source"]["title"]}}"  src="{{$img_url.$result["_source"]["poster"] }}?w=300&h=430&crop-to-fit" />
						</a>
						<div class="cj-info">
							<h3 class="cj-h3">
								<a href="{{$film_url}}" title="{{$result["_source"]["title"]}}">{{$result["_source"]["title"]}}</a>
								
							</h3>
							<p class="cj-p-film">
								<span class="sp-left">Fire With Fire</span>
								               
							</p>
							<p class="cj-p-type">
								<span class="sp-left">Bản đẹp&nbsp;|&nbsp;Thuyết minh</span>
							</p>
						</div>
					</li>
	           		@endforeach
	           	@else
	           	<div style="min-height: 400px;padding: 20px">
	           		<h3 class="show-h3" >Không có kết quả tìm kiếm cho từ khóa "{{ $keyword}}"</h3>
	           	</div>
	           	 		
	           	@endif
				</ul>
			</div>
		</div>
	</div>
	<div class="clear"></div>
@stop