@extends('back.template') @section('main')

@include('back.partials.entete', ['title' => trans('back/category.dashboard')
. link_to_route('cat.create', trans('common.add'), [], ['class' =>
'btn btn-info pull-right']), 'icone' => 'pencil', 'fil' =>
trans('back/category.location')]) @if(session()->has('ok'))
@include('partials/error', ['type' => 'success', 'message' =>
session('ok')]) @endif

<div class="row col-lg-12">
	<div class="pull-right link">{!! $links !!}</div>
</div>

<div class="row col-lg-12">
	<div class="table-responsive">
		<table class="table">
			<thead>
				<tr>
					<th>{{ trans('back/category.title') }} <a href="#" name="title"
						class="order"> <span
							class="fa fa-fw fa-{{ $order->name == 'title' ? $order->sort : 'unsorted' }}">
						</span>
					</a>
					</th>
					<th>{{ trans('common.create_date') }} <a href="#" name="created_at"
						class="order"><span
							class="fa fa-fw fa-{{  $order->name == 'created_at' ? $order->sort : 'unsorted' }}">
						</span> </a>
					</th>
					<th>{{ trans('common.published') }} <a href="#" name="active"
						class="order"> <span
							class="fa fa-fw fa-{{  $order->name == 'active' ? $order->sort : 'unsorted' }}">
						</span>
					</a>
					</th> @if(session('statut') == 'admin')
					<th>{{ trans('common.author') }} <a href="#" name="username"
						class="order"> <span
							class="fa fa-fw fa-{{  $order->name == 'username' ? $order->sort : 'unsorted' }}">
						</span>
					</a>
					</th>
					<th>{{ trans('back/blog.seen') }} <a href="#" name="posts.seen"
						class="order"> <!--   <span class="fa fa-fw fa-{{ $order->sort }}">
                </span> -->
					</a>
					</th> @endif
				</tr>
			</thead>
			<tbody>@include('back.cat.table')
			</tbody>
		</table>
	</div>
</div>

<div class="row col-lg-12">
	<div class="pull-right link">{!! $links !!}</div>
</div>

@stop @section('scripts')

<script>
    
    $(function() {
     
      // Active gestion
      $(document).on('change', ':checkbox[name="active"]', function() {
        $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
        var token = $('input[name="_token"]').val();
        $.ajax({
          url: '{{ url('catactive') }}' + '/' + this.value,
          type: 'PUT',
          data: "active=" + this.checked + "&_token=" + token
        })
        .done(function() {
          $('.fa-spin').remove();
          $('input:checkbox[name="active"]:hidden').show();
        })
        .fail(function() {
          $('.fa-spin').remove();
          chk = $('input:checkbox[name="active"]:hidden');
          chk.show().prop('checked', chk.is(':checked') ? null:'checked').parents('tr').toggleClass('warning');
          alert('{{ trans('back/blog.fail') }}');
        });
      });

      // Sorting gestion
      $('a.order').click(function(e) {
        e.preventDefault();
        // Sorting direction
        var sort;
        if($('span', this).hasClass('fa-unsorted')) sort = 'aucun';
        else if ($('span', this).hasClass('fa-sort-desc')) sort = 'desc';
        else if ($('span', this).hasClass('fa-sort-asc')) sort = 'asc';
        // Set to zero
        $('a.order span').removeClass().addClass('fa fa-fw fa-unsorted');
        // Adjust selected
        $('span', this).removeClass();
        var tri;
        if(sort == 'aucun' || sort == 'asc') {
          $('span', this).addClass('fa fa-fw fa-sort-desc');
          tri = 'desc';
        } else if(sort == 'desc') {
          $('span', this).addClass('fa fa-fw fa-sort-asc');
          tri = 'asc';
        }
        // Wait icon
        $('.breadcrumb li').append('<span id="tempo" class="fa fa-refresh fa-spin"></span>');       
        // Send ajax
        $.ajax({
          url: '{{ url('cat/order') }}',
          type: 'GET',
          dataType: 'json',
          data: "name=" + $(this).attr('name') + "&sort=" + tri
        })
        .done(function(data) {
          $('tbody').html(data.view);
          $('.link').html(data.links);
          $('#tempo').remove();
        })
        .fail(function() {
          $('#tempo').remove();
          alert('{{ trans('back/cat.fail') }}');
        });
      })

    });

  </script>

@stop
