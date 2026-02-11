@extends('layouts.master')

@section('main-content')
<div class="container-flex">
    <div class="row">
        <div class="col-xl-12">
            <div class="card-slider">
                <div id="test1"><p>Service Management System</p></div>
                <div id="test2"><p>Zonal Education Office, Kalkudah</p></div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-4">
            <div class="card-body color1 ">
                <div class="float-left">
                    <h3>
                        <span class="currency"></span>
                        <span class="countn">{{$countservices}}</span>
                    </h3>
                    <p>Total Registered Services</p>
                </div>
                <div class="float-right">
                    <span><i class="fas fa-cogs"></i></span>
                </div>
            </div>
            </div>
        <div class="col-xl-4">  
            <div class="card-body color2">
                <div class="float-left">
                    <h3>
                      <span class="countn">{{$countstaff}}</span>
                    </h3>
                    <p>Total Staff</p>
                </div>
                <div class="float-right">
                    <span><i class="fas fa-child"></i></span>
                </div>
            </div>
            </div>
        <div class="col-xl-4">
            <div class="card-body color3">
                <div class="float-left">
                    <h3>
                      <span class="countn">{{$activeusers}}</span>
                    </h3>
                    <p>Total Active Users</p>
                </div>
                <div class="float-right">
                    <span><i class="fas fa-users"></i></span>
                </div>
            </div>
        </div>
    </div>
    <!-- Project statistic start -->
    <div class="row">
    <div class="col-xl-12">
        <div class="card proj-progress-card">
            <div class="card-block">
                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <h6>Total Process</h6>
                        <h5 class="mb-30 fw-700">{{$process->count('id')}}<span class="text-green ml-10"></span></h5>
                        <div class="progress">
                            <div class="progress-bar bg-red" style="width:100%"></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <h6>Pending Process</h6>
                        <h5 class="mb-30 fw-700">{{$process->where('user_id','!=',0)->where('pendingchk','=',0)->count('id')}}<span class="text-red ml-10">{{empty($process->where('user_id','!=',0)->count('id') > 0) ? '' : round($process->where('user_id','!=',0)->where('pendingchk','=',0)->count('id')/$process->count('id')*100,2)}}%</span></h5>
                            <div class="progress">
                                <div class="progress-bar bg-blue" style="width:{{empty($process->where('user_id','!=',0)->count('id') > 0) ? '' : round($process->where('user_id','!=',0)->where('pendingchk','=',0)->count('id')/$process->count('id')*100,2)}}%"></div>
                            </div>
                        </div>
                    <div class="col-xl-3 col-md-6">
                        <h6>Holding Process</h6>
                        <h5 class="mb-30 fw-700">{{$process->where('pendingchk','=',1)->count('id')}}<span class="text-green ml-10">{{empty($process->where('pendingchk','=',1)->count('id') > 0) ? '' : round($process->where('pendingchk','=',1)->count('id')/$process->count('id')*100,2)}}%</span></h5>
                        <div class="progress">
                            <div class="progress-bar bg-green" style="width:{{empty($process->where('pendingchk','=',1)->count('id') > 0) ? '' : round($process->where('pendingchk','=',1)->count('id')/$process->count('id')*100,2)}}%"></div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <h6>Completed Process</h6>
                        <h5 class="mb-30 fw-700">{{$process->where('user_id','=',0)->count('id')}}<span class="text-green ml-10">{{empty($process->where('user_id','=',0)->count('id') > 0) ? '' : round($process->where('user_id','=',0)->count('id')/$process->count('id')*100,2)}}%</span></h5>
                        <div class="progress">
                            <div class="progress-bar bg-yellow" style="width:{{empty($process->where('user_id','=',0)->count('id') > 0) ? '' :  round($process->where('user_id','=',0)->count('id')/$process->count('id')*100,2)}}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="row">
        <div class="col-xl-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Pending Services of Officers</h3>
                        <div class="box-body">
                        <table class="table table-responsive">
                            <tbody>
                                <tr>
                                <th style="width: 10%">Officer</th>
                                <th style="width: 40%"></th>
                                <th style="width: 30%">Portion</th>
                                <th style="width: 20%">Pending</th>
                                </tr>
                                @foreach($staffsummary as $staff)
                                <tr>
                                <td>
                                    <div class="circular--landscape">
                                    <img src="{{ isset($staff->user->employee_id) ? '/images/employees/'.$staff->user->employee_id.'.jpg' : asset('backend/img/avatar.png') }}" alt="Avatar" style="width:30px">
                                    </div>
                                </td>
                                <td>{{$staff->user->name}}</td>
                                <td>
                                    <div class="progress progress-xs">
                                        <div class="progress-bar progress-bar-danger" style="width: {{round($staff->countid/$process->where('user_id','!=',0)->where('pendingchk','=',0)->count('id') * 100,2)}}%"></div>
                                    </div>
                                </td>
                                <td><span class="badge bg-red">{{$staff->countid}}</span></td>
                                </tr>
                                @endforeach 
                            </tbody>
                        </table>
                        </div> 
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Time Analysis of Services <a href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#staffperf" data-placement="bottom" title="Add Building"><i class="fas fa-plus"></i> Staff Performance</a>
                </h3>
                        <div class="box-body">
                        <table class="table table-responsive">
                            <tbody>
                                <tr>
                                <th style="width: 50%">Service</th>
                                <th style="width: 30%">Spent:Allocated</th>
                                <th style="width: 20%">Scale</th>
                                </tr>
                                @foreach($servicesummary as $service)
                                <tr>
                                <td>{{$service->service}}</td>
                                <td><span class="badge bg-red">{{$service->timetaken}}:{{$service->timeallocated}}</span></td>
                                @if($service->timetaken > $service->timeallocated)
                                <td><img src="{{ asset('backend/img/emogi_sad.png') }}" alt="Avatar" style="width:30px"></span></td>
                                @else
                                <td><img src="{{ asset('backend/img/emogi_smilie.png') }}" alt="Avatar" style="width:30px"></span></td>
                                @endif
                                </tr>
                                @endforeach 
                            </tbody>
                        </table>
                        </div> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Monthly Registered Services</h3>
                    <div class="box-body">
                        <canvas id="myChart" height="40px" width="100%"></canvas>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-12">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Statistics of Benificiaries' Feedbacks</h3>
                        <div class="box-body">
                        <table class="table table-responsive" id="feedbacktable">
                            <tbody>
                                <tr>
                                <th style="width: 40%">Services</th>
                                <th style="width: 10%; text-align: center""><img src="{{ asset('backend/img/1.jpg') }}" alt="Avatar" style="width:30px"></th>
                                <th style="width: 10%; text-align: center""><img src="{{ asset('backend/img/2.jpg') }}" alt="Avatar" style="width:30px"></th>
                                <th style="width: 10%; text-align: center""><img src="{{ asset('backend/img/3.jpg') }}" alt="Avatar" style="width:30px"></th>
                                <th style="width: 10%; text-align: center""><img src="{{ asset('backend/img/4.jpg') }}" alt="Avatar" style="width:30px"></th>
                                <th style="width: 10%; text-align: center"><img src="{{ asset('backend/img/5.jpg') }}" alt="Avatar" style="width:30px"></th>
                                <th style="width: 10%; text-align: center">Total Completed Services</th>
                                </tr>
                                @foreach($feedbacks as $feedback)
                                <tr>
                                <td>{{$feedback->service->service}}</td>
                                <td style="text-align: center">{{$feedback->scale5}}</td>
                                <td style="text-align: center">{{$feedback->scale4}}</td>
                                <td style="text-align: center">{{$feedback->scale3}}</td>
                                <td style="text-align: center">{{$feedback->scale2}}</td>
                                <td style="text-align: center">{{$feedback->scale1}}</td>
                                <td style="text-align: center">{{$feedback->totaldone}}</td>
                                </tr>
                                @endforeach 
                            </tbody>
                        </table>
                        </div> 
                </div>
            </div>
        </div>

    </div>
