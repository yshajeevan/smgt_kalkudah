@extends('layouts.master')

@section('main-content')
<div class="container-table">
    <h4>Total Students (Summary)</h4>
    <table>
      <tr>
        <th>S/N</th>
        <th>Name of schools</th>
        <th>Total Students</th>
      </tr>
      @foreach($students as $student)
      <tr>
        <td></td>
        <td>{{$student->institute->institute}}</td>
        <td>{{$student->totstudent}}</td>
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