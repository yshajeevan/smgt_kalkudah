@extends('layouts.master')

@section('main-content')
<div class="card shadow mb-4">
     <div class="row">
         <div class="col-md-12">
            @include('layouts.notification')
         </div>
     </div>
     <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary float-left">Start New Process</h6>
    </div>
    <div class="card-body">
    <div class="row">
    <div class="col-md-6">
      <form action="{{ route('process.store') }}" name="addform" id="addform" method="post">
        @csrf
        <div class="row">
          <div class="col-lg-12">
            <input type="text" placeholder="Search NIC.." id="employee_search" class="form-control" required>
            <button type="reset" id="resetbtn">&times;</button>
            <img src="{{asset('backend/img/search.png')}}" alt="Girl in a jacket" name="nicbtn" id="nicbtn">
          </div>
        </div>
        <br>
        <div id="showhide" hidden>
          <div class="row">
            <div class="col-lg-3">
                <label for="" class="control-label">Employee ID</label>
            </div>
            <div class="col-lg-9">
            <input type="text" class="form-control form-control-sm" name="empno" id="empno" readonly required>
            <input type="hidden" class="form-control form-control-sm" name="empid" id="empid">
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
                <label for="" class="control-label">Name</label>
            </div>
            <div class="col-lg-9">
            <input type="text" class="form-control form-control-sm" name="name" id="name" readonly required>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
                <label for="" class="control-label">Designation</label>
            </div>
            <div class="col-lg-9">
            <input type="text" class="form-control form-control-sm" name="designation" id="designation" readonly required>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
                <label for="" class="control-label">Cadre Subject</label>
            </div>
            <div class="col-lg-9">
            <input type="text" class="form-control form-control-sm" name="cadre" id="cadre" readonly>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
                <label for="" class="control-label">Institute</label>
            </div>
            <div class="col-lg-9">
            <input type="text" class="form-control form-control-sm" name="institute" id="institute" readonly required>
            <input type="hidden" name="institute_id" id="institute_id">
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
                <label for="" class="control-label">PF Clerk</label>
            </div>
            <div class="col-lg-9">
            <input type="text" class="form-control form-control-sm" name="pfclerk" id="pfclerk" readonly required>
            <input type="hidden" name="pfclerk_id" id="pfclerk_id" required>
            </div>
          </div>
          <div class="row">
            <div class="col-lg-3">
                <label for="" class="control-label">Acct Clerk</label>
            </div>
            <div class="col-lg-9">
            <input type="text" class="form-control form-control-sm" name="acctsubclk" id="acctsubclk" readonly required>
            <input type="hidden" name="acctclerk_id" id="acctclerk_id" required>
            </div>
          </div>
        </div>
        <br>
        <div class="row">
          <div class="col-lg-9">
              <select name="service_id" id="service_id" class="form-control form-control-sm" hidden required>
                <option>--Select Service--</option>
                  @foreach ($services as $service)
                    <option value="{{ $service->id}}">{{ $service->service}}</option>
                  @endforeach    
              </select>
          </div>
        </div> 
        <br>
        <div class="row" id="remarks_m" hidden>
          <div class="col-lg-4">
              <select name="remarks_mf" id="remarks_mf" class="form-control form-control-sm showhide">
                <option value="">--Select month from--</option>
                <option value="January">January</option> 
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option> 
                <option value="May">May</option> 
                <option value="June">June</option> 
                <option value="July">July</option> 
                <option value="August">August</option> 
                <option value="September">September</option> 
                <option value="October">October</option> 
                <option value="November">November</option> 
                <option value="December">December</option> 
              </select>
          </div>
          <div class="col-lg-4">
              <select name="remarks_mt" id="remarks_mt" class="form-control form-control-sm">
                <option value="">--Select month to--</option>
                <option value="January">January</option> 
                <option value="February">February</option>
                <option value="March">March</option>
                <option value="April">April</option> 
                <option value="May">May</option> 
                <option value="June">June</option> 
                <option value="July">July</option> 
                <option value="August">August</option> 
                <option value="September">September</option> 
                <option value="October">October</option> 
                <option value="November">November</option> 
                <option value="December">December</option>   
              </select>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-9">
            <input type="date" class="form-control form-control-sm showhide" name="remarks_o" id="remarks_o" hidden>
          </div>
        </div>
        <!--Include slugs if exist-->
        <div id="transfer" hidden>
          @include('service_mgt.services.partials.transfer.index')
        </div>
        <div id="cffund" hidden>
          @include('service_mgt.services.partials.cfactivity.index')
        </div>
        <br>
        <div class="row">
          <div class="col-lg-9 d-flex justify-content-center text-center">
            <input type="hidden" name="res1" id="res1">
            <input type="hidden" name="user1_id" id="user1_id">
            <input type="hidden" name="res2" id="res2">
            <input type="hidden" name="servicename" id="servicename">
            <input type="hidden" name="mobile" id="mobile">
            <input type="hidden" name="slug" id="slug">
            <input type="submit" class="btn btn-primary" name="submit" id="submit" value="Submit" hidden>
          </div>
        </div>
      
    </div>
    <!-- Next responsible officer   -->
    <div class="col-md-6">
      <div class="card_photo">
          <div class="photo" id="res2photo"></div>
            <div class="banner"></div>
            <ul>
                <li id="res2name"></li>
                <li>(Next Responsible Officer)</li>
            </ul>
            <input type="button" class="contact" id="main-button" value="click to send a text  message">
          <div class="email-form">
            <input type="hidden" name="nres_id" id="nres_id">
            <textarea style="width:300px; height:100px" name="msg" id="msg" type="text" placeholder="Message"></textarea>
          </div>
        </div>
    </div>
    </form>
    </div>
  </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css" rel="stylesheet">