</div>
<!-- Modal KPI Form Start -->
<div class="modal fade" id="staffperf" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Time Efficiency Grading</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Modal Building Table start -->
        <span>Subtractions: (Off Peak Hours: 4.15pm to 8.30am, Weekends, Public Holidays)</span>
        <span>KPI: Total Time Taken/Total Time Allocated (Star rating is in testing state). </span>
        <div class="table-responsive">
            <table class="table-stripe" id="perfstaff" style="width:100%">
                <thead>
                    <th>Staff</th>
                    <th>KPI</th>
                    <th>Scale</th>
                </thead>				
            </table>
        </div>
    </div>
    </div>
  </div>
</div>
<div class="homescreen-btn d-flex justify-content-center">
  <button class="btn add-button"><i class="fa fa-home"></i> Add to home screen</button>
</div>
@endsection

@push('styles')
<link href="{{asset('backend/css/theme.css')}}" rel="stylesheet">
<style>
/*Header start*/
.card-slider{
    background-color: maroon; /* For browsers that do not support gradients */
    /* background-image: linear-gradient(to right, maroon , white); */
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    border-radius:5px;
}
#test1 p {
    margin-top: 30px;
    color:white;
    font-size: 30px;
    text-align: center;
    font-weight: 400;
    animation: fadein 6s;
    -moz-animation: fadein 6s; /* Firefox */
    -webkit-animation: fadein 6s; /* Safari and Chrome */
    -o-animation: fadein 6s; /* Opera */
}
#test2 p {
    margin-bottom: 20px;
    color:white;
    font-size: 40px;
    text-align: center;
    font-weight: 600;
    animation: fadein 6s;
    -moz-animation: fadein 6s; /* Firefox */
    -webkit-animation: fadein 6s; /* Safari and Chrome */
    -o-animation: fadein 6s; /* Opera */
}
@keyframes fadein {
    from {
        opacity:0;
    }
    to {
        opacity:1;
    }
}
@-moz-keyframes fadein { /* Firefox */
    from {
        opacity:0;
    }
    to {
        opacity:1;
    }
}
@-webkit-keyframes fadein { /* Safari and Chrome */
    from {
        opacity:0;
    }
    to {
        opacity:1;
    }
}
@-o-keyframes fadein { /* Opera */
    from {
        opacity:0;
    }
    to {
        opacity: 1;
    }
}
/*widget css*/
.container-flex{
    padding: 10px;
}
.row{
    padding: 10px;
}
.color1{
    background: #00C292;
}
.color2{
    background: #03A9F3;
}
.color3{
    background: #FB7146;
}

