@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
  <div class="row">
    <div class="col-md-12">
      @include('layouts.notification')
    </div>
  </div>
  <div class="card-header py-3">
    <h6 class="m-0 font-weight-bold text-primary float-left">Users List</h6>
  </div>
  <div class="card-body">
    <div>  
      Previous Processing Time: {{$process->$prevprocess}} | Time Due: {{$process->service->$cntrestime}} Min | 
      <p id="timer"></p>
    </div>
    <br>
    <div class="row">
      <div class="col-md-6">
            <div class="row">
              <div class="col-sm-4">
                <label for="" class="control-label">Process ID:</label>
              </div>
              <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" name="id" id="id" value="{{$process->id}}" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <label for="" class="control-label">Employee Name:</label>
              </div>
              <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" name="empname" id="empname" value="{{$process->employee->title.'.'.$process->employee->initial.'.'.$process->employee->surname}}" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <label for="" class="control-label">NIC:</label>
              </div>
              <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" name="nic" id="nic" value="{{$process->employee->nic}}" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <label for="" class="control-label">Designation:</label>
              </div>
              <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" name="designation" id="designation" value="{{isset($process->employee->designation->designation) ? $process->employee->designation->designation : ''}}" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <label for="" class="control-label">Cadre Subject:</label>
              </div>
              <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" name="cadre" id="cadre" value="{{isset($process->employee->cadresubject->cadre) ? $process->employee->cadresubject->cadre : ''}}" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <label for="" class="control-label">Institute:</label>
              </div>
              <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" name="institute" id="institute"   value="{{$process->employee->institute->institute}}" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <label for="" class="control-label">Service:</label>
              </div>
              <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" name="service" id="service" value="{{$process->service->service}}" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <label for="" class="control-label">PF Clerk:</label>
              </div>
              <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" name="pfclerk" id="pfclerk" value="{{$process->employee->institute1->pfclerk->name}}" readonly>
              </div>
            </div>
            <div class="row">
              <div class="col-sm-4">
                <label for="" class="control-label">Acct Clerk:</label>
              </div>
              <div class="col-sm-8">
                <input type="text" class="form-control form-control-sm" name="acctclerk" id="acctclerk" value="{{$process->employee->institute1->acctclerk->name}}" readonly>
              </div>
            </div>
            <br/>
            <form action="{{ route('process.update', ['id'=>$process->id,'cntprocess'=>$cntprocess,'cntres'=>$cntres]) }}" id="employee_form" name="employee_form" method="post" readonly>
              @csrf
              <!-- append slugs if exist -->
              @if(!empty($process->transfer))
                  @include('service_mgt.services.partials.transfer.index')
              @endif
              @if(!empty($process->cfactivity))
                  @include('service_mgt.services.partials.cfactivity.index')
              @endif
              <br>
              @if($process->user_id == auth()->user()->id)
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="pendingchk" name="pendingchk" value="1" {{$process->pendingchk == 1 ? ' checked' : ''}}>
                    <label class="form-check-label">Make Pending</label>
                  </div>
                </div>
                <div class="col-sm-8">
                  @if($process->despending)
                    <textarea rows="4" cols="50" class="form-control form-control-sm" name="despending" id="despending">{{$process->despending}}</textarea>
                  @else
                    <textarea rows="4" cols="50" class="form-control form-control-sm" name="despending" id="despending" hidden>{{$process->despending}}</textarea>
                  @endif
                </div>
              </div>
              @endif
              <div class="form-group" align="right">
                <input type="hidden" name="emp_id" id="emp_id" />
                <input type="hidden" class="form-control form-control-sm" name="nres_id" id="nres_id" value="{{$nxtres}}">
                <input type="hidden" name="cntres_id" id="cntres_id" value="{{$cntres_id}}">

                <!-- for service_log -->
                <input type="hidden" name="cntrestime" id="cntrestime" value="{{$process->service->$cntrestime}}">
                <input type="hidden" name="prevproctime" id="prevproctime" value="{{$process->$prevprocess}}">
                <!-- End -->

                <!--for service slugs-->
                <input type="hidden" name="slug" id="slug" value="{{$process->service->slug}}">
                <input type="hidden" name="service_type" id="service_type" value="{{$process->service->$cntrestype}}">
                <input type="hidden" name="service_category" id="service_category" value="{{isset($process->service->$cntrescategory->category) ? $process->service->$cntrescategory->category : ''}}"> 
                
                @if($process->user_id == auth()->user()->id)
                <div class="row">
                  <div class="col-lg-12 d-flex justify-content-center text-center">
                  <input type="submit" name="action_button" id="saveBtn" class="btn btn-warning" value="Update" />
                  </div>
                </div>
                @endif
              </div>
            </form>
          
  <!--------- Percentage Circle--------->  
  
  <div class="row">
    <div class="col-lg-12 d-flex justify-content-center">
      <div class="box" style="margin-top: 40px;">
        <div class="circlePercent">
          <div class="counter" data-percent="0"></div>
          <div class="progress"></div>
          <div class="progressEnd"></div>
        </div>
      </div>
    </div>
  </div>
