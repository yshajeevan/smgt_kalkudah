<!-- @php
    $template = Auth::user()->hasAnyRole(['Sch_Admin']) ? 'layouts.school.master' : 'layouts.master';
@endphp -->

@extends('layouts.master')

@section('main-content')
<div class="row">
    <div class="col-12">
        @include('layouts.notification')
    </div>
</div>
<div class="row">
    <div class="col-lg-3">
        <div class="card lookup">
            <div class="card">
                <div class="image">
                    <form action="{{isset($employee) ? route('employee.photoupdate',$employee->id) : ''}}" name="profile_photo" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="profile-pic-div">
                        @if(!empty($employee->photo))
                        <img class="card-img-top img-fluid roundend-circle mt-4" style="border-radius:50%;height:120px;width:120px;margin:auto;" src="{{asset('vfiles/profileimg/').'/'.$employee->photo}}" id="photo">
                        @else 
                        <img class="card-img-top img-fluid roundend-circle mt-4" style="border-radius:50%;height:120px;width:120px;margin:auto;" src="{{asset('backend/img/avatar.png')}}" id="photo">
                        @endif
                        @can('employee-edit')
                        <input type="file" id="filepro" name="file" required>
                        <label for="filepro" id="uploadBtn">Choose Photo</label>
                        @endcan
                    </div>
                    @can('employee-edit')
                        <button style="display:none" class="btn" id="proimgupdate"><i class='fas fa-check-circle' style='color: green'></i></button>
                    @endcan
                    </div>
                    @if(!empty($employee->id))
                    <div class="card-body mt-4 ml-2">
                      <h5 class="card-title text-left"><small><i class="fas fa-user"></i> {{$employee->title.".".$employee->name_with_initial_e}}</small></h5>
                      <p class="card-text text-left"><small><i class="fas fa-home"></i> {{$employee->peraddress}}</small></p>
                      <!--<p class="card-text text-left"><small><i class="fas fa-phone"></i> {{$employee->mobile}}</small></p>-->
                      <a class="card-text text-left" href="tel:+94{{isset($employee->mobile) ? $employee->mobile : 'N/A' }}"><i class="fa fa-phone"></i><small>{{$employee->mobile}}</small></a></br>
                      <a class="card-text text-left" href="https://api.whatsapp.com/send?phone=94{{isset($employee->whatsapp) ? $employee->whatsapp : 'N/A' }}"><i class="fab fa-whatsapp-square"></i><small>{{$employee->whatsapp}}</small></a></br>
                      <a class="card-text text-left" href="mailto: {{isset($employee->email) ? $employee->email : 'N/A'}}"><i class="fa fa-envelope"></i><small>{{$employee->email}}</small></a>
                    </div>
                     @endif
                </form>
            </div>
            <div class="card-header">
                Go to section
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="#" class="nav-jump" data-target="#bioinfo">Biological Info</a></li>
                <li class="list-group-item"><a href="#" class="nav-jump" data-target="#geoinfo">Geographical Info</a></li>
                <li class="list-group-item"><a href="#" class="nav-jump" data-target="#serviceinfo">Service Info</a></li>
                <li class="list-group-item"><a href="#" class="nav-jump" data-target="#qualifinfo">Qualifications</a></li>
                <li class="list-group-item"><a href="#" class="nav-jump" data-target="#subjectinfo">Subjects</a></li>
                <li class="list-group-item"><a href="#" class="nav-jump" data-target="#statusinfo">Current Status</a></li>
            </ul>
        </div>
    </div>
    <div class="col-lg-9 view" id="form">
        @if (Auth::user()->hasRole('Sch_Admin'))
            <form id="employee_form" method="POST" action="{{route('dummyemployee.store',$employee->id)}}" accept-charset="UTF-8" enctype="multipart/form-data">
        @else
            <form id="employee_form" method="POST" action="{{ isset($employee) ? route('employee.update',$employee->id) : route('employee.store') }}" accept-charset="UTF-8" enctype="multipart/form-data">
        @endif
        @csrf
        @if(isset($employee))
            @method('PUT')
        @endif
        <fieldset>
        <div class="form-card">
        <div class="form-numbering">
        <div class="card bioinfo" id="bioinfo">
            <div class="card-header">
                <b> <i class="fas fa-file"></i><span style="font-size: 13px"> Biological Information </span> </b>@can('employee-edit')<button type="button" class="editbtn" id="edit"><i class="fas fa-edit"></i></button>@endcan
            </div>
            <div class="card-body">
                @if(isset($employee))
                    <input type="hidden" class="form-control form-control-sm" id="id" value="{{ old('id', isset($employee) ? $employee->id : '') }}" disabled>
                @endif
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Paysheet Number</label>
                    </div>
                    <div class="form-col-9">        
                        <input type="text" class="form-control form-control-sm" name="empno" id="empno" value="{{ old('empno', isset($employee) ? $employee->empno : '') }}" required readonly>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">NIC</label>
                    </div>
                    <div class="form-col-9">
                        <input type="text" class="form-control form-control-sm float-double" name="nic" id="nic" value="{{ old('nic', isset($employee) ? $employee->nic : '') }}" required readonly>
                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id:''}}" data-title="SMGT-vFiles" data-group="a" href="{{isset($employee->virtualfile->nicf) ? asset('/vfiles/' ).'/'.$employee->virtualfile->nicf : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->nicf) ? asset('/vfiles/' ).'/'.$employee->virtualfile->nicf : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button type="button" class="imgedit" id="nicf" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i></button>@endcan
                        
                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id:''}}" data-title="SMGT-vFiles" data-group="a" href="{{isset($employee->virtualfile->nicb) ? asset('/vfiles/' ).'/'.$employee->virtualfile->nicb : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->nicb) ? asset('/vfiles/' ).'/'.$employee->virtualfile->nicb : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button class="imgedit" type="button" id="nicb" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i></button>@endcan
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->nic != $employee->nic) <span class="dummy-value">{{$employee->empdummy->nic}} </span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Title</label>
                    </div>
                    <div class="form-col-9">
                        <select name="title" 
                            id="title" 
                            class="form-control form-control-sm" 
                            required
                            disabled>
                        <option value="">--Select Title--</option>
                        <option value="Rev"
                            {{ old('title', $employee->title ?? '') == 'Rev' ? 'selected' : '' }}>
                            Rev
                        </option>
                        <option value="Mr"
                            {{ old('title', $employee->title ?? '') == 'Mr' ? 'selected' : '' }}>
                            Mr
                        </option>
                        <option value="Mrs"
                            {{ old('title', $employee->title ?? '') == 'Mrs' ? 'selected' : '' }}>
                            Mrs
                        </option>
                        <option value="Miss"
                            {{ old('title', $employee->title ?? '') == 'Miss' ? 'selected' : '' }}>
                            Miss
                        </option>
                    </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->title != $employee->title) <span class="dummy-value">{{$employee->empdummy->title}} <input type="hidden" name="dummy_title" value="{{$employee->empdummy->title}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="name_with_initial_e" class="control-label">Name with Initial (Eng)</label>
                    </div>
                    <div class="form-col-9">
                        <input type="text" class="form-control form-control-sm" name="name_with_initial_e" id="name_with_initial_e" value="{{ old('name_with_initial_e', isset($employee) ? $employee->name_with_initial_e : '') }}" required readonly>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->name_with_initial_e != $employee->name_with_initial_e) <span class="dummy-value">{{$employee->empdummy->name_with_initial_e}} <input type="hidden" name="dummy_name_with_initial_e" value="{{$employee->empdummy->name_with_initial_e}}"/></span>@endif @endif
                </div>  
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="name_denoted_by_initial_e" class="control-label">Name Denoted by Initial (Eng)</label>
                    </div>
                    <div class="form-col-9">
                        <input type="text" class="form-control form-control-sm" name="name_denoted_by_initial_e" id="name_denoted_by_initial_e" value="{{ old('name_denoted_by_initial_e', isset($employee) ? $employee->name_denoted_by_initial_e : '') }}" required readonly>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->name_denoted_by_initial_e != $employee->name_denoted_by_initial_e) <span class="dummy-value">{{$employee->empdummy->name_denoted_by_initial_e}} <input type="hidden" name="dummy_name_denoted_by_initial_e" value="{{$employee->empdummy->name_denoted_by_initial_e}}"/></span>@endif @endif
                </div>  
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="name_with_initial_t" class="control-label">Name with Initial (Tam)</label>
                    </div>
                    <div class="form-col-9">
                        <input type="text" class="form-control form-control-sm" name="name_with_initial_t" id="name_with_initial_t" value="{{ old('name_with_initial_t', isset($employee) ? $employee->name_with_initial_t : '') }}" required readonly>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->name_with_initial_t != $employee->name_with_initial_t) <span class="dummy-value">{{$employee->empdummy->name_with_initial_t}} <input type="hidden" name="dummy_name_with_initial_t" value="{{$employee->empdummy->name_with_initial_t}}"/></span>@endif @endif
                </div>  
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="name_denoted_by_initial_t" class="control-label">Name Denoted by Initial (Tam)</label>
                    </div>
                    <div class="form-col-9">
                        <input type="text" class="form-control form-control-sm" name="name_denoted_by_initial_t" id="name_denoted_by_initial_t" value="{{ old('name_denoted_by_initial_t', isset($employee) ? $employee->name_denoted_by_initial_t : '') }}" required readonly>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->name_denoted_by_initial_t != $employee->name_denoted_by_initial_t) <span class="dummy-value">{{$employee->empdummy->name_denoted_by_initial_t}} <input type="hidden" name="dummy_name_denoted_by_initial_t" value="{{$employee->empdummy->name_denoted_by_initial_t}}"/></span>@endif @endif
                </div> 
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Date of birth</label>
                    </div>
                    <div class="form-col-9">
                        <input type="date" class="form-control form-control-sm float" name="dob" id="dob" value="{{ old('dob', isset($employee) ? $employee->dob : '') }}" readonly>
                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id : ''}}" data-title="SMGT-Scanned Documents" data-group="a" href="{{isset($employee->virtualfile->birthcert) ? asset('/vfiles/' ).'/'.$employee->virtualfile->birthcert : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->birthcert) ? asset('/vfiles/' ).'/'.$employee->virtualfile->birthcert : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button class="imgedit" type="button" id="birthcert" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i></button>@endcan
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->dob != $employee->dob) <span class="dummy-value">{{$employee->empdummy->dob}} <input type="hidden" name="dummy_dob" value="{{$employee->empdummy->dob}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Gender</label>
                    </div>
                    <div class="form-col-9">
                        <select name="gender" 
                            id="gender" 
                            class="form-control form-control-sm" 
                            required
                            disabled>
                        <option value="">--Select Gender--</option>
                        <option value="Male"
                            {{ old('gender', $employee->gender ?? '') == 'Male' ? 'selected' : '' }}>
                            Male
                        </option>
                        <option value="Female"
                            {{ old('gender', $employee->gender ?? '') == 'Female' ? 'selected' : '' }}>
                            Female
                        </option>
                    </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->gender != $employee->gender) <span class="dummy-value">{{$employee->empdummy->gender}} <input type="hidden" name="dummy_gender" value="{{$employee->empdummy->gender}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Ethnicity</label>
                    </div>
                    <div class="form-col-9">
                        <select name="ethinicity" 
                            id="ethinicity" 
                            class="form-control form-control-sm" 
                            disabled>
                        <option value="">--Select Ethnicity--</option>
                        <option value="Tamil"
                            {{ old('ethinicity', $employee->ethinicity ?? '') == 'Tamil' ? 'selected' : '' }}>
                            Tamil
                        </option>
                        <option value="Muslim"
                            {{ old('ethinicity', $employee->ethinicity ?? '') == 'Muslim' ? 'selected' : '' }}>
                            Muslim
                        </option>
                        <option value="Sinhala"
                            {{ old('ethinicity', $employee->ethinicity ?? '') == 'Sinhala' ? 'selected' : '' }}>
                            Sinhala
                        </option>
                        <option value="Burger"
                            {{ old('ethinicity', $employee->ethinicity ?? '') == 'Burger' ? 'selected' : '' }}>
                            Burger
                        </option>
                        <option value="Others"
                            {{ old('ethinicity', $employee->ethinicity ?? '') == 'Others' ? 'selected' : '' }}>
                            Others
                        </option>
                    </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->ethinicity != $employee->ethinicity) <span class="dummy-value">{{$employee->empdummy->ethinicity}} <input type="hidden" name="dummy_ethinicity" value="{{$employee->empdummy->ethinicity}}"/></span>@endif @endif
                </div>
                 <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Religion</label>
                    </div>
                    <div class="form-col-9">    
                        <select name="religion" id="religion" class="form-control form-control-sm" disabled>
                            <option value="">--Select Religion--</option>
                            <option value="Hindu"
                                {{ old('religion', $employee->religion ?? '') == 'Hindu' ? 'selected' : '' }}>
                                Hindu
                            </option>
                            <option value="Islam"
                                {{ old('religion', $employee->religion ?? '') == 'Islam' ? 'selected' : '' }}>
                                Islam
                            </option>
                            <option value="RC"
                                {{ old('religion', $employee->religion ?? '') == 'RC' ? 'selected' : '' }}>
                                RC
                            </option>
                            <option value="NRC"
                                {{ old('religion', $employee->religion ?? '') == 'NRC' ? 'selected' : '' }}>
                                NRC
                            </option>
                            <option value="Buddhist"
                                {{ old('religion', $employee->religion ?? '') == 'Buddhist' ? 'selected' : '' }}>
                                Buddhist
                            </option>
                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->religion != $employee->religion) <span class="dummy-value">{{$employee->empdummy->religion}} <input type="hidden" name="dummy_religion" value="{{$employee->empdummy->religion}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Civil Status</label>
                    </div>
                    <div class="form-col-9">
                        <select name="civilstatus" id="civilstatus" class="form-control form-control-sm" disabled>
                        <option value="">--Select Civil Status--</option>
                        <option value="Married"
                            {{ old('civilstatus', $employee->civilstatus ?? '') == 'Married' ? 'selected' : '' }}>
                            Married
                        </option>
                        <option value="Single"
                            {{ old('civilstatus', $employee->civilstatus ?? '') == 'Single' ? 'selected' : '' }}>
                            Single
                        </option>
                        <option value="Widowed"
                            {{ old('civilstatus', $employee->civilstatus ?? '') == 'Widowed' ? 'selected' : '' }}>
                            Widowed
                        </option>
                        <option value="Not Specified"
                            {{ old('civilstatus', $employee->civilstatus ?? '') == 'Not Specified' ? 'selected' : '' }}>
                            Not Specified
                        </option>
                    </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->civilstatus != $employee->civilstatus) <span class="dummy-value">{{$employee->empdummy->civilstatus}} <input type="hidden" name="dummy_civilstatus" value="{{$employee->empdummy->civilstatus}}"/></span>@endif @endif
                </div>
            </div>   
        </div>
        <!-- Fersonal info end & Geographical Information start-->
        <div class="card geoinfo" id="geoinfo">
            <div class="card-header">
                <b> <i class="fas fa-map"></i><span style="font-size: 13px"> Geographical Information</span> </b>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Permanant Address</label>
                    </div>
                    <div class="form-col-9">  
                        <input type="text" class="form-control form-control-sm" name="peraddress" id="peraddress" value="{{ old('peraddress', isset($employee) ? $employee->peraddress : '') }}" readonly>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->peraddress != $employee->peraddress) <span class="dummy-value">{{$employee->empdummy->peraddress}} <input type="hidden" name="dummy_peraddress" value="{{$employee->empdummy->peraddress}}"/></span>@endif @endif
                </div> 
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Temprory Address</label>
                    </div>
                    <div class="form-col-9">
                        <input type="text" class="form-control form-control-sm" name="tmpaddress" id="tmpaddress" value="{{ old('tmpaddress', isset($employee) ? $employee->tmpaddress : '') }}" readonly>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->tmpaddress != $employee->tmpaddress) <span class="dummy-value">{{$employee->empdummy->tmpaddress}} <input type="hidden" name="dummy_tmpaddress" value="{{$employee->empdummy->tmpaddress}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Residential DS</label>
                    </div>   
                    <div class="form-col-9"> 
                        <select name="dsdivision_id" 
                                id="dsdivision_id" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select DS Division--</option>

                            @foreach ($ds as $division)
                                <option value="{{ $division->id }}"
                                    {{ old('dsdivision_id', $employee->dsdivision_id ?? '') == $division->id ? 'selected' : '' }}>
                                    {{ $division->ds }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->dsdivision_id != $employee->dsdivision_id) <span class="dummy-value">{{$employee->empdummy->dsdivision->ds}} <input type="hidden" name="dummy_dsdivision_id" value="{{$employee->empdummy->dsdivision_id}}"/></span>@endif @endif
                </div> 
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Residential GN</label>
                    </div>
                    <div class="form-col-9">
                        <select name="gndivision_id" 
                                id="gndivision_id" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">-- Select GN Division --</option>

                            @foreach ($gn as $division)
                                <option value="{{ $division->id }}"
                                    {{ old('gndivision_id', $employee->gndivision_id ?? '') == $division->id ? 'selected' : '' }}>
                                    {{ $division->gn }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->gndivision_id != $employee->gndivision_id) <span class="dummy-value">{{$employee->empdummy->gndivision->gn}} <input type="hidden" name="dummy_gndivision_id" value="{{$employee->empdummy->gndivision_id}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Residential Zone</label>
                    </div>
                    <div class="form-col-9">    
                        <select name="zone_id" 
                                id="zone_id" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select Education Zone--</option>

                            @foreach ($zones as $zone)
                                <option value="{{ $zone->id }}"
                                    {{ old('zone_id', $employee->zone_id ?? '') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->zone }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->zone_id != $employee->zone_id) <span class="dummy-value">{{$employee->empdummy->zone->zone}} <input type="hidden" name="dummy_zone_id" value="{{$employee->empdummy->zone_id}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Transportaion Mode</label>
                    </div>
                    <div class="form-col-9">
                        <select name="transmode_id" 
                                id="transmode_id" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select Transportation Mode--</option>

                            @foreach ($transmodes as $transmode)
                                <option value="{{ $transmode->id }}"
                                    {{ old('transmode_id', $employee->transmode_id ?? '') == $transmode->id ? 'selected' : '' }}>
                                    {{ $transmode->tranmode }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->transmode_id != $employee->transmode_id) <span class="dummy-value">{{$employee->empdummy->transmode->tranmode}} <input type="hidden" name="dummy_transmode_id" value="{{$employee->empdummy->transmode_id}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Distance to Resident</label>
                    </div>
                    <div class="form-col-9">
                        <input type="text" class="form-control form-control-sm" name="distores" id="distores" value="{{ old('distores', isset($employee) ? $employee->distores : '') }}" readonly>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->distores != $employee->distores) <span class="dummy-value">{{$employee->empdummy->distores}} <input type="hidden" name="dummy_distores" value="{{$employee->empdummy->distores}}"/></span>@endif @endif
                </div>
                @php
                    function fullMask($val){
                        if (!$val) return '';
                        return str_repeat('*', mb_strlen($val));
                    }

                    $mobile = old('mobile', isset($employee) ? $employee->mobile : '');
                    $whatsapp = old('whatsapp', isset($employee) ? $employee->whatsapp : '');
                    $fixed = old('fixedphone', isset($employee) ? $employee->fixedphone : '');
                @endphp

                <div class="form-group">
                    <div class="form-col-3"><label class="control-label">Mobile</label></div>
                    <div class="form-col-9" style="position:relative;">
                        <input type="text" class="form-control form-control-sm"
                            id="mobile" name="mobile"
                            value="{{ fullMask($mobile) }}" readonly
                            data-original="{{ e($mobile) }}">
                        <i class="fas fa-eye toggle-visibility" data-target="mobile"
                        style="cursor:pointer; position:absolute; right:10px; top:8px;"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-col-3"><label class="control-label">Contact number in case of emergency</label></div>
                    <div class="form-col-9"
                     style="position:relative;">
                        <input type="text" class="form-control form-control-sm"
                            id="fixedphone" name="fixedphone"
                            value="{{ fullMask($fixed) }}" readonly
                            data-original="{{ e($fixed) }}">
                        <i class="fas fa-eye toggle-visibility" data-target="fixedphone"
                        style="cursor:pointer; position:absolute; right:10px; top:8px;"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-col-3"><label class="control-label">Whatsapp</label></div>
                    <div class="form-col-9" style="position:relative;">
                        <input type="text" class="form-control form-control-sm"
                            id="whatsapp" name="whatsapp"
                            value="{{ fullMask($whatsapp) }}" readonly
                            data-original="{{ e($whatsapp) }}">
                        <i class="fas fa-eye toggle-visibility" data-target="whatsapp"
                        style="cursor:pointer; position:absolute; right:10px; top:8px;"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">eMail ID</label>
                    </div>  
                    <div class="form-col-3">  
                        <input type="text" class="form-control form-control-sm" name="email" id="email" value="{{ old('email', isset($employee) ? $employee->email : '') }}" readonly>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->email != $employee->email) <span class="dummy-value">{{$employee->empdummy->email}} <input type="hidden" name="dummy_email" value="{{$employee->empdummy->email}}"/></span>@endif @endif
                </div>
            </div>
        </div>
        <!-- Service Information start-->
        <div class="card serviceinfo" id="serviceinfo">
            <div class="card-header">
                <b> <i class="fas fa-map"></i><span style="font-size: 13px"> Service Information</span> </b>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="form-col-4">
                        <label for="" class="control-label">Present Service</label>
                    </div>
                    <div class="form-col-8">
                        <select name="empservice_id" 
                            id="empservice_id" 
                            class="form-control form-control-sm float" 
                            disabled>

                            <option value="">--Select Service--</option>

                            @foreach ($services as $service)
                                <option value="{{ $service->id }}"
                                    {{ old('empservice_id', $employee->empservice_id ?? '') == $service->id ? 'selected' : '' }}>
                                    {{ $service->service }}
                                </option>
                            @endforeach
                        </select>
                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id : ''}}" data-title="SMGT-Scanned Documents" data-group="a" href="{{isset($employee->virtualfile->firstappltr) ? asset('/vfiles/' ).'/'.$employee->virtualfile->firstappltr : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->firstappltr) ? asset('/vfiles/' ).'/'.$employee->virtualfile->firstappltr : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button class="imgedit" type="button" id="firstappltr" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i>@endcan</button>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->empservice_id != $employee->empservice_id) <span class="dummy-value">{{$employee->empdummy->empservice->service}} <input type="hidden" name="dummy_empservice_id" value="{{$employee->empdummy->empservice_id}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-4">
                        <label for="" class="control-label">Grade/Class</label>
                    </div>
                    <div class="form-col-8">    
                        <select name="grade" 
                                id="grade" 
                                class="form-control form-control-sm float" 
                                disabled>

                            <option value="">--Select Grade--</option>

                            @php
                                $selectedGrade = old('grade', $employee->grade ?? '');
                            @endphp

                            <option value="3-II" {{ $selectedGrade == '3-II' ? 'selected' : '' }}>3-II</option>
                            <option value="3-IA" {{ $selectedGrade == '3-IA' ? 'selected' : '' }}>3-IA</option>
                            <option value="3-IB" {{ $selectedGrade == '3-IB' ? 'selected' : '' }}>3-IB</option>
                            <option value="3-IC" {{ $selectedGrade == '3-IC' ? 'selected' : '' }}>3-IC</option>
                            <option value="III"  {{ $selectedGrade == 'III' ? 'selected' : '' }}>III</option>
                            <option value="2-II" {{ $selectedGrade == '2-II' ? 'selected' : '' }}>2-II</option>
                            <option value="2-I"  {{ $selectedGrade == '2-I' ? 'selected' : '' }}>2-I</option>
                            <option value="II"   {{ $selectedGrade == 'II' ? 'selected' : '' }}>II</option>
                            <option value="I"    {{ $selectedGrade == 'I' ? 'selected' : '' }}>I</option>

                        </select>

                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id : ''}}" data-title="SMGT-Scanned Documents" data-group="a" href="{{isset($employee->virtualfile->promoltr) ? asset('/vfiles/' ).'/'.$employee->virtualfile->promoltr : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->promoltr) ? asset('/vfiles/' ).'/'.$employee->virtualfile->promoltr : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button class="imgedit" type="button" id="promoltr" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i>@endcan</button>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->grade != $employee->grade) <span class="dummy-value">{{$employee->empdummy->grade}} <input type="hidden" name="dummy_grade" value="{{$employee->empdummy->grade}}"/></span>@endif @endif
                </div>
               
                <div class="form-group">
                    <div class="form-col-4">
                        <label for="" class="control-label">Appointment date in present service</label>
                    </div>
                    <div class="form-col-8">                
                        <input type="date" class="form-control form-control-sm float" name="dtyasmcser" id="dtyasmcser" value="{{ old('dtyasmcser', isset($employee) ? $employee->dtyasmcser : '') }}" readonly>
                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id : ''}}" data-title="SMGT-Scanned Documents" data-group="a" href="{{isset($employee->virtualfile->appltrcserv) ? asset('/vfiles/' ).'/'.$employee->virtualfile->appltrcserv : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->appltrcserv) ? asset('/vfiles/' ).'/'.$employee->virtualfile->appltrcserv : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button class="imgedit" type="button" id="appltrcserv" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i>@endcan</button>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->dtyasmcser != $employee->dtyasmcser) <span class="dummy-value">{{$employee->empdummy->dtyasmcser}} <input type="hidden" name="dummy_dtyasmcser" value="{{$employee->empdummy->dtyasmcser}}"/></span>@endif @endif
                </div>

                 <div class="form-group">
                    <div class="form-col-4">
                        <label for="" class="control-label">Duty assumption date in present service</label>
                    </div>
                    <div class="form-col-8">
                        <input type="date" class="form-control form-control-sm float" name="dtyasmfapp" id="dtyasmfapp" value="{{ old('dtyasmfapp', isset($employee) ? $employee->dtyasmfapp : '') }}" readonly>
                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id : ''}}" data-title="SMGT-Scanned Documents" data-group="a" href="{{isset($employee->virtualfile->firstdtyassm) ? asset('/vfiles/' ).'/'.$employee->virtualfile->firstdtyassm : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->firstdtyassm) ? asset('/vfiles/' ).'/'.$employee->virtualfile->firstdtyassm : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button class="imgedit" type="button" id="firstdtyassm" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i>@endcan</button>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->dtyasmfapp != $employee->dtyasmfapp) <span class="dummy-value">{{$employee->empdummy->dtyasmfapp}} <input type="hidden" name="dummy_dtyasmfapp" value="{{$employee->empdummy->dtyasmfapp}}"/></span>@endif @endif
                </div>

                <div class="form-group">
                    <div class="form-col-4">
                        <label for="" class="control-label">Permanent working station (as per paysheet)</label>
                    </div>
                    <div class="form-col-8">    
                        @php
                            $selectedInstitute = old('institute_id', $employee->institute_id ?? '');
                        @endphp

                        <select name="institute_id" 
                                id="institute_id" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select Institution--</option>

                            @foreach ($institutes as $institute)
                                <option value="{{ $institute->id }}"
                                    {{ $selectedInstitute == $institute->id ? 'selected' : '' }}>
                                    {{ $institute->institute }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->institute_id != $employee->institute_id) <span class="dummy-value">{{$employee->empdummy->institute->institute}} <input type="hidden" name="dummy_institute_id" value="{{$employee->empdummy->institute_id}}"/></span>@endif @endif
                </div>

                <div class="form-group">
                    <div class="form-col-4">
                        <label for="" class="control-label">Temprory working station (Attachment/Temprory)</label>
                    </div>
                    <div class="form-col-8">    
                        @php
                            $selectedStation = old('current_working_station', $employee->current_working_station ?? '');
                        @endphp
                        <select name="current_working_station" 
                                id="current_working_station" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select Institution--</option>

                            @foreach ($institutes as $institute)
                                <option value="{{ $institute->id }}"
                                    {{ $selectedStation == $institute->id ? 'selected' : '' }}>
                                    {{ $institute->institute }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->current_working_station != $employee->current_working_station) <span class="dummy-value">{{$employee->empdummy->workingStation->institute}} <input type="text" name="dummy_current_working_station" value="{{$employee->empdummy->current_working_station}}"/></span>@endif @endif
                </div>
                 <div class="form-group">
                    <div class="form-col-4">
                        <label for="" class="control-label">Reason of temprory attachment/Releases</label>
                    </div>
                    <div class="form-col-8">    
                        @php
                            $selectedReason = old('reason_attachment', $employee->reason_attachment ?? '');
                        @endphp
                        <select name="reason_attachment" 
                                id="reason_attachment" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select reason--</option>

                            <option value="maternity"     {{ $selectedReason == 'maternity' ? 'selected' : '' }}>Maternity</option>
                            <option value="accident"      {{ $selectedReason == 'accident' ? 'selected' : '' }}>Accident</option>
                            <option value="sick"          {{ $selectedReason == 'sick' ? 'selected' : '' }}>Sick</option>
                            <option value="training"      {{ $selectedReason == 'training' ? 'selected' : '' }}>Training</option>
                            <option value="abroad"        {{ $selectedReason == 'abroad' ? 'selected' : '' }}>Abroad</option>
                            <option value="other_release" {{ $selectedReason == 'other_release' ? 'selected' : '' }}>Other Release</option>
                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->reason_attachment != $employee->reason_attachment) <span class="dummy-value">{{$employee->empdummy->reason_attachment}} <input type="hidden" name="dummy_reason_attachment" value="{{$employee->empdummy->reason_attachment}}"/></span>@endif @endif
                </div>

                 <div class="form-group">
                    <div class="form-col-4">
                        <label for="" class="control-label">End of temprory attachment/release</label>
                    </div>
                    <div class="form-col-8">
                        <input type="date" class="form-control form-control-sm float" name="date_of_re_joining" id="date_of_re_joining" value="{{ old('date_of_re_joining', isset($employee) ? $employee->date_of_re_joining : '') }}" readonly>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->date_of_re_joining != $employee->date_of_re_joining) <span class="dummy-value">{{$employee->empdummy->date_of_re_joining}} <input type="hidden" name="dummy_date_of_re_joining" value="{{$employee->empdummy->date_of_re_joining }}"/></span>@endif @endif
                </div>

                <div class="form-group">
                    <div class="form-col-4">
                        <label for="" class="control-label">Duty assumption date in present permanent station</label>
                    </div>
                    <div class="form-col-8">
                        <input type="date" class="form-control form-control-sm float" name="dtyasmprins" id="dtyasmprins" value="{{ old('dtyasmprins', isset($employee) ? $employee->dtyasmprins : '') }}" readonly>
                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id : ''}}" data-title="SMGT-Scanned Documents" data-group="a" href="{{isset($employee->virtualfile->dtysssmprinst) ? asset('/vfiles/' ).'/'.$employee->virtualfile->dtysssmprinst : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->dtysssmprinst) ? asset('/vfiles/' ).'/'.$employee->virtualfile->dtysssmprinst : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button class="imgedit" type="button" id="dtysssmprinst" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i>@endcan</button>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->dtyasmprins != $employee->dtyasmprins) <span class="dummy-value">{{$employee->empdummy->dtyasmprins}} <input type="hidden" name="dummy_dtyasmprins" value="{{$employee->empdummy->dtyasmprins}}"/></span>@endif @endif
                </div>

                <div class="form-group">
                    <div class="form-col-4">
                        <label for="" class="control-label">Designation in permanent working station</label>
                    </div>
                    <div class="form-col-8">
                        @php
                            $selectedDesignation = old('designation_id', $employee->designation_id ?? '');
                        @endphp

                        <select name="designation_id" 
                                id="designation_id" 
                                class="form-control form-control-sm float" 
                                disabled>

                            <option value="">--Select Designation--</option>

                            @foreach ($designations as $designation)
                                <option value="{{ $designation->id }}"
                                    {{ $selectedDesignation == $designation->id ? 'selected' : '' }}>
                                    {{ $designation->designation }}
                                </option>
                            @endforeach

                        </select>
                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id : ''}}" data-title="SMGT-Scanned Documents" data-group="a" href="{{isset($employee->virtualfile->designationltr) ? asset('/vfiles/' ).'/'.$employee->virtualfile->designationltr : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->designationltr) ? asset('/vfiles/' ).'/'.$employee->virtualfile->designationltr : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button class="imgedit" type="button" id="designationltr" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i>@endcan</button>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->designation_id != $employee->designation_id) <span class="dummy-value">{{$employee->empdummy->designation->designation}} <input type="hidden" name="dummy_designation_id" value="{{$employee->empdummy->designation_id}}"/></span>@endif @endif
                </div>
            </div>   
            <!--...............Service history.................-->
            <table>
                <thead>
                    <tr>
                        <th colspan="4" style="background-color:#FAF0E6; font-weight:bold; text-align: center;"><label>Service History(Source:NEMIS)</label></th>
                    </tr>
                    <tr>
                        <th>Zone</th>
                        <th>Institute</th>
                        <th>Date From</th>
                        <th>Date To</th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($employee))
                    @if($employee->servicehistory->count() > 0)
                        @foreach($employee->servicehistory as $emp)
                        <tr>
                            <td>{{$emp->zone}}</td>
                            <td>{{$emp->institute}}</td>
                            <td>{{$emp->date_from}}</td>
                            <td>{{$emp->date_to}}</td>
                        </tr>
                        @endforeach
                    @endif
                @endif
                </tbody>
            </table>
        </div>
        
        <!-- Qualification Information start-->
        <div class="card qualifinfo" id="qualifinfo">
            <div class="card-header">
                <b> <i class="fas fa-map"></i><span style="font-size: 13px"> Education/Professional qualifications</span> </b>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Higher education qualification</label>
                    </div>
                    <div class="form-col-9">    
                        @php
                            $selectedQualification = old('highqualification_id', $employee->highqualification_id ?? '');
                        @endphp

                        <select name="highqualification_id" 
                                id="highqualification_id" 
                                class="form-control form-control-sm float" 
                                disabled>

                            <option value="">--Select Highest Qualification--</option>

                            @foreach ($highqualifs as $highqualif)
                                <option value="{{ $highqualif->id }}"
                                    {{ $selectedQualification == $highqualif->id ? 'selected' : '' }}>
                                    {{ $highqualif->qualif }}
                                </option>
                            @endforeach

                        </select>
                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id : ''}}" data-title="SMGT-Scanned Documents" data-group="a" href="{{isset($employee->virtualfile->hiqualif) ? asset('/vfiles/' ).'/'.$employee->virtualfile->hiqualif : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->hiqualif) ? asset('/vfiles/' ).'/'.$employee->virtualfile->hiqualif : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button class="imgedit" type="button" id="hiqualif" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i>@endcan</button>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy))
                        @if($employee->empdummy->highqualification_id != $employee->highqualification_id)
                            @if(!empty($employee->empdummy->highqualif))
                                <span class="dummy-value">
                                    {{ $employee->empdummy->highqualif->qualif }}
                                    <input type="hidden" name="dummy_highqualification_id"
                                        value="{{ $employee->empdummy->highqualification_id }}"/>
                                </span>
                            @endif
                        @endif
                    @endif                
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">A/L stream</label>
                    </div>
                    <div class="form-col-9">    
                        @php
                            $selectedStream = old('al_stream', $employee->al_stream ?? '');
                        @endphp

                        <select name="al_stream" 
                                id="al_stream" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select A/L stream--</option>

                            <option value="bio science"      {{ $selectedStream == 'bio science' ? 'selected' : '' }}>Bio Science</option>
                            <option value="physical science" {{ $selectedStream == 'physical science' ? 'selected' : '' }}>Physical Science</option>
                            <option value="commerce"         {{ $selectedStream == 'commerce' ? 'selected' : '' }}>Commerce</option>
                            <option value="e_tech"           {{ $selectedStream == 'e_tech' ? 'selected' : '' }}>E-Technology</option>
                            <option value="b_tech"           {{ $selectedStream == 'b_tech' ? 'selected' : '' }}>B-Technology</option>
                            <option value="arts"             {{ $selectedStream == 'arts' ? 'selected' : '' }}>Arts</option>
                            <option value="others"           {{ $selectedStream == 'others' ? 'selected' : '' }}>Other Stream</option>
                            <option value="not sat for A/L"  {{ $selectedStream == 'not sat for A/L' ? 'selected' : '' }}>Not Sat for A/L</option>
                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->al_stream != $employee->al_stream) <span class="dummy-value">{{$employee->empdummy->al_stream}} <input type="hidden" name="dummy_al_stream" value="{{$employee->empdummy->al_stream}}"/></span>@endif @endif
                </div>
                <div class="form-group">                
                    <div class="form-col-3">
                        <label for="" class="control-label">Name of basic degree</label>
                    </div>
                    <div class="form-col-9">
                        @php
                            $selectedDegree = old('degree_id', $employee->degree_id ?? '');
                        @endphp

                        <select name="degree_id" 
                                id="degree_id" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select Degree--</option>

                            @foreach ($degrees as $degree)
                                <option value="{{ $degree->id }}"
                                    {{ $selectedDegree == $degree->id ? 'selected' : '' }}>
                                    {{ $degree->degree }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->degree_id != $employee->degree_id) <span class="dummy-value">{{$employee->empdummy->degree->degree}} <input type="hidden" name="dummy_degree_id" value="{{$employee->empdummy->degree_id}}"/></span>@endif @endif
                </div>
                <br>
                <!--............................................................Degree Subject....................................................-->
                <label class="card-title">Degree Subjects</label>
                <table id="degreesubjecttable">
                    <thead>
                        <tr>
                            <th style="width:90%;">Subject Name</th>
                            <th style="width:10%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($employee->id))
                            @include('human_resource.partials.emp_degree_subjects')
                        @endif
                    </tbody>
                </table>

                <!--.......................................Dynamic Degree Subject Area Start.............................................. -->
                <div class="button-container float-left" id="adddegreebtn" style="display:none">
                    <button type="button" class="btn btn-success btn-sm addDegreeSubject"><i class="fa fa-plus"></i> Add degree subject</button>
                </div>
                <br>
                <!--.......................................Dynamic Degree Subjects Area End.............................................. -->
             
                
                <br>
                <!--............................................................Other Qualifications....................................................-->
                <label class="card-title">Professional Qualifications</label>
                <table id="coursetable">
                    <thead>
                        <tr>
                            <th style="width:35%;">Course Name</th>
                            <th style="width:35%;">Institution</th>
                            <th style="width:20%;">Duration (Months)</th>
                            <th style="width:10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($employee->id))
                            @include('human_resource.partials.qualifications')
                        @endif
                    </tbody>
                </table>

                <!--.......................................Dynamic Qualification Area Start.............................................. -->
                <div class="button-container float-left" id="addqualifbtn" style="display:none">
                    <button type="button" class="btn btn-success btn-sm addcourse"><i class="fa fa-plus"></i> Add qualification</button>
                </div>
                <!--.......................................Dynamic Qualification Area Area End.............................................. -->
                
            </div>
        </div>
        <!-- Qualification Information end Subject Information Start-->
        <div class="card subjectinfo" id="subjectinfo">
            <div class="card-header">
                <b> <i class="fas fa-map"></i><span style="font-size: 13px"> Subject Informations</span> </b>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Appointment Subject</label>
                    </div>
                    <div class="form-col-9">     
                        <input type="text" class="form-control form-control-sm float" name="appsubject" id="appsubject" value="{{ old('appsubject', isset($employee) ? $employee->appsubject : '') }}" readonly>
                        <div class="image-set"><a data-gallery="photoviewer" id="{{isset($employee->virtualfile->id) ? $employee->virtualfile->id : ''}}" data-title="SMGT-Scanned Documents" data-group="a" href="{{isset($employee->virtualfile->appsub) ? asset('/vfiles/' ).'/'.$employee->virtualfile->appsub : asset('/vfiles/No_Image_Available.jpg') }}">
                        <img src="{{isset($employee->virtualfile->appsub) ? asset('/vfiles/' ).'/'.$employee->virtualfile->appsub : asset('/vfiles/No_Image_Available.jpg') }}" class="img-fluid img-thumbnail" alt="">
                        </a></div>@can('employee-edit')<button class="imgedit" type="button" id="appsub" data-toggle="modal" data-target="#imageupload"><i class="fas fa-edit"></i>@endcan</button>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->appsubject != $employee->appsubject) <span class="dummy-value">{{$employee->empdummy->appsubject}} <input type="hidden" name="dummy_appsubject" value="{{$employee->empdummy->appsubject}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Appointment Category</label>
                    </div>
                    <div class="form-col-9">     
                        @php
                            $selectedAppCategory = old('appcategory_id', $employee->appcategory_id ?? '');
                        @endphp

                        <select name="appcategory_id" 
                                id="appcategory_id" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select Appointment Category--</option>

                            @foreach ($appcats as $appcat)
                                <option value="{{ $appcat->id }}"
                                    {{ $selectedAppCategory == $appcat->id ? 'selected' : '' }}>
                                    {{ $appcat->appcat }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->appcategory_id != $employee->appcategory_id) <span class="dummy-value">{{$employee->empdummy->appcategory->appcat}} <input type="hidden" name="dummy_appcategory_id" value="{{$employee->empdummy->appcategory_id}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Cadre Subject</label>
                    </div>
                    <div class="form-col-9">
                        @php
                            $selectedCadre = old('cadresubject_id', $employee->cadresubject_id ?? '');
                        @endphp

                        <select name="cadresubject_id" 
                                id="cadresubject_id" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select Cadre Subject--</option>

                            @foreach ($cadresubs as $cadresub)
                                <option value="{{ $cadresub->id }}"
                                    {{ $selectedCadre == $cadresub->id ? 'selected' : '' }}>
                                    {{ $cadresub->cadre }}
                                </option>
                            @endforeach

                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->cadresubject_id != $employee->cadresubject_id) <span class="dummy-value">{{$employee->empdummy->cadresubject->cadre}} <input type="hidden" name="dummy_cadresubject_id" value="{{$employee->empdummy->cadresubject_id}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Trained Status</label>
                    </div>
                    <div class="form-col-9">
                        @php
                            $selectedTrained = old('trained', $employee->trained ?? '');
                        @endphp

                        <select name="trained" 
                                id="trained" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select Trained Status--</option>

                            <option value="trained"       {{ $selectedTrained == 'trained' ? 'selected' : '' }}>Trained</option>
                            <option value="untrained"     {{ $selectedTrained == 'untrained' ? 'selected' : '' }}>Un-Trained</option>
                            <option value="undertraining" {{ $selectedTrained == 'undertraining' ? 'selected' : '' }}>Under Training</option>
                        </select>
                    </div>
                </div>
                @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->trained != $employee->trained) <span class="dummy-value">{{$employee->empdummy->trained}} <input type="hidden" name="dummy_trained" value="{{$employee->empdummy->trained}}"/></span>@endif @endif
                <br>
                <label class="card-title">Teaching Subjects</label>
                <table id="subjecttable">
                    <thead>
                        <tr>
                            <th style="width:40%;">Teaching Subject</th>
                            <th style="width:40%;">Periods (Per week)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($employee->id))
                            @include('human_resource.partials.teachsubjects')
                        @endif
                    </tbody>
                </table>

                <!--.......................................Dynamic Teaching Subject Area Start.............................................. -->
                <div class="button-container float-left" id="subjectbtn" style="display:none">
                    <button type="button" class="btn btn-success btn-sm addsubject"><i class="fa fa-plus"></i> Add row</button>
                </div>
                <!--.......................................Dynamic Teaching Subject Area Area End.............................................. -->
            </div>
        </div>
        <!-- Subject Information end status information start-->
        <div class="card statusinfo" id="statusinfo">
            <div class="card-header">
                <b> <i class="fas fa-map"></i><span style="font-size: 13px"> Current Status</span> </b>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Status(Active/In-Active)</label>
                    </div>
                    <div class="form-col-9">     
                        @php
                            $selectedStatus = old('status', $employee->status ?? 'Active');
                        @endphp

                        <select name="status" 
                                id="status" 
                                class="form-control form-control-sm" 
                                disabled>

                            <option value="">--Select Status--</option>

                            <option value="Active"   {{ $selectedStatus == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="TrOut"    {{ $selectedStatus == 'TrOut' ? 'selected' : '' }}>TrOut</option>
                            <option value="Inactive" {{ $selectedStatus == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Pension"  {{ $selectedStatus == 'Pension' ? 'selected' : '' }}>Pension</option>

                        </select>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->status != $employee->status) <span class="dummy-value">{{$employee->empdummy->status}} <input type="hidden" name="dummy_status" value="{{$employee->empdummy->status}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Remarks</label>
                    </div>
                    <div class="form-col-9">     
                        <input type="text" class="form-control form-control-sm" name="remark" id="remark" value="{{ old('remark', isset($employee) ? $employee->remark : '') }}" readonly>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->remark != $employee->remark) <span class="dummy-value">{{$employee->empdummy->remark}} <input type="hidden" name="dummy_remark" value="{{$employee->empdummy->remark}}"/></span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Created on</label>
                    </div>
                    <div class="form-col-9">     
                        <input type="text" class="form-control form-control-sm" name="created_at" id="created_at" value="{{ old('created_at', isset($employee) ? $employee->created_at : '') }}" readonly disabled>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Last Modification Date & Time</label>
                    </div>
                    <div class="form-col-9">
                        <input type="text" class="form-control form-control-sm" name="updated_at" id="updated_at" value="{{ old('updated_at', isset($employee) ? $employee->updated_at : '') }}" readonly disabled>
                    </div>
                    @if(!empty($employee) && !empty($employee->empdummy)) @if($employee->empdummy->updated_at != $employee->updated_at) <span class="dummy-value">{{$employee->empdummy->updated_at}} </span>@endif @endif
                </div>
                <div class="form-group">
                    <div class="form-col-3">
                        <label for="" class="control-label">Designation Category</label>
                    </div>
                    <input type="text" 
                        class="form-control form-control-sm" 
                        id="desigcatg" 
                        name="desigcatg" 
                        value="{{ old('desigcatg', $employee?->designation?->catg ?? '') }}" 
                        required 
                        readonly>
                </div>
            </div>
        </div>
        <div class="card save">
            <div class="card-header">
                <!-- Certify checkbox - place this right above the submitdiv (always visible) -->
                <div class="form-group" id="certifyDiv" style="margin-bottom:8px;">
                    <div style="display:inline-block;vertical-align:middle;">
                        <label class="control-label" for="certifyChk"> </label>
                    </div>
                    <div class="form-col-9" style="display:inline-block;vertical-align:middle;">
                        <input type="checkbox" id="certifyChk" disabled />
                        <label for="certifyChk" style="margin-left:6px; font-weight:600;">I certify that above detailed are correct.</label>
                    </div>
                </div>
            </div>
            <div class="card-body">
                 <div class="form-group" align="center" id="submitdiv" style="display:none">
                    <button type="button" id="saveBtn" style="margin-left:15px;" class="btn btn-warning">
                        {{ isset($employee) ? 'Update' : 'Add' }}
                    </button>
                </div>
            </div>
        </div>
        </div> 
        </fieldset>
    </form>
</div>
</div>

<!-- Modal image upload Form Start -->
<div class="modal fade" id="imageupload" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upload vFiles</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <form action="{{ isset($employee->virtualfile->employee_id) ? route('vfile.update',$employee->id) : route('vfile.store') }}" name="addform" method="post" enctype="multipart/form-data">
          @csrf  
        <div class="modal-body">
                <img class="img-model" id="vphoto">
                <input type="file" id="files" name="file" required>
                <input type="hidden" id="curfield" name="curfield">
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" id="imgupload"  value="{{isset($employee->virtualfile->employee_id) ? 'Update' : 'Add'}}">
            </div>
        </form>
    </div>
  </div>
</div>

@include('human_resource.partials._dummy_update_modal', ['employee' => $employee, 'employeeDummy' => $employeeDummy])

@endsection

@push('styles')
<link href="{{asset('/css/photoviewer.css')}}" rel="stylesheet" />
<style>
.serviceinfo table, .serviceinfo table th, .serviceinfo table td {
  border: 1px solid #DCDCDC;
  border-collapse: collapse;
  font-size: 13px;
}

@media only screen and (min-width: 768px) {
    .dummy-value{
        margin-left: 245px;
        margin-top: 10px;
    }
}
@media only screen and (max-width: 768px) {
    .dummy-value{
        margin-left: 20px;
    }
}
.dummy-value{
    color: red;
    font-weight: bold;
}
.image{
        background:url('{{asset('backend/img/background.jpg')}}');
        height:150px;
        background-position:center;
        background-attachment:cover;
        position: relative;
    }
    .image img{
        position: absolute;
        top:40%;
        left:35%;
        margin-top:30%;
    }
    i{
        font-size: 14px;
        padding-right:8px;
    }
.img-model{
    height:200px;
    width:auto;
}

#photo{
    height: 100%;
    width: 100%;
}

#filepro{
    display: none;
}

