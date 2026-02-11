@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
  <div class="row">
    <div class="col-md-12">
      @include('layouts.notification')
    </div>
  </div>
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary float-left">Bulk Update</h6>
  </div>
    <h6>Count: {{$count}}</h6>
    <div class="card-body">
        <form name="add-blog-post-form" id="add-blog-post-form" method="post" action="{{route('process.bulkupdate')}}">
        @csrf
        @foreach($services as $service)
            <table>
              <tr>
                <th><input type="checkbox" class="selectAll" /></th>
                <th>Process ID</th>
                <th>Service</th>
                <th>School</th>
                <th>Month</th>
              </tr>
              @foreach($items->where('service_id',$service->service_id) as $item)
              <tr>
                <td><input type="checkbox" name="selected[{{$loop->index}}][id]" value="{{ $item->id }}"/></td>
                <td>{{ $item->id }}<input type="hidden" name="selected[{{$loop->index}}][countproc]" value="{{ $item->countproc }}"/></td>
                <td>{{ $item->service->service }}<input type="hidden" name="selected[{{$loop->index}}][serviceid]" value="{{ $item->service_id }}" /></td>
                <td>{{ $item->employee->institute->institute."(".$item->employee->namewithinitial.")"}}</td>
                <td>{{ $item->remarks }}</td>
              </tr>
              @endforeach
            </table>
            @endforeach
            <div class="button-container text-center">
                <button type="submit" class="btn btn-primary">Submit</button>  
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.button-container{
    padding: 20px;
}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
$('.selectAll').click(function(e){
    var table= $(e.target).closest('table');
    $('td input:checkbox',table).prop('checked',this.checked);
});
</script>
@endpush