<style>

/* Main form */
/* form input {
    width: 100%;
    padding-right: 20px;
    box-sizing: border-box;
} */
/* Reset button property */
form input:placeholder-shown + button{
  opacity: 0;
  pointer-events: none;
} 

#resetbtn {
    position: absolute;
    border: none;
    display: block;
    width: 15px;
    height: 15px;
    line-height: 16px;
    font-size: 12px;
    border-radius: 50%;
    top: 0;
    bottom: 0;
    right: 60px;
    margin: auto;
    background: #ddd;
    padding: 0;
    outline: none;
    cursor: pointer;
    transition: .1s;
}

/* Search input property */
#employee_search {
  padding: 10px;
  font-size: 17px;
  border: 1px solid grey;
  float: left;
  width: 90%;
}

#nicbtn {
  float: left;
  width: 10%;
  background: #2196F3;
  padding: 5px;
  color: white;
  font-size: 17px;
  border-left: none;
  cursor: pointer;
  height:40px;
}

.button:hover {
  background: #0b7dda;
}

#addform::after {
  content: "";
  clear: both;
  display: table;
}
/* Card nesxt responsible person */
.card_photo
{
    z-index: 1;
    position: relative;
    width: 300px;
    height:400px;
    margin: 0 auto;
    margin-top: 10px;
    background-color: white;
    -webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
	  -moz-box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
	  box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    -webkit-transition: all 0.7s ease-in-out;
    -moz-transition:    all 0.7s ease-in-out;
    -o-transition:      all 0.7s ease-in-out;
    -ms-transition:     all 0.7s ease-in-out;
/*
        -webkit-box-shadow: 0 0 10px rgba(0, 0, 0, 0.2), inset 0 0 50px rgba(0, 0, 0, 0.1);
	-moz-box-shadow: 0 0 10px rgba(0, 0, 0, 0.2), inset 0 0 50px rgba(0, 0, 0, 0.1);
	box-shadow: 0 0 15px rgba(0, 0, 0, 0.2), inset 0 0 50px rgba(0, 0, 0, 0.2);
*/
}
.card_photo.active
{
    height:490px;
}
.banner
{
    z-index: 2;
    position: relative;
    margin-top: -154px;
    width:100%;
    height:150px;
    background-image: url("https://snap-photos.s3.amazonaws.com/img-thumbs/960w/RQ2Z75PQIN.jpg");
    background-size: cover;
    border-bottom: solid 1px lightgrey;
  
    -webkit-transition: all 0.7s ease-in-out;
    -moz-transition:    all 0.7s ease-in-out;
    -o-transition:      all 0.7s ease-in-out;
    -ms-transition:     all 0.7s ease-in-out;
}

