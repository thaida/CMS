@extends('back.template')

@section('main')

  @include('back.partials.entete', ['title' => trans('back/category.dashboard') . link_to_route('category.create', trans('back/category.add'), [], ['class' => 'btn btn-info pull-right']), 'icone' => 'pencil', 'fil' => trans('back/category.posts')])

	@if(session()->has('ok'))
    @include('partials/error', ['type' => 'success', 'message' => session('ok')])
	@endif

 <div class="row col-lg-12">
    <div class="pull-right link">gà c</div>
  </div>

 
@stop

@section('scripts')

  <script>
    
    $(function() {

      // Seen gestion
      $(document).on('change', ':checkbox[name="seen"]', function() {
        $(this).parents('tr').toggleClass('warning');
        $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
        var token = $('input[name="_token"]').val();
        $.ajax({
          url: '{{ url('postseen') }}' + '/' + this.value,
          type: 'PUT',
          data: "seen=" + this.checked + "&_token=" + token
        })
        .done(function() {
          $('.fa-spin').remove();
          $('input:checkbox[name="seen"]:hidden').show();
        })
        .fail(function() {
          $('.fa-spin').remove();
          chk = $('input:checkbox[name="seen"]:hidden');
          chk.show().prop('checked', chk.is(':checked') ? null:'checked').parents('tr').toggleClass('warning');
          alert('{{ trans('back/blog.fail') }}');
        });
      });

      // Active gestion
      $(document).on('change', ':checkbox[name="active"]', function() {
        $(this).hide().parent().append('<i class="fa fa-refresh fa-spin"></i>');
        var token = $('input[name="_token"]').val();
        $.ajax({
          url: '{{ url('postactive') }}' + '/' + this.value,
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
        var sens;
        if($('span', this).hasClass('fa-unsorted')) sens = 'aucun';
        else if ($('span', this).hasClass('fa-sort-desc')) sens = 'desc';
        else if ($('span', this).hasClass('fa-sort-asc')) sens = 'asc';
        // Set to zero
        $('a.order span').removeClass().addClass('fa fa-fw fa-unsorted');
        // Adjust selected
        $('span', this).removeClass();
        var tri;
        if(sens == 'aucun' || sens == 'asc') {
          $('span', this).addClass('fa fa-fw fa-sort-desc');
          tri = 'desc';
        } else if(sens == 'desc') {
          $('span', this).addClass('fa fa-fw fa-sort-asc');
          tri = 'asc';
        }
        // Wait icon
        $('.breadcrumb li').append('<span id="tempo" class="fa fa-refresh fa-spin"></span>');       
        // Send ajax
        $.ajax({
          url: '{{ url('blog/order') }}',
          type: 'GET',
          dataType: 'json',
          data: "name=" + $(this).attr('name') + "&sens=" + tri
        })
        .done(function(data) {
          $('tbody').html(data.view);
          $('.link').html(data.links);
          $('#tempo').remove();
        })
        .fail(function() {
          $('#tempo').remove();
          alert('{{ trans('back/blog.fail') }}');
        });
      })

    });

  </script>

@stop
