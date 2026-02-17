@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Edit PF & Acct Officers</h6>
    </div>
    <div class="card-body">
        <table id="updateclerks" class="table table-bordered data-table">
            <thead>
                <tr>
                <th>Name of Institute</th>
                <th>PF Clerk</th>
                <th>Acct Clerk</th>
                </tr>
            </thead>
            <tbody>
                @foreach($institutes as $institute)
                    <tr>
                    <td style="width:50%">{{ $institute->institute }}</td>
                    <td style="width:25%">
                        <a href="#" id="pfclerk" class="xedit" data-pk="{{ $institute->id }}" data-type="select" data-title="Enter name" data-name="pfclerk_id"> {{ optional($institute->pfclerk)->name ?? 'Select Clerk' }}</a>
                    </td>
                    <td style="width:25%">
                        <a href="#" id="acctclerk" class="xedit" data-pk="{{ $institute->id }}" data-type="select" data-title="Enter name" data-name="acctclerk_id"> {{ optional($institute->acctclerk)->name ?? 'Select Clerk' }}</a>
                    </td>
                    </tr>
                    @endforeach
            </tbody>
        </table>
    </div>
</div>    
@endsection

@push('styles')
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet">
  <style>

   .card-body{
       padding:20px;
   }
  </style>
  @endpush

@push('scripts')
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script>

$(document).ready(function () {
var table = $('#updateclerks').DataTable({
"fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
    $.fn.editable.defaults.mode = 'inline';
    $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.xedit').editable({
            url: '{{url('/updateclerk')}}',
            title: 'Update',
            source: '{{url('/clerk')}}',
            success: function (response, newValue) {
                console.log('Updated', response)
            }
        });

}
});
$('.xedit').editable();


});
   



</script>
@endpush