.card-body{
    height: 120px;
    font-family: "Roboto", sans-serif;
    color: #fff;
    border-radius: 5px;
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}
.float-left{
    float: left;
}
.float-right{
    float: right;
}
.card-body h3{
    margin-top: 15px;
    margin-bottom: 5px;
}
.currency, .countn{
    font-size: 30px;
    font-weight: 500;
}
.card-body p{
    font-size: 16px;
    margin-top: 0;
}
.card-body i{
    font-size: 95px;
    opacity: 0.5;
}
.slider{
    height: 140px;
}

.stretch-card>.card {
     width: 100%;
     min-width: 100%
 }

 body {
     background-color: #f9f9fa
 }

 .flex {
     -webkit-box-flex: 1;
     -ms-flex: 1 1 auto;
     flex: 1 1 auto
 }

 @media (max-width:991.98px) {
     .padding {
         padding: 1.5rem
     }
 }

 @media (max-width:767.98px) {
     .padding {
         padding: 1rem
     }
 }

 .padding {
     padding: 3rem
 }

 .box {
     border-radius: 3px;
     background: #ffffff;
     border-top: 3px solid #d2d6de;
     box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1)
 }

 .box-header.with-border {
     border-bottom: 1px solid #f4f4f4
 }

 .box-header {
     color: #444;
     display: block;
     padding: 10px;
     position: relative
 }

 .box-header:before,
 .box-body:before,
 .box-footer:before,
 .box-header:after,
 .box-body:after,
 .box-footer:after {
     content: "";
     display: table
 }

 .box-header .box-title {
     display: inline-block;
     font-size: 18px;
     margin: 0;
     line-height: 1
 }

 h1,
 h2,
 h3,
 h4,
 h5,
 h6,
 .h1,
 .h2,
 .h3,
 .h4,
 .h5,
 .h6 {
     font-family: 'Source Sans Pro', sans-serif
 }

 .box-header:after,
 .box-body:after,
 .box-footer:after {
     content: "";
     display: table
 }

 .box-body {
     border-top-left-radius: 0;
     border-top-right-radius: 0;
     border-bottom-right-radius: 3px;
     border-bottom-left-radius: 3px;
     padding: 10px
 }

 .box-body>.table {
     margin-bottom: 0
 }

 .table {
     width: 100%;
     max-width: 100%;
     margin-bottom: 20px;
     height: 300px;
 }

 table {
     background-color: transparent;
     display: block;
    overflow-x: auto;
    white-space: nowrap;
 }

 .table tr td .progress {
     margin-top: 5px
 }

 .progress-bar-danger {
     background-color: #dd4b39
 }

 .progress-xs {
     height: 7px
 }

 .bg-red {
     background-color: #dd4b39 !important;
     color: #fff
 }

 .badge {
     display: inline-block;
     min-width: 10px;
     padding: 3px 7px;
     font-size: 12px;
     font-weight: 700;
     line-height: 1;
     color: #fff;
     text-align: center;
     white-space: nowrap;
     vertical-align: middle;
     background-color: #777;
     border-radius: 10px
 }

 .progress-bar-yellow,
 .progress-bar-warning {
     background-color: #f39c12
 }

 .bg-yellow {
     background-color: #f39c12
 }

 .progress-bar-primary {
     background-color: #3c8dbc
 }

 .bg-light-blue {
     background-color: #3c8dbc
 }

 .progress-bar-success {
     background-color: #00a65a
 }

 .bg-green {
     background-color: #00a65a
 }

 .box-footer {
     border-top-left-radius: 0;
     border-top-right-radius: 0;
     border-bottom-right-radius: 3px;
     border-bottom-left-radius: 3px;
     border-top: 1px solid #f4f4f4;
     padding: 10px;
     background-color: #fff
 }

 .pull-right {
     float: right !important
 }

 .pagination>li {
     display: inline
 }

 .pagination-sm>li:first-child>a,
 .pagination-sm>li:first-child>span {
     border-top-left-radius: 3px;
     border-bottom-left-radius: 3px
 }

 .pagination>li:first-child>a,
 .pagination>li:first-child>span {
     margin-left: 0;
     border-top-left-radius: 4px;
     border-bottom-left-radius: 4px
 }

 .pagination>li>a {
     background: #fafafa;
     color: #666
 }

 .pagination-sm>li>a,
 .pagination-sm>li>span {
     padding: 5px 10px;
     font-size: 12px;
     line-height: 1.5
 }

 .pagination>li>a,
 .pagination>li>span {
     position: relative;
     float: left;
     padding: 6px 12px;
     margin-left: -1px;
     line-height: 1.42857143;
     color: #337ab7;
     text-decoration: none;
     background-color: #fff;
     border: 1px solid #ddd
 }

 a {
     background-color: transparent
 }

 img {
  border-radius: 50%;
}

