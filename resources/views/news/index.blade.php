@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('layouts.notification')
         </div>
     </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">{{Str::plural(ucwords(str_replace('_', ' ', Request::segment(1))))}}</h6>
    </div>
    <div class="card-body">
    <div class="row">
            <div class="col">
                <div class="py-1 text-end">
                    <a href="{{route(Request::segment(1).'.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="bottom" title="Add {{ucwords(str_replace('_', ' ', Request::segment(1)))}}"><i class="fas fa-plus"></i> Add {{ucwords(str_replace('_', ' ', Request::segment(1)))}}</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="table">
                <thead>
                    <th>S/N</th>
                    <th>Name</th>
                    <th>Options</th>
                </thead>				
            </table>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script type="text/javascript">
  $(function () {
      
    var table = $('#table').DataTable({
        processing: true,
        serverSide: true,
        dom: '<"dt-buttons"Bf><"clear">lirtp',
        paging: true,
        autoWidth: true,
        buttons: [
                'colvis',
                'copyHtml5',
                'excelHtml5',
                'pdfHtml5',
                'print'
			    ],
        ajax: {
            url: "{{ route(request()->path().'.index') }}",
            data: function (d) {
                _token: "{{csrf_token()}}"
            }
        },
        columns: [
            {data: 'DT_RowIndex',orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
      
  });

  $('#table').on('click', '.btn-delete[data-remote]', function (e) { 
    e.preventDefault();
     $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var url = $(this).data('remote');
    console.log(url);
    // confirm then
    if (confirm('Are you sure you want to delete this record?')) {
        $.ajax({
            url: url,
            type: 'DELETE',
            dataType: 'json',
            data: {method: '_DELETE', submit: true}
        }).always(function (data) {
            $('#table').DataTable().draw(false);
        });
    }else
        alert("You have cancelled!");
    
});
</script>
@endpush