.banner.active
{
    height: 0;
  
}

.photo
{
    z-index: 3;
    position: relative;
    border-radius: 50%;
    height: 150px;
    width: 150px;
    background-color: white;
    margin: 0 auto;
    background-image: url("https://filmshotfreezer.files.wordpress.com/2011/07/untitled-1.jpg");
    background-size: cover;
    background-position: 50% 50%;
    top:75px;
    -webkit-box-shadow: inset 0px 0px 5px 1px rgba(0,0,0,0.3);
    -moz-box-shadow: inset 0px 0px 5px 1px rgba(0,0,0,0.3);
    box-shadow: inset 0px 0px 5px 1px rgba(0,0,0,0.3);
    -webkit-transition: top 0.7s ease-in-out, background 0.15s ease;
    -moz-transition:    top 0.7s ease-in-out, background 0.15s ease;
    -o-transition:      top 0.7s ease-in-out, background 0.15s ease;
    -ms-transition:     top 0.7s ease-in-out, background 0.15s ease;
}

.photo.active
{
    top:-80px;
}
.card_photo ul
{
    list-style: none;
    text-align: center;
    padding-left: 0;
    margin-top:87px;
    margin-bottom:30px;
    font-size: 20px;
    -webkit-transition: all 0.7s ease-in-out;
    -moz-transition:    all 0.7s ease-in-out;
    -o-transition:      all 0.7s ease-in-out;
    -ms-transition:     all 0.7s ease-in-out;
}

.card_photo ul.active
{
    opacity:0;
    visibility: hidden;
}

.card_photo i
{
    font-size: 25px;
    display: inline-block;
    margin-top:10px;
    margin-left: 40px;
    margin-right: 150px;
    width: 300px;;
    text-align: left;
    color: #C7D0E1;
}

.contact
{
    margin: 0 auto;
    text-align: center;
    margin-top: -15px;
    width: 100%;
    height: 35px;
    display: block;    
    border:none;
    background-color: transparent;
    font-family: inherit;
    color: white;
    background-color: #C7D0E1;
    font-size:12px;
    text-transform: uppercase;
    -webkit-transition: all 0.3s ease-in-out;
    -moz-transition:    all 0.3s ease-in-out;
    -o-transition:      all 0.3s ease-in-out;
    -ms-transition:     all 0.3s ease-in-out;
}

.contact:hover
{
    cursor: pointer;
    background-color:#979da8;
}

.contact:focus
{
    outline: 0;
}

.email-form
{
    height: 0;
    overflow: hidden;
/*    background-color: #C7D0E1;*/
    width: 300px;
     z-index:-1;
    -webkit-transition: all 0.5s ease-in-out;
    -moz-transition:    all 0.5s ease-in-out;
    -o-transition:      all 0.5s ease-in-out;
    -ms-transition:     all 0.5s ease-in-out;
     transition: all 0.5s ease-in-out;
}

.email-form.active
{
    height: 310px;
    z-index: 3;
    -webkit-transition: all 1s ease-in-out;
    -moz-transition:    all 1s ease-in-out;
    -o-transition:      all 1s ease-in-out;
    -ms-transition:     all 1s ease-in-out;
     transition: all 1s ease-in-out;
}

