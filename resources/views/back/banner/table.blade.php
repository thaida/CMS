<?php
$counter = 1;
?>
 @foreach ($posts as $film)
<tr {!! session('statut') == 'admin'? 'class="warning"' : '' !!}>
	<td align="center">{{ ($posts->currentPage() - 1) * config('constants.LIMIT') + $counter++ }}</td>
	<td class="text-primary"><strong>{{ $film->title }}</strong></td>
	<td>{{ date(config('constants.DATE_FORMAT'),
		strtotime($film->created_at)) }} GMT </td>
		
	<td>{!! Form::checkbox('publish', $film->id, $film->publish) !!}</td>
	@if(session('statut') == 'admin')
	<td>{{ $film->username }}</td> 
	@endif
	<td>{!! link_to('banner/' . $film->slug, trans('common.see'), ['class' => 'btn btn-success btn-block btn']) !!}</td>
	<td>{!! link_to_route('banner.edit', trans('common.edit'), [$film->id],
		['class' => 'btn btn-warning btn-block']) !!}</td>
		
	<td>{!! Form::open(['method' => 'DELETE', 'route' => ['banner.destroy',	$film->id]]) !!} 
		{!! Form::destroy(trans('common.destroy'), trans('common.destroy-warning')) !!} 
		{!! Form::close() !!}</td>
</tr>
@endforeach
