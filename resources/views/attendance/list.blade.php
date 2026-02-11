@extends('layouts.master')

@section('main-content')
<section class="">
  <div class="container">
    <table>
      <thead>
        <tr class="header">
          <th>
            S/N
            <div>S/N</div>
          </th>
          <th>
            Name of School
            <div>Name of School</div>
          </th>
          <th>
            Total Students
            <div>Total Students</div>
          </th>
          <th>
            % of Presented Students
            <div>% of Presented Students</div>
          </th>
          <th>
            Total Teachers
            <div>Total Teachers</div>
          </th>
          <th>
            % of Teachers
            <div>% of Presented Teachers</div>
          </th>
          <th>
            Total DOs
            <div>Total DOs</div>
          </th>
          <th>
            % of DOs
            <div>% of DOs</div>
          </th>
        </tr>
      </thead>
      <tbody>
            @foreach($attendances as $attendance)   
                <tr>
                    <td></td>
                    <td>{{$attendance->adate}}</td>
                    <td>{{$attendance->totstu}}</td>
                    <td>{{$attendance->prstu}}</td>
                    <td>{{$attendance->tottea}}</td>
                    <td>{{$attendance->prtea}}</td>
                    <td>{{$attendance->tottrainee}}</td>
                    <td>{{$attendance->prtrainee}}</td>
                </tr>  
            @endforeach
          </tbody>
        </table>
      </div>
    </section>
@endsection

@push('styles')
<style>
    html, body{
  margin:0;
  padding:0;
  height:100%;
}
section {
  position: relative;
  border: 1px solid #000;
  padding-top: 37px;
  background: #500;
}
section.positioned {
  position: absolute;
  top:100px;
  left:100px;
  width:800px;
  box-shadow: 0 0 15px #333;
}
.container {
  overflow-y: auto;
  height: 260px;
}
table {
  border-spacing: 0;
  width:100%;
}
td + td {
  border-left:1px solid #eee;
}
td, th {
  border-bottom:1px solid #eee;
  background: #ddd;
  color: #000;
  padding: 10px 25px;
}
th {
  height: 0;
  line-height: 0;
  padding-top: 0;
  padding-bottom: 0;
  color: transparent;
  border: none;
  white-space: nowrap;
}
th div{
  position: absolute;
  background: transparent;
  color: #fff;
  padding: 9px 25px;
  top: 0;
  margin-left: -25px;
  line-height: normal;
  border-left: 1px solid #800;
}
th:first-child div{
  border: none;
}
<section class="">
  <div class="container">
    <table>
</style>
@endpush

@push('scripts')
<script>
   var addSerialNumber = function () {
    $('table tr').each(function(index) {
        $(this).find('td:nth-child(1)').html(index+0);
    });
};

addSerialNumber();
  </script>
 
@endpush