.fa-star{
    color:gold;
}
.add-button {
  z-index:999;
  position: fixed;
  bottom: 1px;
  margin-bottom:50px;
}
.homescreen-btn .btn{
  background-color: orange;
  border: none;
  color: white;
  padding: 4px 8px;
  font-size: 16px;
  cursor: pointer;
}
.homescreen-btn .btn:hover {
  background-color: green;
  color: black;
}
</style> 
@endpush

@push('scripts')
<!--<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>-->
 <!--<link href="{{asset('css/responsive.bootstrap.min.css')}}" rel="stylesheet" type="text/css">-->
<!--<script src="https://cdn.datatables.net/fixedheader/3.3.1/js/dataTables.fixedHeader.min.js"></script>-->
<script src="https://www.gstatic.com/firebasejs/7.16.1/firebase-app.js"></script>
<!--<script src="{{asset('js/firebase-app.js')}}"></script>-->
<script src="https://www.gstatic.com/firebasejs/7.16.1/firebase-messaging.js"></script>
<!--<script src="{{asset('js/firebase-messaging.js')}}"></script>-->
<script>
// Initialize Firebase
if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
        var config = {
            apiKey: "AIzaSyCIiag_E_y2aRPuqIES-Rjqu3A1zx6fQSg",
            authDomain: "test-7693a.firebaseapp.com",
            projectId: "test-7693a",
            storageBucket: "test-7693a.appspot.com",
            messagingSenderId: "267840885374",
            appId: "1:267840885374:web:c587c2d0ecf111a41ffe2f",
            measurementId: "G-WZ4TSBJRVK"
        };
        firebase.initializeApp(config);

        const messaging = firebase.messaging();
        messaging
            .requestPermission()
            .then(function () {
                // get the token in the form of promise
                return messaging.getToken()
            })
            .then(function(token) {
                // console.log(token);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      }
                });
                $.ajax({
                    url: '{{ route("store.token") }}',
                    type: 'POST',
                    data: {
                        token: token
                    },
                    dataType: 'JSON',
                    success: function (response) {

                        //
                    },
                    error: function (error) {
                        //
                    },
                });
            }).catch(function (error) {
                alert(error);
            });
} 
$('.countn').each(function(){
    $(this).prop('Counter',0).animate({
        Counter: $(this).text()
        }, 
        {
            duration:4000,
            easing:'swing',
            step: function(now){
                $(this).text(Math.ceil(now));
                }
        });
});
//Services graph
var ctx = document.getElementById('myChart').getContext('2d');
//adding custom chart type
Chart.defaults.multicolorLine = Chart.defaults.line;
Chart.controllers.multicolorLine = Chart.controllers.line.extend({
  draw: function(ease) {
    var
      startIndex = 0,
      meta = this.getMeta(),
      points = meta.data || [],
      colors = this.getDataset().colors,
      area = this.chart.chartArea,
      originalDatasets = meta.dataset._children
        .filter(function(data) {
          return !isNaN(data._view.y);
        });

    function _setColor(newColor, meta) {
      meta.dataset._view.borderColor = newColor;
    }

    if (!colors) {
      Chart.controllers.line.prototype.draw.call(this, ease);
      return;
    }

    for (var i = 2; i <= colors.length; i++) {
      if (colors[i-1] !== colors[i]) {
        _setColor(colors[i-1], meta);
        meta.dataset._children = originalDatasets.slice(startIndex, i);
        meta.dataset.draw();
        startIndex = i - 1;
      }
    }

    meta.dataset._children = originalDatasets.slice(startIndex);
    meta.dataset.draw();
    meta.dataset._children = originalDatasets;

    points.forEach(function(point) {
      point.draw(area);
    });
  }
});
// build colors sequence
var processcount = {{ $processcount }}
const data = processcount;
const availableColors = ['red', 'green'];
let colors = [];
data.forEach(item => {
  availableColors.forEach(color => {
    colors.push(color)
  })
})