#uploadBtn{
    height: 40px;
    width: 100%;
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    background: rgba(0, 0, 0, 0.7);
    color: wheat;
    line-height: 30px;
    font-family: sans-serif;
    font-size: 15px;
    cursor: pointer;
    display: none;
}

@media only screen and (min-width: 600px) {
    .lookup{
        position: fixed !important; 
        width:340px !important;
    }
}

.form-group {
  display: flex;
  flex-flow: row wrap;
  margin: 0 -1rem 1rem -1rem;
}

[class*="form-col"] {
  flex: 0 1 100%;
  padding: 0 1rem;
}

@media (min-width: 576px) {

 .form-col-3 {
  flex: 0 0 25%;
  max-width: 25%;
}

.form-col-9 {
  flex: 0 0 75%;
  max-width: 75%;
}

  .form-col-4 {
  flex: 0 0 40%;
  max-width: 40%;
}

.form-col-8 {
  flex: 0 0 60%;
  max-width: 60%;
}
  
}

.editbtn {
  background-color: transparent;
  border: none;
  color: green !important;
  font-size: 20px !important;
  cursor: pointer;
  float: right;
}
.imgedit {
  background-color: transparent;
  border: none;
  color: green !important;
  font-size: 15px !important;
  cursor: pointer;
  float: left;
}
.image-set{
   float:left !important;
}
.view input {
  border:1;
  background:0 !important;
  outline:none !important;
  border-color: #ffe6e6;
}
.view select {
  border:1;
  background:0 !important;
  outline:none !important;
  appearance: none;
  border-color: #ffe6e6;

}
.control-label{
    font-size:14px !important;
    font-weight: 500;
}
.form-numbering {
    counter-reset: section;
}

