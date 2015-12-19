 @foreach ($posts as $film)
<tr {!! session('statut') == 'admin'? 'class="warning"' : '' !!}>
	<td class="text-primary"><strong>{{ $film->title }}</strong></td>
	<td>{{ date(config('constants.DATE_FORMAT'),
		strtotime($film->created_at)) }} GMT </td>
		
	<td>{!! Form::checkbox('active', $film->id, $film->active) !!}</td>
	@if(session('statut') == 'admin')
	<td>{{ $film->username }}</td> 
	@endif
	
	<td>{!! link_to_route('film.edit', trans('back/film.edit'), [$film->id],
		['class' => 'btn btn-warning btn-block']) !!}</td>
		
	<td>{!! Form::open(['method' => 'DELETE', 'route' => ['film.destroy',
		$film->id]]) !!} {!! Form::destroy(trans('back/film.destroy'),
		trans('back/film.destroy-warning')) !!} {!! Form::close() !!}</td>
</tr>
@endforeach