.email-form input
{
    width: 200px;
    text-transform: capitalize;
/*    background-color: #a4acbc;*/
    margin: 0 auto;
    font-family: inherit;
    border: 1px solid #dadee5;
/*    border: 1px solid black;*/
    margin-top: 35px;
    height: 30px;
    display: block;
        -webkit-transition: all 0.2s ease-in-out;
    -moz-transition:    all 0.2s ease-in-out;
    -o-transition:      all 0.2s ease-in-out;
    -ms-transition:     all 0.2s ease-in-out;
}
.email-form input:focus,.email-form textarea:focus
{
/*    border: none;*/
    border: 1px solid grey;
    outline: none;
    
}
::-webkit-input-placeholder 
{
 font-size: 12px;
 text-transform: uppercase;
 text-align: center;
/*    color: black;*/
}
.email-form textarea
{
    width: 200px;
    text-transform: capitalize;
    background-color: white;
    margin: 0 auto;
    display: block;
    margin-top:40px;
    border: 1px solid #dadee5;
    font-family: inherit;
    -webkit-transition: all 0.3s ease-in-out;
    -moz-transition:    all 0.3s ease-in-out;
    -o-transition:      all 0.3s ease-in-out;
    -ms-transition:     all 0.3s ease-in-out;
}

.email-form button
{
    margin-top: 60px;
}

* {
  box-sizing: border-box;
}
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" ></script>
<script>

var form = document.getElementById('addform');
var submitButton = document.getElementById('submit');
form.addEventListener('submit', function() {
   submitButton.setAttribute('disabled', 'disabled');
   submitButton.value = 'Please wait...';
}, false);

