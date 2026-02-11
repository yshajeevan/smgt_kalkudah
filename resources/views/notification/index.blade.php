@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Users List</h6>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table id="example" class="table table-bordered data-table" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Description</th>
                    <th>Causer</th>
                    <th>Last Access Date</th>
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

@push('styles')
<style>
.label-warning{
    color: yellow;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() { 
  // DataTable initialisation
  var table = $('#example').DataTable({
        processing: true,
        serverSide: true,
        stateSave:true,
        dom: '<"dt-buttons"Bf><"clear">lirtp',
        paging: true,
        autoWidth: true,
        buttons: [
				'colvis',
				'copyHtml5',
                'csvHtml5',
				'excelHtml5',
                'pdfHtml5',
				'print'
			    ],
        ajax: {
          url: "{{ route('all.notification') }}",
          data: function (d) {
                _token: "{{csrf_token()}}"
            }
        },
        columns: [
            {data: 'id', name: 'id'},
            {data: 'description', name: 'description'},
            {data: 'causer_id', name: 'causer_id'},
            {data: 'updated_at', name: 'updated_at'},
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ],
        rowCallback: function(row, data){
            if(data["id"] == 15){ //I'm assuming you're using object JSON/ajax, if not,
                                 //you'll have to find where in the data[] object the id is
                $(row).addClass("label-warning");
                //Or alternatively:
                //$(row).css("background-color","red"); if you don't want to make a css class
        }
        }
  });
});
</script>
@endpush
