@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Institute List</h6>
    </div>
    <div class="card-body">
      @can('institute-create')
      <!--<a href="#" class="btn btn-primary btn-sm float-left" data-toggle="tooltip" data-placement="bottom" title="Add User" style="margin:5px;"><i class="fas fa-plus"></i> Add Institute</a>-->
      <a href="{{route('instituteexport.export')}}" class="btn btn-primary btn-sm float-left" data-toggle="tooltip" data-placement="bottom" title="Add User" style="margin:5px;"><i class="fa fa-file-excel-o"></i> Export</a>
      <br>
      @endcan
      <div class="table-responsive">
        <table class="table table-bordered"" id="institutes">
          <thead>
            <tr>
              <th>ID</th>
              <th>Institute</th>
              <th>Census</th>
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
      var table = $('#institutes').DataTable( {
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
                  url: "{{ route('institute.index') }}",
                  data: function (d) {
                        _token: "{{csrf_token()}}"
                        d.search = $('input[type="search"]').val()
                  }
              },
              columns: [
                    {data: 'id', name: 'id'},
                    {data: 'institute', name: 'institute'},
                    {data: 'census', name: 'census'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},
              ],
              
        });
  })
  
</script>
@endpush