/* .form-numbering .form-group {
    counter-increment: section;
} */

.form-numbering label::before {
    counter-increment: section;
    content: counter(section) ". ";
    font-weight: bold;
}
.form-group {
  padding:2px;
}
.b-image {
    float:left;
    width:20% !important;
}
.float {
    float:left;
    width:93% !important;
}
.float-double {
    float:left;
    width:85% !important;
}
.img-thumbnail{
    height: 30px !important;
    width:auto !important;
    border-radius: 0rem !important;
    padding: 0 !important;
}

.photoviewer-modal {
      background-color: transparent;
      border: none;
      border-radius: 0;
      box-shadow: 0 0 6px 2px rgba(0, 0, 0, .3);
    }

    .photoviewer-header .photoviewer-toolbar {
      background-color: rgba(0, 0, 0, .5);
    }

    .photoviewer-stage {
      top: 0;
      right: 0;
      bottom: 0;
      left: 0;
      background-color: rgba(0, 0, 0, .85);
      border: none;
    }

    .photoviewer-footer .photoviewer-toolbar {
      background-color: rgba(0, 0, 0, .5);
      border-top-left-radius: 5px;
      border-top-right-radius: 5px;
    }

    .photoviewer-header,
    .photoviewer-footer {
      border-radius: 0;
      pointer-events: none;
    }

    .photoviewer-title {
      color: #ccc;
    }

    .photoviewer-button {
      color: #ccc;
      pointer-events: auto;
    }

    .photoviewer-header .photoviewer-button:hover,
    .photoviewer-footer .photoviewer-button:hover {
      color: white;
    }
    
    /*Ajax loader*/
    .overlay{
    display: none;
    position: fixed;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    z-index: 999;
    background: rgba(255,255,255,0.8) url("/images/loader.gif") center no-repeat;
    }
    /* Turn off scrollbar when body element has the loading class */
    body.loading{
        overflow: hidden;   
    }
    /* Make spinner image visible when body element has the loading class */
    body.loading .overlay{
        display: block;
    }