</div>
@php
    $users = [
        ['id' => $process->service->user1_id, 'time' => 'processtime1', 'servicetype' => 'servicetype1', 'res_time' => 'res1time', 'last_user' => 1],
        ['id' => $process->service->user2_id, 'time' => 'processtime2', 'servicetype' => 'servicetype2', 'res_time' => 'res2time', 'last_user' => 2],
        ['id' => $process->service->user3_id, 'time' => 'processtime3', 'servicetype' => 'servicetype3', 'res_time' => 'res3time', 'last_user' => 3],
        ['id' => $process->service->user4_id, 'time' => 'processtime4', 'servicetype' => 'servicetype4', 'res_time' => 'res4time', 'last_user' => 4],
        ['id' => $process->service->user5_id, 'time' => 'processtime5', 'servicetype' => 'servicetype5', 'res_time' => 'res5time', 'last_user' => 5],
        ['id' => $process->service->user6_id, 'time' => 'processtime6', 'servicetype' => 'servicetype6', 'res_time' => 'res6time', 'last_user' => 6],
        ['id' => $process->service->user7_id, 'time' => 'processtime7', 'servicetype' => 'servicetype7', 'res_time' => 'res7time', 'last_user' => 7],
        ['id' => $process->service->user8_id, 'time' => 'processtime8', 'servicetype' => 'servicetype8', 'res_time' => 'res8time', 'last_user' => 8],
        ['id' => $process->service->user9_id, 'time' => 'processtime9', 'servicetype' => 'servicetype9', 'res_time' => 'res9time', 'last_user' => 9],
        ['id' => $process->service->user10_id, 'time' => 'processtime10', 'servicetype' => 'servicetype10', 'res_time' => 'res10time', 'last_user' => 10]
    ];