var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'multicolorLine',

    // The data for our dataset
    data: {
        labels: ["January", "February", "March", "April", "May", "June", "July","August","September","October","November","December"],
        datasets: [{
            label: "Number of Services per Month",
            borderColor: 'rgb(255, 99, 132)',
            data: data,
            //first color is not important
            colors: ['', ...colors]
        }]
    },

    // Configuration options go here
    options: {}
});


$(document).ready(function () {
        var table = $('#perfstaff').DataTable({
            processing: true,
            serverSide: true,
            paging: false,
            bFilter: false,
            bInfo : false,
            columnDefs: [
                {
                    targets: 2,
                    render: function (data, type, row) {
                    return '<span class="fa fa-star checked"></span>'.repeat(data);
                    }
                }
                ],
            ajax: {
                url: "{{ url('/staffperf') }}",
            },
            columns: [
                {data: 'user', name: 'user.name'},
                {data: 'kpi', name: 'kpi'},
                {data: 'scale', name: 'scale' },
            ]
        });
        
});
// ............................Add to Home Screen...................
let deferredPrompt;
const addBtn = document.querySelector('.add-button');
addBtn.style.display = 'none';

window.addEventListener('beforeinstallprompt', (e) => {
  // Prevent Chrome 67 and earlier from automatically showing the prompt
  e.preventDefault();
  // Stash the event so it can be triggered later.
  deferredPrompt = e;
  // Update UI to notify the user they can add to home screen
  addBtn.style.display = 'block';

  addBtn.addEventListener('click', (e) => {
    // hide our user interface that shows our A2HS button
    addBtn.style.display = 'none';
    // Show the prompt
    deferredPrompt.prompt();
    // Wait for the user to respond to the prompt
    deferredPrompt.userChoice.then((choiceResult) => {
        if (choiceResult.outcome === 'accepted') {
          console.log('User accepted the A2HS prompt');
        } else {
          console.log('User dismissed the A2HS prompt');
        }
        deferredPrompt = null;
      });
  });
});
</script>
@endpush

