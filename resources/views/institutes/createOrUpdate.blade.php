@extends('layouts.master')

@section('main-content')
<div class="row">
    <div class="col-md-12">
        @include('layouts.notification')
    </div>
</div>

<link href="{{ asset('css/insmap.css') }}" rel="stylesheet">

<div class="card">
    <div class="card-header">
        <b><i class="fas fa-file"></i>
            <span style="font-size: 13px"> Basic Information</span>
            @can('institute-edit')
            <button class="editbtn" id="edit"><i class="fas fa-edit"></i></button>
            @endcan
        </b>
    </div>
    <div class="card-body">
        <form method="post" action="{{ route('institute.update', $institutes->id) }}" id="form" name="form" enctype="multipart/form-data">
            @csrf  

            <div class="row">
                <div class="form-group col-md-3">
                    {{ Form::label('id', 'Institute ID') }} 
                    {{ Form::text('id', $institutes->id ?? '', ['id' => 'id', 'class' => 'form-control','disabled']) }}
                </div>
                <div class="form-group col-md-3">
                    {{ Form::label('census', 'Census') }} 
                    {{ Form::text('census', $institutes->census ?? '', ['id' => 'census', 'class' => 'form-control','readonly' => 'true']) }}
                </div>
                <div class="form-group col-md-3">
                    {{ Form::label('schoolid', 'School ID') }} 
                    {{ Form::text('schoolid', $institutes->schoolid ?? '', ['id' => 'schoolid', 'class' => 'form-control','readonly' => 'true']) }}
                </div>
                <div class="form-group col-md-3">
                    {{ Form::label('examid', 'Exam ID') }} 
                    {{ Form::text('examid', $institutes->examid ?? '', ['id' => 'examid', 'class' => 'form-control','readonly' => 'true']) }}
                </div>
            </div> 

            <div class="row"> 
                <div class="form-group col-md-12">
                    {{ Form::label('institute', 'Name of Institute') }}
                    {{ Form::text('institute', old('institute', $institutes->institute ?? ''), ['id' => 'institute', 'class' => 'form-control','readonly' => 'true']) }}
                </div>
            </div>

            <div class="row"> 
                <div class="form-group col-md-12">
                    {{ Form::label('institute_t', 'Name of Institute(Tamil)') }}
                    {{ Form::text('institute_t', old('institute_t', $institutes->institute_t ?? ''), ['id' => 'institute_t', 'class' => 'form-control','readonly' => 'true']) }}
                </div>
            </div>

            <div class="row"> 
                <div class="form-group col-md-4">
                    {{ Form::label('email', 'e-Mail') }} 
                    {{ Form::text('email', old('email', $institutes->email ?? ''), ['id' => 'email','class' => 'form-control','readonly' => 'true']) }}
                </div>
                <div class="form-group col-md-4">
                    {{ Form::label('startdate', 'Date of Start') }}
                    {{ Form::date('startdate', old('startdate', $institutes->startdate ?? ''), ['id' => 'startdate', 'class' => 'form-control','readonly' => 'true']) }}
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('type', 'Type') }}
                    {{ Form::select('type', ['' => '--Select Type--','Type-1AB' => 'Type-1AB','Type-1C' => 'Type-1C', 'Type-II' => 'Type-II', 'Type-III' => 'Type-III'], old('type', $institutes->type ?? ''), ['id' => 'type', 'class' => 'form-control','disabled' => 'true']) }}
                </div>
                <div class="form-group col-md-2">
                    {{ Form::label('salaryinst_id', 'Salary Institute ID') }}
                    {{ Form::text('salaryinst_id', old('salaryinst_id', $institutes->salaryinst_id ?? ''), ['id' => 'salaryinst_id', 'class' => 'form-control','readonly' => 'true']) }}
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-3">
                    {{ Form::label('span', 'Span') }}
                    {{ Form::select('span', ['' => '--Select Span--','1-5' => '1-5', '6-9' => '6-9', '1-11' => '1-11', '1-13' => '1-13', '6-13' => '6-13'], old('span', $institutes->span ?? ''), ['id' => 'span', 'class' => 'form-control','disabled' => 'true']) }}
                </div>
                <div class="form-group col-md-3">
                    {{ Form::label('division', 'Division') }}
                    {{ Form::select('division', ['' => '--Select Division--','MW' => 'MW','MSW' => 'MSW','EP' => 'EP'], old('division', $institutes->division ?? ''), ['id' => 'division', 'class' => 'form-control','disabled' => 'true']) }}
                </div>
                <div class="form-group col-md-3">
                    {{ Form::label('category', 'Category') }}
                    {{ Form::select('category', ['' => '--Select Category--','D' => 'Difficult', 'VD' => 'Very Difficult'], old('category', $institutes->category ?? ''), ['id' => 'category', 'class' => 'form-control','disabled' => 'true']) }}
                </div> 
                <div class="form-group col-md-3">
                    {{ Form::label('epsi_id', 'EPSI Coordinator') }}
                    {{ Form::select('epsi_id', [null => '--Select EPSI Coordinator--'] + $epsi, old('epsi_id', $institutes->epsi_id ?? ''), ['id' => 'epsi_id', 'class' => 'form-control','disabled' => 'true']) }}
                </div> 
            </div>

            <div class="row">
                <div class="form-group col-md-12">
                    {{ Form::label('document', 'Photos') }}
                </div>
            </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <b><i class="fas fa-map"></i><span style="font-size: 13px"> Geographical Information</span></b>
    </div>
    <div class="card-body">   
        <div class="row">
            <div class="form-group col-md-4">
                {{ Form::label('gn', 'GN Division') }}
                {{ Form::select('gn', [0 => '--Select a GN Division--'] + $gns, old('gn', $institutes->gn ?? ''), ['id' => 'gn', 'class' => 'form-control select2', 'disabled' => 'true']) }}
            </div>  
            <div class="form-group col-md-4">
                {{ Form::label('police', 'Police Station') }}
                {{ Form::select('police', ['' => '--Select Police Station--','Vavunatheevu' => 'Vavunatheevu','Kokkadicholai' => 'Kokkadicholai','Ayithyamalai' => 'Ayithyamalai','Karadiyanaru' => 'Karadiyanaru'], old('police', $institutes->police ?? ''), ['id' => 'police', 'class' => 'form-control','disabled' => 'true']) }}
            </div>
            <div class="form-group col-md-4">  
                {{ Form::label('postoffice', 'Post Office') }}
                {{ Form::select('postoffice', ['' => '--Select Post Office--','Ampilanthurai'=>'Ampilanthurai','Ayiththiyamalai'=>'Ayiththiyamalai','Kanchirankudah-148D'=>'Kanchirankudah-148D','Kannankudah'=>'Kannankudah','Karadiyanaru'=>'Karadiyanaru','Kokkaddichcholai'=>'Kokkaddichcholai','Mahilavaddavan '=>'Mahilavaddavan','Navatkadu'=>'Navatkadu','Pankudavely'=>'Pankudavely','Periyapullumalai'=>'Periyapullumalai','Unnichchai'=>'Unnichchai','Vavunathivu Sub PO'=>'Vavunathivu Sub PO'], old('postoffice', $institutes->postoffice ?? ''), ['id' => 'postoffice', 'class' => 'form-control','disabled' => 'true']) }}
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-3">  
                {{ Form::label('electorate', 'Electorate') }}
                {{ Form::select('electorate', ['' => '--Select Electorate--','Batticaloa'=>'Batticaloa','Kalkudah'=>'Kalkudah', 'Paddiruppu' => 'Paddiruppu'], old('electorate', $institutes->electorate ?? ''), ['id' => 'electorate', 'class' => 'form-control','disabled' => 'true']) }}
            </div>
            <div class="form-group col-md-3">
                {{ Form::label('cluster', 'Cluster') }} 
                {{ Form::text('cluster', old('cluster', $institutes->cluster ?? ''), ['id' => 'cluster','class' => 'form-control','readonly' => 'true']) }}
            </div>
            <div class="form-group col-md-3">
                {{ Form::label('clustercode', 'Cluster Code') }} 
                {{ Form::text('clustercode', old('clustercode', $institutes->clustercode ?? ''), ['id' => 'clustercode','class' => 'form-control','readonly' => 'true']) }}
            </div>
            <div class="form-group col-md-3">
                {{ Form::label('disfrmzeo', 'Distance from Office') }} 
                {{ Form::text('disfrmzeo', old('disfrmzeo', $institutes->disfrmzeo ?? ''), ['id' => 'disfrmzeo','class' => 'form-control','readonly' => 'true']) }}
            </div>
        </div>

        <div class="row">
            <div class="form-group col-md-6">
                {{ Form::label('schaddress', 'Address') }} 
                {{ Form::text('schaddress', old('schaddress', $institutes->schaddress ?? ''), ['id' => 'schaddress','class' => 'form-control','readonly' => 'true']) }}
            </div>
            <div class="form-group col-md-3"> 
                {{ Form::label('latitude', 'Latitude') }} 
                {{ Form::text('latitude', old('latitude', $institutes->latitude ?? ''), ['id' => 'latitude','class' => 'form-control','readonly' => 'true']) }}
            </div>
            <div class="form-group col-md-3">
                {{ Form::label('longitude', 'Longitude') }} 
                {{ Form::text('longitude', old('longitude', $institutes->longitude ?? ''), ['id' => 'longitude','class' => 'form-control','readonly' => 'true']) }}
            </div>
        </div>
    </div>
</div>

<div class="text-center my-3 mb-5" id="submitdiv" style="display:none">
    <button class="btn btn-success px-4 py-2" type="submit">Update</button>
</div>
</form>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.card { margin: 10px; }
.editbtn {
  background-color: transparent;
  border: none;
  color: green !important;
  font-size: 20px !important;
  cursor: pointer;
  float: right;
}
#submitdiv {
    margin-top: 20px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
$(document).ready(function () {
    $('#gn').select2();
    $('#edit').click(function () {
        $('#form').toggleClass('view');

        $('input').each(function () {
            var inp = $(this);
            if (inp.prop('readonly')) {
                inp.removeAttr('readonly');
            } else {
                inp.attr('readonly', 'readonly');
            }
        });

        $('select').each(function () {
            var inp = $(this);
            if (inp.prop('disabled')) {
                inp.removeAttr('disabled');
            } else {
                inp.attr('disabled', 'disabled');
            }
        });

        // Refresh select2 when enabling/disabling
        $('#gn').select2({
            dropdownParent: $('#gn').parent()
        });

        $('#submitdiv').toggle();
    });
});
</script>
@endpush