</style>
@endpush

@push('scripts')
<script src="{{asset('/js/photoviewer.js')}}"></script>
<script>
// Add remove loading class on body element based on Ajax request status
$(document).on({
    ajaxStart: function(){
        $("body").addClass("loading"); 
    },
    ajaxStop: function(){ 
        $("body").removeClass("loading"); 
    }    
});

// Replace your existing nav-jump handler with this simple robust one
document.addEventListener('DOMContentLoaded', function() {
  const OFFSET = 12; // adjust if you have fixed header
  document.querySelectorAll('.nav-jump').forEach(function(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      const targetSel = this.getAttribute('data-target');
      if (!targetSel) return console.warn('no data-target on', this);
      const target = document.querySelector(targetSel);
      if (!target) return console.warn('target not found', targetSel);

      // If the target is inside a scrollable ancestor (like a modal .modal-body), scroll that container.
      function scrollableAncestor(el) {
        let p = el.parentElement;
        while (p && p !== document.body) {
          const style = getComputedStyle(p);
          const overflowY = style.overflowY;
          if ((overflowY === 'auto' || overflowY === 'scroll') && p.scrollHeight > p.clientHeight) return p;
          p = p.parentElement;
        }
        // fallback to document scrollingElement
        return document.scrollingElement || document.documentElement;
      }

      const container = scrollableAncestor(target);

      if (container === (document.scrollingElement || document.documentElement)) {
        // page-level scroll: use scrollIntoView then adjust for OFFSET
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        // small adjustment after scroll (works cross-browser)
        window.setTimeout(function() {
          window.scrollBy({ top: -OFFSET, left: 0, behavior: 'smooth' });
        }, 60);
      } else {
        // container-level scroll
        // compute target top relative to container
        const containerRect = container.getBoundingClientRect();
        const targetRect = target.getBoundingClientRect();
        const currentScroll = container.scrollTop;
        const relativeTop = (targetRect.top - containerRect.top) + currentScroll - OFFSET;
        // animate with requestAnimationFrame
        const start = container.scrollTop;
        const change = relativeTop - start;
        const duration = 400;
        let startTime = null;
        function animateScroll(time){
          if (!startTime) startTime = time;
          const t = Math.min(1, (time - startTime) / duration);
          // easeInOutQuad
          const eased = t < 0.5 ? 2*t*t : -1 + (4 - 2*t)*t;
          container.scrollTop = Math.round(start + change * eased);
          if (t < 1) requestAnimationFrame(animateScroll);
        }
        requestAnimationFrame(animateScroll);
      }

      // debug log
      console.log('nav-jump clicked ->', targetSel, 'container:', container.tagName);
    });
  });
});


