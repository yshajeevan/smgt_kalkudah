@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
  <div class="row">
    <div class="col-md-12">
      @include('layouts.notification')
    </div>
  </div>
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary float-left">Edit Service</h6>
  </div>
  <div class="card-body">
    <form action="{{ isset($service) 
    ? route('service.update',$service->id) 
    : route('service.store') }}" 
    method="POST">
    @csrf 
    @if(isset($service))
      @method('PUT')
    @endif
    <div class="row">
      <div class="col-md-6">
        <div class="form-group text-dark">
          <label for="" class="control-label">Service</label>
          <input type="text" class="form-control form-control-md" name="service" id="service" value="{{ old('service', isset($service) ? $service->service : '') }}">
        </div>
      </div>
    </div> 
    <div class="row">
      <div class="col-md-6">
        <div class="form-group text-dark">
          <label for="" class="control-label">Branch</label>
          <select name="branch" id="branch" class="form-control form-control-md" required>
            <option value="">--Select Type--</option>
            <option value="adm" @if(isset($service) && $service->branch=="adm"){{"selected"}} @endif>Admin</option>
            <option value="pln" @if(isset($service) && $service->branch=="pln"){{"selected"}} @endif>Planning</option> 
            <option value="dev" @if(isset($service) && $service->branch=="dev"){{"selected"}} @endif>Development</option>
            <option value="mgt" @if(isset($service) && $service->branch=="mgt"){{"selected"}} @endif>Management</option> 
            <option value="act" @if(isset($service) && $service->branch=="act"){{"selected"}} @endif>Accounts</option> 
            <option value="oth" @if(isset($service) && $service->branch=="oth"){{"selected"}} @endif>Others</option>
          </select>
        </div>
      </div>
    </div> 
    <div class="row">
      <div class="col-md-6">
        <div class="form-group text-dark">
          <label for="" class="control-label">Service Type</label>
          <select name="remarks" id="remarks" class="form-control form-control-md" required>
            <option value="">--Select Type--</option>
            <option value="general" @if(isset($service) && $service->remarks=="general"){{"selected"}} @endif>General</option>
            <option value="monthly" @if(isset($service) && $service->remarks=="monthly"){{"selected"}} @endif>Monthly</option> 
            <option value="often" @if(isset($service) && $service->remarks=="often"){{"selected"}} @endif>Often</option>
            <option value="workshop" @if(isset($service) && $service->remarks=="workshop"){{"selected"}} @endif>Workshop</option> 
          </select>
        </div>
      </div>
    </div> 
    <div class="row">
      <div class="col-md-6">
        <div class="form-group text-dark">
          <label for="" class="control-label">SMS Description</label>
          <input type="text" class="form-control form-control-md" name="smsdesc" id="smsdesc" value="{{ old('smsdesc', isset($service) ? $service->smsdesc : '') }}">
        <p>eg: your application is completed</p>
        </div>
      </div>
    </div> 
    <!---------------------------- Res1 ---------------------------->
    <div class="col-md-6">
            <ul class="timeline">
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle1.png') }}" alt="" class="timeline-badge">

                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-9">
                        <div class="form-group">
                          <select name="user1_id" id="user1_id" class="form-control form-control-md font-weight-bold">
                            <option value="">--Select Officer--</option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" {{(isset($service) && $service->user1_id == $user->id)  ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <select name="servicetype1_id" id="servicetype1_id" class="form-control form-control-md">
                            <option value="">--Select Service Type--</option>
                            @foreach ($servicetypes as $servicetype)
                            <option value="{{$servicetype->id}}" {{(isset($service) && $service->servicetype1_id == $servicetype->id)  ? 'selected' : ''}}>{{$servicetype->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="form-group">
                          <input type="number" class="form-control form-control-md" name="res1time" id="res1time" value="{{ old('res1time', isset($service) ? $service->res1time : '0') }}" readonly>
                        <p>Duration (Minutes)</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <!-- ........................................Res2........................................ -->
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle2.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-9">
                        <div class="form-group">
                          <select name="user2_id" id="user2_id" class="form-control form-control-md font-weight-bold">
                            <option value="">--Select Officer--</option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" {{(isset($service) && $service->user2_id == $user->id)  ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <select name="servicetype2_id" id="servicetype2_id" class="form-control form-control-md">
                            <option value="">--Select Service Type--</option>
                            @foreach ($servicetypes as $servicetype)
                            <option value="{{$servicetype->id}}" {{(isset($service) && $service->servicetype2_id == $servicetype->id)  ? 'selected' : ''}}>{{$servicetype->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="form-group">
                          <input type="number" class="form-control form-control-md" name="res2time" id="res2time" value="{{ old('res2time', isset($service) ? $service->res2time : '') }}">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <!-- ........................................Res3........................................ -->
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle3.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-9">
                        <div class="form-group">
                          <select name="user3_id" id="user3_id" class="form-control form-control-md font-weight-bold">
                            <option value="">--Select Officer--</option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" {{(isset($service) && $service->user3_id == $user->id)  ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <select name="servicetype3_id" id="servicetype3_id" class="form-control form-control-md">
                            <option value="">--Select Service Type--</option>
                            @foreach ($servicetypes as $servicetype)
                            <option value="{{$servicetype->id}}" {{(isset($service) && $service->servicetype3_id == $servicetype->id)  ? 'selected' : ''}}>{{$servicetype->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="form-group">
                          <input type="number" class="form-control form-control-md" name="res3time" id="res3time" value="{{ old('res3time', isset($service) ? $service->res3time : '') }}">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <!-- ........................................Res4........................................ -->
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle4.png') }}" alt="" class="timeline-badge">
              
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-9">
                        <div class="form-group">
                          <select name="user4_id" id="user4_id" class="form-control form-control-md font-weight-bold">
                            <option value="">--Select Officer--</option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" {{(isset($service) && $service->user4_id == $user->id)  ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <select name="servicetype4_id" id="servicetype4_id" class="form-control form-control-md">
                            <option value="">--Select Service Type--</option>
                            @foreach ($servicetypes as $servicetype)
                            <option value="{{$servicetype->id}}" {{(isset($service) && $service->servicetype4_id == $servicetype->id)  ? 'selected' : ''}}>{{$servicetype->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="form-group">
                          <input type="number" class="form-control form-control-md" name="res4time" id="res4time" value="{{ old('res4time', isset($service) ? $service->res4time : '') }}">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <!-- ........................................Res5........................................ -->
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle5.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-9">
                        <div class="form-group">
                          <select name="user5_id" id="user5_id" class="form-control form-control-md font-weight-bold">
                            <option value="">--Select Officer--</option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" {{(isset($service) && $service->user5_id == $user->id)  ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <select name="servicetype5_id" id="servicetype5_id" class="form-control form-control-md">
                            <option value="">--Select Service Type--</option>
                            @foreach ($servicetypes as $servicetype)
                            <option value="{{$servicetype->id}}" {{(isset($service) && $service->servicetype5_id == $servicetype->id)  ? 'selected' : ''}}>{{$servicetype->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="form-group">
                          <input type="number" class="form-control form-control-md" name="res5time" id="res5time" value="{{ old('res5time', isset($service) ? $service->res5time : '') }}">
                        </div>
                      </div>
                    </div>              
                  </div>
                </div>
              </li>
              <!-- ........................................Res6........................................ -->
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle6.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-9">
                        <div class="form-group">
                          <select name="user6_id" id="user6_id" class="form-control form-control-md font-weight-bold">
                            <option value="">--Select Officer--</option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" {{(isset($service) && $service->user6_id == $user->id)  ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <select name="servicetype6_id" id="servicetype6_id" class="form-control form-control-md">
                            <option value="">--Select Service Type--</option>
                            @foreach ($servicetypes as $servicetype)
                            <option value="{{$servicetype->id}}" {{(isset($service) && $service->servicetype6_id == $servicetype->id)  ? 'selected' : ''}}>{{$servicetype->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="form-group">
                          <input type="number" class="form-control form-control-md" name="res6time" id="res6time" value="{{ old('res6time', isset($service) ? $service->res6time : '') }}">
                        </div>
                      </div>
                    </div>                
                  </div>
                </div>
              </li>
              <!-- ........................................Res7........................................ -->
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle7.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-9">
                        <div class="form-group">
                          <select name="user7_id" id="user7_id" class="form-control form-control-md font-weight-bold">
                            <option value="">--Select Officer--</option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" {{(isset($service) && $service->user7_id == $user->id)  ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <select name="servicetype7_id" id="servicetype7_id" class="form-control form-control-md">
                            <option value="">--Select Service Type--</option>
                            @foreach ($servicetypes as $servicetype)
                            <option value="{{$servicetype->id}}" {{(isset($service) && $service->servicetype7_id == $servicetype->id)  ? 'selected' : ''}}>{{$servicetype->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="form-group">
                          <input type="number" class="form-control form-control-md" name="res7time" id="res7time" value="{{ old('res7time', isset($service) ? $service->res7time : '') }}">
                        </div>
                      </div>
                    </div>                
                  </div>
                </div>
              </li>
              <!-- ........................................Res8........................................ -->
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle8.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-9">
                        <div class="form-group">
                          <select name="user8_id" id="user8_id" class="form-control form-control-md font-weight-bold">
                            <option value="">--Select Officer--</option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" {{(isset($service) && $service->user8_id == $user->id)  ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <select name="servicetype8_id" id="servicetype8_id" class="form-control form-control-md">
                            <option value="">--Select Service Type--</option>
                            @foreach ($servicetypes as $servicetype)
                            <option value="{{$servicetype->id}}" {{(isset($service) && $service->servicetype8_id == $servicetype->id)  ? 'selected' : ''}}>{{$servicetype->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="form-group">
                          <input type="number" class="form-control form-control-md" name="res8time" id="res8time" value="{{ old('res8time', isset($service) ? $service->res8time : '') }}">
                        </div>
                      </div>
                    </div>                
                  </div>
                </div>
              </li>
              <!-- ........................................Res9........................................ -->
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle9.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-9">
                        <div class="form-group">
                          <select name="user9_id" id="user9_id" class="form-control form-control-md font-weight-bold">
                            <option value="">--Select Officer--</option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" {{(isset($service) && $service->user9_id == $user->id)  ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <select name="servicetype9_id" id="servicetype9_id" class="form-control form-control-md">
                            <option value="">--Select Service Type--</option>
                            @foreach ($servicetypes as $servicetype)
                            <option value="{{$servicetype->id}}" {{(isset($service) && $service->servicetype9_id == $servicetype->id)  ? 'selected' : ''}}>{{$servicetype->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="form-group">
                          <input type="number" class="form-control form-control-md" name="res9time" id="res9time" value="{{ old('res9time', isset($service) ? $service->res9time : '') }}">
                        </div>
                      </div>
                    </div>                
                  </div>
                </div>
              </li>
              <!-- ........................................Res10........................................ -->
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle10.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-9">
                        <div class="form-group">
                          <select name="user10_id" id="user10_id" class="form-control form-control-md font-weight-bold">
                            <option value="">--Select Officer--</option>
                            @foreach ($users as $user)
                            <option value="{{$user->id}}" {{(isset($service) && $service->user10_id == $user->id)  ? 'selected' : ''}}>{{$user->name}}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="form-group">
                          <select name="servicetype10_id" id="servicetype10_id" class="form-control form-control-md">
                            <option value="">--Select Service Type--</option>
                            @foreach ($servicetypes as $servicetype)
                            <option value="{{$servicetype->id}}" {{(isset($service) && $service->servicetype10_id == $servicetype->id)  ? 'selected' : ''}}>{{$servicetype->name}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>
                      <div class="col-lg-3">
                        <div class="form-group">
                          <input type="number" class="form-control form-control-md" name="res10time" id="res10time" value="{{ old('res10time', isset($service) ? $service->res10time : '') }}">
                        </div>
                      </div>
                    </div>                
                  </div>
                </div>
              </li>
          </ul>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group" align="center">
              <input type="submit" id="saveBtn" class="btn btn-warning" value="{{ isset($service) ? 'Update': 'Add' }}">
            </div>
          </div>
        </div>
    </form>
  </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<style type="text/css">
/* Timeline Styles */
.timeline {
  list-style: none;
  padding: 20px 0 20px;
  position: relative;
}
.timeline:before {
  top: 0;
  bottom: 0;
  position: absolute;
  content: " ";
  width: 3px;
  background-color: #eeeeee;
  left: 25px;
  margin-right: -1.5px;
}
.timeline > li {
  margin-bottom: 20px;
  position: relative;
}
.timeline > li:before,
.timeline > li:after {
  content: " ";
  display: table;
}
.timeline > li:after {
  clear: both;
}
.timeline > li:before,
.timeline > li:after {
  content: " ";
  display: table;
}
.timeline > li:after {
  clear: both;
}
.timeline > li > .timeline-panel {
  width: calc( 100% - 75px );
  float: right;
  border: 1px solid #d4d4d4;
  border-radius: 2px;
  padding: 20px;
  position: relative;
  -webkit-box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
  box-shadow: 0 1px 6px rgba(0, 0, 0, 0.175);
}
.timeline > li > .timeline-panel:before {
  position: absolute;
  top: 26px;
  left: -15px;
  display: inline-block;
  border-top: 15px solid transparent;
  border-right: 15px solid #ccc;
  border-left: 0 solid #ccc;
  border-bottom: 15px solid transparent;
  content: " ";
}
.timeline > li > .timeline-panel:after {
  position: absolute;
  top: 27px;
  left: -14px;
  display: inline-block;
  border-top: 14px solid transparent;
  border-right: 14px solid #fff;
  border-left: 0 solid #fff;
  border-bottom: 14px solid transparent;
  content: " ";
}
.timeline > li > .timeline-badge {
  color: #fff;
  width: 50px;
  height: 50px;
  line-height: 50px;
  font-size: 1.4em;
  text-align: center;
  position: absolute;
  top: 16px;
  left: 0px;
  margin-right: -25px;
  background-color: #999999;
  z-index: 100;
  border-top-right-radius: 50%;
  border-top-left-radius: 50%;
  border-bottom-right-radius: 50%;
  border-bottom-left-radius: 50%;
}
.timeline > li.timeline-inverted > .timeline-panel {
  float: left;
}
.timeline > li.timeline-inverted > .timeline-panel:before {
  border-right-width: 0;
  border-left-width: 15px;
  right: -15px;
  left: auto;
}
.timeline > li.timeline-inverted > .timeline-panel:after {
  border-right-width: 0;
  border-left-width: 14px;
  right: -14px;
  left: auto;
}
.timeline-title {
  margin-top: 0;
  color: inherit;
  cursor:pointer;
}
.timeline-body > p,
.timeline-body > ul {
  margin-bottom: 0;
}
.timeline-body > p + p {
  margin-top: 5px;
}
.timeline-body {
    display: none;
}
/* Status notification */
/* .container { position: relative; }
.container img { display: block; } */
.fa fa-check { position: absolute; bottom:6; left:3; }
</style>
@endpush