@endphp
<!---------------------------- Timeline ---------------------------->
<div class="col-md-6">
        <ul class="timeline">
          @foreach ($users as $index => $user)
            @if ($user['id'] != 0)
                <li>
                    @php
                        $photo = '';
                        $name = '';

                        if ($user['id'] == 31) {
                            $photo = $process->employee->institute1->pfclerk->employee_id ?? asset('backend/img/avatar.png');
                            $name = $process->employee->institute1->pfclerk->name;
                        } elseif ($user['id'] == 32) {
                            $photo = $process->employee->institute1->acctclerk->employee_id ?? asset('backend/img/avatar.png');
                            $name = $process->employee->institute1->acctclerk->name;
                        } else {
                            $photo = $service->{'user' . ($index + 1)}->employee_id ?? asset('backend/img/avatar.png');
                            $name = $service->{'user' . ($index + 1)}->name;
                        }
                    @endphp
                    
                    <img src="{{ '/images/employees/'.$photo.'.jpg' }}" alt="" class="timeline-badge">
                    
                    @if($process->last_updated_user >= $user['last_user'])
                        <span><i class="fa fa-check"></i></span>
                    @elseif($process->last_updated_user == $user['last_user'])
                        <span><i class="fa fa-cogs"></i></span>
                    @endif
                    
                    <div class="timeline-panel">
                        <div class="timeline-heading">
                            <h5 class="timeline-title">{{ $name }}</h5>
                        </div>
                        <div class="timeline-body">
                            <p><strong>Service Type : </strong> {{ $process->service->{$user['servicetype']}->name ?? '' }}</p>
                            <p><strong>Process Status : </strong> {{ empty($process->{$user['time']}) ? 'Pending' : 'Completed' }}</p>
                            <p><strong>Time Allocated : </strong> {{ Carbon\CarbonInterval::minutes($process->service->{$user['res_time']})->cascade()->forHumans() }}</p>
                            
                            @if ($cntprocess != $user['time'] && isset($process->{$user['time']}))
                                @php
                                    $timetakenKey = 'timetaken' . ($index + 1);
                                    $timetaken = $timetaken_data[$timetakenKey] ?? null;
                                @endphp

                                @if($timetaken !== null)
                                    <p><strong>Time Taken  : </strong>{{ Carbon\CarbonInterval::minutes($timetaken)->cascade()->forHumans() }}
                                    @if($process->service->{$user['res_time']} > Carbon\Carbon::parse($process->{$user['time']})->diffInMinutes(Carbon\Carbon::parse($process->{$user['time']})))
                                        <span><i class="fa fa-smile-o" style="color:blue;"></i></span>
                                    @else
                                        <span><i class="far fa-frown" style="color:red;"></i></span>
                                    @endif
                                    </p>
                                @endif
                            @endif
                        </div>
                    </div>
                </li>
            @endif
        @endforeach
        </ul>
      @if($duration->timespent != 0) Time spent up to now: {{carbon\CarbonInterval::minutes($duration->timespent)->cascade()->forHumans()." | "."Time allocated Up to now: ".carbon\CarbonInterval::minutes($duration->timeallocated)->cascade()->forHumans()}} @endif
    </div>
  </div>
</div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
<style type="text/css">
/* Check and Cogs Styles */
.fa-check{
  color: green;
}
.fa-cogs{
  color: orange;
}
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


/* <!--------- Percentage Circle---------> */
.container .box{
  width: 10%;
  margin: 0 auto;
}
 .circlePercent {
     position: relative;
     width: 96px;
     height: 96px;
     border-radius: 50%;
     background: orange;
 }

 .circlePercent:before,
 .circlePercent>.progressEnd {
     position: absolute;
     z-index: 3;
     top: 2px;
     left: 45px;
     width: 6px;
     height: 6px;
     border-radius: 50%;
     background: white;
     -ms-transform-origin: 3px 46px;
     transform-origin: 3px 46px;
     content: ""
 }

 .circlePercent:after,
 .circlePercent>.progress {
     position: absolute;
     -ms-transform-origin: 48px 48px;
     transform-origin: 48px 48px;
     z-index: 0;
     top: 0;
     left: 0;
     width: 48px;
     height: 96px;
     border-radius: 48px 0 0 48px;
     background: orange;
     content: ""
 }

 .circlePercent.fiftyPlus:after {
     background: white;
     -ms-transform: rotate(180deg);
     transform: rotate(180deg)
 }

 .circlePercent>.progress.progress {
     background: white
 }

 .circlePercent>.counter {
     position: absolute;
     box-sizing: border-box;
     z-index: 2;
     width: 100px;
     height: 100px;
     margin-top: -2px;
     margin-left: -2px;
     border-radius: 50%;
     border: 4px solid orange
 }

 .circlePercent>.counter:before {
     position: absolute;
     z-index: 1;
     top: 50%;
     margin-top: -13px;
     width: 100%;
     height: 26px;
     font-size: 26px;
     line-height: 26px;
     font-family: sans-serif;
     text-align: center;
     color: white;
     content: attr(data-percent) "%"
 }

 .circlePercent>.counter:after {
     position: absolute;
     width: 80px;
     height: 80px;
     top: 6px;
     left: 6px;
     border-radius: 50%;
     background: orange;
     content: ""
 }

 .circlePercent>.counter[data-percent="100"] {
     background: white
 }
