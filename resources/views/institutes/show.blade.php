@extends('layouts.master')

@section('main-content')
<div id="printElement"> 
<div class="card">
  <div class="card-body">
    <table style="width:100%">
      <thead>
      <th colspan="4" style="background-color:#FAF0E6; font-weight:bold; text-align: center;">{{$institutes->institute}}</th>
      </thead>
      <tr>
        <td colspan="4" style="background-color:#FAF0E6; font-weight:bold">Primary Information</td>
      </tr>
      <tr>
        <td>Institute ID: {{$institutes->id}}</td>
        <td>Census:  {{$institutes->census}}</td>
        <td>School ID:  {{$institutes->schoolid}}</td>
        <td>Exam ID: {{$institutes->examid}}</td>
      </tr>
      <tr>
        <td colspan="4">Name of Institute (English): {{$institutes->institute}}</td>
      </tr>
      <tr>
        <td colspan="4">Name of Institute (Tamil): {{$institutes->institute_t}}</td>
      </tr>
      <tr>
        <td>Cluster Code: {{$institutes->cluster."_".$institutes->clustercode}} </td>
        <td>If 1000 School:  {{$institutes->if1000scl}}</td>
        <td>GIT Exam Center:  {{$institutes->gitcentre}}</td>
        <td>Category: {{$institutes->category}}</td>
      </tr>
      <tr>
        <td>Start Date: {{$institutes->startdate}} </td>
        <td>Type:  {{$institutes->type}}</td>
        <td>Span:  {{$institutes->span}}</td>
        <td>Education Divition: {{$institutes->division}}</td>
      </tr>
      <tr>
        <td colspan="4" style="background-color:#FAF0E6; font-weight:bold">Geo Locationn</td>
      </tr>
      <tr>
        <td colspan="2">Address: {{$institutes->schaddress}}</td>
        <td>GN Division:  {{$institutes->gn}}</td>
        <td>GN Area:  {{$institutes->gnarea}}Km2</td>
      </tr>
      <tr>
        <td>Electorate: {{$institutes->electorate}}</td>
        <td>GPS Location:  {{$institutes->gpslocation}}</td>
        <td>Land Area:  {{$institutes->landarea}} Acres</td>
        <td>Distance from ZEO:  {{$institutes->disfrmzeo}}Km</td>
      </tr>
      <tr>
        <td colspan="2">Police Station: {{$institutes->police}}</td>
        <td colspan="2">Post Office:  {{$institutes->postoffice}}</td>
      </tr>
      <tr>
        <td colspan="4" style="background-color:#FAF0E6; font-weight:bold">Contact Information</td>
      </tr>
      <tr>
        <td>Name of Principal: {{$institutes->principal}}</td>
        <td>email:  {{$institutes->email}}</td>
        <td>Phone:  {{$institutes->mobile}}</td>
        <td>Phone:  {{$institutes->fixedline}}</td>
      </tr>
      <tr>
        <td colspan="4" style="background-color:#FAF0E6; font-weight:bold">Statistical Information</td>
      </tr>
      <tr>
        <td>Total Boys: {{$institutes->stuboys}}</td>
        <td>Total Girls:  {{$institutes->stugirls}}</td>
        <td>Total Students:  {{$institutes->totstu}}</td>
        <td>Total Parellel Classes:  {{$institutes->stuboys}}</td>
      </tr>
      <tr>
        <td>Teachers Male: {{$institutes->teachermale}}</td>
        <td>Teachers Female:  {{$institutes->teacherfemale}}</td>
        <td>Total Teachers:  {{$institutes->teachermale + $institutes->teacherfemale}}</td>
        <td>No.of DOs:  {{$institutes->grtrainee}}</td>
      </tr>
      <tr>
        <td>No.of Lab Assistants: {{$institutes->labassistant}}</td>
        <td>No.of Library Assistants:  {{$institutes->libryassistant}}</td>
        <td>No.of School Labors:  {{$institutes->schlabour}}</td>
        <td>No.of School Watchers:  {{$institutes->schwatcher}}</td>
      </tr>
      <tr>
        <td>No.of School Coachers: {{$institutes->sportscoach}}</td>
        <td>SLPS Principal Avi:  {{$institutes->countprincipal}}</td>
        <td>Performing Principal:  {{$institutes->perprincipal}}</td>
        <td>Total DOs:  {{$institutes->schwatcher}}</td>
      </tr>
  </table>
  </div>
</div>
  
</div>

<div style="text-align: center;">
  <button class="btn btn-primary" id="printButton">Print</button>
</div>
@endsection

@push('styles')
<style type="text/css">
        table {page-break-before: always;}
</style>

<style>
.logo-zoom{ height:200vh;}
.logo-zoom img{ margin:30vh auto 0px; display:flex; position: fixed; left:0px; right:0px; z-index:1; transition:0.3s;}

@media only screen and (min-width: 675px) {
    .center-gallery {
        width: 50%;
        margin: auto;
    }
}
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}

</style>
@endpush

@push('scripts')
<script type="text/javascript" src="https://unpkg.com/pinchzoom@0.8.3/lib/pinchzoom.js"></script>
<script src="{{asset('js/wheelzoom.js')}}"></script>
<script>
$(function(){
    $('.bxslider').bxSlider({
        mode: 'fade',
        slideWidth: 600
    });
});

function print() {
	printJS({
    printable: 'printElement',
    type: 'html',
    targetStyles: ['*']
 })
}

document.getElementById('printButton').addEventListener ("click", print)
</script>
@endpush