//fetch image in model 
$('.imgedit').click(function () {
    var name = $(this).attr("id");
    $('#curfield').val($(this).attr("id"));
    if(name == 'nicf'){
        var file = {!! json_encode(isset($employee->virtualfile->nicf) ? $employee->virtualfile->nicf : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    } else if(name == 'nicb') {
         file = {!! json_encode(isset($employee->virtualfile->nicb) ? $employee->virtualfile->nicb : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    } else if(name == 'birthcert') {
         file = {!! json_encode(isset($employee->virtualfile->birthcert) ? $employee->virtualfile->birthcert : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    } else if(name == 'firstappltr') {
         file = {!! json_encode(isset($employee->virtualfile->firstappltr) ? $employee->virtualfile->firstappltr : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    } else if(name == 'promoltr') {
         file = {!! json_encode(isset($employee->virtualfile->promoltr) ? $employee->virtualfile->promoltr : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    } else if(name == 'firstdtyassm') {
         file = {!! json_encode(isset($employee->virtualfile->firstdtyassm) ? $employee->virtualfile->firstdtyassm : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    } else if(name == 'appltrcserv') {
         file = {!! json_encode(isset($employee->virtualfile->appltrcserv) ? $employee->virtualfile->appltrcserv : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    } else if(name == 'designationltr') {
         file = {!! json_encode(isset($employee->virtualfile->designationltr) ? $employee->virtualfile->designationltr : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    } else if(name == 'dtysssmprinst') {
         file = {!! json_encode(isset($employee->virtualfile->dtysssmprinst) ? $employee->virtualfile->dtysssmprinst : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    } else if(name == 'hiqualif') {
         file = {!! json_encode(isset($employee->virtualfile->hiqualif) ? $employee->virtualfile->hiqualif : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    } else if(name == 'appsub') {
         file = {!! json_encode(isset($employee->virtualfile->appsub) ? $employee->virtualfile->appsub : 'No_Image_Available.jpg', JSON_HEX_TAG) !!};
    }
    
    var source = '{!! asset("/vfiles/")!!}/' + file;
    $('.img-model').attr('src',source);
});

// Mask the mobile number and whatsapp nuber
document.addEventListener('click', function(e){
    if (!e.target.classList.contains('toggle-visibility')) return;

    var icon = e.target;
    var targetId = icon.getAttribute('data-target');
    var input = document.getElementById(targetId);
    var original = input.getAttribute('data-original') || '';

    if (icon.classList.contains('showing')) {
        // hide again - full mask
        input.value = '*'.repeat(original.length);
        icon.classList.remove('fa-eye-slash', 'showing');
        icon.classList.add('fa-eye');
    } else {
        // show full
        input.value = original;
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash', 'showing');
    }
});



//Employee photo upload
const imgDiv = document.querySelector('.profile-pic-div');
const img = document.querySelector('#photo');
const file = document.querySelector('#filepro');
const uploadBtn = document.querySelector('#uploadBtn');

imgDiv.addEventListener('mouseenter', function(){
    uploadBtn.style.display = "block";
});


imgDiv.addEventListener('mouseleave', function(){
    uploadBtn.style.display = "none";
});


file.addEventListener('change', function(){
    const choosedFile = this.files[0];
    if (choosedFile) {
        const reader = new FileReader(); //FileReader is a predefined function of JS
        reader.addEventListener('load', function(){
            img.setAttribute('src', reader.result);
        });
        reader.readAsDataURL(choosedFile);
    }
    
   var t = document.getElementById("proimgupdate");
      if (t.style.display === "none") {
        t.style.display = "block";
      }
});

$('#files').click(function () {
    const imgs = document.querySelector('#vphoto');
    const files = document.querySelector('#files');
    files.addEventListener('change', function(){
        const choosedFile = this.files[0];
        if (choosedFile) {
            const readers = new FileReader(); //FileReader is a predefined function of JS
            readers.addEventListener('load', function(){
                imgs.setAttribute('src', readers.result);
            });
            readers.readAsDataURL(choosedFile);
        }
    });
});

//Model photo viewer
$('[data-gallery=photoviewer]').click(function (e) {
e.preventDefault();

var items = [],
  options = {
    index: $(this).index(),
  };

  items.push({
    src: $(this).attr('href'),
    title: $(this).attr('data-title')
  });
new PhotoViewer(items, options);
});


//Goto section href functions
$(document).ready(function () {
  
    $('#dsdivision_id').change(function () {
             var id = $(this).val();

             $('#gndivision_id').find('option').not(':first').remove();

             $.ajax({
                url:'/gndetails/'+id,
                type:'get',
                dataType:'json',
                success:function (response) {
                    var len = 0;
                    if (response.data != null) {
                        len = response.data.length;
                    }

                    if (len>0) {
                        for (var i = 0; i<len; i++) {
                             var id = response.data[i].id;
                             var gn = response.data[i].gn;

                             var option = "<option value='"+id+"'>"+gn+"</option>"; 

                             $("#gndivision_id").append(option);
                        }
                    }
                }
             })
           });
           
           $('#gndivision_id').change(function () {
               getdistrance();
           });
           
           $('#institute_id').change(function () {
               getdistrance();
           });
           
            function getdistrance(){
                var gnid = $('#gndivision_id').val();
               var insid = $('#institute_id').val();
               $.ajax({
                    url: "{{ url('/distrance') }}",
                    method: 'get',
                      data: {
                        gnid:gnid, 
                        insid:insid
                        },
                        success: function(response){
                          $("#distores").val(response.distance);
     
                  }
                });
            }
            
            $('#designation_id').change(function (){
                var id = $(this).val();
                $.ajax({
                    url:'/desigcatg/'+id,
                    type:'get',
                    dataType:'json',
                    success: function(response){
                        $("#desigcatg").val(response.catg);
                        console.log(response.catg);
                    },
                    error: function(response){
                        alert("Error")
                    }
             });
           });

    //..................................... Adding Degree Subject .....................................
    $(document).on("click", ".addDegreeSubject", function(e){
        e.preventDefault();

        var nextindex = $(".element").length + 1;

        $(".element:last").after("<div class='element' id='div_degree_"+ nextindex +"'></div>");

        var html = "";
        html += "<tr id='row_degree_"+ nextindex +"'>";

        // SUBJECT DROPDOWN
        html += "<td>";
        html += "<select class='form-control form-control-sm' name='subject_name[]' required>";
        html += "<option value=''>-- Choose Subject --</option>";

        var subjectData = @json($degreesubs ?? []);

        if(subjectData && subjectData.length > 0){
            for(var i = 0; i < subjectData.length; i++){
                html += "<option value='"+ subjectData[i].name +"'>"
                    + subjectData[i].name + "</option>";
            }
        }

        html += "</select>";
        html += "</td>";

        // DELETE BUTTON
        html += "<td>";
        html += "<button type='button' id='remove_degree_" + nextindex + "' class='btn remove_degree btn-danger btn-sm'>";
        html += "<i class='fa fa-trash'></i></button>";
        html += "</td>";

        html += "</tr>";

        $("#degreesubjecttable tbody").append(html);

    });

    $('.form-card').on('click','.remove_degree',function(e){
        e.preventDefault();

        var id = this.id;
        var split_id = id.split("_");
        var deleteindex = split_id[2];

        $("#row_degree_" + deleteindex).remove();
    });

    $(document).on('click', '.removedegreedata', function(e){
        e.preventDefault();

        var fullid = $(this).data("id");
        var split_id = fullid.split("_");
        var id = split_id[1];

        if(!confirm("Are you sure you want to remove the subject?")) return;

        var token = $("meta[name='csrf-token']").attr("content");

        $.ajax({
            url: "/empdegsubject/" + id,
            type: 'DELETE',
            data: {
                "_token": token
            },
            success: function (data){
                $("#rowdata_" + id).remove();
                alert(data.success);
            },
            error: function (xhr){
                console.log(xhr.responseText);
                alert("Delete failed");
            }
        });
    });



    //..........................................................Adding a Cource.........................................................       
$(document).on("click", ".addcourse", function(e){

    var nextindex = $("#coursetable tbody tr").length + 1;

    $(".element:last").after("<div class='element' id='div_"+ nextindex +"'></div>");

    var html="";
    html += "<tr id='row_"+ nextindex +"'>";

    // Course
    html += "<td>";
    html += "<select class='form-control form-control-sm' name='course_name[]' required>";
    html += "<option value=''>-- Select Qualification --</option>";

    var qualifData = @json($qualifData ?? []);

    if(qualifData.length > 0){
        for(var i = 0; i < qualifData.length; i++){
            html += "<option value='"+ qualifData[i].name +"'>" 
                 + qualifData[i].name + "</option>";
        }
    }

    html += "</select>";
    html += "</td>";

    // Institution
    html += "<td>";
    html += "<select class='form-control form-control-sm' name='institution[]' required>";
    html += "<option value=''>-- Select Institution --</option>";

    var instituteData = @json($instituteData ?? []);

    if(instituteData.length > 0){
        for(var i = 0; i < instituteData.length; i++){
            html += "<option value='"+ instituteData[i].name +"'>" 
                 + instituteData[i].name + "</option>";
        }
    }

    html += "</select>";
    html += "</td>";

    // Duration
    html += "<td>";
    html += "<input type='text' class='form-control form-control-sm' name='duration[]' required>";
    html += "</td>";

    // Delete Button
    html += "<td>";
    html += "<button type='button' id='remove_" + nextindex + "' class='btn remove btn-danger btn-sm'>";
    html += "<i class='fa fa-trash'></i></button>";
    html += "</td>";

    html += "</tr>";

    $("#coursetable tbody").append(html);
});

// Remove dynamic row
$('.form-card').on('click','.remove',function(e){
    e.preventDefault();
    var id = this.id;
    var split_id = id.split("_");
    var deleteindex = split_id[1];
    $("#row_" + deleteindex).remove();
});

// Remove existing record (AJAX)
$(".removedata").click(function(e){
    e.preventDefault();

    var fullid = $(this).data("id");
    var split_id = fullid.split("_");
    var id = split_id[1];

    if(confirm("Are you sure you want to remove this item?")){

        var token = $("meta[name='csrf-token']").attr("content");

        $.ajax({
            url: "/qualification/" + id,
            type: "DELETE",
            data: {
                id: id,
                _token: token
            },
            success: function(data){
                $("#rowdata_" + id).remove();
                alert(data.success);
            },
            error: function(){
                alert("Error deleting record");
            }
        });
    }
});

    //..........................................................Adding a Teaching Subject.........................................................
    $(document).on("click", ".addsubject", function(e){
        e.preventDefault();
         
            var nextindex = $("#subjecttable tbody tr").length + 1;
 
            // Adding new div container after last occurance of element1 class
            $(".element1:last").after("<div class='element1' id='div_"+ nextindex +"'></div>");

            // Adding element to <table>
            var html="";
            html += "<tr id='row1_"+ nextindex +"'>";
            html += "<td>";
            html += "<select name='teachsubject_id[]' id='teachsubject_id"+ nextindex +"' class='form-control form-control-sm' required>";
            html += "<option disabled selected value> -- Select Subject -- </option>"
            html += "<option value=''></option>"
            html += "</select>";
            html += "</td>";
            html += "<td>";
            html += "<input class='form-control form-control-sm' name='periods[]' id='periods"+ nextindex +"' autocomplete='off' required>";
            html += "</td>";
            html += "<td>"
            html += "<button id='remove1_" + nextindex + "' class='btn remove1 btn-danger btn-sm'><i class='fa fa-trash'></i></button>";
            html += "</td>";
            html += "</tr>";
            
            $("#subjecttable").find('tbody').append(html);
        
            //Append to subject dropdown
            var len= 0;
            var subjects = @json($teachsubs ?? []);
            len = subjects.length;
            if (len>0) {
                for (var i = 0; i<len; i++) {
                    var id = subjects[i].id;
                    var name = subjects[i].cadre;
                    var option2 = "<option value='"+id+"'>"+name+"</option>"; 
                    $("#teachsubject_id"+ nextindex +"").append(option2);                        
                }
            }
    });
    
    // Remove element
    $('.form-card').on('click','.remove1',function(e){
        e.preventDefault();
        var id = this.id;
        var split_id = id.split("_");
        var deleteindex = split_id[1];

        // Remove <div> with id
        $("#row1_" + deleteindex).remove();

    });
    
    $(".removedata1").click(function(e){
        e.preventDefault();
        var fullid = $(this).data("id");
        var split_id = fullid.split("_");
        var id = split_id[1];

        if (!confirm("are you sure you want to remove the item?")) return;

        var token = $("meta[name='csrf-token']").attr("content");

        $.ajax({
            url: "/teachsubject/" + id,
            type: 'DELETE',            // use DELETE verb
            data: {
                "_token": token
            },
            headers: {
                'X-CSRF-TOKEN': token  // extra safety
            },
            success: function (data){
                $("#rowdata1_" + id).remove();
                alert(data.success);
            },
            error: function (xhr, status, error) {
                console.log(xhr);
                alert("Error: " + (xhr.responseJSON?.error || error || "unknown"));
            }
        });
    });
});
    

// Hook edit button — replace prior edit handler (use namespaced event to avoid duplicates)
    $('#edit').off('click.certifyToggle').on('click.certifyToggle', function(e){
        e.preventDefault();

        $('#form').toggleClass('view');

        // toggle readonly/disabled fields as before
        $('input').each(function(){
            var inp = $(this);
            if (inp.attr('readonly')) inp.removeAttr('readonly'); else inp.attr('readonly', 'readonly');
        });
        $('select').each(function(){
            var inp = $(this);
            if (inp.attr('disabled')) inp.removeAttr('disabled'); else inp.attr('disabled', 'disabled');
        });

        // toggle submitdiv display (existing behavior)
        if (submitDiv) {
            if (submitDiv.style.display === "none" || submitDiv.style.display === "") {
                submitDiv.style.display = "block";
            } else {
                submitDiv.style.display = "none";
            }
        }

        // toggle other UI blocks as before
        var adddegreebtn = document.getElementById("adddegreebtn");
        if (adddegreebtn) adddegreebtn.style.display = (adddegreebtn.style.display === "none" || adddegreebtn.style.display === "") ? "block" : "none";

        var addqualifbtn = document.getElementById("addqualifbtn");
        if (addqualifbtn) addqualifbtn.style.display = (addqualifbtn.style.display === "none" || addqualifbtn.style.display === "") ? "block" : "none";

        var subjectbtn = document.getElementById("subjectbtn");
        if (subjectbtn) subjectbtn.style.display = (subjectbtn.style.display === "none" || subjectbtn.style.display === "") ? "block" : "none";

        if ($('.removedata').is(':visible')) {
            $('.removedata').hide();
        } else {
            $('.removedata').show();
        }
        
        if ($('.removedata1').is(':visible')) {
            $('.removedata1').hide();
        } else {
            $('.removedata1').show();
        }

        if ($('.removedegreedata').is(':visible')) {
            $('.removedegreedata').hide();
        } else {
            $('.removedegreedata').show();
        }
        

        // After toggling, enable/disable checkbox and save button appropriately
        var submitVisible = submitDiv && submitDiv.style.display === 'block';

        if (certifyChk) {
            if (submitVisible) {
                certifyChk.disabled = false; // allow user to check
                // preserve its checked state if any; but keep saveBtn controlled by checkbox change listener
                saveBtn.disabled = !certifyChk.checked;
            } else {
                // exiting edit mode: reset checkbox and disable it, disable save button
                certifyChk.checked = false;
                certifyChk.disabled = true;
                saveBtn.disabled = true;
            }
        } else {
            // no checkbox present: keep existing behaviour (saveBtn visible/enabled when submit shown)
            saveBtn.disabled = !submitVisible;
        }
    });


$(document).ready(function() {
  const $save = $('#saveBtn');
  const $cert = $('#certifyChk');
  const $submitDiv = $('#submitdiv');
  const $addQual = $('#addqualifbtn');
  const $subjectBtn = $('#subjectbtn');
  const $addDegreeBtn = $('#adddegreebtn');

  const $rmvData = $('.removedata');
  const $rmvData1 = $('.removedata1');
  const $rmvDegData = $('.removedegreedata');
  
  // initial states
  $submitDiv.hide();
  if ($save.length) $save.prop('disabled', true);
  if ($cert.length) $cert.prop('disabled', true).prop('checked', false);

  // helper: toggle readonly/disabled for inputs/selects
  function toggleFields() {
    $('input').each(function(){
      const $i = $(this);
      if ($i.is('[readonly]')) $i.removeAttr('readonly'); else $i.attr('readonly','readonly');
    });
    $('select').each(function(){
      const $s = $(this);
      if ($s.is(':disabled')) $s.prop('disabled', false); else $s.prop('disabled', true);
    });
  }

  // edit button handler
  $('#edit').off('click.certifyToggle').on('click.certifyToggle', function(e){
    e.preventDefault();

    $('#form').toggleClass('view');
    toggleFields();

    // toggle submit area and related UI
    $submitDiv.toggle();
    $addQual.toggle();
    $addDegreeBtn.toggle();
    $subjectBtn.toggle();

    $rmvData.toggle();
    $rmvData1.toggle();
    $rmvDegData.toggle();


    const visible = $submitDiv.is(':visible');

    if (visible) {
      // entering edit mode -> enable checkbox (user must check to enable save)
      if ($cert.length) {
        $cert.prop('disabled', false);
        $save.prop('disabled', !$cert.is(':checked'));
      } else {
        $save.prop('disabled', false);
      }
    } else {
      // leaving edit mode -> reset checkbox and disable it, disable save button
      if ($cert.length) {
        $cert.prop('checked', false).prop('disabled', true);
      }
      $save.prop('disabled', true);
    }
  });

  // checkbox change -> only enable save when checkbox checked AND submitDiv visible
  $cert.off('change.certify').on('change.certify', function(){
    const visible = $submitDiv.is(':visible');
    $save.prop('disabled', !(this.checked && visible));
  });

  // keep existing save click logic (modal or submit)
  $save.off('click.saveHandler').on('click.saveHandler', function(){
    @if(!empty($employeeDummy))
      var modal = new bootstrap.Modal(document.getElementById('dummyUpdateModal'));
      modal.show();
    @else
      $('#employee_form').submit();
    @endif
  });

  // When form is submitted, ensure disabled fields are enabled so they get posted
  $('#employee_form').on('submit', function(){

    // Restore original values for masked fields
    ['mobile','whatsapp','fixedphone'].forEach(function(id){
        var input = document.getElementById(id);
        if(input){
            var original = input.getAttribute('data-original');
            if(original){
                input.value = original;
            }
        }
    });

    // Enable disabled fields so they get posted
    $(this).find(':disabled').each(function(){
        $(this).removeAttr('disabled');
    });
});

});


</script>
@endpush