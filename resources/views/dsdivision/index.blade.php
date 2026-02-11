@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">DS Division</h6>
    </div>
    <div class="card-body">
    @can('settings-manage')
    <a href="{{route('dsdivision.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add DS Division</a>
    @endcan
      <div class="table-responsive">
        <table id="ds-table" class="table table-bordered data-table" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>DS Division</th>
                    <th>Options</th>
                </tr>
            </thead>
            <tbody>	
            
            </tbody>	
        </table>
      </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
$(document).ready(function() { 
  // DataTable initialisation
  var table = $('#ds-table').DataTable({
        processing: true,
        serverSide: true,
        stateSave:true,
        dom: '<"dt-buttons"Bf><"clear">lirtp',
        paging: true,
        autoWidth: true,
        buttons: [
				'excelHtml5',
				'print'
			    ],
        ajax: {
          url: "{{ route('dsdivision.index') }}",
          data: function (d) {
                _token: "{{csrf_token()}}"
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'ds', name: 'ds'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]   
  });
});
</script>
@endpush
