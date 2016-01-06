<?php
$counter = 1;
?>
 @foreach ($posts as $film)
<tr {!! session('statut') == 'admin'? 'class="even"' : '' !!}>
	<td align="center">{{ ($posts->currentPage() - 1) * config('constants.LIMIT') + $counter++ }}</td>
	<td class="text-primary"><strong><a href={!! url('film/' . $film->slug)  !!}>{{ $film->title }}</a></strong></td>
	<td>{{ date(config('constants.DATE_FORMAT'),
		strtotime($film->created_at)) }} GMT </td>
		
	<td>{!! Form::checkbox('publish', $film->id, $film->publish) !!} <a href=""></a><i class="fa {{$film->publish ?  'fa-star' : 'fa-star-o'}}"></i></a></td>
	<td>{!! Form::checkbox('isHot', $film->id, $film->isHot) !!}
	</td>
	@if(session('statut') == 'admin')
	<td>{{ $film->username }}</td> 
	@endif
	<td>{!! link_to('film/' . $film->slug, trans('common.see'), ['class' => 'glyphicon glyphicon-info-sign']) !!}
{!! link_to_route('film.edit', trans('common.edit'), [$film->id], ['class' => 'glyphicon glyphicon-pencil']) !!}
		
	{!! Form::open(['method' => 'DELETE', 'route' => ['film.destroy', $film->id]]) !!} 
		{!! Form::destroy2(trans('common.destroy'), trans('common.destroy-warning')) !!}
		 <!--  <button type="submit" class="btn btn-danger glyphicon glyphicon-remove" onclick="return confirm(\''{{trans('common.destroy-warning') }}\'')"></button> --> 
		{!! Form::close() !!}</td>
</tr>
@endforeach