$(document).ready(function(){
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });

  $( "#employee_search" ).autocomplete({
    source: function( request, response ) {
      $.ajaxSetup({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
      // Fetch data
      $.ajax({
        url:"{{route('employees.searchEmployees')}}",
        type: 'post',
        dataType: "json",
        data: {
          search: request.term
        },
        success: function( data ) {
          response( data );
        }
      });
    },
    select: function (event, ui) {
            $('#employee_search').val(ui.item.label);  
            $('#empid').val(ui.item.value);
            $('#empno').val(ui.item.empno);
            $('#name').val(ui.item.fullname);
            $('#designation').val(ui.item.designation);
            $('#cadre').val(ui.item.cadresubject);
            $('#institute').val(ui.item.institute);
            $('#institute_id').val(ui.item.insid);
            $('#pfclerk').val(ui.item.pfclerk);
            $('#pfclerk_id').val(ui.item.pfclerk_id);
            $("#acctsubclk").val(ui.item.acctclerk);
            $("#acctclerk_id").val(ui.item.acctclerk_id);
            $("#mobile").val(ui.item.mobile);
            //visible service dropdown
            let element = document.getElementById("showhide");
            element.removeAttribute("hidden");
            
            let elementcbo = document.getElementById("service_id");
            elementcbo.removeAttribute("hidden");
            return false;

    
    }
  });
  
    var ex_slug = '';
  //Service dropdown change function
  $("#service_id").change(function(){
    var serviceid = $(this).val();
    var empid = $('#empid').val();
    var res1 = $('#res1').val();
    var res2 = $('#res2').val();

      $.ajax({
        url: "{{ url('/servicedetails') }}",
        method: 'get',
          data: {
            serviceid:serviceid, 
            empid:empid
            },
            success: function(response){
              // Message card start
              if(response.res2 == 31){
                $('.photo').css('background-image', 'url(' + response.pfphoto + ')'); 
              } else if(response.res2 == 32){
                $('.photo').css('background-image', 'url(' + response.acctphoto + ')'); 
              } else{
                $('.photo').css('background-image', 'url(' + response.res2photo + ')'); 
              }
              //Set user1_id value
              if(response.res1 == 31){
                $("#user1_id").val(response.pfclkid);
              } else if(response.res1 == 32){
                $("#user1_id").val(response.acctclkid);
              } else{
                $("#user1_id").val(response.res1);
              }

              //Set next responsible user
              if(response.res2 == 31){
                $("#res2name").html(response.pfname);
                $("#nres_id").val(response.pfclkid);
              } else if(response.res2 == 32){
                $("#res2name").html(response.acctname);
                $("#nres_id").val(response.acctclkid);
              } else{
                $("#res2name").html(response.res2name);
                $("#nres_id").val(response.res2);
              }

              // Message card end
              $("#res1").val(response.res1);
              $("#res2").val(response.res2);
              $("#servicename").val(response.servicename);
                $("#slug").val(response.slug);

              //show and hide elements
              if(ex_slug != ''){
                document.getElementById(ex_slug).setAttribute("hidden", "hiddenbtn");
              }
              if(response.slug != null){
              let elementslug= document.getElementById(response.slug);
              elementslug.removeAttribute("hidden");
              ex_slug = response.slug;
              }
     
              if(response.remarks != 'monthly' && response.remarks != 'often' && response.remarks != 'workshop'){
                let elementbtn = document.getElementById("submit");
                elementbtn.removeAttribute("hidden");
              }  else {
                document.getElementById("submit").setAttribute("hidden", "hiddenbtn");
              }

              if(response.remarks == 'monthly'){
                  document.getElementById("remarks_o").value = '';
                  document.getElementById("remarks_o").setAttribute("hidden", "hiddenbtn");

                  var dropDown_mf = document.getElementById("cfund");
                  dropDown_mf.selectedIndex = 0;
                  document.getElementById("cfund").setAttribute("hidden", "hiddenbtn");

                  let elementbtn = document.getElementById("remarks_m");
                  elementbtn.removeAttribute("hidden");
              } else if(response.remarks == 'often') {
                  var dropDown_mf = document.getElementById("remarks_mf");
                  dropDown_mf.selectedIndex = 0;

                  var dropDown_mt = document.getElementById("remarks_mt");
                  dropDown_mt.selectedIndex = 0;

                  var dropDown_mf = document.getElementById("cfund");
                  dropDown_mf.selectedIndex = 0;
                  document.getElementById("cfund").setAttribute("hidden", "hiddenbtn");

                  document.getElementById("remarks_m").setAttribute("hidden", "hiddenbtn");

                  let elementbtn = document.getElementById("remarks_o");
                  elementbtn.removeAttribute("hidden");
              } else if(response.remarks == 'workshop') {
                var dropDown_mf = document.getElementById("remarks_mf");
                  dropDown_mf.selectedIndex = 0;

                  var dropDown_mt = document.getElementById("remarks_mt");
                  dropDown_mt.selectedIndex = 0;

                  document.getElementById("remarks_m").setAttribute("hidden", "hiddenbtn");
                  document.getElementById("remarks_o").setAttribute("hidden", "hiddenbtn");

                  let elementbtn = document.getElementById("cfund");
                  elementbtn.removeAttribute("hidden");
              } else{
                  var dropDown_mf = document.getElementById("remarks_mf");
                  dropDown_mf.selectedIndex = 0;

                  var dropDown_mt = document.getElementById("remarks_mt");
                  dropDown_mt.selectedIndex = 0;

                  var dropDown_mt = document.getElementById("cfund");
                  dropDown_mt.selectedIndex = 0;

                  document.getElementById("remarks_o").value = '';

                  document.getElementById("remarks_m").setAttribute("hidden", "hiddenbtn");
                  document.getElementById("cfund").setAttribute("hidden", "hiddenbtn");
                  document.getElementById("remarks_o").setAttribute("hidden", "hiddenbtn");
              }
            }
      });
  });

    $('.contact').click(function (e) 
    {
        $('.card').toggleClass('active');
        $('.banner').toggleClass('active');
        $('.photo').toggleClass('active');
        $('.social-media-banner').toggleClass('active');
        $('.email-form').toggleClass('active');  
        var buttonText = $('.contact#main-button').text();
        if (buttonText === 'back')
        {
            buttonText = 'click to get in touch';
            $('.contact#main-button').text(buttonText);
        }
        else
        {
            buttonText = 'back';
            $('.contact#main-button').text(buttonText);
        }
    });
});