</style>
@endpush

@push('scripts')
<script type="text/javascript">
$('.timeline-panel').click(function() {
    $('.timeline-body', this).toggle(); // p00f
});

// Percentage circle function
$(document).ready(function(){
    function setProgress(elem, percent) {
    var
    degrees = percent * 3.6,
    transform = /MSIE 9/.test(navigator.userAgent) ? 'msTransform' : 'transform';
    elem.querySelector('.counter').setAttribute('data-percent', Math.round(percent));
    elem.querySelector('.progressEnd').style[transform] = 'rotate(' + degrees + 'deg)';
    elem.querySelector('.progress').style[transform] = 'rotate(' + degrees + 'deg)';
    if(percent >= 50 && !/(^|\s)fiftyPlus(\s|$)/.test(elem.className))
    elem.className += ' fiftyPlus';
  }

  (function() {
    var pernt = {!! json_encode($percentage, JSON_HEX_TAG) !!};
    var
    elem = document.querySelector('.circlePercent'),
    percent = 0;
    (function animate() {
    setProgress(elem, (percent += .50));
    if(percent < pernt) setTimeout(animate, 15); })(); })(); 

});

//Sending SMS Functions
function SMSFunction() {
		var xhr = new XMLHttpRequest();
    var checkBox = document.getElementById("pendingchk");

    if (checkBox.checked == true){
      var tmessage = '{{'your service '.$service->service.' (Ref.no: '.$process->id.') is on hold'}}';
    } else {
      var tmessage = '{{$service->smsdesc.'Ref.no: '.$process->id.". Click here to send us a feedback https://smgt.battiwestzeo.lk/feedback/".$process->uniquekey}}';
    }
		var mnumber = '{{'94'.$process->employee->mobile}}';
		xhr.open("GET", "https://richcommunication.dialog.lk/api/sms/inline/send?q=e78f434d6604755&destination=" + mnumber + "&message=" + tmessage + "&from=BATWESTZEO", true);
			xhr.onreadystatechange = function(){
				if (xhr.readyState == 4 && xhr.status == 200) {			
				}
			}; 	    	
		xhr.send();	
}      

$( "#saveBtn" ).click(function() {
  var nextRes = {!! json_encode($nxtres, JSON_HEX_TAG) !!};
  var checkBox = document.getElementById("pendingchk");
  if(nextRes == 0 || checkBox.checked == true){
    SMSFunction();
  }
});


$("#pendingchk").click(function() {
    if($(this).is(":checked")) {
      let elementbtn = document.getElementById("despending");
      elementbtn.removeAttribute("hidden");
    } else {
      document.getElementById("despending").setAttribute("hidden", "hiddenbtn");
    }
});

//Count down timer
var cdate = {!! json_encode($process->$prevprocess, JSON_HEX_TAG) !!}; 
var restime = {!! json_encode($process->service->$cntrestime, JSON_HEX_TAG) !!}; 
const countDownDate = new Date(cdate).getTime();

const timeCountdown = setInterval(() => {
  // Get current Time
  const now = new Date().getTime();

  // Difference bt countDownDate and now
  const timeDifference = (countDownDate - now) + (restime * 60000);

  // Time calculations for days, hours, minutes and seconds
  const millisecondInDay = 1000 * 60 * 60 * 24;
  const millisecondInHour = 1000 * 60 * 60;
  const millisecondInMinute = (1000 * 60);

  const days = Math.floor(timeDifference / millisecondInDay);
  const hours = Math.floor((timeDifference % millisecondInDay) / millisecondInHour);
  const minutes = Math.floor((timeDifference % (millisecondInHour)) / millisecondInMinute);
  const seconds = Math.floor((timeDifference % (millisecondInMinute)) / 1000);

  document.getElementById("timer").innerHTML = days + "d - " + hours + "h - "
    + minutes + "m - " + seconds + "s ";


  if (timeDifference < 0) {
    clearInterval(timeCountdown);
      document.getElementById("timer").innerHTML = "Your due time is expired";
  }
  }, 1000);
</script>
@endpush


