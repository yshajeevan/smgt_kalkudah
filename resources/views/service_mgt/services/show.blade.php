@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary float-left">Show Service</h6>
  </div>
  <div class="card-body">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Service Name and ID: {{$service->service." (".$service->id.")"}}</h5>
            <h6 class="card-subtitle mb-2 text-muted">Branch: {{$service->branch}}</h6>
            <h6 class="card-subtitle mb-2 text-muted">Service Type: {{$service->remarks}}</h6>
            <p class="card-text">SMS Description: {{$service->smsdesc}}</p>
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
                      <div class="col-lg-6">
                        <div class="form-group">
                          {{$service->user1->name}}
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                            <label for="" class="control-label">Duration: Time not allocated</label>
                        <p></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              <!-- ........................................Res2........................................ -->
              @if($service->user2_id)
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle2.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          {{$service->user2->name}}
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                            <label for="" class="control-label">Duration: {{Carbon\CarbonInterval::minutes($service->res2time)->cascade()->forHumans()}}</label>
                        <p></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endif
              <!-- ........................................Res3........................................ -->
              @if($service->user3_id)
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle3.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          {{$service->user3->name}}
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                            <label for="" class="control-label">Duration: {{Carbon\CarbonInterval::minutes($service->res3time)->cascade()->forHumans()}}</label>
                        <p></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endif
              <!-- ........................................Res4........................................ -->
              @if($service->user4_id)
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle4.png') }}" alt="" class="timeline-badge">
              
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          {{$service->user4->name}}
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                            <label for="" class="control-label">Duration: {{Carbon\CarbonInterval::minutes($service->res4time)->cascade()->forHumans()}}</label>
                        <p></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endif
              <!-- ........................................Res5........................................ -->
              @if($service->user5_id)
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle5.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          {{$service->user5->name}}
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                            <label for="" class="control-label">Duration: {{Carbon\CarbonInterval::minutes($service->res5time)->cascade()->forHumans()}}</label>
                        <p></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endif
              <!-- ........................................Res6........................................ -->
              @if($service->user6_id)
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle6.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          {{$service->user6->name}}
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                            <label for="" class="control-label">Duration: {{Carbon\CarbonInterval::minutes($service->res6time)->cascade()->forHumans()}}</label>
                        <p></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endif
              <!-- ........................................Res7........................................ -->
              @if($service->user7_id)
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle7.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          {{$service->user7->name}}
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                            <label for="" class="control-label">Duration: {{Carbon\CarbonInterval::minutes($service->res7time)->cascade()->forHumans()}}</label>
                        <p></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endif
              <!-- ........................................Res8........................................ -->
              @if($service->user8_id)
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle8.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                           {{$service->user8->name}} 
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                            <label for="" class="control-label">Duration: {{Carbon\CarbonInterval::minutes($service->res8time)->cascade()->forHumans()}}</label>
                        <p></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endif
              <!-- ........................................Res9........................................ -->
              @if($service->user9_id)
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle9.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                          {{$service->user9->name}} 
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                            <label for="" class="control-label">Duration: {{Carbon\CarbonInterval::minutes($service->res9time)->cascade()->forHumans()}}</label>
                        <p></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endif
              <!-- ........................................Res10........................................ -->
              @if($service->user10_id)
              <li>
                <img src="{{ isset($pfphoto->photo) ? $pfphoto->photo : asset('backend/img/numbers/circle10.png') }}" alt="" class="timeline-badge">
                    
                <div class="timeline-panel">
                  <div class="timeline-heading">
                    <div class="row">
                      <div class="col-lg-6">
                        <div class="form-group">
                            {{$service->user10->name}} 
                        </div>
                      </div>
                      <div class="col-lg-6">
                        <div class="form-group">
                            <label for="" class="control-label">Duration: {{Carbon\CarbonInterval::minutes($service->res10time)->cascade()->forHumans()}}</label>
                        <p></p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </li>
              @endif
        </div>
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