$("#resetbtn").click(function(){
  document.getElementById("submit").setAttribute("hidden", "hiddenbtn");
  document.getElementById('service_id').setAttribute("hidden", "hiddencbo");
  document.getElementById('showhide').setAttribute("hidden", "showhide");
  document.getElementById('remarks_m').setAttribute("hidden", "remarks_m");
  document.getElementById('remarks_o').setAttribute("hidden", "remarks_o");
});  


//Sending SMS Functions
function SMSFunction() {
		var xhr = new XMLHttpRequest();
    var service = document.getElementById("servicename").value
    var dropDown_mf = document.getElementById("remarks_mf").value;
    var dropDown_mt = document.getElementById("remarks_mt").value;
    var date_o = document.getElementById("remarks_o").value;
    var dropDown_cfund = document.getElementById("cfund").value;

    if(dropDown_mf != ''){
      if(dropDown_mt != ''){
        var remarks = ' from ' + dropDown_mf + ' to ' + dropDown_mt;
      } else{
        var remarks = ' for the month of ' + dropDown_mf;
      }
    } else if(date_o != ''){
        var remarks = ' for the date of ' + date_o;
    } else if(dropDown_cfund != ''){
        var remarks = ' with activity code: ' + dropDown_cfund;
    } else {
        var remarks = '';
    }
    var tmessage = 'Your recent service ' + service + remarks + ' has been taken into process. Ref.no:' + {!! json_encode($maxid + 1, JSON_HEX_TAG) !!} + '. Click the below mentioned link and register to view your services. https://smgtc.battiwestzeo.lk';
		var mnumber = '94'+ document.getElementById("mobile").value;
		xhr.open("GET", "https://richcommunication.dialog.lk/api/sms/inline/send?q=e78f434d6604755&destination=" + mnumber + "&message=" + tmessage + "&from=BATWESTZEO", true);
			xhr.onreadystatechange = function(){
				if (xhr.readyState == 4 && xhr.status == 200) {			
				}
			}; 	    
    // if (confirm("Service is started!, Do you want to send SMS to benificiary?")) {	
		xhr.send();	
    // } else {
		// 	alert("Service started without sending SMS!");
    // }
}

$( "#submit" ).click(function() {
  SMSFunction();
});

$( ".showhide" ).change(function(){
  if(document.getElementById("remarks_mf").value != '' || document.getElementById("remarks_o").value != '' || document.getElementById("cfund").value != ''){
    let elementbtn = document.getElementById("submit");
    elementbtn.removeAttribute("hidden");
  }  else {
    document.getElementById("submit").setAttribute("hidden", "hiddenbtn");
  }
});

$( "#remarks_mf" ).change(function(){
  if(document.getElementById("remarks_mf").value == ''){
    document.getElementById("submit").setAttribute("hidden", "hiddenbtn");
  }
});

$( "#remarks_o" ).change(function(){
  if(document.getElementById("remarks_o").value == ''){
    document.getElementById("submit").setAttribute("hidden", "hiddenbtn");
  }
});

$( "#cfund" ).change(function(){
  if(document.getElementById("cfund").selectedIndex == 0){
    document.getElementById("submit").setAttribute("hidden", "hiddenbtn");
  }
});

$( "#remarks_mt" ).change(function(){
  if(document.getElementById("remarks_mf").value == ''){
    $.toast({
      heading: 'Error',
      text: 'Please select month from!',
      icon: 'error',
      loader: true, 
      position: 'top-center',
      loaderBg: '#f44336',      
    });
  }
});

var msg = '{{Session::get('alert')}}';
    var exist = '{{Session::has('alert')}}';
    if(exist){
      alert(msg);
    }

</script>
@endpush