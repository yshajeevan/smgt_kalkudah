@extends('layouts.master')

@section('main-content')
{{$instid }}
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
        <div class="table-responsive">
            <table class="table table-bordered" id="table">
                <thead>
                    <th>S/N</th>
                    <th>Subject</th>
                    <th>App</th>
                    <th>Avi</th>
                    <th>ExD</th>
                </thead>				
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
  $(function () {
    // console.log({{ json_encode($designation) }};)
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
			    
        ajax: ({
            url: "{{ route('cadre.index') }}",
            data: {
                instid: {!! json_encode($instid) !!},
                designation: {!! json_encode($designation) !!},
                _token: "{{csrf_token()}}"           
            }

        }),
        columns: [
            {data: 'DT_RowIndex',orderable: false, searchable: false},
            {data: 'cadresubject', name: 'cadresubject.name'},
            {data: 'app_cadre', name: 'app_cadre',orderable: false, searchable: false},
            {data: 'avi_cadre', name: 'avi_cadre',orderable: false, searchable: false},
            {data: 'exd_cadre', name: 'exd_cadre',orderable: false, searchable: false},

        ]
    });
      
  });
</script>
@endpush