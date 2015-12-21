 @foreach ($posts as $cat)
<tr {!! !$cat->
	seen && session('statut') == 'admin'? 'class="warning"' : '' !!}>
	<td class="text-primary"><strong>{{ $cat->title }}</strong></td>
	<td>{{ date(config('constants.DATE_FORMAT'),
		strtotime($cat->created_at)) }} GMT </td>
		
	<td>{!! Form::checkbox('active', $cat->id, $cat->active) !!}</td>
	@if(session('statut') == 'admin')
	<td>{{ $cat->username }}</td> 
	@endif
	<td>{{ $cat->catname }}</td>
	<td>{!! link_to_route('subcat.edit', trans('common.edit'), [$cat->id],
		['class' => 'btn btn-warning btn-block']) !!}</td>
		
	<td>{!! Form::open(['method' => 'DELETE', 'route' => ['subcat.destroy',
		$cat->id]]) !!} {!! Form::destroy(trans('common.destroy'),
		trans('common.destroy-warning')) !!} {!! Form::close() !!}</td>
</tr>
@endforeach
