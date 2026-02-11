@extends('layouts.master')

@section('main-content')
<div class="container-table">
    <h4>Total Parallel Classes (Summary)</h4>
    <table>
      <tr>
        <th>S/N</th>
        <th>Name of schools</th>
        <th>Total Parallel Classes</th>
      </tr>
      @foreach($prlclasses as $prlclass)
      <tr>
        <td></td>
        <td>{{$prlclass->institute->institute}}</td>
        <td>{{$prlclass->countprlclass}}</td>
      @endforeach
    </table>
</div>

@endsection

@push('styles')
<style type="text/css">
body
{
    counter-reset: Serial;          
}
tr td:first-child:before
{
    counter-increment: Serial;      
    content: counter(Serial); 
}
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 2px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
.container-table{
    padding:22px;
}
</style>
@endpush

@push('scripts')
<script>

</script>
@endpush