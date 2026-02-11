<!doctype html>

<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SMGT-Service Feedback</title>
  <meta name="description" content="Feedback of Zonal Service Management System">
  <meta name="author" content="SitePoint">
  <meta name="csrf-token" content="{{ csrf_token() }}">

<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/css/bootstrap.min.css" rel="stylesheet">
<link href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" rel="stylesheet">
<style>

body {
    background: white;
    padding: 10px;
}

.mini-container:hover {
    cursor: pointer;
}
.img-profile {
    width: 30px;
    height: auto;
}

.container {
    margin-top:200px;
    width: 100%;
    height: 120px;
    box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;
}

.text-center{
    font-size: 8px;
}

.success{
    font-weight: bold;
    color:green;
    font-size: 20px;
    text-align:center;
}
</style>
</head>

<body>
<div class="container p-3 mb-5 bg-white rounded" id="divfeedback">
    <div class="card-body" id="divemogies">
        <p class="text-center"><small><strong>How satisfied are you with our service?</strong></small></p>
            <div class="row" style="text-align:center;">
                <div class="col text-center">
                    <div class="mini-container" id="5"> <img class="img-profile rounded-circle" src="{{asset('backend/img/1.jpg')}}">
                        <p class="text-center"><small>Full Satisfied</small></p>
                    </div>
                </div>
                <div class="col text-center">
                    <div class="mini-container" id="4"> <img class="img-profile rounded-circle" src="{{asset('backend/img/2.jpg')}}">
                        <p class="text-center"><small>Satisfied</small></p>
                    </div>
                </div>
                <div class="col text-center">
                    <div class="mini-container" id="3"> <img class="img-profile rounded-circle" src="{{asset('backend/img/3.jpg')}}">
                        <p class="text-center"><small>Moderate</small></p>
                    </div>
                </div>
                <div class="col text-center">
                    <div class="mini-container" id="2"> <img class="img-profile rounded-circle" src="{{asset('backend/img/4.jpg')}}">
                        <p class="text-center"><small>Unsatisfied</small></p>
                    </div>
                </div>
                <div class="col text-center">
                    <div class="mini-container" id="1"> <img class="img-profile rounded-circle" src="{{asset('backend/img/5.jpg')}}">
                        <p class="text-center"><small>Extremely Unsatisfied</small></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body" id="divsuccess">
        </div>
      </div>
 <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-alpha1/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
$('.container').hide();
$(document).ready(function() {
  $('.container').delay(1000).fadeIn();
});

$('.mini-container').click(function(event){
    var scaleid = $(this).attr('id');
    var uniqkey = {!! json_encode($scale, JSON_HEX_TAG) !!};
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    $.ajax({
        type: "post",
        url: "{{ url('update_feedback') }}",
        dataType: "json",
        data: {scale: scaleid, uniqkey: uniqkey},
        success: function(data){
            $("#divemogies").hide();
            $('#divsuccess').html("<div class='success' id='divSuccessMsg'></div>");
            $('#divSuccessMsg').html("Your feedback is recieved! Thank you.")
            .hide()
            .fadeIn(1500, function() { $('#divSuccessMsg'); });
            setTimeout(resetAll,8000);
        },
        error: function(data){
            $("#divsuccess").text("Error!");

        }
    });
    function resetAll(){
        $("#divemogies").show(); 
        $('#divSuccessMsg').remove(); // Removing it as with next form submit you will be adding the div again in your code. 
    }
});

  
</script>
</body>
</html>