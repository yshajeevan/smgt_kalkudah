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
    <div class="row">
            <div class="col">
                <div class="py-1 text-end">
                    <a href="{{route('user.create')}}" class="btn btn-primary btn-sm" data-toggle="tooltip" data-placement="bottom" title="Add User"><i class="fas fa-plus"></i> Add User</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered" id="users">
                <thead>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>email</th>
                    <th>Roles</th>
                    <th>Status</th>
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
    var table = $('#users').DataTable({
        processing: true,
        serverSide: true,
        stateSave: true,
        ajax: "{{ route('user.index') }}",
        columns: [
            {data: 'photo', name: 'photo', orderable: false, searchable: false},
            {data: 'surname', name: 'employee.surname'},
            {data: 'email', name: 'email'},
            {data: 'roles', name: 'roles',
                render: function(data, type, full, meta) {
                    return "<p class='badge rounded-pill bg-success text-light'>" + data + "</p>";
                }
            },
            {data: 'is_active', name: 'is_active',
                render: function (data, type, full, meta) {
                    if(data == 1){
                        return "<p class='badge rounded-pill bg-success text-light'>Active</p>";
                    } else {
                        return "<p class='badge rounded-pill bg-danger text-light'>In-Active</p>";
                    }
                }
            }, 
            {data: 'action', name: 'action', orderable: false, searchable: false},
        ]
    });
      
  });
</script>
@endpush