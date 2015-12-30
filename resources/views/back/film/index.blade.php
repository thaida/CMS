@extends('back.template') @section('main')

@include('back.partials.entete', ['title' => trans('back/film.dashboard')
. link_to_route('film.create', trans('common.add'), [], ['class' =>
'btn btn-info pull-right']), 'icone' => 'pencil', 'fil' =>
trans('back/film.location')]) 
@if(session()->has('ok'))
@include('partials/error', ['type' => 'success', 'message' =>
session('ok')]) @endif

<div class="panel panel-primary ">
	<div class="panel-heading">
		<h4 class="panel-title"> <i data-hc="white" data-c="#fff" data-loop="true" data-size="16" data-name="user" class="livicon" id="livicon-48" style="width: 16px; height: 16px;"><svg height="16" version="1.1" width="16" xmlns="http://www.w3.org/2000/svg" style="overflow: hidden; position: relative;" id="canvas-for-livicon-48"><desc>Created with Raphaël 2.1.0</desc><defs/><path style="" fill="#ffffff" stroke="none" d="M21.291,21.271C20.116,20.788,19.645,19.452,19.645,19.452S19.116,19.756,19.116,18.908C19.116,18.058,19.645,19.452,20.176,16.179000000000002C20.176,16.179000000000002,21.644,15.753000000000002,21.351999999999997,12.238000000000003H20.997999999999998C20.997999999999998,12.238000000000003,21.880999999999997,8.479000000000003,20.997999999999998,7.206000000000003C20.115999999999996,5.933000000000003,19.763999999999996,5.085000000000003,17.820999999999998,4.477000000000003C15.879999999999997,3.8700000000000028,16.587999999999997,3.991000000000003,15.174999999999997,4.053000000000003C13.762999999999998,4.1140000000000025,12.585999999999997,4.902000000000003,12.585999999999997,5.325000000000003C12.585999999999997,5.325000000000003,11.703999999999997,5.386000000000003,11.351999999999997,5.750000000000003C10.998999999999997,6.1140000000000025,10.410999999999996,7.810000000000002,10.410999999999996,8.235000000000003S10.805999999999996,11.509000000000004,11.099999999999996,12.116000000000003L10.648999999999996,12.237000000000004C10.354999999999995,15.752000000000004,11.824999999999996,16.178000000000004,11.824999999999996,16.178000000000004C12.353999999999996,19.450000000000003,12.883999999999995,18.057000000000006,12.883999999999995,18.907000000000004C12.883999999999995,19.755000000000003,12.353999999999996,19.451000000000004,12.353999999999996,19.451000000000004S11.883999999999995,20.787000000000003,10.707999999999995,21.270000000000003C9.530999999999995,21.755000000000003,3.002999999999995,24.361000000000004,2.471999999999994,24.906000000000002C1.942,25.455,2.002,28,2.002,28H29.997999999999998C29.997999999999998,28,30.058999999999997,25.455,29.526999999999997,24.906C28.996,24.361,22.468,21.756,21.291,21.271Z" stroke-width="0" transform="matrix(0.5,0,0,0.5,0,0)"/></svg></i>
			Users List
		</h4>
	</div>
    <br>
	<div class="panel-body">
	
                
<div class="row col-lg-12">
	<div class="pull-right link">{!! $links !!}</div>
</div>

<div class="row col-lg-12">
	<div class="table-responsive">
		<table class="table table-bordered  dataTable no-footer">
			<thead>
				<tr>
					<th>STT</th>
					<th>{{ trans('common.title') }} <a href="#" name="title"
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
					<th>{{ trans('common.published') }} <a href="#" name="publish"
						class="order"> <span
							class="fa fa-fw fa-{{  $order->name == 'publish' ? $order->sort : 'unsorted' }}">
						</span>
					</a>
					<th>{{ trans('common.isFront') }} <a href="#" name="isHot"
						class="order"> <span
							class="fa fa-fw fa-{{  $order->name == 'isHot' ? $order->sort : 'unsorted' }}">
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
			<tbody>@include('back.film.table')
			</tbody>
		</table>
	</div>
</div>

<div class="row col-lg-12">
	<div class="pull-right link">{!! $links !!}</div>
</div>
</div>
</div>
@stop 
@section('scripts')

<script>
    
    $(function() {
     
      // Active gestion
      $(document).on('change', ':checkbox[name="publish"]', function() {
        $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
        var token = $('input[name="_token"]').val();
        $.ajax({
          url: '{{ url('filmpublish') }}' + '/' + this.value,
          type: 'PUT',
          data: "publish=" + this.checked + "&_token=" + token
        })
        .done(function() {
          $('.fa-spin').remove();
          $('input:checkbox[name="publish"]:hidden').show();
        })
        .fail(function() {
          $('.fa-spin').remove();
          chk = $('input:checkbox[name="publish"]:hidden');
          chk.show().prop('checked', chk.is(':checked') ? null:'checked').parents('tr').toggleClass('warning');
          alert('{{ trans('back/film.activefail') }}');
        });
      });

      $(document).on('change', ':checkbox[name="isHot"]', function() {
          $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
          var token = $('input[name="_token"]').val();
          $.ajax({
            url: '{{ url('filmhot') }}' + '/' + this.value,
            type: 'PUT',
            data: "ishot=" + this.checked + "&_token=" + token
          })
          .done(function() {
            $('.fa-spin').remove();
            $('input:checkbox[name="isHot"]:hidden').show();
          })
          .fail(function() {
            $('.fa-spin').remove();
            chk = $('input:checkbox[name="isHot"]:hidden');
            chk.show().prop('checked', chk.is(':checked') ? null:'checked').parents('tr').toggleClass('warning');
            alert('{{ trans('back/film.hotfail') }}');
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
          url: '{{ url('film/order') }}',
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
