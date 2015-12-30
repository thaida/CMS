<?php
$counter = 1;
?>
 @foreach ($posts as $film)
<tr {!! session('statut') == 'admin'? 'class="even"' : '' !!}>
	<td align="center">{{ ($posts->currentPage() - 1) * config('constants.LIMIT') + $counter++ }}</td>
	<td class="text-primary"><strong>{{ $film->title }}</strong></td>
	<td>{{ date(config('constants.DATE_FORMAT'),
		strtotime($film->created_at)) }} GMT </td>
		
	<td>{!! Form::checkbox('publish', $film->id, $film->publish) !!}</td>
	<td>{!! Form::checkbox('isHot', $film->id, $film->isHot) !!}</td>
	@if(session('statut') == 'admin')
	<td>{{ $film->username }}</td> 
	@endif
	<td>{!! link_to('film/' . $film->slug, trans('common.see'), ['class' => 'glyphicon glyphicon-info-sign']) !!}
{!! link_to_route('film.edit', trans('common.edit'), [$film->id], ['class' => 'glyphicon glyphicon-pencil']) !!}
		
	{!! Form::open(['method' => 'DELETE', 'route' => ['film.destroy', $film->id]]) !!} 
		{!! Form::destroy(trans('common.destroy'), trans('common.destroy-warning')) !!} 
		{!! Form::close() !!}</td>
</tr>
@endforeach
