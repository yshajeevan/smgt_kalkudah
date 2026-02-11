@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('layouts.notification')
        </div>
    </div>
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Cadre Summary</h6>
    </div>
    <div class="card-body">
	    <div class="table-responsive">
		    <table class="table table-bordered" id="cadreHori">
    			<thead>
    			<tr>
    				<th></th>
    				<th></th>
    				<th></th>
    				<th colspan="3" class="text-center">{{strtoupper($col0_avi)}}</th>
    				@if(array_key_exists(1, $cols))
    				<th colspan="3" class="text-center">{{strtoupper($col1_avi)}}</th> 
    				<th colspan="3" class="highlight-light text-center">{{strtoupper(substr($col1_avi, 0, strpos($col1_avi, "_")))}}</th> 
    				@endif
    			</tr>
    			<tr>
    				<th>ID</th>
    				<th>Institute</th>
    				<th class="text-center">Students</th>
    				<th class="text-center">App</th>  
    				<th class="text-center">Avi</th> 
    				<th class="text-center">Ex/De</th>  
    				@if(array_key_exists(1, $cols))
    				<th class="text-center">App</th>  
    				<th class="text-center">Avi</th> 
    				<th class="text-center">Ex/De</th>
    				<th class="highlight-light">Tot.App</th>  
    				<th class="highlight-light">Tot.Avi</th> 
    				<th class="highlight-light">Tot.Ex/De</th>
    				@endif
    			</tr>
    			</thead>
        			@foreach($cadres as $key=>$value)
                	<tr>
        			<td>{{ $value->institute_id }}</td>
        			<td>{{ $value->institute->institute }}</td>
        			<td class="text-center">{{ $value->count }}</td>
        			<td class="text-center">{{ $value->$col0_app }}</td>
        			<td class="text-center">{{ $value->$col0_avi }}</td>
        			<td class="text-center">{{ $value->$col0_avi - $value->$col0_app }}</td>
        
        			@if(array_key_exists(1, $cols))
        			<td class="text-center">{{ $value->$col1_app }}</td>
        			<td class="text-center">{{ $value->$col1_avi }}</td>
        			<td class="text-center">{{ $value->$col1_avi - $value->$col1_app }}</td>
        			<td class="highlight-light text-center">{{ $value->$col0_app + $value->$col1_app }}</td>
        			<td class="highlight-light text-center">{{ $value->$col0_avi + $value->$col1_avi }}</td>
        			<td class="highlight-light text-center">{{ ($value->$col0_avi + $value->$col1_avi) - ($value->$col0_app + $value->$col1_app) }}</td>
        			@endif
        			</tr>
        			@endforeach
			</table>
		</div>
	</div>
</div>
@endsection

@push('styles')
<style>
.highlight-light{
	background-color:#faf1f0;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    $('#cadreHori').DataTable({
        dom: '<"dt-buttons"Bf><"clear">lirtp',
		buttons : [
		    'pdfHtml5',
            'print',
		    {
            extend : 'excel',
            text : 'Export to Excel',
            exportOptions : {
                modifier : {
                    // DataTables core
                    order : 'index',  // 'current', 'applied', 'index',  'original'
                    page : 'all',      // 'all',     'current'
                    search : 'none'     // 'none',    'applied', 'removed'
                }
            }
        } ]
    });
});

</script>
@endpush

		