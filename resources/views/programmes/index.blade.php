@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>
    
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Programmes and Coordinators</h6>
    </div>
    @can('settings-manage')
    <div>
    <a href="{{route('programme.create')}}" class="btn btn-primary btn-sm float-right" data-toggle="tooltip" data-placement="bottom" title="Add Programme"><i class="fas fa-plus"></i> Add Programme</a>
    </div>
    @endcan
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-bordered"" id="programmes">
          <thead>
            <tr>
              <th>ID</th>
              <th>Programme</th>
              <th>Coordinator</th>
              <th>Phone</th>
              <th>email</th>
              <th>Action</th>
              </tr>
          </thead>
          <tbody>
           
          </tbody>
        </table>
    </div>
  </div>
</div>
@endsection

@push('styles')
  <style>
    
  </style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
      var table = $('#programmes').DataTable( {
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
                  url: "{{ route('programme.index') }}",
                  data: function (d) {
                        _token: "{{csrf_token()}}"
                        d.search = $('input[type="search"]').val()
                  }
              },
              columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'surname', name: 'coordinator.surname'},
                    {data: 'phone', name: 'coordinator.phone'},
                    {data: 'email', name: 'coordinator.email'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
              ],
              
        });
})
 $('#programmes').on('click', '.btn-delete[data-remote]', function (e) { 
    e.preventDefault();
     $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var url = $(this).data('remote');
    // confirm then
    if (confirm('Are you sure you want to delete this record?')) {
        $.ajax({
            url: url,
            type: 'DELETE',
            dataType: 'json',
            data: {method: '_DELETE', submit: true}
        }).always(function (data) {
            $('#programmes').DataTable().draw(false);
        });
    }else
        alert("You have cancelled!");
    
}); 
</script>
@endpush