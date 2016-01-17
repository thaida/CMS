<?php
$counter = 1;
?>
 @foreach ($musics as $music)
<tr {!! session('statut') == 'admin'? 'class="even"' : '' !!}>
	<td align="center">{{ ($musics->currentPage() - 1) * config('constants.LIMIT') + $counter++ }}</td>
	<td class="text-primary"><strong><a href={!! url('music/' . $music->slug)  !!}>{{ $music->title }}</a></strong></td>
	<td>{{ date(config('constants.DATE_FORMAT'),
		strtotime($music->created_at)) }} GMT </td>
		
	<td>{!! Form::checkbox('publish', $music->id, $music->publish) !!} <a href=""></a><i class="fa {{$music->publish ?  'fa-star' : 'fa-star-o'}}"></i></a></td>
	<td>{!! Form::checkbox('isHot', $music->id, $music->isHot) !!}
	</td>
	@if(session('statut') == 'admin')
	<td>{{ $music->username }}</td> 
	@endif
	<td>{!! link_to('music/' . $music->slug, trans('common.see'), ['class' => 'glyphicon glyphicon-info-sign']) !!}
{!! link_to_route('music.edit', trans('common.edit'), [$music->id], ['class' => 'glyphicon glyphicon-pencil']) !!}
		
	{!! Form::open(['method' => 'DELETE', 'route' => ['music.destroy', $music->id]]) !!} 
		{!! Form::destroy2(trans('common.destroy'), trans('common.destroy-warning')) !!}
		 <!--  <button type="submit" class="btn btn-danger glyphicon glyphicon-remove" onclick="return confirm(\''{{trans('common.destroy-warning') }}\'')"></button> --> 
		{!! Form::close() !!}</td>
</tr>
@endforeach
