<?php
$counter = 1;
?>
 @foreach ($posts as $film)
<div class="lv-item media">
	<div class="checkbox pull-left">
		<label> <input type="checkbox" value=""> <i class="input-helper"></i>
		</label>
	</div>
	<div class="media-body">
		<div class="lv-title">{{ $film->title }}</div>
		<small class="lv-small">{!! $film->summary !!}</small>
		<ul class="lv-attrs">
			<li>Date Created: {{ date(config('constants.DATE_FORMAT'),	strtotime($film->created_at)) }} GMT</li>
			<li>Members: 78954</li>
			<li>Published: {{$film->publish }}</li>
		</ul>
		<div class="lv-actions actions dropdown">
			<a href="" data-toggle="dropdown" aria-expanded="true"> <i
				class="md md-more-vert"></i>
			</a>

			<ul class="dropdown-menu dropdown-menu-right">
				{!! link_to_route('film.edit',		trans('common.edit'), [$film->id], ['class' => '']) !!}
				<li><a href="">Delete</a></li>
				{!! Form::open(['method' => 'DELETE', 'route'
		=> ['film.destroy', $film->id]]) !!} {!!
		Form::destroy2(trans('common.destroy'),
		trans('common.destroy-warning')) !!} <!--  <button type="submit" class="btn btn-danger glyphicon glyphicon-remove" onclick="return confirm(\''{{trans('common.destroy-warning') }}\'')"></button> -->
		{!! Form::close() !!}
			</ul>
		</div>
	</div>
</div>


@endforeach
