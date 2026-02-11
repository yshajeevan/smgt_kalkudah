@extends('layouts.master')

@section('main-content')
<div style="text-align: center;">
    <button class="btn btn-primary" id="printbutton" onClick="printdiv('printElement');">PRINT</button>
</div>
<div id="printElement"> 
  <div class="card">
    <div class="card-body">
    @foreach($employees as $employee) 
      <table style="width:100%">
        <thead>
            <tr>
                <th style="width:25%; border:none;"></th>
                <th style="width:25%; border:none;"></th>
                <th style="width:25%; border:none;"></th>
                <th style="width:25%; border:none;"></th>
            </tr>
        </thead>
        <tr>
            <td colspan="3" style="font-weight:bold; text-align: left; font-size:20px; border: none;">{{isset($employee->fullname) ? $employee->title.'.'.$employee->fullname : $employee->title.'.'.$employee->initial.'.'.$employee->surname}}</td>
            <td style="float:right; border: none;">{!! DNS2D::getBarcodeHTML($employee->nic, 'QRCODE',3,3) !!}</td>
        </tr>
        <tr>
          <td colspan="4" style="background-color:#FAF0E6; font-weight:bold">Primary Information</td>
        </tr>
        <tr>
          <td>Emp ID: <span class="td-data">{{$employee->id}}</span></td>
          <td>Paysheet Number:  <span class="td-data">{{$employee->empno}}</span></td>
          <td>NIC:  <span class="td-data">{{$employee->nic}}</span></td>
          <td>NIC(If 12 digit New): <span class="td-data">@if(strlen($employee->nicnew) > 10) {{$employee->nicnew}} @else @endif</span></td>
        </tr>
        <tr>
          <td>Title: <span class="td-data">{{$employee->titile}}</span></td>
          <td>Initial:  <span class="td-data">{{$employee->initial}}</span></td>
          <td colspan="2">Surname:  <span class="td-data">{{$employee->surname}}</span></td>
        </tr>
        <tr>
          <td colspan="3">Name in Full: <span class="td-data"> {{isset($employee->fullname) ? $employee->fullname : ''}}</span></td>
          <td>Gender: <span class="td-data">{{$employee->gender}}</span></td>
        </tr>
        <tr>
          <td>DOB: <span class="td-data">{{$employee->dob}}</span></td>
          <td>Civil Status:  <span class="td-data">{{$employee->civilstatus}}</span></td>
          <td>Ethnicity: <span class="td-data">{{$employee->ethinicity}}</span></td> 
          <td>Religion:  <span class="td-data">{{$employee->religion}}</span></td>
        </tr>
        <tr>
          <td colspan="4" style="background-color:#FAF0E6; font-weight:bold">Geographycal & Contact Information</td>
        </tr>
        <tr>
          <td colspan="4">Permanant Address: <span class="td-data">{{$employee->peraddress}}</span></td>
        </tr>
        <tr>
          <td colspan="4">Temprory Address: <span class="td-data">{{$employee->tmpaddress}}</span></td>
        </tr>
        <tr style="height:50px;">
          <td colspan="2">Residential DS: <span class="td-data">{{isset($employee->dsdivision->ds) ? $employee->dsdivision->ds : ''}}</span></td>
          <td>Residential GN:  <span class="td-data">{{isset($employee->gndivision->gn) ? $employee->gndivision->gn : ''}}</span></td>
          <td>Residential Zone: <span class="td-data">{{isset($employee->zone->zone) ? $employee->zone->zone : ''}}</span></td> 
        </tr>
        <tr>
          <td>Transportaion Mode to Office: <span class="td-data">{{isset($employee->transmode->tranmode) ? $employee->transmode->tranmode : ''}}</span></td>
          <td>Distance from Resident to School(Km):  <span class="td-data">{{$employee->distores}}</span></td>
          <td>Mobile: <span class="td-data">{{$employee->mobile}}</span><br>WhatsApp: <span class="td-data">{{$employee->mobile}}</span></td> 
          <td>Fixed Phone:  <span class="td-data">{{$employee->fixedphone}}</span></td>
        </tr>
        <tr>
          <td colspan="4">eMail address:</td>
        <tr>
          <td colspan="4" style="background-color:#FAF0E6; font-weight:bold">Service Information</td>
        </tr>
        <tr>
          <td>Service: <span class="td-data">{{$employee->empservice->service}}</span></td>
          <td>Grade:  <span class="td-data">{{$employee->grade}}</span></td>
          <td colspan="2">Duty Assmption date of Present Service: <span class="td-data">{{$employee->dtyasmfapp}}</span></td> 
        </tr>
        <tr>
          <td colspan="2">Appointment Date of Present Service: <span class="td-data">{{$employee->dtyasmcser}}</span></td> 
          <td colspan="2">Designation:  <span class="td-data">{{$employee->designation->designation}}</span></td>
        </tr>
        <tr>
          <td colspan="2">Institute: <span class="td-data">{{$employee->institute->institute}}</span></td> 
          <td colspan="2">Duty Assuption Date of Present School:  <span class="td-data">{{$employee->dtyasmprins}}</span></td>
        </tr>
        <tr>
          <td colspan="4" style="background-color:#FAF0E6; font-weight:bold">Education/Professional Qualifications</td>
        </tr>
        <tr>
          <td>Higher Edu.Qulaification: <span class="td-data">{{$employee->highqualif}}</span></td> 
          <td>Name of Degree:  <span class="td-data">{{isset($employee->degree->degree) ? $employee->degree->degree : ''}}</span></td>
          <td colspan="2">Degree Obtained from:  <span class="td-data">{{$employee->insdegree}}</span></td>
        </tr>
        <tr>
          <td>Degree Type: <span class="td-data">{{$employee->degtype}}</td> 
          <td>Degree Subject 1: <span class="td-data">{{isset($employee->degsubject1->degreesub) ? $employee->degsubject1->degreesub : ''}}</span></td> 
          <td>Degree Subject 2: <span class="td-data">{{isset($employee->degsubject2->degreesub) ? $employee->degsubject2->degreesub : ''}}</span></td>
          <td>Degree Subject 3: <span class="td-data">{{isset($employee->degsubject3->degreesub) ? $employee->degsubject3->degreesub : ''}}</span></td>
        </tr>
        <tr>
          <td colspan="4" style="background-color:#FAF0E6; font-weight:bold">Cadre/Teaching Subjects</td>
        </tr>
        <tr style="height:50px;">
          <td>Appointment Subject: <span class="td-data">{{$employee->appsubject}}</td> 
          <td>Appointment Category: <span class="td-data">{{isset($employee->appcategory->appcat) ? $employee->appcategory->appcat : ''}}</span></td> 
          <td  colspan="2">Cadre Subject: <span class="td-data">{{isset($employee->cadresubject->cadre) ? $employee->cadresubject->cadre : ''}}</span></td>
        </tr>
        <tr>
          <td colspan="2">Teach.Subject1: <span class="td-data">{{isset($employee->cadresubject1->cadre) ? $employee->cadresubject1->cadre : ''}}</span></td> 
          <td>Teach.Subject2: <span class="td-data">{{isset($employee->cadresubject2->cadre) ? $employee->cadresubject2->cadret : ''}}</span></td> 
          <td>Teach.Subject3: <span class="td-data">{{isset($employee->cadresubject3->cadre) ? $employee->cadresubject3->cadre : ''}}</span></td>
        </tr>
        <tr>
          <td colspan="4">Trained Status: <span class="td-data">{{$employee->trained}}</span></td> 
        </tr>
        
        <tr>
          <td colspan="2">Status: <span class="td-data">{{$employee->status}}</span></td> 
          <td colspan="2">Remarks: <span class="td-data">{{$employee->remark}}</span></td> 
        </tr>
        <thead>
            <tr>
                <th colspan="4" style="background-color:#FAF0E6; font-weight:bold; text-align: center;">Courses Completed/Following(More than 6 month: Certificate, Diploma, Degree, etc)</th>
            </tr>
            <tr>
                <th>Course name</th>
                <th>Institute</th>
                <th>Duration</th>
                <th>Status(Following/Completed)</th>
            </tr>
        </thead>
            <script>
            for (var i = 0; i < 8; i++) {
              document.write("<tr><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td></tr>");
            }
            </script>
        <thead>
            <tr>
                <th colspan="4" style="background-color:#FAF0E6; font-weight:bold; text-align: center;">Service History(Source:NEMIS)</th>
            </tr>
            <tr>
                <th>Zone</th>
                <th>Institute</th>
                <th>Date From</th>
                <th>Date To</th>
            </tr>
        </thead>
        @if($employee->servicehistory->count() > 0)
            @foreach($employee->servicehistory as $emp)
            <tr>
                <td>{{$emp->zone}}</td>
                <td>{{$emp->institute}}</td>
                <td>{{$emp->date_from}}</td>
                <td>{{$emp->date_to}}</td>
            </tr>
            @endforeach
            <script>
            for (var i = 0; i < 3; i++) {
              document.write("<tr><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td></tr>");
            }
            </script>
        @else
            <script>
            for (var i = 0; i < 8; i++) {
              document.write("<tr><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td></tr>");
            }
            </script>
        @endif
        <tr>
            <td colspan="4">
                <p class="declare">மேற்படி எனது விபரங்கள்(திருத்தங்களுடன்) சரியானவை என்பதனை உறுதிப்படுத்துகின்றேன்.</p>
                <br>
                <p>......................................</p>
                <p class="declare">கையொப்பம்</p>
            </td>
        </tr>
    </table>
    @endforeach
    </div>
  </div>
</div>
@endsection

@push('styles')
<style type="text/css">
table {page-break-after: always;}
        
.td-data{
    font-style: italic;
    font-weight: 500;
}

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
.declare{
    font-size:13px;
}
</style>
@endpush

@push('scripts')
<script>
$(function(){
    $('.bxslider').bxSlider({
        mode: 'fade',
        slideWidth: 600
    });
});

// function print() {
// 	printJS({
//         printable: 'printElement',
//         type: 'html',
//         targetStyles: ['*']
//     })
// }
function printdiv(elem) {
  var header_str = '<html><head><title>' + document.title  + '</title></head><body>';
  var footer_str = '</body></html>';
  var new_str = document.getElementById(elem).innerHTML;
  var old_str = document.body.innerHTML;
  document.body.innerHTML = header_str + new_str + footer_str;
  window.print();
  document.body.innerHTML = old_str;
  return false;
}
</script>
@endpush